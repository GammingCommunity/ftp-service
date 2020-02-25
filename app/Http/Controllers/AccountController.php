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
	private $accountDefaultAvatarName = 'default.png';

	public function uploadAvatar(Request $req)
	{
		$response = new FtpServiceResponse();
		$extensions = ['png', 'jpg', 'jpeg'];
		$clientFile = $req->file;
		$savingFileName = $req->jwtPayload->accountId . $this->accountAvatarExtension;
		$savingFilePath = Storage::disk($this->accountAvatarDisk)->path($savingFileName);

		if (FileHelper::checkExistUploadingFile($clientFile)) {
			if (FileHelper::checkFileExtension($clientFile, $extensions)) {
				try {
					$image = new ImageResize($clientFile->path());
					$image->resize($this->accountAvatarSize, $this->accountAvatarSize);
					$image->save($savingFilePath, IMAGETYPE_PNG);
					$response->status = FtpServiceResponseStatus::SUCCESSFUL;
				} catch (ImageResizeException $ex) {
					$response->describe = $ex->getMessage();
					$response->status = FtpServiceResponseStatus::FAILED;
				}
			} else {
				dd($clientFile->extension());
				$response->describe = "File extension ({$clientFile->extension()}) is not supported!";
				$response->status = FtpServiceResponseStatus::FILE_TYPE_NOT_SUPPORTED;
			}
		} else {
			$response->status = FtpServiceResponseStatus::FILE_NOT_RECEIVED;
		}

		return $response->response();
	}

	public function downloadAvatar(Request $req, $accountId)
	{
		$fileName = $accountId . $this->accountAvatarExtension;
		return FileHelper::download($this->accountAvatarDisk, $fileName, $this->accountDefaultAvatarName);
	}
}
