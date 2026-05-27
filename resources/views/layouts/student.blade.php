<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - EduManage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body class="dashboard-body">
    <div class="page-loader" id="pageLoader" aria-live="polite" aria-label="Loading page">
        <div class="page-loader-card">
            <div class="page-loader-mark">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="page-loader-ring"></div>
            <div class="page-loader-text">Loading EduManage</div>
            <div class="page-loader-bar"><span></span></div>
        </div>
    </div>

    <!-- Student Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top dashboard-navbar student-navbar">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="fas fa-graduation-cap me-2"></i>
                <span class="brand-text">EduManage</span>
                <span class="role-badge">Student</span>
            </a>

            <!-- Mobile toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation items -->
            <div class="collapse navbar-collapse" id="studentNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('student/dashboard') ? 'active' : '' }}" 
                           href="{{ route('student.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('student/courses*') ? 'active' : '' }}" 
                           href="{{ route('student.courses') }}">
                            <i class="fas fa-book me-1"></i>
                            My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('student/attendance*') ? 'active' : '' }}" 
                           href="{{ route('student.attendance') }}">
                            <i class="fas fa-clipboard-check me-1"></i>
                            My Attendance
                        </a>
                    </li>
                    
                </ul>

                <!-- User dropdown -->
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar student-avatar">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="user-info">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="user-role badge bg-success">Student</span>
                            </div>
                        </a>
                        <!-- Update the dropdown menu in student layout -->
<ul class="dropdown-menu dropdown-menu-end">
    <li>
        <a class="dropdown-item" href="{{ route('student.profile') }}">
            <i class="fas fa-user me-2"></i>My Profile
        </a>
    </li>
    
    <li><hr class="dropdown-divider"></li>
    <li>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="dropdown-item">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
        </form>
    </li>
</ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page header -->
            <div class="page-header animate-fade-in">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-title">@yield('title')</h1>
                        @if(isset($subtitle))
                            <p class="page-subtitle">@yield('subtitle')</p>
                        @endif
                    </div>
                    <div class="col-auto">
                        @yield('header-actions')
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate-slide-in-right" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <div class="flex-grow-1">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate-slide-in-right" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div class="flex-grow-1">
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show animate-slide-in-right" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div class="flex-grow-1">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <!-- Main content -->
            <div class="content-area animate-fade-in-up">
                @yield('content')
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
