<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary mb-0">
            <i class="bi bi-person-gear me-2 text-primary"></i>Profile Settings
        </h4>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 100px; height: 100px;">
<div class="mb-3 position-relative text-center d-inline-block">
    <img src="<?= base_url('images/default-avatar.jpg') ?>" 
         alt="Profile Avatar" 
         class="rounded-circle border border-3 border-white shadow-sm" 
         style="width: 120px; height: 120px; object-fit: cover;"
         id="avatarPreview">

    <form action="/admin/profile/upload-avatar" method="POST" enctype="multipart/form-data" id="avatarForm">
        <?= csrf_field() ?>
        <input type="file" name="avatar" id="avatarUpload" accept="image/png, image/jpeg, image/jpg" hidden onchange="document.getElementById('avatarForm').submit();">
        
        <label for="avatarUpload" class="position-absolute translate-middle badge rounded-pill bg-primary text-white p-2 shadow-sm" style="top: 85%; left: 75%; cursor: pointer;" title="Change Profile Photo">
            <i class="bi bi-camera-fill"></i>
        </label>
    </form>
</div>                    </div>
                    
                    <h5 class="fw-bold mb-1">Bien</h5>
                    <p class="text-muted small mb-3">System Administrator</p>
                    
                    <span class="badge bg-success px-3 py-2 rounded-pill">Account Active</span>
                    
                    <hr class="my-4 text-secondary">
                    
                    <div class="text-start">
                        <p class="mb-2 small text-muted"><i class="bi bi-envelope me-2"></i> admin@calis.edu.ph</p>
                        <p class="mb-2 small text-muted"><i class="bi bi-telephone me-2"></i> +63 912 345 6789</p>
                        <p class="mb-0 small text-muted"><i class="bi bi-building me-2"></i> Library IT Office</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-secondary">Personal Information</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/admin/profile/update" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name</label>
                                <input type="text" name="fullname" class="form-control" value="Bien" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address</label>
                                <input type="email" name="email" class="form-control" value="admin@calis.edu.ph" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Contact Number</label>
                                <input type="text" name="contact" class="form-control" value="+63 912 345 6789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Department</label>
                                <input type="text" name="department" class="form-control" value="Library IT Office" readonly>
                                <small class="text-muted" style="font-size: 0.7rem;">Only super-admins can change departments.</small>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-secondary">Security & Password</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/admin/profile/password" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-dark px-4 shadow-sm">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div> </div>
</div>
<?= $this->endSection() ?>