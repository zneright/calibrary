<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-bell-fill text-warning me-2"></i>Notification Center
            </h5>
            
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addNotificationModal">
                <i class="bi bi-send-plus-fill me-1"></i> Create Notification
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="notificationTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.9rem;">
                        <tr>
                            <th width="5%">NO</th>
                            <th>RECIPIENT</th>
                            <th>TYPE</th>
                            <th class="text-start" width="35%">MESSAGE</th>
                            <th>DATE SENT</th>
                            <th>STATUS</th>
                            <th width="100">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        <?php if (!empty($notifications)): ?>
                            <?php foreach($notifications as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                
                                <td class="fw-semibold <?= $row['target_audience'] == 'all_admins' ? 'text-primary' : '' ?>">
                                    <?= esc($row['recipient']) ?>
                                </td>
                                
                                <td>
                                    <?php if($row['type'] == 'alert'): ?>
                                        <span class="badge bg-danger">Overdue Alert</span>
                                    <?php elseif($row['type'] == 'info'): ?>
                                        <span class="badge bg-info text-dark">System Info</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Warning</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-start"><?= esc($row['message']) ?></td>
                                <td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
                                
                                <td>
                                    <?php if($row['status'] == 'read'): ?>
                                        <span class="badge bg-success">Read</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Unread</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <button class="btn btn-danger btn-sm delete-notif-btn" 
                                            title="Delete"
                                            data-id="<?= $row['id'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-secondary">
                    <i class="bi bi-send me-2"></i>Send New Notification
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?= base_url('admin/notifications/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Send To <span class="text-danger">*</span></label>
                            <select name="target_audience" id="targetAudience" class="form-select" required>
                                <option value="" disabled selected>Select Recipient...</option>
                                <option value="all_users">All Registered Users</option>
                                <option value="all_admins">All System Admins</option>
                                <option value="specific_user">A Specific User</option>
                            </select>
                        </div>
                        
                       <div class="col-md-12" id="specificUserField" style="display: none;">
                            <label class="form-label fw-semibold">Search User <span class="text-danger">*</span></label>
                            
                            <input list="userList" name="user_id" id="userIdInput" class="form-control" placeholder="Type name or ID to search...">
                            
                            <datalist id="userList">
                                <?php if(!empty($users)): foreach($users as $u): ?>
                                    <option value="<?= esc($u['user_id']) ?> | <?= esc($u['fullname']) ?>">
                                <?php endforeach; endif; ?>
                            </datalist>
                            <small class="text-muted">You can search by typing their ID Number or Full Name.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Notification Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="info">General Info</option>
                                <option value="warning">Warning / Reminder</option>
                                <option value="alert">Overdue Alert</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Type your notification message here..." required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="bi bi-send-fill me-1"></i> Send Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?= base_url('admin/notifications/delete') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_notif_id">
                
                <div class="modal-body text-center p-4">
                    <i class="bi bi-trash text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1 fw-semibold text-dark">Are you sure you want to delete this notification?</p>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                
                <div class="modal-footer bg-light justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger shadow-sm fw-bold">
                        Yes, Delete It
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
    $('#notificationTable').DataTable({
        "autoWidth": false,
        "order": [], // Stop auto-sort to show newest first!
        "language": { "emptyTable": "No notifications found." }
    });

    // 2. Dynamic Modal Logic (Show/Hide Specific User Field)
    const targetSelect = document.getElementById('targetAudience');
    const specificUserDiv = document.getElementById('specificUserField');
    const userIdInput = document.getElementById('userIdInput');

    targetSelect.addEventListener('change', function() {
        if (this.value === 'specific_user') {
            specificUserDiv.style.display = 'block';
            userIdInput.setAttribute('required', 'required');
        } else {
            specificUserDiv.style.display = 'none';
            userIdInput.removeAttribute('required');
            userIdInput.value = '';
        }
    });

    // 3. Pass ID to Delete Modal
    $(document).on('click', '.delete-notif-btn', function() {
        const id = $(this).data('id');
        $('#delete_notif_id').val(id);
        $('#deleteNotificationModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>