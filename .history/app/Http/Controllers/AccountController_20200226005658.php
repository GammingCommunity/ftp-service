<?php

namespace App\Http\Controllers;

use App\Common\FtpServiceResponse;
use App\Common\FtpServiceResponseStatus;
use App\Helpers\FileHelper;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
	private $accountAvatarDisk = 'account-avatar';
	private $accountAvatarExtension = '.png';
	private $accountAvatarSize = 200;
	private $accountDefaultAvatarName = 'default' . $this->accountAvatarExtension;

	public function uploadAvatar(Request $req)
	{
		$response = new FtpServiceResponse();
		$extensions = ['png', 'jpg', 'jpeg'];
		$clientFile = $req->file;
		$savingFileName = $req->jwtPayload->accountId . $this->accountAvatarExtension;
		$savingFilePath = Storage::disk($this->accountAvatarDisk)->path($savingFileName);

		if (FileHelper::checkFileExtension($clientFile, $extensions)) {
			try {
				$image = new ImageResize($clientFile->path);
				$image->resize($this->accountAvatarSize, $this->accountAvatarSize);
				$image->save($savingFilePath, IMAGETYPE_PNG);
			} catch (ImageResizeException $ex) {
				$response->describe = $ex->getMessage();
				$response->code = FtpServiceResponseStatus::FAILED;
			}
		} else {
			$response->describe = "File extension ({$clientFile->extension()}) is not supported!";
			$response->code = FtpServiceResponseStatus::FILE_TYPE_NOT_SUPPORTED;
		}

		return $response->response();
	}

	public function downloadAvatar(Request $req, $accountId){
		$fileName = $accountId . $this->accountAvatarExtension;
		return FileHelper::download($this->accountAvatarDisk, $fileName, $this->accountDefaultAvatarName);
	}
}
