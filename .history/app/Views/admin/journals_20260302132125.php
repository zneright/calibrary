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

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-journal-text me-2 text-primary"></i>Journal Management
            </h5>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" id="openAddModalBtn">
                <i class="bi bi-plus-lg me-1"></i> Add Journal
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="journalsTable">
                    <thead class="table-light align-middle text-center">
                        <tr>
                            <th width="5%">NO</th>
                            <th class="text-start">SUBJECT / TITLE</th>
                            <th>AUTHOR</th>
                            <th>SOURCE</th>
                            <th>VOL & PAGE</th>
                            <th>STATUS</th> 
                            <th width="120">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center">
                        <?php if (!empty($journals)): foreach($journals as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="text-start">
                                <span class="fw-bold text-dark d-block"><?= esc($row['subject']) ?></span>
                                <small class="text-muted"><?= $row['date'] ? date('M d, Y', strtotime($row['date'])) : 'No Date' ?></small>
                            </td>
                            <td><?= esc($row['author']) ?></td>
                            <td><span class="badge bg-secondary opacity-75"><?= esc($row['source']) ?></span></td>
                            <td>
                                <small class="text-muted">
                                    Vol: <span class="fw-bold text-dark"><?= esc($row['volume'] ?: '-') ?></span> | 
                                    Pg: <span class="fw-bold text-dark"><?= esc($row['page'] ?: '-') ?></span>
                                </small>
                            </td>
                            <td>
                                <?php 
                                    $currentStatus = $row['status'] ?? 'AVAILABLE';
                                    $badge = 'bg-secondary';
                                    if($currentStatus == 'AVAILABLE') $badge = 'bg-success';
                                    if($currentStatus == 'BORROWED') $badge = 'bg-warning text-dark';
                                    if($currentStatus == 'LOST' || $currentStatus == 'DAMAGED') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge ?> rounded-pill px-3"><?= esc($currentStatus) ?></span>
                            </td>
                            <td>
                                <?php $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
                                <div class="btn-group shadow-sm">
                                    <button class="btn btn-warning btn-sm edit-btn" title="Edit" data-row="<?= $jsonData ?>"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-danger btn-sm delete-btn" title="Delete" data-id="<?= $row['id'] ?>" data-subject="<?= esc($row['subject']) ?>"><i class="bi bi-trash"></i></button>
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

<div class="modal fade" id="manageJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom text-secondary">
                <h5 class="modal-title fw-bold" id="manageModalTitle">Manage Journal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="manageForm" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="manage_id">
                
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-secondary">Subject / Title <span class="text-danger">*</span></label>
                            <input list="subjectOptions" name="subject" id="manage_subject" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-secondary">Status <span class="text-danger">*</span></label>
                            <select name="status" id="manage_status" class="form-select shadow-sm border-success text-success fw-bold" required>
                                <option value="AVAILABLE">AVAILABLE</option>
                                <option value="BORROWED">BORROWED</option>
                                <option value="LOST">LOST</option>
                                <option value="DAMAGED">DAMAGED</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Author</label>
                            <input list="authorOptions" name="author" id="manage_author" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Date</label>
                            <input type="date" name="date" id="manage_date" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Source</label>
                            <input list="sourceOptions" name="source" id="manage_source" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Session</label>
                            <input list="sessionOptions" name="session" id="manage_session" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Volume</label>
                            <input list="volumeOptions" name="volume" id="manage_volume" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Page</label>
                            <input list="pageOptions" name="page" id="manage_page" class="form-control shadow-sm">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary shadow-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="manageSubmitBtn" class="btn btn-primary shadow-sm px-4 fw-bold">Save Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Delete Journal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/journals/delete') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_id">
                <input type="hidden" name="subject" id="delete_subject">
                
                <div class="modal-body text-center p-4">
                    <i class="bi bi-trash text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1 fw-semibold text-dark">Delete this Journal?</p>
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

<datalist id="subjectOptions"><?php if(!empty($subjects)): foreach($subjects as $s): ?><option value="<?= esc($s['subject']) ?>"><?php endforeach; endif; ?></datalist>
<datalist id="authorOptions"><?php if(!empty($authors)): foreach($authors as $a): ?><option value="<?= esc($a['author']) ?>"><?php endforeach; endif; ?></datalist>
<datalist id="sourceOptions"><?php if(!empty($sources)): foreach($sources as $src): ?><option value="<?= esc($src['source']) ?>"><?php endforeach; endif; ?></datalist>
<datalist id="sessionOptions"><?php if(!empty($sessions)): foreach($sessions as $sess): ?><option value="<?= esc($sess['session']) ?>"><?php endforeach; endif; ?></datalist>
<datalist id="volumeOptions"><?php if(!empty($volumes)): foreach($volumes as $v): ?><option value="<?= esc($v['volume']) ?>"><?php endforeach; endif; ?></datalist>
<datalist id="pageOptions"><?php if(!empty($pages)): foreach($pages as $p): ?><option value="<?= esc($p['page']) ?>"><?php endforeach; endif; ?></datalist>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    // 1. Initialize DataTable AND add the Status Dropdown Filter
    var table = $('#journalsTable').DataTable({
        "autoWidth": false,
        "order": [],
        "language": { "search": "Quick Search:" },
        "initComplete": function () {
            // Adds a Status filter Dropdown next to the search bar
            var col = this.api().column(5); // Column index 5 is 'STATUS'
            var select = $('<select class="form-select form-select-sm d-inline-block w-auto ms-3"><option value="">All Statuses</option><option value="AVAILABLE">Available</option><option value="BORROWED">Borrowed</option><option value="LOST">Lost</option><option value="DAMAGED">Damaged</option></select>')
                .appendTo('#journalsTable_filter')
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    col.search(val ? '^' + val + '$' : '', true, false).draw();
                });
        }
    });

    // 2. OPEN ADD MODAL
    $('#openAddModalBtn').on('click', function() {
        $('#manage_id').val('');
        $('#manageForm')[0].reset(); 
        
        $('#manage_status').val('AVAILABLE');
        
        $('#manageModalTitle').html('<i class="bi bi-journal-plus me-2 text-primary"></i>Add New Journal');
        $('#manageSubmitBtn').removeClass('btn-warning').addClass('btn-primary').html('<i class="bi bi-save me-1"></i> Save Journal');
        $('#manageForm').attr('action', '<?= base_url('admin/journals/store') ?>');
        
        $('#manageJournalModal').modal('show');
    });

    // 3. OPEN EDIT MODAL
    $(document).on('click', '.edit-btn', function() {
        const rawData = $(this).attr('data-row');
        let rowData = JSON.parse(rawData);

        $('#manage_id').val(rowData.id);
        $('#manage_subject').val(rowData.subject);
        
        // This dynamically assigns the DB status to the select dropdown!
        $('#manage_status').val(rowData.status || 'AVAILABLE'); 
        
        $('#manage_author').val(rowData.author);
        $('#manage_date').val(rowData.date);
        $('#manage_source').val(rowData.source);
        $('#manage_session').val(rowData.session);
        $('#manage_volume').val(rowData.volume);
        $('#manage_page').val(rowData.page);

        $('#manageModalTitle').html('<i class="bi bi-pencil-square me-2 text-warning"></i>Edit Journal');
        $('#manageSubmitBtn').removeClass('btn-primary').addClass('btn-warning').html('Update Changes');
        $('#manageForm').attr('action', '<?= base_url('admin/journals/update') ?>');
        
        $('#manageJournalModal').modal('show');
    });

    // 4. OPEN DELETE MODAL
    $(document).on('click', '.delete-btn', function() {
        $('#delete_id').val($(this).data('id'));
        $('#delete_subject').val($(this).data('subject'));
        $('#deleteJournalModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>