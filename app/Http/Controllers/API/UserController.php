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

use function Flasher\Prime\flash;

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
        // flash()->use('theme.amazon')
        // ->option('timeout',5000)
        // ->success('User has been created succesfully');
        return response()->json([
            'success' => true,
            'data' => $user,
            'flash' => [
                'type' => 'success',
                'message' => 'User created successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ,201
        ]);
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
            'data' => $updated,
            'flash' => [
                'type' => 'success',
                'message' => 'User updated successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ,201
        ]);
            
        } catch (\Exception $e) {
            Log::error('Update user failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
            'success' => true,
            'data' => $e->getMessage(),
            'flash' => [
                'type' => 'error',
                'message' => 'User update failed',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ,500
        ]);
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
            'data' => [],
            'flash' => [
                'type' => 'success',
                'message' => 'User deleted successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ,200
        ]);
    }



        /*
    |--------------------------------------------------------------------------
    | GET USERS
    |--------------------------------------------------------------------------
    */
    public function getUsers(Request $request)
    {
        $query = User::query();
        
        // Fitur Search berdasarkan email
        if ($request->has('email') && !empty($request->email)) {
            $query->where('email', 'LIKE', "%{$request->email}%");
        }
        
        // Fitur Sorting
        if ($request->has('sort') && !empty($request->sort)) {
            if ($request->sort === 'email_asc') {
                $query->orderBy('email', 'asc');
            } elseif ($request->sort === 'email_desc') {
                $query->orderBy('email', 'desc');
            } elseif ($request->sort === 'role_asc') {
                $query->orderBy('role', 'asc');
            } elseif ($request->sort === 'role_desc') {
                $query->orderBy('role', 'desc');
            } elseif ($request->sort === 'status_asc') {
                $query->orderBy('status', 'asc');
            } elseif ($request->sort === 'status_desc') {
                $query->orderBy('status', 'desc');
            } elseif ($request->sort === 'id_asc') {
                $query->orderBy('id', 'asc');
            } elseif ($request->sort === 'id_desc') {
                $query->orderBy('id', 'desc');
            }
        } else {
            // Default sorting
            $query->orderBy('id', 'desc');
        }
        
        $users = $query->get();
        // flash()->use('theme.amazon')
        // ->option('timeout',5000)
        // ->success('User Retrieve');
        return response()->json([
            'success' => true,
            'data' => $users,
            'flash' => [
                'type' => 'success',
                'message' => 'Users retrieved successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ,200
        ]);
    }

    public function showUser($id){

        $user = User::where('id','LIKE',$id)->with(['dosen','mahasiswa'])->get();
    
        return response()->json($user);
    }
}
