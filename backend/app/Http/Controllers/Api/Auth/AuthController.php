<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        // Rate limit: max 5 registration attempts per minute per IP
        $key = 'register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Seed defaults for the new user
        $this->seedDefaultCategories($user);
        $this->seedDefaultAccount($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Clear rate limiter on success
        RateLimiter::clear($key);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Rate limit: max 5 login attempts per minute per email+IP
        $key = 'login:' . Str::lower($request->email) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'currency' => 'sometimes|string|size:3',
        ]);

        $request->user()->update($validated);

        return response()->json(['user' => $request->user()->fresh()]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return response()->json(['message' => 'Password lama salah.'], 422);
        }

        $user = $request->user();
        $user->update(['password' => Hash::make($request->password)]);

        // Security Hardening: Revoke all existing tokens on password change
        $user->tokens()->delete();

        // Generate a fresh token for current session
        $newToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Password berhasil diubah.',
            'token'   => $newToken,
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        // Rate limit: max 3 reset attempts per minute per IP
        $key = 'forgot-password:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }
        RateLimiter::hit($key, 60);

        // Attempt to send reset link (silently succeeds even if email doesn't exist)
        Password::sendResetLink($request->only('email'));

        // SECURITY: Always return the same message to prevent email enumeration.
        // An attacker cannot tell whether the email is registered or not.
        return response()->json([
            'message' => 'Jika email terdaftar, kami telah mengirimkan link reset password. Silakan cek inbox dan folder spam.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                // Security Hardening: Revoke all existing session tokens on password reset
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password berhasil direset.']);
        }

        return response()->json(['message' => __($status)], 422);
    }

    private function seedDefaultCategories(User $user): void
    {
        $categories = [
            // Pemasukan
            ['name' => 'Gaji',      'type' => 'income',  'icon' => '💰', 'color' => '#10B981'],
            ['name' => 'Bisnis',    'type' => 'income',  'icon' => '🏢', 'color' => '#6366F1'],
            ['name' => 'Freelance', 'type' => 'income',  'icon' => '💻', 'color' => '#8B5CF6'],
            ['name' => 'Investasi', 'type' => 'income',  'icon' => '📈', 'color' => '#F59E0B'],
            ['name' => 'Bonus',     'type' => 'income',  'icon' => '🎁', 'color' => '#EC4899'],
            ['name' => 'Hadiah',    'type' => 'income',  'icon' => '🎀', 'color' => '#F43F5E'],
            ['name' => 'Lainnya',   'type' => 'income',  'icon' => '📦', 'color' => '#64748B'],
            // Pengeluaran
            ['name' => 'Makanan',       'type' => 'expense', 'icon' => '🍔', 'color' => '#F97316'],
            ['name' => 'Transportasi',  'type' => 'expense', 'icon' => '🚗', 'color' => '#3B82F6'],
            ['name' => 'Belanja',       'type' => 'expense', 'icon' => '🛒', 'color' => '#EC4899'],
            ['name' => 'Tagihan',       'type' => 'expense', 'icon' => '📋', 'color' => '#EF4444'],
            ['name' => 'Hiburan',       'type' => 'expense', 'icon' => '🎬', 'color' => '#A855F7'],
            ['name' => 'Kesehatan',     'type' => 'expense', 'icon' => '🏥', 'color' => '#14B8A6'],
            ['name' => 'Pendidikan',    'type' => 'expense', 'icon' => '📚', 'color' => '#F59E0B'],
            ['name' => 'Perumahan',     'type' => 'expense', 'icon' => '🏠', 'color' => '#6366F1'],
            ['name' => 'Langganan',     'type' => 'expense', 'icon' => '📱', 'color' => '#8B5CF6'],
            ['name' => 'Keluarga',      'type' => 'expense', 'icon' => '👨‍👩‍👧', 'color' => '#10B981'],
            ['name' => 'Lainnya',       'type' => 'expense', 'icon' => '📦', 'color' => '#64748B'],
        ];

        foreach ($categories as $cat) {
            $user->categories()->create($cat);
        }
    }

    private function seedDefaultAccount(User $user): void
    {
        $account = $user->accounts()->create([
            'name'            => 'Tunai',
            'type'            => 'cash',
            'icon'            => '💵',
            'color'           => '#10B981',
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active'       => true,
        ]);

        // Set as user's default account
        $user->update(['default_account_id' => $account->id]);
    }
}
