<?php

namespace App\Helpers;

class FileUploadingResult
{
	/**
	 * @var bool
	 */
	public $result;

	/**
	 * @var string
	 */
	public $describe;

	/**
	 * @param bool $result
	 * @param string $describe
	 */
	public function __construct(bool $result = false, string $describe = '')
	{
		$this->result = $result;
		$this->describe = $describe;
	}

	/**
	 * @return void
	 */
	public function response(): \Illuminate\Http\JsonResponse
	{
		return response()->json($this);
	}
}
