<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'date_of_birth',
        'department',
        'bio',
        'profile_picture',
        'student_id',
        'email_notifications',
        'theme',
        'qr_code',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'email_notifications' => 'boolean',
    ];

    // Relationships
    public function coursesTeaching()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function attendedLectures()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    // Alias for coursesTeaching (for consistency)
    public function courses()
    {
        return $this->coursesTeaching();
    }

    public function attendances()
    {
        return $this->attendedLectures();
    }

    // New enrollment relationships
    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'active');
    }

    public function completedEnrollments()
    {
        return $this->enrollments()->where('status', 'completed');
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
            ->withPivot('semester', 'status', 'enrolled_at')
            ->withTimestamps();
    }

    // Scopes
    public function scopeLecturers($query)
    {
        return $query->where('role', 'lecturer');
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Accessors
    public function getAttendanceRateAttribute()
    {
        $total = $this->attendances()->count();
        $present = $this->attendances()->where('status', 'present')->count();
        
        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }

    public function getTotalStudentsAttribute()
    {
        if ($this->role !== 'lecturer') {
            return 0;
        }
        
        return $this->coursesTeaching()->withCount('enrollments')->get()->sum('enrollments_count');
    }

    public function getFormattedDateOfBirthAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->format('F d, Y') : 'Not set';
    }

    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return default avatar based on role
        return asset('images/default-avatar-' . $this->role . '.png');
    }

    public function getRoleBadgeClassAttribute()
    {
        return [
            'admin' => 'bg-danger',
            'lecturer' => 'bg-warning',
            'student' => 'bg-success'
        ][$this->role] ?? 'bg-secondary';
    }

    public function getRoleDisplayNameAttribute()
    {
        return [
            'admin' => 'Administrator',
            'lecturer' => 'Lecturer',
            'student' => 'Student'
        ][$this->role] ?? 'User';
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLecturer()
    {
        return $this->role === 'lecturer';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function canManageCourses()
    {
        return $this->isAdmin() || $this->isLecturer();
    }

    public function canViewAttendance()
    {
        return $this->isAdmin() || $this->isLecturer() || $this->isStudent();
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function hasProfilePicture()
    {
        return !empty($this->profile_picture);
    }

    // Student specific methods
    public function getEnrolledCoursesCountAttribute()
    {
        if (!$this->isStudent()) {
            return 0;
        }
        return $this->enrollments()->where('status', 'active')->count();
    }

    public function getActiveEnrollmentsAttribute()
    {
        if (!$this->isStudent()) {
            return collect();
        }
        return $this->enrollments()->where('status', 'active')->with('course')->get();
    }

    public function getCurrentSemesterEnrollments($semester)
    {
        if (!$this->isStudent()) {
            return collect();
        }
        return $this->enrollments()
            ->where('semester', $semester)
            ->where('status', 'active')
            ->with('course')
            ->get();
    }

    public function isEnrolledInCourse($courseId)
    {
        if (!$this->isStudent()) {
            return false;
        }
        return $this->enrollments()
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->exists();
    }

    public function getEnrolledCourses($semester = null)
    {
        if (!$this->isStudent()) {
            return collect();
        }
        
        $query = $this->enrollments()->with('course');
        
        if ($semester) {
            $query->where('semester', $semester);
        }
        
        return $query->get()->pluck('course');
    }

    // Lecturer specific methods
    public function getLecturesCountAttribute()
    {
        if (!$this->isLecturer()) {
            return 0;
        }
        return $this->coursesTeaching()->withCount('lectures')->get()->sum('lectures_count');
    }

    public function getUpcomingLecturesAttribute()
    {
        if (!$this->isLecturer()) {
            return collect();
        }
        return Lecture::whereIn('course_id', $this->coursesTeaching()->pluck('id'))
            ->where('schedule', '>=', now())
            ->orderBy('schedule')
            ->with('course')
            ->get();
    }

    // Admin specific methods
    public function getTotalUsersCountAttribute()
    {
        if (!$this->isAdmin()) {
            return 0;
        }
        return User::count();
    }

    public function getTotalCoursesCountAttribute()
    {
        if (!$this->isAdmin()) {
            return 0;
        }
        return Course::count();
    }

    public function getTotalEnrollmentsCountAttribute()
    {
        if (!$this->isAdmin()) {
            return 0;
        }
        return Enrollment::count();
    }

    // Preferences
    public function prefersEmailNotifications()
    {
        return $this->email_notifications ?? true;
    }

    public function getThemePreference()
    {
        return $this->theme ?? 'light';
    }

    // QR Code methods
    public function generateQRCode()
    {
        // This would typically use a QR code generation library
        // For now, we'll just return a placeholder
        $qrData = json_encode([
            'user_id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'student_id' => $this->student_id,
            'type' => 'user_identification'
        ]);

        // In a real implementation, you would generate an actual QR code
        $this->update(['qr_code' => $qrData]);
        return $qrData;
    }

    // Department methods
    public function getDepartmentDisplayAttribute()
    {
        return $this->department ?: 'Not assigned';
    }

    // Bio methods
    public function getShortBioAttribute()
    {
        return $this->bio ? Str::limit($this->bio, 100) : 'No bio available';
    }
}