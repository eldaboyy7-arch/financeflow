<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    );

if (isset($_ENV['APP_STORAGE']) || getenv('APP_STORAGE')) {
    $app->useStoragePath(getenv('APP_STORAGE') ?: $_ENV['APP_STORAGE']);
}

return $app
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // If an HttpResponseException was explicitly created (e.g. from RateLimiter or custom responses)
        $exceptions->render(function (\Illuminate\Http\Exceptions\HttpResponseException $e, \Illuminate\Http\Request $request) {
            return $e->getResponse();
        });

        // Handle Rate Limiter Throttling — return 429 JSON with Retry-After headers
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Terlalu banyak permintaan. Silakan coba beberapa saat lagi.',
                ], 429, $e->getHeaders());
            }
        });

        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Terlalu banyak permintaan. Silakan coba beberapa saat lagi.',
                ], 429, $e->getHeaders());
            }
        });

        // Handle authentication errors — return 401 JSON
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        // Handle validation errors — return 422 JSON dengan field errors
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Data yang diberikan tidak valid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Handle standard HTTP exceptions (403, 404, 405, 429, dll)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Permintaan tidak dapat diproses.',
                ], $e->getStatusCode(), $e->getHeaders());
            }
        });

        // Return JSON errors untuk semua API errors lainnya
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'message' => $e->getMessage() ?: 'Terjadi kesalahan pada server.',
                ], $status);
            }
        });
    })->create();
