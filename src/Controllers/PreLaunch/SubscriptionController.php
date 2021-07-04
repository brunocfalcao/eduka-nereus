<?php

namespace Eduka\Nereus\Controllers\PreLaunch;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Stores a new subscription into de course database.
     *
     * @return type
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'email|unique:subscribers|required'
        ])->validate();
    }
}
