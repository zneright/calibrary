<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<style>
    .bg-danger.animate__infinite {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .stat-card { transition: all 0.3s ease; border: none; border-radius: 1rem; }
    .stat-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important; }
    .stat-icon { width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; border-radius: 14px; }
    .table-hover tbody tr:hover { background-color: #f8fafc; cursor: pointer; }
    .avatar-glow { box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2); }
</style>

<div class="page-header text-center px-3 py-5 mb-5 text-white rounded-bottom-4 shadow" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); margin-top: -1.5rem;">
    <div class="container mt-3">
        <?php 
            $fullName = session()->get('fullname') ?? 'Employee';
            $firstName = explode(' ', trim($fullName))[0]; 
            $avatarPath = (!empty($currentUser['avatar'])) ? base_url('uploads/avatars/' . $currentUser['avatar']) : base_url('images/default-avatar.png');
        ?>
        
        <div class="mb-4">
            <img src="<?= esc($avatarPath) ?>" alt="Profile" class="rounded-circle avatar-glow border border-2 border-white" style="width: 100px; height: 100px; object-fit: cover; background-color: #<?= empty($currentUser['avatar']) ? 'e2e8f0' : 'fff' ?>;">
        </div>

        <h2 class="fw-bold mb-2" style="letter-spacing: -0.5px;">Hello, <?= esc($firstName) ?>! 👋</h2>
        <p class="fs-6 text-white-50 mb-4">Track your library requests and explore new resources.</p>
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form action="/borrower/catalog" method="GET" class="d-flex shadow-lg rounded-pill bg-white p-2">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 shadow-none bg-transparent" placeholder="Search for books, journals, authors...">
                        <button class="btn btn-dark rounded-pill px-4 fw-semibold shadow-sm" type="submit" style="background-color: #1a2942;">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100 bg-white" style="border-bottom: 4px solid #1e3a8a;">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3" style="background-color: rgba(30, 58, 138, 0.1); color: #1e3a8a;">
                        <i class="bi bi-book-half fs-4"></i>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1 tracking-wide">ON HAND</h6>
                    <h3 class="fw-bold mb-3 text-dark"><?= $borrowedCount ?? 0 ?> <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    <a href="/borrower/my-books#borrowed" class="btn btn-sm btn-light text-primary w-100 rounded-pill fw-semibold border">View Due Dates</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100 bg-white" style="border-bottom: 4px solid #059669;">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3" style="background-color: rgba(5, 150, 105, 0.1); color: #059669;">
                        <i class="bi bi-bag-check-fill fs-4"></i>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1 tracking-wide">READY FOR PICKUP</h6>
                    <h3 class="fw-bold mb-3 text-dark"><?= $pickupCount ?? 0 ?> <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    <a href="/borrower/my-books#approved" class="btn btn-sm btn-light text-success w-100 rounded-pill fw-semibold border">View Details</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100 bg-white" style="border-bottom: 4px solid #d97706;">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3" style="background-color: rgba(217, 119, 6, 0.1); color: #d97706;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1 tracking-wide">PENDING APPROVAL</h6>
                    <h3 class="fw-bold mb-3 text-dark"><?= $pendingCount ?? 0 ?> <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    <a href="/borrower/my-books#pending" class="btn btn-sm btn-light text-warning text-dark w-100 rounded-pill fw-semibold border">Check Status</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <?php $isCritical = ($overdueCount ?? 0) > 0; ?>
            <div class="card stat-card shadow-sm h-100 <?= $isCritical ? 'bg-danger text-white' : 'bg-white' ?>" style="<?= !$isCritical ? 'border-bottom: 4px solid #dc3545;' : '' ?>">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3 <?= $isCritical ? 'bg-white text-danger shadow-sm' : 'bg-danger bg-opacity-10 text-danger' ?>">
                        <i class="bi <?= $isCritical ? 'bi-exclamation-triangle-fill' : 'bi-shield-check' ?> fs-4"></i>
                    </div>
                    <h6 class="<?= $isCritical ? 'text-white-50' : 'text-muted' ?> small fw-bold mb-1 tracking-wide">OVERDUE ITEMS</h6>
                    <h3 class="fw-bold mb-3 <?= $isCritical ? 'text-white' : 'text-dark' ?>"><?= $overdueCount ?? 0 ?> <span class="fs-6 <?= $isCritical ? 'text-white-50' : 'text-muted' ?> fw-normal">Items</span></h3>
                    <a href="/borrower/my-books" class="btn btn-sm w-100 rounded-pill fw-bold <?= $isCritical ? 'btn-light text-danger shadow' : 'btn-light text-danger border' ?>">
                        <?= $isCritical ? 'Resolve Now' : 'Check History' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-bottom border-light">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-activity me-2 text-primary"></i>Active Transaction Monitor</h6>
            <span class="badge bg-light text-dark border fw-normal shadow-sm px-3 py-2 rounded-pill"><i class="bi bi-broadcast me-1 text-success"></i> Real-time Data</span>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-white">
                    <thead class="bg-light text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase border-bottom-0">Resource</th>
                            <th class="text-uppercase border-bottom-0">Request Date</th>
                            <th class="text-uppercase border-bottom-0">Status</th>
                            <th class="text-end pe-4 text-uppercase border-bottom-0">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activeBorrows)): ?>
                            <?php foreach ($activeBorrows as $row): ?>
                                <?php 
                                    // Encode row data to pass to JS modal
                                    $itemJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); 
                                ?>
                                <tr onclick="openDetailsModal(<?= $itemJson ?>)">
                                    <td class="ps-4 py-3 border-light">
                                        <div class="d-flex align-items-center">
                                            
                                            <?php if(!empty($row['cover_photo'])): ?>
                                                <img src="<?= base_url('uploads/covers/'.$row['cover_photo']) ?>" class="rounded shadow-sm me-3" style="width: 45px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 text-primary shadow-sm" style="width: 45px; height: 60px;">
                                                    <i class="bi <?= (isset($row['type']) && $row['type'] == 'Journal') ? 'bi-journal-bookmark' : 'bi-book' ?> fs-4"></i>
                                                </div>
                                            <?php endif; ?>

                                            <div>
                                                <h6 class="mb-0 fw-semibold text-dark text-truncate" style="max-width: 300px;">
                                                    <?= esc($row['collection_title']) ?>
                                                </h6>
                                                <small class="text-muted"><i class="bi bi-bookmark me-1"></i><?= esc($row['class'] ?? 'General') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small text-muted fw-medium border-light">
                                        <i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($row['date_requested'])) ?>
                                    </td>
                                    <td class="border-light">
                                        <?php 
                                            $status = $row['status'];
                                            $badgeClass = 'bg-secondary text-white'; 
                                            $displayLabel = $status;
                                            
                                            if ($status == 'Pending') {
                                                $badgeClass = 'bg-warning text-dark';
                                                $displayLabel = 'Pending Review';
                                            } elseif ($status == 'Approved') { 
                                                $badgeClass = 'bg-success text-white'; 
                                                $displayLabel = 'Ready for Pickup'; 
                                            } elseif ($status == 'Borrowed' || $status == 'Renewing') { 
                                                $isOverdue = (!empty($row['due_date']) && strtotime($row['due_date']) < strtotime($today));
                                                if ($isOverdue) {
                                                    $badgeClass = 'bg-danger text-white animate__animated animate__pulse animate__infinite';
                                                    $displayLabel = 'Overdue';
                                                } elseif ($status == 'Renewing') {
                                                    $badgeClass = 'bg-info text-dark';
                                                    $displayLabel = 'Extension Pending';
                                                } else {
                                                    $badgeClass = 'bg-primary text-white';
                                                    $displayLabel = 'On Hand';
                                                }
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill fw-medium shadow-sm" style="font-size: 0.75rem;">
                                            <i class="bi bi-circle-fill me-1 small" style="font-size: 0.5rem;"></i> <?= esc($displayLabel) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 border-light">
                                        <a href="#" class="btn btn-sm btn-outline-dark rounded-pill small px-4 fw-semibold shadow-sm" onclick="event.preventDefault();">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 border-0">
                                    <div class="text-muted opacity-50 mb-3">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <p class="mb-0 fw-medium">No active transactions right now.</p>
                                    <small>Use the search bar above to find your next read!</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                                    <span class="d-block text-uppercase text-danger" style="font-size: 0.7rem;">Due Date</span>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openDetailsModal(item) {
        
        document.getElementById('modalTitle').innerText = item.collection_title;
        document.getElementById('modalAuthor').innerText = item.author || 'Unknown';
        document.getElementById('modalClass').innerText = item.class || 'N/A';
        document.getElementById('modalTypeBadge').innerText = item.type || 'Resource';
        
        document.getElementById('modalTransId').innerText = 'TRX-' + String(item.id).padStart(5, '0');
        
        // Format Dates
        const reqDate = new Date(item.date_requested).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        document.getElementById('modalDateRequested').innerText = reqDate;
        
        if (item.due_date) {
            const dueDateObj = new Date(item.due_date);
            const today = new Date();
            today.setHours(0,0,0,0);
            
            document.getElementById('modalDueDate').innerText = dueDateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            
            if (dueDateObj < today) {
                document.getElementById('modalDueDate').className = "text-danger fw-bold animate__animated animate__flash animate__infinite";
            } else {
                document.getElementById('modalDueDate').className = "text-dark fw-bold";
            }
        } else {
            document.getElementById('modalDueDate').innerText = 'Pending Setup';
            document.getElementById('modalDueDate').className = "text-muted";
        }

        //Set Status 
        let badge = document.getElementById('modalStatusBadge');
        let status = item.status;
        badge.innerText = status;
        
        if(status === 'Approved') {
            badge.innerText = 'Ready for Pickup';
            badge.className = 'badge bg-success text-white px-3 py-2';
        } else if (status === 'Borrowed') {
            badge.innerText = 'On Hand';
            badge.className = 'badge bg-primary text-white px-3 py-2';
        } else if (status === 'Renewing') {
            badge.innerText = 'Extension Pending';
            badge.className = 'badge bg-info text-dark px-3 py-2';
        } else {
            badge.innerText = 'Pending Review';
            badge.className = 'badge bg-warning text-dark px-3 py-2';
        }

        //  Cover Image
        let coverContainer = document.getElementById('modalCoverContainer');
        if (item.cover_photo) {
            let imgUrl = "<?= base_url('uploads/covers/') ?>" + item.cover_photo;
            coverContainer.innerHTML = `<img src="${imgUrl}" class="img-fluid rounded shadow" style="max-height: 350px; object-fit: contain;" alt="Cover">`;
        } else {
            let icon = (item.type === 'Journal') ? 'bi-journal-bookmark' : 'bi-book';
            coverContainer.innerHTML = `<i class="bi ${icon} text-secondary" style="font-size: 8rem;"></i>`;
        }

        //  Set Footer Actions
        let footerHtml = `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>`;
        
        // If the item is currently borrowed, they can request a renewal directly from this modal
        if (status === 'Borrowed') {
            // Re-usingxisting cancel form logic, butredirect to the my-books page or trigger renewal
            footerHtml = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="/borrower/my-books" class="btn btn-primary fw-bold">Manage Request in Library</a>
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

        // 5. Open Modal
        var myModal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
        myModal.show();
    }
</script>
<?= $this->endSection() ?>