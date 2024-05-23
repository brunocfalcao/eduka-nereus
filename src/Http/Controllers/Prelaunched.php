<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Nereus\Rules\SubscriberExists;
use Illuminate\Support\Facades\DB;

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
        dd('welcome!');
        return view('course::layouts.prelaunched')->with(
            ['course' => Nereus::course()]
        );
    }

    public function subscribe()
    {
        $course = Nereus::course();

        request()->validate([
            'email' => ['required', 'email:rfc,dns', new SubscriberExists($course->id)],
            'uuid' => 'exists:courses,uuid',
        ]);

        $subscriber = new Subscriber();

        $subscriber = $subscriber->create([
            'course_id' => $course->id,
            'email' => request()->email,
        ]);

        /**
         * We now need to create the subscriber ALSO in the
         * progressive database, until everything is done.
         */
        DB::connection(env('PROGRESSIVE_DB_CONNECTION'))
            ->table('subscribers')
            ->insert([
                'course_id' => $subscriber->course_id,
                'email' => $subscriber->email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        return view('course::layouts.prelaunched')->with(
            'message',
            trans('nereus::nereus.subscription-completed', ['course' => $course->name])
        );
    }
}
