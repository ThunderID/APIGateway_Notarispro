<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class HandoverAktaEditableTransformer extends TransformerAbstract
{
	public function transform(array $akta, array $writer)
	{
		$major['info']		= [];

		foreach ($writer as $key => $value) 
		{
			$lists[$value['_id']]	= $value['name'];
		}

		$minor[0]['element-class']		= 'input';
		$minor[0]['element-type']		= 'select';
		$minor[0]['element-properties']	= 	[
												'name' 			=> 'writer_name',
												'value' 		=> $akta['writer']['name'],
												'validation' 	=> 	[
																		'required'	=> true,
																		'max'		=> '255',
																	],
												'lists'			=> $lists,
											];
		$major['elements']	= $minor;

		return $major;
	}
}