<?php

namespace App\Http\Mq;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use App\Http\Transformers\ListAktaTransformer;
use App\Http\Transformers\IsiAktaTransformer;
use App\Http\Transformers\IsiAktaEditableTransformer;

class AktaCaller 
{
	public function index_caller($search, $request, $token) 
	{
		$per_page 					= (!is_null($request->input('per_page')) ? $request->input('per_page') : 20);
		$page 						= (!is_null($request->input('page')) ? max(1, $request->input('page')) : 1);
		$search['skip']				= max(0, ($page - 1)) * $per_page;
		$search['take']				= $per_page;

		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $token,
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.document.index');

		if(str_is($response['status'], 'success'))
		{
			$response['data']['data']	= $this->getIndexAkta($response['data']['data']);
		}
		
		$response 	= json_encode($response);

		return $response;
	}

	public function show_caller($search, $request, $token) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $token,
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.document.index');

		if(str_is($response['status'], 'success') && count($response['data']['data']) > 0)
		{
			$response['data']['data']	= $this->getDetailAkta($response['data']['data'])[0];
		}
		else
		{
			return response()->json( JSend::error(['Tidak dapat melihat draft Akta yang bukan milik Anda!'])->asArray());
		}
		
		$response 	= json_encode($response);

		//2. transform returned value
		return $response;
	}

	public function edit_caller($search, $request, $token) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $token,
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.document.index');

		if(str_is($response['status'], 'success') && count($response['data']['data']) > 0)
		{
			$response['data']['data']	= $this->getEditableSingle($response['data']['data'])[0];
		}
		else
		{
			$response['data']['data']	= $this->getEditableSingle($this->dummy());
		}
		
		$response 	= json_encode($response);

		//2. transform returned value
		return $response;
	}

	public function store_caller($param, $request, $token) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $this->get_new_token($this->token),
												],
								'body'		=> 	$param,
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

	public function delete_caller($search, $request, $token) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $token,
												],
								'body'		=> 	$search,
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

	/**
	 * Fractal Modifying Returned Value
	 *
	 * getIndexAkta method used to transforming response format and included UI inside (@UInside)
	 */
	public function getIndexAkta($draft)
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
	public function getDetailAkta($draft)
	{
		$fractal		= new Manager();
		$resource 		= new Collection($draft, new IsiAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		return $array['data'];
	}

	/**
	 * Fractal Modifying Returned Value
	 *
	 * getStructureMultiple method used to transforming response format and included UI inside (@UInside)
	 */
	public function getEditableSingle($draft)
	{
		$fractal		= new Manager();
		$resource 		= new Collection($draft, new IsiAktaEditableTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		return $array['data'];
	}

	public function dummy()
	{
		return [['_id' => '123456789', 'title' => 'Akta Jual Beli Tanah', 'type' => 'draft_akta', 'writer' => ['_id' => '123456789', 'name' => 'Ada Lovelace'], 'owner' => ['_id' => '123456789', 'name' => 'Thunderlab Indonesia'], 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'paragraph' => [['content' => 'Isi Akta']]]];
	}
};
