<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Resources\TransferResource;
use App\Models\Transfer;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransferController extends Controller
{
    public function __construct(private TransferService $transferService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $transfers = Transfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15);

        return TransferResource::collection($transfers);
    }

    public function store(StoreTransferRequest $request): TransferResource
    {
        $transfer = $this->transferService->create($request->validated(), $request->user()->id);

        return new TransferResource($transfer);
    }

    public function show(Request $request, Transfer $transfer): TransferResource
    {
        abort_if($transfer->user_id !== $request->user()->id, 403);

        return new TransferResource($transfer->load(['fromAccount', 'toAccount']));
    }

    public function update(StoreTransferRequest $request, Transfer $transfer): TransferResource
    {
        abort_if($transfer->user_id !== $request->user()->id, 403);
        $transfer = $this->transferService->update($transfer, $request->validated());

        return new TransferResource($transfer);
    }

    public function destroy(Request $request, Transfer $transfer): JsonResponse
    {
        abort_if($transfer->user_id !== $request->user()->id, 403);
        $this->transferService->delete($transfer);

        return response()->json(['message' => 'Transfer berhasil dihapus.']);
    }
}
