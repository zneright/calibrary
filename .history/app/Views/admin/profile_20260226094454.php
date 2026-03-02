<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        <?php 
                            $avatarPath = ($user['avatar']) ? base_url('uploads/avatars/'.$user['avatar']) : base_url('images/default-avatar.png'); 
                        ?>
                        <img src="<?= $avatarPath ?>" 
                             class="rounded-circle border border-3 border-white shadow-sm" 
                             style="width: 120px; height: 120px; object-fit: cover;" id="avatarPreview">

                        <form action="/admin/profile/upload-avatar" method="POST" enctype="multipart/form-data" id="avatarForm">
                            <?= csrf_field() ?>
                            <input type="file" name="avatar" id="avatarUpload" hidden onchange="this.form.submit()">
                            <label for="avatarUpload" class="position-absolute translate-middle badge rounded-pill bg-primary p-2 shadow-sm" style="top: 85%; left: 80%; cursor: pointer;">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </form>
                    </div>
                    
                    <h5 class="fw-bold mb-1"><?= esc($user['fullname']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($user['role']) ?></p>
                    <span class="badge bg-success px-3 py-2 rounded-pill">Account Active</span>
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <p class="mb-2 small text-muted"><i class="bi bi-envelope me-2"></i> <?= esc($user['email']) ?></p>
                        <p class="mb-2 small text-muted"><i class="bi bi-telephone me-2"></i> <?= esc($user['contact'] ?? 'No contact added') ?></p>
                        <p class="mb-0 small text-muted"><i class="bi bi-building me-2"></i> <?= esc($user['department']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Personal Information</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/admin/profile/update" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Full Name</label>
                                <input type="text" name="fullname" class="form-control" value="<?= esc($user['fullname']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Contact Number</label>
                                <input type="text" name="contact" class="form-control" value="<?= esc($user['contact']) ?>" placeholder="+63 XXX XXX XXXX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Department</label>
                                <input type="text" class="form-control bg-light" value="<?= esc($user['department']) ?>" readonly>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger">Security & Password</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/admin/profile/password" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold mb-0">Current Password</label>
                                    <a href="#" class="small text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                                </div>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mt-2 mb-1">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mt-2 mb-1">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-dark px-4">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-light border-0">
                <h6 class="modal-title fw-bold text-secondary" id="forgotPasswordModalLabel">
                    <i class="bi bi-shield-lock me-2"></i>Reset Password
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/admin/profile/request-reset-code" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4">Select where you would like us to send your password reset code to verify your identity.</p>
                    
                    <div class="form-check border rounded p-3 mb-3 shadow-sm" style="cursor: pointer;" onclick="document.getElementById('resetEmail').checked = true;">
                        <input class="form-check-input ms-1 mt-2" type="radio" name="reset_method" id="resetEmail" value="email" checked>
                        <label class="form-check-label ms-3 d-block w-100" for="resetEmail" style="cursor: pointer;">
                            <span class="d-block fw-bold text-dark small mb-1">Send code via Email</span>
                            <span class="d-block text-muted small"><i class="bi bi-envelope me-1"></i> <?= esc($user['email']) ?></span>
                        </label>
                    </div>

                    ///
                    <!-- <?php if(!empty($user['contact'])): ?>
                    <div class="form-check border rounded p-3 shadow-sm" style="cursor: pointer;" onclick="document.getElementById('resetSMS').checked = true;">
                        <input class="form-check-input ms-1 mt-2" type="radio" name="reset_method" id="resetSMS" value="sms">
                        <label class="form-check-label ms-3 d-block w-100" for="resetSMS" style="cursor: pointer;">
                            <span class="d-block fw-bold text-dark small mb-1">Send code via SMS</span>
                            <span class="d-block text-muted small"><i class="bi bi-phone me-1"></i> <?= esc($user['contact']) ?></span>
                        </label>
                    </div>
                    <?php endif; ?> -->

                </div>
                
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                        Send Code <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>