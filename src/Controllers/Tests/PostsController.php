<?php

namespace Eduka\Nereus\Controllers\Tests;

use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    public function subscribe($post, $comment)
    {
        dd(request()->fullUrl(), request()->path(), request()->url(), request()->query('cmpgn'));
    }
}
