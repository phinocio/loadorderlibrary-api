<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\AuthenticatedException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class DenyAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            throw new AuthenticatedException('You cannot access this route while logged in.');
        }

        return $next($request);
    }
}
