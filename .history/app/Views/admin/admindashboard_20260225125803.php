<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-left: 5px solid #1e3a8a !important;">
                <h6 class="text-muted small fw-bold">TOTAL BORROWED</h6>
                <h2 class="fw-bold"><?= $borrowedCount ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-left: 5px solid #0f766e !important;">
                <h6 class="text-muted small fw-bold">PENDING REQUESTS</h6>
                <h2 class="fw-bold"><?= $pendingCount ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-white <?= $overdueCount > 0 ? 'bg-danger' : 'bg-success' ?>">
                <h6 class="small fw-bold">SYSTEM OVERDUES</h6>
                <h2 class="fw-bold"><?= $overdueCount ?></h2>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Recent Transactions</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Borrower</th><th>Item</th><th>Due Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach($activeBorrows as $row): ?>
                    <tr>
                        <td><?= esc($row['borrower_name']) ?></td>
                        <td><?= esc($row['collection_title']) ?></td>
                        <td><?= $row['due_date'] ?? 'N/A' ?></td>
                        <td><span class="badge bg-primary"><?= $row['status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>