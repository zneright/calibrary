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
                <input type="text" name="q" class="form-control border-0 shadow-sm" placeholder="Search title, author, or keyword..." value="<?= isset($_GET['q']) ? esc($_GET['q']) : '' ?>" style="border-radius: 0.375rem 0 0 0.375rem;">
                <button class="btn btn-primary fw-semibold shadow-sm" type="submit" style="background-color: #1e3a8a; border-color: #1e3a8a; border-radius: 0 0.375rem 0.375rem 0;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-funnel me-2" style="color: #1e3a8a;"></i>Filter Results</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/borrower/catalog" method="GET">
                        
                        <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Resource Type</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" id="typeBook" checked>
                            <label class="form-check-label small" for="typeBook">Books</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" id="typeJournal" checked>
                            <label class="form-check-label small" for="typeJournal">Journals</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" id="typePD">
                            <label class="form-check-label small" for="typePD">Presidential Decrees</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input shadow-sm" type="checkbox" id="typeEO">
                            <label class="form-check-label small" for="typeEO">Executive Orders</label>
                        </div>

                        <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Availability</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="radio" name="status" id="statusAll" checked>
                            <label class="form-check-label small" for="statusAll">Show All</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input shadow-sm" type="radio" name="status" id="statusAvail">
                            <label class="form-check-label small" for="statusAvail">Available Only</label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 fw-semibold shadow-sm text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted">Showing <strong>1-3</strong> of <strong>45</strong> results</p>
                <select class="form-select form-select-sm border-0 shadow-sm w-auto bg-white">
                    <option value="recent">Sort by: Newest First</option>
                    <option value="title_asc">Sort by: Title (A-Z)</option>
                    <option value="title_desc">Sort by: Title (Z-A)</option>
                </select>
            </div>

            <div class="card border-0 shadow-sm mb-3 position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 h-100" style="width: 4px; background-color: #0f766e;"></div>
                <div class="card-body p-4 ms-2">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-light text-dark border me-2"><i class="bi bi-file-earmark-text me-1 text-primary"></i> Executive Order</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="bi bi-check-circle me-1"></i> Available</span>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: #1a2942;">GRANTING SALARY INCREASE TO CAREER EXEC. SERVICE OFFICERS</h5>
                            <p class="text-muted small mb-2">Call No: EO-1992-005 • Published: 1992</p>
                            <p class="text-secondary small mb-0 d-none d-md-block">An executive order detailing the salary adjustments for specific government service officers within the commission...</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
<button class="btn btn-outline-dark w-100 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#requestModal" onclick="openRequestModal('GRANTING SALARY INCREASE TO CAREER EXEC. SERVICE OFFICERS', 'B-001')">
    <i class="bi bi-cart-plus me-1"></i> Request
</button>                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3 position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 h-100" style="width: 4px; background-color: #7f1d1d;"></div>
                <div class="card-body p-4 ms-2">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-light text-dark border me-2"><i class="bi bi-journals me-1 text-primary"></i> Journal</span>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="bi bi-x-circle me-1"></i> Currently Borrowed</span>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: #1a2942;">CA MEMBERS -- HOUSE OF REP. -- 8TH CONGRESS</h5>
                            <p class="text-muted small mb-2">Call No: JRN-8TH-001 • Published: 1989</p>
                            <p class="text-secondary small mb-0 d-none d-md-block">Journal covering the sessions, resolutions, and attendance of the 8th Congress House of Representatives.</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-light w-100 shadow-sm text-muted fw-semibold border"><i class="bi bi-bell me-1"></i> Notify Me</button>
                            <small class="d-block text-danger mt-1" style="font-size: 0.7rem;">Due: Feb 25, 2026</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4 position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 h-100" style="width: 4px; background-color: #0f766e;"></div>
                <div class="card-body p-4 ms-2">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-light text-dark border me-2"><i class="bi bi-book me-1 text-primary"></i> Book</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="bi bi-check-circle me-1"></i> Available</span>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: #1a2942;">Parliamentary Procedures and Practices</h5>
                            <p class="text-muted small mb-2">Call No: QA76.73 • Author: Dr. Maria Santos • Published: 2018</p>
                            <p class="text-secondary small mb-0 d-none d-md-block">A comprehensive guide on parliamentary procedures tailored for the Philippine Senate and Commission on Appointments.</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-outline-dark w-100 shadow-sm fw-semibold"><i class="bi bi-cart-plus me-1"></i> Request</button>
                        </div>
                    </div>
                </div>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link border-0 shadow-sm text-secondary" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link border-0 shadow-sm" href="#" style="background-color: #1e3a8a; border-color: #1e3a8a;">1</a></li>
                    <li class="page-item"><a class="page-link border-0 shadow-sm text-dark" href="#">2</a></li>
                    <li class="page-item"><a class="page-link border-0 shadow-sm text-dark" href="#">3</a></li>
                    <li class="page-item"><a class="page-link border-0 shadow-sm text-dark" href="#">Next</a></li>
                </ul>
            </nav>
    
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
                        
                        <input type="hidden" name="book_id" id="modalBookId">
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