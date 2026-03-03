<?php 
    $isLoggedIn = session()->get('id');
    $role = session()->get('role');
    
    if ($isLoggedIn) {
        // These will work as long as the layout files are in app/Views/layouts/
        echo ($role === 'Admin') ? $this->extend('layouts/admin') : $this->extend('layouts/borrower');
        echo $this->section('content');
    } else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity - CALIS v2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(rgba(33, 37, 41, 0.85), rgba(33, 37, 41, 0.85)), url('/images/library_bg_2.jpg') no-repeat center center fixed; background-size: cover; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body class="bg-light">
<?php } ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 1rem;">
                <div class="py-4 text-center text-white" style="background-color: #1a2942;">
                    <div class="mb-2">
                        <i class="bi bi-shield-lock fs-1 text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Verify Identity</h5>
                    <p class="small mb-0 text-white-50">Secure Password Recovery</p>
                </div>
                
                <div class="card-body p-4 p-sm-5">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger border-0 shadow-sm small py-2">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('process-reset') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4 text-center">
                            <label class="form-label small fw-bold text-secondary mb-2">6-Digit Verification Code</label>
                            <input type="text" name="reset_code" 
                                   class="form-control form-control-lg text-center fw-bold unified-code-input" 
                                   placeholder="000000" maxlength="6" required autofocus>
                            <div class="form-text mt-2 small">Check your email for the code.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" name="new_password" class="form-control border-start-0" required minlength="8" placeholder="At least 8 characters">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-circle"></i></span>
                                <input type="password" name="confirm_password" class="form-control border-start-0" required minlength="8" placeholder="Repeat password">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill" style="background-color: #1e3a8a; border-color: #1e3a8a;">
                            Update Password <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                        
                        <div class="text-center mt-4 pt-3 border-top border-light">
                            <?php if ($isLoggedIn): ?>
                                <a href="<?= base_url($role === 'Admin' ? 'admin/profile' : 'borrower/profile') ?>" class="text-muted small text-decoration-none fw-semibold">
                                    <i class="bi bi-x-circle me-1"></i> Cancel and Return
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('login') ?>" class="text-primary small text-decoration-none fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Sign In
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .unified-code-input {
        letter-spacing: 0.5rem;
        font-size: 1.75rem;
        border-radius: 0.75rem;
        border: 2px solid #e9ecef;
    }
    .unified-code-input:focus {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 0.25rem rgba(30, 58, 138, 0.15);
    }
</style>

<?php if ($isLoggedIn): ?>
    <?= $this->endSection() ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>