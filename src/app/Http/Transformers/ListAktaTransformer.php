<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListAktaTransformer extends TransformerAbstract
{
	/**
	 * fungsi untuk transform data index document akta.
	 * 
	 * Perubahan ini mempengaruhi fungsi : ThunderTransformer@list_document_akta
	 * @param  	array $value
	 * @return 	array data //lebih jelas baca dokumentasi akta index
	 * 
	 */
	public function transform(array $value)
	{
	    return	[
					'id' 			=> $value['_id'],
					'title' 		=> $value['title'],
					'type' 			=> $value['type'],
					'writer' 		=> $value['writer']['name'],
					'writer_id' 	=> $value['writer']['_id'],
					'last_update' 	=> $value['updated_at'],
				];
	}
}