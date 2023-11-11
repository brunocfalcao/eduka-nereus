<?php

namespace Eduka\Nereus\Http\Controllers\Prelaunched;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;

class Welcome extends Controller
{
    public function index()
    {
        dd('hi there!');

        return view('course::prelaunched')
               ->with(['course' => Nereus::matchCourse()]);
    }
}
