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
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-collection me-2 text-primary"></i>Library Collections
            </h5>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                <i class="bi bi-plus-lg me-1"></i> Add Collection
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="collectionTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title & Type</th>
                            <th>Accession No</th>
                            <th>Call No (Class)</th>
                            <th>Author</th>
                            <th class="text-center">Status</th>
                            <th width="120" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle" style="font-size: 0.9rem;">
                        <?php if (!empty($collections)): ?>
                            <?php foreach($collections as $i => $row): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($row['cover_photo']): ?>
                                            <img src="/uploads/covers/<?= esc($row['cover_photo']) ?>" class="rounded me-3 shadow-sm" style="width: 40px; height: 55px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 55px; color: #1e3a8a;">
                                                <i class="bi bi-book fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div>
                                            <span class="fw-bold text-dark d-block text-truncate" style="max-width: 250px;" title="<?= esc($row['title']) ?>">
                                                <?= esc($row['title']) ?>
                                            </span>
                                            <span class="badge bg-secondary opacity-75 fw-normal" style="font-size: 0.7rem;">
                                                <?= esc($row['type'] ?? 'Book') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="text-muted fw-semibold font-monospace small"><?= esc($row['accession_no'] ?? 'N/A') ?></td>
                                <td><?= esc($row['class'] ?? 'N/A') ?></td>
                                <td><?= esc($row['author'] ?? 'Unknown') ?></td>
                                
                                <td class="text-center">
                                    <?php 
                                        $badge = 'bg-secondary';
                                        if($row['status'] == 'AVAILABLE') $badge = 'bg-success';
                                        if($row['status'] == 'BORROWED') $badge = 'bg-warning text-dark';
                                        if($row['status'] == 'LOST' || $row['status'] == 'DAMAGED') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge ?> rounded-pill px-3"><?= esc($row['status']) ?></span>
                                </td>
                                
                                <td class="text-center">
                                    <?php $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
                                    
                                    <button class="btn btn-sm btn-light border text-primary view-btn" title="View Details" data-row="<?= $jsonData ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border text-warning mx-1 edit-btn" title="Edit" data-row="<?= $jsonData ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border text-danger delete-btn" title="Delete" data-id="<?= $row['id'] ?>" data-title="<?= esc($row['title']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="manageCollectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom text-secondary">
                <h5 class="modal-title fw-bold" id="manageModalTitle">Manage Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="manageForm" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="manage_id">
                
                <div class="modal-body p-4 bg-white">
                    <div class="row g-4">
                        <div class="col-lg-6 border-end px-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2"></i>Primary Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Type</label>
                                    <input list="typeOptions" name="type" id="manage_type" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Reading</label>
                                    <input list="readingOptions" name="reading" id="manage_reading" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Class</label>
                                    <input list="classOptions" name="class" id="manage_class" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Status</label>
                                    <select name="status" id="manage_status" class="form-select shadow-sm border-success text-success fw-bold" required>
                                        <option value="AVAILABLE">AVAILABLE</option>
                                        <option value="BORROWED">BORROWED</option>
                                        <option value="LOST">LOST</option>
                                        <option value="DAMAGED">DAMAGED</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Title <span class="text-danger">*</span></label>
                                    <textarea name="title" id="manage_title" class="form-control shadow-sm" rows="2" required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Subject</label>
                                    <input list="subjectOptions" name="subject" id="manage_subject" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small text-secondary">Author</label>
                                    <input list="authorOptions" name="author" id="manage_author" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-secondary">Publisher</label>
                                    <input type="text" name="publisher" id="manage_publisher" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Place</label>
                                    <input type="text" name="place" id="manage_place" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Issued Date</label>
                                    <input type="date" name="issued_date" id="manage_issued_date" class="form-control shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 px-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam me-2"></i>Inventory & Files</h6>
                            <div class="row g-3">
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">Accession No.</label>
                                    <input type="text" name="accession_no" id="manage_accession_no" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">Volume</label>
                                    <input type="text" name="volume" id="manage_volume" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">ISBN</label>
                                    <input type="text" name="isbn" id="manage_isbn" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">Series</label>
                                    <input type="text" name="series" id="manage_series" class="form-control shadow-sm">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Location</label>
                                    <input type="text" name="location" id="manage_location" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Date Acquired</label>
                                    <input type="date" name="date_acquired" id="manage_date_acquired" class="form-control shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Date Received</label>
                                    <input type="date" name="date_received" id="manage_date_received" class="form-control shadow-sm">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Remarks</label>
                                    <textarea name="remarks" id="manage_remarks" class="form-control shadow-sm" rows="1"></textarea>
                                </div>
                                <div class="col-12 mt-3 file-upload-group">
                                    <label class="form-label fw-semibold small text-secondary"><i class="bi bi-image me-1"></i>Update Cover Photo</label>
                                    <input type="file" name="cover_photo" class="form-control form-control-sm shadow-sm" accept="image/*">
                                </div>
                                <div class="col-12 file-upload-group">
                                    <label class="form-label fw-semibold small text-secondary"><i class="bi bi-file-pdf me-1"></i>Update Soft Copy (PDF)</label>
                                    <input type="file" name="soft_copy" class="form-control form-control-sm shadow-sm" accept=".pdf,.doc,.docx">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Digital URL</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" name="url" id="manage_url" class="form-control" placeholder="https://">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary shadow-sm px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="manageSubmitBtn" class="btn btn-warning shadow-sm px-4 fw-bold">
                        Update Changes
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
    $('#collectionTable').DataTable({
        "autoWidth": false,
        "scrollY": "400px",
        "scrollCollapse": true,
        "order": [],
        "language": { "emptyTable": "No collections found. The table is currently empty." }
    });

    // Handle View and Edit button clicks
    $(document).on('click', '.view-btn, .edit-btn', function() {
        // Parse the JSON data stored on the button
        let rowData = $(this).data('row');
        let isEditMode = $(this).hasClass('edit-btn');

        // Populate the form fields with the database data
        $('#manage_id').val(rowData.id);
        $('#manage_type').val(rowData.type);
        $('#manage_reading').val(rowData.reading);
        $('#manage_class').val(rowData.class);
        $('#manage_status').val(rowData.status);
        $('#manage_title').val(rowData.title);
        $('#manage_subject').val(rowData.subject);
        $('#manage_author').val(rowData.author);
        $('#manage_publisher').val(rowData.publisher);
        $('#manage_place').val(rowData.place);
        $('#manage_issued_date').val(rowData.issued_date);
        $('#manage_accession_no').val(rowData.accession_no);
        $('#manage_volume').val(rowData.volume);
        $('#manage_isbn').val(rowData.isbn);
        $('#manage_series').val(rowData.series);
        $('#manage_location').val(rowData.location);
        $('#manage_date_acquired').val(rowData.date_acquired);
        $('#manage_date_received').val(rowData.date_received);
        $('#manage_remarks').val(rowData.remarks);
        $('#manage_url').val(rowData.url);

        // Toggle form behavior based on View vs Edit mode
        if (isEditMode) {
            $('#manageModalTitle').html('<i class="bi bi-pencil-square me-2 text-warning"></i>Edit Collection Details');
            $('#manageForm input, #manageForm select, #manageForm textarea').prop('disabled', false);
            $('.file-upload-group').show(); // Show file upload fields
            $('#manageSubmitBtn').show(); // Show save button
            $('#manageForm').attr('action', '/admin/collections/update');
        } else {
            $('#manageModalTitle').html('<i class="bi bi-eye me-2 text-primary"></i>View Collection Details');
            $('#manageForm input, #manageForm select, #manageForm textarea').prop('disabled', true);
            $('.file-upload-group').hide(); // Hide file uploads since it's view-only
            $('#manageSubmitBtn').hide(); // Hide save button
        }

        $('#manageCollectionModal').modal('show');
    });

    // Delete Modal Logic
    $(document).on('click', '.delete-btn', function() {
        $('#delete_collection_id').val($(this).data('id'));
        $('#delete_collection_title').val($(this).data('title'));
        $('#deleteCollectionModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>