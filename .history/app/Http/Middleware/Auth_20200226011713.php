<?php

namespace App\Http\Middleware;

use App\Common\AuthService\AuthServiceConnection;
use App\Common\AuthService\AuthServiceJwtPayload;
use App\Common\AuthService\AuthServiceResponseStatus;
use App\Common\FtpServiceResponse;
use Closure;
use Illuminate\Http\Request;

class Auth
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$token = $request->header('token');
		if ($token) {
			$authServiceResponse = AuthServiceConnection::request('GET', '/auth', [
				'headers' => [
					'token' => $token,
					'secret_key' => env('SECRET_KEY')
				]
			]);
			if ($authServiceResponse->status === AuthServiceResponseStatus::SUCCESSFUL) {
				$request->jwtPayload = new AuthServiceJwtPayload($token);
				return $next($request);
			} else {
				FtpServiceResponse::exit('[
					'status' => $authServiceResponse->status,
					'describe' => $authServiceResponse->describe
				]');
			}
		} else {
			sleep(2);
			FtpServiceResponse::exit('Missing the token.');
		}
	}
}
