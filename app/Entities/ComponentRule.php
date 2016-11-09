<?php

namespace App\Entities;

use App\Entities\Observers\ComponentRuleObserver;

/**
 * Used for ComponentRule Models
 * 
 * @author cmooy
 */
class ComponentRule extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $collection			= 'bpm_component_rules';

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
											'component'						,
											'rule'							,
											'description'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'client_identifier'				=> 'required|max:255',
											'component'						=> 'required|max:255',
											'rule'							=> 'required|max:255',
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

		ComponentRule::observe(new ComponentRuleObserver);
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
	 * scope to get condition where component
	 *
	 * @param string or array of component
	 **/
	public function scopeComponent($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('component', $variable);
		}

		return $query->where('component', 'regexp', '/^'. preg_quote($variable) .'$/i');
	}
}
