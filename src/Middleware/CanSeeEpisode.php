<?php

namespace Eduka\Nereus\Middleware;

use Closure;
use Eduka\Cube\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanSeeEpisode
{
    public function handle(Request $request, Closure $next)
    {
        $uuid = $request->route('episode');
        $episode = Episode::where('uuid', $uuid)->firstOrFail();

        // Free episode? All good.
        if ($episode->is_free) {
            return $next($request);
        }

        if (Auth::id()) {
            if ($episode->with('course.students')->where('students.id', Auth::id())->exists()) {
                return $next($request);
            }
        }

        // Redirect to buy course.
        return redirect($episode->course->domain.'/buy');

        // Logged in? Course bought?
    }
}
