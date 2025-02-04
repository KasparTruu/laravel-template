<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuppressDeprecationErrors
{
    public function handle(Request $request, Closure $next)
    {
        error_reporting(E_ALL & ~E_DEPRECATED);
        return $next($request);
    }
}