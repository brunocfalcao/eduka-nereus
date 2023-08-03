<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Brunocfalcao\Cerebrus\Cerebrus;
use Eduka\Cube\Models\Coupon;
use Eduka\Cube\Models\User;
use Eduka\Nereus\NereusServiceProvider;
use Eduka\Nereus\Payments\PaymentProviders\LemonSqueezy\LemonSqueezy;
use Eduka\Nereus\Payments\PaymentProviders\LemonSqueezy\Responses\CreatedCheckoutResponse;
use Exception;
use Illuminate\Http\Request as HttpRequest;
use Hibit\GeoDetect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private Cerebrus $session;
    // private PaymentsInterface $payment;
    private string $lemonSqueezyApiKey;

    public function __construct(Cerebrus $session)
    {
        $this->lemonSqueezyApiKey = env('LEMON_SQUEEZY_API_KEY');
        $this->session = $session;
    }

    public function redirectToCheckoutPage(HttpRequest $request)
    {
        $course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);

        if (!$course) {
            return redirect()->back();
        }

        // @todo remove hardcoded ip
        // $request->ip()
        $userCountryIsoCode = $this->getUserCountryIsoCode('101.188.67.134');

        if ($userCountryIsoCode) {
            $this->ensureCouponOnLemonSqueezy($userCountryIsoCode);
        }

        $paymentsApi = new LemonSqueezy($this->lemonSqueezyApiKey);

        $nonceKey = Str::random();

        // should i create the user first?
        $r = $paymentsApi
            ->setRedirectUrl(route('purchase.callback', $nonceKey))
            ->setExpiresAt(now()->addHours(2)->toString())
            ->setVariantId("102989") // @todo change from db
            ->createCheckout();

        $checkoutUrl = (new CreatedCheckoutResponse($r))->checkoutUrl();

        $this->session->set(NereusServiceProvider::NONCE_KEY, $nonceKey);

        return redirect()->away($checkoutUrl);
    }

    private function ensureCouponOnLemonSqueezy(string $userCountryIsoCode)
    {
        $coupon = Coupon::where('country_iso_code', strtolower($userCountryIsoCode))->exists();

        if ($coupon) {
            return true;
        }

        // create on lemon squzze
        $lsApi = new LemonSqueezy($this->lemonSqueezyApiKey);
        $amount = 30;
        $code = "ILOVE" . strtoupper($userCountryIsoCode) . $amount;
        $isFlat = false;
        // @todo note: remove hardcoded value
        $remoteRef = null;

        try {
            $response = $lsApi->createDiscount($code, $code, $amount, $isFlat);

            $res = json_decode($response, true);

            if (isset($res['data'])) {
                $remoteRef = $res['data']['id'];
            }
        } catch (Exception $e) {
            $this->log("could not create coupon in lemonsquzzy", $e);

            return false;
        }

        return $this->storeCouponInDb(
            $code,
            $isFlat ? $amount * 100 : $amount,
            $isFlat,
            $userCountryIsoCode,
            $remoteRef
        );
    }

    private function storeCouponInDb(string $code, float $amount, bool $isFlat, string $userCountryIsoCode, string $remoteRef = null)
    {
        return Coupon::forceCreate([
            'code' => $code,
            // 'name' => $code,
            'is_flat_discount' => $isFlat,
            'discount_amount' => $amount,
            'country_iso_code' => $userCountryIsoCode,
            'remote_reference_id' => $remoteRef,
        ]);
    }

    private function log(string $message, Exception $e)
    {
        Log::error($message, [
            'message' => $e->getMessage(),
        ]);
    }

    private function getUserCountryIsoCode(string $ip)
    {
        try {
            $geoDetect = new GeoDetect();
            $country = $geoDetect->getCountry($ip);

            return $country->getIsoCode();
        } catch (\Exception $e) {
            // pass
            Log::error('could not determine user country', [
                'message' => $e->getMessage()
            ]);
        }

        return null;
    }

    public function handleWebhook(HttpRequest $request)
    {
        $this->validate($request, [
            'attributes.user_email' => 'required',
        ]);

        $email = $request->get('attributes.user_email');
        $user = User::whereEmail($email)->first();

        if (!$user) {
            logger(sprintf('user with email: %s not found.', $email));
            return;
        }
    }
}
