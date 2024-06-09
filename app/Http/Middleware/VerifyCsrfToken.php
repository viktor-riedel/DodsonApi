<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/website/register',
        'api/website/send-contact-us',
        'api/website/send-enquiry',
        'api/website/send-enquiry',
        'api/auth/forgot',
        'api/auth/restore',
    ];
}
