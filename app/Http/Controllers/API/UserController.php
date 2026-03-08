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
use Illuminate\Support\Facades\Log;



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
    public function update(updateUserRequest $request, User $user)
    {   
            try {
            $maker = Auth::user();
            
            Log::info('Updating user', [
                'user_id' => $user->id,
                'maker_id' => $maker->id,
                'data' => $request->validated()
            ]);
            
            $updated = $this->userService->update($maker, $user->id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $updated
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Update user failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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

    public function showUser($id){

        $user = User::where('id','LIKE',$id)->with(['dosen','mahasiswa'])->get();
    
        return response()->json($user);
    }
}
