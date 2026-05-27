@extends('layouts.lecturer')

@section('title', 'Lecturer Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="welcome-subtitle">Here's what's happening with your courses today.</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-clock me-2"></i>
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
                            <i class="fas fa-book-open me-1"></i>
                            <span>Active courses</span>
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
                        <div class="stat-title">Total Students</div>
                        <div class="stat-value">{{ $courses->sum('students_count') }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-user-graduate me-1"></i>
                            <span>Enrolled students</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-info animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Total Lectures</div>
                        <div class="stat-value">{{ $courses->sum('lectures_count') }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-chalkboard me-1"></i>
                            <span>All time lectures</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-warning animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Today's Lectures</div>
                        <div class="stat-value">{{ $todayLectures->count() }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-calendar-day me-1"></i>
                            <span>Scheduled for today</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row quick-actions">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('lectures.index') }}" class="action-card">
                                <div class="action-icon bg-primary">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="action-text">My Lectures</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('lecturer.attendance') }}" class="action-card">
                                <div class="action-icon bg-success">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="action-text">View Attendance</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('lectures.create') }}" class="action-card">
                                <div class="action-icon bg-info">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="action-text">Create Lecture</div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <button class="action-card" data-bs-toggle="modal" data-bs-target="#quickAttendanceModal">
                                <div class="action-icon bg-warning">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div class="action-text">Quick Attendance</div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- My Courses -->
        <div class="col-lg-8">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.6s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book me-2"></i>My Courses
                    </h3>
                </div>
                <div class="card-body">
                    @if($courses->count() > 0)
                    <div class="row courses-grid">
                        @foreach($courses as $course)
                        <div class="col-md-6 mb-4">
                            <div class="course-card">
                                <div class="course-header">
                                    <h5 class="course-title">{{ $course->name }}</h5>
                                    <span class="course-code">{{ $course->code }}</span>
                                </div>
                                <div class="course-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-user-graduate"></i>
                                        <span>{{ $course->students_count }} Students</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-chalkboard"></i>
                                        <span>{{ $course->lectures_count }} Lectures</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-star"></i>
                                        <span>{{ $course->credits }} Credits</span>
                                    </div>
                                </div>
                                <div class="course-actions">
                                    <a href="{{ route('lectures.create') }}?course_id={{ $course->id }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-1"></i>Create Lecture
                                    </a>
                                    <a href="{{ route('lecturer.attendance') }}?course_id={{ $course->id }}" class="btn btn-outline-primary btn-sm">View Attendance</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-book empty-icon"></i>
                        <h4>No Courses Assigned</h4>
                        <p>You haven't been assigned to any courses yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="col-lg-4">
            <!-- Today's Lectures -->
            @if($todayLectures->count() > 0)
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.7s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day me-2"></i>Today's Schedule
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="schedule-list">
                        @foreach($todayLectures as $lecture)
                        <div class="schedule-item">
                            <div class="schedule-time">
                                <div class="time">{{ $lecture->schedule->format('h:i A') }}</div>
                                <div class="duration">{{ $lecture->duration }}min</div>
                            </div>
                            <div class="schedule-details">
                                <h6 class="lecture-title">{{ $lecture->title }}</h6>
                                <p class="course-name">{{ $lecture->course->name }}</p>
                            </div>
                            <div class="schedule-actions">
                                <a href="{{ route('lectures.show', $lecture) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Lectures -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.8s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history me-2"></i>Recent Lectures
                    </h3>
                    <a href="{{ route('lectures.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentLectures->count() > 0)
                    <div class="recent-list">
                        @foreach($recentLectures as $lecture)
                        <div class="recent-item">
                            <div class="recent-icon">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div class="recent-details">
                                <h6>{{ $lecture->title }}</h6>
                                <div class="recent-meta">
                                    <span class="course">{{ $lecture->course->name }}</span>
                                    <span class="time">{{ $lecture->schedule->format('M d, h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state small">
                        <i class="fas fa-chalkboard empty-icon"></i>
                        <p>No recent lectures</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Attendance Modal -->
<div class="modal fade" id="quickAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>Quick Attendance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($courses as $course)
                    <div class="col-md-6 mb-3">
                        <div class="course-quick-card">
                            <div class="course-info">
                                <h6>{{ $course->name }}</h6>
                                <small class="text-muted">{{ $course->code }}</small>
                            </div>
                            <a href="{{ route('lectures.create') }}?course_id={{ $course->id }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i>Create Lecture
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
