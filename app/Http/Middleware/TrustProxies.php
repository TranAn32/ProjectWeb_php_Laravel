<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Trust proxies middleware configured to trust all proxies and forwarded headers.
 *
 * This helps Laravel correctly detect HTTPS when the app is behind a proxy
 * such as Railway, Heroku, Cloud Run, etc.
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     * Set to '*' to trust all proxies (suitable for PaaS behind stable proxies).
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     * Use all X-Forwarded-* headers.
     *
     * @var int
     */
    // Numeric mask for X-Forwarded-* headers (for compatibility across framework versions)
    // This corresponds to FORWARDED_FOR | HOST | PORT | PROTO
    protected $headers = 0x0f;
}
