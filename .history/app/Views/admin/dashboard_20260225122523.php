<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="page-header text-center px-3 py-5 mb-4 bg-primary text-white rounded-3 shadow-sm">
    <div class="container">
        <?php 
            $fullName = session()->get('fullname');
            $firstName = explode(' ', trim($fullName))[0]; 
        ?>
        <h2 class="fw-bold mb-2">Hello, <?= esc($firstName) ?>! 👋</h2>
        <p class="fs-6 opacity-75 mb-4">Manage your borrowed items and find new resources.</p>
        
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
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #1e3a8a !important;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center me-4" style="width: 60px; height: 60px; background-color: rgba(30, 58, 138, 0.1); color: #1e3a8a;">
                        <i class="bi bi-book fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1 small">CURRENTLY BORROWED</h6>
                        <h3 class="fw-bold mb-0 text-dark"><?= $borrowedCount ?> <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <a href="/borrower/my-books" class="text-decoration-none fw-semibold small" style="color: #1e3a8a;">View due dates <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0f766e !important;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center me-4" style="width: 60px; height: 60px; background-color: rgba(15, 118, 110, 0.1); color: #0f766e;">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1 small">PENDING REQUESTS</h6>
                        <h3 class="fw-bold mb-0 text-dark"><?= $pendingCount ?> <span class="fs-6 text-muted fw-normal">Item</span></h3>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <a href="/borrower/my-books" class="text-decoration-none fw-semibold small" style="color: #0f766e;">Check status <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-white <?= ($overdueCount > 0) ? 'bg-danger' : 'bg-success' ?>">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center me-4 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi <?= ($overdueCount > 0) ? 'bi-exclamation-triangle-fill text-danger' : 'bi-check-circle-fill text-success' ?> fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 fw-bold mb-1 small">ACTION REQUIRED</h6>
                        <h5 class="fw-bold mb-0"><?= ($overdueCount > 0) ? $overdueCount . ' Item Overdue' : 'No Overdue Items' ?></h5>
                        <small class="text-white-50"><?= ($overdueCount > 0) ? 'Please return immediately.' : 'You are all clear!' ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">My Active Borrows</h5>
            <a href="/borrower/my-books" class="btn btn-sm btn-outline-dark shadow-sm">View All History</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">BOOK / ITEM</th>
                            <th>DATE BORROWED</th>
                            <th>DUE DATE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activeBorrows)): ?>
                            <?php foreach ($activeBorrows as $row): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3"><i class="bi bi-book fs-4 text-primary"></i></div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold"><?= esc($row['collection_title']) ?></h6>
                                                <small class="text-muted">Call No: <?= esc($row['class']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td class="fw-semibold <?= (isset($row['due_date']) && $row['due_date'] < $today && $row['status'] == 'Borrowed') ? 'text-danger' : '' ?>">
                                        <?= isset($row['due_date']) ? date('M d, Y', strtotime($row['due_date'])) : '---' ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-info';
                                            if ($row['status'] == 'Borrowed') $badgeClass = 'bg-success';
                                            if ($row['status'] == 'Pending') $badgeClass = 'bg-warning text-dark';
                                            
                                            // Handle Overdue label
                                            if (isset($row['due_date']) && $row['due_date'] < $today && $row['status'] == 'Borrowed') {
                                                $statusText = 'Overdue';
                                                $badgeClass = 'bg-danger';
                                            } else {
                                                $statusText = $row['status'];
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> bg-opacity-10 text-<?= str_replace('bg-', '', $badgeClass) ?> border border-<?= str_replace('bg-', '', $badgeClass) ?> border-opacity-25 px-2 py-1">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">You have no active borrowings or requests.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>