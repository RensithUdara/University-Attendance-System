<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'lecture_id',
        'course_id',
        'date',
        'status',
        'notes',
        'marked_at'
    ];

    protected $casts = [
        'date' => 'date',
        'marked_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        return $this->date->format('M d, Y');
    }

    public function getFormattedMarkedAtAttribute()
    {
        return $this->marked_at ? $this->marked_at->format('h:i A') : 'N/A';
    }

    // Scopes
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeForLecture($query, $lectureId)
    {
        return $query->where('lecture_id', $lectureId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days));
    }
}