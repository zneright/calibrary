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
                                        <?= $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '<span class="text-muted">---</span>' ?>
                                    </td>
                                <td>
    <?php if($row['status'] == 'Approved'): ?>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Ready for Pickup</span>
    <?php elseif($row['status'] == 'Renewing'): ?>
        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
            <i class="bi bi-clock-history me-1"></i>Extension Pending
        </span>
    <?php elseif(strtotime($row['due_date'] ?? '') < time() && $row['due_date'] != null): ?>
        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Overdue</span>
    <?php else: ?>
        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">On Hand</span>
    <?php endif; ?>
</td>

<td class="text-center pe-4">
    <?php if($row['status'] == 'Borrowed'): ?>
        <button class="btn btn-sm btn-outline-primary fw-bold px-3 shadow-sm" onclick="openRenewModal('<?= $row['id'] ?>', '<?= esc(addslashes($row['collection_title'])) ?>')">
            Renew
        </button>
    <?php elseif($row['status'] == 'Renewing'): ?>
        <small class="text-muted italic">Waiting for Admin...</small>
    <?php endif; ?>
</td>

                                    <td class="text-center pe-4">
                                        <?php if($row['status'] == 'Borrowed'): ?>
                                            <button class="btn btn-sm btn-outline-primary fw-bold px-3 shadow-sm" onclick="openRenewModal('<?= $row['id'] ?>', '<?= esc(addslashes($row['collection_title'])) ?>')">
                                                Renew
                                            </button>
                                        <?php elseif($row['status'] == 'Renewing'): ?>
                                            <small class="text-muted">Wait for Admin</small>
                                        <?php else: ?>
                                            <small class="text-muted">---</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <?php if($row['status'] == 'Borrowed'): ?>
                                            <button class="btn btn-sm btn-outline-primary fw-bold px-3 shadow-sm" onclick="openRenewModal('<?= $row['id'] ?>', '<?= esc(addslashes($row['collection_title'])) ?>')">
                                                Renew
                                            </button>
                                        <?php elseif($row['status'] == 'Renewing'): ?>
                                            <small class="text-muted italic">Waiting for Admin...</small>
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
                                        <h6 class="mb-0 fw-semibold text-dark"><?= esc($row['collection_title']) ?></h6>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td><span class="badge bg-warning text-dark border border-warning border-opacity-50">Pending Approval</span></td>
                                    <td class="text-center pe-4">
                                        <form action="/borrower/request/cancel" method="POST" onsubmit="return confirm('Cancel this request?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm text-danger text-decoration-none fw-bold border-0">Cancel Request</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">No pending requests found.</td></tr>
                                <?php endif; ?>
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
                                    <th>DATE REQUESTED</th>
                                    <th>DATE RETURNED</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($history)): foreach($history as $row): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <h6 class="mb-0 fw-semibold text-secondary"><?= esc($row['collection_title']) ?></h6>
                                        <small class="text-muted">Ref ID: #<?= $row['id'] ?></small>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td class="text-muted small">
                                        <?= ($row['status'] == 'Returned') ? date('M d, Y', strtotime($row['date_returned'])) : '---' ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $row['status'];
                                            $badge = 'bg-secondary'; // Default for Cancelled

                                            if($status == 'Returned') {
                                                $badge = 'bg-success';
                                                $label = 'Returned';
                                            } elseif($status == 'Rejected') {
                                                $badge = 'bg-danger';
                                                $label = 'Request Denied';
                                            } elseif($status == 'Extension Rejected') {
                                                $badge = 'bg-dark';
                                                $label = 'Extension Denied';
                                            } else {
                                                $label = esc($status);
                                            }
                                        ?>
                                        <span class="badge <?= $badge ?> bg-opacity-10 <?= str_replace('bg-', 'text-', $badge) ?> border border-opacity-25 px-2 py-1">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Your history is empty.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="renewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('borrower/renew/submit') ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="renew_id">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Request Renewal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-calendar2-range text-primary" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-1 text-muted">You are requesting to extend the duration for:</p>
                <h6 class="fw-bold text-dark mb-4" id="renew_title"></h6>
                <div class="alert alert-info small text-start border-0 shadow-sm">
                    <i class="bi bi-info-circle-fill me-2"></i>Admin will review and set a new return date for you.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Send Request</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRenewModal(id, title) {
    document.getElementById('renew_id').value = id;
    document.getElementById('renew_title').innerText = title;
    var myModal = new bootstrap.Modal(document.getElementById('renewModal'));
    myModal.show();
}
</script>

<?= $this->endSection() ?>