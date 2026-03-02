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
            --sidebar-bg: #1e293b; /* Darker slate */
            --sidebar-hover: #334155;
            --accent-color: #3b82f6; /* Modern blue */
            --text-muted: #94a3b8;
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
            transition: all 0.3s ease;
            z-index: 1040;
            position: fixed;
            width: 260px;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            font-size: 1.25rem;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.05);
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
        }

        #sidebar .nav-link:hover { 
            background: var(--sidebar-hover); 
            color: white; 
        }

        #sidebar .nav-link.active { 
            background: var(--accent-color); 
            color: white; 
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }
        
        #sidebar .nav-link i { font-size: 1.2rem; margin-right: 12px; width: 20px; text-align: center; }
        
        /* --- Main Content --- */
        #main-content { 
            transition: all 0.3s ease; 
            margin-left: 260px; 
            width: calc(100% - 260px); 
        }

        .topbar { 
            background: white; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 12px 24px; 
            height: 70px;
        }

        .topbar-brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        /* --- Mobile Responsiveness --- */
        @media (max-width: 991.98px) {
            #sidebar { left: -260px; }
            #sidebar.show { left: 0; }
            #main-content { margin-left: 0; width: 100%; }
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
    </style>
</head>
<body>

<div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 9999;">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow border-0 d-flex align-items-center px-4 py-3" role="alert">
            <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
            <div>
                <strong class="d-block text-dark">Success</strong>
                <span class="text-muted small"><?= session()->getFlashdata('success') ?></span>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="container-fluid p-0 d-flex">
    <nav id="sidebar" class="shadow">
        <div class="sidebar-header d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white m-0 fw-bold"><i class="bi bi-book-half me-2"></i>CALIS</h4>
            <button class="btn btn-sm text-white d-lg-none" id="closeSidebar"><i class="bi bi-x-lg fs-5"></i></button>
        </div>
        
        <div class="nav flex-column">
            <a href="/admin/dashboard" class="nav-link <?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i> <span class="nav-text">Dashboard</span>
            </a>
            <a href="/admin/collections" class="nav-link <?= (strpos(uri_string(), 'collections') !== false) ? 'active' : '' ?>">
                <i class="bi bi-collection-fill"></i> <span class="nav-text">Collections</span>
            </a>
            <a href="/admin/journals" class="nav-link <?= (strpos(uri_string(), 'journals') !== false) ? 'active' : '' ?>">
                <i class="bi bi-bookmark-star-fill"></i> <span class="nav-text">Journals</span>
            </a>
            <a href="/admin/reports" class="nav-link <?= (strpos(uri_string(), 'reports') !== false) ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> <span class="nav-text">Reports</span>
            </a>
            <a href="/admin/transactions" class="nav-link <?= (strpos(uri_string(), 'transactions') !== false) ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-right"></i> <span class="nav-text">Transactions</span>
            </a>
            <a href="/admin/logs" class="nav-link <?= (strpos(uri_string(), 'logs') !== false) ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> <span class="nav-text">System Logs</span>
            </a>
            <a href="/admin/users" class="nav-link <?= (strpos(uri_string(), 'users') !== false) ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> <span class="nav-text">Users</span>
            </a>
        </div>
    </nav>

    <main id="main-content">
        <header class="topbar d-flex justify-content-between align-items-center sticky-top shadow-sm">
            <div class="d-flex align-items-center">
                <button class="btn btn-light me-3 border-0 d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <span class="topbar-brand d-none d-sm-block">Admin Portal</span>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-md-block">
                            <span class="d-block fw-bold text-dark small" style="line-height: 1;"><?= session()->get('fullname') ?? 'Admin User' ?></span>
                            <small class="text-muted" style="font-size: 0.7rem;">System Administrator</small>
                        </div>
                        <img src="<?= base_url('images/default-avatar.png') ?>" alt="Admin" class="rounded-circle user-avatar shadow-sm">
                    </a>        

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" style="min-width: 200px;">
                        <li><a class="dropdown-item rounded py-2 fw-medium" href="/admin/profile"><i class="bi bi-person-gear me-2"></i>Account Settings</a></li>            
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger rounded py-2 fw-medium" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
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
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('closeSidebar');
        
        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if(overlay) overlay.addEventListener('click', toggleSidebar);
        if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
    });
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>