<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

    class UserController extends Controller
    {
        public function index(Request $request): JsonResponse
        {
            $users = User::with('role')
                ->where('company_id', $request->user()->company_id)
                ->paginate();

            return response()->json($users);
        }

        public function store(StoreUserRequest $request): JsonResponse
        {
            $authUser = $request->user();

            $user = User::create([
                ...$request->validated(),
                'company_id' => $authUser->company_id,
            ]);

            return response()->json($user->load('role'), 201);
        }

        public function show(Request $request, int $user): JsonResponse
        {
            $found = User::with('role')
                ->where('company_id', $request->user()->company_id)
                ->findOrFail($user);

            return response()->json($found);
        }

        public function update(UpdateUserRequest $request, int $user): JsonResponse
        {
            $found = User::where('company_id', $request->user()->company_id)->findOrFail($user);
            $found->update(array_filter($request->validated(), fn($value) => $value !== null));

            return response()->json($found->fresh()->load('role'));
        }

        public function destroy(Request $request, int $user): JsonResponse
        {
            $found = User::where('company_id', $request->user()->company_id)->findOrFail($user);

            if ($found->id === $request->user()->id) {
                return response()->json(['message' => 'Você não pode excluir seu próprio usuário.'], 422);
            }

            $found->delete();

            return response()->json(['message' => 'Usuário excluído com sucesso.']);
        }
    }
