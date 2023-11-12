<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Brunocfalcao\Cerebrus\Cerebrus;
use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Cube\Models\Subscriber;
use Eduka\Nereus\NereusServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscribeController extends Controller
{
    private Cerebrus $session;

    public function __construct(Cerebrus $session)
    {
        $this->session = $session;
    }

    /**
     * To subscribe a user to the newsletter, we at first check
     * if the given email is already registered to this course's
     * newsletter.
     *
     * if yes, we notify the user.
     * else we create a new subscriber.
     *
     *
     * @return void
     */

    /**
     * Subscribes to the course to have early access and/or a special
     * early access discount. Normally it happens for situations that
     * the course starts to be sold before it's completed, and also
     * if the visitor wants to know when the course is launched.
     */
    public function subscribeToNewsletter(Request $request)
    {
        $course = Nereus::course();

        // @todo take it behind some method, so we don't missmatch the string key.
        //$course = $this->session->get(NereusServiceProvider::COURSE_SESSION_KEY);

        Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        // check if user already exists or not
        $subscriberCount = Subscriber::where('email', $request->get('email'))
            ->where('course_id', $course->id)
            ->count();

        if ($subscriberCount > 0) {
            return redirect()
                ->back()
                ->withErrors([
                    'subscriber.message' => 'you have already subscribed to this newsletter',
                ], 'subscribeToNewsletter');
        }

        $subscriber = Subscriber::create([
            'course_id' => $request->get('course_id', $course->id),
            'email' => $request->get('email'),
        ]);

        event(new SubscriberCreated($subscriber, $course));

        $this->session->set('subscribed_'.$course->id.'_newsletter', true);

        return redirect()
            ->back()
            ->with([
                'notification.newsletter.message' => 'you have successfully subscribed to the newsletter.',
            ]);
    }
}
