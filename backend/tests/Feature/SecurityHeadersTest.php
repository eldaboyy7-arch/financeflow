<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class SecurityHeadersTest extends TestCase
{
    /**
     * Test core security headers are present on API responses in development environment.
     */
    public function test_api_responses_include_security_headers_in_dev(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        $permissions = (string) $response->headers->get('Permissions-Policy');
        $this->assertStringContainsString('camera=(self', $permissions);
        $this->assertStringContainsString('microphone=()', $permissions);
        $this->assertStringContainsString('geolocation=()', $permissions);

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline' https://fonts.googleapis.com", $csp);
        $this->assertStringContainsString("font-src 'self' https://fonts.gstatic.com data:", $csp);

        // Obsolete headers must NOT be present
        $this->assertFalse($response->headers->has('X-XSS-Protection'));
        // Version disclosure headers must NOT be present
        $this->assertFalse($response->headers->has('X-Powered-By'));
    }

    /**
     * Test production CSP omits unsafe-inline on script-src and omits localhost origins.
     */
    public function test_production_csp_is_strict(): void
    {
        $this->app->detectEnvironment(fn() => 'production');

        $response = $this->getJson('/api/auth/me');

        $csp = (string) $response->headers->get('Content-Security-Policy');

        // In Production: script-src 'self' without 'unsafe-inline'
        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $csp);

        // In Production: development origins must NOT be present in connect-src
        $this->assertStringNotContainsString("ws://localhost:5173", $csp);
    }

    /**
     * Test authenticated requests preserve all security headers.
     */
    public function test_authenticated_requests_include_security_headers(): void
    {
        $user = new User([
            'id' => 1,
            'name' => 'Header Test User',
            'email' => 'header_test@financeflow.test',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Test CORS preflight OPTIONS request behavior.
     */
    public function test_cors_preflight_request_behaves_correctly(): void
    {
        $response = $this->withHeaders([
            'Origin'                         => 'http://localhost:5173',
            'Access-Control-Request-Method'  => 'POST',
            'Access-Control-Request-Headers' => 'Content-Type, Authorization',
        ])->options('/api/auth/login');

        $response->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
    }
}
