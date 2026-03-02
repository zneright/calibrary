<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-secondary mb-1">System Notifications</h4>
            <p class="text-muted small mb-0">Manage and view all administrative alerts and system logs.</p>
        </div>
        <span class="badge bg-primary px-3 py-2"><?= $unreadCount ?> New Alerts</span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            <?php if (!empty($notifs)): ?>
                <?php foreach ($notifs as $n): ?>
                    <div class="list-group-item p-4 <?= $n['status'] == 'unread' ? 'bg-light' : '' ?>" style="border-left: 4px solid <?= $n['type'] == 'alert' ? '#dc3545' : ($n['type'] == 'warning' ? '#ffc107' : '#0dcaf0') ?>;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3">
                                <div class="fs-4">
                                    <i class="bi <?= ($n['type'] == 'alert') ? 'bi-exclamation-octagon-fill text-danger' : (($n['type'] == 'warning') ? 'bi-exclamation-triangle-fill text-warning' : 'bi-info-circle-fill text-info') ?>"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 <?= $n['status'] == 'unread' ? 'fw-bold text-dark' : 'text-secondary' ?>">
                                        <?= esc($n['message']) ?>
                                    </h6>
                                    <div class="d-flex align-items-center gap-3 mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($n['created_at'])) ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i><?= date('h:i A', strtotime($n['created_at'])) ?>
                                        </small>
                                        <small class="badge bg-secondary-subtle text-secondary border">
                                            Type: <?= ucfirst(esc($n['type'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($n['status'] == 'unread'): ?>
                                <span class="badge rounded-pill bg-primary">NEW</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted display-1"></i>
                    <p class="mt-3 text-muted">No system notifications found.</p>
                    <a href="/admin/dashboard" class="btn btn-primary btn-sm mt-2">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>