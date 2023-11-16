<?php

namespace App\Http\Middleware;

use App\Service\AuthService;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsClient
{

    /**
     * @param $request
     * @param Closure $next
     * @param mixed $role
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $role = false)
    {
        $client = Auth::user();
        if (AuthService::isClient($client, $role)) {
            return $next($request);
        }

        return response()->json(['message' => 'unauthenticated'], 401);
    }
}
