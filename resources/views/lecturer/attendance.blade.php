@extends('layouts.lecturer')

@section('title', 'Attendance Records')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Attendance Records 📊</h2>
                <p class="welcome-subtitle">View and manage student attendance</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-clipboard-check me-2"></i>
                    <span id="currentTime">{{ now()->format('l, F j, Y - h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter me-2"></i>Filter Attendance
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('lecturer.attendance') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Select Course</label>
                                    <select name="course_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Courses</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }} ({{ $course->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Select Lecture</label>
                                    <select name="lecture_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Lectures</option>
                                        @foreach($lectures as $lecture)
                                            <option value="{{ $lecture->id }}" 
                                                {{ $selectedLectureId == $lecture->id ? 'selected' : '' }}>
                                                {{ $lecture->title }} ({{ $lecture->formatted_schedule }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-check me-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('lecturer.attendance') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($totalRecords > 0)
    <div class="row stats-row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-primary animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Total Records</div>
                        <div class="stat-value">{{ $totalRecords }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-clipboard-list me-1"></i>
                            <span>All attendance</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-success animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Present</div>
                        <div class="stat-value">{{ $presentCount }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-check-circle me-1"></i>
                            <span>On time</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-warning animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Late</div>
                        <div class="stat-value">{{ $lateCount }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-clock me-1"></i>
                            <span>Late arrivals</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-danger animate-fade-in-up" style="animation-delay: 0.6s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Absent</div>
                        <div class="stat-value">{{ $absentCount }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-times-circle me-1"></i>
                            <span>Missed classes</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Attendance Records -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.7s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i>Attendance Records
                    </h3>
                    <div class="export-actions">
                        <form method="GET" action="{{ route('lecturer.attendance.export') }}" class="d-inline me-2">
                            @if($selectedCourseId)
                                <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
                            @endif
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i> Export CSV
                            </button>
                        </form>
                        <button class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($attendance->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Student ID</th>
                                    <th>Course</th>
                                    <th>Lecture</th>
                                    <th>Status</th>
                                    <th>Time Marked</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $record)
                                <tr>
                                    <td>
                                        <strong>{{ $record->formatted_date }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-small student-avatar me-2">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $record->student->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $record->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $record->student->student_id ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-icon me-2">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $record->course->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $record->course->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $record->lecture->title ?? 'N/A' }}
                                        @if($record->lecture)
                                        <br>
                                        <small class="text-muted">{{ $record->lecture->formatted_schedule }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->status === 'present')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Present
                                            </span>
                                        @elseif($record->status === 'late')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Late
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Absent
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $record->formatted_marked_at }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $record->notes ?? 'QR Scan' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list empty-icon"></i>
                        <h4>No Attendance Records</h4>
                        <p>
                            @if($selectedCourseId || $selectedLectureId)
                                Try changing your filters or
                            @else
                                Create lectures and ask students to scan QR codes to record attendance.
                            @endif
                        </p>
                        <div class="mt-3">
                            @if($selectedCourseId || $selectedLectureId)
                                <a href="{{ route('lecturer.attendance') }}" class="btn btn-primary me-2">
                                    View All Records
                                </a>
                            @endif
                            <a href="{{ route('lectures.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Create Lecture
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.export-actions {
    display: flex;
    gap: 0.5rem;
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

.student-avatar {
    background: var(--success-color);
}

.course-icon {
    width: 30px;
    height: 30px;
    background: rgba(67, 97, 238, 0.1);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 0.8rem;
}

.table th {
    border-bottom: 2px solid var(--border-color);
    font-weight: 600;
    color: var(--text-light);
    background: rgba(67, 97, 238, 0.05);
}

.table td {
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

@media print {
    .dashboard-navbar,
    .welcome-section,
    .stats-row,
    .card-header .export-actions,
    .btn {
        display: none !important;
    }
    
    .dashboard-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update lecture dropdown when course changes
    const courseSelect = document.querySelector('select[name="course_id"]');
    const lectureSelect = document.querySelector('select[name="lecture_id"]');
    
    if (courseSelect) {
        courseSelect.addEventListener('change', function() {
            if (this.value) {
                this.form.submit();
            }
        });
    }
});
</script>
@endsection