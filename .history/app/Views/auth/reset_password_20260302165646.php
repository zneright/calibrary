<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password | CALIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .reset-card { width: 100%; max-width: 400px; border: none; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="card reset-card shadow-lg p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">New Password</h4>
            <p class="text-muted small">Please enter your new secure password below.</p>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('auth/updateResetPassword') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= $token ?>">
            
            <div class="mb-3">
                <label class="form-label small fw-bold">New Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                Update Password <i class="bi bi-check-circle ms-1"></i>
            </button>
        </form>
    </div>
</body>
</html>