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
 * Proposed Akta  resource representation.
 *
 */
class ProposedAktaController extends Controller
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
		if(str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'proposed_akta';
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
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
		if(str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'proposed_akta';
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
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

	//Here is void a draft deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- void/draft/akta
	public function void($status = 'void_akta', $prev_status = 'proposed_akta')
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'notary'))
		{
			$search['search']['type']		= 'proposed_akta';
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
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

