<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-arrow-left-right me-2"></i>Circulation & Transactions
            </h5>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#newTransactionModal">
                <i class="bi bi-cart-plus me-1"></i> Process Checkout
            </button>
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
                                <span class="fw-semibold"><?= esc($row['collection_title']) ?></span><br>
                                <small class="text-muted">ID: <?= esc($row['collection_id']) ?></small>
                            </td>
                            <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                            <td>
                                <?= $row['due_date'] ? '<span class="fw-bold">' . date('M d, Y', strtotime($row['due_date'])) . '</span>' : '<span class="text-muted">---</span>' ?>
                            </td>
                            <td>
                                <?php 
                                    $badge = 'bg-secondary';
                                    $label = esc($row['status']);
                                    if($row['status'] == 'Pending') $badge = 'bg-warning text-dark';
                                    if($row['status'] == 'Approved') { $badge = 'bg-primary'; $label = 'Ready for Pickup'; }
                                    if($row['status'] == 'Borrowed') { $badge = 'bg-info text-dark'; $label = 'On Hand'; }
                                    if($row['status'] == 'Returned') $badge = 'bg-success';
                                ?>
                                <span class="badge <?= $badge ?> px-3 py-2"><?= $label ?></span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Pending'): ?>
                                    <form action="<?= base_url('admin/transactions/approve') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">Approve</button>
                                    </form>
                                <?php elseif($row['status'] == 'Approved'): ?>
                                    <button class="btn btn-sm btn-info text-white handover-btn shadow-sm" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-title="<?= esc($row['collection_title']) ?>">
                                        Handover
                                    </button>
                                    
                                <?php elseif($row['status'] == 'Borrowed'): ?>
                                    <form action="<?= base_url('admin/transactions/return') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm">Return</button>
                                    </form>
                                <?php endif; ?>
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
                <button type="submit" class="btn btn-info text-white fw-bold shadow-sm">Mark as On Hand</button>
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

    // 2. Open Handover Modal and pass data
    $(document).on('click', '.handover-btn', function() {
        $('#h_id').val($(this).data('id'));
        $('#h_title').text($(this).data('title'));
        $('#handoverModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>