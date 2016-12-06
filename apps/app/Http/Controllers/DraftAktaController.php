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

/**
 * Draft Akta  resource representation.
 *
 * @Resource("Draft", uri="/Draft")
 */
class DraftAktaController extends Controller
{
	public $corr_id;
	public $response;

	public function __construct(Request $request)
	{
		$this->request 		= $request;

		$this->token  		= $this->request->header('Authorization');

		$tokens 			= explode(' ', $this->token);

		$this->token 		= $tokens[count($tokens) - 1];

		$this->token		= (new Parser())->parse((string) $this->token); // Parses from a string
	}

	public function index($id = null)
	{
		//Check 
		//1. if JWT is drafter, display only my
		$role 		= $this->token->getClaim('role');

		if(str_is($role, 'drafter'))
		{
			$writerid 						= $this->token->getClaim('pid');
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $writerid;
			$search['search']['ownerid']	= $writerid;
			$search['search']['ownertype']	= 'person';
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$akta 		= new AktaCaller;

		return $akta->index_caller($search, $this->request, $this->get_new_token($this->token));
	}

	public function show()
	{
		//Check 
		//1. if JWT is drafter, display only my
		$role 		= $this->token->getClaim('role');

		if(str_is($role, 'drafter'))
		{
			$writerid 						= $this->token->getClaim('pid');
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $writerid;
			$search['search']['ownerid']	= $writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$akta 		= new AktaCaller;

		return $akta->show_caller($search, $this->request, $this->get_new_token($this->token));
	}

	public function edit()
	{
		//Check 
		//1. if JWT is drafter, display only my
		$role 		= $this->token->getClaim('role');

		if(str_is($role, 'drafter'))
		{
			$writerid 						= $this->token->getClaim('pid');
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $writerid;
			$search['search']['ownerid']	= $writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$akta 		= new AktaCaller;

		return $akta->edit_caller($search, $this->request, $this->get_new_token($this->token));
	}

	public function store($status = 'draft_akta', $prev_status = 'draft_akta')
	{
		//Check 
		//1. if JWT is drafter, display only my
		$role 		= $this->token->getClaim('role');
		$ownerid 	= $this->token->getClaim('oid');
		$ownername 	= $this->token->getClaim('oname');
		$writerid 	= $this->token->getClaim('pid');
		$writername = $this->token->getClaim('pname');

		if(str_is($role, 'drafter'))
		{
			//a. check whose document is it
			if(!is_null($this->request->input('id')))
			{
				$search['search']['type']		= $prev_status;
				$search['search']['writerid']	= $writerid;
				$search['search']['ownerid']	= $writerid;
				$search['search']['ownertype']	= 'person';
				$search['search']['id']			= $this->request->input('id');

				$akta 		= new AktaCaller;

				$response 	= $akta->show_caller($search, $this->request, $this->get_new_token($this->token));

				$response 	= json_decode($response, true);

				if(!str_is($response['status'], 'success') || count($response['data']['data']) < 1)
				{
					return response()->json( JSend::error(['Tidak dapat menyimpan draft akta yang bukan milik Anda!'])->asArray());
				}
			}
		}
		else
		{
			throw new \Exception('invalid role');
		}

		if(in_array($status, ['proposed_akta']))
		{
			$body 					= $response['data']['data'][0];
			$body['id'] 			= $response['data']['data'][0]['_id'];
			$body['owner']['_id']	= $ownerid;
			$body['owner']['type']	= 'organization';
			$body['owner']['name']	= $ownername;
		}
		else
		{
			$body 					= $this->request->input();
			$body['owner']['_id']	= $writerid;
			$body['owner']['type']	= 'person';
			$body['owner']['name']	= $writername;

			foreach ($body['paragraph'] as $key => $value) 
			{
				$body['paragraph'][$key]= ['content' => $value];
			}
		}

		$body['writer']['_id']		= $writerid;
		$body['writer']['name']		= $writername;
		$body['type']				= $status;

		$akta 		= new AktaCaller;

		return $akta->store_caller($body, $this->request, $this->get_new_token($this->token));
	}

	public function delete()
	{
		//Check 
		//1. if JWT is drafter, display only my
		$role 		= $this->token->getClaim('role');

		if(str_is($role, 'drafter'))
		{
			//a. check whose document is it
			$writerid 						= $this->token->getClaim('pid');
			$search['search']['type']		= 'draft_akta';
			$search['search']['writerid']	= $writerid;
			$search['search']['ownerid']	= $writerid;
			$search['search']['ownertype']	= 'person';
			$search['search']['id']			= $this->request->input('id');

			$akta 		= new AktaCaller;

			$response 	= $akta->show_caller($search, $this->request, $this->get_new_token($this->token));

			$response 	= json_decode($response, true);
			
			if(!str_is($response['status'], 'success') || count($response['data']['data']) < 1)
			{
				return response()->json( JSend::error(['Tidak dapat menghapus draft akta yang bukan milik Anda!'])->asArray());
			}
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$body['id']	= $this->request->input('id');

		$akta 		= new AktaCaller;

		return $akta->edit_caller($body, $this->request, $this->get_new_token($this->token));
	}

	public function issue()
	{
		return $this->store('proposed_akta', 'draft_akta');
	}
}

