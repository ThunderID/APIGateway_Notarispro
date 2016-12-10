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
 * Akta  resource representation.
 *
 * @Resource("Akta", uri="/akta")
 */
class AktaController extends Controller
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

	public function get_handover($type = 'draft_akta')
	{
		//1. Middleware Parse Parameter
		if((str_is($this->role, 'drafter') || str_is($this->role, 'notary')) && str_is($type, 'draft_akta'))
		{
			$search['search']['type']		= $type;
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
		}
		elseif(str_is($this->role, 'notary') && str_is($type, 'renvoi_akta'))
		{
			$search['search']['type']		= $type;
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$search_writer['search']['organizationid']	= $this->ownerid;
		$search_writer['search']['role']			= ['notary', 'drafter'];

		//2. Mq Caller
		//2a. Check Existance
		$akta 		= new AktaCaller;
		$response 	= $akta->edit_caller($search, $this->request, $this->get_new_token($this->token));
		
		//2b. Call All possible User
		$writer 			= new WriterCaller;
		$response_writer 	= $writer->index_caller($search_writer, $this->request, $this->get_new_token($this->token));
		
		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$resource 		= new HandoverAktaEditableTransformer();
			$resource 		= $resource->transform($response['data']['data'][0], $response_writer['data']['data'][0]);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$response['data']['data']	= $resource;
		}

		$response 	= json_encode($response);

		return $response;
	}

	public function post_handover($type = 'draft_akta')
	{
		//1. Middleware Parse Parameter
		if((str_is($this->role, 'drafter') || str_is($this->role, 'notary')) && str_is($type, 'draft_akta'))
		{
			$search['search']['type']		= $type;
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
			$this->validator 				= new AktaValidator;
		}
		elseif(str_is($this->role, 'notary') && str_is($type, 'renvoi_akta'))
		{
			$search['search']['type']		= $type;
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
			$search['search']['id']			= $this->request->input('id');
			$this->validator 				= new AktaValidator;
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$search_writer['search']['organizationid']	= $this->ownerid;
		$search_writer['search']['role']			= ['notary', 'drafter'];

		//2. Validating This Process on Business Rule
		//2a. Check Existance
		if(!$this->validator->is_okay_to_drafting($search, $this->request, $this->get_new_token($this->token)))
		{
			return response()->json( JSend::error([$this->validator->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		//3a. Parse Akta
		$this->formattor 	= new AktaFormattor;
		$body 				= $this->formattor->parse_to_akta_handover($this->validator->data, $this->request, $this->token, $type);

		//4. Mq Caller (Action)
		//4a. Simpan Akta
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
}