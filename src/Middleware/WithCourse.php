<?php

namespace Eduka\Nereus\Middleware;

use Closure;
use Illuminate\Http\Request;
use Eduka\Nereus\Facades\Nereus;

class WithCourse
{
    public function handle(Request $request, Closure $next)
    {
        if (! Nereus::course()) {
            throw new \Exception('No course contextualized');
        }

        return $next($request);
    }
}
