<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicVehicleResource;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicFleetController extends Controller
{
    /**
     * Return public fleet catalog (Read-only, cached in memory & Cloudflare CDN).
     */
    public function index(Request $request): JsonResponse
    {
        $version = (int) Cache::get('public_fleet_version', 1);
        $cacheKey = "public_fleet_v{$version}_" . md5(json_encode($request->all()));

        // Cache for 30 minutes (1800s) on backend
        $cachedData = Cache::remember($cacheKey, 1800, function () use ($request) {
            // Determine the rental business owner ID
            $ownerId = (int) env('RENTAL_OWNER_ID', config('app.rental_owner_id', 0));

            if ($ownerId <= 0) {
                // Auto-detect the owner who actually owns vehicles in the fleet
                $ownerId = (int) Vehicle::whereNotNull('user_id')->value('user_id');
            }

            if ($ownerId <= 0) {
                $ownerId = (int) User::orderBy('id')->value('id');
            }

            if ($ownerId <= 0) {
                return [
                    'data' => [],
                    'meta' => ['total_fleet' => 0],
                ];
            }

            $query = Vehicle::where('user_id', $ownerId);

            // Optional filter by brand
            if ($request->filled('brand')) {
                $query->where('brand', $request->get('brand'));
            }

            // Optional filter by transmission
            if ($request->filled('transmission') && in_array($request->get('transmission'), ['matic', 'manual'], true)) {
                $query->where('transmission', $request->get('transmission'));
            }

            // Optional filter by status
            if ($request->filled('status') && in_array($request->get('status'), ['available', 'rented', 'maintenance'], true)) {
                $query->where('status', $request->get('status'));
            }

            // Dedicated Featured Vehicles query for Homepage (with automatic fallback to available units)
            if ($request->boolean('featured')) {
                $limit = $request->integer('limit', 3);
                if ($limit <= 0) {
                    $limit = 3;
                }

                // 1. Fetch featured vehicles
                $featuredVehicles = (clone $query)
                    ->where('is_featured', true)
                    ->orderBy('name')
                    ->limit($limit)
                    ->get();

                // 2. Pad with available units if featured count < limit
                if ($featuredVehicles->count() < $limit) {
                    $needed = $limit - $featuredVehicles->count();
                    $excludedIds = $featuredVehicles->pluck('id')->toArray();

                    $fallbackVehicles = (clone $query)
                        ->whereNotIn('id', $excludedIds)
                        ->where('status', 'available')
                        ->orderBy('name')
                        ->limit($needed)
                        ->get();

                    $vehicles = $featuredVehicles->concat($fallbackVehicles);
                } else {
                    $vehicles = $featuredVehicles;
                }

                return [
                    'data' => PublicVehicleResource::collection($vehicles)->resolve(),
                    'meta' => [
                        'total_fleet' => (clone $query)->count(),
                    ],
                ];
            }

            $totalFleet = (clone $query)->count();

            if ($request->filled('limit') && (int) $request->get('limit') > 0) {
                $query->limit((int) $request->get('limit'));
            }

            $vehicles = $query->orderBy('name')->get();

            return [
                'data' => PublicVehicleResource::collection($vehicles)->resolve(),
                'meta' => [
                    'total_fleet' => $totalFleet,
                ],
            ];
        });

        // Instruct browser (10s) and Cloudflare CDN (60s with stale-while-revalidate)
        return response()->json($cachedData)
            ->header('Cache-Control', 'public, max-age=10, s-maxage=60, stale-while-revalidate=300');
    }
}
