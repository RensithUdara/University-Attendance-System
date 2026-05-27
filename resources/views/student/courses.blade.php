@extends('layouts.student')

@section('title', 'My Courses')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">My Courses 📚</h2>
                <p class="welcome-subtitle">View all your enrolled courses</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-book me-2"></i>
                    <span id="currentTime">{{ now()->format('l, F j, Y - h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book me-2"></i>My Enrolled Courses
                    </h3>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                    <div class="row courses-grid">
                        @foreach($enrollments as $enrollment)
                        <div class="col-md-6 mb-4">
                            <div class="course-card animate-fade-in-up">
                                <div class="course-header">
                                    <h5 class="course-title">{{ $enrollment->course->name }}</h5>
                                    <span class="course-code">{{ $enrollment->course->code }}</span>
                                </div>
                                
                                <div class="course-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-user-tie text-primary"></i>
                                        <span><strong>Lecturer:</strong> {{ $enrollment->course->lecturer->name }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-star text-warning"></i>
                                        <span><strong>Credits:</strong> {{ $enrollment->course->credits }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-calendar text-info"></i>
                                        <span><strong>Semester:</strong> {{ $enrollment->course->semester }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-clock text-success"></i>
                                        <span><strong>Duration:</strong> {{ $enrollment->course->duration }}</span>
                                    </div>
                                </div>
                                
                                <p class="course-description text-muted mb-3">
                                    {{ Str::limit($enrollment->course->description, 120) }}
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-plus me-1"></i>
                                        {{ $enrollment->enrolled_at->format('M d, Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-book empty-icon"></i>
                        <h4>No Courses Enrolled</h4>
                        <p>You are not enrolled in any courses yet.</p>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.courses-grid {
    margin: -0.75rem;
}

.course-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    height: 100%;
}

.course-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

.course-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.course-title {
    color: var(--text-dark);
    font-weight: 600;
    margin-bottom: 0.25rem;
    flex: 1;
    margin-right: 1rem;
}

.course-code {
    background: var(--primary-light);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.course-stats {
    margin-bottom: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    color: var(--text-light);
    font-size: 0.875rem;
}

.stat-item i {
    margin-right: 0.5rem;
    width: 16px;
    text-align: center;
}

.course-description {
    font-size: 0.9rem;
    line-height: 1.4;
}
</style>
@endsection