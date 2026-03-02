<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-people-fill me-2 text-primary"></i>User Management
            </h5>
            
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Add New User
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="usersTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.9rem;">
                        <tr>
                            <th width="5%">NO</th>
                            <th>USER ID</th>
                            <th class="text-start">FULL NAME</th>
                            <th>ROLE</th>
                            <th>DATE REGISTERED</th>
                            <th>STATUS</th>
                            <th width="120">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        
                        <tr>
                            <td>1</td>
                            <td class="fw-bold text-secondary">ADMIN-001</td>
                            <td class="text-start fw-bold text-dark">
                                Bien <br>
                                <small class="text-muted fw-normal">System Administrator</small>
                            </td>
                            <td><span class="badge bg-dark">Admin</span></td>
                            <td>Feb 01, 2026</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
<button class="btn btn-warning btn-sm edit-user-btn" 
            title="Edit"
            data-bs-toggle="modal" 
            data-bs-target="#editUserModal"
            data-id="<?= $row['id'] ?>"
            data-fullname="<?= esc($row['fullname']) ?>"
            data-userid="<?= esc($row['user_id']) ?>"
            data-role="<?= $row['role'] ?>"
            data-dept="<?= esc($row['department'] ?? '') ?>">
        <i class="bi bi-pencil"></i>
    </button>                                <button class="btn btn-danger btn-sm" title="Disable Account" disabled><i class="bi bi-person-x"></i></button>
                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td class="fw-bold text-secondary">2026-0451</td>
                            <td class="text-start fw-semibold text-primary">
                                Nishia Pinlac <br>
                                <small class="text-muted fw-normal">Student Trainee - DBLS</small>
                            </td>
                            <td><span class="badge bg-info text-dark">Borrower</span></td>
                            <td>Feb 18, 2026</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-danger btn-sm" title="Disable Account"><i class="bi bi-person-x"></i></button>
                            </td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td class="fw-bold text-secondary">2026-0452</td>
                            <td class="text-start fw-semibold text-primary">
                                Renz Jericho Buday <br>
                                <small class="text-muted fw-normal">Student Trainee - DBLS</small>
                            </td>
                            <td><span class="badge bg-info text-dark">Borrower</span></td>
                            <td>Feb 19, 2026</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <button class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-danger btn-sm" title="Disable Account"><i class="bi bi-person-x"></i></button>
                            </td>
                        </tr>
                        
                        <?php if (!empty($users)): ?>
                            <?php foreach($users as $i => $row): ?>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-secondary">
                    <i class="bi bi-person-plus me-2"></i>Add System User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/admin/users/store" method="POST">
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
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="Borrower" selected>Borrower</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Department / Info</label>
                            <input type="text" name="department" class="form-control" placeholder="e.g. IT Department">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Temporary Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" value="library123" required>
                            <small class="text-muted">Default: library123</small>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
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
                    <i class="bi bi-pencil-square me-2"></i>Edit System User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/admin/users/update" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="fullname" id="edit_fullname" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">User ID</label>
                            <input type="text" name="user_id" id="edit_userid" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <option value="Borrower">Borrower</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Department / Info</label>
                            <input type="text" name="department" id="edit_dept" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning shadow-sm fw-bold">
                        Update Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<script>
$(document).ready(function () {
    $('#usersTable').DataTable();

    // When the edit button is clicked
    $('.edit-user-btn').on('click', function() {
        // Get data from button attributes
        const id = $(this).data('id');
        const fullname = $(this).data('fullname');
        const userid = $(this).data('userid');
        const role = $(this).data('role');
        const dept = $(this).data('dept');

        // Populate the modal fields
        $('#edit_id').val(id);
        $('#edit_fullname').val(fullname);
        $('#edit_userid').val(userid);
        $('#edit_role').val(role);
        $('#edit_dept').val(dept);
    });
});
</script>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    $('#usersTable').DataTable({
        "autoWidth": false,
        "language": { "emptyTable": "No users found." }
    });
});
</script>
<?= $this->endSection() ?>