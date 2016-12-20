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
class HandOverAktaController extends Controller
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
		// $search['search']['writerid']	= $this->request->input('writerid');
		$search['search']['ownerid']	= $this->request->input('ownerid');
		$search['search']['ownertype']	= $this->request->input('ownertype');
		$search['search']['type']		= ['proposed_akta', 'renvoi_akta', 'draft_akta'];

		return $search;
	}

	//Here is list of all deed drafts, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- handover/draft/akta
	//- assignee/akta
	public function get_handover()
	{
		//1. Parse Search Parameter
		//1a. Akta Parameter
		$search 				= $this->search();
		$search['search']['id']	= $this->request->input('id');

		//1b. Writer Parameter
		$search_w['search']['organizationid']	= $this->request->input('ownerid');
		$search_w['search']['role']				= ['notary', 'drafter'];

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator	= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.document.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}

		//2b. get lists of writer 
		$writer 	= new ThunderServiceCaller;
		$response 	= $writer->index_caller($search_w, $this->request, $this->request->input('ocode').'.user.index');

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->edit_handover_akta($validator->data, $response['data']['data']);
		}

		return $response;
	}
	
	//Here is void a proposed deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- handover/draft/akta
	//- assignee/akta
	public function post_handover()
	{
		//1. Parse Search Parameter
		$search 				= $this->search();
		$search['search']['id']	= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator	= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.document.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		$formattor 		= new AktaFormattor;
		$body 			= $formattor->formatting_writer($validator->data, $this->request);
		
		//4. Mq Caller (Action)
		$mqcaller 		= new ThunderServiceCaller;
		$response 		= $mqcaller->store_caller($body, $this->request, $this->request->input('ocode').'.document.store');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_document_akta($response);
		}

		$response 		= json_encode($response);

		return $response;
	}
}