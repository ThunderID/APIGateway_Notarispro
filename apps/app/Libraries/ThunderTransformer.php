<?php

namespace App\Libraries;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use App\Http\Transformers\ListTemplateAktaTransformer;
use App\Http\Transformers\IsiTemplateAktaTransformer;
use App\Http\Transformers\EditableTemplateAktaTransformer;

use App\Http\Transformers\ListAktaTransformer;
use App\Http\Transformers\IsiAktaTransformer;
use App\Http\Transformers\EditableAktaTransformer;

class ThunderTransformer 
{
	/**
	 * fungsi transform template akta
	 * 
	 * Perubahan ini mempengaruhi fungsi : TemplateAktaController@index
	 * @param   array of JSend
	 * @return 	array of JSend
	 * 
	 */
	public function list_template_akta($response) 
	{
		$fractal			= new Manager();
		$resource 			= new Collection($response['data']['data'], new ListTemplateAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array				= $fractal->createData($resource)->toArray();

        $minor['header']	= ['title', 'type', 'writer', 'last_update'];
        $minor['data']		= $array['data'];

		$response['data']['page_data']	= $minor;

		unset($response['data']['data']);

		return $response;
	}

	/**
	 * fungsi transform template akta
	 * 
	 * Perubahan ini mempengaruhi fungsi : TemplateAktaController@show
	 * @param   array of JSend
	 * @return 	array of JSend
	 * 
	 */
	public function isi_template_akta($response) 
	{
		$fractal		= new Manager();
		$resource 		= new Collection($response['data']['data'], new IsiTemplateAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		unset($response['data']['count']);
		unset($response['data']['page_info']);

		$response['data']	= $array['data'][0];

		return $response;
	}

	/**
	 * fungsi transform template akta
	 * 
	 * Perubahan ini mempengaruhi fungsi : TemplateAktaController@create
	 * @param   array of JSend
	 * @return 	array of JSend
	 * 
	 */
	public function edit_template_akta($response) 
	{
		$fractal		= new Manager();
		$resource 		= new Collection($response['data']['data'], new EditableTemplateAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		unset($response['data']['count']);
		unset($response['data']['page_info']);

		$response['data']	= $array['data'][0];

		return $response;
	}

	/**
	 * fungsi transform document akta
	 * 
	 * Perubahan ini mempengaruhi fungsi : DraftAktaController@index
	 * @param   array of JSend
	 * @return 	array of JSend
	 * 
	 */
	public function list_document_akta($response) 
	{
		$fractal			= new Manager();
		$resource 			= new Collection($response['data']['data'], new ListAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array				= $fractal->createData($resource)->toArray();

        $minor['header']	= ['title', 'writer', 'last_update'];
        $minor['data']		= $array['data'];

		$response['data']['page_data']	= $minor;

		unset($response['data']['data']);

		return $response;
	}

	/**
	 * fungsi transform document akta
	 * 
	 * Perubahan ini mempengaruhi fungsi : DraftAktaController@show
	 * @param   array of JSend
	 * @return 	array of JSend
	 * 
	 */
	public function isi_document_akta($response) 
	{
		$fractal		= new Manager();
		$resource 		= new Collection($response['data']['data'], new IsiAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		unset($response['data']['count']);
		unset($response['data']['page_info']);

		$response['data']	= $array['data'][0];

		return $response;
	}

	/**
	 * fungsi transform draft akta
	 * 
	 * Perubahan ini mempengaruhi fungsi : DraftAktaController@create
	 * @param   array of JSend
	 * @return 	array of JSend
	 * 
	 */
	public function edit_draft_akta($response) 
	{
		$fractal		= new Manager();
		$resource 		= new Collection($response['data']['data'], new EditableAktaTransformer);

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array			= $fractal->createData($resource)->toArray();

		unset($response['data']['count']);
		unset($response['data']['page_info']);

		$response['data']	= $array['data'][0];

		return $response;
	}

	/**
	 * fungsi transform draft akta
	 * 
	 * Perubahan ini mempengaruhi fungsi : RenvoiController@edit
	 * @param   array of $akta
	 * @param   array of $lock
	 * @return 	array of UI Contract
	 * 
	 */
	public function edit_draft_akta(array $akta, array $lock) 
	{
		$response		= new EditableLockedAktaTransformer();

		return $response->transform($akta, $lock);
	}
};
