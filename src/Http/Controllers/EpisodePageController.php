<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;
use Eduka\Cube\Models\Episode;
use Illuminate\Support\Facades\Auth;

class EpisodePageController extends Controller
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

    public function index(Episode $episode)
    {
        return view('backend::episode', [
            'episode' => $episode,
            'chapter' => $episode->chapter()->first(),
            'course' => $episode->course()->first()
        ]);
    }
}
