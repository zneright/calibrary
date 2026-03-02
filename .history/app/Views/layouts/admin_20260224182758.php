<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CALIS v2.0 - Admin' ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body { overflow-x: hidden; background: #f4f6f9; }
        #sidebar { min-height: 100vh; background: #1f2937; color: white; transition: all 0.3s ease; z-index: 1040; overflow-x: hidden; }
        #sidebar a { color: #cbd5e1; text-decoration: none; display: flex; align-items: center; padding: 12px; white-space: nowrap; }
        #sidebar a:hover { background: #374151; color: white; border-radius: 4px; }
        #sidebar a i { font-size: 1.2rem; min-width: 30px; text-align: center; }
        #main-content { transition: all 0.3s ease; }
        .topbar { background: white; border-bottom: 1px solid #e3e6f0; padding: 10px 20px; }
        
        @media (min-width: 768px) {
            #sidebar { width: 250px; position: fixed; left: 0; top: 0; }
            #sidebar.collapsed { width: 70px; }
            #sidebar.collapsed .nav-text, #sidebar.collapsed .full-logo { display: none; } 
            #sidebar.collapsed .mini-logo { display: block !important; } 
            #main-content { margin-left: 250px; width: calc(100% - 250px); }
            #main-content.expanded { margin-left: 70px; width: calc(100% - 70px); }
        }
        @media (max-width: 767.98px) {
            #sidebar { width: 250px; position: fixed; left: -250px; top: 0; }
            #sidebar.show { left: 0; }
            #main-content { width: 100%; margin-left: 0 !important; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1030; }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>

<div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 9999;">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-lg border-0 d-flex align-items-center px-4 py-3" role="alert">
            <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
            <div>
                <strong class="d-block mb-0 text-dark">Success!</strong>
                <span class="text-muted small"><?= session()->getFlashdata('success') ?></span>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="container-fluid p-0">
    <nav id="sidebar" class="p-3 shadow">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white m-0 full-logo fw-bold">CALIS v2.0</h4>
            <h4 class="text-white m-0 mini-logo fw-bold d-none text-center w-100">C</h4>
            <button class="btn btn-sm text-white d-md-none" id="closeSidebar"><i class="bi bi-x-lg fs-5"></i></button>
        </div>
        <hr class="text-secondary">
        <a href="/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i> <span class="nav-text">Dashboard</span></a>
        <a href="/admin/collections"><i class="bi bi-book me-2"></i> <span class="nav-text">Collections</span></a>
        <a href="/admin/journals"><i class="bi bi-journals me-2"></i> <span class="nav-text">Journals</span></a>
        <a href="/admin/notificationlist"><i class="bi bi-bell me-2"></i> <span class="nav-text">Notifications</span></a>
        <a href="/admin/reports"><i class="bi bi-file-earmark-bar-graph me-2"></i> <span class="nav-text">Requests</span></a>
        <a href="/admin/transactions"><i class="bi bi-arrow-left-right me-2"></i> <span class="nav-text">Transactions</span></a>
        <a href="/admin/logs"><i class="bi bi-clock-history me-2"></i> <span class="nav-text">Logs</span></a>
        <a href="/admin/users"><i class="bi bi-people me-2"></i> <span class="nav-text">Users</span></a>
    </nav>

    <main id="main-content">
        <header class="topbar d-flex justify-content-between align-items-center sticky-top shadow-sm">
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 border shadow-sm" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
                <span class="navbar-brand mb-0 h5 d-none d-sm-block text-secondary fw-bold">ADMIN PORTAL</span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" data-bs-toggle="dropdown">
                        <img src="<?= base_url('images/default-avatar.png') ?>" alt="Profile" class="rounded-circle border border-2 border-white shadow-sm" style="width: 35px; height: 35px; object-fit: cover;">
                        <span class="d-none d-sm-block fw-semibold text-dark"><?= session()->get('fullname') ?? 'Administrator' ?></span>
                    </a>        
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li><a class="dropdown-item fw-semibold text-secondary py-2" href="/admin/profile"><i class="bi bi-person-gear me-2"></i>Profile Settings</a></li>             
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger fw-semibold py-2" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const overlay = document.getElementById('sidebarOverlay');
        
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            } else {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>