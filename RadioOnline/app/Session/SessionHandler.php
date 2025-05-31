<?php

namespace App\Session;

use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Facades\Request;

class SessionHandler extends DatabaseSessionHandler
{
    public function write($sessionId, $data): bool
    {
        $userAgent = Request::header('User-Agent');

        $skipUserAgent = [
            'Googlebot',
            'Bingbot',
            'Slurp',
            'DuckDuckBot',
            'Baidu',
            'YandexBot',
            'Sogou',
            'Exabot',
            'Facebot',
            'ia_archiver',
            'RadioLiquidSoap/1.0'
        ];

        foreach ($skipUserAgent as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }
        return parent::write($sessionId, $data);
    }
}
