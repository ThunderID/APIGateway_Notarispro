<?php

namespace App\Http\Middleware;

use Closure;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

use App\Libraries\ThunderToken;

class JWTNotaryOrganizationMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return request or exception
	 */
	public function handle($request, Closure $next)
	{
		$token  = $request->header('Authorization');

		if(empty($token))
		{
			throw new \Exception('no JWT');
		}

		$tokens 	= explode(' ', $token);

		$token 		= $tokens[count($tokens) - 1];

		$token		= (new Parser())->parse((string) $token); // Parses from a string

		$signer 	= new Sha256();
		$keychain 	= new Keychain();

		$new_token 	= ThunderToken::get_services_token($token);

		if($token->verify($signer, $keychain->getPublicKey(file_get_contents('public_rsa.key'))) && 
			str_is($token->getClaim('role'), 'notary'))
		{
			$request->merge(['role' 		=> 	$token->getClaim('role')]);
			$request->merge(['writerid'	 	=> 	$token->getClaim('pid')]);
			$request->merge(['writername' 	=> 	$token->getClaim('pname')]);
			$request->merge(['ownerid' 		=> 	$token->getClaim('oid')]);
			$request->merge(['ownername' 	=> 	$token->getClaim('oname')]);
			$request->merge(['ownertype'	=> 'person']);
			$request->merge(['ocode'		=> 'notarispro']);
			$request->merge(['thundertoken' => $new_token]);

			return $next($request);
		}

		throw new \Exception('invalid token');

		return $next($request);
	}
}
