@extends('layouts.lecturer')

@section('title', 'Create New Lecture')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Create New Lecture</h2>
                <p class="welcome-subtitle">Schedule a new lecture session for your course</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-plus-circle me-2"></i>
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
                        <i class="fas fa-chalkboard-teacher me-2"></i>Create New Lecture
                    </h3>
                    <a href="{{ route('lectures.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Lectures
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('lectures.store') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Lecture Basic Information -->
                            <div class="col-md-6">
                                <h5 class="section-title mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Lecture Information
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Lecture Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           placeholder="Enter lecture title" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" 
                                              placeholder="Enter lecture description, topics to be covered, etc." required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Select Course *</label>
                                    <select class="form-control @error('course_id') is-invalid @enderror" 
                                            id="course_id" name="course_id" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', request('course_id')) == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }} ({{ $course->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($courses->isEmpty())
                                        <div class="form-text text-danger">No courses are assigned to your lecturer account yet. Ask an admin to create or assign a course first.</div>
                                    @endif
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Lecture Details -->
                            <div class="col-md-6">
                                <h5 class="section-title mb-3">
                                    <i class="fas fa-cog me-2"></i>Lecture Details
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="schedule" class="form-label">Schedule Date & Time *</label>
                                            <input type="datetime-local" class="form-control @error('schedule') is-invalid @enderror" 
                                                   id="schedule" name="schedule" value="{{ old('schedule') }}" 
                                                   required>
                                            @error('schedule')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration (minutes) *</label>
                                            <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                                   id="duration" name="duration" value="{{ old('duration', 60) }}" 
                                                   min="1" max="240" required>
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="lesson_type" class="form-label">Lesson Type *</label>
                                            <select class="form-control @error('lesson_type') is-invalid @enderror" 
                                                    id="lesson_type" name="lesson_type" required>
                                                <option value="">Select Type</option>
                                                <option value="theory" {{ old('lesson_type') == 'theory' ? 'selected' : '' }}>Theory</option>
                                                <option value="practical" {{ old('lesson_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                                                <option value="lab" {{ old('lesson_type') == 'lab' ? 'selected' : '' }}>Lab</option>
                                                <option value="workshop" {{ old('lesson_type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                            </select>
                                            @error('lesson_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="room" class="form-label">Room/Location</label>
                                            <input type="text" class="form-control @error('room') is-invalid @enderror" 
                                                   id="room" name="room" value="{{ old('room') }}" 
                                                   placeholder="e.g., Room 101, Lab A">
                                            @error('room')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="info-alert">
                                    <div class="alert-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="alert-content">
                                        <strong>After creating the lecture</strong>, you can generate a unique QR code for students to scan and mark attendance.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-actions">
                                    <a href="{{ route('lectures.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Create Lecture & Generate QR Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-title {
    color: var(--text-dark);
    font-weight: 600;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.form-control {
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
}

.info-alert {
    background: rgba(76, 201, 240, 0.1);
    border: 1px solid rgba(76, 201, 240, 0.3);
    border-radius: 10px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-top: 1rem;
}

.alert-icon {
    color: var(--success-color);
    font-size: 1.2rem;
    margin-top: 0.1rem;
}

.alert-content {
    flex: 1;
    color: var(--text-dark);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum datetime to current time
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('schedule').min = now.toISOString().slice(0, 16);
    
    // Add real-time validation
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    
    titleInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            this.classList.remove('is-invalid');
        }
    });
    
    descriptionInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            this.classList.remove('is-invalid');
        }
    });
});
</script>
@endsection
