<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourPackageRequest;
use App\Http\Requests\UpdateTourPackageRequest;
use App\Http\Resources\TourPackageResource;
use App\Models\TourPackage;
use App\Services\SupabaseStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TourPackageController extends Controller
{
    public function __construct(private SupabaseStorageService $storageService)
    {
    }

    /**
     * Display a listing of tour packages for admin.
     */
    public function index(Request $request): JsonResponse
    {
        $packages = TourPackage::with('vehicle')
            ->where('user_id', Auth::id())
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'data' => TourPackageResource::collection($packages),
        ]);
    }

    /**
     * Store a newly created tour package.
     */
    public function store(StoreTourPackageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (TourPackage::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $package = TourPackage::create($validated);

        return response()->json([
            'message' => 'Paket tour berhasil ditambahkan.',
            'data'    => new TourPackageResource($package),
        ], 201);
    }

    /**
     * Display the specified tour package.
     */
    public function show(TourPackage $tourPackage): JsonResponse
    {
        $this->authorizePackage($tourPackage);

        return response()->json([
            'data' => new TourPackageResource($tourPackage->load('vehicle')),
        ]);
    }

    /**
     * Update the specified tour package.
     */
    public function update(UpdateTourPackageRequest $request, TourPackage $tourPackage): JsonResponse
    {
        $this->authorizePackage($tourPackage);

        $validated = $request->validated();

        if (empty($validated['slug']) && isset($validated['title']) && $validated['title'] !== $tourPackage->title) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (TourPackage::where('slug', $slug)->where('id', '!=', $tourPackage->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $tourPackage->update($validated);

        return response()->json([
            'message' => 'Paket tour berhasil diperbarui.',
            'data'    => new TourPackageResource($tourPackage->fresh()),
        ]);
    }

    /**
     * Remove the specified tour package.
     */
    public function destroy(TourPackage $tourPackage): JsonResponse
    {
        $this->authorizePackage($tourPackage);

        $tourPackage->delete();

        return response()->json([
            'message' => 'Paket tour berhasil dihapus.',
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(TourPackage $tourPackage): JsonResponse
    {
        $this->authorizePackage($tourPackage);

        $tourPackage->is_active = !$tourPackage->is_active;
        $tourPackage->save();

        return response()->json([
            'message' => 'Status paket tour berhasil diubah.',
            'data'    => new TourPackageResource($tourPackage),
        ]);
    }

    /**
     * Upload photo for cover or gallery to Supabase storage.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
        ]);

        $file = $request->file('photo');
        $userId = Auth::id();
        $filename = 'tp_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $storagePath = "{$userId}/tour_packages/{$filename}";

        $uploadedPath = $this->storageService->uploadFile($file, $storagePath);

        if (!$uploadedPath) {
            return response()->json(['message' => 'Gagal mengunggah foto ke storage.'], 500);
        }

        return response()->json([
            'message'    => 'Foto berhasil diunggah.',
            'photo_path' => $uploadedPath,
            'photo_url'  => $this->storageService->getPublicUrl($uploadedPath),
        ]);
    }

    private function authorizePackage(TourPackage $tourPackage): void
    {
        abort_if(
            $tourPackage->user_id && $tourPackage->user_id !== Auth::id(),
            403,
            'Akses ditolak: Anda tidak memiliki akses ke paket tour ini.'
        );
    }
}
