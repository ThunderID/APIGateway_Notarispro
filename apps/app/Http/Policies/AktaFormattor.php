<?php

namespace App\Http\Policies;

class AktaFormattor 
{
	public function parse_to_draft_structure($request, $token, $status)
	{
		$writerid 	= $token->getClaim('pid');
		$writername = $token->getClaim('pname');

		$body 					= $request->input();

		$body['owner']['_id']	= $writerid;
		$body['owner']['type']	= 'person';
		$body['owner']['name']	= $writername;

		foreach ($body['paragraph'] as $key => $value) 
		{
			$body['paragraph'][$key]= ['content' => $value];
		}
	
		$body['writer']['_id']		= $writerid;
		$body['writer']['name']		= $writername;
		$body['type']				= $status;

		return $body;
	}

	public function parse_to_akta_structure($prev_input, $token, $status)
	{
		$ownerid 	= $token->getClaim('oid');
		$ownername 	= $token->getClaim('oname');

		$body 					= $prev_input;
		$body['id'] 			= $prev_input['_id'];
		$body['owner']['_id']	= $ownerid;
		$body['owner']['name']	= $ownername;
		$body['owner']['type']	= 'organization';
		$body['type']			= $status;

		return $body;
	}
};
