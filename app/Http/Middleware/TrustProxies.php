<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';
//    protected $proxies =
//        [
//            '185.234.113.1',
//            '185.234.113.2',
//            '185.234.113.3',
//            '185.234.113.4',
//            '185.234.113.5',
//            '185.234.113.6',
//            '185.234.113.7',
//            '185.234.113.8',
//            '185.234.113.9',
//            '185.234.113.10',
//            '185.234.113.11',
//            '185.234.113.12',
//            '185.234.113.13',
//            '185.234.113.14',
//            '185.234.113.15',
//        ];

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
