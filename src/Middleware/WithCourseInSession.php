<?php

namespace Eduka\Nereus\Middleware;

use Closure;
use Eduka\Nereus\Facades\Nereus;
use Illuminate\Http\Request;

class WithCourseInSession
{
    public function handle(Request $request, Closure $next)
    {
        if (! Nereus::course()) {
            throw new \Exception('No course contextualized');
        }

        return $next($request);
    }
}
