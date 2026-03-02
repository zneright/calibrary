<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="py-4 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #1a2942 0%, #0f172a 100%);">
    <div class="container d-flex align-items-center">
        <div class="bg-white bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
            <i class="bi bi-journal-bookmark fs-3"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1">My Books & Requests</h3>
            <p class="mb-0 text-white-50 small">Track your active borrows, pickup status, and history.</p>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-0 border-bottom">
            <ul class="nav nav-tabs nav-justified" id="myBooksTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold py-3 border-0 text-dark" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                        <i class="bi bi-book-half me-2"></i>Active (<?= count($active_borrows) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold py-3 border-0 text-muted" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="bi bi-hourglass-split me-2"></i>Pending (<?= count($pending_requests) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold py-3 border-0 text-muted" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                        <i class="bi bi-clock-history me-2"></i>History
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
                                    <th>DUE DATE</th>
                                    <th>STATUS</th>
                                    <th class="text-center pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($active_borrows)): foreach($active_borrows as $row): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold text-dark"><?= esc($row['collection_title']) ?></h6>
                                        <small class="text-muted">Ref ID: #<?= $row['id'] ?></small>
                                    </td>
                                    <td class="fw-semibold">
                                        <?= $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '<span class="text-muted">Not Set</span>' ?>
                                    </td>
                                    <td>
                                        <?php if($row['status'] == 'Approved'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                <i class="bi bi-box-seam me-1"></i>Ready for Pickup
                                            </span>
                                        <?php elseif(strtotime($row['due_date']) < time() && $row['due_date'] != null): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">
                                                <i class="bi bi-person-check me-1"></i>On Hand
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <?php if($row['status'] == 'Borrowed'): ?>
                                            <button class="btn btn-sm btn-outline-primary" onclick="openRenewModal('<?= esc(addslashes($row['collection_title'])) ?>', '<?= $row['id'] ?>')">
                                                Renew
                                            </button>
                                        <?php else: ?>
                                            <small class="text-muted italic">Visit Library</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">No active items.</td></tr>
                                <?php endif; ?>
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
                                    <th>STATUS</th>
                                    <th class="text-center pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($pending_requests)): foreach($pending_requests as $row): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold"><?= esc($row['collection_title']) ?></h6>
                                    </td>
                                    <td class="text-muted"><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td><span class="badge bg-warning text-dark">Waiting for Approval</span></td>
                                    <td class="text-center pe-4">
                                        <form action="/borrower/request/cancel" method="POST" onsubmit="return confirm('Cancel this request?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm text-danger border-0">Cancel Request</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">No pending requests.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="history" role="tabpanel">
                     </div>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>