<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CALIS v2.0 - Admin' ?></title>
    <link rel="icon" href="data:,">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body { overflow-x: hidden; background: #f4f6f9; }
        
        #sidebar {
            min-height: 100vh;
            background: #1f2937;
            color: white;
            transition: all 0.3s ease;
            z-index: 1040;
            overflow-x: hidden;
        }
        
        #sidebar a { 
            color: #cbd5e1; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            padding: 12px; 
            white-space: nowrap; 
        }
        #sidebar a:hover { background: #374151; color: white; border-radius: 4px; }
        
        #sidebar a i { font-size: 1.2rem; min-width: 30px; text-align: center; }
        
        #main-content { transition: all 0.3s ease; }
        .topbar { background: white; border-bottom: 1px solid #e3e6f0; padding: 10px 20px; }

        @media (min-width: 768px) {
            #sidebar { width: 250px; position: fixed; left: 0; top: 0; }
            

            #sidebar.collapsed { width: 70px; }
            #sidebar.collapsed .nav-text { display: none; } 
            #sidebar.collapsed .full-logo { display: none; } 
            #sidebar.collapsed .mini-logo { display: block !important; } 
            #sidebar.collapsed a i { margin-right: 0 !important; }
            
            #main-content { margin-left: 250px; width: calc(100% - 250px); }
            #main-content.expanded { margin-left: 70px; width: calc(100% - 70px); }
        }

        /* --- Mobile Behavior --- */
        @media (max-width: 767.98px) {
            #sidebar { width: 250px; position: fixed; left: -250px; top: 0; }
            #sidebar.show { left: 0; }
            #main-content { width: 100%; margin-left: 0 !important; }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0,0,0,0.5);
                z-index: 1030;
            }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>

<div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 9999;">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-lg border-0 d-flex align-items-center px-4 py-3" role="alert" style="min-width: 300px;">
            <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
            <div>
                <strong class="d-block mb-0 text-dark">Success!</strong>
                <span class="text-muted small"><?= session()->getFlashdata('success') ?></span>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-lg border-0 d-flex align-items-center px-4 py-3" role="alert" style="min-width: 300px;">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-3"></i>
            <div>
                <strong class="d-block mb-0 text-dark">Oops! Something went wrong.</strong>
                <span class="text-muted small">Please check your inputs and try again.</span>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="container-fluid p-0">
    
    <nav id="sidebar" class="p-3 shadow">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white m-0 full-logo fw-bold">CALIS v2.0</h4>
            <h4 class="text-white m-0 mini-logo fw-bold d-none text-center w-100">C</h4>
            
            <button class="btn btn-sm text-white d-md-none" id="closeSidebar">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>
        <hr class="text-secondary">
        
        <a href="/admin/dashboard" title="Dashboard"><i class="bi bi-speedometer2 me-2"></i> <span class="nav-text">Dashboard</span></a>
        <a href="/admin/collections" title="Collections"><i class="bi bi-book me-2"></i> <span class="nav-text">Collections</span></a>
        <a href="/admin/journals" title="Journals"><i class="bi bi-journals me-2"></i> <span class="nav-text">Journals</span></a>
        <a href="/admin/notifications" title="Notifications"><i class="bi bi-bell me-2"></i> <span class="nav-text">Notifications</span></a>
        <a href="/admin/reports" title="Reports"><i class="bi bi-file-earmark-bar-graph me-2"></i> <span class="nav-text">Request & Reports</span></a>
        <a href="/admin/transactions" title="Transactions"><i class="bi bi-arrow-left-right me-2"></i> <span class="nav-text">Transactions</span></a>
        <a href="/admin/logs" title="Logs"><i class="bi bi-clock-history me-2"></i> <span class="nav-text">Logs</span></a>
        <a href="/admin/users" title="Users"><i class="bi bi-people me-2"></i> <span class="nav-text">Users</span></a>
    </nav>

    <main id="main-content">
        <header class="topbar d-flex justify-content-between align-items-center sticky-top shadow-sm">
            
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 border shadow-sm" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="navbar-brand mb-0 h5 d-none d-sm-block text-secondary fw-bold">Commission on Appointments - CALIS</span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                
                <button class="btn btn-light btn-sm border shadow-sm" id="darkModeToggle" title="Toggle Theme">
                    <i class="bi bi-moon-stars-fill text-secondary" id="themeIcon"></i>
                </button>
                
            <div class="dropdown">
                <button class="btn btn-light btn-sm border shadow-sm position-relative" type="button" data-bs-toggle="dropdown" id="adminBell">
                    <i class="bi bi-bell text-secondary"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="admin-badge" style="font-size: 0.6rem;">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 320px;">
                    <li><h6 class="dropdown-header text-dark fw-bold">Admin Notifications</h6></li>
                    
                    <?php if (!empty($notifs)): ?>
                        <?php foreach ($notifs as $n): ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-start py-2 border-bottom" href="/admin/notifications">
                                    <div class="rounded-circle d-flex justify-content-center align-items-center me-3 mt-1 
                                        <?= $n['type'] == 'alert' ? 'bg-danger' : 'bg-info' ?> text-white" style="width: 30px; height: 30px; min-width:30px;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div class="text-wrap">
                                        <span class="d-block small <?= $n['status'] == 'unread' ? 'fw-bold text-dark' : 'text-muted' ?>">
                                            <?= esc($n['message']) ?>
                                        </span>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?= date('M d, h:i A', strtotime($n['created_at'])) ?></small>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="px-3 py-4 text-center text-muted small">No admin alerts</li>
                    <?php endif; ?>
                    
                    <li><a class="dropdown-item text-center text-primary small fw-semibold" href="/admin/notifications">View Notification Center</a></li>
                </ul>
            </div>

<span class="d-none d-sm-block fw-semibold text-dark"><?= session()->get('fullname') ?></span>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= base_url('images/default-avatar.png') ?>" 
                             alt="Admin" 
                             class="rounded-circle border border-2 border-white shadow-sm" 
                             style="width: 35px; height: 35px; min-width: 35px; min-height: 35px; object-fit: cover; background-color: #e9ecef;">
                        <span class="d-none d-sm-block fw-semibold text-dark">Bien</span>
                    </a>        

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li>
                            <a class="dropdown-item fw-semibold text-secondary py-2" href="/admin/profile">
                                <i class="bi bi-person-gear me-2"></i>Profile Settings
                            </a>
                        </li>            
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-semibold py-2" href="/">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </header>

        <div class="p-4">
            <?= $this->renderSection('content') ?>
        </div>
    </main> 
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const overlay = document.getElementById('sidebarOverlay');
        
        // Hamburger Click Event
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            if (window.innerWidth >= 768) {
                // On Desktop
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            } else {
                // On Mobile
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        });

        function closeMobileSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
        
        overlay.addEventListener('click', closeMobileSidebar);
        document.getElementById('closeSidebar').addEventListener('click', closeMobileSidebar);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById('darkModeToggle');
        const themeIcon = document.getElementById('themeIcon');

        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('bg-dark');
            document.body.classList.toggle('text-white');

            if (themeIcon.classList.contains('bi-moon-stars-fill')) {
                themeIcon.classList.replace('bi-moon-stars-fill', 'bi-brightness-high-fill');
                themeIcon.classList.replace('text-secondary', 'text-warning');
            } else {
                themeIcon.classList.replace('bi-brightness-high-fill', 'bi-moon-stars-fill');
                themeIcon.classList.replace('text-warning', 'text-secondary');
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toastAlerts = document.querySelectorAll('#toastContainer .alert');
        if (toastAlerts.length > 0) {
            setTimeout(() => {
                toastAlerts.forEach(alertEl => {
                    const bsAlert = new bootstrap.Alert(alertEl);
                    bsAlert.close();
                });
            }, 4000);
        }
    });
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>