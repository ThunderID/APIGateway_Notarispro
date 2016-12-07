<?php

namespace App\Http\Mq;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class LockCaller 
{
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
		$response 		= $mq->call($data, 'tlab.lock.store');

		//2. transform returned value
		return $response;
	}
};
