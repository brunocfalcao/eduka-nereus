<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;

class HomeController extends Controller
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
        if (Nereus::backend()->courses()->count() == 1) {
            //dd(Nereus::backend()->courses()->first()->chapters()->with('episodes')->first()->episodes);
            return view('backend::home-single-course', ['course' => Nereus::backend()->courses()->first()]);
        } else if (Nereus::backend()->courses()->count() > 1) {
            return view('backend::home-multiple-courses', ['courses' => Nereus::backend()->courses]);
        }

        // No courses in this backend, this shouldn't happen
        dd("Error: no courses in this backend");

        // if backend has multiple courses:
        // backend::home-multiple-courses, pass the courses list (ALL courses)
        // inside the view:
        // for course in courses:
        // for chapter in course->chapters:
        // if chapter->canBeClickable():
        // normal
        // there are episodes the user can access (either free or already purchased)
        // else:
        // gray, if they click they get taken to the buy course page

        // bought 1 out of 1 -> show single course
    }
}
