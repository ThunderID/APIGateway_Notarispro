<?php

namespace App\Http\Policies;

class LockFormattor 
{
	public function parse_to_lock_structure($entity, $token, $status)
	{
		$body['pandora']['_id']		= $entity['_id'];
		$body['pandora']['type']	= $status;
		
		foreach ($entity['paragraph'] as $key => $value) 
		{
			$body['pandora']['field'][$key]	= 'paragraph.'.$key.'.content';
		}

		$body['owner']['_id']		= $entity['owner']['_id'];
		$body['owner']['name']		= $entity['owner']['name'];
		$body['owner']['type']		= $entity['owner']['type'];

		return $body;
	}
};
