<?php

namespace App\Http\Policies;

use Illuminate\Http\Request;

class TemplateFormattor 
{
	/**
	 * fungsi untuk format seluruh konten template
	 * 
	 * Perubahan ini mempengaruhi fungsi route : TemplateAktaController@store
	 * @param  	\Illuminate\Http\Request $request
	 * @param  	string $status
	 * @return 	array $body
	 * 
	 */
	public function formatting_whole_content(Request $request, $status)
	{
		$writerid 	= $request->input('writerid');
		$writername = $request->input('writername');
		$ownerid 	= $request->input('ownerid');
		$ownername 	= $request->input('ownername');
		$ownertype 	= $request->input('ownertype');

		$body 						= $request->input();

		$body['writer']['_id']		= $writerid;
		$body['writer']['name']		= $writername;
		
		$body['owner']['_id']		= $ownerid;
		$body['owner']['type']		= $ownertype;
		$body['owner']['name']		= $ownername;
		
		foreach ($body['paragraph'] as $key => $value) 
		{
			$body['paragraph'][$key]= ['content' => $value];
		}

		$body['type']				= $status;

		return $body;
	}

	/**
	 * fungsi untuk format seluruh konten template
	 * 
	 * Perubahan ini mempengaruhi fungsi route : TemplateAktaController@issue
	 * @param  	string $status
	 * @param  	array $prev_data
	 * @return 	array $body
	 * 
	 */
	public function formatting_status(array $prev_data, $status)
	{
		$body 						= $prev_data;
		$body['id']					= $prev_data['_id'];
		$body['type']				= $status;

		return $body;
	}
};
