<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ParagraphTransformer extends TransformerAbstract
{
	public function transform(array $value)
	{
		return 	[
	    			'element-class'			=> 'input',
	    			'element-type'			=> 'string',
	    			'element-properties'	=> 	[
	    											'name'	=> 'paragraph[]',
	    											'value'	=> $value['content'],
	    										],
		    	];
	}
}