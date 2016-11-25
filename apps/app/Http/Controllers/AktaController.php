<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

use Tymon\JWTAuth\JWTAuth;
/**
 * Akta  resource representation.
 *
 * @Resource("Akta", uri="/akta")
 */
class AktaController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}

	public function index()
	{
		return 'call data from mq';
	}

	public function post()
	{
		return 'business workflow';
	}

	public function delete()
	{
		return 'business workflow';
	}
}