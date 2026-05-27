@extends('layouts.admin')

@section('title', 'Add New Course')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Add New Course 📚</h2>
                <p class="welcome-subtitle">Create a new course for the system</p>
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
                        <i class="fas fa-plus-circle me-2"></i>Create New Course
                    </h3>
                    <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Courses
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('courses.store') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Course Basic Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Course Information
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Course Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Enter course name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="code" class="form-label">Course Code *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" 
                                           placeholder="e.g., CS101, MATH202" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Course Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Enter course description" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Details -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-cog me-2"></i>Course Details
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="lecturer_id" class="form-label">Assigned Lecturer *</label>
                                    <select class="form-control @error('lecturer_id') is-invalid @enderror" 
                                            id="lecturer_id" name="lecturer_id" required>
                                        <option value="">Select Lecturer</option>
                                        @foreach($lecturers as $lecturer)
                                            <option value="{{ $lecturer->id }}" {{ old('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                                                {{ $lecturer->name }} ({{ $lecturer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lecturer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="semester" class="form-label">Semester *</label>
                                            <select class="form-control @error('semester') is-invalid @enderror" 
                                                    id="semester" name="semester" required>
                                                <option value="">Select Semester</option>
                                                @foreach($semesters as $key => $value)
                                                    <option value="{{ $key }}" {{ old('semester') == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('semester')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="credits" class="form-label">Credits *</label>
                                            <input type="number" class="form-control @error('credits') is-invalid @enderror" 
                                                   id="credits" name="credits" value="{{ old('credits', 3) }}" 
                                                   min="1" max="10" required>
                                            @error('credits')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Course Duration *</label>
                                            <input type="text" class="form-control @error('duration') is-invalid @enderror" 
                                                   id="duration" name="duration" value="{{ old('duration') }}" 
                                                   placeholder="e.g., 15 weeks, 1 semester" required>
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_students" class="form-label">Maximum Students</label>
                                            <input type="number" class="form-control @error('max_students') is-invalid @enderror" 
                                                   id="max_students" name="max_students" value="{{ old('max_students') }}" 
                                                   placeholder="Optional">
                                            @error('max_students')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('courses.index') }}" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Create Course
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
.form-label {
    font-weight: 600;
    color: var(--text-dark);
}
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
}
</style>
@endsection