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
	 * @var mixed
	 */
	public $describe;

	/**
	 * @var string
	 */
	public $status;

	/**
	 * @param string $status
	 * @param mixed $data
	 * @param mixed $describe
	 */
	public function __construct(string $status = FtpServiceResponseStatus::FAILED, $data = null, $describe = null)
	{
		$this->data = $data;
		$this->status = $status;
		$content = $describe;
	}

	/**
	 * @return JsonResponse
	 */
	public function response(): JsonResponse
	{
		if (!config('app.debug')) {
			$this->describe = gettype($this->describe) === 'string' ? hash('sha256', $this->describe) : null;
		}
		return response()->json($this);
	}

	/**
	 * @param mixed $content
	 * @return void
	 */
	public static function exit($content)
	{
		header('Content-Type: application/json');
		if (!config('app.debug')) {
			$content = gettype($content) === 'string' ? hash('sha256', $content) : null;
		}
		exit(json_encode(new FtpServiceResponse(FtpServiceResponseStatus::FAILED, null, $content)));
	}
}
