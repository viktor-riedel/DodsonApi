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
<p>New user is registered!</p>
<p>Just letting you know about a new registration on DODSONPARTS.ONLINE</p>
<p>Registration data</p>
<ul style="list-style: circle">
    <li>Name: {{$user->name}}</li>
    <li>Country code: {{$user->country_code}}</li>
</ul>
<p>Kind Regards, Dodson Team</p>
</body>
</html>
