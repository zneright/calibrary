<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 text-center">
                    <h5 class="fw-bold mb-0">Verify Reset Code</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger small"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/profile/process-reset') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4 text-center">
                            <label class="form-label small fw-bold text-muted mb-2">6-Digit Verification Code</label>
                            <input type="text" name="reset_code" 
                                   class="form-control form-control-lg text-center fw-bold letter-spacing-lg" 
                                   placeholder="000000" maxlength="6" required autofocus>
                            <div class="form-text mt-2 small">Check your email for the code sent to your inbox.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            Update Password <i class="bi bi-check-circle ms-1"></i>
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <a href="<?= base_url('admin/profile') ?>" class="small text-decoration-none">Cancel and return to profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-lg {
        letter-spacing: 0.5rem;
        font-size: 1.5rem;
    }
</style>
<?= $this->endSection() ?>