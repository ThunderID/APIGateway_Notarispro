<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Builder;

use App\Http\Mq\AktaCaller;

/**
 * Draft Akta  resource representation.
 *
 * @Resource("Draft", uri="/Draft")
 */
class ProposedAktaController extends Controller
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
		//1. if JWT is notary, display only my
		$role 		= $this->token->getClaim('role');

		if(str_is($role, 'notary'))
		{
			$ownerid 						= $this->token->getClaim('oid');
			$search['search']['type']		= 'proposed_akta';
			$search['search']['ownerid']	= $ownerid;
			$search['search']['ownertype']	= 'organization';
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
		//1. if JWT is notary, display only my
		$role 		= $this->token->getClaim('role');
		$ownerid 	= $this->token->getClaim('oid');

		if(str_is($role, 'notary'))
		{
			$search['search']['type']		= 'proposed_akta';
			$search['search']['ownerid']	= $ownerid;
			$search['search']['ownertype']	= 'organization';
			$search['search']['id']			= $this->request->input('id');
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$akta 		= new AktaCaller;

		return $akta->show_caller($search, $this->request, $this->get_new_token($this->token));
	}

	public function store($status = 'void_akta', $prev_status = 'proposed_akta')
	{
		//Check 
		//1. if JWT is drafter, display only my
		$role 		= $this->token->getClaim('role');
		$ownerid 	= $this->token->getClaim('oid');
		$ownername 	= $this->token->getClaim('oname');
		$writerid 	= $this->token->getClaim('pid');
		$writername = $this->token->getClaim('pname');

		if(str_is($role, 'notary'))
		{
			//a. check whose document is it
			if(!is_null($this->request->input('id')))
			{
				$search['search']['type']		= $prev_status;
				$search['search']['ownerid']	= $ownerid;
				$search['search']['ownertype']	= 'organization';
				$search['search']['id']			= $this->request->input('id');

				$akta 		= new AktaCaller;

				$response 	= $akta->show_caller($search, $this->request, $this->get_new_token($this->token));

				$response 	= json_decode($response, true);

				if(!str_is($response['status'], 'success') || count($response['data']['data']) < 1)
				{
					return response()->json( JSend::error(['Tidak dapat void akta yang belum selesai di edit!'])->asArray());
				}
			}
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$body 					= $response['data']['data'][0];
		$body['id'] 			= $response['data']['data'][0]['_id'];
		$body['owner']['_id']	= $ownerid;
		$body['owner']['type']	= 'organization';
		$body['owner']['name']	= $ownername;
		$body['type']			= $status;

		$akta 		= new AktaCaller;

		return $akta->store_caller($body, $this->request, $this->get_new_token($this->token));
	}
}

