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
	//Sprint I
	$app->get('/lihat/list/template',
		[
			'uses'				=> 'TemplateAktaController@index',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/lihat/isi/template',
		[
			'uses'				=> 'TemplateAktaController@show',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->post('/simpan/template',
		[
			'uses'				=> 'TemplateAktaController@store',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->delete('/hapus/template',
		[
			'uses'				=> 'TemplateAktaController@delete',
			// 'middleware'		=> 'jwt|company:delete-akta',
		]
	);
	
	$app->post('/issue/template',
		[
			'uses'				=> 'TemplateAktaController@issue',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);
	
	$app->post('/void/template',
		[
			'uses'				=> 'TemplateAktaController@void',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->get('/lihat/list/draft/akta',
		[
			'uses'				=> 'DraftAktaController@index',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/lihat/isi/draft/akta',
		[
			'uses'				=> 'DraftAktaController@show',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/mulai/draft/akta',
		[
			'uses'				=> 'DraftAktaController@edit',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/edit/isi/draft/akta',
		[
			'uses'				=> 'DraftAktaController@edit',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->post('/simpan/draft/akta',
		[
			'uses'				=> 'DraftAktaController@store',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	$app->delete('/hapus/draft/akta',
		[
			'uses'				=> 'DraftAktaController@delete',
			// 'middleware'		=> 'jwt|company:delete-akta',
		]
	);

	$app->post('/issue/draft/akta',
		[
			'uses'				=> 'DraftAktaController@issue',
			// 'middleware'		=> 'jwt|company:store-akta',
		]
	);

	//Sprint II
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

	$app->delete('/void/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@void',
			// 'middleware'		=> 'jwt|company:store-akta',
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

	//Sprint III
	$app->get('/lihat/list/akta',
		[
			'uses'				=> 'AktaController@index',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);
	
	$app->get('/lihat/isi/akta',
		[
			'uses'				=> 'AktaController@show',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);

	$app->get('/print/akta',
		[
			'uses'				=> 'AktaController@print',
			// 'middleware'		=> 'jwt|company:read-akta',
		]
	);
});

