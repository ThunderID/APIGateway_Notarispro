<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class IsiTemplateAktaEditableTransformer extends TransformerAbstract
{
	public function transform(array $value)
	{
		$fractal		= new Manager();
		$resource 		= new Collection($value['paragraph'], new ParagraphTransformer);
		$minor 			= $fractal->createData($resource)->toArray();

	    $major = [
			    'info' 		=> ['id' => $value['_id']],
	    		'elements' 	=> [
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'title',
					    											'value'	=> $value['title'],
																	'validation'	=> ['required' => true, 'max' => '255'],
					    										],
					    		],
					    		]
		];

		$major['elements'] = array_merge($major['elements'], $minor['data']);

		return $major;
	}
}