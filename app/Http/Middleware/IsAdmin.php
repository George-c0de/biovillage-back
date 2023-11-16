<?php

namespace App\Http\Middleware;

use App\Service\AuthService;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param array $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $client = Auth::user();
        if(AuthService::isAdmin($client, $roles)){
            return $next($request);
        }

        return response()->json(['message' => 'unauthenticated'], 401);
    }
}
