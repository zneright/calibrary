<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary"><i class="bi bi-people-fill me-2 text-primary"></i>User Management</h5>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Add New User
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="usersTable">
                    <thead class="table-light align-middle text-center">
                        <tr>
                            <th>NO</th>
                            <th>USER ID</th>
                            <th class="text-start">FULL NAME & INFO</th>
                            <th>ROLE</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center">
                        <?php if (!empty($users)): foreach($users as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold"><?= esc($row['user_id']) ?></td>
                            <td class="text-start">
                                <div class="d-flex align-items-center">
                                    <img src="<?= base_url('uploads/avatars/' . ($row['avatar'] ?: 'default.png')) ?>" 
                                         style="width: 40px; height: 40px; cursor: pointer; object-fit: cover;" 
                                         class="rounded-circle me-2 border shadow-sm"
                                         onclick="openZoom('<?= base_url('uploads/avatars/' . ($row['avatar'] ?: 'default.png')) ?>')">
                                    <div>
                                        <span class="fw-bold"><?= esc($row['fullname']) ?></span><br>
                                        <small class="text-muted"><i class="bi bi-telephone"></i> <?= esc($row['contact'] ?? 'N/A') ?></small> <br>
                                        <small class="text-muted"><i class="bi bi-building"></i> <?= esc($row['department'] ?? 'N/A') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge <?= $row['role'] == 'Admin' ? 'bg-dark' : 'bg-info text-dark' ?>"><?= $row['role'] ?></span></td>
                            <td>
                                <?php $currentStatus = $row['status'] ?? 'Active'; ?>
                                <span class="badge <?= $currentStatus == 'Active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $currentStatus ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-warning btn-sm edit-user-btn shadow-sm" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-userid="<?= esc($row['user_id']) ?>"
                                            data-fullname="<?= esc($row['fullname']) ?>"
                                            data-contact="<?= esc($row['contact'] ?? '') ?>"
                                            data-email="<?= esc($row['email'] ?? '') ?>"
                                            data-dept="<?= esc($row['department'] ?? '') ?>"
                                            data-role="<?= esc($row['role']) ?>"
                                            title="Edit User">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    <?php if(($row['status'] ?? 'Active') == 'Active'): ?>
                                        <button class="btn btn-danger btn-sm deactivate-user-btn shadow-sm" data-id="<?= $row['id'] ?>" title="Deactivate Account">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-success btn-sm shadow-sm" onclick="reactivateUser(<?= $row['id'] ?>)" title="Reactivate">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageZoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 text-center">
                <img id="zoomedImg" src="" class="img-fluid rounded shadow-lg" style="max-height: 80vh; border: 4px solid white;">
                <div class="mt-3"><button class="btn btn-light rounded-pill px-4 fw-bold shadow" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-secondary">
                        <i class="bi bi-person-plus me-2 text-primary"></i>Add System User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="<?= base_url('admin/users/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" placeholder="e.g. Juan Dela Cruz" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">User ID <span class="text-danger">*</span></label>
                                <input type="text" name="user_id" class="form-control" placeholder="e.g. 2024-019" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact No. <span class="text-danger">*</span></label>
                                <input type="text" name="contact" class="form-control" placeholder="09xxxxxxxxx" required
                                       minlength="11" maxlength="11" pattern="\d{11}" title="Must be exactly 11 digits"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="Borrower" selected>Borrower</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Department</label>
                                <input type="text" name="department" class="form-control" placeholder="e.g. IT Department">
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Temporary Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" value="library123" required>
                                <small class="text-muted">Default: library123</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-save me-1"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-secondary">
                        <i class="bi bi-pencil-square me-2 text-warning"></i>Edit System User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="<?= base_url('admin/users/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" id="edit_fullname" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">User ID <span class="text-danger">*</span></label>
                                <input type="text" name="user_id" id="edit_userid" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact No. <span class="text-danger">*</span></label>
                                <input type="text" name="contact" id="edit_contact" class="form-control" required
                                       minlength="11" maxlength="11" pattern="\d{11}" title="Must be exactly 11 digits"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select name="role" id="edit_role" class="form-select" required>
                                    <option value="Borrower">Borrower</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Department</label>
                                <input type="text" name="department" id="edit_dept" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning shadow-sm fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Update Changes
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
$(document).ready(function () {
    // Initialize DataTables
    var table = $('#usersTable').DataTable({
        "autoWidth": false,
        "order": [], 
        "language": { 
            "search": "Search:",
            "emptyTable": "No users found in the database." 
        }
    });

    // click to zoom
    window.openZoom = function(src) {
        $('#zoomedImg').attr('src', src);
        var zoomModal = new bootstrap.Modal(document.getElementById('imageZoomModal'));
        zoomModal.show();
    };

    // Deactivate User
    $(document).on('click', '.deactivate-user-btn', function() {
        if(confirm('Are you sure you want to deactivate this user? They will stay in the database but lose access.')) {
            let id = $(this).data('id');
            let form = $('<form action="<?= base_url('admin/users/delete') ?>" method="POST">' +
            '<input type="hidden" name="id" value="' + id + '">' +
            '<?= csrf_field() ?>' +
            '</form>');
            $('body').append(form);
            form.submit();
        }
    });

    // Edit Modal
    $(document).on('click', '.edit-user-btn', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_userid').val($(this).data('userid'));
        $('#edit_fullname').val($(this).data('fullname'));
        $('#edit_contact').val($(this).data('contact'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_dept').val($(this).data('dept'));
        $('#edit_role').val($(this).data('role'));
        $('#editUserModal').modal('show');
    });
});

// Reactivate User
function reactivateUser(id) {
    if(confirm('Reactivate this user?')) {
        window.location.href = "<?= base_url('admin/users/reactivate/') ?>" + id;
    }
}
</script>
<?= $this->endSection() ?>