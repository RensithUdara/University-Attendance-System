@extends('layouts.lecturer')

@section('title', 'My Lectures')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">My Lectures 📚</h2>
                <p class="welcome-subtitle">Manage and track your lecture sessions</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
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
                        <div class="stat-title">Total Lectures</div>
                        <div class="stat-value">{{ $lectures->count() }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-chalkboard me-1"></i>
                            <span>All lectures</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-success animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Upcoming</div>
                        <div class="stat-value">{{ $lectures->where('status', 'upcoming')->count() }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-clock me-1"></i>
                            <span>Scheduled</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-warning animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Ongoing</div>
                        <div class="stat-value">{{ $lectures->where('status', 'ongoing')->count() }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-play me-1"></i>
                            <span>In progress</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card card-info animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div class="stat-title">Completed</div>
                        <div class="stat-value">{{ $lectures->where('status', 'completed')->count() }}</div>
                        <div class="stat-trend">
                            <i class="fas fa-check me-1"></i>
                            <span>Finished</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
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
                        <i class="fas fa-list me-2"></i>All Lectures
                    </h3>
                    <a href="{{ route('lectures.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create New Lecture
                    </a>
                </div>
                <div class="card-body">
                    @if($lectures->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Title</th>
                                    <th>Schedule</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>QR Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lectures as $lecture)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-icon me-3">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $lecture->course->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $lecture->course->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $lecture->title }}</strong>
                                        @if($lecture->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($lecture->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $lecture->formatted_schedule }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $lecture->duration }} minutes</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-capitalize">
                                            <i class="fas fa-{{ $lecture->lesson_type === 'theory' ? 'book' : ($lecture->lesson_type === 'practical' ? 'flask' : 'laptop') }} me-1"></i>
                                            {{ $lecture->lesson_type }}
                                        </span>
                                        @if($lecture->room)
                                        <br>
                                        <small class="text-muted">{{ $lecture->room }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'upcoming' => 'warning',
                                                'ongoing' => 'success', 
                                                'completed' => 'secondary'
                                            ][$lecture->status];
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }} text-capitalize">
                                            <i class="fas fa-{{ $lecture->status === 'upcoming' ? 'clock' : ($lecture->status === 'ongoing' ? 'play' : 'check') }} me-1"></i>
                                            {{ $lecture->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($lecture->qr_code)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i> Generated
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-times me-1"></i> Not Generated
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('lectures.show', $lecture) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('lectures.generate-qr', $lecture) }}" class="btn btn-sm btn-success" title="QR Code">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            <a href="{{ route('lectures.edit', $lecture) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('lectures.destroy', $lecture) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this lecture?')"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-chalkboard-teacher empty-icon"></i>
                        <h4>No Lectures Found</h4>
                        <p>Get started by creating your first lecture.</p>
                        <a href="{{ route('lectures.create') }}" class="btn btn-primary">Create First Lecture</a>
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
    color: var(--primary-color);
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
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
    .action-buttons {
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
        margin-bottom: 0.25rem;
    }
}
</style>
@endsection