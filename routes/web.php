<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});


$app->group(['namespace' => 'App\Http\Controllers'], function ($app) 
{
	$app->get('/business/rules',
		[
			'uses'				=> 'ComponentRuleController@index',
			// 'middleware'		=> 'jwt|company:read-purchase.order',
		]
	);

	$app->post('/business/rules',
		[
			'uses'				=> 'ComponentRuleController@post',
			// 'middleware'		=> 'jwt|company:store-purchase.order',
		]
	);

	$app->delete('/business/rules',
		[
			'uses'				=> 'ComponentRuleController@delete',
			// 'middleware'		=> 'jwt|company:delete-purchase.order',
		]
	);

	$app->get('/workflow/protocols',
		[
			'uses'				=> 'WorkflowProtocolController@index',
			// 'middleware'		=> 'jwt|company:read-purchase.order',
		]
	);

	$app->post('/workflow/protocols',
		[
			'uses'				=> 'WorkflowProtocolController@post',
			// 'middleware'		=> 'jwt|company:store-purchase.order',
		]
	);

	$app->delete('/workflow/protocols',
		[
			'uses'				=> 'WorkflowProtocolController@delete',
			// 'middleware'		=> 'jwt|company:delete-purchase.order',
		]
	);

	$app->get('/workflow/processes',
		[
			'uses'				=> 'WorkflowProcessController@index',
			// 'middleware'		=> 'jwt|company:read-purchase.order',
		]
	);

	$app->post('/workflow/processes',
		[
			'uses'				=> 'WorkflowProcessController@post',
			// 'middleware'		=> 'jwt|company:store-purchase.order',
		]
	);

	$app->delete('/workflow/processes',
		[
			'uses'				=> 'WorkflowProcessController@delete',
			// 'middleware'		=> 'jwt|company:delete-purchase.order',
		]
	);
});
