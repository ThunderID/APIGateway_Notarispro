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
	$app->get('/lihat/list/template/akta',
		[
			'uses'				=> 'TemplateAktaController@index',
		]
	);

	$app->get('/lihat/isi/template/akta',
		[
			'uses'				=> 'TemplateAktaController@show',
		]
	);

	$app->get('/mulai/template/akta',
		[
			'uses'				=> 'TemplateAktaController@edit',
		]
	);

	$app->get('/edit/isi/template/akta',
		[
			'uses'				=> 'TemplateAktaController@edit',
		]
	);
	
	$app->post('/simpan/template/akta',
		[
			'uses'				=> 'TemplateAktaController@store',
		]
	);
	
	$app->post('/update/template/akta',
		[
			'uses'				=> 'TemplateAktaController@store',
		]
	);

	$app->delete('/hapus/template/akta',
		[
			'uses'				=> 'TemplateAktaController@delete',
		]
	);
	
	$app->post('/issue/template/akta',
		[
			'uses'				=> 'TemplateAktaController@issue',
		]
	);
	
	$app->post('/void/template/akta',
		[
			'uses'				=> 'TemplateAktaController@void',
		]
	);

	$app->get('/lihat/list/draft/akta',
		[
			'uses'				=> 'DraftAktaController@index',
		]
	);

	$app->get('/lihat/isi/draft/akta',
		[
			'uses'				=> 'DraftAktaController@show',
		]
	);

	$app->get('/mulai/draft/akta',
		[
			'uses'				=> 'DraftAktaController@edit',
		]
	);

	$app->get('/edit/isi/draft/akta',
		[
			'uses'				=> 'DraftAktaController@edit',
		]
	);

	$app->post('/simpan/draft/akta',
		[
			'uses'				=> 'DraftAktaController@store',
		]
	);
	
	$app->post('/update/draft/akta',
		[
			'uses'				=> 'DraftAktaController@store',
		]
	);

	$app->delete('/hapus/draft/akta',
		[
			'uses'				=> 'DraftAktaController@delete',
		]
	);

	$app->post('/issue/draft/akta',
		[
			'uses'				=> 'DraftAktaController@issue',
		]
	);

	//Sprint II
	$app->get('/lihat/list/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@index',
		]
	);

	$app->get('/lihat/isi/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@show',
		]
	);

	$app->delete('/void/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@void',
		]
	);

	$app->post('/issue/renvoi',
		[
			'uses'				=> 'RenvoiController@issue',
		]
	);

	$app->get('/lihat/list/renvoi',
		[
			'uses'				=> 'RenvoiController@index',
		]
	);

	$app->get('/lihat/isi/renvoi',
		[
			'uses'				=> 'RenvoiController@show',
		]
	);

	$app->post('/simpan/renvoi',
		[
			'uses'				=> 'RenvoiController@store',
		]
	);

	$app->post('/handover/akta',
		[
			'uses'				=> 'AktaController@handover',
		]
	);

	//Sprint III
	$app->get('/lihat/list/akta',
		[
			'uses'				=> 'AktaController@index',
		]
	);
	
	$app->get('/lihat/isi/akta',
		[
			'uses'				=> 'AktaController@show',
		]
	);

	$app->get('/print/akta',
		[
			'uses'				=> 'AktaController@print',
		]
	);
});

