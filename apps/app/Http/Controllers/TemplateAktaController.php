<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use App\Libraries\ThunderMQCaller;
use App\Libraries\ThunderMQValidator;
use App\Libraries\ThunderTransformer;

use Illuminate\Http\Request;

use App\Http\Policies\TemplateFormattor;

class TemplateAktaController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 		= $request;
		$this->all_type 	= ['akta', 'draft_akta', 'void_akta'];
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

		return $search;
	}

	//Here is list of all template, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/list/template/akta
	public function index()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['type']	= $this->all_type;

		//2. Mq Caller
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->request->input('ocode').'.template.index');

		//3. Transform Return
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->list_template_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is show content of a template deed, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/template/akta
	public function show()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['type']	= $this->all_type;
		$search['search']['id']		= $this->request->input('id');

		//2. Mq Caller
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->request->input('ocode').'.template.index');

		//3. Transform Return
		if(!str_is($response['status'], 'success') || count($response['data']['data']) < 0)
		{
			$response 	= JSend::error(['Tidak dapat melihat template Akta yang bukan milik Anda!'])->asArray();
		}
		elseif(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_template_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is edit content of a deed template, only can be used by owner of document (personally)
	//Also used by create process. If it's create then dummy data will be sent
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- mulai/template/akta
	//- edit/isi/template/akta
	public function create()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['type']	= $this->all_type;
		$search['search']['id']		= $this->request->input('id');

		//2. Mq Caller
		$akta		= new ThunderMQCaller;
		$response 	= $akta->edit_caller($search, $this->request, $this->request->input('ocode').'.template.index');

		//3. Transform Return
		$transform 	= new ThunderTransformer;
		if(str_is($response['status'], 'success') && count($response['data']['data']) <= 0)
		{
			$response 	= $transform->edit_template_akta($this->dummy());
		}
		elseif(str_is($response['status'], 'success'))
		{
			$response 	= $transform->edit_template_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is store content of a template deed, only can be used by owner of document (personally)
	//Also using to update a content of a template deed, void akta, akta, and so. If it's update then it will check
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- simpan/template/akta
	//- update/template/akta
	public function store($status = 'draft_akta', $prev_status = 'draft_akta')
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['type']	= $prev_status;
		$search['search']['id']		= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator					= new ThunderMQValidator;
		if(!is_null($this->request->input('id')))
		{
			if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.template.index'))
			{
				return response()->json( JSend::error([$validator->error])->asArray());
			}
		}

		//3. Parse Data to Store format based on policy
		$formattor	= new TemplateFormattor;
		$body		= $formattor->formatting_whole_content($this->request, $status);

		//4. Mq Caller (Action)
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->request->input('ocode').'.template.store');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_template_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is delete a template deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- hapus/template/akta
	public function delete()
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['type']	= 'draft_akta';
		$search['search']['id']		= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator					= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.template.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}

		//3. Parse Data to Delete format
		$body['id']					= $this->request->input('id');

		//4. Mq Caller (Action)
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->delete_caller($body, $this->request, $this->request->input('ocode').'.template.delete');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_template_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is issue a template deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- issue/template/akta
	//- void/template/akta
	public function issue($status = 'akta', $prev_status = 'draft_akta')
	{
		//1. Parse Search Parameter
		$search 					= $this->search();
		$search['search']['type']	= $prev_status;
		$search['search']['id']		= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. If updating, mq existence
		$validator					= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.template.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		$formattor	= new TemplateFormattor;
		$body		= $formattor->formatting_status($validator->data, $status);

		//4. Mq Caller (Action)
		$akta 		= new ThunderMQCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->request->input('ocode').'.template.store');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_template_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is void a template deed, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Use Same Procedure as issue 
	public function void()
	{
		return $this->issue('void_akta', 'akta');
	}

	//Here is dataset of document structure
	//Used in : TemplateAktaController@edit
	public function dummy()
	{
		return [['_id' => '123456789', 'title' => 'Akta Jual Beli Tanah', 'writer' => ['_id' => '123456789', 'name' => 'Ada Lovelace'], 'owner' => ['_id' => '123456789', 'name' => 'Thunderlab Indonesia'], 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'paragraph' => [['content' => 'Isi Akta']]]];
	}
}

