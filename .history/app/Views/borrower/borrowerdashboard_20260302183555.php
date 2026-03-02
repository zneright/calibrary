<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<style>
    
    /* Modern Card Hover Effects */
    .stat-card { 
        transition: all 0.3s ease; 
        border: none; 
        border-radius: 1rem;
    }
    .stat-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
    }
    .stat-icon { 
        width: 52px; 
        height: 52px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border-radius: 14px; 
    }
    
    /* Table UI Polish */
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }
    
    /* Avatar Glow */
    .avatar-glow {
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
    }
</style>

<div class="page-header text-center px-3 py-5 mb-5 text-white rounded-bottom-4 shadow" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); margin-top: -1.5rem;">
    <div class="container mt-3">
        <?php 
            $fullName = session()->get('fullname') ?? 'Employee';
            $firstName = explode(' ', trim($fullName))[0]; 
            
            // Construct the avatar path, fallback to a default UI icon if empty
            $avatarPath = (!empty($currentUser['avatar'])) 
                ? base_url('uploads/avatars/' . $currentUser['avatar']) 
                : base_url('images/default-avatar.png');
        ?>
        
        <div class="mb-4">
            <?php if(!empty($currentUser['avatar'])): ?>
                <img src="<?= esc($avatarPath) ?>" alt="Profile" class="rounded-circle avatar-glow border border-2 border-white" style="width: 100px; height: 100px; object-fit: cover; background-color: #fff;">
            <?php else: ?>
                <img src="<?= esc($avatarPath) ?>" alt="Profile" class="rounded-circle avatar-glow border border-2 border-white" style="width: 100px; height: 100px; object-fit: cover; background-color: #e2e8f0;">
            <?php endif; ?>
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
                                <tr>
                                    <td class="ps-4 py-3 border-light">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 text-primary shadow-sm" style="width: 45px; height: 45px;">
                                                <i class="bi bi-file-earmark-text fs-5"></i>
                                            </div>
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
                                            $badgeClass = 'bg-secondary text-white'; // Default
                                            $displayLabel = $status;
                                            
                                            // 1. PENDING REQUESTS
                                            if ($status == 'Pending') {
                                                $badgeClass = 'bg-warning text-dark';
                                                $displayLabel = 'Pending Review';
                                            } 
                                            
                                            // 2. READY FOR PICKUP
                                            elseif ($status == 'Approved') { 
                                                $badgeClass = 'bg-success text-white'; 
                                                $displayLabel = 'Ready for Pickup'; 
                                            }

                                            // 3. ON HAND / RENEWING / OVERDUE
                                            elseif ($status == 'Borrowed' || $status == 'Renewing') { 
                                                // Check if it's actually overdue
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
                                            <i class="bi bi-circle-fill me-1 small" style="font-size: 0.5rem;"></i>
                                            <?= esc($displayLabel) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 border-light">
                                        <a href="/borrower/my-books" class="btn btn-sm btn-outline-dark rounded-pill small px-4 fw-semibold shadow-sm">Manage</a>
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

<?= $this->endSection() ?>