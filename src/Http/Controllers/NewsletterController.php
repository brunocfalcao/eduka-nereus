<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Brunocfalcao\Cerebrus\Cerebrus;
use Eduka\Cube\Events\Subscribers\SubscriberCreated;
use Eduka\Cube\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
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
     * @param Request $request
     *
     * @return void
     */
    public function subscribeToNewsletter(Request $request)
    {
        // @todo refactor
        $course = $this->session->get('mastering-nova.course');

        $this->validate($request, [
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
                ]);
        }

        $subscriber = Subscriber::create([
            'course_id' => $request->get('course_id',$course->id),
            'email' => $request->get('email'),
        ]);

        event(new SubscriberCreated($subscriber, $course));

        return redirect()
            ->back()
            ->with([
                // 2. use locale __()
                'message' => 'you have successfully subscribed to the newsletter.'
            ]);
    }
}
