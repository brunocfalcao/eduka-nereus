<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Notifications\UserEmailNotification;
use Brunocfalcao\Cerebrus\Cerebrus;
use Eduka\Cube\Models\Coupon;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Order;
use Eduka\Cube\Models\User;
use Eduka\Nereus\NereusServiceProvider;
use Eduka\Nereus\Payments\PaymentProviders\LemonSqueezy\LemonSqueezy;
use Eduka\Nereus\Payments\PaymentProviders\LemonSqueezy\Responses\CreatedCheckoutResponse;
use Eduka\Payments\Notifications\WelcomeNewCourseUserNotification;
use Exception;
use Illuminate\Http\Request as HttpRequest;
use Hibit\GeoDetect;
use Hibit\Country\CountryRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private Cerebrus $session;
    private string $lemonSqueezyApiKey;
    private Course $course;

    public function __construct(Cerebrus $session)
    {
        $this->lemonSqueezyApiKey = env('LEMON_SQUEEZY_API_KEY');
        $this->session = $session;
        $this->course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);
    }

    public function redirectToCheckoutPage(HttpRequest $request)
    {
        if (!$this->course) {
            return redirect()->back();
        }

        $userCountry = self::getUserCountry($request->ip2());

        if ($userCountry) {
            $this->ensureCouponOnLemonSqueezy($userCountry);
        }

        $paymentsApi = new LemonSqueezy($this->lemonSqueezyApiKey);

        $nonceKey = Str::random();

        $checkoutResponse = $this->createCheckout($paymentsApi, $this->course, $nonceKey);

        $checkoutUrl = (new CreatedCheckoutResponse($checkoutResponse))->checkoutUrl();

        $this->session->set(NereusServiceProvider::NONCE_KEY, $nonceKey);

        return redirect()->away($checkoutUrl);
    }

    private function createCheckout(LemonSqueezy $paymentsApi, Course $course, string $nonceKey)
    {
        try {
            return $paymentsApi
                ->setRedirectUrl(route('purchase.callback', $nonceKey))
                ->setExpiresAt(now()->addHours(2)->toString())
                ->setCustomData(['course_id' => (string) $course->id]) // course metadata
                ->setCustomPrice($course->priceInCents())
                ->setStoreId($course->paymentProviderStoreId())
                ->setVariantId($course->paymentProviderProductId())
                ->createCheckout();
        } catch (\Exception $e) {
            $this->log("could not create checkout.", $e);
            throw $e;
        }
    }

    private function ensureCouponOnLemonSqueezy(CountryRecord $country)
    {
        $coupon = Coupon::where('country_iso_code', strtoupper($country->getIsoCode()))->first();
        // coupon does not exists in database
        if (!$coupon) {
            return false;
        }

        // check if coupon has remote reference id, if yes, it means coupon also exists in lemon squeezy
        if ($coupon->hasRemoteReference()) {
            return true;
        }

        // reaching here means coupon exists in database, but not on lemon squeezy.
        // create coupon on lemon squeezy and update remote reference id
        $code = $coupon->generateCodeForCountry(strtoupper($country->getName()), strtoupper($country->getIsoCode()));

        $reference = $this->createCouponInLemonSqueezy($code, $coupon->discount_amount, $coupon->is_flat_discount);

        if (!$reference) {
            // could not create coupon in lemon squezzy
            return false;
        }

        // coupon created, update $coupon in local db
        $coupon->update([
            'code' => $code,
            'remote_reference_id' => $reference,
        ]);
    }

    private function createCouponInLemonSqueezy(string $code, float $amount, bool $isFixed)
    {
        $couponApi = new LemonSqueezy($this->lemonSqueezyApiKey);

        try {
            $response = $couponApi
                ->setStoreId($this->course->paymentProviderStoreId())
                ->createDiscount($code, $amount, $isFixed);

            $res = json_decode($response, true);

            if(isset($res['errors'])) {
                $this->log($res['errors'][0]['detail'], null, $res['errors']);
                return false;
            }

            if (isset($res['data'])) {
                return $res['data']['id'];
            }

            return false;
        } catch (Exception $e) {
            $this->log("could not create coupon in lemonsquzzy", $e);

            return false;
        }
    }

    private function log(string $message, ?Exception $e, array $data = [])
    {
        if($e) {
            $data[] = [
                'message' => $e->getMessage(),
            ];
        }

        Log::error($message, $data);
    }

    public static function getUserCountry(string $ip): CountryRecord|null
    {
        try {
            $geoDetect = new GeoDetect();
            return $geoDetect->getCountry($ip);
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
        $json = $request->all();

        // check if user exists or not
        $userEmail = $json['data']['attributes']['user_email'];

        list($user, $newUser) = $this->findOrCreateUser($userEmail, $json['data']['attributes']['user_name']);
        // if yes, use user id
        // if not, create and then use user id
        $course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);

        // create order
        // send email
        // save everything in the response
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'response_body' => json_encode($json),
        ]);

        $user->notify(
            $newUser ?
            new UserEmailNotification($course->name) // @todo change here
            :
            new WelcomeNewCourseUserNotification($course->name)
            );

        return response()->json(['status' => 'ok']);
    }

    private function findOrCreateUser(string $email, string $name)
    {
        $user = User::where('email', $email)->first();
        $newUser = false;

        if (!$user) {
            $user = User::forceCreate([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random()),
                'uuid' => Str::uuid(),
                'created_at' => now(),
            ]);

            $newUser = true;
        }

        return [
            $user, $newUser,
        ];
    }
}
