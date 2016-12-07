<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Builder;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use App\Http\Mq\TemplateCaller;

use App\Http\Policies\TemplateValidator;
use App\Http\Policies\TemplateFormattor;

use App\Http\Transformers\ListTemplateAktaTransformer;
use App\Http\Transformers\IsiTemplateAktaTransformer;
use App\Http\Transformers\IsiTemplateAktaEditableTransformer;

/**
 * Template Akta  resource representation.
 *
 * @Resource("Template", uri="/Template")
 */
class TemplateAktaController extends Controller
{
	public function __construct(Request $request)
	{
		//Here lies all needed data, token and request

		$this->request 		= $request;

		$this->token  		= $this->request->header('Authorization');

		$tokens 			= explode(' ', $this->token);

		$this->token 		= $tokens[count($tokens) - 1];

		$this->token		= (new Parser())->parse((string) $this->token); // Parses from a string

		//Here lies global parameter
		$this->role 		= $this->token->getClaim('role');
		$this->writerid 	= $this->token->getClaim('pid');
		$this->ownerid 		= $this->token->getClaim('oid');
	}

	//Here is list of all template, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/list/template/akta
	public function index($id = null)
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= ['akta', 'draft_akta', 'void_akta'];
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Mq Caller
		$akta 		= new TemplateCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->get_new_token($this->token));

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection($response['data']['data'], new ListTemplateAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is show content of a template deed, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/template/akta
	public function show()
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= ['akta', 'void_akta', 'draft_akta'];
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['id']			= $this->request->input('id');
			$search['search']['ownertype']	= 'organization';
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Mq Caller
		$akta 		= new TemplateCaller;
		$response 	= $akta->show_caller($search, $this->request, $this->get_new_token($this->token));

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection($response['data']['data'], new IsiTemplateAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is edit content of a deed template, only can be used by owner of document (personally)
	//Also used by create process. If it's create then dummy data will be sent
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- mulai/template/akta
	//- edit/isi/template/akta
	public function edit()
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= ['akta', 'void_akta', 'draft_akta'];
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['id']			= $this->request->input('id');
			$search['search']['ownertype']	= 'organization';
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Mq Caller
		$akta 		= new TemplateCaller;

		$response 	= $akta->edit_caller($search, $this->request, $this->get_new_token($this->token));

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection($response['data']['data'], new IsiTemplateAktaEditableTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is store content of a template deed, only can be used by owner of document (personally)
	//Also using to update a content of a template deed, void akta, akta, and so. If it's update then it will check
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- simpan/template/akta
	//- update/template/akta
	public function store($status = 'draft_akta', $prev_status = 'draft_akta')
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter', 'notary'))
		{
			$search['search']['type']		= $prev_status;
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownertype']	= 'organization';
			$search['search']['id']			= $this->request->input('id');
			$this->validator 				= new TemplateValidator;
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		if(!is_null($this->request->input('id')))
		{
			if(!$this->validator->is_okay_to_templating($search, $this->request, $this->get_new_token($this->token)))
			{
				return response()->json( JSend::error([$this->validator->error])->asArray());
			}
		}

		//3. Parse Data to Store format based on policy
		$this->formattor 	= new TemplateFormattor;
		$body 				= $this->formattor->parse_to_draft_structure($this->request, $this->token, $status);

		//4. Mq Caller (Action)
		$akta 		= new TemplateCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->get_new_token($this->token));

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection([$response['data']], new IsiTemplateAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is delete a template deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- hapus/template/akta
	public function delete()
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'draft_akta';
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
			$search['search']['writerid']	= $this->writerid;
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$akta 		= new TemplateCaller;

		$response 	= $akta->show_caller($search, $this->request, $this->get_new_token($this->token));

		if(!str_is($response['status'], 'success') || count($response['data']['data']) < 1)
		{
			return response()->json( JSend::error(['Tidak dapat menghapus draft akta yang bukan milik Anda!'])->asArray());
		}

		//3. Parse Data to Delete format
		$body['id']	= $this->request->input('id');

		//4. Mq Caller (Action)
		$akta 		= new TemplateCaller;
		$response 	= $akta->delete_caller($body, $this->request, $this->get_new_token($this->token));

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection($response['data']['data'], new IsiTemplateAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is issue a template deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- issue/template/akta
	//- void/template/akta
	public function issue($status = 'akta', $prev_status = 'draft_akta')
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= $prev_status;
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
			$this->validator 				= new TemplateValidator;
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Validating This Process on Business Rule
		//2a. Check Existance
		if(!$this->validator->is_okay_to_templating($search, $this->request, $this->get_new_token($this->token)))
		{
			return response()->json( JSend::error([$this->validator->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		//3a. Parse Akta
		$this->formattor 	= new TemplateFormattor;
		$body 				= $this->formattor->parse_to_template_structure($this->validator->data, $this->token, $status);

		//4. Mq Caller (Action)
		//4a. Simpan Akta
		$akta 		= new TemplateCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->get_new_token($this->token));

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection([$response['data']], new IsiTemplateAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is void a template deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Use Same Procedure as issue :
	public function void()
	{
		return $this->issue('void_akta', 'akta');
	}
}

