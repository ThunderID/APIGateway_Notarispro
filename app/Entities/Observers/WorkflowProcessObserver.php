<?php 

namespace App\Entities\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Entities\WorkflowProcess as Model; 

/**
 * Used in WorkflowProcess model
 *
 * @author cmooy
 */
class WorkflowProcessObserver 
{
	public function saving($model)
	{
		return true;
	}
}
