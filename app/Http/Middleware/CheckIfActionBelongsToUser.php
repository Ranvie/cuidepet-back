<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfActionBelongsToUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userTokenId = auth()->user()->getAuthIdentifier();
        $userRouteId = $request->route('userId');

        if($userRouteId != $userTokenId){
            throw new BusinessException('O usuário não possui a permissão para isso.', 403);
        }

        return $next($request);
    }
}
