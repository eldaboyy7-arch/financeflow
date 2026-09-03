<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAdvisorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAdvisorController extends Controller
{
    public function __construct(private AiAdvisorService $advisorService)
    {
    }

    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $reply = $this->advisorService->askAdvisor(
            $request->user()->id,
            $validated['message'],
            $validated['history'] ?? []
        );

        return response()->json([
            'reply' => $reply,
        ]);
    }
}
