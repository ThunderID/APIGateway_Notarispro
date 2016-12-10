<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use App\Libraries\ThunderMQCaller;
use App\Libraries\ThunderMQValidator;
use App\Libraries\ThunderTransformer;

use Illuminate\Http\Request;

use App\Http\Policies\AktaFormattor;
use App\Http\Policies\LockFormattor;

/**
 * Draft Akta  resource representation.
 *
 */
class DraftAktaController extends Controller
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
		$search['search']['writerid']	= $this->request->input('writerid');
		$search['search']['ownerid']	= $this->request->input('ownerid');
		$search['search']['ownertype']	= $this->request->input('ownertype');
		$search['search']['type']		= 'draft_akta';

		return $search;
	}

	//Here is list of all deed drafts, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/draft/akta
	public function index($id = null)
	{
		//1. Parse Search Parameter
		$search 	= $this->search();

		//2. Mq Caller
		$akta 		= new ThunderMQCaller;
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

	//Here is show content of a draft deed, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/draft/akta
	public function show()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');

		//2. Mq Caller
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->request->input('ocode').'.document.index');

		//3. Transform Return
		if(!str_is($response['status'], 'success') || count($response['data']['data']) < 0)
		{
			$response 	= JSend::error(['Tidak dapat melihat draft Akta yang bukan milik Anda!'])->asArray();
		}
		elseif(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_document_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is edit content of a draft deed, only can be used by owner of document (personally)
	//Also used by create process. If it's create then dummy data will be sent
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- mulai/draft/akta
	//- edit/isi/draft/akta
	public function create()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');

		//2. Mq Caller
		$akta		= new ThunderMQCaller;
		$response 	= $akta->edit_caller($search, $this->request, $this->request->input('ocode').'.document.index');

		//3. Transform Return
		$transform 	= new ThunderTransformer;
		if(str_is($response['status'], 'success') && count($response['data']['data']) <= 0)
		{
			$response 	= $transform->edit_draft_akta($this->dummy());
		}
		elseif(str_is($response['status'], 'success'))
		{
			$response 	= $transform->edit_draft_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is store content of a draft deed, only can be used by owner of document (personally)
	//Also using to update a content of a draft deed, proposed akta, akta, and so. If it's update then it will check
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- simpan/draft/akta
	//- update/draft/akta
	public function store($status = 'draft_akta')
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator					= new ThunderMQValidator;
		if(!is_null($this->request->input('id')))
		{
			if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.document.index'))
			{
				return response()->json( JSend::error([$validator->error])->asArray());
			}
		}

		//3. Parse Data to Store format based on policy
		$formattor	= new AktaFormattor;
		$body		= $formattor->formatting_whole_content($this->request, $status);

		//4. Mq Caller (Action)
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->request->input('ocode').'.document.store');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_document_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is delete a draft deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- hapus/draft/akta
	public function delete()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator					= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.document.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}

		//3. Parse Data to Delete format
		$body['id']					= $this->request->input('id');

		//4. Mq Caller (Action)
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->delete_caller($body, $this->request, $this->request->input('ocode').'.document.delete');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_document_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is issue a draft deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- issue/draft/akta
	public function issue($status = 'proposed_akta')
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator					= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.document.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		//3a. Parse Akta
		$formattor 		= new AktaFormattor;
		$body 			= $formattor->formatting_status_owner_organization($validator->data, $status, $this->request);

		//3b. Parse Lock
		$formattor_lock = new LockFormattor;
		$body_lock 		= $formattor_lock->formatting_whole_content($validator->data, $status);

		//4. Mq Caller (Action)
		//4a. Simpan Akta
		$mqcaller 		= new ThunderMQCaller;
		$response 		= $mqcaller->store_caller($body, $this->request, $this->request->input('ocode').'.document.store');

		//4b. Lock Akta (use response from 4a)
		$response_lock 	= $mqcaller->store_caller($response['data']['data'], $this->request, $this->request->input('ocode').'.lock.store');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_document_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is dataset of document structure
	//Used in : DraftAktaController@edit
	public function dummy()
	{
		return [['_id' => '123456789', 'title' => 'Akta Jual Beli Tanah', 'type' => 'draft_akta', 'writer' => ['_id' => '123456789', 'name' => 'Ada Lovelace'], 'owner' => ['_id' => '123456789', 'name' => 'Thunderlab Indonesia'], 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'paragraph' => [['content' => 'Isi Akta']]]];
	}
}