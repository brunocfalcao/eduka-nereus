<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;

class Launched extends Controller
{
    public function welcome()
    {
        return view('course::layouts.launched');
    }
}
