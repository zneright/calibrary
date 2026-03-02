<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<style>
    .bg-aqua { background-color: #00c0ef !important; color: white; }
    .bg-orange { background-color: #f39c12 !important; color: white; }
    .bg-red { background-color: #dd4b39 !important; color: white; }
    .info-box { background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,0.1); border-radius: 2px; display: flex; margin-bottom: 15px; }
    .info-box-icon { border-top-left-radius: 2px; border-bottom-left-radius: 2px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; width: 90px; }
    .info-box-content { padding: 10px 15px; flex: 1; }
    .info-box-text { text-transform: uppercase; font-size: 11px; font-weight: 600; color: #333; }
    .info-box-number { font-weight: bold; font-size: 20px; margin: 0; color: #000; }
    .small-box { border-radius: 2px; position: relative; display: block; margin-bottom: 15px; box-shadow: 0 1px 1px rgba(0,0,0,0.1); overflow: hidden; }
    .small-box .inner { padding: 15px; }
    .small-box h3 { font-size: 38px; font-weight: bold; margin: 0 0 10px 0; z-index: 5; }
    .small-box .icon { transition: all .3s linear; position: absolute; top: -10px; right: 10px; z-index: 0; font-size: 70px; color: rgba(0,0,0,0.15); }
    .small-box-footer { background: rgba(0,0,0,0.1); color: rgba(255,255,255,0.8); display: block; padding: 3px 0; text-align: center; text-decoration: none; font-size: 14px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="info-box">
                        <div class="info-box-icon bg-aqua"><i class="bi bi-files"></i></div>
                        <div class="info-box-content">
                            <span class="info-box-text">Collections</span>
                            <span class="info-box-number"><?= $totalCollections ?></span>
                        </div>
                    </div>
                </div>
               <div class="col-md-6 col-xl-3">
                    <div class="info-box">
                        <div class="info-box-icon bg-aqua"><i class="bi bi-journals"></i></div>
                        <div class="info-box-content">
                            <span class="info-box-text">Journals</span>
                            <span class="info-box-number"><?= $totalJournals ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="info-box">
                        <div class="info-box-icon bg-aqua"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="info-box-content">
                            <span class="info-box-text">P. Decrees</span>
                            <span class="info-box-number"><?= $totalDecrees ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="info-box">
                        <div class="info-box-icon bg-aqua"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="info-box-content">
                            <span class="info-box-text">E. Orders</span>
                            <span class="info-box-number"><?= $totalOrders ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h3><?= $pendingApproval ?></h3>
                            <p>Pending Approval</p>
                        </div>
                        <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                        <a href="/admin/transactions" class="small-box-footer">View Transactions <i class="bi bi-arrow-right-circle-fill"></i></a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3><?= $overdueItems ?></h3>
                            <p>Overdue</p>
                        </div>
                        <div class="icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <a href="/admin/transactions" class="small-box-footer">View Transactions <i class="bi bi-arrow-right-circle-fill"></i></a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-graph-up me-2"></i>Activity Chart</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="activityChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-person-lines-fill me-2"></i>Recent Borrowers</h6>
                </div>
                <div class="list-group list-group-flush">
                    <?php if(!empty($recentBorrowers)): foreach($recentBorrowers as $borrow): ?>
                    <div class="list-group-item py-3">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3"><i class="bi bi-person text-primary"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold small"><?= esc($borrow['borrower_name']) ?></h6>
                                    <small class="text-muted">ID: <?= esc($borrow['user_id_num']) ?></small>
                                </div>
                            </div>
                                <small class="text-muted fw-bold">
                                    <?= \CodeIgniter\I18n\Time::parse($borrow['created_at'])->humanize() ?>
                                </small>
                        </div>
                        <div class="ms-5 mt-1">
                            <p class="mb-0 small text-secondary">Borrowed: <b><?= esc($borrow['collection_title']) ?></b></p>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                        <div class="p-4 text-center text-muted small">No recent activity</div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <a href="/admin/transactions" class="text-decoration-none fw-semibold small">View All Activity</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById('activityChart');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['3 Weeks Ago', '2 Weeks Ago', 'Last Week', 'This Week'],
                datasets: [
                    {
                        label: 'Items Added',
                        data: <?= $addedData ?>, 
                        borderColor: '#00c0ef',
                        backgroundColor: 'rgba(0, 192, 239, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Items Borrowed',
                        data: <?= $borrowedData ?>, 
                        borderColor: '#f39c12',
                        backgroundColor: 'rgba(243, 156, 18, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Items Returned',
                        data: <?= $returnedData ?>, 
                        borderColor: '#28a745', // Green color for returned items
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>