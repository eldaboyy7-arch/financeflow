<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->notificationService->getNotificationsForUser($request->user()->id);
        return response()->json($data);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $this->notificationService->markAsRead($request->user()->id, $id);
        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->notificationService->markAllAsRead($request->user()->id);
        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
