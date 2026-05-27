@extends('layouts.lecturer')

@section('title', $lecture->title)

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">{{ $lecture->title }}</h2>
                <p class="welcome-subtitle">Lecture details and attendance tracking</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    <span id="currentTime">{{ now()->format('l, F j, Y - h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lecture Details & Statistics -->
        <div class="col-lg-4">
            <!-- Lecture Details Card -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Lecture Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="lecture-info">
                        <h4 class="lecture-title">{{ $lecture->title }}</h4>
                        <p class="lecture-description text-muted">{{ $lecture->description }}</p>
                        
                        <div class="info-list">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="info-content">
                                    <strong>Course:</strong> {{ $lecture->course->name }} ({{ $lecture->course->code }})
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <strong>Schedule:</strong> {{ $lecture->formatted_schedule }}
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="info-content">
                                    <strong>Duration:</strong> {{ $lecture->duration }} minutes
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="info-content">
                                    <strong>Type:</strong> 
                                    <span class="badge bg-info text-capitalize">{{ $lecture->lesson_type }}</span>
                                </div>
                            </div>
                            @if($lecture->room)
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <strong>Room:</strong> {{ $lecture->room }}
                                </div>
                            </div>
                            @endif
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="info-content">
                                    <strong>Status:</strong> 
                                    @php
                                        $statusClass = [
                                            'upcoming' => 'warning',
                                            'ongoing' => 'success', 
                                            'completed' => 'secondary'
                                        ][$lecture->status];
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }} text-capitalize">{{ $lecture->status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lecture-actions mt-4">
                        <a href="{{ route('lectures.generate-qr', $lecture) }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-qrcode me-1"></i> Generate/View QR Code
                        </a>
                        <a href="{{ route('lectures.edit', $lecture) }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-edit me-1"></i> Edit Lecture
                        </a>
                        <a href="{{ route('lectures.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-arrow-left me-1"></i> Back to Lectures
                        </a>
                    </div>
                </div>
            </div>

            <!-- Attendance Statistics -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>Attendance Statistics
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="attendance-rate mb-4">
                        <div class="rate-circle">
                            <div class="rate-value">{{ $attendancePercentage }}%</div>
                            <div class="rate-label">Attendance Rate</div>
                        </div>
                    </div>
                    <div class="attendance-stats">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-present">
                                    <div class="stat-value text-success">{{ $presentCount }}</div>
                                    <div class="stat-label">Present</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-absent">
                                    <div class="stat-value text-danger">{{ $totalStudents - $presentCount }}</div>
                                    <div class="stat-label">Absent</div>
                                </div>
                            </div>
                        </div>
                        <div class="total-students mt-3">
                            <small class="text-muted">Total Students: {{ $totalStudents }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Attendance List -->
        <div class="col-lg-8">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users me-2"></i>Real-time Attendance
                    </h3>
                    <button id="refreshAttendance" class="btn btn-primary btn-sm">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="attendanceList">
                        @if($attendance->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Student ID</th>
                                        <th>Status</th>
                                        <th>Marked At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendance as $record)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar-small student-avatar me-2">
                                                    <i class="fas fa-user-graduate"></i>
                                                </div>
                                                {{ $record->student->name }}
                                            </div>
                                        </td>
                                        <td>{{ $record->student->email }}</td>
                                        <td>
                                            <code>{{ $record->student->student_id ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i> Present
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $record->marked_at ? $record->marked_at->format('h:i A') : 'N/A' }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-users empty-icon"></i>
                            <h4>No Attendance Records</h4>
                            <p>No students have marked attendance yet.</p>
                            <p class="text-muted">Generate QR code and ask students to scan it.</p>
                            <a href="{{ route('lectures.generate-qr', $lecture) }}" class="btn btn-primary">
                                <i class="fas fa-qrcode me-1"></i> Generate QR Code
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.lecture-info {
    margin-bottom: 1.5rem;
}

.lecture-title {
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.lecture-description {
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.info-item:last-child {
    border-bottom: none;
}

.info-icon {
    width: 24px;
    height: 24px;
    background: rgba(67, 97, 238, 0.1);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 0.8rem;
    flex-shrink: 0;
    margin-top: 0.1rem;
}

.info-content {
    flex: 1;
    font-size: 0.9rem;
}

.lecture-actions .btn {
    border-radius: 8px;
    padding: 0.75rem;
}

.attendance-rate {
    padding: 1rem 0;
}

.rate-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(var(--primary-color) {{ $attendancePercentage }}%, #e9ecef 0%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.rate-circle::before {
    content: '';
    position: absolute;
    width: 90px;
    height: 90px;
    background: var(--card-bg);
    border-radius: 50%;
}

.rate-value {
    position: relative;
    z-index: 1;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
}

.rate-label {
    margin-top: 0.5rem;
    color: var(--text-light);
    font-size: 0.9rem;
}

.attendance-stats {
    margin-top: 1rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: var(--text-light);
}

.total-students {
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
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
    background: var(--success-color);
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

@media (max-width: 768px) {
    .info-item {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .info-icon {
        align-self: center;
    }
    
    .rate-circle {
        width: 100px;
        height: 100px;
    }
    
    .rate-circle::before {
        width: 75px;
        height: 75px;
    }
    
    .rate-value {
        font-size: 1.25rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lectureId = {{ $lecture->id }};
    const refreshBtn = document.getElementById('refreshAttendance');
    const attendanceList = document.getElementById('attendanceList');
    
    // Function to update lecture status
    async function updateLectureStatus() {
        try {
            const response = await fetch(`/lecturer/lectures/${lectureId}/status`);
            const data = await response.json();
            
            if (data.success) {
                updateStatusUI(data.data);
            }
        } catch (error) {
            console.error('Error updating status:', error);
        }
    }
    
    // Update status UI
    function updateStatusUI(data) {
        // Update status badge in the info list
        const statusBadge = document.querySelector('.info-content .badge');
        if (statusBadge) {
            const statusClasses = {
                'upcoming': 'warning',
                'ongoing': 'success', 
                'completed': 'secondary'
            };
            const statusClass = statusClasses[data.status] || 'secondary';
            statusBadge.className = `badge bg-${statusClass} text-capitalize`;
            statusBadge.textContent = data.status;
        }
        
        // Update QR validity info if it exists
        const qrValidityElement = document.getElementById('qr-validity-info');
        if (qrValidityElement) {
            if (data.qr_valid) {
                qrValidityElement.innerHTML = `
                    <span class="badge bg-success">Valid</span> (${data.qr_validity_window})
                `;
            } else {
                qrValidityElement.innerHTML = `
                    <span class="badge bg-danger">Expired</span> (${data.qr_validity_window})
                `;
            }
        }
    }
    
    // Function to refresh attendance
    function refreshAttendance() {
        fetch(`/lecturer/lectures/${lectureId}/attendance-data`)
            .then(response => response.json())
            .then(data => {
                // Update attendance list
                if (data.attendance.length > 0) {
                    let html = `
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Student ID</th>
                                        <th>Status</th>
                                        <th>Marked At</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.attendance.forEach(record => {
                        html += `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-small student-avatar me-2">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        ${record.student.name}
                                    </div>
                                </td>
                                <td>${record.student.email}</td>
                                <td><code>${record.student.student_id || 'N/A'}</code></td>
                                <td>
                                    <span class="badge bg-${record.status === 'present' ? 'success' : 'warning'}">
                                        <i class="fas fa-${record.status === 'present' ? 'check' : 'clock'} me-1"></i> 
                                        ${record.status.charAt(0).toUpperCase() + record.status.slice(1)}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">${record.marked_at ? new Date(record.marked_at).toLocaleTimeString() : 'N/A'}</small>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    attendanceList.innerHTML = html;
                } else {
                    attendanceList.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-users empty-icon"></i>
                            <h4>No Attendance Records</h4>
                            <p>No students have marked attendance yet.</p>
                            <p class="text-muted">QR Code Status: 
                                <span id="qr-validity-info">Loading...</span>
                            </p>
                            <a href="{{ route('lectures.generate-qr', $lecture) }}" class="btn btn-primary">
                                <i class="fas fa-qrcode me-1"></i> Generate QR Code
                            </a>
                        </div>
                    `;
                    
                    // Update QR validity info after rendering
                    updateLectureStatus();
                }
                
                // Update statistics
                if (data.stats) {
                    updateStatistics(data.stats);
                }
            })
            .catch(error => {
                console.error('Error refreshing attendance:', error);
            });
    }
    
    // Update statistics
    function updateStatistics(stats) {
        // Update attendance rate circle
        const rateCircle = document.querySelector('.rate-circle');
        if (rateCircle) {
            rateCircle.style.background = `conic-gradient(var(--primary-color) ${stats.attendance_percentage}%, #e9ecef 0%)`;
            document.querySelector('.rate-value').textContent = `${stats.attendance_percentage}%`;
        }
        
        // Update counts
        const presentCount = document.querySelector('.stat-present .stat-value');
        const absentCount = document.querySelector('.stat-absent .stat-value');
        
        if (presentCount) presentCount.textContent = stats.present_count;
        if (absentCount) absentCount.textContent = stats.absent_count;
    }
    
    // Initialize
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshAttendance);
    }
    
    // Update status immediately and then periodically
    updateLectureStatus();
    
    // Auto-refresh every 30 seconds for status
    setInterval(updateLectureStatus, 30000);
    
    // Auto-refresh attendance if lecture is ongoing
    @if($lecture->status === 'ongoing')
    refreshAttendance(); // Initial load
    setInterval(refreshAttendance, 10000); // Every 10 seconds
    @endif
});
</script>
@endsection