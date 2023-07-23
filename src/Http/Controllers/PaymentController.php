<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Brunocfalcao\Cerebrus\Cerebrus;
use Eduka\Nereus\NereusServiceProvider;
use Eduka\Payments\PaymentProviders\LemonSqueezy\LemonSqueezy;
use Illuminate\Http\Client\Request;

class PaymentController extends Controller
{
    // private Cerebrus $session;
    // private PaymentsInterface $payment;

    public function __construct(protected Cerebrus $session)
    {
    }

    public function viewPurchasePage()
    {
        $course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);

        $paymentsApi = new LemonSqueezy();
        $paymentsApi->getProductById($course->paymentProviderProductId());

        return [
            'page' => [],
            'purchasePage' => $course->paymentProviderProductId(),
            'course' => $course,
        ];
    }

    public function checkout(Request $request)
    {
        $course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);

        // fetch product
        // if it exists
        //
        dd($course->paymentProviderProductId());
    }
}
