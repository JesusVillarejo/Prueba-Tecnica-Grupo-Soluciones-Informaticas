<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function list(Request $request)
    {
        $users = User::latest()->get();
        return UserResource::collection($users);
    }

    public function store(UserStoreRequest $request, UserService $service)
    {
        $service->create($request->validated());
        return response()->json(['success' => true, 'message' => 'Usuario creado correctamente']);
    }

    public function update(UserUpdateRequest $request, UserService $service, User $user)
    {
        $service->update($user, $request->validated());
        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente']);
    }

    public function destroy(UserService $service, User $user)
    {
        $service->delete($user);
        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    }
}

