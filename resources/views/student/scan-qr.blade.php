@extends('layouts.student')

@section('title', 'Scan QR Code')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Scan QR Code 📱</h2>
                <p class="welcome-subtitle">Mark your attendance using QR code</p>
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
                        <i class="fas fa-camera me-2"></i>QR Code Scanner  
                    </h3>
                    <a href="{{ route('student.attendance') }}" class="btn btn-secondary">
                        <i class="fas fa-history me-1"></i> View History
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Scanner Section -->
                        <div class="col-lg-8 mb-4">
                            <div class="scanner-container glass-card p-4">
                                <div id="scanner-status">
                                    <div id="loading-message" class="alert alert-info">
                                        <i class="fas fa-camera me-2"></i>Click "Start Camera" to begin scanning
                                    </div>
                                    
                                    <div id="camera-container" style="display: none;">
                                        <div id="qr-reader" style="width: 100%;"></div>
                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Point your camera at the QR code
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div id="camera-error" class="alert alert-danger" style="display: none;">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <span id="error-message"></span>
                                        <div id="error-solutions" class="mt-2 small"></div>
                                    </div>

                                    <div id="scan-result" class="mt-3" style="display: none;">
                                        <div class="alert" id="result-alert">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <span id="result-message"></span>
                                        </div>
                                        <div id="attendance-details" class="mt-2" style="display: none;"></div>
                                    </div>
                                </div>

                                <div class="scanner-controls text-center mt-4">
                                    <button id="start-camera" class="btn btn-primary btn-lg">
                                        <i class="fas fa-camera me-2"></i>Start Camera
                                    </button>
                                    <button id="stop-camera" class="btn btn-warning btn-lg" style="display: none;">
                                        <i class="fas fa-stop me-2"></i>Stop Camera
                                    </button>
                                </div>
                                
                                <!-- Debug Information (hidden by default) -->
                                <div id="debug-info" class="mt-3 small text-muted" style="display: none;">
                                    <hr>
                                    <strong>Debug Info:</strong>
                                    <div id="debug-content"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Instructions Section -->
                        <div class="col-lg-4">
                            <div class="instructions-card">
                                <h5 class="instruction-title">
                                    <i class="fas fa-info-circle me-2"></i>Quick Instructions
                                </h5>
                                <div class="instructions-list">
                                    <div class="instruction-step">
                                        <div class="step-number">1</div>
                                        <div class="step-content">
                                            <strong>Click "Start Camera"</strong>
                                            <p class="text-muted small mb-0">Allow camera access when prompted</p>
                                        </div>
                                    </div>
                                    <div class="instruction-step">
                                        <div class="step-number">2</div>
                                        <div class="step-content">
                                            <strong>Point at QR Code</strong>
                                            <p class="text-muted small mb-0">Hold steady until scanned</p>
                                        </div>
                                    </div>
                                    <div class="instruction-step">
                                        <div class="step-number">3</div>
                                        <div class="step-content">
                                            <strong>Wait for Confirmation</strong>
                                            <p class="text-muted small mb-0">Attendance marked automatically</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Note:</strong> Make sure to allow camera permissions in your browser.
                                    <ul class="mt-2 mb-0 small">
                                        <li>QR codes are valid for 5 minutes before/after lecture</li>
                                        <li>Each QR code can be used only once</li>
                                        <li>Make sure you have internet connection</li>
                                    </ul>
                                </div>
                                
                                <!-- Manual Entry -->
                                <div class="manual-entry-section mt-4">
                                    <h6 class="manual-title">
                                        <i class="fas fa-keyboard me-2"></i>Manual Entry
                                    </h6>
                                    <p class="text-muted small mb-2">If camera doesn't work, ask lecturer for QR code data:</p>
                                    <form id="manual-entry-form">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="manual-qr" 
                                                   placeholder="Enter QR code data here" required>
                                        </div>
                                        <button type="submit" class="btn btn-secondary w-100">
                                            <i class="fas fa-paper-plane me-1"></i> Submit Attendance
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Troubleshooting Tips -->
                                <div class="troubleshooting-section mt-4">
                                    <h6 class="manual-title">
                                        <i class="fas fa-wrench me-2"></i>Troubleshooting
                                    </h6>
                                    <ul class="small text-muted mb-0">
                                        <li>Make sure camera is not in use by another app</li>
                                        <li>Try refreshing the page if camera doesn't start</li>
                                        <li>Check your internet connection</li>
                                        <li>Use manual entry if camera fails</li>
                                        <li><button id="toggle-debug" class="btn btn-sm btn-link p-0">Show Debug Info</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// QR Scanner Class
