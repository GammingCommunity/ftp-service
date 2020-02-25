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

		$response = FileHelper::change($diskName, $clientFile, )

		return $response->response();
	}
}
