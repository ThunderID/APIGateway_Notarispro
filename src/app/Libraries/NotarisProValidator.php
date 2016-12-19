<?php

namespace App\Libraries;

use Illuminate\Http\Request;

class NotarisProValidator 
{
	public $error;
	public $data;

	/**
	 * fungsi untuk mengecek apakah data exists via mq based on spesific topic.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : TemplateAktaController@store
	 * @param  	array $search
	 * @param  	\Illuminate\Http\Request $request
	 * @param  	string $topic
	 * @return 	array JSend
	 * 
	 */
	public function is_locked_paragraph(array $search, Request $request, string $topic) 
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

		if((str_is($response['status'], 'success') && count($response['data']['data']) <= 0) || !str_is($response['status'], 'success'))
		{
			$this->error= 'Data tidak ditemukan!';

			return false;
		}

		$this->data 	= $response['data']['data'][0];

		return true;
	}
};
