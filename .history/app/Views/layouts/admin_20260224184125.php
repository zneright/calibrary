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
            --primary-accent: #3b82f6;
            --topbar-height: 70px;
        }

        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Sidebar Styling */
        #sidebar { 
            min-height: 100vh; 
            background: var(--sidebar-bg); 
            color: #f1f5f9; 
            transition: all 0.3s ease; 
            z-index: 1040; 
            position: fixed; 
            width: 260px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.25rem;
            letter-spacing: 1px;
            background: rgba(0,0,0,0.1);
        }

        #sidebar .nav-link { 
            color: #94a3b8; 
            padding: 12px 20px; 
            margin: 4px 12px;
            border-radius: 8px;
            display: flex; 
            align-items: center; 
            font-weight: 500;
            transition: all 0.2s;
        }

        #sidebar .nav-link:hover { 
            background: var(--sidebar-hover); 
            color: white; 
        }

        #sidebar .nav-link.active { 
            background: var(--primary-accent); 
            color: white; 
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        #sidebar .nav-link i { 
            font-size: 1.2rem; 
            margin-right: 12px;
        }

        /* Content Area */
        #main-content { 
            transition: all 0.3s ease; 
            margin-left: 260px; 
            width: calc(100% - 260px); 
            min-height: 100vh;
        }

        .topbar { 
            height: var(--topbar-height);
            background: white; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 0 1.5rem; 
        }

        .breadcrumb-text {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
        }

        /* Mobile Adjustments */
        @media (max-width: 991.98px) {
            #sidebar { left: -260px; }
            #sidebar.show { left: 0; }
            #main-content { width: 100%; margin-left: 0; }
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

<div id="sidebarOverlay" class="sidebar-overlay"></div>

<div class="container-fluid p-0 d-flex">
    <nav id="sidebar" class="shadow-lg">
        <div class="sidebar-brand mb-3">
            <span class="fw-bold text-white"><i class="bi bi-book-half me-2"></i>CALIS <span class="text-primary">v2.0</span></span>
        </div>
        
        <div class="nav flex-column">
            <a href="/admin/dashboard" class="nav-link <?= current_url() == base_url('admin/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="/admin/collections" class="nav-link <?= strpos(current_url(), 'collections') ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Collections
            </a>
            <a href="/admin/journals" class="nav-link <?= strpos(current_url(), 'journals') ? 'active' : '' ?>">
                <i class="bi bi-bookmark-star"></i> Journals
            </a>
            <a href="/admin/notifications" class="nav-link <?= strpos(current_url(), 'notifications') ? 'active' : '' ?>">
                <i class="bi bi-send"></i> Send Notifs
            </a>
            <a href="/admin/reports" class="nav-link <?= strpos(current_url(), 'reports') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph"></i> Reports
            </a>
            <a href="/admin/transactions" class="nav-link <?= strpos(current_url(), 'transactions') ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-right"></i> Transactions
            </a>
            <a href="/admin/logs" class="nav-link <?= strpos(current_url(), 'logs') ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> System Logs
            </a>
            <a href="/admin/users" class="nav-link <?= strpos(current_url(), 'users') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Manage Users
            </a>
        </div>
    </nav>

    <main id="main-content">
        <header class="topbar d-flex justify-content-between align-items-center sticky-top">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark d-lg-none me-2" id="sidebarToggle">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <div class="breadcrumb-text d-none d-sm-block">
                    <i class="bi bi-house-door me-1"></i> Admin / <span class="text-dark fw-bold"><?= $title ?? 'Dashboard' ?></span>
                </div>
            </div>
            
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" data-bs-toggle="dropdown">
                    <div class="text-end d-none d-md-block">
                        <small class="d-block text-muted" style="font-size: 0.7rem;">Logged in as</small>
                        <span class="fw-semibold text-dark small"><?= session()->get('fullname') ?? 'Administrator' ?></span>
                    </div>
                    <img src="<?= base_url('images/default-avatar.png') ?>" alt="Profile" class="avatar-circle">
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" style="min-width: 200px;">
                    <li><h6 class="dropdown-header">User Options</h6></li>
                    <li><a class="dropdown-item rounded py-2" href="/admin/profile"><i class="bi bi-person-gear me-2"></i>My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger rounded py-2" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </header>

        <div class="p-4">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main> 
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');
        
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
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