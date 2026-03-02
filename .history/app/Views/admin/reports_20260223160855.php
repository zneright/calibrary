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
                        <tr>
                            <td>1</td>
                            <td class="text-start fw-semibold">Juan Dela Cruz <br><small class="text-muted fw-normal">ID: 2024-0192</small></td>
                            <td><span class="badge bg-primary">Book Borrow</span></td>
                            <td class="text-start">Data Structures & Algorithms</td>
                            <td>Feb 18, 2026</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>
                                <button class="btn btn-success btn-sm" title="Approve"><i class="bi bi-check-lg"></i></button>
                                <button class="btn btn-danger btn-sm" title="Reject"><i class="bi bi-x-lg"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td class="text-start fw-semibold">Maria Santos <br><small class="text-muted fw-normal">ID: 2025-0844</small></td>
                            <td><span class="badge bg-info text-dark">Journal Access</span></td>
                            <td class="text-start">Presidential Decree No. 1081</td>
                            <td>Feb 17, 2026</td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td>
                                <button class="btn btn-secondary btn-sm" title="View Details" disabled><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                        
                        <?php if (!empty($reports)): ?>
                            <?php foreach($reports as $i => $row): ?>
                            <?php endforeach ?>
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
                </div>Z
                
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