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

        $vehicles = $query->orderBy('name')->get();

        return PublicVehicleResource::collection($vehicles);
    }
}
