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

    'course' => [
        'name' => env('EDUKA_COURSE_NAME', env('APP_NAME')),
        'active' => false,
        'launched_at' => Carbon::createFromDate(2021, 07, 15)
    ]
];
