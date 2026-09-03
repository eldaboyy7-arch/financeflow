<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'plate_number'=> 'nullable|string|max:20',
            'brand'       => 'nullable|string|max:100',
            'model_year'  => 'nullable|string|max:10',
            'status'      => 'nullable|in:available,rented,maintenance',
            'daily_rate'  => 'nullable|numeric|min:0',
            'color'       => 'nullable|string|max:7',
            'notes'       => 'nullable|string',
        ]);

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
     * Update a vehicle.
     */
    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($vehicle);

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'plate_number'=> 'nullable|string|max:20',
            'brand'       => 'nullable|string|max:100',
            'model_year'  => 'nullable|string|max:10',
            'status'      => 'nullable|in:available,rented,maintenance',
            'daily_rate'  => 'nullable|numeric|min:0',
            'color'       => 'nullable|string|max:7',
            'notes'       => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return response()->json([
            'message' => 'Kendaraan berhasil diperbarui.',
            'data'    => $this->formatVehicle($vehicle->fresh(), now()->month, now()->year),
        ]);
    }

    /**
     * Delete a vehicle.
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $this->authorizeVehicle($vehicle);

        // Detach transactions (set vehicle_id null) instead of blocking delete
        $vehicle->transactions()->update(['vehicle_id' => null]);
        $vehicle->delete();

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

                // Recent transactions for this vehicle in this period
                $transactions = $v->transactions()
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->orderByDesc('date')
                    ->limit(10)
                    ->get(['id', 'type', 'amount', 'description', 'date']);

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

        return response()->json([
            'data' => [
                'month'         => $month,
                'year'          => $year,
                'vehicles'      => $vehicles,
                'total_income'  => $totalIncome,
                'total_expense' => $totalExpense,
                'total_profit'  => $totalIncome - $totalExpense,
            ],
        ]);
    }

    // ── Private helpers ────────────────────────────────────────────────

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
            'id'           => $vehicle->id,
            'name'         => $vehicle->name,
            'plate_number' => $vehicle->plate_number,
            'brand'        => $vehicle->brand,
            'model_year'   => $vehicle->model_year,
            'status'       => $vehicle->status,
            'daily_rate'   => (float) $vehicle->daily_rate,
            'color'        => $vehicle->color,
            'notes'        => $vehicle->notes,
            'created_at'   => $vehicle->created_at,
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
