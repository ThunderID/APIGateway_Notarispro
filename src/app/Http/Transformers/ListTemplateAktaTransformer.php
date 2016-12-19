<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListTemplateAktaTransformer extends TransformerAbstract
{
	/**
	 * fungsi untuk transform data index template akta.
	 * 
	 * Perubahan ini mempengaruhi fungsi : ThunderTransformer@list_template_akta
	 * @param  	array $value
	 * @return 	array data //lebih jelas baca dokumentasi template index
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