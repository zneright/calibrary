<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="py-4 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #1a2942 0%, #0f172a 100%);">
    <div class="container d-flex align-items-center">
        <div class="bg-white bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
            <i class="bi bi-journal-bookmark fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1">My Books & Requests</h3>
            <p class="mb-0 text-white-50 small">Track your active borrows, pending requests, and reading history.</p>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-0 border-bottom">
            <ul class="nav nav-tabs nav-justified" id="myBooksTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold py-3 border-0 text-dark" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" style="border-bottom: 3px solid #1e3a8a !important; color: #1e3a8a !important; background: transparent;">
                        <i class="bi bi-book-half me-2"></i>Active Borrows (2)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold py-3 border-0 text-muted" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="bi bi-hourglass-split me-2"></i>Pending Requests (1)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold py-3 border-0 text-muted" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                        <i class="bi bi-clock-history me-2"></i>Borrow History
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="myBooksTabsContent">
                
                <div class="tab-pane fade show active" id="active" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">RESOURCE TITLE</th>
                                    <th>DATE BORROWED</th>
                                    <th>DUE DATE</th>
                                    <th>STATUS</th>
                                    <th class="text-center pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold" style="color: #1a2942;">GRANTING SALARY INCREASE TO CAREER EXEC. SERVICE OFFICERS</h6>
                                        <small class="text-muted">Call No: QA76.73 • Book</small>
                                    </td>
                                    <td class="text-muted">Feb 18, 2026</td>
                                    <td class="fw-semibold">Feb 21, 2026</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-check-circle me-1"></i>On Hand</span></td>
                                    <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-outline-secondary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#renewModal" onclick="openRenewModal('GRANTING SALARY INCREASE TO CAREER EXEC. SERVICE OFFICERS', 'B-001', 'Feb 21, 2026')">
                                        <i class="bi bi-arrow-repeat me-1"></i> Renew
                                    </button>                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold" style="color: #7f1d1d;">CA MEMBERS -- HOUSE OF REP. -- 8TH CONGRESS</h6>
                                        <small class="text-muted">Call No: JRN-8TH-001 • Journal</small>
                                    </td>
                                    <td class="text-muted">Feb 10, 2026</td>
                                    <td class="text-danger fw-bold">Feb 15, 2026</td>
                                    <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Overdue</span></td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-sm btn-outline-danger fw-semibold shadow-sm" disabled><i class="bi bi-slash-circle me-1"></i> Cannot Renew</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">RESOURCE TITLE</th>
                                    <th>DATE REQUESTED</th>
                                    <th>QUEUE STATUS</th>
                                    <th class="text-center pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold" style="color: #1a2942;">Parliamentary Procedures and Practices</h6>
                                        <small class="text-muted">Call No: QA76.73 • Book</small>
                                    </td>
                                    <td class="text-muted">Feb 19, 2026</td>
                                    <td><span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50 px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Pending Approval</span></td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-sm btn-outline-danger fw-semibold shadow-sm"><i class="bi bi-x-circle me-1"></i> Cancel</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="history" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">RESOURCE TITLE</th>
                                    <th>DATE BORROWED</th>
                                    <th>DATE RETURNED</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold text-muted">Presidential Decree No. 1081</h6>
                                        <small class="text-muted">Call No: PD-1081 • Document</small>
                                    </td>
                                    <td class="text-muted">Jan 10, 2026</td>
                                    <td class="text-muted">Jan 14, 2026</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1"><i class="bi bi-check2-all me-1"></i>Returned & Cleared</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold text-muted">Rules of the Senate</h6>
                                        <small class="text-muted">Call No: RS-2022 • Book</small>
                                    </td>
                                    <td class="text-muted">Nov 05, 2025</td>
                                    <td class="text-muted">Nov 12, 2025</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1"><i class="bi bi-check2-all me-1"></i>Returned & Cleared</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Simple script to change tab text color when active
    document.querySelectorAll('#myBooksTabs .nav-link').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            // Reset all tabs to muted
            document.querySelectorAll('#myBooksTabs .nav-link').forEach(t => {
                t.classList.remove('text-dark');
                t.classList.add('text-muted');
                t.style.borderBottom = 'none';
                t.style.color = ''; 
            });
            // Style the active tab to match the Navy theme
            event.target.classList.remove('text-muted');
            event.target.classList.add('text-dark');
            event.target.style.borderBottom = '3px solid #1e3a8a';
            event.target.style.color = '#1e3a8a';
        });
    });
</script>
<div class="modal fade" id="renewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header text-white" style="background-color: #1a2942;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>Request Renewal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/borrower/renew/submit" method="POST">
                <?= csrf_field() ?>
                
                <div class="modal-body p-4">
                    <div class="alert alert-warning bg-opacity-10 border-warning border-opacity-50 small mb-4 text-dark">
                        <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                        Renewals are subject to admin approval and availability. You cannot renew an item if another employee has already requested it.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Resource Title</label>
                        <input type="text" class="form-control bg-light text-muted fw-semibold" id="renewBookTitle" readonly>
                        <input type="hidden" name="book_id" id="renewBookId">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Current Due Date</label>
                            <input type="text" class="form-control bg-light text-danger fw-bold" id="currentDueDate" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Requested Extension <span class="text-danger">*</span></label>
                            <input type="date" name="new_due_date" class="form-control shadow-sm" required min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Reason for Extension <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control shadow-sm" rows="2" placeholder="e.g., Still cross-referencing this document with recent cases..." required></textarea>
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
    function openRenewModal(title, id, currentDue) {
        document.getElementById('renewBookTitle').value = title;
        document.getElementById('renewBookId').value = id;
        document.getElementById('currentDueDate').value = currentDue;
    }
</script>
<?= $this->endSection() ?>