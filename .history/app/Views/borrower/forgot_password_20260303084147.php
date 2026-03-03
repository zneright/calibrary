<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CALIS v2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px; border-radius: 1rem;">
        <h4 class="fw-bold text-center mb-3">Reset Password</h4>
        <p class="text-muted text-center small mb-4">Enter your email and we'll send you a 6-digit code.</p>
        
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger small p-2"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('send-reset-code') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="example@email.com" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Send Reset Code</button>
            <div class="text-center mt-3">
                <a href="<?= base_url('login') ?>" class="text-decoration-none small">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>