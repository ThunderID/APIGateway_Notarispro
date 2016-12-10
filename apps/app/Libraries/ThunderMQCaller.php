<?php

namespace App\Libraries;

use Illuminate\Http\Request;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ThunderMQCaller 
{
	private $connection;
	private $channel;
	private $callback_queue;
	private $response;
	private $corr_id;

	public function __construct() 
	{
		$this->connection 				= new AMQPStreamConnection('172.17.0.2', 5672, 'guest', 'guest');
		$this->channel 					= $this->connection->channel();
		list($this->callback_queue, ,) 	= $this->channel->queue_declare("", false, false, true, false);

		$this->channel->basic_consume($this->callback_queue, '', false, false, false, false, array($this, 'on_response'));
	}

	//
	public function on_response($rep) 
	{
		if($rep->get('correlation_id') == $this->corr_id) 
		{
			$this->response 			= $rep->body;
		}
	}

	/**
	 * fungsi untuk mq index caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $search
	 * @param  	\Illuminate\Http\Request $request
	 * @param  	string $topic
	 * @return 	array JSend
	 * 
	 */
	public function index_caller(array $search, Request $request, string $topic) 
	{
		$per_page 		= (!is_null($request->input('per_page')) ? $request->input('per_page') : 20);
		$page 			= (!is_null($request->input('page')) ? max(1, $request->input('page')) : 1);

		$search['skip']	= max(0, ($page - 1)) * $per_page;
		$search['take']	= $per_page;

		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $request->input('thundertoken'),
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$this->response = null;
		$this->corr_id 	= uniqid();

		$msg 			= new AMQPMessage((string) $data, array('correlation_id' => $this->corr_id, 'reply_to' => $this->callback_queue));

		$this->channel->basic_publish($msg, '', $topic);
		
		while(!$this->response) 
		{
			$this->channel->wait();
		}

		$response 		= json_decode($this->response, true);

		if(str_is($response['status'], 'success'))
		{
			$response['data']['page_info']	= ['total_data' => $response['data']['count'], 'pagination' => ['current_page' => $page, 'start_number' => (($page -1)* $per_page)+1, 'per_page' => $per_page]];

			unset($response['data']['count']);
		}

		return $response;
	}

	/**
	 * fungsi untuk mq edit caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $search
	 * @param  	\Illuminate\Http\Request $request
	 * @param  	string $topic
	 * @return 	array JSend
	 * 
	 */
	public function edit_caller(array $search, Request $request, string $topic) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $request->input('thundertoken'),
												],
								'body'		=> 	$search,
							];
		$data 			= json_encode($attributes);

		$this->response = null;
		$this->corr_id 	= uniqid();

		$msg 			= new AMQPMessage((string) $data, array('correlation_id' => $this->corr_id, 'reply_to' => $this->callback_queue));

		$this->channel->basic_publish($msg, '', $topic);
		
		while(!$this->response) 
		{
			$this->channel->wait();
		}

		$response 		= json_decode($this->response, true);

		if(str_is($response['status'], 'success'))
		{
			unset($response['data']['count']);
		}

		return $response;
	}

	/**
	 * fungsi untuk mq store caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $param
	 * @param  	\Illuminate\Http\Request $request
	 * @param  	string $topic
	 * @return 	array JSend
	 * 
	 */
	public function store_caller(array $param, Request $request, string $topic) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $request->input('thundertoken'),
												],
								'body'		=> 	$param,
							];
		$data 			= json_encode($attributes);

		$this->response = null;
		$this->corr_id 	= uniqid();

		$msg 			= new AMQPMessage((string) $data, array('correlation_id' => $this->corr_id, 'reply_to' => $this->callback_queue));

		$this->channel->basic_publish($msg, '', $topic);
		
		while(!$this->response) 
		{
			$this->channel->wait();
		}

		$response 		= json_decode($this->response, true);

		if(str_is($response['status'], 'success'))
		{
			$response['data']['data'] 	= [$response['data']];
			unset($response['data']['count']);
		}

		return $response;
	}

	/**
	 * fungsi untuk mq delete caller based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController, DraftAktaController, ProposedAktaController, RenvoiController, dan AktaController
	 * @param  	array $param
	 * @param  	\Illuminate\Http\Request $request
	 * @param  	string $topic
	 * @return 	array JSend
	 * 
	 */
	public function delete_caller(array $param, Request $request, string $topic) 
	{
		$attributes 	= 	[
								'header'	=>
												[
													'token'		=>  $request->input('thundertoken'),
												],
								'body'		=> 	$param,
							];
		$data 			= json_encode($attributes);

		$this->response = null;
		$this->corr_id 	= uniqid();

		$msg 			= new AMQPMessage((string) $data, array('correlation_id' => $this->corr_id, 'reply_to' => $this->callback_queue));

		$this->channel->basic_publish($msg, '', $topic);
		
		while(!$this->response) 
		{
			$this->channel->wait();
		}

		$response 		= json_decode($this->response, true);

		if(str_is($response['status'], 'success'))
		{
			$response['data']['data'] 	= [$response['data']];
			unset($response['data']['count']);
		}

		return $response;
	}
};
