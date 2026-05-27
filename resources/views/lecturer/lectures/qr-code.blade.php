@extends('layouts.lecturer')

@section('title', 'QR Code - ' . $lecture->title)

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">QR Code Generator 📱</h2>
                <p class="welcome-subtitle">Attendance QR code for {{ $lecture->title }}</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-qrcode me-2"></i>
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
                        <i class="fas fa-qrcode me-2"></i>QR Code for {{ $lecture->title }}
                    </h3>
                    <a href="{{ route('lectures.show', $lecture) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Lecture
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- QR Code Display -->
                        <div class="col-lg-6 mb-4">
                            <div class="qr-display-section text-center">
                                <div class="qr-container mb-4">
                                    <h4 class="mb-3">Scan for Attendance</h4>
                                    <div class="qr-code-wrapper border rounded p-4 d-inline-block bg-white">
                                        {!! $qrCode !!}
                                    </div>
                                    <div class="qr-validity-info mt-3">
                                        <div id="qr-validity-alert" class="alert">
                                            <i class="fas fa-clock me-2"></i>
                                            <span id="qr-validity-text">Loading status...</span>
                                        </div>
                                        <div id="qr-timer" class="small text-muted mt-2">
                                            <i class="fas fa-hourglass-half me-1"></i>
                                            <span id="qr-timer-text">Checking...</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Display this QR code during your lecture for students to scan.
                                    <br>
                                    <small class="mt-1 d-block">
                                        <strong>Note:</strong> QR code will automatically expire 5 minutes after lecture starts.
                                    </small>
                                </div>

                                <div class="qr-actions mt-4">
                                    <button onclick="window.print()" class="btn btn-primary me-2">
                                        <i class="fas fa-print me-1"></i> Print QR Code
                                    </button>
                                    <button id="refreshQR" class="btn btn-warning me-2">
                                        <i class="fas fa-sync-alt me-1"></i> Refresh QR Code
                                    </button>
                                    <a href="{{ route('lectures.show', $lecture) }}" class="btn btn-success">
                                        <i class="fas fa-clipboard-check me-1"></i> View Attendance
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lecture Information -->
                        <div class="col-lg-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Lecture Information
                                    </h5>
                                </div>
                                <div class="info-body">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-label">Lecture:</div>
                                            <div class="info-value">{{ $lecture->title }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Course:</div>
                                            <div class="info-value">
                                                {{ $lecture->course->name }} ({{ $lecture->course->code }})
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Schedule:</div>
                                            <div class="info-value">
                                                {{ $lecture->formatted_schedule }}
                                                <br>
                                                <small class="text-muted" id="schedule-countdown"></small>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Duration:</div>
                                            <div class="info-value">{{ $lecture->duration }} minutes</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Type:</div>
                                            <div class="info-value">
                                                <span class="badge bg-info text-capitalize">{{ $lecture->lesson_type }}</span>
                                            </div>
                                        </div>
                                        @if($lecture->room)
                                        <div class="info-item">
                                            <div class="info-label">Room:</div>
                                            <div class="info-value">{{ $lecture->room }}</div>
                                        </div>
                                        @endif
                                        <div class="info-item">
                                            <div class="info-label">Status:</div>
                                            <div class="info-value">
                                                <span id="status-badge" class="badge text-capitalize">Loading...</span>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">QR Status:</div>
                                            <div class="info-value">
                                                <span id="qr-status-badge" class="badge">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <h6 class="instructions-title">
                                        <i class="fas fa-list-ol me-2"></i>Instructions:
                                    </h6>
                                    <div class="instructions-list">
                                        <div class="instruction-step">
                                            <div class="step-number">1</div>
                                            <div class="step-content">
                                                <strong>Display this QR code</strong> to students during the lecture
                                            </div>
                                        </div>
                                        <div class="instruction-step">
                                            <div class="step-number">2</div>
                                            <div class="step-content">
                                                <strong>Students scan</strong> using their student dashboard (5 minutes before/after lecture)
                                            </div>
                                        </div>
                                        <div class="instruction-step">
                                            <div class="step-number">3</div>
                                            <div class="step-content">
                                                <strong>Attendance recorded</strong> automatically in real-time
                                            </div>
                                        </div>
                                        <div class="instruction-step">
                                            <div class="step-number">4</div>
                                            <div class="step-content">
                                                <strong>QR code expires 5 minutes</strong> after lecture starts
                                            </div>
                                        </div>
                                        <div class="instruction-step">
                                            <div class="step-number">5</div>
                                            <div class="step-content">
                                                <strong>View real-time attendance</strong> on the lecture page
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="warning-alert mt-3">
                                        <div class="alert-icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="alert-content">
                                            <strong>Important:</strong> This QR code is valid only for <strong>{{ $lecture->title }}</strong>.
                                            <br>
                                            <small class="mt-1 d-block">
                                                <strong>QR Validity:</strong> <span id="qr-validity-details">Loading...</span>
                                            </small>
                                            <small class="mt-1 d-block">
                                                <strong>Current Server Time:</strong> <span id="server-time">{{ now()->format('h:i:s A') }}</span>
                                            </small>
                                            <small class="mt-1 d-block">
                                                <strong>Lecture Time (Server):</strong> {{ $lecture->schedule->format('h:i A') }}
                                            </small>
                                            <small class="mt-1 d-block">
                                                <strong>QR Valid From (Server):</strong> {{ \Carbon\Carbon::parse($lecture->schedule)->subMinutes(5)->format('h:i A') }}
                                            </small>
                                            <small class="mt-1 d-block">
                                                <strong>QR Valid To (Server):</strong> {{ \Carbon\Carbon::parse($lecture->schedule)->addMinutes(5)->format('h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.qr-display-section {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 2rem;
    border: 1px solid var(--border-color);
}

.qr-code-wrapper {
    box-shadow: var(--shadow);
    border-radius: 12px;
    max-width: 100%;
}

.qr-validity-info .alert {
    max-width: 300px;
    margin: 0 auto;
}

.qr-actions .btn {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
}

.info-card {
    background: var(--card-bg);
    border-radius: 15px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    height: 100%;
}

.info-header {
    background: rgba(67, 97, 238, 0.1);
    padding: 1.25rem;
    border-bottom: 1px solid var(--border-color);
}

.info-body {
    padding: 1.5rem;
}

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--text-dark);
    flex: 1;
}

.info-value {
    flex: 2;
    text-align: right;
    color: var(--text-light);
}

.instructions-title {
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.instructions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.instruction-step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.step-number {
    width: 28px;
    height: 28px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
    font-size: 0.9rem;
}

.warning-alert {
    background: rgba(248, 150, 30, 0.1);
    border: 1px solid rgba(248, 150, 30, 0.3);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.warning-alert .alert-icon {
    color: var(--warning-color);
}

@media (max-width: 768px) {
    .instruction-step {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .step-number {
        align-self: center;
    }
    
    .info-item {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .info-value {
        text-align: left;
    }
    
    .qr-actions .btn {
        display: block;
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

@media print {
    .dashboard-navbar,
    .welcome-section,
    .card-header,
    .btn,
    .alert,
    .col-lg-6:last-child {
        display: none !important;
    }
    
    .col-lg-6:first-child {
        width: 100% !important;
    }
    
    .qr-code-wrapper {
        border: 2px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lectureId = {{ $lecture->id }};
    
    // Get server times from PHP (these are already in server timezone)
    const serverLectureTime = '{{ $lecture->schedule->format("Y-m-d H:i:s") }}';
    const serverCurrentTime = '{{ now()->format("Y-m-d H:i:s") }}';
    
    // Parse server times
    const lectureTime = new Date(serverLectureTime);
    const currentTime = new Date(serverCurrentTime);
    
    // Calculate validity times (5 minutes before/after lecture)
    const validStartTime = new Date(lectureTime);
    validStartTime.setMinutes(validStartTime.getMinutes() - 5);
    
    const validEndTime = new Date(lectureTime);
    validEndTime.setMinutes(validEndTime.getMinutes() + 5);
    
    console.log('=== TIME DEBUG INFO ===');
    console.log('Server Current Time:', currentTime.toLocaleString());
    console.log('Server Lecture Time:', lectureTime.toLocaleString());
    console.log('QR Valid From:', validStartTime.toLocaleString());
    console.log('QR Valid To:', validEndTime.toLocaleString());
    
    // Refresh QR code button
    const refreshBtn = document.getElementById('refreshQR');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to generate a new QR code? The old one will no longer work.')) {
                window.location.reload();
            }
        });
    }
    
    // Format time difference in human-readable format
    function formatTimeDifference(minutes) {
        if (minutes < 1) return 'Now';
        
        const hours = Math.floor(minutes / 60);
        const mins = Math.floor(minutes % 60);
        
        if (hours > 0 && mins > 0) {
            return `${hours}h ${mins}m`;
        } else if (hours > 0) {
            return `${hours}h`;
        } else {
            return `${mins}m`;
        }
    }
    
    // Format exact time in words
    function formatExactTime(minutes) {
        if (minutes < 1) return 'less than a minute';
        if (minutes < 60) return `${Math.floor(minutes)} minute${minutes >= 2 ? 's' : ''}`;
        
        const hours = Math.floor(minutes / 60);
        const mins = Math.floor(minutes % 60);
        
        if (mins === 0) {
            return `${hours} hour${hours !== 1 ? 's' : ''}`;
        }
        return `${hours} hour${hours !== 1 ? 's' : ''} ${mins} minute${mins !== 1 ? 's' : ''}`;
    }
    
    // Calculate time differences
    function calculateTimeDifferences() {
        const now = new Date(); // Client time
        
        // Calculate all time differences in minutes
        const minutesUntilStart = Math.max(0, Math.floor((lectureTime - now) / 60000));
        const minutesUntilQRValid = Math.max(0, Math.floor((validStartTime - now) / 60000));
        const minutesUntilQRExpires = Math.max(0, Math.floor((validEndTime - now) / 60000));
        
        // Calculate status
        let status, statusClass;
        if (now < lectureTime) {
            status = 'upcoming';
            statusClass = 'warning';
        } else if (now >= lectureTime && now <= new Date(lectureTime.getTime() + ({{ $lecture->duration }} * 60000))) {
            status = 'ongoing';
            statusClass = 'success';
        } else {
            status = 'completed';
            statusClass = 'secondary';
        }
        
        // Check if QR is valid
        const isQRValid = now >= validStartTime && now <= validEndTime;
        
        return {
            status,
            statusClass,
            isQRValid,
            minutesUntilStart,
            minutesUntilQRValid,
            minutesUntilQRExpires
        };
    }
    
    // Update the UI
    function updateUI() {
        const data = calculateTimeDifferences();
        const now = new Date();
        
        // Update status badge
        const statusBadge = document.getElementById('status-badge');
        statusBadge.className = `badge bg-${data.statusClass} text-capitalize`;
        statusBadge.textContent = data.status;
        
        // Update QR status badge
        const qrStatusBadge = document.getElementById('qr-status-badge');
        if (data.isQRValid) {
            qrStatusBadge.className = 'badge bg-success';
            qrStatusBadge.textContent = 'Valid';
        } else {
            qrStatusBadge.className = 'badge bg-danger';
            qrStatusBadge.textContent = 'Expired';
        }
        
        // Update QR validity alert
        const validityAlert = document.getElementById('qr-validity-alert');
        const validityText = document.getElementById('qr-validity-text');
        
        if (data.isQRValid) {
            validityAlert.className = 'alert alert-success';
            if (data.minutesUntilQRExpires > 0) {
                validityText.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    QR code is <strong>VALID</strong> for ${formatExactTime(data.minutesUntilQRExpires)}
                `;
            } else {
                validityText.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    QR code is <strong>VALID</strong> (expiring soon)
                `;
            }
        } else {
            validityAlert.className = 'alert alert-danger';
            if (data.minutesUntilQRValid > 0) {
                validityText.innerHTML = `
                    <i class="fas fa-clock me-2"></i>
                    QR code will be valid in <strong>${formatExactTime(data.minutesUntilQRValid)}</strong>
                `;
            } else {
                validityText.innerHTML = `
                    <i class="fas fa-times-circle me-2"></i>
                    QR code has <strong>EXPIRED</strong>
                `;
            }
        }
        
        // Update QR timer
        const timerText = document.getElementById('qr-timer-text');
        const validFromStr = validStartTime.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            timeZoneName: 'short'
        });
        const validToStr = validEndTime.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            timeZoneName: 'short'
        });
        
        if (data.isQRValid) {
            timerText.innerHTML = `
                <i class="fas fa-hourglass-end me-1"></i>
                Expires at <strong>${validToStr}</strong>
            `;
        } else if (data.minutesUntilQRValid > 0) {
            timerText.innerHTML = `
                <i class="fas fa-hourglass-start me-1"></i>
                Becomes valid at <strong>${validStartTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</strong>
            `;
        } else {
            timerText.innerHTML = `
                <i class="fas fa-hourglass me-1"></i>
                Was valid from <strong>${validStartTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</strong> to <strong>${validEndTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</strong>
            `;
        }
        
        // Update schedule countdown
        const scheduleCountdown = document.getElementById('schedule-countdown');
        if (data.minutesUntilStart > 0) {
            scheduleCountdown.innerHTML = `
                <i class="fas fa-clock me-1"></i>
                Starts in <strong>${formatExactTime(data.minutesUntilStart)}</strong>
            `;
        } else {
            const minutesSinceStart = Math.abs(Math.floor((now - lectureTime) / 60000));
            scheduleCountdown.innerHTML = `
                <i class="fas fa-play-circle me-1"></i>
                Started <strong>${formatExactTime(minutesSinceStart)} ago</strong>
            `;
        }
        
        // Update QR validity details
        const validityDetails = document.getElementById('qr-validity-details');
        validityDetails.innerHTML = `
            <strong>${validStartTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</strong> to <strong>${validEndTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</strong>
        `;
        
        // Update current time display
        document.getElementById('currentTime').textContent = now.toLocaleString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        
        document.getElementById('server-time').textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        
        // Debug output
        console.log('Update:', {
            now: now.toLocaleString(),
            lectureTime: lectureTime.toLocaleString(),
            validFrom: validStartTime.toLocaleString(),
            validTo: validEndTime.toLocaleString(),
            minutesUntilQRValid: data.minutesUntilQRValid,
            minutesUntilStart: data.minutesUntilStart,
            isQRValid: data.isQRValid
        });
    }
    
    // Update status from server (optional, for sync)
    async function updateStatusFromServer() {
        try {
            const response = await fetch(`/lecturer/lectures/${lectureId}/status`);
            const data = await response.json();
            
            if (data.success) {
                console.log('Server status sync:', data.data);
                // We could sync with server time if needed
            }
        } catch (error) {
            console.log('Using client-side calculation only');
        }
    }
    
    // Initialize
    updateStatusFromServer();
    updateUI(); // Initial update
    
    // Update every 30 seconds
    const updateInterval = setInterval(updateUI, 30000);
    
    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        clearInterval(updateInterval);
    });
});
</script>
@endsection