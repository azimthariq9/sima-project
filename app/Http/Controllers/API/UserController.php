<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\jurusan;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\createUserRequest;
use App\Http\Requests\User\updateUserRequest;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
   protected UserService $userService;

    public function __construct(UserService $userService)
    {
        // $this->middleware('auth');
        $this->userService = $userService;
    }
    /*
    |--------------------------------------------------------------------------
    |INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $users = $this->userService->getAll();
    
        return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE USER
    |--------------------------------------------------------------------------
    */
    public function store(createUserRequest $request)
    {
        $maker = Auth::User();
        $user = $this->userService->create($maker, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }
     /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */
    public function update(updateUserRequest $request, $id)
    {   
        $maker = Auth::User();
        $updated = $this->userService->update($maker, $id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $updated
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */
    public function destroy($user)
    {
        $maker = Auth::user();
        $this->userService->delete($maker,$user);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }



        /*
    |--------------------------------------------------------------------------
    | GET USERS
    |--------------------------------------------------------------------------
    */
    public function getUsers(Request $request){
        $email = $request->query('email'); // ambil dari query string
        
        if ($email) {
            // Gunakan where seperti ini, bukan where('email'==$email)
            $users = User::where('email', 'LIKE', "%{$email}%")->get();
        } else {
            $users = User::all();
        }
        
        return response()->json($users, 200);
    }
}
