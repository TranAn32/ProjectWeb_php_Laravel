<?php

namespace App\Http\Middleware;

// Đối với Laravel 8+
use Illuminate\Http\Middleware\TrustProxies as Middleware;
// Đối với Laravel cũ hơn (5.5 -> 7), bạn có thể cần dùng:
// use Fideloper\Proxy\TrustProxies as Middleware;

use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies = '*'; // <--- Quan trọng: Tin tưởng tất cả proxy

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