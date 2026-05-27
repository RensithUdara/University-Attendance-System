@extends('layouts.student')

@section('title', 'My Profile')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">My Profile 👤</h2>
                <p class="welcome-subtitle">Manage your account information and settings</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-user me-2"></i>
                    <span id="currentTime">{{ now()->format('l, F j, Y - h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-circle me-2"></i>Profile Picture
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="profile-picture-section">
                        <div class="profile-avatar-large student-avatar mb-3">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                     alt="{{ Auth::user()->name }}" class="profile-img">
                            @else
                                <i class="fas fa-user-graduate"></i>
                            @endif
                        </div>
                        <h4>{{ Auth::user()->name }}</h4>
                        <p class="text-muted">{{ Auth::user()->email }}</p>
                        <span class="badge bg-success">Student</span>
                        
                        @if(Auth::user()->student_id)
                        <div class="student-id mt-2">
                            <strong>Student ID:</strong> 
                            <code>{{ Auth::user()->student_id }}</code>
                        </div>
                        @endif
                        
                        <form action="{{ route('student.profile.picture') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                            @csrf
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Change Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                <div class="form-text">Max file size: 2MB. Supported formats: JPG, PNG, GIF</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-upload me-1"></i> Upload Picture
                            </button>
                        </form>
                        
                        @if(Auth::user()->profile_picture)
                        <form action="{{ route('student.profile.picture.remove') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i> Remove Picture
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap me-2"></i>Academic Info
                    </h3>
                </div>
                <div class="card-body">
                    <div class="academic-info">
                        <div class="info-item">
                            <div class="info-icon text-primary">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="info-content">
                                <strong>Enrolled Courses</strong>
                                <div>{{ Auth::user()->enrollments->count() }} courses</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon text-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <strong>Attendance Rate</strong>
                                <div>{{ Auth::user()->attendance_rate ?? 0 }}%</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon text-info">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="info-content">
                                <strong>Member Since</strong>
                                <div>{{ Auth::user()->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Settings -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit me-2"></i>Personal Information
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                           id="student_id" name="student_id" value="{{ old('student_id', Auth::user()->student_id) }}"
                                           {{ Auth::user()->student_id ? 'readonly' : '' }}>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if(Auth::user()->student_id)
                                    <div class="form-text">Student ID cannot be changed</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', Auth::user()->date_of_birth) }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                           id="department" name="department" value="{{ old('department', Auth::user()->department) }}"
                                           {{ Auth::user()->department ? 'readonly' : '' }}>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" name="bio" rows="3" 
                                      placeholder="Tell us about yourself, your interests, and academic goals...">{{ old('bio', Auth::user()->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.5s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lock me-2"></i>Change Password
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password *</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password *</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" name="new_password" required>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" 
                                           id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        <div class="password-requirements">
                            <small class="text-muted">Password must be at least 8 characters long and include uppercase, lowercase, numbers, and symbols.</small>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- QR Code Settings -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.6s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-qrcode me-2"></i>QR Code Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="qr-settings">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Your personal QR code can be used for quick attendance marking. Show this to lecturers when needed.
                        </div>
                        
                        <div class="text-center">
                            @if(Auth::user()->qr_code)
                                <div class="mb-3">
                                    {!! Auth::user()->qr_code !!}
                                </div>
                                <small class="text-muted">Scan this QR code for attendance</small>
                            @else
                                <p class="text-muted">No QR code generated yet.</p>
                            @endif
                        </div>
                        
                        <div class="form-actions text-center">
                            <a href="{{ route('student.generate-qr') }}" class="btn btn-primary">
                                <i class="fas fa-sync me-1"></i> Generate New QR Code
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-avatar-large {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 3rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.profile-avatar-large.student-avatar {
    background: var(--success-color);
}

.profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.student-id {
    background: rgba(76, 201, 240, 0.1);
    padding: 0.5rem;
    border-radius: 6px;
    margin: 1rem 0;
}

.academic-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.info-item:last-child {
    border-bottom: none;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: rgba(67, 97, 238, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-content {
    flex: 1;
}

.info-content strong {
    display: block;
    color: var(--text-dark);
    font-size: 0.9rem;
}

.info-content div {
    color: var(--text-light);
    font-size: 0.8rem;
}

.form-actions {
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
    text-align: right;
}

.password-requirements {
    background: rgba(67, 97, 238, 0.05);
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.qr-settings {
    text-align: center;
}

@media (max-width: 768px) {
    .profile-avatar-large {
        width: 120px;
        height: 120px;
        font-size: 2.5rem;
    }
    
    .info-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .form-actions {
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile picture preview
    const profilePictureInput = document.getElementById('profile_picture');
    const profileAvatar = document.querySelector('.profile-avatar-large');
    
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (profileAvatar.querySelector('img')) {
                        profileAvatar.querySelector('img').src = e.target.result;
                    } else {
                        profileAvatar.innerHTML = `<img src="${e.target.result}" alt="Profile Picture" class="profile-img">`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Date of birth validation
    const dobInput = document.getElementById('date_of_birth');
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            const minAgeDate = new Date(today.getFullYear() - 16, today.getMonth(), today.getDate());
            
            if (selectedDate > minAgeDate) {
                alert('You must be at least 16 years old.');
                this.value = '';
            }
        });
    }
});
</script>
@endsection
