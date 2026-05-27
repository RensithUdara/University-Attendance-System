<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'code', 
        'credits', 
        'lecturer_id',
        'semester',
        'duration',
        'max_students'
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id');
    }

    // New methods for enrollment system
    public function activeEnrollments()
    {
        return $this->hasMany(Enrollment::class)->where('status', 'active');
    }

    public function getStudentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    public function getLecturesCountAttribute()
    {
        return $this->lectures()->count();
    }

    // Scope for semester filtering
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    // Get available slots
    public function getAvailableSlotsAttribute()
    {
        if ($this->max_students) {
            return max(0, $this->max_students - $this->students_count);
        }
        return null; // No limit
    }

    public function hasAvailableSlots()
    {
        return $this->max_students === null || $this->students_count < $this->max_students;
    }

    // Accessor for formatted duration
    public function getFormattedDurationAttribute()
    {
        return $this->duration ?: 'Not specified';
    }

    // Check if student is enrolled
    public function isStudentEnrolled($studentId)
    {
        return $this->enrollments()
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->exists();
    }

    // Get upcoming lectures
    public function upcomingLectures()
    {
        return $this->lectures()
            ->where('schedule', '>=', now())
            ->orderBy('schedule')
            ->get();
    }

    // Get completed lectures
    public function completedLectures()
    {
        return $this->lectures()
            ->where('schedule', '<', now())
            ->orderBy('schedule', 'desc')
            ->get();
    }
}