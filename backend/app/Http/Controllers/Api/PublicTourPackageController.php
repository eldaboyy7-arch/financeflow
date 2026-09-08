<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicTourPackageResource;
use App\Models\TourPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicTourPackageController extends Controller
{
    /**
     * Get all active tour packages for public rental website.
     * Public read-only endpoint, rate limited, order by sort_order.
     */
    public function index(Request $request): JsonResponse
    {
        $packages = TourPackage::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'data' => PublicTourPackageResource::collection($packages),
        ]);
    }
}
