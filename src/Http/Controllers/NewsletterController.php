<?php

namespace Eduka\Nereus\Http\Controllers;

use App\Http\Controllers\Controller;
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
            ->where('course_id', $request->course_id)
            ->count();

        // @todo refactor response code
        if ($subscriberCount > 0) {
            return response()->json([
                'status' => 'error',
                // @todo
                // 1. update message
                // 2. use locale __()
                'message' => 'you have already subscribed to this newsletter.'
            ]);
        }

        // should create event that triggers
        // subscriber -> create -> createdEvent -> listener -> mialable
        // @todo note: use session for $request->get('course_id')
        $subscriber = Subscriber::create([
            'course_id' => $request->get('course_id'),
            'email' => $request->email,
        ]);

        // can call event here
        // use normal markdown email

        return response()->json([
            'status' => 'success',
            // @todo
            // 1. update message
            // 2. use locale __()
            'message' => 'you have successfully subscribed to the newsletter.'
        ]);
    }
}
