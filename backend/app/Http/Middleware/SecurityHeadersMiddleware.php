<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply hardened HTTP security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Suppress PHP version disclosure header before response is sent
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        $response = $next($request);

        $isProduction = app()->isProduction();
        $frontendUrl  = rtrim(env('FRONTEND_URL', ''), '/');
        $appUrl       = rtrim(config('app.url', ''), '/');

        // 1. Prevent MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 2. Clickjacking protection (Deny framing)
        $response->headers->set('X-Frame-Options', 'DENY');

        // 3. Referrer Policy: Send full referrer on same-origin, origin-only on cross-origin HTTPS, no referrer on downgrade
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 4. Permissions-Policy: Allow camera on self and frontend origin (for Smart Receipt Scanner) while disabling unused device APIs
        $cameraOrigins = ['self'];
        if ($frontendUrl) {
            $parsedOrigin = parse_url($frontendUrl, PHP_URL_SCHEME) . '://' . parse_url($frontendUrl, PHP_URL_HOST) . (parse_url($frontendUrl, PHP_URL_PORT) ? ':' . parse_url($frontendUrl, PHP_URL_PORT) : '');
            if ($parsedOrigin && $parsedOrigin !== '://') {
                $cameraOrigins[] = '"' . $parsedOrigin . '"';
            }
        }
        $cameraPolicy = 'camera=(' . implode(' ', array_unique($cameraOrigins)) . ')';
        $response->headers->set('Permissions-Policy', "{$cameraPolicy}, microphone=(), geolocation=(), payment=(), usb=(), display-capture=()");

        // 5. Content-Security-Policy (Environment-Aware Least-Privilege Policy)
        $connectOrigins = ["'self'"];
        if ($frontendUrl) {
            $connectOrigins[] = $frontendUrl;
        }
        if ($appUrl) {
            $connectOrigins[] = $appUrl;
        }

        if (! $isProduction) {
            // Development-only origins for local Vite dev server and HMR WebSockets
            $connectOrigins = array_merge($connectOrigins, [
                'http://localhost:8000',
                'http://127.0.0.1:8000',
                'http://localhost:5173',
                'http://127.0.0.1:5173',
                'ws://localhost:5173',
                'ws://127.0.0.1:5173',
            ]);
        }

        // Fonts & stylesheet endpoints
        $connectOrigins[] = 'https://fonts.googleapis.com';
        $connectOrigins[] = 'https://fonts.gstatic.com';
        $connectOriginsStr = implode(' ', array_unique(array_filter($connectOrigins)));

        // In Production: script-src 'self' (No unsafe-inline, as compiled Vite dist contains zero inline scripts)
        // In Development: script-src 'self' 'unsafe-inline' (Permits Vite HMR client and runtime modules)
        $scriptSrc = $isProduction ? "script-src 'self'" : "script-src 'self' 'unsafe-inline'";
        
        // style-src requires 'unsafe-inline' across environments due to Vue dynamic :style bindings and ApexCharts CSS injection
        $styleSrc  = "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com";
        $fontSrc   = "font-src 'self' https://fonts.gstatic.com data:";
        $imgSrc    = "img-src 'self' data: blob:";

        $csp = [
            "default-src 'self'",
            $scriptSrc,
            $styleSrc,
            $fontSrc,
            $imgSrc,
            "connect-src {$connectOriginsStr}",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        // 6. Anti Information Disclosure: Strip server signature headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
