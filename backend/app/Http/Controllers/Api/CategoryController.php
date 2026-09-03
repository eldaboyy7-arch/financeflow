<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::forUser($request->user()->id)
            ->orderBy('type')
            ->orderBy('name');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return CategoryResource::collection($query->get());
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $category = Category::create(
            array_merge($request->validated(), ['user_id' => $request->user()->id])
        );

        return new CategoryResource($category);
    }

    public function update(StoreCategoryRequest $request, Category $category): CategoryResource
    {
        $this->authorizeCategory($category, $request);
        $category->update($request->validated());

        return new CategoryResource($category->fresh());
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $this->authorizeCategory($category, $request);

        if ($category->transactions()->count() > 0) {
            return response()->json(['message' => 'Kategori memiliki transaksi dan tidak dapat dihapus.'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }

    private function authorizeCategory(Category $category, Request $request): void
    {
        abort_if($category->user_id !== $request->user()->id, 403, 'Akses tidak diizinkan.');
    }
}
