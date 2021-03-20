<?php

namespace Eduka\Nereus\Controllers;

use App\Http\Controllers\Controller;

class PostmarkController extends Controller
{
    /**
     * Treats the logic of monitoring inbound email from the Postmark
     * API. Basically, each time someone sends an email to the course
     * domain, it will be grabbed and saved on the database.
     *
     * @return void
     */
    public function inbound()
    {
        $inbound = new \Postmark\Inbound(file_get_contents('php://input'));
        storage($inbound->Subject());
    }
}
