@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">Manage Users</h2>
                <p class="welcome-subtitle">Add, edit, and manage system users</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-users me-2"></i>
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
                        <i class="fas fa-users me-2"></i>User Management
                    </h3>
                    <div>
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="fas fa-user-plus me-1"></i> Add Student
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLecturerModal">
                            <i class="fas fa-chalkboard-teacher me-1"></i> Add Lecturer
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-small {{ $user->role === 'student' ? 'student-avatar' : 'lecturer-avatar' }} me-3">
                                                <i class="fas fa-{{ $user->role === 'student' ? 'user-graduate' : 'user-tie' }}"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'student' ? 'success' : ($user->role === 'lecturer' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate me-2"></i>Add New Student
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="role" value="student">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control @if(old('role') === 'student') @error('name') is-invalid @enderror @endif" name="name" value="{{ old('role') === 'student' ? old('name') : '' }}" required placeholder="Enter student's full name">
                        @if(old('role') === 'student')
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control @if(old('role') === 'student') @error('email') is-invalid @enderror @endif" name="email" value="{{ old('role') === 'student' ? old('email') : '' }}" required placeholder="Enter student's email">
                        @if(old('role') === 'student')
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control @if(old('role') === 'student') @error('password') is-invalid @enderror @endif" name="password" required placeholder="Enter password (min 8 characters)">
                        @if(old('role') === 'student')
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('role') === 'student' ? old('phone') : '' }}" placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Enter address">{{ old('role') === 'student' ? old('address') : '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @if(old('role') === 'student') @error('date_of_birth') is-invalid @enderror @endif" name="date_of_birth" value="{{ old('role') === 'student' ? old('date_of_birth') : '' }}">
                        @if(old('role') === 'student')
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Lecturer Modal -->
<div class="modal fade" id="addLecturerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Add New Lecturer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="role" value="lecturer">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control @if(old('role') === 'lecturer') @error('name') is-invalid @enderror @endif" name="name" value="{{ old('role') === 'lecturer' ? old('name') : '' }}" required placeholder="Enter lecturer's full name">
                        @if(old('role') === 'lecturer')
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control @if(old('role') === 'lecturer') @error('email') is-invalid @enderror @endif" name="email" value="{{ old('role') === 'lecturer' ? old('email') : '' }}" required placeholder="Enter lecturer's email">
                        @if(old('role') === 'lecturer')
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control @if(old('role') === 'lecturer') @error('password') is-invalid @enderror @endif" name="password" required placeholder="Enter password (min 8 characters)">
                        @if(old('role') === 'lecturer')
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('role') === 'lecturer' ? old('phone') : '' }}" placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Enter address">{{ old('role') === 'lecturer' ? old('address') : '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @if(old('role') === 'lecturer') @error('date_of_birth') is-invalid @enderror @endif" name="date_of_birth" value="{{ old('role') === 'lecturer' ? old('date_of_birth') : '' }}">
                        @if(old('role') === 'lecturer')
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Lecturer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.user-avatar-small {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}
</style>

@if($errors->any() && in_array(old('role'), ['student', 'lecturer'], true))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalId = @json(old('role') === 'student' ? 'addStudentModal' : 'addLecturerModal');
    const modalElement = document.getElementById(modalId);

    if (modalElement && window.bootstrap?.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }
});
</script>
@endif
@endsection
