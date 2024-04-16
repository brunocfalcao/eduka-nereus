<?php

namespace Eduka\Nereus\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SubscriberExists implements Rule
{
    protected $courseId;

    public function __construct($courseId)
    {
        $this->courseId = $courseId;
    }

    public function passes($attribute, $value)
    {
        // Check if email already exists with the same course_id
        return ! DB::table('subscribers') // replace 'your_table_name' with your actual table name
            ->where('course_id', $this->courseId)
            ->where('email', $value)
            ->exists();
    }

    public function message()
    {
        return 'The email is already registered for this course';
    }
}
