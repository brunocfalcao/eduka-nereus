<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Nereus\Facades\Nereus;

class Launched extends Controller
{
    public function welcome()
    {
        // Obtain the Payment Class that we need to use.
        $gateway = Nereus::course()->payments_gateway_class;

        // Get a variant, to make some tests.

        $variant = Nereus::course()->getDefaultVariant();

        dd($variant->price(['product_ids' => 893026]));

        return view('course::layouts.launched');
    }
}
