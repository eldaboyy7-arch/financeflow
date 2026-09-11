<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicTourPackageResource;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicTourPackageController extends Controller
{
    /**
     * Get all active tour packages for public rental website.
     * Filtered by rental business owner so packages are never duplicated.
     */
    public function index(Request $request): JsonResponse
    {
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

        return response()->json([
            'data' => PublicTourPackageResource::collection($packages),
        ]);
    }
}
