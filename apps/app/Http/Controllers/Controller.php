<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class Controller extends BaseController
{
    //
    function get_new_token($token)
    {
    	$signer 	= new Sha256();
		$keychain 	= new Keychain();

		$newtoken = (new Builder())->setIssuer('http://thunderlab.id') // Configures the issuer (iss claim)
					->setAudience('http://thunderlab.id') // Configures the audience (aud claim)
					->setId('thunderlabIchiGogoGo!', true) // Configures the id (jti claim), replicating as a header item
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

		return 'Bearer '.(string)$newtoken;
    }
}
