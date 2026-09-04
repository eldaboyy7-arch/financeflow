<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * List all vehicles for the authenticated user,
     * with current month summary (income, expense, profit).
     */
    public function index(Request $request): JsonResponse
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $vehicles = Vehicle::where('user_id', Auth::id())
            ->orderBy('name')
            ->get()
            ->map(fn(Vehicle $v) => $this->formatVehicle($v, $month, $year));

        return response()->json(['data' => $vehicles]);
    }

    /**
     * Store a new vehicle.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateVehicleData($request);

        $vehicle = Vehicle::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status'  => $validated['status'] ?? 'available',
            'color'   => $validated['color'] ?? '#3B82F6',
        ]);

        return response()->json([
            'message' => 'Kendaraan berhasil ditambahkan.',
            'data'    => $this->formatVehicle($vehicle, now()->month, now()->year),
        ], 201);
    }

    /**
     * Show a single vehicle with period summary.
     */
    public function show(Request $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($vehicle);

        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        return response()->json(['data' => $this->formatVehicle($vehicle, $month, $year)]);
    }

    /**
     * Update a vehicle with safe distributed photo replacement.
     */
    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($vehicle);

        $validated = $this->validateVehicleData($request, $vehicle);

        $oldPhotoPath = null;
        $shouldDeleteOldPhoto = false;

        // DB Transaction ensures local database consistency
        DB::transaction(function () use ($vehicle, $validated, &$oldPhotoPath, &$shouldDeleteOldPhoto) {
            $locked = Vehicle::where('id', $vehicle->id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            $oldPhotoPath = $locked->photo_path;

            $locked->update($validated);

            // Flag old photo for deletion if path changed and old path exists
            if (array_key_exists('photo_path', $validated) && $validated['photo_path'] !== $oldPhotoPath && !empty($oldPhotoPath)) {
                $shouldDeleteOldPhoto = true;
            }
        });

        // Outside DB transaction: Attempt cleanup of old photo in Supabase Storage
        if ($shouldDeleteOldPhoto && $oldPhotoPath) {
            app(\App\Services\SupabaseStorageService::class)->deleteFile('fleet', $oldPhotoPath);
        }

        return response()->json([
            'message' => 'Kendaraan berhasil diperbarui.',
            'data'    => $this->formatVehicle($vehicle->fresh(), now()->month, now()->year),
        ]);
    }

    /**
     * Delete a vehicle and its associated storage photo.
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($vehicle);

        $photoPath = $vehicle->photo_path;

        // Detach transactions instead of blocking delete
        $vehicle->transactions()->update(['vehicle_id' => null]);
        $vehicle->delete();

        // Cleanup storage file outside DB
        if (!empty($photoPath)) {
            app(\App\Services\SupabaseStorageService::class)->deleteFile('fleet', $photoPath);
        }

        return response()->json(['message' => 'Kendaraan berhasil dihapus.']);
    }

    /**
     * Monthly rental report: income/expense/profit per vehicle.
     */
    public function report(Request $request): JsonResponse
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $vehicles = Vehicle::where('user_id', Auth::id())
            ->orderBy('name')
            ->get()
            ->map(function (Vehicle $v) use ($month, $year) {
                $income  = $v->incomeForPeriod($month, $year);
                $expense = $v->expenseForPeriod($month, $year);

                // Recent transactions for this vehicle in this period with category
                $transactions = $v->transactions()
                    ->with('category:id,name,icon,color')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->orderByDesc('date')
                    ->orderByDesc('id')
                    ->limit(20)
                    ->get(['id', 'type', 'amount', 'description', 'date', 'category_id'])
                    ->map(fn($t) => [
                        'id'          => $t->id,
                        'type'        => $t->type?->value ?? $t->type,
                        'amount'      => (float) $t->amount,
                        'description' => $t->description,
                        'date'        => $t->date ? \Carbon\Carbon::parse($t->date)->format('d M Y') : '',
                        'category'    => $t->category,
                    ]);

                return [
                    'id'           => $v->id,
                    'name'         => $v->name,
                    'plate_number' => $v->plate_number,
                    'status'       => $v->status,
                    'color'        => $v->color,
                    'income'       => $income,
                    'expense'      => $expense,
                    'profit'       => $income - $expense,
                    'transactions' => $transactions,
                ];
            });

        $totalIncome  = $vehicles->sum('income');
        $totalExpense = $vehicles->sum('expense');

        // Breakdown per category for rental transactions this month
        $incomeBreakdown = Transaction::with('category')
            ->where('user_id', Auth::id())
            ->whereNotNull('vehicle_id')
            ->where('type', 'income')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(id) as count'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn($t) => [
                'category_id' => $t->category_id,
                'category'    => $t->category?->name ?? 'Lainnya',
                'icon'        => $t->category?->icon ?? '💰',
                'color'       => $t->category?->color ?? '#10B981',
                'total'       => (float) $t->total,
                'count'       => (int) $t->count,
            ])->values();

        $expenseBreakdown = Transaction::with('category')
            ->where('user_id', Auth::id())
            ->whereNotNull('vehicle_id')
            ->where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(id) as count'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn($t) => [
                'category_id' => $t->category_id,
                'category'    => $t->category?->name ?? 'Lainnya',
                'icon'        => $t->category?->icon ?? '⛽',
                'color'       => $t->category?->color ?? '#EF4444',
                'total'       => (float) $t->total,
                'count'       => (int) $t->count,
            ])->values();

        return response()->json([
            'data' => [
                'month'             => $month,
                'year'              => $year,
                'vehicles'          => $vehicles,
                'total_income'      => $totalIncome,
                'total_expense'     => $totalExpense,
                'total_profit'      => $totalIncome - $totalExpense,
                'income_breakdown'  => $incomeBreakdown,
                'expense_breakdown' => $expenseBreakdown,
            ],
        ]);
    }

    // ── Private helpers ────────────────────────────────────────────────

    private function validateVehicleData(Request $request, ?Vehicle $vehicle = null): array
    {
        $validated = $request->validate([
            'name'         => ($vehicle ? 'sometimes|' : '') . 'required|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'brand'        => 'nullable|string|max:100',
            'model_year'   => 'nullable|string|max:10',
            'status'       => 'nullable|in:available,rented,maintenance',
            'daily_rate'   => 'nullable|numeric|min:0',
            'color'        => 'nullable|string|max:7',
            'notes'        => 'nullable|string',
            'photo_path'   => 'nullable|string|max:500',
            'video_url'    => 'nullable|string|max:500',
            'video_path'   => 'nullable|string|max:500',
            'transmission' => 'nullable|in:matic,manual',
            'capacity'     => 'nullable|integer|min:1|max:100',
            'fuel_type'    => 'nullable|in:bensin,diesel',
            'description'  => 'nullable|string|max:2000',
        ]);

        // 1. Strict Tenant-Aware Photo Path Validation
        if (!empty($validated['photo_path'])) {
            $path = trim($validated['photo_path']);

            // Reject directory traversal
            if (str_contains($path, '..') || str_starts_with($path, '\\')) {
                abort(422, 'Format photo_path tidak valid atau terdeteksi directory traversal.');
            }

            $isUnchangedExisting = $vehicle && ($path === trim($vehicle->photo_path ?? ''));
            $isFullUrl = str_starts_with($path, 'http://') || str_starts_with($path, 'https://');

            // If photo is unchanged or is an existing valid storage URL, allow it
            if ($isUnchangedExisting) {
                // Keep unchanged photo path
            } elseif ($isFullUrl) {
                // Allow valid URLs from storage or server
                if (filter_var($path, FILTER_VALIDATE_URL) === false) {
                    abort(422, 'URL photo_path tidak valid.');
                }
            } else {
                // Reject leading slash for relative storage keys
                if (str_starts_with($path, '/')) {
                    abort(422, 'Format photo_path tidak valid atau terdeteksi directory traversal.');
                }

                // Must match Supabase object key format: {user_id}/{vehicle_id_or_new}/{filename}.{ext}
                if (!preg_match('/^(\d+)\/([a-zA-Z0-9_\-]+)\/[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|webp|avif)$/i', $path, $matches)) {
                    abort(422, 'Format photo_path harus sesuai pola: {user_id}/{vehicle_id}/{filename}.ext');
                }

                $pathUserId = (int) $matches[1];
                $pathVehicleId = $matches[2];

                // Tenant isolation: folder pertama WAJIB sama dengan Auth::id()
                if ($pathUserId !== (int) Auth::id()) {
                    abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk menggunakan path foto milik user lain.');
                }

                // Pada update kendaraan, vehicle_id pada path tidak boleh milik kendaraan lain
                if ($vehicle && is_numeric($pathVehicleId) && (int) $pathVehicleId !== (int) $vehicle->id) {
                    abort(403, 'Akses ditolak: photo_path tidak sesuai dengan ID kendaraan ini.');
                }
            }
        }

        // 2. Strict Video URL Validation
        if (!empty($validated['video_url'])) {
            $videoService = app(\App\Services\VideoEmbedService::class);
            if (!$videoService->isValidPlatformUrl($validated['video_url'])) {
                abort(422, 'URL video harus berasal dari platform yang diizinkan (YouTube, TikTok, atau Instagram).');
            }
        }

        return $validated;
    }

    private function authorizeVehicle(Vehicle $vehicle): void
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    private function formatVehicle(Vehicle $vehicle, int $month, int $year): array
    {
        $income  = $vehicle->incomeForPeriod($month, $year);
        $expense = $vehicle->expenseForPeriod($month, $year);

        return [
            'id'                   => $vehicle->id,
            'name'                 => $vehicle->name,
            'plate_number'         => $vehicle->plate_number,
            'brand'                => $vehicle->brand,
            'model_year'           => $vehicle->model_year,
            'status'               => $vehicle->status,
            'daily_rate'           => (float) $vehicle->daily_rate,
            'color'                => $vehicle->color,
            'notes'                => $vehicle->notes,
            'photo_path'           => $vehicle->photo_path,
            'photo_url'            => $vehicle->photo_url,
            'video_url'            => $vehicle->video_url,
            'video_path'           => $vehicle->video_path,
            'safe_video_embed_url' => $vehicle->safe_video_embed_url,
            'transmission'         => $vehicle->transmission ?? 'matic',
            'capacity'             => (int) ($vehicle->capacity ?? 7),
            'fuel_type'            => $vehicle->fuel_type ?? 'bensin',
            'description'          => $vehicle->description,
            'created_at'           => $vehicle->created_at,
            'summary' => [
                'income'  => $income,
                'expense' => $expense,
                'profit'  => $income - $expense,
                'month'   => $month,
                'year'    => $year,
            ],
        ];
    }
}
