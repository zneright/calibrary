<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Requests & Reports
            </h5>
            
            <div class="d-flex gap-2">
                <a href="/reports/export" class="btn btn-danger btn-sm shadow-sm" title="Export Table to PDF">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
                </a>
                
                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addRequestModal">
                    <i class="bi bi-plus-lg"></i> New Request
                </button>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="reportsTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.9rem;">
                        <tr>
                            <th width="5%">NO</th>
                            <th class="text-start">REQUESTER</th>
                            <th>TYPE</th>
                            <th class="text-start" width="30%">ITEM / SUBJECT DETAILS</th>
                            <th>DATE REQUESTED</th>
                            <th>STATUS</th>
                            <th width="100">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
    <?php if (!empty($reports)): ?>
        <?php foreach($reports as $i => $row): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td class="text-start fw-semibold">
                <?= esc($row['requester_name']) ?> <br>
                <small class="text-muted fw-normal">ID: <?= esc($row['requester_id']) ?></small>
            </td>
            <td>
                <?php if($row['type'] == 'book_borrow'): ?>
                    <span class="badge bg-primary">Book Borrow</span>
                <?php elseif($row['type'] == 'journal_access'): ?>
                    <span class="badge bg-info text-dark">Journal Access</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Other Inquiry</span>
                <?php endif; ?>
            </td>
            <td class="text-start"><?= esc($row['details']) ?></td>
            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
            <td>
                <?php if($row['status'] == 'pending'): ?>
                    <span class="badge bg-warning text-dark">Pending</span>
                <?php elseif($row['status'] == 'approved'): ?>
                    <span class="badge bg-success">Approved</span>
                <?php else: ?>
                    <span class="badge bg-danger">Rejected</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if($row['status'] == 'pending'): ?>
                    <button class="btn btn-success btn-sm approve-btn" 
                            title="Approve"
                            data-id="<?= $row['id'] ?>"
                            data-bs-toggle="modal" 
                            data-bs-target="#approveRequestModal">
                        <i class="bi bi-check-lg"></i>
                    </button>
                    <button class="btn btn-danger btn-sm reject-btn" 
                            title="Reject"
                            data-id="<?= $row['id'] ?>"
                            data-bs-toggle="modal" 
                            data-bs-target="#rejectRequestModal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" title="View Details" disabled><i class="bi bi-eye"></i></button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center text-muted">No requests or reports found.</td>
        </tr>
    <?php endif; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-secondary">
                    <i class="bi bi-file-earmark-plus me-2"></i>Create New Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/reports/store" method="POST">
                <?= csrf_field() ?>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Requester Name <span class="text-danger">*</span></label>
                            <input type="text" name="requester_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Requester ID <span class="text-danger">*</span></label>
                            <input type="text" name="requester_id" class="form-control" placeholder="e.g. 2024-001" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Request Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="book_borrow">Book Borrow</option>
                                <option value="journal_access">Journal Access</option>
                                <option value="other">Other Inquiry</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Item / Details <span class="text-danger">*</span></label>
                            <textarea name="details" class="form-control" rows="2" placeholder="Title of the book, journal name, or request details..." required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="bi bi-save me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approveRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Approve Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('reports/approve') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="approve_id">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1 fw-semibold text-dark">Are you sure you want to approve this request?</p>
                </div>
                <div class="modal-footer bg-light justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success shadow-sm fw-bold">Yes, Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Reject Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('reports/reject') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="reject_id">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-x-circle text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1 fw-semibold text-dark">Are you sure you want to reject this request?</p>
                </div>
                <div class="modal-footer bg-light justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger shadow-sm fw-bold">Yes, Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    $('#reportsTable').DataTable({
        "autoWidth": false,
        "language": { "emptyTable": "No requests or reports found." }
    });
});
</script>
<?= $this->endSection() ?>