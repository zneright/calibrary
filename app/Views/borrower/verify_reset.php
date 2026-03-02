<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="py-3 text-center text-white" style="background-color: #1a2942;">
                    <h5 class="fw-bold mb-0">Verify Identity</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('borrower/profile/process-reset') ?>" method="POST">
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>