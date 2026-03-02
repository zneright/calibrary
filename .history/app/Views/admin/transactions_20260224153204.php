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
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        
                        <tr>
                            <td>1</td>
                            <td class="fw-bold text-secondary">TRX-00101</td>
                            <td class="text-start fw-semibold">Juan Dela Cruz <br><small class="text-muted fw-normal">ID: 2024-0192</small></td>
                            <td class="text-start text-primary">Data Structures & Algorithms <br><small class="text-muted fw-normal">Call No: QA76.73</small></td>
                            <td>Feb 18, 2026</td>
                            <td class="text-danger fw-semibold">Feb 21, 2026</td>
                            <td><span class="badge bg-warning text-dark">Borrowed</span></td>
                            <td>
                                <button class="btn btn-success btn-sm" title="Mark as Returned"><i class="bi bi-box-arrow-in-down"></i> Return</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td class="fw-bold text-secondary">TRX-00100</td>
                            <td class="text-start fw-semibold">Maria Santos <br><small class="text-muted fw-normal">ID: 2025-0844</small></td>
                            <td class="text-start text-primary">Modern PHP Programming <br><small class="text-muted fw-normal">Call No: QA76.73.P2</small></td>
                            <td>Feb 10, 2026</td>
                            <td>Feb 15, 2026</td>
                            <td><span class="badge bg-danger">Overdue</span></td>
                            <td>
                                <button class="btn btn-success btn-sm" title="Mark as Returned"><i class="bi bi-box-arrow-in-down"></i> Return</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td class="fw-bold text-secondary">TRX-00099</td>
                            <td class="text-start fw-semibold">Pedro Reyes <br><small class="text-muted fw-normal">ID: 2023-1102</small></td>
                            <td class="text-start text-primary">Presidential Decree No. 1081 <br><small class="text-muted fw-normal">Journal</small></td>
                            <td>Feb 01, 2026</td>
                            <td>Feb 05, 2026</td>
                            <td><span class="badge bg-success">Returned</span></td>
                            <td>
                                <span class="text-muted small"><i class="bi bi-check-circle"></i> Cleared</span>
                            </td>
                        </tr>
                        
                        <?php if (!empty($transactions)): ?>
                            <?php foreach($transactions as $i => $row): ?>
                            <?php endforeach ?>
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
</script>
<?= $this->endSection() ?>