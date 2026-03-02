<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-bell me-2 text-primary"></i>Notification Center
                    </h5>
                    <span class="badge bg-primary rounded-pill"><?= $unreadCount ?> New</span>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (!empty($notifs)): ?>
                        <?php foreach ($notifs as $n): ?>
                            <div class="list-group-item p-4 <?= $n['status'] == 'unread' ? 'bg-light' : '' ?>">
                                <div class="d-flex align-items-start">
                                    <div class="me-3 fs-4">
                                        <i class="bi <?= ($n['type'] == 'alert') ? 'bi-exclamation-circle-fill text-danger' : (($n['type'] == 'warning') ? 'bi-exclamation-triangle-fill text-warning' : 'bi-info-circle-fill text-info') ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <p class="mb-1 <?= $n['status'] == 'unread' ? 'fw-bold' : 'text-muted' ?>">
                                                <?= esc($n['message']) ?>
                                            </p>
                                        </div>
                                        <small class="text-muted">
                                            <?= date('M d, Y | h:i A', strtotime($n['created_at'])) ?>
                                        </small>
                                    </div>
                                    <?php if($n['status'] == 'unread'): ?>
                                        <span class="badge bg-primary badge-dot p-1"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash text-muted display-4"></i>
                            <p class="text-muted mt-2">Your inbox is empty.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>