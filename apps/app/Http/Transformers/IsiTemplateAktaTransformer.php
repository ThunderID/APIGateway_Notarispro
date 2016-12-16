<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class IsiTemplateAktaTransformer extends TransformerAbstract
{
	/**
	 * fungsi untuk transform data show template akta.
	 * 
	 * Perubahan ini mempengaruhi fungsi : ThunderTransformer@isi_template_akta
	 * @param  	array $value
	 * @return 	array data //lebih jelas baca dokumentasi template show
	 * 
	 */
	public function transform(array $value)
	{
		foreach ($value['paragraph'] as $key => $value2) 
		{
			$paragraph_title[$key]		= 'paragraph_'.$key;
			$paragraph_content[$key]	= ['paragraph_'.$key => $value2['content']];
		}

	    $major	= 	[
	    				'page_info' 	=> ['id' => $value['_id']],
	    				'page_data' 	=> [
	    									'title'	=> 	[
	    														'header'	=> ['title'],
	    														'data'		=> ['title' => $value['title']],
	    													],
											'content'	=> 	[
	    														'header'	=> $paragraph_title,
	    														'data'		=> $paragraph_content,
	    													],
		    								'ownership'	=> 	[
	    														'header'	=> 	['writer', 'owner'],
	    														'data'		=> 	[
	    																			'writer' => $value['writer']['name'],
	    																			'writer_id' => $value['writer']['_id'],
	    																			'owner' => $value['owner']['name'],
	    																			'owner_id' => $value['owner']['_id'],
	    																		],
	    													]
	    								   ]
					];

		return $major;
	}
}