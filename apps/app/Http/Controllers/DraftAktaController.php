<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Builder;

use App\Http\Mq\MessageQueueCaller;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use App\Http\Transformers\ListAktaTransformer;
use App\Http\Transformers\IsiAktaTransformer;

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

		$per_page 					= (!is_null($this->request->input('per_page')) ? $this->request->input('per_page') : 20);
		$page 						= (!is_null($this->request->input('page')) ? max(1, $this->request->input('page')) : 1);
		$search['skip']				= max(0, ($page - 1)) * $per_page;
		$search['take']				= $per_page;

		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $this->get_new_token($this->token),
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.document.index');

		if(str_is($response['status'], 'success'))
		{
			$response['data']['data']	= $this->getStructureMultiple($response['data']['data']);
		}
		
		$response 	= json_encode($response);

		//2. transform returned value
		return $response;
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

		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $this->get_new_token($this->token),
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.document.index');

		if(str_is($response['status'], 'success') && count($response['data']['data']) > 0)
		{
			$response['data']['data']	= $this->getStructureSingle($response['data']['data'])[0];
		}
		else
		{
			$response['data']['data']	= $this->getStructureSingle($this->dummy());
		}
		
		$response 	= json_encode($response);

		//2. transform returned value
		return $response;
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

				$attributes 	= 	[
										'header'	=>
														[
															'token'		=>  $this->get_new_token($this->token),
														],
										'body'		=> 	$search,
									];
				$data 			= json_encode($attributes);

				$mq 			= new MessageQueueCaller();
				$response 		= $mq->call($data, 'tlab.document.index');

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

		if(in_array($status, ['akta']))
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
		}

		$body['writer']['_id']		= $writerid;
		$body['writer']['name']		= $writername;
		$body['type']				= $status;

		foreach ($body['paragraph'] as $key => $value) 
		{
			$body['paragraph'][$key]= ['content' => $value];
		}

		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $this->get_new_token($this->token),
												],
								'body'		=> 	$body,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.document.store');

		if(str_is($response['status'], 'success'))
		{
			$response['data']	= $this->getStructureSingle([$response['data']]);
		}
		
		$response 		= json_encode($response);

		//2. transform returned value
		return $response;
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

			$attributes 	= 	[
									'header'	=>
													[
														'token'		=>  $this->get_new_token($this->token),
													],
									'body'		=> 	$search,
								];
			$data 			= json_encode($attributes);

			$mq 			= new MessageQueueCaller();
			$response 		= $mq->call($data, 'tlab.document.index');

			if(!str_is($response['status'], 'success') || count($response['data']['data']) < 1)
			{
				return response()->json( JSend::error(['Tidak dapat menghapus draft akta yang bukan milik Anda!'])->asArray());
			}
		}
		else
		{
			throw new \Exception('invalid role');
		}

		$body['id']		= $this->request->input('id');

		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $this->get_new_token($this->token),
												],
								'body'		=> 	$body,
							];

		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.document.delete');

		if(str_is($response['status'], 'success'))
		{
			$response['data']	= $this->getStructureSingle([$response['data']]);
		}
		
		$response 		= json_encode($response);

		//2. transform returned value
		return $response;
	}

	public function issue()
	{
		return $this->store('akta', 'draft_akta');
	}

	/**
	 * Fractal Modifying Returned Value
	 *
	 * getStructureMultiple method used to transforming response format and included UI inside (@UInside)
	 */
	public function getStructureMultiple($draft)
	{
		$fractal		= new Manager();
		$resource 		= new Collection($draft, new ListAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		return $array['data'];
	}

	/**
	 * Fractal Modifying Returned Value
	 *
	 * getStructureMultiple method used to transforming response format and included UI inside (@UInside)
	 */
	public function getStructureSingle($draft)
	{
		$fractal		= new Manager();
		$resource 		= new Collection($draft, new IsiAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		return $array['data'];
	}

	public function dummy()
	{
		return [['_id' => '123456789', 'title' => 'Akta Jual Beli Tanah', 'type' => 'draft_akta', 'writer' => ['_id' => '123456789', 'name' => 'Ada Lovelace'], 'owner' => ['_id' => '123456789', 'name' => 'Thunderlab Indonesia'], 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'paragraph' => [['content' => 'Isi Akta']]]];
	}
}

