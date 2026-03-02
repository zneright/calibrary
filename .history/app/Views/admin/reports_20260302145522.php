<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php 
    $currentGet = service('request')->getGet();
    $currentGet['export'] = 'true'; 
    $exportQuery = '?' . http_build_query($currentGet);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary mb-0">
            <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Advanced Reports & Filtering
        </h4>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/reports/exportPdf') . $exportQuery ?>" class="btn btn-danger btn-sm shadow-sm fw-semibold" title="Export Filtered Results to PDF" target="_blank">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF Report
            </a>
        </div>
    </div>

    <div class="card bg-white border-0 shadow-sm mb-4" style="border-top: 4px solid #1e3a8a;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-ui-checks-grid me-2 text-primary"></i>Select Report Categories</h6>
            <p class="text-muted small mb-4">Check the modules you want to include in your generated report, then configure their specific filters.</p>
            
            <div class="d-flex flex-wrap align-items-center gap-4">
                <div class="form-check custom-checkbox">
                    <input class="form-check-input filter-checkbox shadow-sm" type="checkbox" id="catCollections" value="collections" <?= in_array('collections', $selectedCats) ? 'checked' : '' ?> style="transform: scale(1.2); margin-right: 8px;">
                    <label class="form-check-label fw-bold text-secondary" for="catCollections">Collections Inventory</label>
                </div>
                <div class="form-check custom-checkbox">
                    <input class="form-check-input filter-checkbox shadow-sm" type="checkbox" id="catJournals" value="journals" <?= in_array('journals', $selectedCats) ? 'checked' : '' ?> style="transform: scale(1.2); margin-right: 8px;">
                    <label class="form-check-label fw-bold text-secondary" for="catJournals">Journals Directory</label>
                </div>
                <div class="form-check custom-checkbox">
                    <input class="form-check-input filter-checkbox shadow-sm" type="checkbox" id="catTransactions" value="transactions" <?= in_array('transactions', $selectedCats) ? 'checked' : '' ?> style="transform: scale(1.2); margin-right: 8px;">
                    <label class="form-check-label fw-bold text-secondary" for="catTransactions">Library Transactions</label>
                </div>
                
                <button type="button" class="btn btn-dark px-4 fw-bold shadow ms-auto" id="btnOpenFilter">
                    Configure Filters <i class="bi bi-sliders ms-1"></i>
                </button>
            </div>
        </div>
    </div>

    <?php if ($showReports): ?>
        <h5 class="fw-bold text-dark mb-3 mt-5 border-bottom pb-2">Generated Report Results</h5>

        <?php if ($showCollections): ?>
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header text-white py-3 d-flex justify-content-between align-items-center" style="background-color: #1e3a8a;">
                <h6 class="mb-0 fw-bold"><i class="bi bi-book me-2"></i>Collections Report</h6>
                <span class="badge bg-light text-dark fw-semibold"><?= count($collections) ?> Records</span>
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
                            <?php if(!empty($collections)): foreach($collections as $row): ?>
                                <tr>
                                    <td class="fw-semibold text-secondary"><?= esc($row['accession_no'] ?: 'N/A') ?></td>
                                    <td class="text-start fw-bold text-dark"><?= esc($row['title']) ?></td>
                                    <td><?= esc($row['author']) ?></td>
                                    <td><?= esc($row['class'] ?: 'N/A') ?></td>
                                    <td><?= $row['issued_date'] ? date('Y-m-d', strtotime($row['issued_date'])) : '---' ?></td>
                                    <td>
                                        <?php 
                                            $s = $row['status'];
                                            $b = 'bg-secondary';
                                            if($s == 'AVAILABLE') $b = 'bg-success text-white';
                                            if($s == 'BORROWED') $b = 'bg-warning text-dark';
                                            if($s == 'LOST' || $s == 'DAMAGED') $b = 'bg-danger text-white';
                                        ?>
                                        <span class="badge <?= $b ?>"><?= esc($s) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-muted p-4">No collection records match your filter.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($showJournals): ?>
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header bg-info text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-journals me-2"></i>Journals Report</h6>
                <span class="badge bg-light text-dark fw-semibold"><?= count($journals) ?> Records</span>
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
                            <?php if(!empty($journals)): foreach($journals as $row): ?>
                                <tr>
                                    <td class="text-start fw-bold text-dark"><?= esc($row['subject']) ?></td>
                                    <td><?= esc($row['author']) ?></td>
                                    <td><?= esc($row['source']) ?></td>
                                    <td>Vol <?= esc($row['volume'] ?: '-') ?>, Pg <?= esc($row['page'] ?: '-') ?></td>
                                    <td><?= $row['date'] ? date('Y-m-d', strtotime($row['date'])) : '---' ?></td>
                                    <td>
                                        <?php 
                                            $s = $row['status'] ?? 'AVAILABLE';
                                            $b = 'bg-secondary';
                                            if($s == 'AVAILABLE') $b = 'bg-success text-white';
                                            if($s == 'BORROWED') $b = 'bg-warning text-dark';
                                            if($s == 'LOST' || $s == 'DAMAGED') $b = 'bg-danger text-white';
                                        ?>
                                        <span class="badge <?= $b ?>"><?= esc($s) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-muted p-4">No journal records match your filter.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($showTransactions): ?>
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header text-white py-3 d-flex justify-content-between align-items-center" style="background-color: #0f766e;">
                <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Transactions Report</h6>
                <span class="badge bg-light text-dark fw-semibold"><?= count($transactions) ?> Records</span>
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
                            <?php if(!empty($transactions)): foreach($transactions as $row): ?>
                                <tr>
                                    <td class="fw-bold text-secondary">TRX-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td class="text-start fw-semibold"><?= esc($row['user_name']) ?> <br><small class="text-muted">ID: <?= esc($row['user_id_num']) ?></small></td>
                                    <td class="text-start"><?= esc($row['collection_title']) ?></td>
                                    <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td class="fw-bold text-danger"><?= $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '---' ?></td>
                                    <td>
                                        <?php 
                                            $status = $row['status'];
                                            $badge = 'bg-secondary text-white';
                                            if($status == 'Pending' || $status == 'Renewing') $badge = 'bg-warning text-dark';
                                            if($status == 'Approved') $badge = 'bg-primary text-white';
                                            if($status == 'Borrowed') $badge = 'bg-info text-dark';
                                            if($status == 'Returned') $badge = 'bg-success text-white';
                                            if(in_array($status, ['Rejected', 'Lost', 'Damaged'])) $badge = 'bg-danger text-white';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= esc($status) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-muted p-4">No transaction records match your filter.</td></tr>
                            <?php endif; ?>
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
        <form action="<?= base_url('admin/reports') ?>" method="GET" class="modal-content border-0 shadow-lg" id="filterForm">
            <div class="modal-header bg-dark text-white border-bottom-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-funnel-fill me-2"></i>Configure Report Filters</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0 bg-light">
                <ul class="nav nav-tabs nav-fill bg-white pt-3 px-3 border-bottom shadow-sm" id="filterTabs" role="tablist">
                    <li class="nav-item" id="nav-collections" style="display:none;">
                        <button class="nav-link fw-bold pb-3" id="tab-collections" data-bs-toggle="tab" data-bs-target="#pane-collections" type="button" role="tab" style="color: #1e3a8a;"><i class="bi bi-book me-2"></i>Collections</button>
                    </li>
                    <li class="nav-item" id="nav-journals" style="display:none;">
                        <button class="nav-link fw-bold pb-3 text-info" id="tab-journals" data-bs-toggle="tab" data-bs-target="#pane-journals" type="button" role="tab"><i class="bi bi-journals me-2"></i>Journals</button>
                    </li>
                    <li class="nav-item" id="nav-transactions" style="display:none;">
                        <button class="nav-link fw-bold pb-3" id="tab-transactions" data-bs-toggle="tab" data-bs-target="#pane-transactions" type="button" role="tab" style="color: #0f766e;"><i class="bi bi-arrow-left-right me-2"></i>Transactions</button>
                    </li>
                </ul>

                <div class="tab-content p-4" id="filterTabsContent">
                    
                    <div class="tab-pane fade" id="pane-collections" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-6 border-end px-4">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Primary Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Type</label><input type="text" name="collections[type]" value="<?= esc($cFilter['type'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Reading</label><input type="text" name="collections[reading]" value="<?= esc($cFilter['reading'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Class</label><input type="text" name="collections[class]" value="<?= esc($cFilter['class'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Status</label>
                                        <select name="collections[status]" class="form-select shadow-sm">
                                            <option value="">Any Status</option>
                                            <option value="AVAILABLE" <?= ($cFilter['status'] ?? '') == 'AVAILABLE' ? 'selected' : '' ?>>AVAILABLE</option>
                                            <option value="BORROWED" <?= ($cFilter['status'] ?? '') == 'BORROWED' ? 'selected' : '' ?>>BORROWED</option>
                                            <option value="LOST" <?= ($cFilter['status'] ?? '') == 'LOST' ? 'selected' : '' ?>>LOST</option>
                                            <option value="DAMAGED" <?= ($cFilter['status'] ?? '') == 'DAMAGED' ? 'selected' : '' ?>>DAMAGED</option>
                                        </select>
                                    </div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Title</label><input type="text" name="collections[title]" value="<?= esc($cFilter['title'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Subject</label><input type="text" name="collections[subject]" value="<?= esc($cFilter['subject'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-12"><label class="form-label small fw-semibold text-secondary">Author</label><input type="text" name="collections[author]" value="<?= esc($cFilter['author'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Publisher</label><input type="text" name="collections[publisher]" value="<?= esc($cFilter['publisher'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Issued Date</label><input type="date" name="collections[issued_date]" value="<?= esc($cFilter['issued_date'] ?? '') ?>" class="form-control shadow-sm"></div>
                                </div>
                            </div>
                            <div class="col-lg-6 px-4">
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Inventory and Files</h6>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Accession No.</label><input type="text" name="collections[accession_no]" value="<?= esc($cFilter['accession_no'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Volume</label><input type="text" name="collections[volume]" value="<?= esc($cFilter['volume'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">ISBN</label><input type="text" name="collections[isbn]" value="<?= esc($cFilter['isbn'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Series</label><input type="text" name="collections[series]" value="<?= esc($cFilter['series'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-12"><label class="form-label small fw-semibold text-secondary">Location</label><input type="text" name="collections[location]" value="<?= esc($cFilter['location'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Date Acquired</label><input type="date" name="collections[date_acquired]" value="<?= esc($cFilter['date_acquired'] ?? '') ?>" class="form-control shadow-sm"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Date Received</label><input type="date" name="collections[date_received]" value="<?= esc($cFilter['date_received'] ?? '') ?>" class="form-control shadow-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pane-journals" role="tabpanel">
                        <div class="row g-4 px-3">
                            <div class="col-md-8"><label class="form-label small fw-semibold text-secondary">Subject / Title</label><input type="text" name="journals[subject_title]" value="<?= esc($jFilter['subject_title'] ?? '') ?>" class="form-control shadow-sm"></div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Status</label>
                                <select name="journals[status]" class="form-select shadow-sm">
                                    <option value="">Any Status</option>
                                    <option value="AVAILABLE" <?= ($jFilter['status'] ?? '') == 'AVAILABLE' ? 'selected' : '' ?>>AVAILABLE</option>
                                    <option value="BORROWED" <?= ($jFilter['status'] ?? '') == 'BORROWED' ? 'selected' : '' ?>>BORROWED</option>
                                    <option value="LOST" <?= ($jFilter['status'] ?? '') == 'LOST' ? 'selected' : '' ?>>LOST</option>
                                    <option value="DAMAGED" <?= ($jFilter['status'] ?? '') == 'DAMAGED' ? 'selected' : '' ?>>DAMAGED</option>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Author</label><input type="text" name="journals[author]" value="<?= esc($jFilter['author'] ?? '') ?>" class="form-control shadow-sm"></div>
                            <div class="col-md-6"><label class="form-label small fw-semibold text-secondary">Date</label><input type="date" name="journals[date]" value="<?= esc($jFilter['date'] ?? '') ?>" class="form-control shadow-sm"></div>
                            <div class="col-md-12"><label class="form-label small fw-semibold text-secondary">Source</label><input type="text" name="journals[source]" value="<?= esc($jFilter['source'] ?? '') ?>" class="form-control shadow-sm"></div>
                            <div class="col-md-4"><label class="form-label small fw-semibold text-secondary">Session</label><input type="text" name="journals[session]" value="<?= esc($jFilter['session'] ?? '') ?>" class="form-control shadow-sm"></div>
                            <div class="col-md-4"><label class="form-label small fw-semibold text-secondary">Volume</label><input type="text" name="journals[volume]" value="<?= esc($jFilter['volume'] ?? '') ?>" class="form-control shadow-sm"></div>
                            <div class="col-md-4"><label class="form-label small fw-semibold text-secondary">Page</label><input type="text" name="journals[page]" value="<?= esc($jFilter['page'] ?? '') ?>" class="form-control shadow-sm"></div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pane-transactions" role="tabpanel">
                        <div class="row g-4 px-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Trans Id</label>
                                <input type="text" name="transactions[trans_id]" value="<?= esc($tFilter['trans_id'] ?? '') ?>" class="form-control shadow-sm" placeholder="e.g. TRX-0001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Borrower</label>
                                <input type="text" name="transactions[borrower]" value="<?= esc($tFilter['borrower'] ?? '') ?>" class="form-control shadow-sm" placeholder="Name or ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Status</label>
                                <select name="transactions[status]" class="form-select shadow-sm">
                                    <option value="">Any Status</option>
                                    <option value="Pending" <?= ($tFilter['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending Request</option>
                                    <option value="Approved" <?= ($tFilter['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved (Ready for Pickup)</option>
                                    <option value="Borrowed" <?= ($tFilter['status'] ?? '') == 'Borrowed' ? 'selected' : '' ?>>Borrowed (On Hand)</option>
                                    <option value="Renewing" <?= ($tFilter['status'] ?? '') == 'Renewing' ? 'selected' : '' ?>>Pending Renewal</option>
                                    <option value="Returned" <?= ($tFilter['status'] ?? '') == 'Returned' ? 'selected' : '' ?>>Returned</option>
                                    <option value="Rejected" <?= ($tFilter['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                    <option value="Lost" <?= ($tFilter['status'] ?? '') == 'Lost' ? 'selected' : '' ?>>Lost</option>
                                    <option value="Damaged" <?= ($tFilter['status'] ?? '') == 'Damaged' ? 'selected' : '' ?>>Damaged</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Item Details</label>
                                <input type="text" name="transactions[item_details]" value="<?= esc($tFilter['item_details'] ?? '') ?>" class="form-control shadow-sm" placeholder="Title or Call No.">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Requested On</label>
                                <input type="date" name="transactions[requested_on]" value="<?= esc($tFilter['requested_on'] ?? '') ?>" class="form-control shadow-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Due Date</label>
                                <input type="date" name="transactions[due_date]" value="<?= esc($tFilter['due_date'] ?? '') ?>" class="form-control shadow-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Date Returned</label>
                                <input type="date" name="transactions[date_returned]" value="<?= esc($tFilter['date_returned'] ?? '') ?>" class="form-control shadow-sm">
                            </div>
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
    // intialize db for report table
    $('.report-table').DataTable({
        "autoWidth": false,
        "pageLength": 10,
        "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" + 
               "<'row'<'col-sm-12'tr>>" +
               "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "language": { "emptyTable": "No records match your filter criteria." }
    });

    // control which tab is visible
    $('#btnOpenFilter').on('click', function() {
        let selected = [];
        $('.filter-checkbox:checked').each(function() {
            selected.push($(this).val());
        });

        // validation, ensuring na nakapali atleast one
        if(selected.length === 0) {
            alert("Please select at least one category to filter.");
            return;
        }

        // show only selected tabs
        $('.nav-item').hide();
        $('.tab-pane').removeClass('show active');
        $('.nav-link').removeClass('active');

        let first = true;
        selected.forEach(function(val) {
            $('#nav-' + val).show();
            if(first) {
                $('#tab-' + val).addClass('active');
                $('#pane-' + val).addClass('show active');
                first = false;
            }
        });

        $('#modalAdvancedFilter').modal('show');
    });

    //c
    $('#filterForm').on('submit', function() {
        let btn = $(this).find('button[type="submit"]');
        if (btn.prop('disabled')) { return false; }
        
        btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...');
        btn.prop('disabled', true);

        // Add hidden inputs for the checked categories so the controller knows what to generate
        $('.filter-checkbox:checked').each(function() {
            $('<input>').attr({
                type: 'hidden',
                name: 'categories[]',
                value: $(this).val()
            }).appendTo('#filterForm');
        });
    });
});
</script>
<?= $this->endSection() ?>