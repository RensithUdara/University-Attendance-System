<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment; // Add this import
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with('lecturer')->get();
        
        // Add these missing variables that your view expects
        $lecturersCount = User::where('role', 'lecturer')->count();
        $studentsCount = User::where('role', 'student')->count();
        $enrollmentsCount = Enrollment::count(); // Make sure you have an Enrollment model

        return view('admin.courses.index', compact('courses', 'lecturersCount', 'studentsCount', 'enrollmentsCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lecturers = User::where('role', 'lecturer')->get();
        $semesters = [
            '1' => 'Semester 1',
            '2' => 'Semester 2', 
            '3' => 'Semester 3',
            '4' => 'Semester 4',
            '5' => 'Semester 5',
            '6' => 'Semester 6',
            '7' => 'Semester 7',
            '8' => 'Semester 8'
        ];
        
        return view('admin.courses.create', compact('lecturers', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|unique:courses|max:20',
            'credits' => 'required|integer|min:1|max:10',
            'lecturer_id' => 'required|exists:users,id',
            'semester' => 'required|in:1,2,3,4,5,6,7,8',
            'duration' => 'required|string|max:50',
            'max_students' => 'nullable|integer|min:1',
        ]);

        Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'credits' => $request->credits,
            'lecturer_id' => $request->lecturer_id,
            'semester' => $request->semester,
            'duration' => $request->duration,
            'max_students' => $request->max_students,
        ]);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load('lecturer', 'students', 'lectures');
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $lecturers = User::where('role', 'lecturer')->get();
        $semesters = [
            '1' => 'Semester 1',
            '2' => 'Semester 2', 
            '3' => 'Semester 3',
            '4' => 'Semester 4',
            '5' => 'Semester 5',
            '6' => 'Semester 6',
            '7' => 'Semester 7',
            '8' => 'Semester 8'
        ];
        
        return view('admin.courses.edit', compact('course', 'lecturers', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'credits' => 'required|integer|min:1|max:10',
            'lecturer_id' => 'required|exists:users,id',
            'semester' => 'required|in:1,2,3,4,5,6,7,8',
            'duration' => 'required|string|max:50',
            'max_students' => 'nullable|integer|min:1',
        ]);

        $course->update([
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'credits' => $request->credits,
            'lecturer_id' => $request->lecturer_id,
            'semester' => $request->semester,
            'duration' => $request->duration,
            'max_students' => $request->max_students,
        ]);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}