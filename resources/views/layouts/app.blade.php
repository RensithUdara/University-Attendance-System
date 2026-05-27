<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - EduManage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-body">
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

    <!-- Layout for guest users (login/register pages) -->
    <div class="auth-container">
        <!-- Background elements -->
        <div class="auth-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
        
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-5">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card glass-card animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="card-body p-4 p-md-5">
                            <!-- Brand -->
                            <div class="text-center mb-4">
                                <div class="brand-logo animate-bounce-in">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h2 class="auth-title fw-bold mb-2">EduManage</h2>
                                <p class="auth-subtitle">University Attendance System</p>
                            </div>

                            <!-- Alerts -->
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show glass-card mb-4 animate-slide-in-right" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <div class="flex-grow-1">
                                            {{ session('success') }}
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                                    </div>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show glass-card mb-4 animate-slide-in-right" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <div class="flex-grow-1">
                                            {{ session('error') }}
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                                    </div>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show glass-card mb-4 animate-slide-in-right" role="alert">
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
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                                    </div>
                                </div>
                            @endif

                            <!-- Content -->
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
