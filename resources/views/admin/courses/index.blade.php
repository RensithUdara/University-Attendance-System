@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Manage Courses 📚</h2>
                <p class="welcome-subtitle">View and manage all courses in the system</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-book me-2"></i>
                    <span id="currentTime">{{ now()->format('l, F j, Y - h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row stats-row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-primary animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Total Courses</div>
                        <div class="stat-value">{{ $courses->count() }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-book me-1"></i>
                            <span>All courses</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-success animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Active Lecturers</div>
                        <div class="stat-value">{{ $lecturersCount }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-chalkboard-teacher me-1"></i>
                            <span>Teaching staff</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-info animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Total Students</div>
                        <div class="stat-value">{{ $studentsCount }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-user-graduate me-1"></i>
                            <span>Enrolled students</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-warning animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Total Enrollments</div>
                        <div class="stat-value">{{ $enrollmentsCount }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-clipboard-list me-1"></i>
                            <span>All enrollments</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book me-2"></i>Course Management
                    </h3>
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New Course
                    </a>
                </div>
                <div class="card-body">
                    @if($courses->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Lecturer</th>
                                    <th>Semester</th>
                                    <th>Credits</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $course->code }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-icon me-3">
                                                <i class="fas fa-book text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $course->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-small lecturer-avatar me-2">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            {{ $course->lecturer->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Semester {{ $course->semester }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $course->credits }} Credits</span>
                                    </td>
                                    <td>{{ $course->duration }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-book empty-icon"></i>
                        <h4>No Courses Found</h4>
                        <p>Get started by creating your first course.</p>
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">Add First Course</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-icon {
    width: 40px;
    height: 40px;
    background: rgba(67, 97, 238, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.user-avatar-small {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
}
</style>
@endsection