class QRScanner {
    constructor() {
        this.scanner = null;
        this.isScanning = false;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        
        this.elements = {
            loading: document.getElementById('loading-message'),
            cameraContainer: document.getElementById('camera-container'),
            cameraError: document.getElementById('camera-error'),
            errorMessage: document.getElementById('error-message'),
            errorSolutions: document.getElementById('error-solutions'),
            scanResult: document.getElementById('scan-result'),
            resultAlert: document.getElementById('result-alert'),
            resultMessage: document.getElementById('result-message'),
            attendanceDetails: document.getElementById('attendance-details'),
            startBtn: document.getElementById('start-camera'),
            stopBtn: document.getElementById('stop-camera'),
            manualForm: document.getElementById('manual-entry-form'),
            manualInput: document.getElementById('manual-qr'),
            debugInfo: document.getElementById('debug-info'),
            debugContent: document.getElementById('debug-content'),
            toggleDebug: document.getElementById('toggle-debug')
        };
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.checkLibrary();
        this.logDebug('QR Scanner initialized');
    }
    
    bindEvents() {
        this.elements.startBtn.addEventListener('click', () => this.startScanner());
        this.elements.stopBtn.addEventListener('click', () => this.stopScanner());
        this.elements.manualForm.addEventListener('submit', (e) => this.handleManualSubmit(e));
        
        if (this.elements.toggleDebug) {
            this.elements.toggleDebug.addEventListener('click', (e) => {
                e.preventDefault();
                this.elements.debugInfo.style.display = this.elements.debugInfo.style.display === 'none' ? 'block' : 'none';
                this.elements.toggleDebug.textContent = 
                    this.elements.debugInfo.style.display === 'none' ? 'Show Debug Info' : 'Hide Debug Info';
            });
        }
    }
    
    checkLibrary() {
        if (typeof Html5QrcodeScanner === 'undefined') {
            console.error('QR Scanner library not loaded');
            this.showError(
                'QR Scanner library not loaded', 
                'Please check your internet connection and refresh the page.'
            );
            return false;
        }
        this.logDebug('QR Scanner library loaded successfully');
        return true;
    }
    
