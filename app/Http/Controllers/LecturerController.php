<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LecturerController extends Controller
{
    public function dashboard()
    {
        $lecturer = Auth::user();
        $courses = $lecturer->coursesTeaching()
            ->withCount(['lectures', 'students'])
            ->get();
            
        $recentLectures = Lecture::whereHas('course', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with('course')->latest()->take(5)->get();

        // Get today's lectures
        $todayLectures = Lecture::whereHas('course', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->whereDate('schedule', Carbon::today())->get();

        return view('lecturer.dashboard', compact(
            'courses', 
            'recentLectures', 
            'todayLectures'
        ));
    }

    public function viewAttendance(Request $request)
    {
        $lecturer = Auth::user();
        
        // Get lecturer's courses
        $courses = $lecturer->coursesTeaching()->with('lectures')->get();
        
        // Get selected course and lecture filters
        $selectedCourseId = $request->get('course_id');
        $selectedLectureId = $request->get('lecture_id');
        
        // Build attendance query
        $attendanceQuery = Attendance::whereHas('course', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with(['student', 'course', 'lecture']);

        // Apply filters
        if ($selectedCourseId) {
            $attendanceQuery->where('course_id', $selectedCourseId);
        }
        
        if ($selectedLectureId) {
            $attendanceQuery->where('lecture_id', $selectedLectureId);
        }

        // Get filtered attendance
        $attendance = $attendanceQuery->orderBy('date', 'desc')
            ->orderBy('marked_at', 'desc')
            ->get();

        // Get lectures for selected course
        $lectures = [];
        if ($selectedCourseId) {
            $lectures = Lecture::where('course_id', $selectedCourseId)->get();
        }

        // Calculate statistics
        $totalRecords = $attendance->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $lateCount = $attendance->where('status', 'late')->count();
        $absentCount = $totalRecords - ($presentCount + $lateCount);

        return view('lecturer.attendance', compact(
            'attendance', 
            'courses',
            'lectures',
            'selectedCourseId',
            'selectedLectureId',
            'totalRecords',
            'presentCount',
            'lateCount',
            'absentCount'
        ));
    }

    /**
     * Get attendance data for a specific lecture (AJAX)
     */
    public function getLectureAttendance($lectureId)
    {
        $lecturer = Auth::user();
        
        $attendance = Attendance::where('lecture_id', $lectureId)
            ->whereHas('course', function($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
            })
            ->with(['student'])
            ->get();

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'total_students' => $attendance->count(),
            'present_count' => $attendance->where('status', 'present')->count(),
            'late_count' => $attendance->where('status', 'late')->count(),
        ]);
    }

    /**
     * Export attendance for a course
     */
    public function exportAttendance(Request $request)
    {
        $lecturer = Auth::user();
        $courseId = $request->get('course_id');
        
        $attendance = Attendance::whereHas('course', function($query) use ($lecturer, $courseId) {
            $query->where('lecturer_id', $lecturer->id)
                  ->when($courseId, function($q) use ($courseId) {
                      $q->where('id', $courseId);
                  });
        })->with(['student', 'course', 'lecture'])
          ->orderBy('date', 'desc')
          ->get();

        // You can implement CSV or PDF export here
        // For now, return JSON response
        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }
}