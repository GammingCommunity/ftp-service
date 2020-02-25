<?php

namespace App\Common\AuthService;

final class AuthServiceResponseStatus{
	const SUCCESSFUL = "SUCCESSFUL";
	const FAILED = "FAILED";
	const WRONG_PWD = "WRONG_PWD";
	const WRONG_USERNAME = "WRONG_USERNAME";
	const SESSION_EXPIRED = "SESSION_EXPIRED";
	const IS_BANNED_ACCOUNT = "IS_BANNED_ACCOUNT";
	const IS_UNACTIVATED_ACCOUNT = "IS_UNACTIVATED_ACCOUNT";
}
