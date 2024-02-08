<?php

namespace Eduka\Nereus\Middleware;

use Closure;
use Eduka\Cube\Models\EdukaRequestLog;
use Eduka\Nereus\Facades\Nereus;
use Illuminate\Http\Request;

class RequestLog
{
    public function handle(Request $request, Closure $next)
    {
        EdukaRequestLog::create([
            'referrer' => $request->headers->get('referer'),
            'url' => $request->fullUrl(),
            'request_payload' => $request->all(),
            'request_headers' => $request->headers->all(),
            'organization_id' => Nereus::organization()?->id,
            'course_id' => Nereus::course()?->id,
        ]);

        return $next($request);
    }
}
