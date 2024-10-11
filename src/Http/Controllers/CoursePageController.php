<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\Auth;

class CoursePageController extends Controller
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

    public function index(Course $course)
    {
        return view('backend::course', [
            'course' => $course
        ]);
    }
}
