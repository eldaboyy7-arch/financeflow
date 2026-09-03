<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GoalController extends Controller
{
    public function __construct(private GoalService $goalService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $goals = $this->goalService->getGoalsForUser($request->user()->id);
        return response()->json(['data' => $goals]);
    }

    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:1000',
            'target_date'   => 'nullable|date',
            'icon'          => 'nullable|string|max:10',
            'color'         => 'nullable|string|max:7',
            'account_id'    => [
                'nullable',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
        ]);

        $goal = $this->goalService->create($validated, $userId);
        return response()->json(['data' => $goal], 201);
    }

    public function update(Request $request, Goal $goal): JsonResponse
    {
        $this->authorizeGoal($goal, $request);
        $userId = $request->user()->id;

        $validated = $request->validate([
            'name'          => 'sometimes|required|string|max:100',
            'target_amount' => 'sometimes|required|numeric|min:1000',
            'target_date'   => 'nullable|date',
            'icon'          => 'nullable|string|max:10',
            'color'         => 'nullable|string|max:7',
            'account_id'    => [
                'nullable',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'is_completed'  => 'sometimes|boolean',
        ]);

        $updated = $this->goalService->update($goal, $validated);
        return response()->json(['data' => $updated]);
    }

    public function destroy(Request $request, Goal $goal): JsonResponse
    {
        $this->authorizeGoal($goal, $request);
        $this->goalService->delete($goal);
        return response()->json(['message' => 'Target menabung berhasil dihapus.']);
    }

    public function contribute(Request $request, Goal $goal): JsonResponse
    {
        $this->authorizeGoal($goal, $request);
        $userId = $request->user()->id;

        $validated = $request->validate([
            'amount'     => 'required|numeric|min:1000',
            'type'       => 'required|in:deposit,withdraw',
            'account_id' => [
                'nullable',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'date'       => 'nullable|date',
            'notes'      => 'nullable|string|max:255',
        ]);

        $contribution = $this->goalService->contribute($goal, $validated, $userId);
        return response()->json([
            'message' => $validated['type'] === 'deposit' ? 'Berhasil menambah tabungan!' : 'Berhasil menarik dana tabungan.',
            'data'    => $contribution,
            'goal'    => $goal->fresh(['account']),
        ]);
    }

    private function authorizeGoal(Goal $goal, Request $request): void
    {
        abort_if($goal->user_id !== $request->user()->id, 403, 'Unauthorized');
    }
}
