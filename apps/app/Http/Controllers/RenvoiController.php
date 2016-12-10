<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Builder;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use App\Http\Mq\AktaCaller;
use App\Http\Mq\LockCaller;

use App\Http\Policies\AktaValidator;
use App\Http\Policies\AktaFormattor;
use App\Http\Policies\LockFormattor;

use App\Http\Transformers\ListAktaTransformer;
use App\Http\Transformers\IsiAktaTransformer;
use App\Http\Transformers\IsiRenvoiEditableTransformer;

/**
 * Renvoi Akta  resource representation.
 *
 */
class RenvoiController extends Controller
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
		$search['search']['type']		= 'renvoi_akta';

		return $search;
	}

	//Here is list of all deed drafts, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/renvoi
	public function index($id = null)
	{
		//1. Parse Search Parameter
		$search 	= $this->search();

		//2. Mq Caller
		$akta 		= new AktaCaller;
		$response 	= $akta->index_caller($search, $this->request, $this->get_new_token($this->token));

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

	//Here is show content of a renvoi, only can be seen by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- lihat/isi/renvoi
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

	//Here is edit content of a renvoi, only can be used by owner of document (personally)
	//Also used by create process. If it's create then dummy data will be sent
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- edit/isi/renvoi
	public function edit()
	{
		//1. Parse Search Parameter
		//1a. Akta Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');
		//1b. Lock Parameter
		$s_lock['search']['type']		= 'renvoi_akta';
		$s_lock['search']['pandoraid']	= $this->request->input('id');

		//2. Validating existance
		//2a. renvoi existance
		$validator					= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.document.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}

		//2b. lock existance
		$v_lock					= new ThunderMQValidator;
		if(!$v_lock->is_exists($s_lock, $this->request, $this->request->input('ocode').'.lock.index'))
		{
			return response()->json( JSend::error([$v_lock->error])->asArray());
		}

		//3. Transform Return
		$transform 	= new ThunderTransformer;
		$response 	= JSend::success($transform->edit_renvoi_akta($validator->data, $v_lock->data))->asArray();
		$response 	= json_encode($response);

		return $response;
	}

	//Here is store content of a renvoi, only can be used by owner of document (personally)
	//Also using to update a content of a renvoi, proposed akta, akta, and so. If it's update then it will check
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- simpan/renvoi
	public function store($status = 'renvoi_akta')
	{
		//1. Parse Search Parameter
		//1a. Akta Parameter
		$search 					= $this->search();
		$search['search']['id']		= $this->request->input('id');
		//1b. Lock Parameter
		$s_lock['search']['type']		= 'proposed_akta';
		$s_lock['search']['pandoraid']	= $this->request->input('id');

		//2. Validating This Process on Business Rule
		//2a. Akta existence
		$validator					= new ThunderMQValidator;
		if(!$validator->is_exists($search, $this->request, $this->request->input('ocode').'.document.index'))
		{
			return response()->json( JSend::error([$validator->error])->asArray());
		}
		//2b. Lock existence
		$v_lock					= new ThunderMQValidator;
		if(!$v_lock->is_exists($s_lock, $this->request, $this->request->input('ocode').'.lock.index'))
		{
			return response()->json( JSend::error([$v_lock->error])->asArray());
		}
		//2c. Check modifying paragraph
		$v_notaris				= new NotarisProValidator;
		if(!$v_notaris->is_locked_paragraph($validator->data, $v_lock->data))
		{
			return response()->json( JSend::error([$v_lock->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		//3a. Parse Akta
		$formattor 		= new AktaFormattor;
		$body 			= $formattor->formatting_certain_paragraph($validator->data, $status, $this->request);

		//4. Mq Caller (Action)
		//4a. Simpan Akta
		$mqcaller 		= new ThunderMQCaller;
		$response 		= $mqcaller->store_caller($body, $this->request, $this->request->input('ocode').'.document.store');

		//5. Transforming Data
		if(str_is($response['status'], 'success'))
		{
			$transform 	= new ThunderTransformer;
			$response 	= $transform->isi_document_akta($response);
		}

		$response 	= json_encode($response);

		return $response;
	}

	//Here is issue a renvoi, only can be used by owner of document (personally)
	//Allowing Role : Drafter, Notary
	//Affected Route :
	//- issue/renvoi
	public function issue($status = 'proposed_akta', $prev_status = 'renvoi_akta')
	{
		//1. Middleware Parse Parameter
		if(str_is($this->role, 'drafter') || str_is($this->role, 'notary'))
		{
			$search['search']['type']		= $prev_status;
			$search['search']['writerid']	= $this->writerid;
			$search['search']['ownerid']	= $this->ownerid;
			$search['search']['ownertype']	= 'organization';
			$search['search']['id']			= $this->request->input('id');
			$this->validator 				= new AktaValidator;
		}
		else
		{
			throw new \Exception('invalid role');
		}

		//2. Validating This Process on Business Rule
		//2a. Check Existance
		if(!$this->validator->is_okay_to_drafting($search, $this->request, $this->get_new_token($this->token)))
		{
			return response()->json( JSend::error([$this->validator->error])->asArray());
		}

		//3. Parse Data to Store format based on policy
		//3a. Parse Akta
		$this->formattor 	= new AktaFormattor;
		$body 				= $this->formattor->parse_to_akta_structure($this->validator->data, $this->token, $status);

		//3b. Parse Lock
		$this->formattor_lock 	= new LockFormattor;
		$body_lock 				= $this->formattor_lock->parse_to_lock_structure($this->validator->data, $this->token, $status);

		//4. Mq Caller (Action)
		//4a. Simpan Akta
		$akta 		= new AktaCaller;
		$response 	= $akta->store_caller($body, $this->request, $this->get_new_token($this->token));

		if(str_is($response['status'], 'success'))
		{
			//4b. Lock Akta
			$lock 			= new LockCaller;

			$response_lock = $lock->store_caller($body_lock, $this->request, $this->get_new_token($this->token));

			//5. Transforming Data
			$fractal		= new Manager();
			$resource 		= new Collection([$response['data']], new IsiAktaTransformer);

			// Turn that into a structured array (handy for XML views or auto-YAML converting)
			$array			= $fractal->createData($resource)->toArray();

			$response['data']['data']	= $array['data'][0];
		}

		$response 	= json_encode($response);

		return $response;
	}
}

