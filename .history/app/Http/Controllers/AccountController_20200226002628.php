<?php

namespace App\Http\Controllers;

use App\Common\FtpServiceResponse;
use App\Common\FtpServiceResponseStatus;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function uploadAvatar(Request $req){
		$response = new FtpServiceResponse();
		$diskName = 'account-avatar';
		$extensions = ['png', 'jpg', 'jpeg'];
		$clientFile = $req->file;
		$accountId = $req->jwtPayload->accountId;

		if(FileHelper::checkFileExtension($clientFile, $extensions)){
			$response = FileHelper::change($diskName, $clientFile, )

		} else {
			$response->describe = "File extension ({$clientFile->extension()}) is not supported!";
			$response->code = FtpServiceResponseStatus::FILE_TYPE_NOT_SUPPORTED;

		}

		return $response->response();
	}
}
