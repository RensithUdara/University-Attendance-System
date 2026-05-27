<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'profile_picture',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user associated with the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the courses that the student is enrolled in.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('semester', 'status', 'enrolled_at')
                    ->withTimestamps();
    }

    /**
     * Get the enrollments for the student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the student's full name with ID.
     */
    public function getFullNameWithIdAttribute(): string
    {
        return "{$this->name} ({$this->student_id})";
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if student is enrolled in a specific course and semester.
     */
    public function isEnrolledIn($courseId, $semester): bool
    {
        return $this->enrollments()
                    ->where('course_id', $courseId)
                    ->where('semester', $semester)
                    ->exists();
    }
}