<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\Facades\Nereus;

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
        return view('course::prelaunched')
               ->with(['course' => Nereus::course()]);
    }

    public function subscribe()
    {
        request()->validate([
            'email' => 'required|email',
        ]);

        $course = Nereus::course();

        // Check if the subscriber + course already exists on the database.
        if (! Subscriber::where('email', request()->email)
                        ->where('course_id', $course->id)
                        ->exists()) {
            $subscriber = Subscriber::create([
                'course_id' => $course->id,
                'email' => request()->email,
            ]);
        }

        /**
         * If the subscriber email + course already subscribed then just
         * give a nice message saying it's already subscribed. If not,
         * then subscribe it with a thank you message.
         */
        return back()->with(
            'message',
            isset($subscriber) ?
                Nereus::trans('subscription-completed', ['course' => $course->name], 'nereus') :
                Nereus::trans('subscription-repeated', [], 'nereus')
        );
    }
}
