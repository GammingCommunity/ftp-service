<?php

namespace App\Common\AuthService;

use App\Common\FtpServiceResponse;

class AuthServiceJwtPayload
{
	/**
	 * @var int
	 */
	public $accountId;

	/**
	 * @var int 
	 */
	public $accountRole;

	/**
	 * @var int 
	 */
	public $accountStatus;

	/**
	 * @var string
	 */
	public $session;

	/**
	 * @param string $token
	 */
	public function __construct(string $token)
	{
		if (strpos($token, '.') === false) {
			FtpServiceResponse::exit('Token format error.');
		}
		
		$payloadBase64Url = explode('.', $token)[1];
		$payloadBase64 = str_replace('-', '+', str_replace('_', '/', $payloadBase64Url));
		$obj = json_decode(base64_decode($payloadBase64)); //decode jwt payload

		if (property_exists($obj, 'id') && property_exists($obj, 'rl') && property_exists($obj, 'ss') && property_exists($obj, 'st')) {
			$this->accountId = $obj->id;
			$this->accountRole = $obj->rl;
			$this->accountStatus = $obj->st;
			$this->session = $obj->ss;
		} else {
			FtpServiceResponse::exit('JWT payload format error.');
		}
	}
}
