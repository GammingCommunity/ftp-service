<?php

namespace App\Common;

use \Illuminate\Http\JsonResponse;

class FtpServiceResponse
{
	/**
	 * @var mixed
	 */
	public $result;

	/**
	 * @var string
	 */
	public $describe;

	/**
	 * @var string
	 */
	public $code;

	/**
	 * @param string $describe
	 * @param mixed $result
	 * @param string $code
	 */
	public function __construct(string $code = FtpServiceResponseStatusCode::SUCCESS, $result = null, string $describe = '')
	{
		$this->result = $result;
		$this->code = $code;
		$this->describe = $describe;
	}

	/**
	 * @return JsonResponse
	 */
	public function response(): JsonResponse
	{
		return response()->json($this);
	}
}
