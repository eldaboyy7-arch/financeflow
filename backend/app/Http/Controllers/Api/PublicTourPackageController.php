<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicTourPackageResource;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicTourPackageController extends Controller
{
    /**
     * Get all active tour packages for public rental website (cached in memory & Cloudflare CDN).
     * Filtered by rental business owner so packages are never duplicated.
     */
    public function index(Request $request): JsonResponse
    {
        $version = (int) Cache::get('public_tour_version', 1);
        $cacheKey = "public_tour_v{$version}_" . md5(json_encode($request->all()));

        $cachedData = Cache::remember($cacheKey, 1800, function () {
            // Determine the rental business owner ID
            $ownerId = (int) env('RENTAL_OWNER_ID', config('app.rental_owner_id', 0));

            if ($ownerId <= 0) {
                // Auto-detect owner from existing tour packages (prioritizing the client)
                $ownerId = (int) TourPackage::where('user_id', 18)->value('user_id')
                    ?: (int) TourPackage::whereNotNull('user_id')->value('user_id');
            }

            if ($ownerId <= 0) {
                $ownerId = (int) Vehicle::whereNotNull('user_id')->value('user_id');
            }

            if ($ownerId <= 0) {
                $ownerId = (int) User::orderBy('id')->value('id');
            }

            $query = TourPackage::where('is_active', true);

            if ($ownerId > 0) {
                $query->where('user_id', $ownerId);
            }

            $packages = $query
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            return [
                'data' => PublicTourPackageResource::collection($packages)->resolve(),
            ];
        });

        // Instruct browser and Cloudflare CDN to cache response (max-age 5m, CDN s-maxage 30m)
        return response()->json($cachedData)
            ->header('Cache-Control', 'public, max-age=300, s-maxage=1800');
    }
}
