<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReceiptScannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReceiptScannerController extends Controller
{
    public function __construct(
        protected ReceiptScannerService $scannerService
    ) {}

    /**
     * Scan a receipt image and return a transaction draft.
     * Stored privately under receipts/{user_id}/{uuid}.{ext} on local disk.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120', // Max 5MB
            ],
        ], [
            'image.required' => 'Foto struk atau bukti transfer wajib diunggah.',
            'image.image'    => 'File yang diunggah harus berupa gambar.',
            'image.mimes'    => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max'      => 'Ukuran gambar maksimal 5 MB.',
        ]);

        $file = $request->file('image');
        $userId = $request->user()->id;

        // Generate clean, secure UUID filename (never use client-supplied filename)
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $extension = 'jpg';
        }

        $uuid = Str::uuid()->toString();
        $fileName = "{$uuid}.{$extension}";
        $directory = "receipts/{$userId}";
        $relativePath = "{$directory}/{$fileName}";

        // Store file on PRIVATE 'local' disk (storage/app/private/receipts/{user_id}/...)
        $path = $file->storeAs($directory, $fileName, 'local');
        $fullPath = Storage::disk('local')->path($path);
        $mimeType = $file->getMimeType() ?? 'image/jpeg';

        // Scan via AI service using server-side local path
        $draft = $this->scannerService->scan($fullPath, $mimeType, $request->user());

        return response()->json([
            'message'      => 'Struk berhasil dianalisis.',
            'receipt_path' => $relativePath,
            'draft'        => $draft,
        ]);
    }
}
