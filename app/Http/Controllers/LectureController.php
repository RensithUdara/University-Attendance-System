<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LectureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lecturer = Auth::user();
        $lectures = Lecture::whereHas('course', function($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with('course')->latest()->get();

        return view('lecturer.lectures.index', compact('lectures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lecturer = Auth::user();
        $courses = $lecturer->coursesTeaching;
        
        return view('lecturer.lectures.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'schedule' => 'required|date',
            'duration' => 'required|integer|min:1',
            'course_id' => 'required|exists:courses,id',
            'room' => 'nullable|string|max:100',
            'lesson_type' => 'required|in:theory,practical,lab,workshop',
        ]);

        // Verify the course belongs to the lecturer
        $lecturer = Auth::user();
        $course = Course::where('id', $request->course_id)
            ->where('lecturer_id', $lecturer->id)
            ->firstOrFail();

        $lecture = Lecture::create([
            'title' => $request->title,
            'description' => $request->description,
            'schedule' => $request->schedule,
            'duration' => $request->duration,
            'course_id' => $request->course_id,
            'room' => $request->room,
            'lesson_type' => $request->lesson_type,
            'qr_code' => $this->generateUniqueQRCode(),
        ]);

        return redirect()->route('lectures.show', $lecture)
            ->with('success', 'Lecture created successfully. QR code generated!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecture $lecture)
    {
        // Verify the lecture belongs to the lecturer
        $this->authorizeLecturer($lecture);
        
        $lecture->load('course', 'attendances.student');
        
        // Get real-time attendance data
        $attendance = $lecture->attendances()
            ->with('student')
            ->get();
            
        $totalStudents = $lecture->course->students()->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $attendancePercentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 2) : 0;

        // Get QR validity information
        $qrValid = $lecture->isQRCodeValid();
        $qrValidityWindow = $lecture->getQRValidityWindow();

        return view('lecturer.lectures.show', compact(
            'lecture', 
            'attendance', 
            'totalStudents',
            'presentCount',
            'attendancePercentage',
            'qrValid',
            'qrValidityWindow'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lecture $lecture)
    {
        // Verify the lecture belongs to the lecturer
        $this->authorizeLecturer($lecture);
        
        $lecturer = Auth::user();
        $courses = $lecturer->coursesTeaching;
        
        return view('lecturer.lectures.edit', compact('lecture', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lecture $lecture)
    {
        // Verify the lecture belongs to the lecturer
        $this->authorizeLecturer($lecture);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'schedule' => 'required|date',
            'duration' => 'required|integer|min:1',
            'course_id' => 'required|exists:courses,id',
            'room' => 'nullable|string|max:100',
            'lesson_type' => 'required|in:theory,practical,lab,workshop',
        ]);

        $lecture->update($request->all());

        return redirect()->route('lectures.show', $lecture)
            ->with('success', 'Lecture updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lecture $lecture)
    {
        // Verify the lecture belongs to the lecturer
        $this->authorizeLecturer($lecture);
        
        $lecture->delete();

        return redirect()->route('lectures.index')
            ->with('success', 'Lecture deleted successfully.');
    }

    /**
     * Generate QR code for attendance
     */
    public function generateQR(Lecture $lecture)
    {
        $this->authorizeLecturer($lecture);

        // Generate unique QR code data with expiration
        $qrData = [
            'lecture_id' => $lecture->id,
            'course_id' => $lecture->course_id,
            'lecturer_id' => Auth::id(),
            'timestamp' => now()->timestamp,
            'expires_at' => Carbon::parse($lecture->schedule)->addMinutes(5)->timestamp,
            'valid_from' => Carbon::parse($lecture->schedule)->subMinutes(5)->timestamp,
            'type' => 'attendance'
        ];

        $qrString = base64_encode(json_encode($qrData));
        
        // Update lecture with new QR code
        $lecture->update(['qr_code' => $qrString]);
        
        // Generate QR code as SVG
        $qrCode = QrCode::size(300)->generate($qrString);

        return view('lecturer.lectures.qr-code', compact('lecture', 'qrCode', 'qrString'));
    }

    /**
     * Mark attendance via QR code scan
     */
    public function markAttendance(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'student_id' => 'required|exists:users,id',
        ]);

        try {
            $qrData = json_decode(base64_decode($request->qr_code), true);
            
            if (!$qrData || !isset($qrData['lecture_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ], 400);
            }

            $lecture = Lecture::findOrFail($qrData['lecture_id']);
            
            // Check if QR code is valid (not expired)
            if (isset($qrData['expires_at'])) {
                $expiryTime = Carbon::createFromTimestamp($qrData['expires_at']);
                if (now()->gt($expiryTime)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR code has expired. Please ask lecturer for a new QR code.'
                    ], 400);
                }
            }
            
            // Check if attendance already marked
            $existingAttendance = Attendance::where('student_id', $request->student_id)
                ->where('lecture_id', $lecture->id)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance already marked for this lecture'
                ], 400);
            }

            // Create attendance record
            $attendance = Attendance::create([
                'student_id' => $request->student_id,
                'lecture_id' => $lecture->id,
                'course_id' => $lecture->course_id,
                'date' => now(),
                'status' => 'present',
                'marked_at' => now(),
                'notes' => 'Marked via QR code scan',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully!',
                'attendance' => $attendance->load('student')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time attendance data for a lecture
     */
    public function getAttendanceData(Lecture $lecture)
    {
        $this->authorizeLecturer($lecture);

        $attendance = $lecture->attendances()
            ->with('student')
            ->get();
            
        $totalStudents = $lecture->course->students()->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $attendancePercentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 2) : 0;

        return response()->json([
            'attendance' => $attendance,
            'stats' => [
                'total_students' => $totalStudents,
                'present_count' => $presentCount,
                'attendance_percentage' => $attendancePercentage,
                'absent_count' => $totalStudents - $presentCount,
            ]
        ]);
    }

    /**
     * Generate unique QR code string
     */
    private function generateUniqueQRCode()
    {
        return uniqid('lec_', true) . '_' . time();
    }

    /**
     * Authorize lecturer access to lecture
     */
    private function authorizeLecturer(Lecture $lecture)
    {
        $lecturer = Auth::user();
        if ($lecture->course->lecturer_id !== $lecturer->id) {
            abort(403, 'Unauthorized action.');
        }
    }

/**
 * Get real-time status of a lecture
 */
public function getStatus(Lecture $lecture)
{
    // Verify the lecture belongs to the lecturer
    $this->authorizeLecturer($lecture);
    
    try {
        $statusData = $lecture->getRealtimeStatusData();
        
        return response()->json([
            'success' => true,
            'data' => $statusData
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error getting status: ' . $e->getMessage()
        ], 500);
    }
}
}