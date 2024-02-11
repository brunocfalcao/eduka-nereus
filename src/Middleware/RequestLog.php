<?php

namespace Eduka\Nereus\Middleware;

use Closure;
use Eduka\Cube\Models\EdukaRequestLog;
use Eduka\Nereus\Facades\Nereus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class RequestLog
{
    public function handle(Request $request, Closure $next)
    {
        $route = Route::getRoutes()->match($request);

        EdukaRequestLog::create([
            'referrer' => $request->headers->get('referer'),
            'url' => $request->fullUrl(),
            'payload' => Arr::dot($request->all()),
            'headers' => Arr::dot($request->headers->all()),

            'route' => $route?->getName(),
            'parameters' => Arr::dot($route?->parameters()),
            'middleware' => Arr::dot($route?->middleware()),

            'organization_id' => Nereus::organization()?->id,
            'course_id' => Nereus::course()?->id,
        ]);

        return $next($request);
    }
}
