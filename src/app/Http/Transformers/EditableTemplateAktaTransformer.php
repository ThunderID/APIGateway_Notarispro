<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class EditableTemplateAktaTransformer extends TransformerAbstract
{
		/**
	 * fungsi untuk transform data show template akta.
	 * 
	 * Perubahan ini mempengaruhi fungsi : ThunderTransformer@edit_template_akta
	 * @param  	array $value
	 * @return 	array data //lebih jelas baca dokumentasi template show
	 * 
	 */
	public function transform(array $value)
	{
		foreach ($value['paragraph'] as $key => $value2) 
		{
			$paragraph_title[$key]					= 'paragraph['.$key.']';
			$paragraph_content['paragraph['.$key.']']	= 	[
															'element-class'			=> 'input',
															'element-type'			=> 'text',
															'element-properties'	=> 
																[
		    														'value'			=> $value2['content'],
		    														'validation' 	=> 
		    															[
		    																'required'	=> true,
		    															],
																],
														];
		}

	    $major	= 	[
	    				'page_info' => ['id' => $value['_id']],
	    				'page_data' => 
	    					[
	    						'title'	=> 	
	    							[
	    								'header'	=> 	['title'],
	    								'data'		=> 	
	    									[
	    										'title' =>
	    											[
	    												'element-class'		=> 'input',
	    												'element-type'		=> 'string',
	    												'element-properties'=> 
	    													[
	    														'value'			=> $value['title'],
	    														'validation' 	=> 
	    															[
	    																'required'	=> true,
	    																'max'		=> 255,
	    															],
															],
													]
											],
									],
								'content'	=> 	
									[
	    								'header'	=> $paragraph_title,
										'data'		=> $paragraph_content,
	    							],
				   			]
					];

		return $major;
	}
}
