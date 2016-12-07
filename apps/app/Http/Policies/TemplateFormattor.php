<?php

namespace App\Http\Policies;

class TemplateFormattor 
{
	public function parse_to_draft_structure($request, $token, $status)
	{
		$writerid 	= $token->getClaim('pid');
		$writername = $token->getClaim('pname');
		$ownerid 	= $token->getClaim('oid');
		$ownername 	= $token->getClaim('oname');

		$body 						= $request->input();

		$body['writer']['_id']		= $writerid;
		$body['writer']['name']		= $writername;
		
		$body['owner']['_id']		= $ownerid;
		$body['owner']['type']		= 'organization';
		$body['owner']['name']		= $ownername;
		
		$body['type']				= $status;

		foreach ($body['paragraph'] as $key => $value) 
		{
			$body['paragraph'][$key]= ['content' => $value];
		}

		$body['type']				= $status;

		return $body;
	}

	public function parse_to_template_structure($prev_input, $token, $status)
	{
		$ownerid 	= $token->getClaim('oid');
		$ownername 	= $token->getClaim('oname');

		$body 					= $prev_input;
		$body['id'] 			= $prev_input['_id'];
		$body['type']			= $status;

		return $body;
	}
};
