<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

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
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Call No</th>
                            <th>Author</th>
                            <th class="text-center">Status</th>
                            <th width="120" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($collections)): ?>
                            <?php foreach($collections as $i => $row): ?>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCollectionModal" tabindex="-1" aria-labelledby="addCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-light border-bottom text-secondary">
                <h5 class="modal-title fw-bold" id="addCollectionModalLabel">
                    <i class="bi bi-journal-plus me-2 text-primary"></i>Add New Collection
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/admin/collections/store" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="modal-body p-4 bg-white">
                    <div class="row g-4">
                        
                        <div class="col-lg-6 border-end px-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2"></i>Primary Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Type</label>
                                    <select name="type" class="form-select shadow-sm">
                                        <option value="" selected>Select or Add New</option>
                                        <option value="Book">Book</option>
                                        <option value="Journal">Journal</option>
                                        <option value="Presidential Decree">Presidential Decree</option>
                                        <option value="Executive Order">Executive Order</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Reading</label>
                                    <select name="reading" class="form-select shadow-sm">
                                        <option value="" selected>Select or Add New</option>
                                        <option value="General">General</option>
                                        <option value="Restricted">Restricted</option>
                                    </select>
                                </div>

                             <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Class</label>
                                    <input list="classOptions" name="class" class="form-control shadow-sm" placeholder="Select or type new class" value="<?= old('class') ?>">
                                    <datalist id="classOptions">
                                        <?php foreach($classes as $c): ?>
                                            <option value="<?= esc($c['class']) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select shadow-sm border-success text-success fw-bold" required>
                                        <option value="AVAILABLE" selected>AVAILABLE</option>
                                        <option value="BORROWED">BORROWED</option>
                                        <option value="LOST">LOST</option>
                                        <option value="DAMAGED">DAMAGED</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Title <span class="text-danger">*</span></label>
                                    <textarea name="title" class="form-control shadow-sm" rows="2" placeholder="Enter complete title" required><?= old('title') ?></textarea>
                                </div>

                             <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Subject</label>
                                    <input list="subjectOptions" name="subject" class="form-control shadow-sm" placeholder="Select or type new subject" value="<?= old('subject') ?>">
                                    <datalist id="subjectOptions">
                                        <?php foreach($subjects as $s): ?>
                                            <option value="<?= esc($s['subject']) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>

                               <div class="col-md-8">
                                    <label class="form-label fw-semibold small text-secondary">Author</label>
                                    <input list="authorOptions" name="author" class="form-control shadow-sm" placeholder="Select or type new author" value="<?= old('author') ?>">
                                    <datalist id="authorOptions">
                                        <?php foreach($authors as $a): ?>
                                            <option value="<?= esc($a['author']) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-secondary">Publisher</label>
                                    <input type="text" name="publisher" class="form-control shadow-sm" value="<?= old('publisher') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Place</label>
                                    <input type="text" name="place" class="form-control shadow-sm" value="<?= old('place') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Issued Date</label>
                                    <input type="date" name="issued_date" class="form-control shadow-sm" value="<?= old('issued_date') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 px-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam me-2"></i>Inventory & Files</h6>
                            <div class="row g-3">
                                
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">Accession No.</label>
                                    <input type="text" name="accession_no" class="form-control shadow-sm" value="<?= old('accession_no') ?>">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">Volume</label>
                                    <input type="text" name="volume" class="form-control shadow-sm" value="<?= old('volume') ?>">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">ISBN</label>
                                    <input type="text" name="isbn" class="form-control shadow-sm" value="<?= old('isbn') ?>">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label fw-semibold small text-secondary">Series</label>
                                    <input type="text" name="series" class="form-control shadow-sm" value="<?= old('series') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Location</label>
                                    <input type="text" name="location" class="form-control shadow-sm" placeholder="e.g. Shelf A, Row 3" value="<?= old('location') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Date Acquired</label>
                                    <input type="date" name="date_acquired" class="form-control shadow-sm" value="<?= old('date_acquired') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Date Received</label>
                                    <input type="date" name="date_received" class="form-control shadow-sm" value="<?= old('date_received') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Remarks</label>
                                    <textarea name="remarks" class="form-control shadow-sm" rows="1"><?= old('remarks') ?></textarea>
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label fw-semibold small text-secondary"><i class="bi bi-image me-1"></i>Cover Photo</label>
                                    <input type="file" name="cover_photo" class="form-control form-control-sm shadow-sm" accept="image/*">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary"><i class="bi bi-file-pdf me-1"></i>Soft Copy (PDF)</label>
                                    <input type="file" name="soft_copy" class="form-control form-control-sm shadow-sm" accept=".pdf,.doc,.docx">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Digital URL</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" name="url" class="form-control" placeholder="https://" value="<?= old('url') ?>">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary shadow-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Save Collection
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
        "language": { "emptyTable": "No collections found. The table is currently empty." }
    });

    // If there are validation errors, reopen the modal automatically
    <?php if (session()->has('errors')) : ?>
        var myModal = new bootstrap.Modal(document.getElementById('addCollectionModal'));
        myModal.show();
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>