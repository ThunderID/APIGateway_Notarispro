<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ParagraphLockTransformer extends TransformerAbstract
{
	public function transform(array $value, $lock)
	{
		$data	= [
	    			'element-class'			=> 'input',
	    			'element-type'			=> 'string',
	    			'element-properties'	=> 	[
	    											'name'	=> 'paragraph[]',
	    											'value'	=> $value['content'],
	    										],
		    	];
	}
}