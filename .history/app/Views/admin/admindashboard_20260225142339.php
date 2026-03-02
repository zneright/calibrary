<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<style>
    /* Custom CSS to match your reference image */
    .bg-aqua { background-color: #00c0ef !important; color: white; }
    .bg-orange { background-color: #f39c12 !important; color: white; }
    
    /* Info Box (Top Row) */
    .info-box { background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,0.1); border-radius: 2px; display: flex; margin-bottom: 15px; }
    .info-box-icon { border-top-left-radius: 2px; border-bottom-left-radius: 2px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; width: 90px; }
    .info-box-content { padding: 10px 15px; flex: 1; }
    .info-box-text { text-transform: uppercase; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 2px; }
    .info-box-number { font-weight: bold; font-size: 24px; margin: 0; color: #000; }
    
    /* Small Box (Bottom Row) */
    .small-box { border-radius: 2px; position: relative; display: block; margin-bottom: 15px; box-shadow: 0 1px 1px rgba(0,0,0,0.1); overflow: hidden; }
    .small-box .inner { padding: 15px; }
    .small-box h3 { font-size: 38px; font-weight: bold; margin: 0 0 10px 0; white-space: nowrap; padding: 0; z-index: 5; }
    .small-box p { font-size: 15px; z-index: 5; margin-bottom: 0; }
    .small-box .icon { transition: all .3s linear; position: absolute; top: -10px; right: 10px; z-index: 0; font-size: 70px; color: rgba(0,0,0,0.15); }
    .small-box:hover .icon { font-size: 75px; right: 15px; }
    .small-box-footer { background: rgba(0,0,0,0.1); color: rgba(255,255,255,0.8); display: block; padding: 3px 0; position: relative; text-align: center; text-decoration: none; z-index: 10; font-size: 14px; }
    .small-box-footer:hover { color: #fff; background: rgba(0,0,0,0.15); }
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
                            <span class="info-box-number">63</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="info-box">
                        <div class="info-box-icon bg-aqua"><i class="bi bi-journals"></i></div>
                        <div class="info-box-content">
                            <span class="info-box-text">Journals</span>
                            <span class="info-box-number">52</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="info-box">
                        <div class="info-box-icon bg-aqua"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="info-box-content">
                            <span class="info-box-text" style="font-size: 11px;">Presidential Decree</span>
                            <span class="info-box-number">50</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="info-box">
                        <div class="info-box-icon bg-aqua"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="info-box-content">
                            <span class="info-box-text" style="font-size: 11px;">Executive Order</span>
                            <span class="info-box-number">50</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h3>5</h3>
                            <p>Overall Transactions</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-exclamation-circle-fill"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            More info <i class="bi bi-arrow-right-circle-fill"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>7</h3>
                            <p>Overall Requests</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            More info <i class="bi bi-arrow-right-circle-fill"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-graph-up me-2"></i>Monthly Activity
                    </h6>
                    
                    <div class="d-flex align-items-center">
                        <label for="chartMonth" class="form-label mb-0 me-2 small text-muted fw-semibold whitespace-nowrap">Filter:</label>
                        <input type="month" id="chartMonth" class="form-control form-control-sm" value="<?= date('Y-m') ?>">
                    </div>
                </div>
                
                <div class="card-body p-3">
                    <canvas id="activityChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
            
        </div> <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-person-lines-fill me-2"></i>Recent Borrowers
                    </h6>
                    <span class="badge bg-primary rounded-pill">Today</span>
                </div>
                
                <div class="list-group list-group-flush">
                    
                    <div class="list-group-item py-3">
                        <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3">
                                    <i class="bi bi-person text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Juan Dela Cruz</h6>
                                    <small class="text-muted">ID: 2024-0192</small>
                                </div>
                            </div>
                            <small class="text-muted text-end">10 mins ago</small>
                        </div>
                        <div class="ms-5 ps-1">
                            <p class="mb-1 text-sm text-secondary">Borrowed: <span class="fw-semibold text-dark">Data Structures & Algorithms</span></p>
                            <small class="text-danger fw-semibold"><i class="bi bi-calendar-event me-1"></i>Due: Feb 21, 2026</small>
                        </div>
                    </div>

                    <div class="list-group-item py-3">
                        <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3">
                                    <i class="bi bi-person text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Maria Santos</h6>
                                    <small class="text-muted">ID: 2025-0844</small>
                                </div>
                            </div>
                            <small class="text-muted text-end">2 hours ago</small>
                        </div>
                        <div class="ms-5 ps-1">
                            <p class="mb-1 text-sm text-secondary">Borrowed: <span class="fw-semibold text-dark">Presidential Decree No. 1081</span></p>
                            <small class="text-danger fw-semibold"><i class="bi bi-calendar-event me-1"></i>Due: Feb 25, 2026</small>
                        </div>
                    </div>
                    
                    <div class="list-group-item py-3">
                        <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3">
                                    <i class="bi bi-person text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Pedro Reyes</h6>
                                    <small class="text-muted">ID: 2023-1102</small>
                                </div>
                            </div>
                            <small class="text-muted text-end">Yesterday</small>
                        </div>
                        <div class="ms-5 ps-1">
                            <p class="mb-1 text-sm text-secondary">Borrowed: <span class="fw-semibold text-dark">Modern PHP Programming</span></p>
                            <small class="text-danger fw-semibold"><i class="bi bi-calendar-event me-1"></i>Due: Feb 20, 2026</small>
                        </div>
                    </div>

                </div>
                
                <div class="card-footer bg-white text-center py-3">
                    <a href="/admin/transactions" class="text-decoration-none fw-semibold">View All Transactions</a>
                </div>
            </div>
        </div> </div>
</div>  
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // We only load the chart if the canvas exists on the page
    const canvas = document.getElementById('activityChart');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [
                    {
                        label: 'Books Added',
                        data: [5, 12, 8, 15],
                        borderColor: '#00c0ef', // Aqua to match the cards
                        backgroundColor: 'rgba(0, 192, 239, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Books Borrowed',
                        data: [25, 40, 35, 60],
                        borderColor: '#f39c12', // Orange to match the cards
                        backgroundColor: 'rgba(243, 156, 18, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>