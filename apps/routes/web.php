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
			'middleware'		=> 'organization',
		]
	);

	$app->get('/lihat/isi/template/akta',
		[
			'uses'				=> 'TemplateAktaController@show',
			'middleware'		=> 'organization',
		]
	);

	$app->get('/mulai/template/akta',
		[
			'uses'				=> 'TemplateAktaController@create',
			'middleware'		=> 'organization',
		]
	);

	$app->get('/edit/isi/template/akta',
		[
			'uses'				=> 'TemplateAktaController@create',
			'middleware'		=> 'organization',
		]
	);
	
	$app->post('/simpan/template/akta',
		[
			'uses'				=> 'TemplateAktaController@store',
			'middleware'		=> 'organization',
		]
	);
	
	$app->post('/update/template/akta',
		[
			'uses'				=> 'TemplateAktaController@store',
			'middleware'		=> 'organization',
		]
	);

	$app->delete('/hapus/template/akta',
		[
			'uses'				=> 'TemplateAktaController@delete',
			'middleware'		=> 'organization',
		]
	);
	
	$app->post('/issue/template/akta',
		[
			'uses'				=> 'TemplateAktaController@issue',
			'middleware'		=> 'organization',
		]
	);

	$app->post('/void/template/akta',
		[
			'uses'				=> 'TemplateAktaController@void',
			'middleware'		=> 'organization',
		]
	);

	$app->get('/lihat/list/draft/akta',
		[
			'uses'				=> 'DraftAktaController@index',
			'middleware'		=> 'person',
		]
	);

	$app->get('/lihat/isi/draft/akta',
		[
			'uses'				=> 'DraftAktaController@show',
			'middleware'		=> 'person',
		]
	);

	$app->get('/mulai/draft/akta',
		[
			'uses'				=> 'DraftAktaController@create',
			'middleware'		=> 'person',
		]
	);

	$app->get('/edit/isi/draft/akta',
		[
			'uses'				=> 'DraftAktaController@create',
			'middleware'		=> 'person',
		]
	);

	$app->post('/simpan/draft/akta',
		[
			'uses'				=> 'DraftAktaController@store',
			'middleware'		=> 'person',
		]
	);
	
	$app->post('/update/draft/akta',
		[
			'uses'				=> 'DraftAktaController@store',
			'middleware'		=> 'person',
		]
	);

	$app->delete('/hapus/draft/akta',
		[
			'uses'				=> 'DraftAktaController@delete',
			'middleware'		=> 'person',
		]
	);

	$app->post('/issue/draft/akta',
		[
			'uses'				=> 'DraftAktaController@issue',
			'middleware'		=> 'person',
		]
	);

	//Sprint II
	$app->get('/lihat/list/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@index',
			'middleware'		=> 'notary',
		]
	);

	$app->get('/lihat/isi/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@show',
			'middleware'		=> 'notary',
		]
	);

	$app->delete('/void/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@void',
			'middleware'		=> 'notary',
		]
	);

	$app->post('/issue/proposed/akta',
		[
			'uses'				=> 'ProposedAktaController@issue',
			'middleware'		=> 'notary',
		]
	);

	$app->get('/lihat/list/renvoi',
		[
			'uses'				=> 'RenvoiController@index',
			'middleware'		=> 'person',
		]
	);

	$app->get('/lihat/isi/renvoi',
		[
			'uses'				=> 'RenvoiController@show',
			'middleware'		=> 'person',
		]
	);

	$app->get('/edit/isi/renvoi',
		[
			'uses'				=> 'RenvoiController@edit',
			'middleware'		=> 'person',
		]
	);

	$app->post('/simpan/renvoi',
		[
			'uses'				=> 'RenvoiController@store',
			'middleware'		=> 'person',
		]
	);

	$app->post('/issue/renvoi',
		[
			'uses'				=> 'RenvoiController@issue',
			'middleware'		=> 'person',
		]
	);

	$app->post('/generate/akta',
		[
			'uses'				=> 'ProposedAktaController@generate',
			'middleware'		=> 'notary',
		]
	);

	$app->get('/handover/draft/akta',
		[
			'uses'				=> 'HandOverAktaController@get_handover',
			'middleware'		=> 'person',
		]
	);

	$app->post('/handover/draft/akta',
		[
			'uses'				=> 'HandOverAktaController@post_handover',
			'middleware'		=> 'person',
		]
	);

	$app->get('/assignee/akta',
		[
			'uses'				=> 'HandOverAktaController@get_handover',
			'middleware'		=> 'notary',
		]
	);

	$app->post('/assignee/akta',
		[
			'uses'				=> 'HandOverAktaController@post_handover',
			'middleware'		=> 'notary',
		]
	);

	//Sprint III
	$app->get('/lihat/list/akta',
		[
			'uses'				=> 'AktaController@index',
			'middleware'		=> 'notary',
		]
	);
	
	$app->get('/lihat/isi/akta',
		[
			'uses'				=> 'AktaController@show',
			'middleware'		=> 'notary',
		]
	);

	$app->get('/print/akta',
		[
			'uses'				=> 'AktaController@show',
			'middleware'		=> 'notary',
		]
	);
});

