<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="page-header text-center px-3 py-5 mb-4 bg-primary text-white rounded-3 shadow-sm">
    <div class="container">
        <?php 
            $fullName = session()->get('fullname');
            $firstName = explode(' ', trim($fullName))[0]; 
        ?>
        <h2 class="fw-bold mb-3">Hello, <?= esc($firstName) ?>! 👋</h2>
        <p class="fs-5 text-white-50 mb-4">Search for library resources below.</p>
        
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
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <?php $hasOverdue = $overdueCount > 0; ?>
            <div class="card border-0 shadow-sm h-100 text-white <?= $hasOverdue ? 'bg-danger' : 'bg-success' ?>">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center me-4 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi <?= $hasOverdue ? 'bi-exclamation-triangle-fill text-danger' : 'bi-check-circle-fill text-success' ?> fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 fw-bold mb-1 small">ACTION REQUIRED</h6>
                        <h5 class="fw-bold mb-0"><?= $hasOverdue ? $overdueCount . ' Item Overdue' : 'No Overdue Items' ?></h5>
                        <small class="text-white-50"><?= $hasOverdue ? 'Please return immediately.' : 'You are all clear!' ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">My Active Borrows</h5>
            <a href="/borrower/my-books" class="btn btn-sm btn-outline-dark">View History</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">BOOK / ITEM</th>
                            <th>DATE REQUESTED</th>
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
                                            <div class="bg-light rounded p-2 me-3"><i class="bi bi-journal text-primary fs-4"></i></div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold"><?= esc($row['collection_title']) ?></h6>
                                                <small class="text-muted">Call No: <?= esc($row['class'] ?? 'N/A') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                                    <td class="<?= ($row['due_date'] < $today && $row['status'] == 'Borrowed') ? 'text-danger fw-bold' : '' ?>">
                                        <?= $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '---' ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-warning text-dark';
                                            $status = $row['status'];
                                            if ($status == 'Borrowed') {
                                                $badgeClass = ($row['due_date'] < $today) ? 'bg-danger' : 'bg-success';
                                                if ($row['due_date'] < $today) $status = 'Overdue';
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> px-2 py-1"><?= $status ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">No active items found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>