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
                            <th width="150" class="text-center">Action</th>
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
                                        <img src="<?= base_url('uploads/covers/' . esc($row['cover_photo'])) ?>" 
                                            class="rounded me-3 shadow-sm zoomable-cover" 
                                            style="width: 40px; height: 55px; object-fit: cover; cursor: pointer;"
                                            onclick="openZoom(this.src)" title="Click to enlarge">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 40px; height: 55px; color: #1e3a8a;">
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
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border text-primary view-btn" title="View Details" data-row="<?= $jsonData ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        <?php if(!empty($row['soft_copy'])): ?>
                                            <button class="btn btn-sm btn-light border text-danger" title="Read PDF" onclick="viewPDF('<?= base_url('uploads/softcopies/' . $row['soft_copy']) ?>')">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if(!empty($row['url'])): ?>
                                            <a href="<?= esc($row['url']) ?>" target="_blank" class="btn btn-sm btn-light border text-info" title="Visit Link">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        <?php endif; ?>

                                        <button class="btn btn-sm btn-light border text-warning edit-btn" title="Edit" data-row="<?= $jsonData ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border text-danger delete-btn" title="Delete" data-id="<?= $row['id'] ?>" data-title="<?= esc($row['title']) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
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

<div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="" id="pdfFrame" width="100%" height="700px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 shadow-none text-center">
            <img src="" id="fullSizeImage" class="img-fluid rounded shadow-lg" style="max-height: 85vh; border: 5px solid white;">
            <div class="mt-2"><button class="btn btn-light rounded-pill" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="manageCollectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom text-secondary">
                <h5 class="modal-title fw-bold" id="manageModalTitle">Manage Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="manageForm" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="manage_id">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-4">
                        <div class="col-lg-6 border-end px-3">
                            <h6 class="fw-bold text-dark mb-3">Primary Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label small">Type</label><input list="typeOptions" name="type" id="manage_type" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small">Reading</label><input list="readingOptions" name="reading" id="manage_reading" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small">Class</label><input list="classOptions" name="class" id="manage_class" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small">Status</label><select name="status" id="manage_status" class="form-select text-success fw-bold"><option value="AVAILABLE">AVAILABLE</option><option value="BORROWED">BORROWED</option><option value="LOST">LOST</option><option value="DAMAGED">DAMAGED</option></select></div>
                                <div class="col-12"><label class="form-label small">Title</label><textarea name="title" id="manage_title" class="form-control" rows="2" required></textarea></div>
                                <div class="col-12"><label class="form-label small">Subject</label><input list="subjectOptions" name="subject" id="manage_subject" class="form-control"></div>
                                <div class="col-md-8"><label class="form-label small">Author</label><input list="authorOptions" name="author" id="manage_author" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label small">Publisher</label><input type="text" name="publisher" id="manage_publisher" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small">Place</label><input type="text" name="place" id="manage_place" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small">Issued Date</label><input type="date" name="issued_date" id="manage_issued_date" class="form-control"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 px-3">
                            <h6 class="fw-bold text-dark mb-3">Inventory & Files</h6>
                            <div class="row g-3">
                                <div class="col-md-3"><label class="form-label small">Accession No.</label><input type="text" name="accession_no" id="manage_accession_no" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label small">Volume</label><input type="text" name="volume" id="manage_volume" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label small">ISBN</label><input type="text" name="isbn" id="manage_isbn" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label small">Series</label><input type="text" name="series" id="manage_series" class="form-control"></div>
                                <div class="col-12"><label class="form-label small">Location</label><input type="text" name="location" id="manage_location" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small">Date Acquired</label><input type="date" name="date_acquired" id="manage_date_acquired" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label small">Date Received</label><input type="date" name="date_received" id="manage_date_received" class="form-control"></div>
                                <div class="col-12"><label class="form-label small">Remarks</label><textarea name="remarks" id="manage_remarks" class="form-control" rows="1"></textarea></div>
                                <div class="col-12 mt-3 file-upload-group"><label class="form-label small">Update Cover Photo</label><input type="file" name="cover_photo" class="form-control" accept="image/*"></div>
                                <div class="col-12 file-upload-group"><label class="form-label small">Update Soft Copy (PDF)</label><input type="file" name="soft_copy" class="form-control" accept=".pdf"></div>
                                <div class="col-12"><label class="form-label small">Digital URL</label><input type="url" name="url" id="manage_url" class="form-control" placeholder="https://"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" id="manageSubmitBtn" class="btn btn-warning fw-bold">Update Changes</button></div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    // 1. Initialize DataTable
    const table = $('#collectionTable').DataTable({
        "autoWidth": false,
        "order": [],
        "language": { "emptyTable": "No collections found." }
    });

    // 2. PDF Function
    window.viewPDF = function(url) {
        $('#pdfFrame').attr('src', url);
        $('#pdfViewerModal').modal('show');
    };

    // 3. PDF Preview Before Save
    $('input[name="soft_copy"]').on('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type === "application/pdf") {
            const fileURL = URL.createObjectURL(file);
            if(confirm("Review selected PDF before saving?")) {
                viewPDF(fileURL);
            }
        }
    });

    // 4. Image Zoom
    window.openZoom = function(src) {
        $('#fullSizeImage').attr('src', src);
        $('#imagePreviewModal').modal('show');
    };

    // 5. Manage Logic
    $(document).on('click', '.view-btn, .edit-btn', function() {
        const isEditMode = $(this).hasClass('edit-btn');
        const rowData = JSON.parse($(this).attr('data-row'));

        $('#manage_id').val(rowData.id);
        $('#manage_title').val(rowData.title);
        $('#manage_type').val(rowData.type);
        $('#manage_author').val(rowData.author);
        $('#manage_status').val(rowData.status);
        $('#manage_class').val(rowData.class);
        $('#manage_subject').val(rowData.subject);
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

        if (isEditMode) {
            $('#manageModalTitle').text('Edit Collection');
            $('#manageForm').find('input, select, textarea').prop('disabled', false);
            $('.file-upload-group').show(); 
            $('#manageSubmitBtn').show(); 
            $('#manageForm').attr('action', '<?= base_url('admin/collections/update') ?>');
        } else {
            $('#manageModalTitle').text('View Details');
            $('#manageForm').find('input, select, textarea').prop('disabled', true);
            $('.file-upload-group').hide(); 
            $('#manageSubmitBtn').hide(); 
        }
        $('#manageCollectionModal').modal('show');
    });

    // 6. Delete Logic
    $(document).on('click', '.delete-btn', function() {
        $('#delete_collection_id').val($(this).attr('data-id'));
        $('#deleteCollectionModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>