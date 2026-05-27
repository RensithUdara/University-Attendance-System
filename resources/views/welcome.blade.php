<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>University Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .welcome-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .welcome-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: none;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card welcome-card">
                        <div class="card-body p-5">
                            <div class="text-center mb-5">
                                <h1 class="display-4 text-primary mb-3">
                                    <i class="fas fa-graduation-cap"></i>
                                    EduManage
                                </h1>
                                <p class="lead text-muted">University Attendance System</p>
                                <p class="text-muted">Manage students, lecturers, courses, and attendance in one place</p>
                            </div>

                            <!-- Login/Register Buttons -->
                            
                              
                                    <div class="d-grid">
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login
                                        </a>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>