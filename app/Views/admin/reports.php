<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php 
    $showReports = true; // Simulating that a report was generated
    $showCollections = true; 
    $showJournals = true;
    $showTransactions = true;
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary mb-0">
            <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Advanced Reports & Filtering
        </h4>
        <div class="d-flex gap-2">
            <a href="/reports/export" class="btn btn-danger btn-sm shadow-sm fw-semibold" title="Export All to PDF">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export to PDF
            </a>
            <a href="/reports/export-excel" class="btn btn-success btn-sm shadow-sm fw-semibold" title="Export All to Excel">
                <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Export to Excel
            </a>
        </div>
    </div>

    <div class="card bg-white border-0 shadow-sm mb-4" style="border-top: 4px solid #1e3a8a;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-ui-checks-grid me-2 text-primary"></i>Select Report Categories</h6>
            <p class="text-muted small mb-4">Check the modules you want to include in your generated report, then configure their specific filters.</p>
            
            <div class="d-flex flex-wrap align-items-center gap-4">
                <div class="form-check custom-checkbox">
                    <input class="form-check-input filter-checkbox shadow-sm" type="checkbox" id="catCollections" value="collections" checked style="transform: scale(1.2); margin-right: 8px;">
                    <label class="form-check-label fw-bold text-secondary" for="catCollections">Collections Inventory</label>
                </div>
                <div class="form-check custom-checkbox">
                    <input class="form-check-input filter-checkbox shadow-sm" type="checkbox" id="catJournals" value="journals" checked style="transform: scale(1.2); margin-right: 8px;">
                    <label class="form-check-label fw-bold text-secondary" for="catJournals">Journals Directory</label>
                </div>
                <div class="form-check custom-checkbox">
                    <input class="form-check-input filter-checkbox shadow-sm" type="checkbox" id="catTransactions" value="transactions" checked style="transform: scale(1.2); margin-right: 8px;">
                    <label class="form-check-label fw-bold text-secondary" for="catTransactions">Library Transactions</label>
                </div>
                
                <button type="button" class="btn btn-dark px-4 fw-bold shadow ms-auto" id="btnOpenFilter">
                    Configure Filters <i class="bi bi-sliders ms-1"></i>
                </button>
            </div>
        </div>
    </div>

    <?php if (isset($showReports) && $showReports): ?>
        
        <h5 class="fw-bold text-dark mb-3 mt-5 border-bottom pb-2">Generated Report Results</h5>

        <?php if (isset($showCollections) && $showCollections): ?>
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header text-white py-3 d-flex justify-content-between align-items-center" style="background-color: #1e3a8a;">
                <h6 class="mb-0 fw-bold"><i class="bi bi-book me-2"></i>Collections Report</h6>
                <span class="badge bg-light text-dark fw-semibold">120 Records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-hover table-bordered align-middle w-100 report-table">
                        <thead class="table-light text-muted small text-center text-uppercase">
                            <tr>
                                <th>Accession No.</th>
                                <th class="text-start">Title</th>
                                <th>Author</th>
                                <th>Call No.</th>
                                <th>Issued Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center small">
                            <tr>
                                <td class="fw-semibold text-secondary">ACC-00102</td>
                                <td class="text-start fw-bold text-dark">The 1987 Constitution of the Philippines</td>
                                <td>De Leon, Hector S.</td>
                                <td>KF5624.1 1987</td>
                                <td>1987-02-02</td>
                                <td><span class="badge bg-success bg-opacity-10 text-success border border-success">AVAILABLE</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($showJournals) && $showJournals): ?>
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header bg-info text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-journals me-2"></i>Journals Report</h6>
                <span class="badge bg-light text-dark fw-semibold">45 Records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-hover table-bordered align-middle w-100 report-table">
                        <thead class="table-light text-muted small text-center text-uppercase">
                            <tr>
                                <th class="text-start">Subject / Title</th>
                                <th>Author</th>
                                <th>Source</th>
                                <th>Vol & Page</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center small">
                            <tr>
                                <td class="text-start fw-bold text-dark">Supreme Court Reports Annotated</td>
                                <td>Central Book Supply</td>
                                <td>SCRA</td>
                                <td>Vol 45, Pg 112</td>
                                <td>2020-05-15</td>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger">BORROWED</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($showTransactions) && $showTransactions): ?>
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header text-white py-3 d-flex justify-content-between align-items-center" style="background-color: #0f766e;">
                <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Transactions Report</h6>
                <span class="badge bg-light text-dark fw-semibold">8 Active</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-hover table-bordered align-middle w-100 report-table">
                        <thead class="table-light text-muted small text-center text-uppercase">
                            <tr>
                                <th>Trans. ID</th>
                                <th class="text-start">Borrower Name</th>
                                <th class="text-start">Item Details</th>
                                <th>Requested On</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center small">
                            <tr>
                                <td class="fw-bold text-secondary">TRX-00045</td>
                                <td class="text-start fw-semibold">Nishia Pinlac <br><small class="text-muted">ID: 2026-001</small></td>
                                <td class="text-start">GRANTING SALARY INCREASE...<br><small class="text-muted">Call No: EO-1992</small></td>
                                <td>Feb 18, 2026</td>
                                <td class="fw-bold text-danger">Feb 21, 2026</td>
                                <td><span class="badge bg-warning text-dark border border-warning">PENDING RENEWAL</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<div class="modal fade" id="modalAdvancedFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <form action="/reports/generate" method="GET" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-bottom-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-funnel-fill me-2"></i>Configure Report Filters</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0 bg-light">
                <ul class="nav nav-tabs nav-fill bg-white pt-3 px-3 border-bottom shadow-sm" id="filterTabs" role="tablist">
                    <li class="nav-item" id="nav-collections" style="display:none;">
                        <button class="nav-link fw-bold pb-3 active" id="tab-collections" data-bs-toggle="tab" data-bs-target="#pane-collections" type="button" role="tab" style="color: #1e3a8a;"><i class="bi bi-book me-2"></i>Collections</button>
                    </li>
                    <li class="nav-item" id="nav-journals" style="display:none;">
                        <button class="nav-link fw-bold pb-3 text-info" id="tab-journals" data-bs-toggle="tab" data-bs-target="#pane-journals" type="button" role="tab"><i class="bi bi-journals me-2"></i>Journals</button>
                    </li>
                    <li class="nav-item" id="nav-transactions" style="display:none;">
                        <button class="nav-link fw-bold pb-3" id="tab-transactions" data-bs-toggle="tab" data-bs-target="#pane-transactions" type="button" role="tab" style="color: #0f766e;"><i class="bi bi-arrow-left-right me-2"></i>Transactions</button>
                    </li>
                </ul>

                <div class="tab-content p-4" id="filterTabsContent">
                    
                    <div class="tab-pane fade show active" id="pane-collections" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-6 border-end px-4">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Primary Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Type</label><input type="text" name="collections[type]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Reading</label><input type="text" name="collections[reading]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Class</label><input type="text" name="collections[class]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Status</label>
                                        <select name="collections[status]" class="form-select shadow-sm">
                                            <option value="">Any Status</option>
                                            <option value="AVAILABLE">AVAILABLE</option>
                                            <option value="BORROWED">BORROWED</option>
                                            <option value="LOST">LOST</option>
                                            <option value="DAMAGED">DAMAGED</option>
                                        </select>
                                    </div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Title</label><input type="text" name="collections[title]" class="form-control shadow-sm"></div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Subject</label><input type="text" name="collections[subject]" class="form-control shadow-sm"></div>
                                    <div class="col-md-12"><label class="form-label small fw-semibold text-secondary">Author</label><input type="text" name="collections[author]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Publisher</label><input type="text" name="collections[publisher]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Issued Date</label><input type="date" name="collections[issued_date]" class="form-control shadow-sm"></div>
                                </div>
                            </div>
                            <div class="col-lg-6 px-4">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Inventory and Files</h6>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Accession No.</label><input type="text" name="collections[accession_no]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Volume</label><input type="text" name="collections[volume]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">ISBN</label><input type="text" name="collections[isbn]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Series</label><input type="text" name="collections[series]" class="form-control shadow-sm"></div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Location</label><input type="text" name="collections[location]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Date Acquired</label><input type="date" name="collections[date_acquired]" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Date Received</label><input type="date" name="collections[date_received]" class="form-control shadow-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pane-journals" role="tabpanel">
                        <div class="row g-4 px-3">
                            <div class="col-md-8"><label class="form-label small fw-semibold text-secondary">Subject / Title</label><input type="text" name="journals[subject_title]" class="form-control shadow-sm"></div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Status</label>
                                <select name="journals[status]" class="form-select shadow-sm">
                                    <option value="">Any Status</option>
                                    <option value="AVAILABLE">AVAILABLE</option>
                                    <option value="BORROWED">BORROWED</option>
                                    <option value="LOST">LOST</option>
                                    <option value="DAMAGED">DAMAGED</option>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Author</label><input type="text" name="journals[author]" class="form-control shadow-sm"></div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Date</label><input type="date" name="journals[date]" class="form-control shadow-sm"></div>
                            <div class="col-md-12"><label class="form-label small fw-semibold text-secondary">Source</label><input type="text" name="journals[source]" class="form-control shadow-sm"></div>
                            <div class="col-md-4"><label class="form-label small fw-semibold text-secondary">Session</label><input type="text" name="journals[session]" class="form-control shadow-sm"></div>
                            <div class="col-md-4"><label class="form-label small fw-semibold text-secondary">Volume</label><input type="text" name="journals[volume]" class="form-control shadow-sm"></div>
                            <div class="col-md-4"><label class="form-label small fw-semibold text-secondary">Page</label><input type="text" name="journals[page]" class="form-control shadow-sm"></div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pane-transactions" role="tabpanel">
                        <div class="row g-4 px-3">
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Trans Id</label><input type="text" name="transactions[trans_id]" class="form-control shadow-sm" placeholder="e.g. TRX-0001"></div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Borrower</label><input type="text" name="transactions[borrower]" class="form-control shadow-sm" placeholder="Name or ID"></div>
                            <div class="col-md-12"><label class="form-label small fw-semibold text-secondary">Item Details</label><input type="text" name="transactions[item_details]" class="form-control shadow-sm" placeholder="Title or Call No."></div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Requested On</label><input type="date" name="transactions[requested_on]" class="form-control shadow-sm"></div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Due Date</label><input type="date" name="transactions[due_date]" class="form-control shadow-sm"></div>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="modal-footer bg-white border-top p-3">
                <button type="button" class="btn btn-light border shadow-sm px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark shadow-sm fw-bold px-4 btn-loading">
                    <i class="bi bi-gear-fill me-2"></i> Process Report Data
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    // Initialize DataTables for all report tables
    $('.report-table').DataTable({
        "autoWidth": false,
        "pageLength": 10,
        "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" + 
               "<'row'<'col-sm-12'tr>>" +
               "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "language": { "emptyTable": "No records match your filter criteria." }
    });

    // ===============================================================
    // DYNAMIC MEGA MODAL LOGIC
    // ===============================================================
    $('#btnOpenFilter').on('click', function() {
        
        let selected = [];
        $('.filter-checkbox:checked').each(function() {
            selected.push($(this).val());
        });

        // Validation: Ensure they picked at least one
        if(selected.length === 0) {
            alert("Please select at least one category to filter.");
            return;
        }

        // Hide all tabs first
        $('.nav-item').hide();
        $('.tab-pane').removeClass('show active');
        $('.nav-link').removeClass('active');

        // Show only the selected tabs, and make the first one active automatically
        let first = true;
        selected.forEach(function(val) {
            $('#nav-' + val).show();
            if(first) {
                $('#tab-' + val).addClass('active');
                $('#pane-' + val).addClass('show active');
                first = false;
            }
        });

        // Open the configured modal
        $('#modalAdvancedFilter').modal('show');
    });

    // Global Button Loader
    $('form').on('submit', function() {
        let btn = $(this).find('button[type="submit"]');
        if (btn.prop('disabled')) { return false; }
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...');
        btn.prop('disabled', true);
    });
});
</script>
<?= $this->endSection() ?>