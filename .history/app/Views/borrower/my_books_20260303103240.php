<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<style>
    .table-hover tbody tr:hover { background-color: #f8fafc; cursor: pointer; }
    .bg-danger.animate__infinite {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>

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
                        <i class="bi bi-book-half me-2"></i>Active (<?= count($active_borrows ?? []) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold py-3 border-0 text-muted" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="bi bi-hourglass-split me-2"></i>Pending (<?= count($pending_requests ?? []) ?>)
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
                                    <th class="ps-4 text-uppercase">Resource</th>
                                    <th class="text-uppercase">Due Date</th>
                                    <th class="text-uppercase">Status</th>
                                    <th class="text-center pe-4 text-uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($active_borrows)): foreach($active_borrows as $row): ?>
                                <?php $itemJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
                                <tr onclick="openDetailsModal(<?= $itemJson ?>)">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($row['cover_photo'])): ?>
                                                <img src="<?= base_url('uploads/covers/'.$row['cover_photo']) ?>" class="rounded shadow-sm me-3" style="width: 45px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 text-primary shadow-sm" style="width: 45px; height: 60px;">
                                                    <i class="bi <?= (isset($row['type']) && $row['type'] == 'Journal') ? 'bi-journal-bookmark' : 'bi-book' ?> fs-4"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-dark text-truncate" style="max-width: 300px;"><?= esc($row['collection_title']) ?></h6>
                                                <small class="text-muted">Ref ID: #<?= $row['id'] ?></small>
                                            </div>
                                        </div>
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
                                        <?php elseif($row['due_date'] != null && date('Y-m-d', strtotime($row['due_date'])) < date('Y-m-d')): ?>
                                            <span class="badge bg-danger text-white px-2 py-1 animate__animated animate__pulse animate__infinite">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">On Hand</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <?php if($row['status'] == 'Borrowed'): ?>
                                            <button class="btn btn-sm btn-outline-primary fw-bold px-3 shadow-sm" onclick="event.stopPropagation(); openRenewModal('<?= $row['id'] ?>', '<?= esc(addslashes($row['collection_title'])) ?>')">
                                                Renew
                                            </button>
                                        <?php elseif($row['status'] == 'Renewing'): ?>
                                            <small class="text-muted italic">Waiting for Admin...</small>
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
                                    <th class="ps-4 text-uppercase">Resource</th>
                                    <th class="text-uppercase">Date Requested</th>
                                    <th class="text-uppercase">Status</th>
                                    <th class="text-center pe-4 text-uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($pending_requests)): foreach($pending_requests as $row): ?>
                                <?php $itemJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
                                <tr onclick="openDetailsModal(<?= $itemJson ?>)">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($row['cover_photo'])): ?>
                                                <img src="<?= base_url('uploads/covers/'.$row['cover_photo']) ?>" class="rounded shadow-sm me-3" style="width: 45px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 text-primary shadow-sm" style="width: 45px; height: 60px;">
                                                    <i class="bi <?= (isset($row['type']) && $row['type'] == 'Journal') ? 'bi-journal-bookmark' : 'bi-book' ?> fs-4"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-dark text-truncate" style="max-width: 300px;"><?= esc($row['collection_title']) ?></h6>
                                                <small class="text-muted">Ref ID: #<?= $row['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td><span class="badge bg-warning text-dark border border-warning border-opacity-50">Pending Approval</span></td>
                                    <td class="text-center pe-4">
                                        <form action="/borrower/request/cancel" method="POST" onsubmit="event.stopPropagation(); return confirm('Cancel this request?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm text-danger text-decoration-none fw-bold border-0" onclick="event.stopPropagation();">Cancel Request</button>
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
                                    <th class="ps-4 text-uppercase">Resource</th>
                                    <th class="text-uppercase">Date Requested</th>
                                    <th class="text-uppercase">Date Returned</th>
                                    <th class="text-uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($history)): foreach($history as $row): ?>
                                <?php $itemJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
                                <tr onclick="openDetailsModal(<?= $itemJson ?>)">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($row['cover_photo'])): ?>
                                                <img src="<?= base_url('uploads/covers/'.$row['cover_photo']) ?>" class="rounded shadow-sm me-3" style="width: 45px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 text-secondary shadow-sm" style="width: 45px; height: 60px;">
                                                    <i class="bi <?= (isset($row['type']) && $row['type'] == 'Journal') ? 'bi-journal-bookmark' : 'bi-book' ?> fs-4"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-secondary text-truncate" style="max-width: 300px;"><?= esc($row['collection_title']) ?></h6>
                                                <small class="text-muted">Ref ID: #<?= $row['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td class="text-muted small">
                                        <?= ($row['status'] == 'Returned') ? date('M d, Y', strtotime($row['date_returned'])) : '---' ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $row['status'];
                                            $badge = 'bg-secondary';
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

