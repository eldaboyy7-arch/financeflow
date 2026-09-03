<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = Account::where('user_id', $request->user()->id)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return AccountResource::collection($accounts);
    }

    public function store(StoreAccountRequest $request): AccountResource
    {
        $data = $request->validated();
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['current_balance'] = $data['opening_balance'];

        $account = Account::create(array_merge($data, ['user_id' => $request->user()->id]));

        return new AccountResource($account);
    }

    public function show(Request $request, Account $account): AccountResource
    {
        $this->authorizeAccount($account, $request);

        return new AccountResource($account);
    }

    public function update(StoreAccountRequest $request, Account $account): AccountResource
    {
        $this->authorizeAccount($account, $request);

        $oldOpeningBalance = $account->opening_balance;
        $account->update($request->validated());

        // Recalculate balance if opening balance changed
        if (isset($request->validated()['opening_balance']) && $request->validated()['opening_balance'] != $oldOpeningBalance) {
            $account->recalculateBalance();
        }

        return new AccountResource($account->fresh());
    }

    public function destroy(Request $request, Account $account): JsonResponse
    {
        $this->authorizeAccount($account, $request);

        if ($account->transactions()->count() > 0) {
            return response()->json(['message' => 'Akun memiliki transaksi dan tidak dapat dihapus.'], 422);
        }

        $account->delete();

        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }

    private function authorizeAccount(Account $account, Request $request): void
    {
        abort_if($account->user_id !== $request->user()->id, 403, 'Akses tidak diizinkan.');
    }
}
