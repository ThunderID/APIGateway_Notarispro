<?php 

namespace App\Entities\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Entities\ComponentRule as Model; 

/**
 * Used in ComponentRule model
 *
 * @author cmooy
 */
class ComponentRuleObserver 
{
	public function saving($model)
	{
		return true;
	}
}
