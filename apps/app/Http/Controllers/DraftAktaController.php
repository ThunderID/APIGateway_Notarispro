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

use App\Http\Mq\AktaCaller;
use App\Http\Mq\LockCaller;

use App\Http\Policies\AktaValidator;
use App\Http\Policies\AktaFormattor;
use App\Http\Policies\LockFormattor;

use App\Http\Transformers\ListAktaTransformer;
use App\Http\Transformers\IsiAktaTransformer;
use App\Http\Transformers\IsiAktaEditableTransformer;

/**
 * Draft Akta  resource representation.
 *
 */
class DraftAktaController extends Controller
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

	//Here is list of all deed drafts, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/draft/akta
	public function index($id = null)
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Mq Caller
		$akta 		= new AktaCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->get_new_token($this->token));

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection($response['data']['data'], new ListAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is show content of a draft deed, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/draft/akta
	public function show()
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Mq Caller
		$akta 		= new AktaCaller;
		$response 	= $akta->show_caller($search, $this->request, $this->get_new_token($this->token));

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection($response['data']['data'], new IsiAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is edit content of a draft deed, only can be used by owner of document (personally)
	//Also used by create process. If it's create then dummy data will be sent
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- mulai/draft/akta
	//- edit/isi/draft/akta
	public function edit()
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Mq Caller
		$akta 		= new AktaCaller;

		$response 	= $akta->edit_caller($search, $this->request, $this->get_new_token($this->token));

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection($response['data']['data'], new IsiAktaEditableTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is store content of a draft deed, only can be used by owner of document (personally)
	//Also using to update a content of a draft deed, proposed akta, akta, and so. If it's update then it will check
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- simpan/draft/akta
	//- update/draft/akta
	public function store($status = 'draft_akta', $prev_status = 'draft_akta')
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter', 'notary'))
		{
			$search['search']['type']		= $prev_status;
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
			$this->validator 				= new AktaValidator;
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		if(!is_null($this->request->input('id')))
		{
			if(!$this->validator->is_okay_to_drafting($search, $this->request, $this->get_new_token($this->token)))
			{
				return response()->json( JSend::error([$this->validator->error])->asArray());
			}
		}

		//3. Parse Data to Store format based on policy
		$this->formattor 	= new AktaFormattor;
		$body 				= $this->formattor->parse_to_draft_structure($this->request, $this->token, $status);

		//4. Mq Caller (Action)
		$akta 		= new AktaCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->get_new_token($this->token));

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection([$response['data']], new IsiAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is delete a draft deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- hapus/draft/akta
	public function delete()
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$akta 		= new AktaCaller;

		$response 	= $akta->show_caller($search, $this->request, $this->get_new_token($this->token));

		if(!str_is($response['status'], 'success') || count($response['data']['data']) < 1)
		{
			return response()->json( JSend::error(['Tidak dapat menghapus draft akta yang bukan milik Anda!'])->asArray());
		}

		//3. Parse Data to Delete format
		$body['id']	= $this->request->input('id');

		//4. Mq Caller (Action)
		$akta 		= new AktaCaller;
		$response 	= $akta->delete_caller($body, $this->request, $this->get_new_token($this->token));

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$fractal		= new Manager();
			$resource 		= new Collection([$response['data']['data']], new IsiAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is issue a draft deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- issue/draft/akta
	public function issue($status = 'proposed_akta', $prev_status = 'draft_akta')
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= $prev_status;
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
			$this->validator 				= new AktaValidator;
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Validating This Process on Business Rule
		//2a. Check Existance
		if(!$this->validator->is_okay_to_drafting($search, $this->request, $this->get_new_token($this->token)))
		{
			return response()->json( JSend::error([$this->validator->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		//3a. Parse Akta
		$this->formattor 	= new AktaFormattor;
		$body 				= $this->formattor->parse_to_akta_structure($this->validator->data, $this->token, $status);

		//3b. Parse Lock
		$this->formattor_lock 	= new LockFormattor;
		$body_lock 				= $this->formattor_lock->parse_to_lock_structure($this->validator->data, $this->token, $status);

		//4. Mq Caller (Action)
		//4a. Simpan Akta
		$akta 		= new AktaCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->get_new_token($this->token));

		if(str_is($response['status'], 'success'))
		{
			//4b. Lock Akta
			$lock 			= new LockCaller;

			$response_lock = $lock->store_caller($body_lock, $this->request, $this->get_new_token($this->token));

			//5. Transforming Data
			$fractal		= new Manager();
			$resource 		= new Collection([$response['data']], new IsiAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}
}

