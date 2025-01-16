<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserDoNotHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$params): Response
    {
        foreach ($params as $param) {
            if (in_array($param, auth()->user()->currentAccessToken()->abilities)) {
                throw new BusinessException('Token inválido ou sem as permissões necessárias', 401);
            }
        }

        return $next($request);
    }
}
