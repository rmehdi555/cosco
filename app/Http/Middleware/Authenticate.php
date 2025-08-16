<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Closure;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'شما وارد نشده‌اید. لطفاً ابتدا وارد شوید.',
                    'status' => 401,
                    'errors' => null
                ], 401);
            }
            throw $e;
        }

        return $next($request);
    }
}
