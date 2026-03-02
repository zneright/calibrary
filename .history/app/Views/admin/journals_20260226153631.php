<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                                <td><?= esc($row['date'] ?? 'N/A') ?></td>
                                <td><?= esc($row['source'] ?? 'N/A') ?></td>
                                <td><?= esc($row['session'] ?? 'N/A') ?></td>
                                <td><?= esc($row['volume'] ?? 'N/A') ?></td>
                                <td><?= esc($row['page'] ?? 'N/A') ?></td>
                                <td class="text-muted small"><?= date('M d, Y', strtotime($row['updated_at'])) ?></td>
                                <td>
                                    <?php $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
                                    
                                    <button class="btn btn-warning btn-sm edit-btn" title="Edit" data-row="<?= $jsonData ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-btn" title="Delete" data-id="<?= $row['id'] ?>" data-subject="<?= esc($row['subject']) ?>">
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

<div class="modal fade" id="addJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-secondary">
                    <i class="bi bi-journal-plus me-2"></i>Add New Journal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/journals/store') ?>" method="POST">
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
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="bi bi-save me-1"></i> Save Journal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-warning">
                    <i class="bi bi-pencil-square me-2"></i>Edit Journal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/journals/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="edit_subject" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Author <span class="text-danger">*</span></label>
                            <input type="text" name="author" id="edit_author" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="date" id="edit_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Source</label>
                            <input type="text" name="source" id="edit_source" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Session</label>
                            <input type="text" name="session" id="edit_session" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Volume</label>
                            <input type="text" name="volume" id="edit_volume" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Page</label>
                            <input type="text" name="page" id="edit_page" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning shadow-sm fw-bold">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Journal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/journals/delete') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_journal_id">
                <input type="hidden" name="subject" id="delete_journal_subject">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-trash text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1 fw-semibold text-dark">Delete this journal?</p>
                    <p class="small text-muted mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger shadow-sm fw-bold">Yes, Delete</button>
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
        "order": [], // Stop auto-sort
        "language": { "emptyTable": "No journals found. The table is currently empty." }
    });

    // Edit Modal Logic
    $(document).on('click', '.edit-btn', function() {
        let rowData = $(this).data('row');

        $('#edit_id').val(rowData.id);
        $('#edit_subject').val(rowData.subject);
        $('#edit_author').val(rowData.author);
        $('#edit_date').val(rowData.date);
        $('#edit_source').val(rowData.source);
        $('#edit_session').val(rowData.session);
        $('#edit_volume').val(rowData.volume);
        $('#edit_page').val(rowData.page);

        $('#editJournalModal').modal('show');
    });

    // Delete Modal Logic`z
    $(document).on('click', '.delete-btn', function() {
        $('#delete_journal_id').val($(this).data('id'));
        $('#delete_journal_subject').val($(this).data('subject'));
        
        $('#deleteJournalModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>