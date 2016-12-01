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
	$app->get('/lihat/list/draft',
		[
			'uses'				=> 'DraftAktaController@index',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/lihat/isi/draft',
		[
			'uses'				=> 'DraftAktaController@show',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->post('/simpan/draft',
		[
			'uses'				=> 'DraftAktaController@store',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->delete('/hapus/draft',
		[
			'uses'				=> 'DraftAktaController@delete',
			// 'middleware'		=> 'jwt|company:delete-akta',
		]
	);

	$app->post('/issue/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@issue',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->get('/lihat/list/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@index',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/lihat/isi/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@show',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->post('/issue/renvoi',
		[
			'uses'				=> 'RenvoiController@issue',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->get('/lihat/list/renvoi',
		[
			'uses'				=> 'RenvoiController@index',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/lihat/isi/renvoi',
		[
			'uses'				=> 'RenvoiController@show',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->post('/simpan/renvoi',
		[
			'uses'				=> 'RenvoiController@store',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->post('/handover/akta',
		[
			'uses'				=> 'AktaController@handover',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->delete('/void/akta',
		[
			'uses'				=> 'AktaController@void',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);
});

