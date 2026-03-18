<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = Department::where('company_id', $request->user()->company_id)
            ->paginate();

        return response()->json($items);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $item = Department::create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json($item, 201);
    }

    public function show(Request $request, int $department): JsonResponse
    {
        $item = Department::where('company_id', $request->user()->company_id)
            ->findOrFail($department);

        return response()->json($item);
    }

    public function update(StoreDepartmentRequest $request, int $department): JsonResponse
    {
        $item = Department::where('company_id', $request->user()->company_id)
            ->findOrFail($department);

        $item->update($request->validated());

        return response()->json($item);
    }

    public function destroy(Request $request, int $department): JsonResponse
    {
        $item = Department::where('company_id', $request->user()->company_id)
            ->findOrFail($department);

        $item->delete();

        return response()->json([
            'message' => 'Departamento excluído com sucesso.',
        ]);
    }
}