<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class IsiAktaEditableTransformer extends TransformerAbstract
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
					    											'name'	=> 'id',
					    											'value'	=> $value['_id'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'title',
					    											'value'	=> $value['title'],
																	'validation'	=> ['required' => true, 'max' => '255'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'writer_id',
					    											'value'	=> $value['writer']['_id'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'writer_name',
					    											'value'	=> $value['writer']['name'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'owner_id',
					    											'value'	=> $value['owner']['_id'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'owner_name',
					    											'value'	=> $value['owner']['name'],
					    										],
					    		],
					    		]
		];

		$major['elements'] = array_merge($major['elements'], $minor['data']);

		return $major;
	}
}