<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class WorkflowProtocolProcessesTransformer extends TransformerAbstract
{
	public function transform(array $process)
	{
	    return [
	        'rules' 		=> [
								'value' => $process['rules'],
								'type'	=> 'array',
								'array'	=> 'string',
							],
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
		];
	}
}