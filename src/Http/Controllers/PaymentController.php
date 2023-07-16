<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Brunocfalcao\Cerebrus\Cerebrus;
use Eduka\Nereus\NereusServiceProvider;
use Eduka\Payments\PaymentsInterface;
use Illuminate\Http\Client\Request;

class PaymentController extends Controller
{
    // private Cerebrus $session;
    // private PaymentsInterface $payment;

    public function __construct(protected Cerebrus $session, protected PaymentsInterface $payment)
    {
    }

    public function viewPurchasePage()
    {
        $course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);

        return [
            'page' => [],
            'course' => $course,
        ];
    }

    public function checkout(Request $request)
    {
        $course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);
        dd($this->payment->calculateGrandTotal());
    }
}
