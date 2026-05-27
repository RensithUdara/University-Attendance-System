@extends('layouts.lecturer')

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
                        <div class="profile-avatar-large lecturer-avatar mb-3">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                     alt="{{ Auth::user()->name }}" class="profile-img">
                            @else
                                <i class="fas fa-user-tie"></i>
                            @endif
                        </div>
                        <h4>{{ Auth::user()->name }}</h4>
                        <p class="text-muted">{{ Auth::user()->email }}</p>
                        <span class="badge bg-warning">Lecturer</span>
                        
                        <form action="{{ route('lecturer.profile.picture') }}" method="POST" enctype="multipart/form-data" class="mt-4">
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
                        <form action="{{ route('lecturer.profile.picture.remove') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i> Remove Picture
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Teaching Statistics -->
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Teaching Stats
                    </h3>
                </div>
                <div class="card-body">
                    <div class="teaching-stats">
                        <div class="stat-item">
                            <div class="stat-icon text-primary">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ Auth::user()->courses->count() }}</div>
                                <div class="stat-label">Courses</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon text-success">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">{{ Auth::user()->total_students ?? 0 }}</div>
                                <div class="stat-label">Students</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon text-info">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value">Member since</div>
                                <div class="stat-label">{{ Auth::user()->created_at->format('M d, Y') }}</div>
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
                    <form action="{{ route('lecturer.profile.update') }}" method="POST">
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
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                           id="department" name="department" value="{{ old('department', Auth::user()->department) }}">
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
                                      placeholder="Tell us about your teaching experience and expertise...">{{ old('bio', Auth::user()->bio) }}</textarea>
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
                    <form action="{{ route('lecturer.password.update') }}" method="POST">
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

.profile-avatar-large.lecturer-avatar {
    background: var(--warning-color);
}

.profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.teaching-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: rgba(67, 97, 238, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1.1rem;
}

.stat-label {
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

@media (max-width: 768px) {
    .profile-avatar-large {
        width: 120px;
        height: 120px;
        font-size: 2.5rem;
    }
    
    .stat-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
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
});
</script>
@endsection