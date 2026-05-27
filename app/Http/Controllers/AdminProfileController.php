<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'department', 'bio']));

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old picture if exists
        if ($user->profile_picture) {
            Storage::delete($user->profile_picture);
        }

        // Store new picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        $user->update(['profile_picture' => $path]);

        return redirect()->route('admin.profile')->with('success', 'Profile picture updated successfully!');
    }

    public function removePicture(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return redirect()->route('admin.profile')->with('success', 'Profile picture removed successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully!');
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications' => 'nullable|boolean',
            'theme' => 'nullable|in:light,dark,auto',
        ]);

        $user->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'theme' => $request->theme ?? 'light',
        ]);

        return redirect()->route('admin.profile')->with('success', 'Preferences updated successfully!');
    }
}