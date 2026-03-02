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
                            <th>STATUS</th>
                            <th width="120">ACTION</th>
                        </tr>
                    </thead>
                   <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        <?php if (!empty($users)): ?>
                            <?php foreach($users as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="fw-bold text-secondary"><?= esc($row['user_id']) ?></td>
                                <td class="text-start fw-bold text-dark">
                                    <?= esc($row['fullname']) ?> <br>
                                    <small class="text-muted fw-normal"><?= esc($row['email']) ?></small>
                                </td>
                                <td>
                                    <span class="badge <?= $row['role'] == 'Admin' ? 'bg-dark' : 'bg-info text-dark' ?>">
                                        <?= esc($row['role']) ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <?php if(isset($row['is_verified']) && $row['is_verified'] == 1): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if(isset($row['is_verified']) && $row['is_verified'] == 0): ?>
                                        <button class="btn btn-success btn-sm approve-user-btn" 
                                                title="Approve Account"
                                                data-id="<?= $row['id'] ?>"
                                                data-fullname="<?= esc($row['fullname']) ?>"
                                                data-email="<?= esc($row['email']) ?>">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    <?php endif; ?>

                                    <button class="btn btn-warning btn-sm edit-user-btn" 
                                            title="Edit"
                                            data-id="<?= $row['id'] ?>"
                                            data-fullname="<?= esc($row['fullname']) ?>"
                                            data-userid="<?= esc($row['user_id']) ?>"
                                            data-role="<?= esc($row['role']) ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" title="Disable Account"><i class="bi bi-person-x"></i></button>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No users found in the database.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="approveUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Approve User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/users/approve') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="approve_user_id">
                <input type="hidden" name="email" id="approve_user_email">
                <input type="hidden" name="fullname" id="approve_user_fullname">
                
                <div class="modal-body text-center p-4">
                    <i class="bi bi-shield-check text-success mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1 fw-semibold text-dark">Grant access to this user?</p>
                    <p class="small text-muted mb-0">They will receive an email letting them know they can log in.</p>
                </div>
                <div class="modal-footer bg-light justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success shadow-sm fw-bold">Yes, Approve</button>
                </div>
            </form>
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
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
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

<?= $this->section('scripts') ?>
<script>

$(document).ready(function () {
    // 1. Initialize DataTable
    var table = $('#usersTable').DataTable({
        "autoWidth": false,
        "language": { "emptyTable": "No users found in the database." }
    });
    

    // 2. Edit Button Logic (Event Delegation ensures it works on all pages of the table)
    $(document).on('click', '.edit-user-btn', function() {
        
        // Grab data from the clicked button
        const id = $(this).data('id');
        const fullname = $(this).data('fullname');
        const userid = $(this).data('userid');
        const role = $(this).data('role');
        const dept = $(this).data('dept');

        // Push that data into the Edit Modal inputs
        $('#edit_id').val(id);
        $('#edit_fullname').val(fullname);
        $('#edit_userid').val(userid);
        $('#edit_role').val(role);
        $('#edit_dept').val(dept);

        // Show the Edit Modal
        $('#editUserModal').modal('show');

        // Pass Data to Approve Modal
    $(document).on('click', '.approve-user-btn', function() {
        $('#approve_user_id').val($(this).data('id'));
        $('#approve_user_email').val($(this).data('email'));
        $('#approve_user_fullname').val($(this).data('fullname'));
        $('#approveUserModal').modal('show');
    });
    });
});
</script>
<?= $this->endSection() ?>