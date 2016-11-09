<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class WorkflowProcessProcessesTransformer extends TransformerAbstract
{
	public function transform(array $process)
	{
	    return [
			'parameters' 	=> [
								'value' => $process['parameters'],
								'type'	=> 'array',
								'array'	=> 'string',
							],
			'command' 		=> [
								'value' => $process['command'],
								'type'	=> 'string',
								'max'	=> '255',
							],
			'status' 		=> [
								'value' 	=> $process['status'],
								'type'		=> 'enum',
								'option'	=> 'waiting,failed,succeed',
							],
			'current_data_version' 		=> [
								'value' => $process['current_data_version'],
								'type'	=> 'integer',
							],
			'prev_data_version' 		=> [
								'value' => $process['prev_data_version'],
								'type'	=> 'integer',
							],
		];
	}
}