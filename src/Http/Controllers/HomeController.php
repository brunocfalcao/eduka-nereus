<?php

namespace Eduka\Nereus\Http\Controllers;

use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

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

    public function index(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return Auth::user()->courses->count() == 1 ?
            view('backend::home-one-course') :
            view('backend::home-multiple-courses');
    }
}
