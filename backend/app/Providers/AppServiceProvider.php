<?php

namespace App\Providers;

use App\Mail\Transports\ResendTransport;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Configure Named Rate Limiters for Security Hardening
        $this->configureRateLimiters();

        // 2. Driver Resend via HTTPS REST API
        Mail::extend('resend', function (array $config = []) {
            $apiKey = env('RESEND_API_KEY', env('MAIL_PASSWORD', ''));
            $fromAddress = env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev');
            $fromName = env('MAIL_FROM_NAME', 'FinanceFlow');

            return new ResendTransport($apiKey, $fromAddress, $fromName);
        });

        // 3. Custom URL reset password untuk frontend SPA
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
            return "{$frontendUrl}/reset-password?token={$token}&email=" . urlencode($notifiable->getEmailForPasswordReset());
        });

        // 4. Template email reset password dalam Bahasa Indonesia & Brand FinanceFlow
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
            $resetUrl = "{$frontendUrl}/reset-password?token={$token}&email=" . urlencode($notifiable->getEmailForPasswordReset());

            return (new MailMessage)
                ->subject('Reset Password Akun — FinanceFlow')
                ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna FinanceFlow') . '!')
                ->line('Kami menerima permintaan untuk mereset password akun FinanceFlow Anda.')
                ->action('Reset Password Sekarang', $resetUrl)
                ->line('Link reset password ini berlaku selama 60 menit.')
                ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman.')
                ->salutation("Salam hangat,\nTim FinanceFlow");
        });
    }

    /**
     * Configure named rate limiters across the application.
     */
    protected function configureRateLimiters(): void
    {
        // A. Login Limiter: 5 attempts per minute per normalized email & IP (HMAC-SHA256 with APP_KEY secret)
        RateLimiter::for('auth.login', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email', '')));
            $emailHash = $email ? hash_hmac('sha256', $email, (string) config('app.key', 'financeflow_secret')) : 'none';
            $key = 'login:' . $emailHash . '|' . $request->ip();

            return Limit::perMinute(5)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak percobaan login. Silakan coba beberapa saat lagi.',
                    ], 429, $headers);
                });
        });

        // B. Registration Limiter: 5 requests per minute per IP
        RateLimiter::for('auth.register', function (Request $request) {
            return Limit::perMinute(5)
                ->by('register:' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan registrasi. Silakan coba beberapa saat lagi.',
                    ], 429, $headers);
                });
        });

        // C. Forgot Password Limiter: 5 requests per minute per IP
        RateLimiter::for('auth.forgot', function (Request $request) {
            return Limit::perMinute(5)
                ->by('forgot:' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan reset password. Silakan coba beberapa saat lagi.',
                    ], 429, $headers);
                });
        });

        // D. Reset Password Submission Limiter: 5 requests per minute per IP
        RateLimiter::for('auth.reset', function (Request $request) {
            return Limit::perMinute(5)
                ->by('reset:' . $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan pembaruan password. Silakan coba beberapa saat lagi.',
                    ], 429, $headers);
                });
        });

        // E. AI Endpoints (Advisor & Receipt Scanner): 20 requests per minute per user/IP
        RateLimiter::for('ai.endpoints', function (Request $request) {
            $identifier = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(20)
                ->by('ai:' . $identifier)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Batas penggunaan fitur AI tercapai. Silakan coba 1 menit lagi.',
                    ], 429, $headers);
                });
        });

        // F. Financial Mutations: 60 requests per minute per user/IP
        RateLimiter::for('financial.mutations', function (Request $request) {
            $identifier = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(60)
                ->by('mutation:' . $identifier)
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak transaksi/mutasi dalam waktu singkat. Silakan tunggu sejenak.',
                    ], 429, $headers);
                });
        });

        // G. General API Read: 120 requests per minute per user/IP
        RateLimiter::for('api.general', function (Request $request) {
            $identifier = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(120)->by('api:' . $identifier);
        });
    }
}
