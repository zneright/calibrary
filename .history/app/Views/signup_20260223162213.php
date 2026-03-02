<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CALIS v2.0</title>
    <link rel="icon" href="data:,">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(rgba(33, 37, 41, 0.85), rgba(33, 37, 41, 0.85)), 
                        url('/images/library_bg_2.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        
        .login-card {
            border: none;
            border-radius: 1rem;
            background-color: #ffffff;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px; 
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .form-control, .form-select {
            border-color: #dee2e6;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
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
                    <i class="bi bi-person-plus fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Create Account</h4>
                <p class="text-muted small">Join CALIS v2.0 Library</p>
            </div>

            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger p-3 small rounded-3 mb-4 border-0 shadow-sm">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/signup/store" method="POST">
                <?= csrf_field() ?>
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-secondary">Full Name</label>
                        <input type="text" name="fullname" class="form-control shadow-sm" placeholder="Juan Dela Cruz" value="<?= old('fullname') ?>" required>
                    </div>
                <div class="col-12">
                <label class="form-label fw-semibold small text-secondary">Email Address</label>
    <input type="email" name="email" class="form-control shadow-sm" placeholder="you@example.com" value="<?= old('email') ?>" required>
</div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Services</label>
                        <input type="text" name="user_id" class="form-control shadow-sm" placeholder="Data Bank and Library" value="<?= old('user_id') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Role</label>
                        <select name="role" class="form-select shadow-sm" required>
                            <option value="Borrower" selected>Borrower</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Password</label>
                        <input type="password" name="password" class="form-control shadow-sm" placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">Confirm</label>
                        <input type="password" name="confirm_password" class="form-control shadow-sm" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-check mt-4 mb-4">
                    <input class="form-check-input shadow-sm" type="checkbox" id="terms" required>
                    <label class="form-check-label small text-muted user-select-none" for="terms">
                        I agree to the <a href="#" class="text-primary fw-semibold text-decoration-none">Terms and Conditions</a>.
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold mb-3 shadow-sm rounded-3">
                    Register Now <i class="bi bi-check2-circle ms-1"></i>
                </button>

                <div class="text-center mt-4 pt-3 border-top border-light">
                    <p class="small text-muted mb-1">Already have an account?</p>
                    <a href="/login" class="text-decoration-none fw-bold text-primary">
                        <i class="bi bi-arrow-left small me-1"></i> Back to Sign In
                    </a>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>