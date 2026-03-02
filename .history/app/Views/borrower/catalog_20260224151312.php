<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="py-4 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #1a2942 0%, #0f172a 100%);">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <h3 class="fw-bold mb-1">Library Catalog</h3>
                <p class="mb-0 text-white-50 small">Search and request resources from the Commission on Appointments</p>
            </div>
            <form action="/borrower/catalog" method="GET" class="d-flex w-100" style="max-width: 400px;">
                <?php if(!empty($selectedTypes)): foreach($selectedTypes as $t): ?>
                    <input type="hidden" name="type[]" value="<?= esc($t) ?>">
                <?php endforeach; endif; ?>
                <input type="hidden" name="status" value="<?= esc($selectedStatus) ?>">
                
                <input type="text" name="q" class="form-control border-0 shadow-sm" placeholder="Search title, author, or keyword..." value="<?= esc($search) ?>" style="border-radius: 0.375rem 0 0 0.375rem;">
                <button class="btn btn-primary fw-semibold shadow-sm" type="submit" style="background-color: #1e3a8a; border-color: #1e3a8a; border-radius: 0 0.375rem 0.375rem 0;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="container mb-5">
    
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="row g-4 mt-1">
        ```

Save your files and try submitting a book request again! The 404 error will be gone, a green success banner will appear at the top of the catalog, and you will see the action beautifully recorded in your Admin Logs!

**To make these book requests fully functional, we need to build the `Transactions` database table next so the Admin can actually view and approve them. Should we build the Transactions module, or set up the Logout function first?**
    <div class="row g-4">
        
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-funnel me-2" style="color: #1e3a8a;"></i>Filter Results</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/borrower/catalog" method="GET">
                        <?php if(!empty($search)): ?>
                            <input type="hidden" name="q" value="<?= esc($search) ?>">
                        <?php endif; ?>

                        <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Resource Type</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Book" id="typeBook" <?= in_array('Book', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typeBook">Books</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Journal" id="typeJournal" <?= in_array('Journal', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typeJournal">Journals</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Presidential Decree" id="typePD" <?= in_array('Presidential Decree', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typePD">Presidential Decrees</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Executive Order" id="typeEO" <?= in_array('Executive Order', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typeEO">Executive Orders</label>
                        </div>

                        <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Availability</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="radio" name="status" value="all" id="statusAll" <?= $selectedStatus == 'all' ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="statusAll">Show All</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input shadow-sm" type="radio" name="status" value="available" id="statusAvail" <?= $selectedStatus == 'available' ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="statusAvail">Available Only</label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 fw-semibold shadow-sm text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted">Found <strong><?= $total_results ?></strong> results</p>
            </div>

            <?php if(!empty($items)): ?>
                <?php foreach($items as $item): ?>
                    <?php 
                        $borderColor = '#0f766e'; // Default Teal
                        if($item['type'] == 'Journal') $borderColor = '#7f1d1d'; // Red
                        if($item['type'] == 'Executive Order') $borderColor = '#1e3a8a'; // Blue
                    ?>

                    <div class="card border-0 shadow-sm mb-3 position-relative overflow-hidden">
                        <div class="position-absolute top-0 start-0 h-100" style="width: 4px; background-color: <?= $borderColor ?>;"></div>
                        <div class="card-body p-4 ms-2">
                            <div class="row align-items-center">
                                <div class="col-md-9 d-flex align-items-start">
                                    
                                    <?php if($item['cover_photo']): ?>
                                        <img src="/uploads/covers/<?= esc($item['cover_photo']) ?>" class="rounded shadow-sm me-4" style="width: 70px; height: 95px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex justify-content-center align-items-center me-4 shadow-sm" style="width: 70px; height: 95px; color: <?= $borderColor ?>;">
                                            <i class="bi bi-book fs-2"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-light text-dark border me-2"><i class="bi bi-tag me-1" style="color: <?= $borderColor ?>;"></i> <?= esc($item['type'] ?? 'Book') ?></span>
                                            
                                            <?php if($item['status'] == 'AVAILABLE'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="bi bi-check-circle me-1"></i> Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="bi bi-x-circle me-1"></i> <?= esc($item['status']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h5 class="fw-bold mb-1" style="color: #1a2942;"><?= esc($item['title']) ?></h5>
                                        <p class="text-muted small mb-2">
                                            Class/Call No: <?= esc($item['class'] ?? 'N/A') ?> 
                                            <?= $item['author'] ? ' • Author: ' . esc($item['author']) : '' ?> 
                                            <?= $item['issued_date'] ? ' • Published: ' . date('Y', strtotime($item['issued_date'])) : '' ?>
                                        </p>
                                        <p class="text-secondary small mb-0 d-none d-md-block text-truncate" style="max-width: 600px;">
                                            <?= esc($item['subject']) ?>
                                        </p>
                                    </div>
                                </div>
                                
                          <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                        <?php if (in_array($item['id'], $existingRequests)): ?>
                                            <button class="btn btn-secondary w-100 shadow-sm fw-semibold" disabled>
                                                <i class="bi bi-check2-all me-1"></i> Requested
                                            </button>
                                        <?php elseif ($item['status'] == 'AVAILABLE'): ?>
                                            <button class="btn btn-outline-dark w-100 shadow-sm fw-semibold" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#requestModal" 
                                                    onclick="openRequestModal('<?= esc(addslashes($item['title'])) ?>', '<?= $item['id'] ?>')">
                                                <i class="bi bi-cart-plus me-1"></i> Request
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-light w-100 shadow-sm text-muted fw-semibold border" disabled>
                                                <i class="bi bi-lock me-1"></i> Unavailable
                                            </button>
                                        <?php endif; ?>
                                    </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-4 d-flex justify-content-center">
                    <?= $pager->links() ?>
                </div>

            <?php else: ?>
                <div class="text-center py-5 bg-white rounded shadow-sm border-0">
                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold text-dark mt-3">No resources found</h5>
                    <p class="text-muted">Try adjusting your search terms or filters.</p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<div class="modal fade" id="requestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header text-white" style="background-color: #1a2942;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-journal-plus me-2"></i>Resource Request Form
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/borrower/request/submit" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="alert alert-info bg-opacity-10 border-info border-opacity-25 small mb-4">
                        <i class="bi bi-info-circle-fill me-2 text-info"></i>
                        Requests are subject to approval by the Library Administrator.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Resource Title</label>
                        <input type="text" class="form-control bg-light text-muted fw-semibold" id="modalBookTitle" readonly>
                        <input type="hidden" name="collection_id" id="modalBookId">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Date Needed <span class="text-danger">*</span></label>
                        <input type="date" name="date_needed" class="form-control shadow-sm" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Reason / Purpose for Borrowing <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control shadow-sm" rows="3" placeholder="e.g., Needed for research regarding the upcoming committee hearing..." required></textarea>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm fw-semibold" style="background-color: #1e3a8a; border-color: #1e3a8a;">
                        <i class="bi bi-send me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openRequestModal(title, id) {
        document.getElementById('modalBookTitle').value = title;
        document.getElementById('modalBookId').value = id;
    }
</script>
<?= $this->endSection() ?>