    async startScanner() {
        if (!this.checkLibrary()) return;
        
        try {
            this.showLoading('Starting camera...');
            this.elements.cameraError.style.display = 'none';
            this.hideResult();
            
            // Stop if already scanning
            if (this.scanner) {
                await this.stopScanner();
            }
            
            // Check for camera support
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Camera not supported on this device');
            }
            
            // Initialize scanner
            this.scanner = new Html5QrcodeScanner(
                "qr-reader",
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                false
            );
            
            this.scanner.render(
                (decodedText) => this.onScanSuccess(decodedText),
                (error) => this.onScanFailure(error)
            );
            
            this.isScanning = true;
            this.showCamera();
            this.logDebug('Camera started successfully');
            
        } catch (error) {
            console.error('Scanner error:', error);
            this.handleCameraError(error);
        }
    }
    
    async stopScanner() {
        if (this.scanner && this.isScanning) {
            try {
                await this.scanner.clear();
                this.scanner = null;
                this.isScanning = false;
                this.logDebug('Camera stopped');
            } catch (error) {
                console.error('Error stopping scanner:', error);
            }
        }
        this.hideCamera();
    }
    
    onScanSuccess(decodedText) {
        this.logDebug('QR Code scanned successfully', decodedText.substring(0, 50) + '...');
        this.stopScanner();
        this.processQRCode(decodedText);
    }
    
    onScanFailure(error) {
        // Ignore "not found" errors
        if (error && !error.includes('NotFoundException')) {
            this.logDebug('Scan error:', error);
        }
    }
    
    processQRCode(qrData) {
        this.showResult('QR Code detected! Processing attendance...', 'info');
        
        if (!qrData || qrData.trim() === '') {
            this.showResult('Invalid QR code. Please try again.', 'error');
            setTimeout(() => {
                this.hideResult();
                this.startScanner();
            }, 3000);
            return;
        }
        
        this.markAttendance(qrData);
    }
    
    markAttendance(qrCode) {
        this.logDebug('Sending QR code to server...');
        
        // Show processing message
        this.showResult('Sending attendance data...', 'info');
        
        // Create request data
        const requestData = {
            qr_code: qrCode
        };
        
        this.logDebug('Request data:', requestData);
        
        // Make AJAX request
        fetch('{{ route("student.mark-attendance") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            this.logDebug('Response status:', response.status, response.statusText);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Server returned non-JSON response: ${contentType}`);
            }
            
            return response.json();
        })
        .then(data => {
            this.logDebug('Response data:', data);
            
            if (data.success) {
                this.showSuccessResult(data.message, data.attendance);
                
                // Redirect to dashboard after 3 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("student.dashboard") }}';
                }, 3000);
            } else {
                this.showResult(data.message, 'error');
                setTimeout(() => {
                    this.hideResult();
                    this.startScanner();
                }, 4000);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            this.logDebug('Fetch error details:', error.message, error.stack);
            
            let errorMessage = 'Network error. Please check your connection and try again.';
            
            if (error.message.includes('non-JSON response')) {
                errorMessage = 'Server error. Please try again or contact support.';
            }
            
            this.showResult(errorMessage, 'error');
            setTimeout(() => {
                this.hideResult();
                this.startScanner();
            }, 3000);
        });
    }
    
    handleManualSubmit(e) {
        e.preventDefault();
        const qrCode = this.elements.manualInput.value.trim();
        
        if (qrCode) {
            this.stopScanner();
            this.processQRCode(qrCode);
        } else {
            alert('Please enter QR code data');
        }
    }
    
    handleCameraError(error) {
        let message = 'Unable to access camera';
        let solutions = 'Please try manual entry below.';
        
        if (error.name === 'NotAllowedError') {
            message = 'Camera permission denied';
            solutions = 'Please allow camera access in your browser settings and refresh the page.';
        } else if (error.name === 'NotFoundError') {
            message = 'No camera found';
            solutions = 'Please check if your device has a working camera.';
        } else if (error.name === 'NotSupportedError') {
            message = 'Browser not supported';
            solutions = 'Please try using Google Chrome or Mozilla Firefox.';
        } else if (error.message) {
            message = error.message;
        }
        
        this.showError(message, solutions);
    }
    
    // UI Methods
    showLoading(message) {
        this.elements.loading.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${message}`;
        this.elements.loading.style.display = 'block';
        this.elements.cameraContainer.style.display = 'none';
    }
    
    showCamera() {
        this.elements.loading.style.display = 'none';
        this.elements.cameraContainer.style.display = 'block';
        this.elements.cameraError.style.display = 'none';
        this.elements.startBtn.style.display = 'none';
        this.elements.stopBtn.style.display = 'inline-block';
    }
    
    hideCamera() {
        this.elements.cameraContainer.style.display = 'none';
        this.elements.startBtn.style.display = 'inline-block';
        this.elements.stopBtn.style.display = 'none';
        this.elements.loading.style.display = 'block';
        this.elements.loading.innerHTML = '<i class="fas fa-camera me-2"></i>Click "Start Camera" to begin scanning';
    }
    
    showError(message, solutions = '') {
        this.elements.errorMessage.textContent = message;
        this.elements.errorSolutions.innerHTML = solutions;
        this.elements.cameraError.style.display = 'block';
        this.elements.loading.style.display = 'none';
        this.elements.cameraContainer.style.display = 'none';
    }
    
    showResult(message, type = 'info') {
        this.elements.resultMessage.textContent = message;
        this.elements.scanResult.style.display = 'block';
        
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 'alert-info';
        
        this.elements.resultAlert.className = `alert ${alertClass}`;
        
        // Set appropriate icon
        const iconClass = type === 'error' ? 'fa-exclamation-triangle' : 
                         type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        
        this.elements.resultAlert.querySelector('i').className = `fas ${iconClass} me-2`;
    }
    
    showSuccessResult(message, attendance) {
        this.showResult(message, 'success');
        
        if (attendance) {
            this.elements.attendanceDetails.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Attendance Details:</h6>
                        <p class="mb-1"><strong>Lecture:</strong> ${attendance.lecture}</p>
                        <p class="mb-1"><strong>Course:</strong> ${attendance.course}</p>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">${attendance.status}</span></p>
                        <p class="mb-1"><strong>Time:</strong> ${attendance.time}</p>
                    </div>
                </div>
            `;
            this.elements.attendanceDetails.style.display = 'block';
        }
    }
    
    hideResult() {
        this.elements.scanResult.style.display = 'none';
        this.elements.attendanceDetails.style.display = 'none';
    }
    
    logDebug(...args) {
        const timestamp = new Date().toLocaleTimeString();
        const message = `[${timestamp}] ${args.map(arg => 
            typeof arg === 'object' ? JSON.stringify(arg, null, 2) : arg
        ).join(' ')}`;
        
        console.log(...args);
        
        if (this.elements.debugContent) {
            this.elements.debugContent.innerHTML += `<div>${message}</div>`;
            // Keep only last 10 debug messages
            const lines = this.elements.debugContent.innerHTML.split('<div>');
            if (lines.length > 11) {
                this.elements.debugContent.innerHTML = lines.slice(-10).join('<div>');
            }
        }
    }
}

