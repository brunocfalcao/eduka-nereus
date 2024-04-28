<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Nereus\Rules\SubscriberExists;

class Prelaunched extends Controller
{
    public function __construct()
    {
        if (app()->environment() != 'local') {
            $this->middleware('throttle:1,1')->only('subscribe');
        }
    }

    public function welcome()
    {
        return view('course::layouts.prelaunched')
            ->with(['course' => Nereus::course()]);
    }

    public function subscribe()
    {
        $course = Nereus::course();

        request()->validate([
            'email' => ['required', 'email:rfc,dns', new SubscriberExists($course->id)],
            'uuid' => 'exists:courses,uuid',
        ]);

        // Add subscriber to course.
        // For now we need to use the eduka-progressive for data retention.
        $subscriber = Subscriber::connection(env('PROGRESSIVE_DB_CONNECTION'))
                                ->create([
            'course_id' => $course->id,
            'email' => request()->email,
        ]);

        return view('course::layouts.prelaunched')->with(
            'message',
            trans('nereus::nereus.subscription-completed', ['course' => $course->name])
        );
    }
}
