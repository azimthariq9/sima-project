<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\createUserRequest;
use App\Http\Requests\User\updateUserRequest;
use Illuminate\Auth\Middleware\Authenticate;


class UserController extends Controller
{
   protected UserService $userService;

    public function __construct(UserService $userService)
    {
        // $this->middleware('auth');
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAll();
    
        return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
        ], 200);
    }

    public function store(createUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    public function update(updateUserRequest $request, User $user)
    {
        $updated = $this->userService->update($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $updated
        ], 200);
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }
}
