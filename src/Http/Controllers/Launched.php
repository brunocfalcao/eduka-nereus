<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;

class Launched extends Controller
{
    public function __construct()
    {
        if (app()->environment() != 'local') {
            $this->middleware('throttle:10,1')->only('welcome');
        }
    }

    public function welcome()
    {
        return view('course::launched')
               ->with(['course' => Nereus::course()]);
    }
}
