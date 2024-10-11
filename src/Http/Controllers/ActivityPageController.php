<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Cube\Models\Episode;
use Illuminate\Support\Facades\Auth;

class ActivityPageController extends Controller
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
        $student = Auth::user();
        $seenEpisodes = $student->episodesThatWereSeen;
        $bookmarkedEpisodes = $student->episodesThatWereBookmarked;

        return view('backend::activity', [
            'seenEpisodes' => $seenEpisodes,
            'bookmarkedEpisodes' => $bookmarkedEpisodes
        ]);
    }
}
