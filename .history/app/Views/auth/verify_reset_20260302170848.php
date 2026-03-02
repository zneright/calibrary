<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="container d-flex justify-content-center">
    <div class="card shadow-sm border-0 col-md-5 mt-5">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Verify Reset Code</h5>
            <form action="/admin/profile/process-reset" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Enter 6-Digit Code</label>
                    <input type="text" name="reset_code" class="form-control text-center fw-bold" maxlength="6" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>