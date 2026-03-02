<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<style>
    .stat-card { transition: transform 0.2s; border: none; }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
</style>

<div class="page-header text-center px-3 py-5 mb-4 bg-primary text-white rounded-3 shadow-sm">
    <div class="container">
        <?php 
        
            $fullName = session()->get('fullname');
            $firstName = explode(' ', trim($fullName))[0]; 
        ?>
        <h4 class="fw-bold">Welcome, <?= session()->get('fullname') ?>!</h4>
        <p class="fs-6 opacity-75 mb-4">Track your library requests and explore new resources.</p>
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form action="/borrower/catalog" method="GET" class="d-flex shadow-lg rounded-pill bg-white p-1">
                    <input type="text" name="q" class="form-control border-0 rounded-pill px-4" placeholder="Search for books, journals, authors..." style="box-shadow: none;">
                    <button class="btn btn-dark rounded-pill px-4 fw-semibold" type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row g-3 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100" style="border-top: 4px solid #1e3a8a;">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3" style="background-color: rgba(30, 58, 138, 0.1); color: #1e3a8a;">
                        <i class="bi bi-book-fill fs-4"></i>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">ON HAND</h6>
                    <h3 class="fw-bold mb-3"><?= $borrowedCount ?> <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    <a href="/borrower/my-books#borrowed" class="btn btn-sm btn-outline-primary w-100 rounded-pill">View Due Dates</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100" style="border-top: 4px solid #059669;">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3" style="background-color: rgba(5, 150, 105, 0.1); color: #059669;">
                        <i class="bi bi-box-seam-fill fs-4"></i>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">READY FOR PICKUP</h6>
                    <h3 class="fw-bold mb-3"><?= $pickupCount ?> <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    <a href="/borrower/my-books#approved" class="btn btn-sm btn-outline-success w-100 rounded-pill">View Details</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100" style="border-top: 4px solid #d97706;">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3" style="background-color: rgba(217, 119, 6, 0.1); color: #d97706;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">PENDING APPROVAL</h6>
                    <h3 class="fw-bold mb-3"><?= $pendingCount ?> <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    <a href="/borrower/my-books#pending" class="btn btn-sm btn-outline-warning w-100 rounded-pill">Check Status</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <?php $isCritical = $overdueCount > 0; ?>
            <div class="card stat-card shadow-sm h-100 text-white <?= $isCritical ? 'bg-danger' : 'bg-secondary opacity-75' ?>">
                <div class="card-body p-4">
                    <div class="stat-icon mb-3 bg-white">
                        <i class="bi <?= $isCritical ? 'bi-exclamation-triangle-fill text-danger' : 'bi-check-circle-fill text-secondary' ?> fs-4"></i>
                    </div>
                    <h6 class="text-white-50 small fw-bold mb-1">OVERDUE ITEMS</h6>
                    <h3 class="fw-bold mb-3"><?= $overdueCount ?></h3>
                    <a href="/borrower/my-books" class="btn btn-sm <?= $isCritical ? 'btn-light text-danger' : 'btn-dark' ?> w-100 rounded-pill fw-bold">
                        <?= $isCritical ? 'Resolve Now' : 'Check History' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">Active Transaction Monitor</h6>
            <span class="badge bg-light text-dark border fw-normal">Real-time Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">RESOURCE</th>
                            <th>REQUEST DATE</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activeBorrows)): ?>
                            <?php foreach ($activeBorrows as $row): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-primary"><i class="bi bi-file-earmark-text fs-4"></i></div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold small text-truncate" style="max-width: 300px;"><?= esc($row['collection_title']) ?></h6>
                                                <small class="text-muted"><?= esc($row['class'] ?? 'N/A') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small"><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-warning text-dark';
                                            $status = $row['status'];
                                            if ($status == 'Approved') { $status = 'Ready for Pickup'; $badgeClass = 'bg-success'; }
                                            if ($status == 'Borrowed') { 
                                                $status = 'On Hand'; 
                                                $badgeClass = ($row['due_date'] < $today) ? 'bg-danger' : 'bg-info text-white';
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> bg-opacity-75 px-2 py-1" style="font-size: 0.75rem;">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="/borrower/my-books" class="btn btn-sm btn-light border small px-3">Manage</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted small">You have no active transactions at the moment.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>