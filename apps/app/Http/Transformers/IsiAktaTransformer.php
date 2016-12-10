<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class IsiAktaTransformer extends TransformerAbstract
{
	/**
	 * fungsi untuk transform data show template akta.
	 * 
	 * Perubahan ini mempengaruhi fungsi : ThunderTransformer@isi_document_akta
	 * @param  	array $value
	 * @return 	array data //lebih jelas baca dokumentasi akta show
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
	    																			'writerid' => $value['writer']['_id'],
	    																			'owner' => $value['owner']['name'],
	    																			'ownerid' => $value['owner']['_id'],
	    																		],
	    													]
	    								   ]
					];

		return $major;
	}
}