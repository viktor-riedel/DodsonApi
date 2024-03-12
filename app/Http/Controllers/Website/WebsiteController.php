<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\ContactUsRequest;
use App\Mail\ContactUsMail;

class WebsiteController extends Controller
{
    public function sendContactEmail(ContactUsRequest $request)
    {
        $data = [
            'first_name' => $request->validated('first_name'),
            'last_name' => $request->validated('last_name'),
            'user_email' => $request->validated('user_email'),
            'message' => $request->validated('message'),
        ];

        \Mail::to(config('misc.info_email'))->send(new ContactUsMail($data));

        return response()->json([], 202);
    }
}
