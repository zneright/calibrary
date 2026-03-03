<?php 
    // Determine layout dynamically
    $isLoggedIn = session()->get('id');
    $role = session()->get('role');
    
    if ($isLoggedIn) {
        // Use existing layouts based on role
        echo ($role === 'Admin') ? $this->extend('layouts/admin') : $this->extend('layouts/borrower');
        echo $this->section('content');
    } else {
        // Fallback for Guests (Forgot Password flow)
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
        body { 
            background: linear-gradient(rgba(33, 37, 41, 0.85), rgba(33, 37, 41, 0.85)), url('/images/library_bg_2.jpg') no-repeat center center fixed; 
            background-size: cover; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .unified-code-input {
            letter-spacing: 0.5rem;
            font-size: 1.75rem;
            border-radius: 0.75rem;
            border: 2px solid #e9ecef;
        }
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

                    <div id="dynamicErrorAlert" class="alert alert-danger border-0 shadow-sm small py-2 d-none"></div>

                    <form id="resetForm" action="<?= base_url('process-reset') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div id="step-1">
                            <div class="mb-4 text-center">
                                <label class="form-label small fw-bold text-secondary mb-2">6-Digit Verification Code</label>
                                <input type="text" id="reset_code" name="reset_code" 
                                       class="form-control form-control-lg text-center fw-bold unified-code-input" 
                                       placeholder="000000" maxlength="6" required autofocus>
                            </div>
                            
                            <button type="button" id="verifyCodeBtn" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill" style="background-color: #1e3a8a; border-color: #1e3a8a;">
                                Verify Code <i class="bi bi-arrow-right-short ms-1"></i>
                            </button>
                        </div>

                        <div id="step-2" class="d-none">
                            <div class="alert alert-success small py-2 mb-4 text-center border-0 shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Code verified! You may now set a new password.
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                    <input type="password" id="new_password" name="new_password" class="form-control border-start-0" minlength="8" placeholder="At least 8 characters">
                                </div>
                            </div>
        <div class="text-center mt-4 pt-3 border-top border-light">
                                    <a href="<?= base_url('cancel-reset') ?>" class="text-muted small text-decoration-none fw-semibold">
                                        <i class="bi bi-x-circle me-1"></i> Cancel Password Reset
                                    </a>
                                </div>

                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm rounded-pill">
                                Update Password <i class="bi bi-check-lg ms-1"></i>
                            </button>
                        </div>
                        
                        <div class="text-center mt-4 pt-3 border-top border-light">
                            <?php if ($isLoggedIn): ?>
                                <a href="<?= base_url($role === 'Admin' ? 'admin/profile' : 'borrower/profile') ?>" class="text-muted small text-decoration-none fw-semibold">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
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

<script>
    document.getElementById('verifyCodeBtn').addEventListener('click', async function() {
        const codeInput = document.getElementById('reset_code').value;
        const errorAlert = document.getElementById('dynamicErrorAlert');
        const btn = this;

        // Basic frontend validation
        if(codeInput.length !== 6) {
            errorAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Please enter the full 6-digit code.';
            errorAlert.classList.remove('d-none');
            return;
        }

        // UX: Show loading state
        const originalBtnText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
        btn.disabled = true;
        errorAlert.classList.add('d-none');

        try {
            // Grab the form data (this automatically includes the CI4 CSRF token + the reset code)
            const formData = new FormData(document.getElementById('resetForm'));

            // Send AJAX request to our new verification endpoint
            const response = await fetch('<?= base_url('verify-reset-code-ajax') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const result = await response.json();

            // Refresh CSRF token dynamically if CodeIgniter returned a new one
            if (result.csrf_hash) {
                document.querySelector('input[name="<?= csrf_token() ?>"]').value = result.csrf_hash;
            }

            if (result.success) {
                // SUCCESS: Hide step 1, show step 2!
                document.getElementById('step-1').classList.add('d-none');
                document.getElementById('step-2').classList.remove('d-none');
                
                // Make password fields required now that they are visible
                document.getElementById('new_password').setAttribute('required', 'required');
                document.getElementById('confirm_password').setAttribute('required', 'required');
                document.getElementById('reset_code').setAttribute('readonly', 'readonly'); // Lock the code input
            } else {
                // FAILED: Show error message
                errorAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> ' + result.message;
                errorAlert.classList.remove('d-none');
                btn.innerHTML = originalBtnText;
                btn.disabled = false;
            }
        } catch (error) {
            errorAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Connection error. Please try again.';
            errorAlert.classList.remove('d-none');
            btn.innerHTML = originalBtnText;
            btn.disabled = false;
        }
    });
</script>

<?php if ($isLoggedIn): ?>
    <?= $this->endSection() ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>