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
                            <th width="140">ACTION</th>
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
                                    <button class="btn btn-info btn-sm text-white view-btn" title="View Details" data-row="<?= $jsonData ?>"><i class="bi bi-eye"></i></button>
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

<div class="modal fade" id="viewJournalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold text-primary">Journal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4 border-end" id="viewCoverContainer" style="min-height: 300px;">
                        </div>
                    
                    <div class="col-md-7 p-4">
                        <span id="viewStatus" class="badge mb-2 px-3 py-2"></span>
                        
                        <h4 id="viewSubject" class="fw-bold text-dark mb-1"></h4>
                        <p id="viewAuthor" class="text-primary fw-semibold mb-3"></p>
                        <hr>
                        <div class="row mt-3">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Source</small>
                                <span id="viewSource" class="fw-bold text-dark"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Date</small>
                                <span id="viewDate" class="text-dark"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Session</small>
                                <span id="viewSession" class="text-dark"></span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Volume & Page</small>
                                <span class="text-dark">Vol: <span id="viewVolume" class="fw-bold"></span> | Pg: <span id="viewPage" class="fw-bold"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
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
            
            <form id="manageForm" method="POST" enctype="multipart/form-data">
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
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-secondary">Cover Photo <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="file" name="cover_photo" id="manage_cover" class="form-control shadow-sm" accept="image/*">
                            <small class="text-muted d-block mt-1">Leave empty to keep existing image during an update.</small>
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
    $('#journalsTable').DataTable({
        "autoWidth": false,
        "order": [],
        "language": { "search": "Quick Search:" }
    });

    // Open Add Modal
    $('#openAddModalBtn').on('click', function() {
        $('#manage_id').val('');
        $('#manageForm')[0].reset(); 
        $('#manage_status').val('AVAILABLE');
        
        $('#manageModalTitle').html('<i class="bi bi-journal-plus me-2 text-primary"></i>Add New Journal');
        $('#manageSubmitBtn').removeClass('btn-warning').addClass('btn-primary').html('<i class="bi bi-save me-1"></i> Save Journal');
        $('#manageForm').attr('action', '<?= base_url('admin/journals/store') ?>');
        $('#manageJournalModal').modal('show');
    });

    // Open Edit Modal
    $(document).on('click', '.edit-btn', function() {
        const rawData = $(this).attr('data-row');
        let rowData = JSON.parse(rawData);

        $('#manage_id').val(rowData.id);
        $('#manage_subject').val(rowData.subject);
        $('#manage_status').val(rowData.status || 'AVAILABLE'); 
        $('#manage_author').val(rowData.author);
        $('#manage_date').val(rowData.date);
        $('#manage_source').val(rowData.source);
        $('#manage_session').val(rowData.session);
        $('#manage_volume').val(rowData.volume);
        $('#manage_page').val(rowData.page);
        $('#manage_cover').val(''); // Clear file input on edit

        $('#manageModalTitle').html('<i class="bi bi-pencil-square me-2 text-warning"></i>Edit Journal');
        $('#manageSubmitBtn').removeClass('btn-primary').addClass('btn-warning').html('Update Changes');
        $('#manageForm').attr('action', '<?= base_url('admin/journals/update') ?>');
        $('#manageJournalModal').modal('show');
    });

    // View Details Logic
    $(document).on('click', '.view-btn', function() {
        const rawData = $(this).attr('data-row');
        let book = JSON.parse(rawData);

        // Text details
        $('#viewSubject').text(book.subject || 'No Subject');
        $('#viewAuthor').text(book.author || 'Unknown Author');
        $('#viewSource').text(book.source || 'N/A');
        $('#viewDate').text(book.date || 'N/A');
        $('#viewSession').text(book.session || 'N/A');
        $('#viewVolume').text(book.volume || '-');
        $('#viewPage').text(book.page || '-');

        // Status Badge
        let statusBadge = $('#viewStatus');
        statusBadge.text(book.status || 'AVAILABLE');
        if((book.status || 'AVAILABLE') === 'AVAILABLE') {
            statusBadge.attr('class', 'badge bg-success bg-opacity-10 text-success border border-success mb-2 px-3 py-2');
        } else {
            statusBadge.attr('class', 'badge bg-danger bg-opacity-10 text-danger border border-danger mb-2 px-3 py-2');
        }

        // Image Handling
        let coverContainer = $('#viewCoverContainer');
        if (book.cover_photo) {
            let imgUrl = "<?= base_url('uploads/covers/') ?>" + book.cover_photo;
            coverContainer.html(`<img src="${imgUrl}" class="img-fluid rounded shadow" style="max-height: 350px; object-fit: contain;" alt="Cover">`);
        } else {
            coverContainer.html(`<i class="bi bi-journal-text text-secondary" style="font-size: 8rem;"></i>`);
        }

        $('#viewJournalModal').modal('show');
    });

    // Open Delete Form
    $(document).on('click', '.delete-btn', function() {
        $('#delete_id').val($(this).data('id'));
        $('#delete_subject').val($(this).data('subject'));
        $('#deleteJournalModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>