<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-clock-history me-2 text-primary"></i>System Activity Logs
            </h5>
        </div>

        <div class="card-body p-4">
            
            <form action="<?= base_url('admin/logs/export') ?>" method="GET" target="_blank" class="bg-light p-3 rounded mb-4 border shadow-sm">
                <div class="row g-2 align-items-end">
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-calendar-range me-1"></i>Start Date</label>
                        <input type="date" name="start_date" id="startDate" class="form-control form-control-sm border-secondary">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-calendar-range-fill me-1"></i>End Date</label>
                        <input type="date" name="end_date" id="endDate" class="form-control form-control-sm border-secondary">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Module</label>
                        <select name="module" id="moduleFilter" class="form-select form-select-sm border-secondary">
                            <option value="">All Modules</option>
                            <option value="Authentication">Authentication</option>
                            <option value="Reports">Reports</option>
                            <option value="Transactions">Transactions</option>
                            <option value="Collections">Collections</option>
                            <option value="Journals">Journals</option>
                            <option value="User Profile">User Profile</option>
                            <option value="Security">Security</option>
                            <option value="Notifications">Notifications</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Action</label>
                        <select name="action" id="actionFilter" class="form-select form-select-sm border-secondary">
                            <option value="">All Actions</option>
                            <option value="Login">Login</option>
                            <option value="Generate">Generate</option>
                            <option value="Download">Download</option>
                            <option value="Add">Add / Create</option>
                            <option value="Update">Update</option>
                            <option value="Delete">Delete</option>
                            <option value="Approve">Approve</option>
                            <option value="Register">Register</option>
                            <option value="Reset">Reset Password</option>
                            <option value="Cancel">Cancel / Deny</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex gap-2">
                        <button type="button" id="btnFilter" class="btn btn-primary btn-sm w-50 fw-bold shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i> Go
                        </button>
                        
                        <button type="submit" class="btn btn-danger btn-sm w-50 fw-bold shadow-sm" title="Export Filtered Data to PDF">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="logsTable">
                    <thead class="table-light align-middle text-center" style="font-size: 0.9rem;">
                        <tr>
                            <th width="5%">NO</th>
                            <th width="15%">TIMESTAMP</th>
                            <th class="text-start" width="20%">USER</th>
                            <th width="12%">MODULE</th>
                            <th width="12%">ACTION</th>
                            <th class="text-start" width="36%">DETAILS</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center" style="font-size: 0.85rem;">
                        <?php if (!empty($logs)): ?>
                            <?php foreach($logs as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td data-sort="<?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?>" class="text-muted fw-medium">
                                    <?= date('M d, Y', strtotime($row['created_at'])) ?><br>
                                    <small><?= date('h:i A', strtotime($row['created_at'])) ?></small>
                                </td>
                                
                                <td class="text-start fw-semibold">
                                    <?= esc($row['user_name']) ?> <br>
                                    <small class="text-muted fw-normal">ID: <?= esc($row['user_id_num']) ?></small>
                                </td>
                                
                                <td><span class="badge bg-light text-dark border px-2 py-1"><?= esc($row['module']) ?></span></td>
                                
                                <td>
                                    <?php 
                                        $badgeClass = 'bg-secondary';
                                        $actionStr = strtolower($row['action']);
                                        
                                        // Colors for different actions
                                        if (str_contains($actionStr, 'login')) $badgeClass = 'bg-info text-dark';
                                        if (str_contains($actionStr, 'generate') || str_contains($actionStr, 'download')) $badgeClass = 'bg-primary';
                                        if (str_contains($actionStr, 'register')) $badgeClass = 'bg-primary';
                                        if (str_contains($actionStr, 'approve') || str_contains($actionStr, 'add') || str_contains($actionStr, 'create')) $badgeClass = 'bg-success';
                                        if (str_contains($actionStr, 'delete') || str_contains($actionStr, 'failed') || str_contains($actionStr, 'cancel') || str_contains($actionStr, 'reject')) $badgeClass = 'bg-danger';
                                        if (str_contains($actionStr, 'update') || str_contains($actionStr, 'reset')) $badgeClass = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-2 py-1 shadow-sm"><?= esc($row['action']) ?></span>
                                </td>
                                
                                <td class="text-start text-secondary"><?= esc($row['details']) ?></td>
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
    // 1. Initialize DataTable
    var table = $('#logsTable').DataTable({
        "autoWidth": false,
        "order": [], // Keeps original backend ordering initially
        "pageLength": 25,
        "language": { "emptyTable": "No system logs found matching criteria." }
    });

    // 2. Custom Filtering logic to hide rows visually based on the Filter Bar
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            // Get values from the filter form
            var minDate = $('#startDate').val();
            var maxDate = $('#endDate').val();
            var searchModule = $('#moduleFilter').val().toLowerCase();
            var searchAction = $('#actionFilter').val().toLowerCase();

            // Get row data (Index 1 is Timestamp, 3 is Module, 4 is Action)
            // Note: data[1] pulls the display text, so we parse it safely.
            var rowDateStr = data[1].split('<br>')[0].trim(); // Grabs 'M d, Y'
            var rowDate = new Date(rowDateStr).setHours(0,0,0,0);
            
            var rowModule = data[3].toLowerCase();
            var rowAction = data[4].toLowerCase();

            // Evaluate Module
            if (searchModule && rowModule.indexOf(searchModule) === -1) {
                return false;
            }
            
            // Evaluate Action
            if (searchAction && rowAction.indexOf(searchAction) === -1) {
                return false;
            }

            // Evaluate Start Date
            if (minDate) {
                var min = new Date(minDate).setHours(0,0,0,0);
                if (rowDate < min) return false;
            }

            // Evaluate End Date
            if (maxDate) {
                var max = new Date(maxDate).setHours(0,0,0,0);
                if (rowDate > max) return false;
            }

            return true;
        }
    );

    // 3. UPDATED: Only trigger the filter when the "Go" button is clicked!
    $('#btnFilter').on('click', function() {
        table.draw();
    });
});
</script>
<?= $this->endSection() ?>