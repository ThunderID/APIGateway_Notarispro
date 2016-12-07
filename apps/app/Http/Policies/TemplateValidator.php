<?php

namespace App\Http\Policies;

use App\Http\Mq\TemplateCaller;

class TemplateValidator 
{
	public $error;
	public $data;

	public function is_okay_to_templating($search, $request, $token)
	{
		$template 	= new TemplateCaller;

		$response 	= $template->show_caller($search, $request, $token);

		if(!str_is($response['status'], 'success') || count($response['data']['data']) < 1)
		{
			$this->error 	= 'Tidak dapat menyimpan draft yang bukan Milik Anda!';

			return false;
		}

		$this->data = $response['data']['data'][0];

		return true;
	}
};
