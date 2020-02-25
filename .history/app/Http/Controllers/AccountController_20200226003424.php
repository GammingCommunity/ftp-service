<?php

namespace App\Http\Controllers;

use App\Common\FtpServiceResponse;
use App\Common\FtpServiceResponseStatus;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;

class AccountController extends Controller
{
	private $accountAvatarDisk = 'account-avatar';
	private $accountAvatarExtension = 'png';

	public function uploadAvatar(Request $req)
	{
		$response = new FtpServiceResponse();
		$extensions = ['png', 'jpg', 'jpeg'];
		$clientFile = $req->file;
		$fileName = $req->jwtPayload->accountId . '.' . $this->accountAvatarExtension;

		if (FileHelper::checkFileExtension($clientFile, $extensions)) {
			$image = new ImageRe
			// $response = FileHelper::change($this->accountAvatarDisk, $clientFile, $fileName);
		} else {
			$response->describe = "File extension ({$clientFile->extension()}) is not supported!";
			$response->code = FtpServiceResponseStatus::FILE_TYPE_NOT_SUPPORTED;
		}

		return $response->response();
	}
}
