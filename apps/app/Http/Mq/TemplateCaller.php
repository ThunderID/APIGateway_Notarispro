<?php

namespace App\Http\Mq;

use App\Libraries\JSend;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use App\Http\Transformers\ListAktaTransformer;
use App\Http\Transformers\IsiAktaTransformer;
use App\Http\Transformers\IsiAktaEditableTransformer;

class TemplateCaller 
{
	public function index_caller($search, $request, $token) 
	{
		$per_page 		= (!is_null($request->input('per_page')) ? $request->input('per_page') : 20);
		$page 			= (!is_null($request->input('page')) ? max(1, $request->input('page')) : 1);
		$search['skip']	= max(0, ($page - 1)) * $per_page;
		$search['take']	= $per_page;

		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $token,
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.template.index');
		
		if(str_is($response['status'], 'success'))
		{
			$response['data']['paginator']	= ['total' => $response['data']['count'], 'current_page' => $page, 'start_number' => (($page -1)* $per_page)+1, 'per_page' => $per_page];

			unset($response['data']['count']);
		}

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
		$response 		= $mq->call($data, 'tlab.template.index');

		if(!str_is($response['status'], 'success') || !count($response['data']['data']) > 0)
		{
			$response 	= JSend::error(['Tidak dapat melihat template Akta yang bukan milik Anda!'])->asArray();
			
			return $response;
		}
		else
		{
			unset($response['data']['count']);
		}
		
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
		$response 		= $mq->call($data, 'tlab.template.index');

		if(str_is($response['status'], 'success') && count($response['data']['data']) > 0)
		{
			unset($response['data']['count']);
		}
		elseif(str_is($response['status'], 'success'))
		{
			$response['data']['data']	= $this->dummy();
			unset($response['data']['count']);
		}
		
		//2. transform returned value
		return $response;
	}

	public function store_caller($param, $request, $token) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $token,
												],
								'body'		=> 	$param,
							];
		$data 			= json_encode($attributes);

		$mq 			= new MessageQueueCaller();
		$response 		= $mq->call($data, 'tlab.template.store');

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
		$response 		= $mq->call($data, 'tlab.template.delete');

		//2. transform returned value
		return $response;
	}

	public function dummy()
	{
		return [['_id' => '123456789', 'title' => 'Akta Jual Beli Tanah', 'writer' => ['_id' => '123456789', 'name' => 'Ada Lovelace'], 'owner' => ['_id' => '123456789', 'name' => 'Thunderlab Indonesia'], 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'paragraph' => [['content' => 'Isi Akta']]]];
	}
};
