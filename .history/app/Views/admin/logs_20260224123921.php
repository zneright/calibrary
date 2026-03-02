<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-clock-history me-2 text-primary"></i>System Activity Logs
            </h5>
            
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm shadow-sm" title="Export to CSV">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <button type="button" class="btn btn-danger btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                    <i class="bi bi-trash3 me-1"></i> Clear Logs
                </button>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="logsTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.9rem;">
                        <tr>
                            <th width="5%">NO</th>
                            <th width="15%">TIMESTAMP</th>
                            <th class="text-start" width="15%">USER</th>
                            <th>MODULE</th>
                            <th>ACTION</th>
                            <th class="text-start" width="30%">DETAILS</th>
                        </tr>
                    </thead>
                   <tbody class="align-middle text-center" style="font-size: 0.85rem;">
                        <?php if (!empty($logs)): ?>
                            <?php foreach($logs as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="text-muted"><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
                                <td class="text-start fw-semibold">
                                    <?= esc($row['user_name']) ?> <br>
                                    <small class="text-muted fw-normal">ID: <?= esc($row['user_id_num']) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= esc($row['module']) ?></span></td>
                                
                                <td>
                                    <?php 
                                        // Dynamic badge colors based on action type
                                        $badgeClass = 'bg-secondary';
                                        if ($row['action'] == 'Register') $badgeClass = 'bg-primary';
                                        if ($row['action'] == 'Approve') $badgeClass = 'bg-success';
                                        if ($row['action'] == 'Delete' || $row['action'] == 'Failed') $badgeClass = 'bg-danger';
                                        if ($row['action'] == 'Update') $badgeClass = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc($row['action']) ?></span>
                                </td>
                                
                                <td class="text-start"><?= esc($row['details']) ?></td>
                                <td class="text-muted font-monospace"><?= esc($row['ip_address']) ?></td>
                            </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-muted py-4">No system logs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Warning
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold">Are you sure?</h5>
                <p class="text-muted mb-0">This will permanently delete all system logs. This action cannot be undone.</p>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary shadow-sm px-4" data-bs-dismiss="modal">Cancel</button>
                <form action="/admin/logs/clear" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger shadow-sm px-4">Yes, Clear All</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    $('#logsTable').DataTable({
        "autoWidth": false,
        "order": [[ 0, "asc" ]], // Change to "desc" when using real IDs to show newest first
        "language": { "emptyTable": "No system logs found." }
    });
});
</script>
<?= $this->endSection() ?>