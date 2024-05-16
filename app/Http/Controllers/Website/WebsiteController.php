<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\ContactUsRequest;
use App\Http\Requests\Website\EnquiryRequest;
use App\Mail\ContactUsMail;
use App\Mail\EnquiryMail;
use App\Models\Lead;

class WebsiteController extends Controller
{
    public function sendContactEmail(ContactUsRequest $request): \Illuminate\Http\JsonResponse
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

    public function sendEnquiry(EnquiryRequest $request)
    {
        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'order' => $request->validated('order'),
        ];
        $lead = Lead::create([
            'lead_name' => 'Website Enquiry',
            'from' => $request->validated('name'),
            'lead_type' => Lead::LEAD_TYPES['ENQUIRY'],
            'lead_status' => 0,
        ]);
        \Mail::to(config('misc.info_email'))->send(new EnquiryMail($data));
    }
}
