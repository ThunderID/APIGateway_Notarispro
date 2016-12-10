<?php

namespace App\Libraries;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class ThunderToken 
{
	/**
	 * fungsi generate jwt token dengan informasi token sebelumnya.
	 * 
	 * Perubahan ini mempengaruhi fungsi middleware route : organization, person, notary, drafter
	 * @param  	\Lcobucci\JWT\Token $prev_token
	 * @return 	Bearer token
	 * 
	 */

	static function get_services_token(Token $prev_token) 
	{
		$signer 	= new Sha256();
		$keychain 	= new Keychain();

		$newtoken = (new Builder())->setIssuer('http://thunderlab.id') // Configures the issuer (iss claim)
					->setAudience('http://thunderlab.id') // Configures the audience (aud claim)
					->setId('thunderlabIchiGogoGo!', true) // Configures the id (jti claim), replicating as a header item
					->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
					->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
					->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
					->set('pid', $prev_token->getClaim('pid')) // Configures a new claim, called "pid"
					->set('oid', $prev_token->getClaim('oid')) // Configures a new claim, called "oid"
					->set('pname', $prev_token->getClaim('pname')) // Configures a new claim, called "pname"
					->set('oname', $prev_token->getClaim('oname')) // Configures a new claim, called "oname"
					->set('role', $prev_token->getClaim('role')) // Configures a new claim, called "role"
					->sign($signer,  $keychain->getPrivateKey(file_get_contents('private_rsa.key')))
					->getToken(); // Retrieves the generated token

		return 'Bearer '.(string)$newtoken;
	}
};
