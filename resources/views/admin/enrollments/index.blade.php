@extends('layouts.admin')

@section('title', 'Manage Enrollments')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="page-title">🎓 Manage Enrollments</h2>
            <p class="page-subtitle">Enroll students in courses</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" onclick="showEnrollmentForm()">
                <i class="fas fa-user-plus me-2"></i>New Enrollment
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['totalEnrollments'] }}</h3>
                    <p>Total Enrollments</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['activeStudents'] }}</h3>
                    <p>Active Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['totalCourses'] }}</h3>
                    <p>Available Courses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Form -->
    <div class="row mb-4" id="enrollmentFormSection" style="display: none;">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-user-plus me-2"></i>Enroll Student
                    </h4>
                    <button type="button" class="btn-close" onclick="hideEnrollmentForm()"></button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enrollments.store') }}" id="enrollmentForm">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Student Selection -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Select Student *</label>
                                <select class="form-select" name="student_id" id="studentSelect" required>
                                    <option value="">Choose a student...</option>
                                    @foreach($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Semester Selection -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Select Semester *</label>
                                <select class="form-select" name="semester" id="semesterSelect" required>
                                    <option value="">Choose Semester</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ $i == $currentSemester ? 'selected' : '' }}>
                                            Semester {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Course Selection -->
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Select Course(s) *</label>
                                <div id="coursesContainer">
                                    <div class="alert alert-info">
                                        Select a semester to view available courses
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="clearForm()">
                                <i class="fas fa-times me-1"></i> Clear
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enroll Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Enrollments -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-list me-2"></i>Recent Enrollments
                    </h4>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('enrollments.index') }}">All</a></li>
                            <li><a class="dropdown-item" href="?status=active">Active Only</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @for($i = 1; $i <= 8; $i++)
                                <li><a class="dropdown-item" href="?semester={{ $i }}">Semester {{ $i }}</a></li>
                            @endfor
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Semester</th>
                                    <th>Status</th>
                                    <th>Enrolled Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-success me-2">
                                                {{ substr($enrollment->student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $enrollment->student->name }}</strong>
                                                <div class="small text-muted">{{ $enrollment->student->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $enrollment->course->code }}</strong>
                                            <div class="small text-muted">{{ $enrollment->course->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Sem {{ $enrollment->semester }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'info' : 'danger') }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $enrollment->enrolled_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form method="POST" action="{{ route('enrollments.update-status', $enrollment) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" name="status" value="completed" 
                                                        class="btn btn-outline-success btn-sm" title="Mark as Completed">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('enrollments.update-status', $enrollment) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" name="status" value="dropped" 
                                                        class="btn btn-outline-warning btn-sm" title="Drop Course">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $enrollments->links() }}
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4>No Enrollments Found</h4>
                        <p class="text-muted">Start by enrolling a student.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Dynamic Course Loading -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load courses for selected semester
    const semesterSelect = document.getElementById('semesterSelect');
    const coursesContainer = document.getElementById('coursesContainer');
    
    semesterSelect.addEventListener('change', function() {
        loadCourses(this.value);
    });
});

function showEnrollmentForm() {
    document.getElementById('enrollmentFormSection').style.display = 'block';
}

function hideEnrollmentForm() {
    document.getElementById('enrollmentFormSection').style.display = 'none';
}

function loadCourses(semester) {
    if (!semester) {
        document.getElementById('coursesContainer').innerHTML = `
            <div class="alert alert-info">
                Select a semester to view available courses
            </div>
        `;
        return;
    }
    
    fetch(`/admin/courses-by-semester/${semester}`)
        .then(response => response.json())
        .then(courses => {
            if (courses.length === 0) {
                document.getElementById('coursesContainer').innerHTML = `
                    <div class="alert alert-warning">
                        No courses available for this semester
                    </div>
                `;
                return;
            }
            
            let html = '<div class="row g-2">';
            courses.forEach(course => {
                html += `
                <div class="col-md-6">
                    <div class="course-checkbox-card">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="course_ids[]" 
                                   value="${course.id}" 
                                   id="course_${course.id}">
                            <label class="form-check-label w-100" for="course_${course.id}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${course.code}</strong>
                                        <div class="small text-muted">${course.name}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary">${course.credits} Credits</span>
                                        <div class="small text-muted">${course.lecturer.name}</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                `;
            });
            html += '</div>';
            
            document.getElementById('coursesContainer').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            document.getElementById('coursesContainer').innerHTML = `
                <div class="alert alert-danger">
                    Error loading courses. Please try again.
                </div>
            `;
        });
}

function clearForm() {
    document.getElementById('enrollmentForm').reset();
    document.getElementById('coursesContainer').innerHTML = `
        <div class="alert alert-info">
            Select a semester to view available courses
        </div>
    `;
}

// Form validation
document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
    const studentId = document.getElementById('studentSelect').value;
    const semester = document.getElementById('semesterSelect').value;
    const courseCheckboxes = document.querySelectorAll('input[name="course_ids[]"]:checked');
    
    if (!studentId) {
        e.preventDefault();
        alert('Please select a student');
        return;
    }
    
    if (!semester) {
        e.preventDefault();
        alert('Please select a semester');
        return;
    }
    
    if (courseCheckboxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one course');
        return;
    }
});
</script>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.course-checkbox-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.course-checkbox-card:hover {
    border-color: #4361ee;
    background-color: rgba(67, 97, 238, 0.05);
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.stat-card.stat-primary .stat-icon {
    background: rgba(67, 97, 238, 0.1);
    color: #4361ee;
}

.stat-card.stat-success .stat-icon {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.stat-card.stat-info .stat-icon {
    background: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-right: 15px;
}

.stat-content h3 {
    margin: 0;
    font-size: 28px;
    font-weight: bold;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
}
</style>
@endsection