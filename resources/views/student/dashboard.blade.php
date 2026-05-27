@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                My Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $enrollments->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Attendance Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendancePercentage }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Lectures</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLectures }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Present Days</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $presentCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('student.scan-qr') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-qrcode me-2"></i>Scan QR Code
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('student.courses') }}" class="btn btn-success btn-block">
                                <i class="fas fa-book me-2"></i>My Courses
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('student.attendance') }}" class="btn btn-info btn-block">
                                <i class="fas fa-clipboard-check me-2"></i>Attendance History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Attendance</h6>
                    <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentAttendance->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendance as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>{{ $attendance->lecture->course->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center">No attendance records found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- My Courses -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">My Courses</h6>
                    <a href="{{ route('student.courses') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                    <div class="row">
                        @foreach($enrollments as $enrollment)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $enrollment->course->name }}</h5>
                                    <p class="card-text">
                                        <small class="text-muted">Code: {{ $enrollment->course->code }}</small><br>
                                        <small class="text-muted">Lecturer: {{ $enrollment->course->lecturer->name }}</small><br>
                                        <small class="text-muted">Credits: {{ $enrollment->course->credits }}</small>
                                    </p>
                                    <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center">You are not enrolled in any courses yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection