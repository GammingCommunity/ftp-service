<?php

namespace App\Http\Controllers;

use App\Common\FtpServiceResponse;
use App\Common\FtpServiceResponseStatus;
use App\Helpers\FileHelper;
use Gumlet\ImageResize;
use Illuminate\Http\Request;

class AccountController extends Controller
{
	private $accountAvatarDisk = 'account-avatar';
	private $accountAvatarExtension = '.png';
	private $accountAvatarSize = 200;

	public function uploadAvatar(Request $req)
	{
		$response = new FtpServiceResponse();
		$extensions = ['png', 'jpg', 'jpeg'];
		$clientFile = $req->file;
		$savingFileName = $req->jwtPayload->accountId . $this->accountAvatarExtension;
		$savingFilePath = $savingFileName;

		if (FileHelper::checkFileExtension($clientFile, $extensions)) {
			$image = new ImageResize($clientFile->path);
			$image->resize($this->accountAvatarSize, $this->accountAvatarSize);
			$image->save($savingFilePath, IMAGETYPE_PNG);
			// $response = FileHelper::change($this->accountAvatarDisk, $clientFile, $fileName);
		} else {
			$response->describe = "File extension ({$clientFile->extension()}) is not supported!";
			$response->code = FtpServiceResponseStatus::FILE_TYPE_NOT_SUPPORTED;
		}

		return $response->response();
	}
}
