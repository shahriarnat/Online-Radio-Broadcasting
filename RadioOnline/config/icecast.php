<?php
return [
    'host' => env('ICECAST_HOST', 'http://icecast:1990'),
    'username' => env('ICECAST_USERNAME', 'admin'),
    'password' => env('ICECAST_PASSWORD', 'hackme'),
    'alias' => env('ICECAST_ALIAS', '/admin'),
];
