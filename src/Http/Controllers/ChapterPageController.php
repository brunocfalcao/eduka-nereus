<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Cube\Models\Chapter;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Cube\Models\Course;
use Illuminate\Support\Facades\Auth;

class ChapterPageController extends Controller
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

    public function index(Chapter $chapter)
    {
        return view('backend::chapter', [
            'course' => $chapter->course,
            'chapter' => $chapter
        ]);
    }
}
