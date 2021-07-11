<?php

namespace Eduka\Nereus\Controllers\PreLaunch;

use App\Http\Controllers\Controller;
use Eduka\Cube\Models\Subscriber;
use Eduka\Cube\Models\User;
use Eduka\NovaAdvancedUI\Mail\ThankYouForSubscribing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PreLaunchController extends Controller
{
    public function __construct()
    {
        if (app()->environment() == 'production') {
            $this->middleware(['throttle:2,1'])
                 ->only(['subscribe']);
        }
    }

    public function welcome()
    {
        return view('site::prelaunch');
    }

    public function subscribe(Request $request)
    {
        // Testing purposes.
        if (app()->environment() != 'production') {
            User::where('email', 'bruno.falcao@live.com')->forceDelete();
            Subscriber::where('email', 'bruno.falcao@live.com')->forceDelete();
        }

        $request->validate([
            'email' => 'required|email:rfc,dns|unique:Eduka\Cube\Models\Subscriber,email',
        ]);

        // Create subscriber.
        $subscriber = Subscriber::create([
            'name' => null,
            'email' => strtolower($request->input('email')),
        ]);

        // Send subscribed email.
        //$mailable = config('eduka-nereus.mail.subscribed');

        Mail::to($request->input('email'))
            ->send(new ThankYouForSubscribing());

        return view('site::prelaunched.default');
    }
}
