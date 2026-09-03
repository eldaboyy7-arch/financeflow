<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private ExportService $exportService
    ) {}

    public function export(Request $request)
    {
        return $this->exportService->exportTransactionsCsv($request->user()->id, $request->all());
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Transaction::with(['account', 'category'])
            ->where('user_id', $request->user()->id)
            ->whereNull('transfer_id');

        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        }

        if ($request->filled('search')) {
            $query->where('description', 'ilike', '%' . $request->search . '%');
        }

        $sort  = in_array($request->get('sort'), ['date', 'amount', 'created_at']) ? $request->get('sort') : 'date';
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sort, $order)->orderBy('id', 'desc');

        $perPage = min((int) $request->get('per_page', 15), 100);

        return TransactionResource::collection($query->paginate($perPage));
    }

    public function store(StoreTransactionRequest $request): TransactionResource
    {
        $transaction = $this->transactionService->create($request->validated(), $request->user()->id);

        return new TransactionResource($transaction);
    }

    public function show(Request $request, Transaction $transaction): TransactionResource
    {
        $this->authorizeTransaction($transaction, $request);

        return new TransactionResource($transaction->load(['account', 'category']));
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): TransactionResource
    {
        $this->authorizeTransaction($transaction, $request);
        $transaction = $this->transactionService->update($transaction, $request->validated());

        return new TransactionResource($transaction);
    }

    public function destroy(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorizeTransaction($transaction, $request);
        $this->transactionService->delete($transaction);

        return response()->json(['message' => 'Transaksi berhasil dihapus.']);
    }

    /**
     * Securely stream private receipt image after strict server-side ownership authorization.
     */
    public function receipt(Request $request, Transaction $transaction): StreamedResponse|JsonResponse
    {
        $this->authorizeTransaction($transaction, $request);

        $path = $transaction->receipt_path;

        if (! $path) {
            return response()->json(['message' => 'Bukti transaksi tidak ditemukan.'], 404);
        }

        // Anti-Path Traversal & Ownership Isolation Check
        $expectedPrefix = "receipts/{$request->user()->id}/";
        if (! str_starts_with($path, $expectedPrefix) || str_contains($path, '..')) {
            return response()->json(['message' => 'Akses file tidak diizinkan.'], 403);
        }

        if (! Storage::disk('local')->exists($path)) {
            return response()->json(['message' => 'File bukti transaksi tidak ditemukan di server.'], 404);
        }

        return Storage::disk('local')->response($path);
    }

    /**
     * Securely delete receipt attachment from transaction.
     */
    public function deleteReceipt(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorizeTransaction($transaction, $request);

        $path = $transaction->receipt_path;
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        $transaction->update(['receipt_path' => null]);

        return response()->json([
            'message'     => 'Bukti transaksi berhasil dihapus.',
            'transaction' => new TransactionResource($transaction->fresh(['account', 'category'])),
        ]);
    }

    private function authorizeTransaction(Transaction $transaction, Request $request): void
    {
        abort_if($transaction->user_id !== $request->user()->id, 403, 'Unauthorized');
    }
}
