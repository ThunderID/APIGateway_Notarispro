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
			'input-string'	=> 	[
									[
										'name' 			=> 'id',
										'value'			=> $value['_id'],
									],
									[
										'name' 			=> 'title',
										'value'			=> $value['title'],
										'validation'	=> ['required' => true],
									],
									[
										'name' 			=> 'writer.id',
										'value'			=> $value['writer']['_id'],
										'validation'	=> ['required' => true],
									],
									[
										'name' 			=> 'writer.name',
										'value'			=> $value['writer']['name'],
										'validation'	=> ['required' => true],
									],
									[
										'name' 			=> 'owner.id',
										'value'			=> $value['owner']['_id'],
										'validation'	=> ['required' => true],
									],
									[
										'name' 			=> 'owner.name',
										'value'			=> $value['owner']['name'],
										'validation'	=> ['required' => true],
									],
								],
			'input-multiSelect'	=> 	[
									[
										'name' 			=> 'type',
										'value'			=> $value['type'],
										'validation'	=> ['required' => true],
										'list'			=> ['draft_akta', 'proposed_akta', 'renvoi_akta', 'akta'],
									],
								],
			'input-datetime'=> 	[
									[
										'name' 			=> 'created_at',
										'value'			=> $value['created_at'],
										'type' 			=> 'datetime',
										'GMT'			=> env('APP_TIMEZONE', 'Asia/Jakarta'),
									],
									[
										'name' 			=> 'updated_at',
										'value'			=> $value['updated_at'],
										'type' 			=> 'datetime',
										'GMT'			=> env('APP_TIMEZONE', 'Asia/Jakarta'),
									],
									[
										'name' 			=> 'deleted_at',
										'value'			=> null,
										'type' 			=> 'datetime',
										'GMT'			=> env('APP_TIMEZONE', 'Asia/Jakarta'),
									]
								],
		];
	}
}