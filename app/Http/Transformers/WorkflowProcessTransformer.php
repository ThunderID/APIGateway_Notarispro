<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use App\Http\Transformers\WorkflowProcessProcessesTransformer;

class WorkflowProcessTransformer extends TransformerAbstract
{
	public function transform(array $rule)
	{
		$fractal		= new Manager();
		$resource 		= new Collection($rule['processes'], new WorkflowProcessProcessesTransformer);
		$array 			= $fractal->createData($resource)->toArray();

	    return [
	        'id' 		=> [
								'value' => $rule['_id'],
								'type'	=> 'string',
								'max'	=> '255',
							],
			'client_identifier' => [
								'value' => $rule['client_identifier'],
								'type'	=> 'string',
								'max'	=> '255',
							],
			'trigger' 	=> [
								'value' => $rule['trigger'],
								'type'	=> 'string',
								'max'	=> '255',
							],
			'ticket' 	=> [
								'value' => $rule['ticket'],
								'type'	=> 'string',
								'max'	=> '255',
							],
			'method' 	=> [
								'value' => $rule['method'],
								'type'	=> 'string',
								'max'	=> '255',
							],
			'status' 	=> [
								'value'		=> $rule['status'],
								'type'		=> 'enum',
								'option'	=> 'waiting,failed,succeed',
							],
			'processes'	=> [
								'value' => $array['data'],
								'type'	=> 'array',
								'max'	=> '255',
							],
			'created_at'=> [
								'value' => $rule['created_at'],
								'type'	=> 'datetime',
								'zone'	=> env('APP_TIMEZONE', ''),
								'format'=> 'Y-m-d H:i:s',
							],
			'updated_at' => [
								'value' => $rule['updated_at'],
								'type'	=> 'datetime',
								'zone'	=> env('APP_TIMEZONE', ''),
								'format'=> 'Y-m-d H:i:s',
							],
			'deleted_at' => [
								'value' => null,
								'type'	=> 'datetime',
								'zone'	=> env('APP_TIMEZONE', ''),
								'format'=> 'Y-m-d H:i:s',
							],
		];
	}
}