<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="py-4 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #1a2942 0%, #0f172a 100%);">
    <div class="container d-flex align-items-center">
        <div class="bg-white bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
            <i class="bi bi-person-gear fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1">My Profile Settings</h3>
            <p class="mb-0 text-white-50 small">Manage your personal information and account security.</p>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 text-center sticky-top" style="top: 80px;">
                <div class="card-body p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        <?php 
                            $avatarPath = (!empty($user['avatar']) && file_exists(FCPATH . 'uploads/avatars/' . $user['avatar'])) 
                                ? base_url('uploads/avatars/' . $user['avatar']) 
                                : base_url('images/default-avatar.png'); 
                        ?>
                        <img src="<?= $avatarPath ?>" 
                             alt="Profile" 
                             class="rounded-circle border border-3 border-white shadow-sm" 
                             style="width: 120px; height: 120px; object-fit: cover;"
                             id="avatarPreview">

                        <form action="/borrower/profile/upload-avatar" method="POST" enctype="multipart/form-data" id="avatarForm">
                            <?= csrf_field() ?>
                            <input type="file" name="avatar" id="avatarUpload" accept="image/*" hidden onchange="this.form.submit();">
                            <label for="avatarUpload" class="position-absolute translate-middle badge rounded-pill text-white p-2 shadow-sm" style="background-color: #1e3a8a; top: 85%; left: 80%; cursor: pointer;">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </form>
                    </div>
                    
                    <h5 class="fw-bold mb-1" style="color: #1a2942;"><?= esc($user['fullname'] ?? '') ?></h5>
                    <p class="text-muted small mb-3"><?= esc($user['role'] ?? 'Borrower') ?></p>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i> Account Active
                    </span>
                    
                    <hr class="my-4 text-secondary opacity-25">
                    
                    <div class="text-start">
                        <p class="mb-2 small text-muted"><i class="bi bi-envelope me-2" style="color: #1e3a8a;"></i> <?= esc($user['email'] ?? '') ?></p>
                        <p class="mb-2 small text-muted"><i class="bi bi-telephone me-2" style="color: #1e3a8a;"></i> <?= esc($user['contact'] ?? 'None Provided') ?></p>
                        <p class="mb-0 small text-muted"><i class="bi bi-building me-2" style="color: #1e3a8a;"></i> <?= esc($user['department'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold" style="color: #1a2942;">Personal Information</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/borrower/profile/update" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Full Name</label>
                                <input type="text" name="fullname" class="form-control" value="<?= esc($user['fullname'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Contact Number</label>
                                <input type="text" name="contact" class="form-control" value="<?= esc($user['contact'] ?? '') ?>" 
                                       placeholder="09xxxxxxxxx" required minlength="11" maxlength="11" pattern="\d{11}" title="Must be exactly 11 digits"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Department / Services</label>
                                <input type="text" class="form-control bg-light text-muted" value="<?= esc($user['department'] ?? '') ?>" readonly>
                                <small class="text-muted" style="font-size: 0.7rem;">Contact admin to change your department.</small>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn text-white px-4 fw-semibold shadow-sm btn-loading" style="background-color: #1e3a8a;">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold" style="color: #1a2942;">Security & Password</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/borrower/profile/password" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <label class="form-label fw-semibold small text-secondary mb-0">Current Password</label>
                                    <a href="#" class="small text-decoration-none fw-semibold" style="color: #1e3a8a;" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot password?</a>
                                </div>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">New Password</label>
                                <input type="password" name="new_password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-dark fw-semibold shadow-sm btn-loading">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h6 class="modal-title fw-bold" style="color: #1a2942;">
                    <i class="bi bi-shield-lock me-2"></i>Reset Password
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?= base_url('borrower/profile/request-reset-code') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4">We will send a 6-digit verification code to your registered email to verify your identity.</p>
                    
                    <div class="form-check border rounded p-3 mb-3 shadow-sm" style="cursor: pointer;" onclick="document.getElementById('resetEmail').checked = true;">
                        <input class="form-check-input ms-1 mt-2" type="radio" name="reset_method" id="resetEmail" value="email" checked>
                        <label class="form-check-label ms-3 d-block w-100" for="resetEmail" style="cursor: pointer;">
                            <span class="d-block fw-bold text-dark small mb-1">Send code via Email</span>
                            <span class="d-block text-muted small"><i class="bi bi-envelope me-1"></i> <?= esc($user['email']) ?></span>
                        </label>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white btn-sm px-3" style="background-color: #1e3a8a;">
                        Send Code <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('form').on('submit', function() {
        let btn = $(this).find('button[type="submit"]');
        if (btn.prop('disabled')) return false; 
        
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...');
        btn.prop('disabled', true);
    });
});
</script>
<?= $this->endSection() ?>