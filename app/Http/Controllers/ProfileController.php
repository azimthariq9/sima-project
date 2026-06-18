<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\RoleServices;
class ProfileController extends Controller
{

    public function __construct()
    {
        $userRole = Auth::user()->role;
        $roleServices = new RoleServices($userRole);
        $userRole = $roleServices->trimRole($userRole);
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $userRole = $request->user()->role;
        $roleServices = new RoleServices();
        $userRole = $roleServices->trimRole($userRole);

        return view('profile.edit', [
            'user' => $request->user(),
            
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
