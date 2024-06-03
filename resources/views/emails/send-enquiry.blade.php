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
<p>A new order eqnuiry from website</p>
<p>From: {{$data['name']}}</p>
<p>Email: {{$data['email']}}</p>
<p>Phone: {{$data['phone']}}</p>
<p>Order: {!! $data['order'] !!}</p>
<p>The lead has been created in admin panel</p>
<p>Kind Regards, Dodson Team</p>
</body>
</html>
