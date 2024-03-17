<?php

namespace Eduka\Nereus\Middleware;

use Closure;
use Eduka\Cube\Models\RequestLog as RequestLogModel;
use Eduka\Nereus\Facades\Nereus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RequestLog
{
    public function handle(Request $request, Closure $next)
    {
        $route = Route::getRoutes()->match($request);

        RequestLogModel::create([
            'referrer' => $request->headers->get('referer'),
            'url' => $request->fullUrl(),
            'payload' => Arr::dot($request->all()),
            'headers' => Arr::dot($request->headers->all()),

            'student_id' => Auth::id(),

            'route' => $route?->getName(),
            'parameters' => Arr::dot($route?->parameters()),
            'middleware' => Arr::dot($route?->middleware()),

            'backend_id' => Nereus::backend()?->id,
            'course_id' => Nereus::course()?->id,
        ]);

        return $next($request);
    }
}
