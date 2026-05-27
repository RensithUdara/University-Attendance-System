<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Calculate attendance rate
        $total_lectures = $user->attendances()->count();
        $present_count = $user->attendances()->where('status', 'present')->count();
        $attendance_rate = $total_lectures > 0 ? round(($present_count / $total_lectures) * 100, 2) : 0;

        return view('student.profile', compact('user', 'attendance_rate'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'student_id' => 'nullable|string|max:50|unique:users,student_id,' . $user->id,
            'date_of_birth' => 'nullable|date|before:today',
            'department' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update($request->only([
            'name', 'email', 'phone', 'student_id', 'date_of_birth', 'department', 'bio'
        ]));

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
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

        return redirect()->route('student.profile')->with('success', 'Profile picture updated successfully!');
    }

    public function removePicture(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        return redirect()->route('student.profile')->with('success', 'Profile picture removed successfully!');
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

        return redirect()->route('student.profile')->with('success', 'Password updated successfully!');
    }

    public function generateQR()
    {
        $user = Auth::user();
        
        // Generate QR code data with student information
        $qrData = json_encode([
            'student_id' => $user->id,
            'name' => $user->name,
            'student_code' => $user->student_id,
            'type' => 'student_identification'
        ]);

        // Generate QR code
        $qrCode = QrCode::size(200)->generate($qrData);

        // Update user's QR code
        $user->update(['qr_code' => $qrCode]);

        return redirect()->route('student.profile')->with('success', 'QR Code generated successfully!');
    }
}