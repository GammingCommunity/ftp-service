<?php

namespace App\Http\Middleware;

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
				return Account::find((new AuthServiceJwtPayload($token))->accountId);
			} else {
				ErrorResult::exit(json_encode([
					'status' => $authServiceResponse->status,
					'describe' => $authServiceResponse->describe
				], JSON_PRETTY_PRINT));
			}
		} else {
			sleep(2);
			ErrorResult::exit('Missing the token.');
		}

        return $next($request);
    }
}
