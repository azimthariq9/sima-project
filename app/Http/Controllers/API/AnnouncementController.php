<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\createAnnouncementRequest;
use App\Http\Requests\Announcement\updateAnnouncementRequest;
use App\Services\AnnouncementService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    protected $announcementService;
    protected $userService;
    public function __construct(
        UserService $userService,
        AnnouncementService $announcementService,
    ){
        $this->announcementService = $announcementService;
        $this->userService = $userService;
    }

    
     /*
    |--------------------------------------------------------------------------
    |CREATE ANNOUNCEMENT
    |--------------------------------------------------------------------------
    */
    public function store(createAnnouncementRequest $request)
    {
        $data = $request->validated();
        $maker = Auth::user();
        $store=$this->announcementService->create($maker, $data);

        return response()->json(
           ['success' => true, 
           'data'=> $store,
           'flash' => [
                'type' => 'success',
                'message' => 'Announcement created successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],201
        );
    }
    /*
    |--------------------------------------------------------------------------
    |GET ANNOUNCEMENT
    |--------------------------------------------------------------------------
    */
    public function getAnnouncement(){
        $data = $this->announcementService->getAll();
        return response()->json(['success' => true, 
           'data'=> $data,
           'flash' => [
                'type' => 'success',
                'message' => 'Announcement Retrieved successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],200);
    }
    /*
    |--------------------------------------------------------------------------
    |SPECIFIC ANNOUNCEMENT
    |--------------------------------------------------------------------------
    */    
    public function specificAnnouncement($id){
        $data = $this->announcementService->getSpecific($id);
        return response()->json($data);
    }
    /*
    |--------------------------------------------------------------------------
    |UPDATE ANNOUNCEMENT
    |--------------------------------------------------------------------------
    */
    public function updateAnnouncement(updateAnnouncementRequest $request, $id){
        try {
            // Check authentication
            if (!Auth::check()) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }
            
            $maker = Auth::user();
            
            // Check if announcement exists
            $announcement = Announcement::find($id);
            if (!$announcement) {
                return response()->json(['error' => 'Announcement not found'], 404);
            }
            
            // Validate and update
            $data = $request->validated();
            $updated = $this->announcementService->update($maker, $id, $data);
            
            return response()->json(['success' => true, 
           'data'=> $updated,
           'flash' => [
                'type' => 'success',
                'message' => 'Announcement Updated successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],203);


            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 
           'data'=> [],
           'flash' => [
                'type' => 'error',
                'message' => 'Announcement Updated failed',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],500);
        }
    }

    public function destroy($id)
    {   
        try{
            $maker = Auth::user();
            $delete = $this->announcementService->delete($maker, $id);

           return response()->json(['success' => true, 
           'data'=> $delete,
           'flash' => [
                'type' => 'success',
                'message' => 'Announcement deleted successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],200);
        }catch(\Exception $e){
            Log::error('Delete Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 
           'data'=> [],
           'flash' => [
                'type' => 'error',
                'message' => 'Announcement Delete failed',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],500);
        }
        
    }

    public function getAllIds(){
        try{
        $data = $this->userService->getIds();
        return response()->json(['success' => true, 
           'data'=> $data,
           'flash' => [
                'type' => 'success',
                'message' => 'Announcement Ids retrieved successfully',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],200);
        }catch(\Exception $e){
            Log::error('Error getting announcement ids: ' . $e->getMessage());
            return response()->json(['success' => false, 
           'data'=> [],
           'flash' => [
                'type' => 'error',
                'message' => 'get all ids failed',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],500);
        }
    }

}
