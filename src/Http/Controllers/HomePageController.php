<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $course = Nereus::backend()->courses()->first();

        // No courses in this backend, this shouldn't happen
        if ($course === null)
            dd("Error: Backend has no courses");

        return redirect()->route('course.view', ['course' => $course]);
        /* TODO: Make a home page that lists all courses */
        /*
        return view('backend::home');
        */
    }
}
