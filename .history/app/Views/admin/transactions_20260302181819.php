<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

   <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-pills bg-white p-1 rounded-pill shadow-sm border gap-1">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 <?= !isset($_GET['status']) ? 'active fw-bold shadow-sm' : 'text-secondary' ?>" href="<?= base_url('admin/transactions') ?>">
                    All
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'active fw-bold shadow-sm' : 'text-secondary' ?>" href="<?= base_url('admin/transactions?status=Pending') ?>">
                    Pending <span class="badge bg-warning text-dark ms-1 small rounded-pill">Requests</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= (isset($_GET['status']) && $_GET['status'] == 'Approved') ? 'active fw-bold shadow-sm' : 'text-secondary' ?>" href="<?= base_url('admin/transactions?status=Approved') ?>">
                    Ready for Pickup <span class="badge bg-primary text-white ms-1 small rounded-pill">Handover</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= (isset($_GET['status']) && $_GET['status'] == 'Borrowed') ? 'active fw-bold shadow-sm' : 'text-secondary' ?>" href="<?= base_url('admin/transactions?status=Borrowed') ?>">
                    On Hand
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 <?= (isset($_GET['status']) && $_GET['status'] == 'Returned') ? 'active fw-bold shadow-sm' : 'text-secondary' ?>" href="<?= base_url('admin/transactions?status=Returned') ?>">
                    Returned
                </a>
            </li>
        </ul>

        <button class="btn btn-outline-secondary rounded-pill px-4 shadow-sm bg-white" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-arrow-left-right me-2 text-primary"></i>
                <?= isset($_GET['status']) ? esc($_GET['status']) : 'All' ?> Transactions
            </h5>
        </div>

        <div class="card-body p-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-arrow-left-right me-2"></i>Transactions
            </h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="transactionsTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.9rem;">
                        <tr>
                            <th width="5%">NO</th>
                            <th>TRANS. ID</th>
                            <th class="text-start">BORROWER</th>
                            <th class="text-start" width="25%">ITEM DETAILS</th>
                            <th>REQUESTED ON</th>
                            <th>DUE DATE</th>
                            <th>STATUS</th>
                            <th width="120">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        <?php if (!empty($transactions)): foreach($transactions as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold text-secondary">TRX-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="text-start">
                                <span class="fw-bold"><?= esc($row['user_name']) ?></span><br>
                                <small class="text-muted">ID: <?= esc($row['user_id_num']) ?></small>
                            </td>
                            
                            <td class="text-start text-primary">
                                <span class="fw-semibold"><?= esc($row['collection_title']) ?></span>
                                
                                <?php if(!empty($row['reason'])): ?>
                                    <span class="badge bg-light text-secondary border border-secondary border-opacity-25 ms-1" 
                                          data-bs-toggle="tooltip" 
                                          data-bs-placement="top" 
                                          title="Reason: <?= esc($row['reason']) ?>" 
                                          style="cursor: help;">
                                        <i class="bi bi-chat-text-fill"></i> Reason
                                    </span>
                                <?php endif; ?>
                                
                                <br>
                                <small class="text-muted">ID: <?= esc($row['collection_id']) ?></small>
                            </td>

                            <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                            <td><?= $row['due_date'] ? '<span class="fw-bold">' . date('M d, Y', strtotime($row['due_date'])) . '</span>' : '<span class="text-muted">---</span>' ?></td>
                            <td>
                                <?php 
                                    $status = $row['status'];
                                    $badge = 'bg-secondary';
                                    if($status == 'Pending') $badge = 'bg-warning text-dark';
                                    if($status == 'Renewing') { $badge = 'bg-warning text-dark'; $label = 'Renew Request'; }
                                    if($status == 'Approved') { $badge = 'bg-primary'; $label = 'Ready for Pickup'; }
                                    if($status == 'Borrowed') { $badge = 'bg-info text-dark'; $label = 'On Hand'; }
                                    if($status == 'Returned') $badge = 'bg-success';
                                    if($status == 'Rejected') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge ?> px-3 py-2"><?= esc($row['status'] == 'Renewing' ? 'Renew Request' : ($row['status'] == 'Approved' ? 'Ready for Pickup' : ($row['status'] == 'Borrowed' ? 'On Hand' : $row['status']))) ?></span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <?php if($row['status'] == 'Pending'): ?>
                                        <form action="<?= base_url('admin/transactions/approve') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm btn-loading">Approve</button>
                                        </form>
                                        <form action="<?= base_url('admin/transactions/reject') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm btn-loading"><i class="bi bi-x-lg"></i></button>
                                        </form>

                                    <?php elseif($row['status'] == 'Approved'): ?>
                                        <button class="btn btn-sm btn-info text-white handover-btn" data-id="<?= $row['id'] ?>" data-title="<?= esc($row['collection_title']) ?>">Handover</button>

                                    <?php elseif($row['status'] == 'Renewing'): ?>
                                        <button class="btn btn-sm btn-warning fw-bold admin-renew-btn" data-id="<?= $row['id'] ?>" data-title="<?= esc($row['collection_title']) ?>">Renew</button>
                                        <form action="<?= base_url('admin/transactions/reject') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm btn-loading"><i class="bi bi-x-lg"></i></button>
                                        </form>

                                    <?php elseif($row['status'] == 'Borrowed'): ?>
                                        <form action="<?= base_url('admin/transactions/processReturn') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm btn-loading">Return</button>
                                        </form>
                                        <form action="<?= base_url('admin/transactions/sendManualReminder') ?>" method="POST">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-warning btn-sm btn-loading" title="Remind"><i class="bi bi-bell-fill"></i></button>
                                        </form>

                                    <?php else: ?>
                                        <span class="text-muted small">---</span>
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
</div>

<div class="modal fade" id="handoverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/transactions/handover') ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="h_id">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">Confirm Book Pickup</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-1 fw-bold text-dark" id="h_title"></p>
                <p class="small text-muted mb-4">Set the return deadline for this borrower.</p>
                <label class="form-label small fw-bold">Due Date (Return Deadline) <span class="text-danger">*</span></label>
                <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info text-white fw-bold shadow-sm btn-loading">Mark as On Hand</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="adminRenewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/transactions/approveRenewal') ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="ar_id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-check me-2"></i>Set New Return Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-2">Item: <span id="ar_title" class="fw-bold text-dark"></span></p>
                <label class="form-label small fw-bold">New Due Date <span class="text-danger">*</span></label>
                <input type="date" name="new_due_date" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning fw-bold shadow-sm btn-loading">Confirm Extension</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    // 1. Initialize Datatable
    $('#transactionsTable').DataTable({
        "autoWidth": false,
        "order": [[ 0, "desc" ]],
        "language": { "emptyTable": "No transactions recorded yet." }
    });

    // 2. Initialize Tooltips for the "Hover Reason"
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // 3. Open Handover Modal
    $(document).on('click', '.handover-btn', function() {
        $('#h_id').val($(this).data('id'));
        $('#h_title').text($(this).data('title'));
        $('#handoverModal').modal('show');
    });

    // 4. Open Renewal Modal
    $(document).on('click', '.admin-renew-btn', function() {
        $('#ar_id').val($(this).data('id'));
        $('#ar_title').text($(this).data('title'));
        $('#adminRenewModal').modal('show');
    });


    $('form').on('submit', function() {
        let btn = $(this).find('button[type="submit"]');
        
        if (btn.prop('disabled')) {
            return false; 
        }

        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Wait...');
        btn.prop('disabled', true);
    });
});
</script>
<?= $this->endSection() ?>