<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-arrow-left-right me-2"></i>Circulation & Transactions
            </h5>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="transactionsTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.85rem;">
                        <tr>
                            <th>NO</th>
                            <th>BORROWER</th>
                            <th class="text-start">ITEM DETAILS</th>
                            <th>REQUESTED ON</th>
                            <th>NEEDED BY</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.9rem;">
                        <?php if (!empty($transactions)): foreach($transactions as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="text-start">
                                <span class="fw-bold"><?= esc($row['user_name']) ?></span><br>
                                <small class="text-muted">ID: <?= esc($row['user_id_num']) ?></small>
                            </td>
                            <td class="text-start text-primary">
                                <span class="fw-semibold"><?= esc($row['collection_title']) ?></span><br>
                                <small class="text-muted">ID: <?= esc($row['collection_id']) ?></small>
                            </td>
                            <td><?= date('M d, Y', strtotime($row['date_requested'])) ?></td>
                            <td><?= date('M d, Y', strtotime($row['date_needed'])) ?></td>
                            <td>
                                <?php 
                                    $badge = 'bg-secondary';
                                    if($row['status'] == 'Pending') $badge = 'bg-warning text-dark';
                                    if($row['status'] == 'Borrowed' || $row['status'] == 'Approved') $badge = 'bg-info text-dark';
                                    if($row['status'] == 'Returned') $badge = 'bg-success';
                                    if($row['status'] == 'Cancelled' || $row['status'] == 'Rejected') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge ?>"><?= esc($row['status']) ?></span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Pending'): ?>
                                    <form action="/admin/transactions/updateStatus" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="status" value="Borrowed">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Approve & Checkout">Approve</button>
                                    </form>
                                    <form action="/admin/transactions/updateStatus" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="status" value="Rejected">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                    </form>
                                <?php elseif($row['status'] == 'Borrowed'): ?>
                                    <form action="/admin/transactions/updateStatus" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="status" value="Returned">
                                        <button type="submit" class="btn btn-success btn-sm">Mark Returned</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">No Actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    $('#transactionsTable').DataTable({
        "autoWidth": false,
        "order": [], // This keeps the oldest-first order we set in the controller
        "language": { "emptyTable": "No pending or active transactions." }
    });
});
</script>
<?= $this->endSection() ?>