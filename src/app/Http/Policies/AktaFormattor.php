<?php

namespace App\Http\Policies;

use Lcobucci\JWT\Parser;

use Illuminate\Http\Request;

class AktaFormattor 
{
	/**
	 * fungsi untuk format seluruh konten akta
	 * 
	 * Perubahan ini mempengaruhi fungsi route : DraftAktaController@store
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
	 * fungsi untuk paragraph tertentu dari akta
	 * 
	 * Perubahan ini mempengaruhi fungsi route : RenvoiController@store
	 * @param  	\Illuminate\Http\Request $request
	 * @param  	string $status
	 * @return 	array $body
	 * 
	 */
	public function formatting_certain_paragraph(array $prev_data, $status, Request $request)
	{
		$body 						= $prev_data;
		$body['id'] 				= $prev_data['_id'];

		foreach ($request->input('paragraph') as $key => $value) 
		{
			$body['paragraph'][$key]= ['content' => $value];
		}

		return $body;
	}

	/**
	 * fungsi untuk format seluruh konten akta
	 * 
	 * Perubahan ini mempengaruhi fungsi route : DraftAktaController@issue
	 * @param  	array $prev_data
	 * @param  	string $status
	 * @param  	\Illuminate\Http\Request $request
	 * @return 	array $body
	 * 
	 */
	public function formatting_status_owner_organization(array $prev_data, $status, Request $request)
	{
		$token  = $request->header('Authorization');

		$tokens 	= explode(' ', $token);

		$token 		= $tokens[count($tokens) - 1];

		$token		= (new Parser())->parse((string) $token); // Parses from a string

		$body 						= $prev_data;
		$body['id'] 				= $prev_data['_id'];

		$body['owner']['_id']		= $token->getClaim('oid');
		$body['owner']['name']		= $token->getClaim('oname');
		$body['owner']['type']		= 'organization';

		$body['type']				= $status;

		return $body;
	}

	/**
	 * fungsi untuk format seluruh konten akta
	 * 
	 * Perubahan ini mempengaruhi fungsi route : DraftAktaController@issue
	 * @param  	array $prev_data
	 * @param  	\Illuminate\Http\Request $request
	 * @return 	array $body
	 * 
	 */
	public function formatting_writer(array $prev_data, Request $request)
	{
		$body 						= $prev_data;
		$body['id'] 				= $prev_data['_id'];

		$body['writer']['_id']		= $request->input('writer_id');
		unset($body['writer']['name']);
		// $body['writer']['name']		= $request->input('writer_name');
		
		if($prev_data['owner']['type']=='person')
		{
			$body['owner']['id']	= $request->input('writer_id');
			unset($body['owner']['name']);
		}

		return $body;
	}
};