<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold text-primary">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4 border-end" id="modalCoverContainer" style="min-height: 300px;">
                        </div>
                    
                    <div class="col-md-7 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2 gap-2">
                                <span id="modalTypeBadge" class="badge bg-light border text-dark px-3 py-2"></span>
                                <span id="modalStatusBadge" class="badge px-3 py-2 shadow-sm"></span>
                            </div>
                            
                            <h4 id="modalTitle" class="fw-bold text-dark mb-1"></h4>
                            <p id="modalAuthor" class="text-primary fw-semibold mb-3"></p>
                            
                            <hr>
                            
                            <div class="row mt-3 text-muted small">
                                <div class="col-6 mb-3">
                                    <span class="d-block text-uppercase" style="font-size: 0.7rem;">Transaction ID</span>
                                    <span id="modalTransId" class="fw-bold text-dark font-monospace"></span>
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="d-block text-uppercase" style="font-size: 0.7rem;">Class / Vol</span>
                                    <span id="modalClass" class="fw-bold text-dark"></span>
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="d-block text-uppercase text-primary" style="font-size: 0.7rem;">Date Requested</span>
                                    <span id="modalDateRequested" class="text-dark fw-bold"></span>
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="d-block text-uppercase text-danger" id="modalDateLabel" style="font-size: 0.7rem;">Due Date</span>
                                    <span id="modalDueDate" class="text-danger fw-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0" id="modalFooterActions">
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openDetailsModal(item) {
        
        // 1. Populate Text
        document.getElementById('modalTitle').innerText = item.collection_title;
        document.getElementById('modalAuthor').innerText = item.author || 'Unknown';
        document.getElementById('modalClass').innerText = item.class || 'N/A';
        document.getElementById('modalTypeBadge').innerText = item.type || 'Resource';
        document.getElementById('modalTransId').innerText = 'TRX-' + String(item.id).padStart(5, '0');
        
        // Format Request Date
        const reqDate = new Date(item.date_requested).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        document.getElementById('modalDateRequested').innerText = reqDate;
        
        // Due Date OR Return Date Logic
        let dateLabel = document.getElementById('modalDateLabel');
        let dueDateElem = document.getElementById('modalDueDate');
        
        if (item.status === 'Returned' && item.date_returned) {
            dateLabel.innerText = 'Date Returned';
            dateLabel.className = 'd-block text-uppercase text-success';
            dueDateElem.innerText = new Date(item.date_returned).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            dueDateElem.className = 'text-success fw-bold';
        } else if (item.due_date && item.status !== 'Rejected' && item.status !== 'Cancelled') {
            dateLabel.innerText = 'Due Date';
            dateLabel.className = 'd-block text-uppercase text-danger';
            const dueDateObj = new Date(item.due_date);
            const today = new Date();
            today.setHours(0,0,0,0);
            
            dueDateElem.innerText = dueDateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            
            if (dueDateObj < today) {
                dueDateElem.className = "text-danger fw-bold animate__animated animate__flash animate__infinite";
            } else {
                dueDateElem.className = "text-dark fw-bold";
            }
        } else {
            dateLabel.innerText = 'Due Date';
            dateLabel.className = 'd-block text-uppercase text-muted';
            dueDateElem.innerText = 'N/A';
            dueDateElem.className = "text-muted";
        }

        // Set Status
        let badge = document.getElementById('modalStatusBadge');
        let status = item.status;
        
        if(status === 'Approved') {
            badge.innerText = 'Ready for Pickup';
            badge.className = 'badge bg-success text-white px-3 py-2';
        } else if (status === 'Borrowed') {
            badge.innerText = 'On Hand';
            badge.className = 'badge bg-primary text-white px-3 py-2';
        } else if (status === 'Renewing') {
            badge.innerText = 'Extension Pending';
            badge.className = 'badge bg-info text-dark px-3 py-2';
        } else if (status === 'Pending') {
            badge.innerText = 'Pending Review';
            badge.className = 'badge bg-warning text-dark px-3 py-2';
        } else if (status === 'Returned') {
            badge.innerText = 'Returned';
            badge.className = 'badge bg-success text-white px-3 py-2';
        } else {
            badge.innerText = status; 
            badge.className = 'badge bg-danger text-white px-3 py-2';
        }

        // Set Cover Image
        let coverContainer = document.getElementById('modalCoverContainer');
        if (item.cover_photo) {
            let imgUrl = "<?= base_url('uploads/covers/') ?>" + item.cover_photo;
            coverContainer.innerHTML = `<img src="${imgUrl}" class="img-fluid rounded shadow" style="max-height: 350px; object-fit: contain;" alt="Cover">`;
        } else {
            let icon = (item.type === 'Journal') ? 'bi-journal-bookmark' : 'bi-book';
            coverContainer.innerHTML = `<i class="bi ${icon} text-secondary" style="font-size: 8rem;"></i>`;
        }

        //Action Buttons based on status
        let footerHtml = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>`;
        
        if (status === 'Borrowed') {
            let safeTitle = item.collection_title.replace(/'/g, "\\'");
            footerHtml = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary fw-bold" onclick="document.getElementById('itemDetailsModal').querySelector('.btn-close').click(); openRenewModal('${item.id}', '${safeTitle}');">Request Extension</button>
            `;
        } else if (status === 'Pending') {
            footerHtml = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="/borrower/request/cancel" method="POST" class="d-inline" onsubmit="return confirm('Cancel this request?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="${item.id}">
                    <button type="submit" class="btn btn-danger fw-bold">Cancel Request</button>
                </form>
            `;
        }

        document.getElementById('modalFooterActions').innerHTML = footerHtml;

        //Open Modal
        var myModal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
        myModal.show();
    }

    function openRenewModal(id, title) {
        document.getElementById('renew_id').value = id;
        document.getElementById('renew_title').innerText = title;
        var myModal = new bootstrap.Modal(document.getElementById('renewModal'));
        myModal.show();
    }
</script>
<?= $this->endSection() ?>