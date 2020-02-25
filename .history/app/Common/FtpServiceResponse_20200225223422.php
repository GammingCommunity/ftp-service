<?php

namespace App\Common;

use \Illuminate\Http\JsonResponse;
use App\Common\FtpServiceResponseStatuses;

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
		return response()->json($this);
	}
}
