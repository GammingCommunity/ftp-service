<?php

namespace App\Common;

use \Illuminate\Http\JsonResponse;
use App\Common\FtpServiceResponseStatus;

class FtpServiceResponse
{
	/**
	 * @var mixed
	 */
	public $data;

	/**
	 * @var string
	 */
	public $describe;

	/**
	 * @var string
	 */
	public $status;

	/**
	 * @param string $describe
	 * @param mixed $data
	 * @param string $status
	 */
	public function __construct(string $status = FtpServiceResponseStatus::SUCCESS, $data = null, string $describe = '')
	{
		$this->data = $data;
		$this->status = $status;
		$this->describe = $describe;
	}

	/**
	 * @return JsonResponse
	 */
	public function response(): JsonResponse
	{
		if (!config('app.debug')) {
			$this->describe = hash('sha256', $this->describe);
		} 
		return response()->json($this);
	}

	ErrorResult::exit('Failed to decode jsonString to AuthServiceResponse.');
	public static function exit(string $text){
		(new FtpServiceResponse())
	}
}
