<?php

use Illuminate\Support\Carbon;

return [

    /*
    |--------------------------------------------------------------------------
    | Your course information
    |--------------------------------------------------------------------------
    |
    | This file is for storing information regarding your course.
    | Most of this information can be placed in environment variables.
    | Remember that you can change these values to dynamic class values in
    | case you need more logic on them.
    |
    */

    'name' => env('EDUKA_COURSE_NAME', env('APP_NAME')),

    /*
     * An "inactive" page will appear in case the active is false.
     **/
    'active' => true,

    /*
     * There is some logic applied with a date, so, don't put it null
     * but just a very far future date.
     **/
    'launched_at' => Carbon::createFromDate(2025, 07, 15),

    /*
     * Mailable classes to send emails
     **/
    'mail' => [

        /*
         * Thank you email, when someone subscribes your pre-launch page.
         * Constructor needs to receive a Eduka\Cube\Models\Subscriber
         * object.
         **/
        'subscribed' => \Eduka\NovaAdvancedUI\Mail\ThankYouForSubscribing::class,

    ],
];
