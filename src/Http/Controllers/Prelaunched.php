<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\Facades\Nereus;

class Prelaunched extends Controller
{
    public const SUBSCRIPTION_COMPLETED = 'subscription-completed';

    public const SUBSCRIPTION_REPEATED = 'subscription-repeated';

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

        if (! Subscriber::where('email', request()->email)
                        ->where('course_id', $course->id)
                        ->exists()) {
            $subscriber = Subscriber::create([
                'course_id' => $course->id,
                'email' => request()->email,
            ]);
        }

        return back()->with(
            'message',
            isset($subscriber) ?
                __('nereus.'.self::SUBSCRIPTION_COMPLETED, ['course' => $course->name]) :
                __('nereus.'.self::SUBSCRIPTION_REPEATED)
        );
    }
}
