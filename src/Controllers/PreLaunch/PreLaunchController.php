<?php

namespace Eduka\Nereus\Controllers\PreLaunch;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Eduka\Cube\Models\User;

class PreLaunchController extends Controller
{
    public function welcome()
    {
        return view('site::prelaunch');
    }

    public function subscribe(Request $request)
    {
        // Testing purposes.
        if (app()->environment() != 'production') {
            User::where('email', 'bruno.falcao@live.com')->forceDelete();
        }

        $validatedData = $request->validateWithBag($request->input('bag'), [
            'email' => 'required|email:rfc,dns|unique:Eduka\Cube\Models\User,email',
        ]);

        // Create user and send email.
        $user = User::create(['name' => null,
                              'email' => strtolower($request->input('email')),
                              'password' => bcrypt(Str::random(20)),
                              'uuid' => (string) Str::uuid(), ]);

        Mail::to($request->input('email'))
            ->send(new ThanksForSubscribing($user));

        return view('welcome');
    }
}
