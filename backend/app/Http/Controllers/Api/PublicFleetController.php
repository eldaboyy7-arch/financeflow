<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicVehicleResource;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicFleetController extends Controller
{
    /**
     * Return public fleet catalog (Read-only, no authentication required).
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        // Determine the rental business owner ID
        $ownerId = (int) env('RENTAL_OWNER_ID', 0);

        if ($ownerId <= 0) {
            $ownerId = (int) User::orderBy('id')->value('id');
        }

        if ($ownerId <= 0) {
            return response()->json([
                'success' => true,
                'data'    => [],
            ]);
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

            return PublicVehicleResource::collection($vehicles)->additional([
                'meta' => [
                    'total_fleet' => (clone $query)->count(),
                ],
            ]);
        }

        $totalFleet = (clone $query)->count();

        if ($request->filled('limit') && (int) $request->get('limit') > 0) {
            $query->limit((int) $request->get('limit'));
        }

        $vehicles = $query->orderBy('name')->get();

        return PublicVehicleResource::collection($vehicles)->additional([
            'meta' => [
                'total_fleet' => $totalFleet,
            ],
        ]);
    }
}
