<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseStorageService
{
    /**
     * Delete an object from Supabase Storage via REST API.
     * Wrapped in a try-catch to ensure failures do not rollback DB operations.
     */
    public function deleteFile(string $bucket, ?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // If path is a full external URL, don't attempt Supabase storage deletion
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return false;
        }

        $supabaseUrl = rtrim(config('services.supabase.url', env('SUPABASE_URL', '')), '/');
        $serviceRoleKey = config('services.supabase.service_role_key', env('SUPABASE_SERVICE_ROLE_KEY', ''));

        if (empty($supabaseUrl) || empty($serviceRoleKey)) {
            Log::warning('Supabase credentials missing, skipping storage cleanup.', [
                'bucket' => $bucket,
                'path'   => $path,
            ]);
            return false;
        }

        try {
            // Supabase Storage DELETE /storage/v1/object/{bucket} with prefixes array
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$serviceRoleKey}",
                'apikey'        => $serviceRoleKey,
            ])->timeout(5)->delete("{$supabaseUrl}/storage/v1/object/{$bucket}", [
                'prefixes' => [$path],
            ]);

            if ($response->successful()) {
                Log::info('Successfully deleted old object from Supabase storage', [
                    'bucket' => $bucket,
                    'path'   => $path,
                ]);
                return true;
            }

            Log::warning('Supabase storage delete returned non-200 status', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'path'   => $path,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::warning('Supabase storage delete encountered network/exception error', [
                'error' => $e->getMessage(),
                'path'  => $path,
            ]);
            return false;
        }
    }

    /**
     * Generate the public CDN URL for an object key.
     */
    public function getPublicUrl(?string $path, ?string $bucket = null): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $bucket = $bucket ?? config('services.supabase.storage_bucket', 'fleet');
        $supabaseUrl = rtrim(config('services.supabase.url', env('SUPABASE_URL', '')), '/');

        if (empty($supabaseUrl)) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');
        return "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$cleanPath}";
    }
}
