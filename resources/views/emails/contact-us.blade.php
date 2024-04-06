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
<p>A new message from website</p>
<p>From: {{$data['first_name']}} {{$data['last_name']}}</p>
<p>Email: {{$data['user_email']}}</p>
@if(isset($data['phone']))
    <p>Phone: {{$data['phone']}}</p>
@endif
<p>Message: {!! $data['message'] !!}</p>
<p>Kind Regards, Dodson Team</p>
</body>
</html>
