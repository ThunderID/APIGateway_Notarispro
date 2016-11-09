<?php 

namespace App\Entities\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Entities\WorkflowProtocol as Model; 
use App\Entities\ComponentRule; 

/**
 * Used in WorkflowProtocol model
 *
 * @author cmooy
 */
class WorkflowProtocolObserver 
{
	public function saving($model)
	{
		if(count($model->processes) > 0)
		{
			foreach ($model->processes as $key => $value) 
			{
				foreach ($value['rules'] as $idx => $rule) 
				{
					$model_rules 	= ComponentRule::component($rule)->client($model->client_identifier)->first();

					if(is_null($model_rules))
					{
						$model['errors']	= ['Invalid rules'];

						return false;
					}
				}
			}
		}
		return true;
	}
}
