<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\createAnnouncementRequest;
use App\Http\Requests\Announcement\updateAnnouncementRequest;
use App\Services\AnnouncementService;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    protected AnnouncementService $announcementService;
    public function __construct(AnnouncementService $announcementService)
    {
        // $this->middleware('auth');
        $this->announcementService = $announcementService;
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
            ],201);


            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 
           'data'=> [],
           'flash' => [
                'type' => 'success',
                'message' => 'Announcement Updated failed',
                'theme' => 'amazon',
                'timeout' => 5000
            ]
            ],200);
        }
    }

}
