@extends('layouts.lecturer')

@section('title', 'Generate QR Code - ' . $course->name)

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">QR Code Generator 📱</h2>
                <p class="welcome-subtitle">Generate QR code for {{ $course->name }}</p>
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
                        <i class="fas fa-qrcode me-2"></i>QR Code for {{ $course->name }}
                    </h3>
                    <a href="{{ route('lecturer.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- QR Code Display -->
                        <div class="col-lg-6 mb-4">
                            <div class="qr-display-section text-center">
                                <div class="qr-container mb-4">
                                    <h5 class="mb-3">Scan this QR Code for Attendance</h5>
                                    <div class="qr-code-wrapper border rounded p-4 d-inline-block bg-white">
                                        {!! $qrCode !!}
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This QR code is unique to this course and will work for today's attendance.
                                </div>

                                <div class="qr-actions mt-4">
                                    <button onclick="window.print()" class="btn btn-primary me-2">
                                        <i class="fas fa-print me-1"></i> Print QR Code
                                    </button>
                                    <a href="{{ route('lecturer.attendance') }}" class="btn btn-success">
                                        <i class="fas fa-clipboard-check me-1"></i> View Attendance
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Course Information -->
                        <div class="col-lg-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Attendance Information
                                    </h5>
                                </div>
                                <div class="info-body">
                                    <div class="info-item">
                                        <strong>Course:</strong> {{ $course->name }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Course Code:</strong> {{ $course->code }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Lecturer:</strong> {{ Auth::user()->name }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}
                                    </div>
                                    <div class="info-item">
                                        <strong>QR Code Data:</strong>
                                        <div class="qr-data mt-1">
                                            <code class="small">{{ $qrString }}</code>
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
                                                <strong>Display this QR code</strong> to students during lecture
                                            </div>
                                        </div>
                                        <div class="instruction-step">
                                            <div class="step-number">2</div>
                                            <div class="step-content">
                                                <strong>Students scan</strong> using their student dashboard
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
                                                <strong>Each QR code is unique</strong> and time-sensitive
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
}

.info-header {
    background: rgba(67, 97, 238, 0.1);
    padding: 1.25rem;
    border-bottom: 1px solid var(--border-color);
}

.info-body {
    padding: 1.5rem;
}

.info-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.info-item:last-child {
    border-bottom: none;
}

.qr-data {
    background: rgba(0, 0, 0, 0.05);
    padding: 0.75rem;
    border-radius: 8px;
    word-break: break-all;
    font-size: 0.8rem;
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

@media (max-width: 768px) {
    .instruction-step {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .step-number {
        align-self: center;
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
@endsection