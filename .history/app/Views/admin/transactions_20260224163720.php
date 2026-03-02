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
                            <th>BORROWED ON</th>
                            <th>DUE DATE</th>
                            <th>STATUS</th>
                            <th width="120">ACTION</th>
                        </tr>
                    <tbody class="align-middle text-center">
    <?php foreach($transactions as $row): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td class="fw-bold">TRX-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
        <td><?= esc($row['user_name']) ?></td>
        <td><?= esc($row['collection_title']) ?></td>
        <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
        <td><?= $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '---' ?></td>
        <td>
            <?php 
                $badge = 'bg-secondary';
                if($row['status'] == 'Pending') $badge = 'bg-warning text-dark';
                if($row['status'] == 'Approved') $badge = 'bg-primary';
                if($row['status'] == 'Borrowed') $badge = 'bg-info text-dark';
                if($row['status'] == 'Returned') $badge = 'bg-success';
            ?>
            <span class="badge <?= $badge ?>"><?= $row['status'] == 'Approved' ? 'Ready for Pickup' : $row['status'] ?></span>
        </td>
        <td>
            <?php if($row['status'] == 'Pending'): ?>
                <form action="<?= base_url('admin/transactions/approve') ?>" method="POST">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button class="btn btn-sm btn-primary">Approve</button>
                </form>
            <?php elseif($row['status'] == 'Approved'): ?>
                <button class="btn btn-sm btn-info text-white handover-btn" data-id="<?= $row['id'] ?>" data-title="<?= esc($row['collection_title']) ?>">Handover</button>
            <?php elseif($row['status'] == 'Borrowed'): ?>
                <form action="<?= base_url('admin/transactions/return') ?>" method="POST">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button class="btn btn-sm btn-success">Return</button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

<div class="modal fade" id="handoverModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/transactions/handover') ?>" method="POST" class="modal-content">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="h_id">
            <div class="modal-header"><h5>Set Due Date</h5></div>
            <div class="modal-body">
                <p id="h_title" class="fw-bold"></p>
                <label>Return Deadline:</label>
                <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info text-white">Hand over to User</button>
            </div>
        </form>
    </div>
</div>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-secondary">
                    <i class="bi bi-cart-plus me-2"></i>Process Checkout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/admin/transactions/store" method="POST">
                <?= csrf_field() ?>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Borrower ID <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="borrower_id" class="form-control" placeholder="Scan or enter User ID" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Item Accession / Call No. <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" name="item_code" class="form-control" placeholder="Scan or enter Book Code" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Checkout Date <span class="text-danger">*</span></label>
                            <input type="date" name="borrow_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expected Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Remarks (Optional)</label>
                            <input type="text" name="remarks" class="form-control" placeholder="e.g. Book cover slightly damaged prior to checkout">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Confirm Checkout
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
    $('#transactionsTable').DataTable({
        "autoWidth": false,
        "order": [[ 0, "desc" ]], // Automatically sorts so the newest transactions are at the top
        "language": { "emptyTable": "No transactions recorded yet." }
    });
});
$(document).on('click', '.handover-btn', function() {
    $('#h_id').val($(this).data('id'));
    $('#h_title').text($(this).data('title'));
    $('#handoverModal').modal('show');
});