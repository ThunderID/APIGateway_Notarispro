<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListAktaTransformer extends TransformerAbstract
{
	public function transform(array $value)
	{
	    return [
			    'fragment' 	=> ['id' => $value['_id']],
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
					    											'name'	=> 'writer.id',
					    											'value'	=> $value['writer']['_id'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'writer.name',
					    											'value'	=> $value['writer']['name'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'owner.id',
					    											'value'	=> $value['owner']['_id'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'owner.name',
					    											'value'	=> $value['owner']['name'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'datetime',
					    			'element-properties'	=> 	[
					    											'name'	=> 'created_at',
					    											'value'	=> $value['created_at'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'updated_at',
					    											'value'	=> $value['updated_at'],
					    										],
					    		],
					    		]
		];
	}
}