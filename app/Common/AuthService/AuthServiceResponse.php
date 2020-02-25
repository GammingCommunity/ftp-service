<?php

namespace App\Common\AuthService;

use App\Common\AuthService\AuthServiceResponseStatus;
use App\Common\FtpServiceResponse;

class AuthServiceResponse{
	/**
	 * @var AuthServiceResponseStatus
	 */
	public $status;

	/**
	 * @var mixed
	 */
	public $data;

	/**
	 * @var string
	 */
	public $describe;

	/**
	 * @param string $jsonString
	 */
	public function __construct(string $jsonString)
	{
		$obj = json_decode($jsonString);
		if(property_exists($obj, 'status') && property_exists($obj, 'data') && property_exists($obj, 'describe')){
			$this->status = $obj->status;
			$this->data = $obj->data;
			$this->describe = $obj->describe;
		} else {
			FtpServiceResponse::exit('Failed to decode jsonString to AuthServiceResponse.');
		}
	}
}
