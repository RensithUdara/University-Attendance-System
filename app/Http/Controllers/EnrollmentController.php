<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'course.lecturer']);
        
        // Apply filters if present
        if ($request->has('status') && in_array($request->status, ['active', 'completed', 'dropped'])) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('semester') && is_numeric($request->semester)) {
            $query->where('semester', $request->semester);
        }
        
        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);
            
        $students = User::where('role', 'student')->get(); // Removed status filter
        
        // Get statistics for dashboard
        $stats = [
            'totalEnrollments' => Enrollment::count(),
            'activeStudents' => User::where('role', 'student')->count(), // Changed from status filter
            'totalCourses' => Course::count(),
        ];
        
        // Get current semester (you can modify this logic based on your system)
        $currentSemester = 1; // Default to semester 1

        return view('admin.enrollments.index', compact(
            'enrollments', 
            'students', 
            'stats',
            'currentSemester'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'semester' => 'required|in:1,2,3,4,5,6,7,8',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $student = User::findOrFail($request->student_id);
        $semester = $request->semester;
        $courseIds = $request->course_ids;
        
        $enrolledCount = 0;
        $errors = [];
        $successfulEnrollments = [];

        // Start transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            foreach ($courseIds as $courseId) {
                $course = Course::findOrFail($courseId);
                
                // Check if course belongs to the selected semester
                if ($course->semester != $semester) {
                    $errors[] = "Course {$course->code} ({$course->name}) does not belong to semester {$semester}";
                    continue;
                }
                
                // Check if student is already enrolled in this course
                $existingEnrollment = Enrollment::where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->first();

                if ($existingEnrollment) {
                    $errors[] = "Student is already enrolled in {$course->code} ({$course->name})";
                    continue;
                }

                // Check if student has reached maximum enrollments for the semester
                $semesterEnrollments = Enrollment::where('student_id', $student->id)
                    ->where('semester', $semester)
                    ->count();

                if ($semesterEnrollments >= 6) {
                    $errors[] = "Student has reached maximum course limit (6) for semester {$semester}";
                    break; // Stop processing more courses
                }

                // Create enrollment
                $enrollment = Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'semester' => $semester,
                ]);
                
                $enrolledCount++;
                $successfulEnrollments[] = $course->code;
            }
            
            DB::commit();
            
            // Prepare success message
            if ($enrolledCount > 0) {
                $message = "Successfully enrolled {$student->name} in {$enrolledCount} course(s): " . implode(', ', $successfulEnrollments);
                $messageType = 'success';
            } else {
                $message = "No courses were enrolled. " . ($errors ? implode(' ', $errors) : '');
                $messageType = 'error';
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('enrollments.index')
                ->with('error', 'An error occurred during enrollment. Please try again.');
        }

        return redirect()->route('enrollments.index')
            ->with($messageType, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }

    /**
     * Update enrollment status
     */
    public function updateStatus(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:active,completed,dropped',
        ]);

        $enrollment->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return redirect()->route('enrollments.index')
            ->with('success', 'Enrollment status updated successfully.');
    }

    /**
     * Get courses by semester for AJAX request
     */
    public function getCoursesBySemester($semester)
    {
        $courses = Course::where('semester', $semester)
            ->with('lecturer')
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'code' => $course->code,
                    'name' => $course->name,
                    'credits' => $course->credits,
                    'lecturer' => [
                        'name' => $course->lecturer->name,
                    ],
                ];
            });
            
        return response()->json($courses);
    }

    /**
     * Get student details for AJAX request
     */
    public function getStudentDetails($studentId)
    {
        $student = User::with(['enrollments' => function($query) {
            $query->with('course')->where('status', 'active');
        }])->findOrFail($studentId);
        
        return response()->json([
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'student_id' => $student->student_id ?? 'N/A',
            'current_enrollments' => $student->enrollments->map(function($enrollment) {
                return [
                    'course_code' => $enrollment->course->code,
                    'course_name' => $enrollment->course->name,
                    'semester' => $enrollment->semester,
                ];
            }),
        ]);
    }

    /**
     * Search students by name or ID
     */
    public function searchStudents(Request $request)
    {
        $searchTerm = $request->get('q', '');
        
        $students = User::where('role', 'student')
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('student_id', 'LIKE', "%{$searchTerm}%");
            })
            ->limit(10)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'text' => $student->name . ' (' . $student->email . ')',
                    'name' => $student->name,
                    'email' => $student->email,
                    'student_id' => $student->student_id ?? 'N/A',
                ];
            });
            
        return response()->json(['results' => $students]);
    }

    /**
     * Get student's current semester enrollments
     */
    public function getStudentSemesterEnrollments($studentId, $semester)
    {
        $enrollments = Enrollment::where('student_id', $studentId)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->with('course')
            ->get()
            ->map(function($enrollment) {
                return [
                    'course_id' => $enrollment->course_id,
                    'course_code' => $enrollment->course->code,
                    'course_name' => $enrollment->course->name,
                ];
            });
            
        return response()->json($enrollments);
    }

    /**
     * Get available seats for courses
     */
    public function getCourseAvailability($courseId)
    {
        $course = Course::withCount(['enrollments' => function($query) {
            $query->where('status', 'active');
        }])->findOrFail($courseId);
        
        $maxCapacity = 50; // You can make this a field in your courses table
        
        return response()->json([
            'enrolled_count' => $course->enrollments_count,
            'available_seats' => $maxCapacity - $course->enrollments_count,
            'is_full' => $course->enrollments_count >= $maxCapacity,
        ]);
    }

    /**
     * Bulk delete enrollments
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
        ]);
        
        $deletedCount = Enrollment::whereIn('id', $request->enrollment_ids)->delete();
        
        return redirect()->route('enrollments.index')
            ->with('success', "Successfully deleted {$deletedCount} enrollment(s).");
    }

    /**
     * Export enrollments to CSV
     */
    public function export(Request $request)
    {
        $enrollments = Enrollment::with(['student', 'course'])
            ->when($request->has('semester'), function($query) use ($request) {
                $query->where('semester', $request->semester);
            })
            ->when($request->has('status'), function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->get();
            
        $filename = "enrollments_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Student Name', 'Student ID', 'Email', 'Course Code', 'Course Name', 'Semester', 'Status', 'Enrolled Date']);
            
            // Add data
            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->student->name,
                    $enrollment->student->student_id ?? 'N/A',
                    $enrollment->student->email,
                    $enrollment->course->code,
                    $enrollment->course->name,
                    $enrollment->semester,
                    ucfirst($enrollment->status),
                    $enrollment->enrolled_at->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Quick stats for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_enrollments' => Enrollment::count(),
            'active_enrollments' => Enrollment::where('status', 'active')->count(),
            'students_without_enrollments' => User::where('role', 'student')
                ->whereDoesntHave('enrollments', function($query) {
                    $query->where('status', 'active');
                })->count(),
            'courses_without_students' => Course::whereDoesntHave('enrollments', function($query) {
                $query->where('status', 'active');
            })->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Get all semesters
     */
    public function getSemesters()
    {
        $semesters = [];
        for ($i = 1; $i <= 8; $i++) {
            $semesters[] = [
                'id' => $i,
                'name' => 'Semester ' . $i,
                'enrollments_count' => Enrollment::where('semester', $i)->where('status', 'active')->count(),
            ];
        }
        
        return response()->json($semesters);
    }
}