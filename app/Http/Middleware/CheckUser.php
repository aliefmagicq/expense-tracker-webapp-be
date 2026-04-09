<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        // return response()->json([
        //     'success' => $user
        // ], 200);

        if (! $user) {
            return response()->json([
                'message' => 'unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
