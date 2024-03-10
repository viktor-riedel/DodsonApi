<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DODSON PARTS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
    </style>
</head>
<body class="antialiased">
<p>You requested to change your password on dodsonparts.online</p>
<p>Please delete this message if you did not request a password change.</p>
<p>Please follow that link to reset your password <a href="{{config('misc.front_end_url') . '/restore/' . $user->reset_code}}">RESET PASSWORD</a></p>
<p>Kind Regards, Dodson Team</p>
</body>
</html>
