<?php

namespace App\Entities;

use App\Entities\Observers\WorkflowProcessObserver;

/**
 * Used for WorkflowProcess Models
 * 
 * @author cmooy
 */
class WorkflowProcess extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $collection			= 'bpm_workflow_processes';

	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'client_identifier'				,
											'trigger'						,
											'ticket'						,
											'method'						,
											'status'						,
											'processes'						,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'client_identifier'					=> 'required|max:255',
											'trigger'							=> 'required|max:255',
											'ticket'							=> 'required|max:255',
											'method'							=> 'required|in:get,post,delete',
											'status'							=> 'required|max:255',
											'processes.*.parameters.*'			=> 'max:255',
											'processes.*.command'				=> 'max:255',
											'processes.*.status'				=> 'max:255',
											'processes.*.current_data_version'	=> 'max:255',
											'processes.*.prev_data_version'		=> 'max:255',
										];


	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();

		WorkflowProcess::observe(new WorkflowProcessObserver);
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to get condition where client identifier
	 *
	 * @param string or array of client identifier
	 **/
	public function scopeClient($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('client_identifier', $variable);
		}

		return $query->where('client_identifier', 'regexp', '/^'. preg_quote($variable) .'$/i');
	}

	/**
	 * scope to get condition where trigger
	 *
	 * @param string or array of trigger
	 **/
	public function scopeTrigger($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('trigger', $variable);
		}

		return $query->where('trigger', 'regexp', '/^'. preg_quote($variable) .'$/i');
	}
}
