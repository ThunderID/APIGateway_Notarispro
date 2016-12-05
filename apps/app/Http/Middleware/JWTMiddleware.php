<?php

namespace App\Http\Middleware;

use Closure;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class JWTMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$token  = $request->header('Authorization');

		if(empty($token))
		{
			throw new \Exception('no JWT');
		}

		$tokens = explode(' ', $token);

		$token 	= $tokens[count($tokens) - 1];

		$token	= (new Parser())->parse((string) $token); // Parses from a string

		$data	= new ValidationData(); // It will use the current time to validate (iat, nbf and exp)

		$data->setIssuer(env('JWT_ISSUER','http://example.com'));
		$data->setAudience(env('JWT_AUDIENCE','http://example.org'));
		$data->setId(env('JWT_ID','4f1g23a12aa'));

		$signer 	= new Sha256();
		$keychain 	= new Keychain();

		if($token->verify($signer, $keychain->getPublicKey(file_get_contents('public_rsa.key'))))
		{
			$signer 	= new Sha256();
			$keychain 	= new Keychain();

			$newtoken = (new Builder())->setIssuer('http://example.com') // Configures the issuer (iss claim)
                        ->setAudience('http://example.org') // Configures the audience (aud claim)
                        ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                        ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
                        ->set('pid', $token->getClaim('pid')) // Configures a new claim, called "uid"
                        ->set('oid', $token->getClaim('oid')) // Configures a new claim, called "uid"
                        ->set('pname', $token->getClaim('pname')) // Configures a new claim, called "uid"
                        ->set('oname', $token->getClaim('oname')) // Configures a new claim, called "uid"
                        ->set('role', 'drafter') // Configures a new claim, called "uid"
                        ->sign($signer,  $keychain->getPrivateKey(file_get_contents('private_rsa.key')))
                        ->getToken(); // Retrieves the generated token

            $request->header('Authorization', 'Bearer '.$newtoken);

			return $next($request);
		}

		throw new \Exception('invalid token');

		return $next($request);
	}
}
