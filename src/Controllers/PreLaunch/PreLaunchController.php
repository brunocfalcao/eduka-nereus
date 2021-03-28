<?php

namespace Eduka\Nereus\Controllers\PreLaunch;

use App\Http\Controllers\Controller;

class PreLaunchController extends Controller
{
    public function welcome()
    {
        return view('eduka::pre-launch.welcome');
    }

    public function subscribe()
    {
        dd(request()->input());
    }
}
