<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CALIS v2.0</title>
    <link rel="icon" href="data:,">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            /* Modern dark slate overlay (matches your dashboard sidebar) */
            background: linear-gradient(rgba(33, 37, 41, 0.85), rgba(33, 37, 41, 0.85)), 
                        url('/images/library_bg_2.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Uses the system default modern sans-serif font automatically */
        }
        
        /*--- Clean, Modern Card Styling ---*/
        .login-card {
            border: none;
            border-radius: 1rem; /* Smoother, larger rounded corners */
            background-color: #ffffff;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
        }

        /*--- Logo Icon Circle ---*/
        .icon-circle {
            width: 70px;
            height: 70px;
            background-color: rgba(13, 110, 253, 0.1); /* Soft primary blue background */
            color: #0d6efd; /* Bootstrap Primary Blue */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        /*--- Form Elements ---*/
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: #6c757d;
        }
        .form-control {
            border-left: none;
        }
        .form-control:focus {
            border-color: #dee2e6; /* Keep border neutral on focus */
            box-shadow: none; /* Remove harsh default glow */
        }
        /* Create a custom bottom border highlight on focus instead */
        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-radius: 0.375rem;
        }
        
        /*--- Custom Button ---*/
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 0.6rem 1.2rem;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card login-card p-2"> 
        <div class="card-body p-4 p-sm-5">
            
            <div class="text-center mb-4">
                <div class="icon-circle mb-3">
                    <i class="bi bi-book-half fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">CALIS v2.0</h4>
                <p class="text-muted small">Library Management System</p>
            </div>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success p-2 text-center small rounded-3 mb-4 border-0 shadow-sm">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger p-2 text-center small rounded-3 mb-4 border-0 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/login/attempt" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">ID Number or Email</label>
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
    <input type="text" name="username" class="form-control" placeholder="ID or Email Address" required autofocus>
</div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-semibold small text-secondary mb-0">Password</label>
                        <a href="#" class="text-decoration-none small text-primary fw-semibold">Forgot Password?</a>
                    </div>
                    <div class="input-group shadow-sm rounded mt-2">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input shadow-sm" type="checkbox" id="rememberMe">
                    <label class="form-check-label small text-muted user-select-none" for="rememberMe">
                        Remember me on this device
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold mb-3 shadow-sm rounded-3">
                    Sign In <i class="bi bi-arrow-right ms-1"></i>
                </button>

                <div class="text-center mt-4 pt-3 border-top border-light">
                    <p class="small text-muted mb-1">Don't have an account?</p>
                    <a href="/signup" class="text-decoration-none fw-bold text-primary">
                        Create an Account
                    </a>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>