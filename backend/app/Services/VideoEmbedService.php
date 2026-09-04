<?php

namespace App\Services;

class VideoEmbedService
{
    /**
     * Allowed video hosting hostnames.
     */
    private const ALLOWED_HOSTS = [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'youtu.be',
        'www.youtu.be',
        'tiktok.com',
        'www.tiktok.com',
        'vm.tiktok.com',
        'instagram.com',
        'www.instagram.com',
    ];

    /**
     * Determine if a given URL is from an authorized video provider.
     */
    public function isValidPlatformUrl(?string $url): bool
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        return in_array($host, self::ALLOWED_HOSTS, true);
    }

    /**
     * Parse raw video URL and return safe embed URL for iframes.
     * Returns null if URL is invalid or unrecognized platform.
     */
    public function toSafeEmbedUrl(?string $url): ?string
    {
        if (!$this->isValidPlatformUrl($url)) {
            return null;
        }

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

        // ── 1. YouTube ─────────────────────────────────────────────────
        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be')) {
            $videoId = null;

            if (str_contains($host, 'youtu.be')) {
                $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
                $segments = explode('/', $path);
                $videoId = $segments[0] ?? null;
            } elseif (str_contains($url, '/shorts/')) {
                if (preg_match('/\/shorts\/([a-zA-Z0-9_-]{11})/i', $url, $m)) {
                    $videoId = $m[1];
                }
            } elseif (str_contains($url, '/embed/')) {
                if (preg_match('/\/embed\/([a-zA-Z0-9_-]{11})/i', $url, $m)) {
                    $videoId = $m[1];
                }
            } else {
                parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
                $videoId = $query['v'] ?? null;
            }

            if ($videoId && preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId)) {
                return "https://www.youtube-nocookie.com/embed/{$videoId}";
            }
        }

        // ── 2. TikTok ──────────────────────────────────────────────────
        if (str_contains($host, 'tiktok.com')) {
            if (preg_match('/\/video\/(\d+)/i', $url, $m)) {
                return "https://www.tiktok.com/embed/v2/{$m[1]}";
            }
        }

        // ── 3. Instagram ───────────────────────────────────────────────
        if (str_contains($host, 'instagram.com')) {
            if (preg_match('/\/(?:reel|p)\/([a-zA-Z0-9_-]+)/i', $url, $m)) {
                return "https://www.instagram.com/reel/{$m[1]}/embed/";
            }
        }

        return null;
    }
}
