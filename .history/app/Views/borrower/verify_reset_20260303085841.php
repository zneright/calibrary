<?php if (session()->get('id')): ?>
    <?= $this->extend('layouts/borrower') ?>
    <?= $this->section('content') ?>
<?php else: ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verify Reset - CALIS v2.0</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
<?php endif; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="py-3 text-center text-white" style="background-color: #1a2942;">
                    <h5 class="fw-bold mb-0">Verify Identity</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('process-reset') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4 text-center">
                            <label class="form-label small fw-bold text-muted mb-2">Enter 6-Digit Code</label>
                            <input type="text" name="reset_code" 
                                   class="form-control form-control-lg text-center fw-bold" 
                                   style="letter-spacing: 0.4rem; font-size: 1.5rem;"
                                   maxlength="6" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="8">
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="8">
                        </div>

                        <button type="submit" class="btn text-white w-100 py-2" style="background-color: #1e3a8a;">
                            Verify & Update Password
                        </button>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('login') ?>" class="text-muted small text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (session()->get('id')): ?>
    <?= $this->endSection() ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>