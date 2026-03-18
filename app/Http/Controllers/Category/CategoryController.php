<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = Category::where('company_id', $request->user()->company_id)
            ->paginate();

        return response()->json($items);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $item = Category::create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json($item, 201);
    }

    public function show(Request $request, int $category): JsonResponse
    {
        $item = Category::where('company_id', $request->user()->company_id)
            ->findOrFail($category);

        return response()->json($item);
    }

    public function update(StoreCategoryRequest $request, int $category): JsonResponse
    {
        $item = Category::where('company_id', $request->user()->company_id)
            ->findOrFail($category);

        $item->update($request->validated());

        return response()->json($item);
    }

    public function destroy(Request $request, int $category): JsonResponse
    {
        $item = Category::where('company_id', $request->user()->company_id)
            ->findOrFail($category);

        $item->delete();

        return response()->json([
            'message' => 'Categoria excluída com sucesso.',
        ]);
    }
}