<?php

return [
    [
        'name' => 'Admin',
        'description' => 'can access everything',
    ],
    [
        'name' => 'System',
        'description' => 'Can access everything except users etc',
    ],
    [
        'name' => 'User',
        'description' => 'Access users functionality only',
    ],
    [
        'name' => 'Manager',
        'description' => 'Can access reports'
    ],
    [
        'name' => 'Operator',
        'description' => 'Can create offers, add parts etc'
    ],
];
