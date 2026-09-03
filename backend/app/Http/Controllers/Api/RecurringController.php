<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecurringTransaction;
use App\Services\RecurringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecurringController extends Controller
{
    public function __construct(private RecurringService $recurringService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->recurringService->getRecurringForUser($request->user()->id);
        return response()->json(['data' => $items]);
    }

    public function upcoming(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $bills = $this->recurringService->getUpcomingBills($request->user()->id, $days);
        return response()->json(['data' => $bills]);
    }

    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'amount'      => 'required|numeric|min:100',
            'type'        => 'required|in:income,expense',
            'frequency'   => 'required|in:daily,weekly,monthly,yearly',
            'account_id'  => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
            'start_date'  => 'required|date',
            'auto_create' => 'nullable|boolean',
            'notes'       => 'nullable|string|max:255',
        ]);

        $rec = $this->recurringService->create($validated, $userId);
        return response()->json(['data' => $rec->load(['account', 'category'])], 201);
    }

    public function update(Request $request, RecurringTransaction $recurring): JsonResponse
    {
        $this->authorizeRecurring($recurring, $request);
        $userId = $request->user()->id;

        $validated = $request->validate([
            'name'          => 'sometimes|required|string|max:100',
            'amount'        => 'sometimes|required|numeric|min:100',
            'type'          => 'sometimes|required|in:income,expense',
            'frequency'     => 'sometimes|required|in:daily,weekly,monthly,yearly',
            'account_id'    => [
                'sometimes',
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'category_id'   => [
                'sometimes',
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
            'next_due_date' => 'sometimes|required|date',
            'is_active'     => 'sometimes|boolean',
            'auto_create'   => 'nullable|boolean',
            'notes'         => 'nullable|string|max:255',
        ]);

        $updated = $this->recurringService->update($recurring, $validated);
        return response()->json(['data' => $updated]);
    }

    public function destroy(Request $request, RecurringTransaction $recurring): JsonResponse
    {
        $this->authorizeRecurring($recurring, $request);
        $this->recurringService->delete($recurring);
        return response()->json(['message' => 'Tagihan rutin berhasil dihapus.']);
    }

    public function pay(Request $request, RecurringTransaction $recurring): JsonResponse
    {
        $this->authorizeRecurring($recurring, $request);
        $transaction = $this->recurringService->pay($recurring, $request->user()->id);

        return response()->json([
            'message'     => 'Tagihan berhasil dibayar & dicatat ke transaksi!',
            'transaction' => $transaction,
            'recurring'   => $recurring->fresh(['account', 'category']),
        ]);
    }

    private function authorizeRecurring(RecurringTransaction $rec, Request $request): void
    {
        abort_if($rec->user_id !== $request->user()->id, 403, 'Unauthorized');
    }
}
