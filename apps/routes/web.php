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

$app->get('/', function () use ($app) 
{
    return 'Welcome to Notarispro.com API Gateway';
});


$app->group(['namespace' => 'App\Http\Controllers'], function ($app) 
{
	$app->get('/akta',
		[
			'uses'				=> 'AktaController@index',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->post('/akta',
		[
			'uses'				=> 'AktaController@post',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->delete('/akta',
		[
			'uses'				=> 'AktaController@delete',
			// 'middleware'		=> 'jwt|company:delete-akta',
		]
	);
});


$api 							= app('Dingo\Api\Routing\Router');

// $api->version('v1', function ($api) 
// {
//     $api->group(['namespace' => 'App\Http\Controllers'], function ($api) 
// 	{
// 		$api->get('/akta',
// 			[
// 				'uses'				=> 'AktaController@index',
// 				// 'middleware'		=> 'jwt|company:read-akta',
// 			]
// 		);

// 		$api->post('/akta',
// 			[
// 				'uses'				=> 'AktaController@post',
// 				// 'middleware'		=> 'jwt|company:store-akta',
// 			]
// 		);

// 		$api->delete('/akta',
// 			[
// 				'uses'				=> 'AktaController@delete',
// 				// 'middleware'		=> 'jwt|company:delete-akta',
// 			]
// 		);
// 	});
// });
