<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\LecturerProfileController;
use App\Http\Controllers\StudentProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Simple home redirect
Route::get('/home', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    $user = Auth::user();
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'lecturer') {
        return redirect()->route('lecturer.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->name('home');

// Dashboard redirect route
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'lecturer') {
        return redirect()->route('lecturer.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->name('dashboard');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
   // ADMIN ROUTES
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/courses-by-semester/{semester}', function ($semester) {
        $courses = \App\Models\Course::where('semester', $semester)
        ->with('lecturer')
        ->get();

        return response()->json($courses);
    })->name('courses.by-semester');
    
    // User Management Routes
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    
    // Course Management Routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    
     // Enrollment routes
Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
Route::put('/enrollments/{enrollment}/status', [EnrollmentController::class, 'updateStatus'])->name('enrollments.update-status');

// AJAX routes
Route::get('/admin/courses-by-semester/{semester}', [EnrollmentController::class, 'getCoursesBySemester'])->name('enrollments.courses-by-semester');
Route::get('/admin/student/{student}/details', [EnrollmentController::class, 'getStudentDetails'])->name('enrollments.student-details');
Route::get('/admin/search-students', [EnrollmentController::class, 'searchStudents'])->name('enrollments.search-students');
Route::get('/admin/student/{studentId}/semester/{semester}/enrollments', [EnrollmentController::class, 'getStudentSemesterEnrollments'])->name('enrollments.student-semester-enrollments');
Route::get('/admin/course/{courseId}/availability', [EnrollmentController::class, 'getCourseAvailability'])->name('enrollments.course-availability');
Route::get('/admin/enrollments/stats', [EnrollmentController::class, 'getStats'])->name('enrollments.stats');
Route::post('/admin/enrollments/bulk-delete', [EnrollmentController::class, 'bulkDestroy'])->name('enrollments.bulk-destroy');
Route::get('/admin/enrollments/export', [EnrollmentController::class, 'export'])->name('enrollments.export');

    // Admin Profile Routes
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('admin.profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/profile/picture', [AdminProfileController::class, 'updatePicture'])->name('admin.profile.picture');
    Route::post('/profile/picture/remove', [AdminProfileController::class, 'removePicture'])->name('admin.profile.picture.remove');
    Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('admin.password.update');
    Route::put('/preferences', [AdminProfileController::class, 'updatePreferences'])->name('admin.preferences.update');
    
    // Add the missing settings route
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
});

    // LECTURER ROUTES  
    Route::prefix('lecturer')->group(function () {
        // Dashboard
        Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('lecturer.dashboard');
        // Add this route for real-time status updates
Route::get('/lectures/{lecture}/status', [LectureController::class, 'getStatus'])->name('lectures.status');
        // Lecture Management Routes
        Route::get('/lectures', [LectureController::class, 'index'])->name('lectures.index');
        Route::get('/lectures/create', [LectureController::class, 'create'])->name('lectures.create');
        Route::post('/lectures', [LectureController::class, 'store'])->name('lectures.store');
        Route::get('/lectures/{lecture}', [LectureController::class, 'show'])->name('lectures.show');
        Route::get('/lectures/{lecture}/edit', [LectureController::class, 'edit'])->name('lectures.edit');
        Route::put('/lectures/{lecture}', [LectureController::class, 'update'])->name('lectures.update');
        Route::delete('/lectures/{lecture}', [LectureController::class, 'destroy'])->name('lectures.destroy');
        
        // QR Code and Attendance Routes
        Route::get('/lectures/{lecture}/generate-qr', [LectureController::class, 'generateQR'])->name('lectures.generate-qr');
        Route::post('/lectures/{lecture}/mark-attendance', [LectureController::class, 'markAttendance'])->name('lectures.mark-attendance');
        Route::get('/lectures/{lecture}/attendance-data', [LectureController::class, 'getAttendanceData'])->name('lectures.attendance-data');
        
        // Attendance Management
        Route::get('/attendance', [LecturerController::class, 'viewAttendance'])->name('lecturer.attendance');
        Route::get('/attendance/export', [LecturerController::class, 'exportAttendance'])->name('lecturer.attendance.export');
        Route::get('/attendance/lecture/{lectureId}', [LecturerController::class, 'getLectureAttendance'])->name('lecturer.attendance.lecture');
        
        // QR Generator Route
        Route::get('/qr-generator/{course}', [LecturerController::class, 'qrGenerator'])->name('lecturer.qr-generator');
        
        // Lecturer Profile Routes
        Route::get('/profile', [LecturerProfileController::class, 'show'])->name('lecturer.profile');
        Route::put('/profile', [LecturerProfileController::class, 'update'])->name('lecturer.profile.update');
        Route::post('/profile/picture', [LecturerProfileController::class, 'updatePicture'])->name('lecturer.profile.picture');
        Route::post('/profile/picture/remove', [LecturerProfileController::class, 'removePicture'])->name('lecturer.profile.picture.remove');
        Route::put('/password', [LecturerProfileController::class, 'updatePassword'])->name('lecturer.password.update');
    });

    // STUDENT ROUTES
Route::prefix('student')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/courses', [StudentController::class, 'courses'])->name('student.courses');
    Route::get('/attendance', [StudentController::class, 'attendance'])->name('student.attendance');
    Route::get('/scan-qr', [StudentController::class, 'scanQR'])->name('student.scan-qr');
    Route::post('/mark-attendance', [StudentController::class, 'markAttendance'])->name('student.mark-attendance');
    
    // Student Profile Routes
    Route::get('/profile', [StudentProfileController::class, 'show'])->name('student.profile');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
    Route::post('/profile/picture', [StudentProfileController::class, 'updatePicture'])->name('student.profile.picture');
    Route::post('/profile/picture/remove', [StudentProfileController::class, 'removePicture'])->name('student.profile.picture.remove');
    Route::put('/password', [StudentProfileController::class, 'updatePassword'])->name('student.password.update');
    Route::get('/generate-qr', [StudentProfileController::class, 'generateQR'])->name('student.generate-qr');
});
    Route::get('/check-users-table', function () {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');
    echo "<h3>Current Users Table Columns:</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        $type = \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnType('users', $column);
        echo "<li><strong>{$column}</strong> ({$type})</li>";
    }
    echo "</ul>";
});
// Debug route for testing attendance
Route::get('/test-attendance', function() {
    $lecture = \App\Models\Lecture::first();
    if ($lecture) {
        $qrData = [
            'lecture_id' => $lecture->id,
            'course_id' => $lecture->course_id,
            'lecturer_id' => $lecture->course->lecturer_id,
            'timestamp' => now()->timestamp,
            'expires_at' => \Carbon\Carbon::parse($lecture->schedule)->addMinutes(5)->timestamp,
            'valid_from' => \Carbon\Carbon::parse($lecture->schedule)->subMinutes(5)->timestamp,
            'type' => 'attendance'
        ];
        
        $qrString = base64_encode(json_encode($qrData));
        $lecture->update(['qr_code' => $qrString]);
        
        return response()->json([
            'lecture_id' => $lecture->id,
            'lecture_title' => $lecture->title,
            'qr_code' => $qrString,
            'qr_preview' => substr($qrString, 0, 50) . '...'
        ]);
    }
    return response()->json(['error' => 'No lecture found']);
});
});

// Manual Authentication Routes instead of Auth::routes()
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);