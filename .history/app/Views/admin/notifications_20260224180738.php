<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                            <th class="text-start">RECIPIENT</th>
                            <th>TYPE</th>
                            <th class="text-start" width="35%">MESSAGE</th>
                            <th>DATE SENT</th>
                            <th width="100">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        <?php if (!empty($notifications)): foreach($notifications as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="text-start fw-semibold">
                                <?php if($row['target_audience'] == 'specific_user'): ?>
                                    <i class="bi bi-person-fill text-muted me-1"></i>
                                <?php else: ?>
                                    <i class="bi bi-people-fill text-primary me-1"></i>
                                <?php endif; ?>
                                <?= esc($row['recipient']) ?>
                            </td>
                            <td>
                                <?php 
                                    $typeBadge = 'bg-info text-dark';
                                    if($row['type'] == 'alert') $typeBadge = 'bg-danger';
                                    if($row['type'] == 'warning') $typeBadge = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= $typeBadge ?>"><?= ucfirst(esc($row['type'])) ?></span>
                            </td>
                            <td class="text-start small"><?= esc($row['message']) ?></td>
                            <td class="small"><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-notif-btn" data-id="<?= $row['id'] ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/notifications/store') ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-secondary"><i class="bi bi-send me-2"></i>New Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Target Audience</label>
                    <select name="target_audience" id="targetAudience" class="form-select" required>
                        <option value="all_users">All Registered Users</option>
                        <option value="all_admins">All System Admins</option>
                        <option value="specific_user">Specific User</option>
                    </select>
                </div>
                <div class="mb-3" id="specificUserField" style="display: none;">
                    <label class="form-label fw-bold small">Search User (ID | Name)</label>
                    <input list="userList" name="user_id" id="userIdInput" class="form-control" placeholder="Search...">
                  <datalist id="userList">
                <?php if(!empty($users)): foreach($users as $u): ?>
                    <option value="<?= esc($u['user_id']) ?> | <?= esc($u['fullname']) ?>">
                <?php endforeach; endif; ?>
            </datalist>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Type</label>
                    <select name="type" class="form-select">
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="alert">Alert</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold small">Message</label>
                    <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn-primary px-4">Send Notification</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form action="<?= base_url('admin/notifications/delete') ?>" method="POST" class="modal-content">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="delete_notif_id">
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash text-danger fs-1 mb-3"></i>
                <p class="fw-bold mb-1">Delete notification?</p>
                <p class="text-muted small">This cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    $('#notificationTable').DataTable({ "order": [] });

    $('#targetAudience').on('change', function() {
        if ($(this).val() === 'specific_user') {
            $('#specificUserField').show();
            $('#userIdInput').attr('required', 'required');
        } else {
            $('#specificUserField').hide();
            $('#userIdInput').removeAttr('required');
        }
    });

    $(document).on('click', '.delete-notif-btn', function() {
        $('#delete_notif_id').val($(this).data('id'));
        $('#deleteNotificationModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>