<?php

namespace App\Http\Policies;

use Illuminate\Http\Request;

class LockFormattor 
{
	/**
	 * fungsi untuk format seluruh konten lock
	 * 
	 * Perubahan ini mempengaruhi fungsi route : DraftAktaController@issue
	 * @param   array $prev_data
	 * @param  	string $status
	 * @return 	array $body
	 * 
	 */
	public function formatting_whole_content(array $prev_data, $status)
	{
		$body['pandora']['_id']		= $prev_data['_id'];
		$body['pandora']['type']	= $status;
		
		foreach ($prev_data['paragraph'] as $key => $value) 
		{
			$body['pandora']['field'][$key]	= 'paragraph.'.$key.'.content';
		}

		$body['owner']['_id']		= $prev_data['owner']['_id'];
		$body['owner']['name']		= $prev_data['owner']['name'];
		$body['owner']['type']		= $prev_data['owner']['type'];

		return $body;
	}
	
	/**
	 * fungsi untuk format unlocked paragraph
	 * 
	 * Perubahan ini mempengaruhi fungsi route : ProposedAktaController@issue
	 * @param   array $prev_data
	 * @param  	string $status
	 * @param  	\Illuminate\Http\Request $request
	 * @return 	array $body
	 * 
	 */
	public function formatting_certain_paragraph(array $prev_data, $status, Request $request)
	{
		$body 						= $prev_data;
		$body['id'] 				= $prev_data['_id'];
		$body['pandora']['type']	= $status;
		
		foreach ($request->input('paragraph') as $key => $value) 
		{
			$body['pandora']['field'][$key]	= 'paragraph.'.$key.'.content';
		}

		return $body;
	}
};
