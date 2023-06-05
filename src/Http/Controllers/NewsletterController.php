<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
use Eduka\Cube\Events\Domains\SubscriberCreated;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
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
        $this->validate($request, [
            'course_id' => 'reuqired|exists:courses,id',
            'email' => 'required|email',
        ]);

        // check if user already exists or not
        $subscriberCount = Subscriber::where('email', $request->get('email'))
            ->where('course_id', 1) // @todo change course id
            ->count();

        // @todo change json responses to return redirect back with error
        // add directives

        if ($subscriberCount > 0) {
            return redirect()
                ->back()
                ->withErrors([
                    'subscriber.message' => 'you have already subscribed to this newsletter',
                ]);
        }

        // should create event that triggers
        // subscriber -> create -> createdEvent -> listener -> mialable
        // @todo note: use session for $request->get('course_id')
        $subscriber = Subscriber::create([
            'course_id' => $request->get('course_id'),
            'email' => $request->email,
        ]);

        // @todo fetch the current course
        $course = Course::make();
        event(new SubscriberCreated($subscriber, $course));

        return redirect()
            ->back()
            ->with([
                // 2. use locale __()
                'message' => 'you have successfully subscribed to the newsletter.'
            ]);
    }
}
