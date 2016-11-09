<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Entities\ComponentRule;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

/**
 * ComponentRule  resource representation.
 *
 * @Resource("ComponentRule", uri="/companies")
 */
class ComponentRuleController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}

	/**
	 * Show all companies
	 *
	 * @Get("/")
	 * @Versions({"v1"})
	 * @Transaction({
	 *      @Request({"search":{"_id":"string","name":"string","code":"string"},"sort":{"newest":"asc|desc"}, "take":"integer", "skip":"integer"}),
	 *      @Response(200, body={"status": "success", "data": {"data":{"_id":{"value":"1234567890", "type":"string", "max":"255"},"client_identifier":{"value":"123456789","type":"string","max":"255"},"component":{"value":"referre.point", "type":"string", "max":"255"},"rule":{"value":"50000", "type":"string", "max":"255"}, "description":{"value":"Bonus yang diterima user ketika memasukkan referral", "type":"text"}, "created_at":{"value":"2016-11-08 00:00:00","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}, "updated_at":{"value":"2016-11-08 00:00:00","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}, "deleted_at":{"value":"null","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}},"count":"integer"} })
	 * })
	 */
	public function index()
	{
		$result						= new ComponentRule;

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case '_id':
						$result		= $result->id($value);
						break;
					case 'client':
						$result		= $result->client($value);
						break;
					case 'component':
						$result		= $result->component($value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('sort'))
		{
			$sort					= Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
				}
				switch (strtolower($key)) 
				{
					case 'newest':
						$result		= $result->orderby('created_at', $value);
						break;
					default:
						# code...
						break;
				}
			}
		}
		else
		{
			$result		= $result->orderby('created_at', 'asc');
		}

		$count						= count($result->get());

		if(Input::has('skip'))
		{
			$skip					= Input::get('skip');
			$result					= $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take					= Input::get('take');
			$result					= $result->take($take);
		}

		$result 					= $this->getStructure($result->get()->toArray());

		return response()->json( JSend::success([array_merge($result, ['count' => $count])])->asArray())
				->setCallback($this->request->input('callback'));
	}

	/**
	 * Store ComponentRule
	 *
	 * @Post("/")
	 * @Versions({"v1"})
	 * @Transaction({
	 *      @Request({"_id":"string","name":"string","code":"string"}),
	 *      @Response(200, body={"status": "success", "data": {"_id":{"value":"1234567890", "type":"string", "max":"255"},"client_identifier":{"value":"123456789","type":"string","max":"255"},"component":{"value":"referre.point", "type":"string", "max":"255"},"rule":{"value":"50000", "type":"string", "max":"255"}, "description":{"value":"Bonus yang diterima user ketika memasukkan referral", "type":"text"}, "created_at":{"value":"2016-11-08 00:00:00","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}, "updated_at":{"value":"2016-11-08 00:00:00","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}, "deleted_at":{"value":"null","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}}}),
	 *      @Response(200, body={"status": {"error": {"code must be unique."}}})
	 * })
	 */
	public function post()
	{
		$id 			= Input::get('_id');

		if(!is_null($id) && !empty($id))
		{
			$result		= ComponentRule::id($id)->first();
		}
		else
		{
			$result 	= new ComponentRule;
		}
		

		$result->fill(Input::only('client_identifier', 'component', 'rule', 'description'));

		if($result->save())
		{
			return response()->json( JSend::success($this->getStructure([$result->toArray()])['data'][0])->asArray())
					->setCallback($this->request->input('callback'));
		}
		
		return response()->json( JSend::error($result->getError())->asArray());
	}

	/**
	 * Delete ComponentRule
	 *
	 * @Delete("/")
	 * @Versions({"v1"})
	 * @Transaction({
	 *      @Request({"_id":null}),
	 *      @Response(200, body={"status": "success", "data": {"_id":{"value":"1234567890", "type":"string", "max":"255"},"client_identifier":{"value":"123456789","type":"string","max":"255"},"component":{"value":"referre.point", "type":"string", "max":"255"},"rule":{"value":"50000", "type":"string", "max":"255"}, "description":{"value":"Bonus yang diterima user ketika memasukkan referral", "type":"text"}, "created_at":{"value":"2016-11-08 00:00:00","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}, "updated_at":{"value":"2016-11-08 00:00:00","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}, "deleted_at":{"value":"null","type":"datetime","zone":"Asia/Jakarta","format":"Y-m-d H:i:s"}}}),
	 *      @Response(200, body={"status": {"error": {"code must be unique."}}})
	 * })
	 */
	public function delete()
	{
		$rule 				= ComponentRule::id(Input::get('_id'))->first();
		
		$result 			= $rule;

		if($rule && $rule->delete())
		{
			return response()->json( JSend::success($this->getStructure([$result->toArray()])['data'][0])->asArray())
					->setCallback($this->request->input('callback'));
		}

		if(!$rule)
		{
			return response()->json( JSend::error(['ID tidak valid'])->asArray());
		}

		return response()->json( JSend::error($rule->getError())->asArray());
	}

	/**
	 * Fractal Modifying Returned Value
	 *
	 * getStructure method used to transforming response format and included UI inside (@UInside)
	 */
	public function getStructure($draft)
	{
		$fractal 					= new Manager();
		$resource 					= new Collection($draft, function(array $rule) {
										return [
												'id' 		=> [
																	'value' => $rule['_id'],
																	'type'	=> 'string',
																	'max'	=> '255',
																],
												'client_identifier' => [
																	'value' => $rule['client_identifier'],
																	'type'	=> 'string',
																	'max'	=> '255',
																],
												'component' => [
																	'value' => $rule['component'],
																	'type'	=> 'string',
																	'max'	=> '255',
																],
												'rule' 		=> [
																	'value' => $rule['rule'],
																	'type'	=> 'string',
																	'max'	=> '255',
																],
												'description' 	=> [
																	'value' => $rule['description'],
																	'type'	=> 'text',
																],
												'created_at'=> [
																	'value' => $rule['created_at'],
																	'type'	=> 'datetime',
																	'zone'	=> env('APP_TIMEZONE', ''),
																	'format'=> 'Y-m-d H:i:s',
																],
												'updated_at' => [
																	'value' => $rule['updated_at'],
																	'type'	=> 'datetime',
																	'zone'	=> env('APP_TIMEZONE', ''),
																	'format'=> 'Y-m-d H:i:s',
																],
												'deleted_at' => [
																	'value' => null,
																	'type'	=> 'datetime',
																	'zone'	=> env('APP_TIMEZONE', ''),
																	'format'=> 'Y-m-d H:i:s',
																],
											];
										});

		// Turn that into a structured array (handy for XML views or auto-YAML converting)
		$array 						= $fractal->createData($resource)->toArray();

		return $array;
	}
}