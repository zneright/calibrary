<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">Journals</h5>
            
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addJournalModal">
                <i class="bi bi-plus-lg"></i> Add Journal
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="journalTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.9rem;">
                        <tr>
                            <th width="5%">NO</th>
                            <th class="text-start">SUBJECT</th>
                            <th>AUTHOR</th>
                            <th>DATE</th>
                            <th>SOURCE</th>
                            <th>SESSION</th>
                            <th>VOLUME</th>
                            <th>PAGE</th>
                            <th>LAST UPDATE</th>
                            <th>STATUS</th>
                            <th width="100">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        <?php if (!empty($journals)): ?>
                            <?php foreach($journals as $i => $row): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td class="text-start text-primary fw-semibold"><?= esc($row['subject']) ?></td>
                                <td><?= esc($row['author']) ?></td>
                                <td><?= esc($row['date']) ?></td>
                                <td><?= esc($row['source']) ?></td>
                                <td><?= esc($row['session']) ?></td>
                                <td><?= esc($row['volume']) ?></td>
                                <td><?= esc($row['page']) ?></td>
                                <td class="text-muted"><?= esc($row['last_update']) ?></td>
                                <td>
                                    <span class="badge bg-success">Available</span>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
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

<div class="modal fade" id="addJournalModal" tabindex="-1" aria-labelledby="addJournalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-secondary" id="addJournalModalLabel">
                    <i class="bi bi-journal-plus me-2"></i>Add New Journal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/journals/store" method="POST">
                <?= csrf_field() ?>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" placeholder="Enter journal subject" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Author <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control" placeholder="e.g. Juan Dela Cruz" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" class="form-control">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Source</label>
                            <input type="text" name="source" class="form-control" placeholder="e.g. CA JOURNAL">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Session</label>
                            <input type="text" name="session" class="form-control" placeholder="e.g. 1 (1987-1988)">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Volume</label>
                            <input type="text" name="volume" class="form-control" placeholder="e.g. 1">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Page</label>
                            <input type="text" name="page" class="form-control" placeholder="e.g. 1-2">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="bi bi-save me-1"></i> Save Journal
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
    $('#journalTable').DataTable({
        "autoWidth": false,
        "scrollY": "400px",
        "scrollCollapse": true,
        "language": { "emptyTable": "No journals found. The table is currently empty." }
    });
});
</script>
<?= $this->endSection() ?>