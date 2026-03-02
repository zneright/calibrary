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
                        <img src="<?= base_url('images/default-avatar.jpg') ?>" 
                             alt="Profile" 
                             class="rounded-circle border border-3 border-white shadow-sm" 
                             style="width: 120px; height: 120px; min-width: 120px; min-height: 120px; object-fit: cover; background-color: #e9ecef; display: block;"
                             id="avatarPreview">

                        <form action="/borrower/profile/upload-avatar" method="POST" enctype="multipart/form-data" id="avatarForm">
                            <?= csrf_field() ?>
                            <input type="file" name="avatar" id="avatarUpload" accept="image/png, image/jpeg, image/jpg" hidden onchange="document.getElementById('avatarForm').submit();">
                            
                            <label for="avatarUpload" class="position-absolute translate-middle badge rounded-pill text-white p-2 shadow-sm" style="background-color: #1e3a8a; top: 85%; left: 80%; cursor: pointer;" title="Change Profile Photo">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </form>
                    </div>
                    
                    <h5 class="fw-bold mb-1" style="color: #1a2942;">Nishia Pinlac</h5>
                    <p class="text-muted small mb-3">Employee / Borrower</p>
                    
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Account Active</span>
                    
                    <hr class="my-4 text-secondary opacity-25">
                    
                    <div class="text-start">
                        <p class="mb-2 small text-muted"><i class="bi bi-envelope me-2" style="color: #1e3a8a;"></i> nishia.pinlac@calis.gov.ph</p>
                        <p class="mb-2 small text-muted"><i class="bi bi-telephone me-2" style="color: #1e3a8a;"></i> +63 912 345 6789</p>
                        <p class="mb-0 small text-muted"><i class="bi bi-building me-2" style="color: #1e3a8a;"></i> Data Bank and Library</p>
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
                                <input type="text" name="fullname" class="form-control shadow-sm" value="Nishia Pinlac" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Email Address</label>
                                <input type="email" name="email" class="form-control shadow-sm" value="nishia.pinlac@calis.gov.ph" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Contact Number</label>
                                <input type="text" name="contact" class="form-control shadow-sm" value="+63 912 345 6789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Department / Services</label>
                                <input type="text" name="department" class="form-control shadow-sm bg-light text-muted" value="Data Bank and Library" readonly>
                                <small class="text-muted" style="font-size: 0.7rem;">Please contact the Library Admin to change your department.</small>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn fw-semibold text-white shadow-sm" style="background-color: #1e3a8a;">Save Changes</button>
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
                                <label class="form-label fw-semibold small text-secondary">Current Password</label>
                                <input type="password" name="current_password" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">New Password</label>
                                <input type="password" name="new_password" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-dark fw-semibold shadow-sm">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>