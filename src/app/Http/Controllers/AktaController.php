<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use App\Libraries\ThunderServiceCaller;
use App\Libraries\ThunderMQValidator;
use App\Libraries\ThunderTransformer;

use Illuminate\Http\Request;

use App\Http\Policies\AktaFormattor;
use App\Http\Policies\LockFormattor;

/**
 * Akta  resource representation.
 *
 * @Resource("Akta", uri="/akta")
 */
class AktaController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 		= $request;
	}

	/**
	 * Parse Search Helper.
	 * Changes affect all methods here
	 * 
	 * @return array of search
	 */
	private function search()
	{
		$search['search']['ownerid']	= $this->request->input('ownerid');
		$search['search']['ownertype']	= $this->request->input('ownertype');
		$search['search']['type']		= 'akta';

		return $search;
	}

	//Here is list of all deeds, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/akta
	public function index()
	{
		//1. Parse Search Parameter
		$search 	= $this->search();

		//2. Mq Caller
		$akta 		= new ThunderServiceCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->request->input('ocode').'.document.index');

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->list_document_akta($response);
		}

		$response 		= json_encode($response);

		return $response;
	}

	//Here is show content of a deed, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/akta
	//- print/akta
	public function show()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');

		//2. Mq Caller
		$akta 		= new ThunderServiceCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->request->input('ocode').'.document.index');

		//3. Transform Return
		if(!str_is($response['status'], 'success') || count($response['data']['data']) < 0)
		{
			$response 	= JSend::error(['Tidak dapat melihat Akta yang bukan milik Anda!'])->asArray();
		}
		elseif(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_document_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}
}