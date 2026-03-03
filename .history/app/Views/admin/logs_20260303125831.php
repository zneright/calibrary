<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-clock-history me-2 text-primary"></i>System Activity Logs
            </h5>
            
            <div class="d-flex gap-2">
                <form action="<?= base_url('admin/logs/export') ?>" method="GET" target="_blank" class="mb-0 d-flex gap-2 align-items-center">
                    
                    <select name="module" class="form-select form-select-sm" id="exportModuleFilter" style="width: 150px;">
                        <option value="">All Modules</option>
                        <option value="Transactions">Transactions</option>
                        <option value="Collections">Collections</option>
                        <option value="User Profile">User Profile</option>
                        <option value="Security">Security</option>
                    </select>

                    <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm" title="Export Filtered Data to PDF">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
                    </button>
                </form>
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
                                        if ($row['action'] == 'Approve' || $row['action'] == 'Add') $badgeClass = 'bg-success';
                                        if ($row['action'] == 'Delete' || $row['action'] == 'Failed') $badgeClass = 'bg-danger';
                                        if ($row['action'] == 'Update') $badgeClass = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc($row['action']) ?></span>
                                </td>
                                
                                <td class="text-start"><?= esc($row['details']) ?></td>
                            </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-muted py-4">No system logs found.</td>
                            </tr>
                        <?php endif; ?>
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
    // Initialize DataTable
    var table = $('#logsTable').DataTable({
        "autoWidth": false,
        "order": [],
        "language": { "emptyTable": "No system logs found." }
    });

    // Make the UI Filter Dropdown sync with the DataTable visually so the user sees what they are about to export
    $('#exportModuleFilter').on('change', function() {
        var selectedModule = $(this).val();
        table.column(3).search(selectedModule).draw(); // Column 3 is the "MODULE" column
    });
});
</script>
<?= $this->endSection() ?>