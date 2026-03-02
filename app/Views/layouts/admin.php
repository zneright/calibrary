<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CALIS v2.0 - Admin' ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --accent-color: #3b82f6;
            --text-muted: #94a3b8;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }

        body { 
            font-family: 'Inter', sans-serif;
            background: #f8fafc; 
            overflow-x: hidden; 
        }
        
        /* --- Sidebar --- */
        #sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            transition: width var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1), left var(--transition-speed);
            z-index: 1040;
            position: fixed;
            width: var(--sidebar-width);
            left: 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }

        #sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        #sidebar.collapsed .nav-text, #sidebar.collapsed .full-logo-text { display: none !important; }
        #sidebar.collapsed .sidebar-header { justify-content: center !important; padding: 0; }
        #sidebar.collapsed .nav-link { justify-content: center; margin: 4px 10px; padding: 12px 0; }
        #sidebar.collapsed .nav-link i { margin-right: 0; font-size: 1.4rem; }

        .sidebar-header {
            padding: 1.5rem;
            height: 70px;
            display: flex;
            align-items: center;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
            transition: all var(--transition-speed);
        }

        #sidebar .nav-link { 
            color: var(--text-muted); 
            padding: 12px 20px; 
            margin: 4px 15px;
            border-radius: 8px;
            display: flex; 
            align-items: center; 
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        #sidebar .nav-link:hover { background: var(--sidebar-hover); color: white; }
        #sidebar .nav-link.active { 
            background: var(--accent-color); 
            color: white; 
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }
        
        #sidebar .nav-link i { font-size: 1.2rem; margin-right: 12px; min-width: 25px; text-align: center; }
        
        /* --- Main Content Area --- */
        #main-content { 
            transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1), width var(--transition-speed); 
            margin-left: var(--sidebar-width); 
            width: calc(100% - var(--sidebar-width)); 
            min-height: 100vh;
        }

        #main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        .topbar { 
            background: white; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 0 24px; 
            height: 70px;
        }

        .topbar-brand { font-size: 1.1rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        .user-avatar { width: 38px; height: 38px; object-fit: cover; border: 2px solid #e2e8f0; }

        /* --- Mobile --- */
        @media (max-width: 991.98px) {
            #sidebar { left: calc(var(--sidebar-width) * -1); }
            #sidebar.show { left: 0; }
            #main-content { margin-left: 0 !important; width: 100% !important; }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.5);
                z-index: 1030;
                backdrop-filter: blur(4px);
            }
            .sidebar-overlay.show { display: block; }
        }

        .alert-toast {
            position: relative;
            overflow: hidden !important; 
        }
        .toast-progress {
            position: absolute;
            bottom: 0; /* Moved to the bottom edge */
            left: 0;
            height: 5px; /* Thicker line */
            background-color: rgba(0, 0, 0, 0.25); /* Darker semi-transparent line */
            animation: toastTimer 5s linear forwards;
        }
        @keyframes toastTimer {
            0% { width: 100%; }
            100% { width: 0%; }
        }
    </style>
</head>
<body>

<div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 9999;">
    
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-lg border-0 d-flex align-items-center px-4 py-3 alert-toast" role="alert" style="min-width: 300px;">
            <div class="toast-progress"></div>
            
            <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
            <div>
                <strong class="d-block mb-0 text-dark">Success!</strong>
                <span class="text-muted small"><?= session()->getFlashdata('success') ?></span>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-lg border-0 d-flex align-items-center px-4 py-3 alert-toast" role="alert" style="min-width: 300px;">
            <div class="toast-progress"></div>
            
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

<div class="container-fluid p-0 d-flex">
    <nav id="sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
<img src="<?= base_url('images/logo.png') ?>" alt="CALIS Logo" width="35" height="35" class="me-2" style="object-fit: contain;">                <h4 class="text-white m-0 fw-bold full-logo-text">CALIS</h4>
            </div>
            <button class="btn btn-sm text-white d-lg-none" id="closeSidebar"><i class="bi bi-x-lg fs-5"></i></button>
        </div>
        
        <div class="nav flex-column mt-3">
            <?php 
                $uri = uri_string(); 
                $navItems = [
                    ['url' => 'admin/dashboard', 'icon' => 'bi-grid-1x2-fill', 'text' => 'Dashboard'],
                    ['url' => 'admin/collections', 'icon' => 'bi-collection-fill', 'text' => 'Collections'],
                    ['url' => 'admin/journals', 'icon' => 'bi-bookmark-star-fill', 'text' => 'Journals'],
                    ['url' => 'admin/notifications', 'icon' => 'bi-bell-fill', 'text' => 'Notifications'],
                    ['url' => 'admin/reports', 'icon' => 'bi-file-earmark-bar-graph-fill', 'text' => 'Reports'],
                    ['url' => 'admin/transactions', 'icon' => 'bi-arrow-left-right', 'text' => 'Transactions'],
                    ['url' => 'admin/logs', 'icon' => 'bi-clock-history', 'text' => 'System Logs'],
                    ['url' => 'admin/users', 'icon' => 'bi-people-fill', 'text' => 'Users'],
                ];
            ?>
            <?php foreach ($navItems as $item): ?>
                <a href="<?= base_url($item['url']) ?>" class="nav-link <?= (strpos($uri, $item['url']) !== false) ? 'active' : '' ?>">
                    <i class="bi <?= $item['icon'] ?>"></i> <span class="nav-text"><?= $item['text'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <main id="main-content">
        <header class="topbar d-flex justify-content-between align-items-center sticky-top shadow-sm">
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 border-0" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <span class="topbar-brand d-none d-sm-block">Library Admin Portal</span>
            </div>
            
       <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" data-bs-toggle="dropdown">
                <div class="text-end d-none d-md-block">
                    <span class="d-block fw-bold text-dark small" style="line-height: 1;">
                        <?= esc(session()->get('fullname')) ?>
                    </span>
                    <small class="text-muted" style="font-size: 0.7rem;"><?= esc(session()->get('role')) ?></small>
                </div>
                
                <?php 
                    $sessionAvatar = session()->get('avatar');
                    // Check physical path relative to FCPATH (public folder)
                    if ($sessionAvatar && file_exists(FCPATH . 'uploads/avatars/' . $sessionAvatar)) {
                        $userImage = base_url('uploads/avatars/' . $sessionAvatar);
                    } else {
                        $userImage = base_url('images/default-avatar.png');
                    }
                ?>
                <img src="<?= $userImage ?>" alt="User Avatar" class="rounded-circle user-avatar shadow-sm" style="width: 38px; height: 38px; object-fit: cover; border: 2px solid #e2e8f0;">
            </a>        
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2">
                <li><a class="dropdown-item rounded py-2" href="<?= base_url('admin/profile') ?>"><i class="bi bi-person-gear me-2"></i>Profile Settings</a></li>             
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger rounded py-2" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
            </ul>
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
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('closeSidebar');
        
        function toggleSidebar() {
            if (window.innerWidth >= 992) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            } else {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        }

        if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if(overlay) overlay.addEventListener('click', toggleSidebar);
        if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        const toastAlerts = document.querySelectorAll('#toastContainer .alert');
        if (toastAlerts.length > 0) {
            // Changed to 5000 milliseconds (5 seconds)
            setTimeout(() => {
                toastAlerts.forEach(alertEl => {
                    const bsAlert = new bootstrap.Alert(alertEl);
                    bsAlert.close();
                });
            }, 5000); 
        }
    });
</script>

<?= $this->renderSection('scripts') ?>

</body>
</html>