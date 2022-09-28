<?php

namespace App\Http\Middleware;

use App\Traits\Responses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoSanctumGuard
{
    use Responses;

    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->bearerToken();
        if ($bearer === (string) env("NO_SANCTUM_API_GUARD")) {
            return $next($request);
        }

        return $this->unauthorized_response([]);
    }
}
