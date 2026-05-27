@extends('layouts.student')

@section('title', 'Attendance History')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Attendance History 📊</h2>
                <p class="welcome-subtitle">Track your class attendance records</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-clipboard-check me-2"></i>
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
                        <div class="stat-title">Total Records</div>
                        <div class="stat-value">{{ $attendance->count() }}</div>
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
            <div class="stat-card card-success animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Present</div>
                        <div class="stat-value">{{ $attendance->where('status', 'present')->count() }}</div>
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
            <div class="stat-card card-warning animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Late</div>
                        <div class="stat-value">{{ $attendance->where('status', 'late')->count() }}</div>
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
            <div class="stat-card card-danger animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Absent</div>
                        <div class="stat-value">{{ $attendance->where('status', 'absent')->count() }}</div>
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

    <div class="row">
        <div class="col-12">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history me-2"></i>Attendance Records
                    </h3>
                    <a href="{{ route('student.scan-qr') }}" class="btn btn-primary">
                        <i class="fas fa-qrcode me-1"></i> Scan QR Code
                    </a>
                </div>
                <div class="card-body">
                    @if($attendance->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Course</th>
                                    <th>Lecture</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $record)
                                <tr>
                                    <td>
                                        <strong>{{ $record->date->format('M d, Y') }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-icon me-3">
                                                <i class="fas fa-book text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $record->lecture->course->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $record->lecture->course->code ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $record->lecture->title ?? 'N/A' }}</td>
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
                                        <small class="text-muted">{{ $record->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $record->notes ?? '-' }}</span>
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
                        <p>You haven't marked any attendance yet. Scan a QR code to get started!</p>
                        <div class="mt-3">
                            <a href="{{ route('student.scan-qr') }}" class="btn btn-primary me-2">
                                <i class="fas fa-qrcode me-1"></i> Scan QR Code
                            </a>
                            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
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
.course-icon {
    width: 40px;
    height: 40px;
    background: rgba(67, 97, 238, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
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
</style>
@endsection