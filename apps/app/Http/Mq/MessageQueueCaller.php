<?php

namespace App\Http\Mq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageQueueCaller 
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

	public function on_response($rep) 
	{
		if($rep->get('correlation_id') == $this->corr_id) 
		{
			$this->response 			= $rep->body;
		}
	}

	public function call($body, $topic) 
	{
		$this->response 	= null;
		$this->corr_id 		= uniqid();

		$msg 				= new AMQPMessage((string) $body, array('correlation_id' => $this->corr_id, 'reply_to' => $this->callback_queue));

		$this->channel->basic_publish($msg, '', $topic);
		
		while(!$this->response) 
		{
			$this->channel->wait();
		}

		$result 			= json_decode($this->response, true);

		return $result;
	}
};