// Initialize scanner when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the scan QR page
    if (document.getElementById('start-camera')) {
        window.qrScanner = new QRScanner();
    }
    
    // Update current time
    function updateCurrentTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        };
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = now.toLocaleDateString('en-US', options) + ' - ' + now.toLocaleTimeString('en-US');
        }
    }
    
    updateCurrentTime();
    setInterval(updateCurrentTime, 60000);
});
</script>

<style>
.scanner-container {
    background: var(--card-bg);
    border-radius: 15px;
    border: 1px solid var(--border-color);
    min-height: 400px;
}

.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

#qr-reader {
    margin: 0 auto;
}

#qr-reader video {
    border-radius: 12px;
    box-shadow: var(--shadow);
    max-width: 100%;
    height: auto;
}

.scanner-controls .btn {
    margin: 0 5px;
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    min-width: 150px;
}

.instructions-card {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.instruction-title {
    color: var(--text-dark);
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--primary-color);
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
    width: 30px;
    height: 30px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
}

.manual-entry-section {
    background: rgba(67, 97, 238, 0.05);
    border-radius: 10px;
    padding: 1.25rem;
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.manual-title {
    color: var(--text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
}

.troubleshooting-section {
    background: rgba(108, 117, 125, 0.05);
    border-radius: 10px;
    padding: 1rem;
    border: 1px solid rgba(108, 117, 125, 0.1);
}

#debug-info {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    padding: 10px;
    font-family: monospace;
    font-size: 11px;
    max-height: 200px;
    overflow-y: auto;
}

#debug-content {
    white-space: pre-wrap;
    word-break: break-all;
}

@media (max-width: 768px) {
    .scanner-controls .btn {
        display: block;
        width: 100%;
        margin: 5px 0;
    }
    
    .instruction-step {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .step-number {
        align-self: center;
    }
}
</style>
@endsection