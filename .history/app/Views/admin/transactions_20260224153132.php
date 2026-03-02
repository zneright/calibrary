<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">



    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-arrow-left-right me-2"></i>Circulation & Transactions
            </h5>
            
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#newTransactionModal">
                <i class="bi  me-1"></i> Process Checkout
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
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        <?php if (!empty($transactions)): foreach($transactions as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold text-secondary">TRX-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="text-start fw-semibold">
                                <?= esc($row['user_name']) ?> <br>
                                <small class="text-muted fw-normal">ID: <?= esc($row['user_id_num']) ?></small>
                            </td>
                            <td class="text-start text-primary">
                                <?= esc($row['collection_title']) ?> <br>
                                <small class="text-muted fw-normal">ID: <?= esc($row['collection_id']) ?></small>
                            </td>
                            <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                            <td class="<?= (strtotime($row['due_date']) < time() && $row['status'] == 'Borrowed') ? 'text-danger fw-bold' : '' ?>">
                                <?= $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : 'Pending' ?>
                            </td>
                            <td>
                                <?php 
                                    $badge = 'bg-secondary';
                                    if($row['status'] == 'Pending') $badge = 'bg-warning text-dark';
                                    if($row['status'] == 'Borrowed') $badge = 'bg-info text-dark';
                                    if($row['status'] == 'Returned') $badge = 'bg-success';
                                    if($row['status'] == 'Rejected' || $row['status'] == 'Cancelled') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge ?>"><?= esc($row['status']) ?></span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Pending'): ?>
                                    <form action="<?= base_url('admin/reports/approve') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Approve Request">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                <?php elseif($row['status'] == 'Borrowed'): ?>
                                    <form action="<?= base_url('admin/transactions/updateStatus') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="status" value="Returned">
                                        <button type="submit" class="btn btn-success btn-sm" title="Mark as Returned">
                                            <i class="bi bi-box-arrow-in-down"></i> Return
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" class="text-center py-4">No transactions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
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
<script>
$(document).ready(function () {
    $('#transactionsTable').DataTable({
        "autoWidth": false,
        "order": [], // This keeps the 'Oldest First' order from your database!
        "language": { "emptyTable": "No transactions recorded yet." }
    });
});
</script>