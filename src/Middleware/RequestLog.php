<?php

namespace Eduka\Nereus\Middleware;

use Closure;
use Illuminate\Http\Request;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Cube\Models\EdukaRequestLog;

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
            'course_id' => Nereus::course()?->id
        ]);

        return $next($request);
    }
}
