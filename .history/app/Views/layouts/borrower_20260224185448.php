<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CALIS - Student Portal' ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
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

        .user-avatar { width: 35px; height: 35px; object-fit: cover; border-radius: 50%; border: 2px solid #e2e8f0; }

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
    </style>
</head>
<body>

<div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 9999;">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-lg d-flex align-items-center px-4 py-3" role="alert">
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
    <nav id="sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-book-half text-primary fs-3 me-2"></i>
                <h4 class="text-white m-0 fw-bold full-logo-text">CALIS</h4>
            </div>
            <button class="btn btn-sm text-white d-lg-none" id="closeSidebar"><i class="bi bi-x-lg fs-5"></i></button>
        </div>
        
        <div class="nav flex-column mt-3">
            <?php 
                $uri = uri_string(); 
                $navItems = [
                    ['url' => 'borrower/dashboard', 'icon' => 'bi-house-fill', 'text' => 'Home'],
                    ['url' => 'borrower/catalog', 'icon' => 'bi-search', 'text' => 'Catalog'],
                    ['url' => 'borrower/my-books', 'icon' => 'bi-journal-bookmark-fill', 'text' => 'My Books'],
                    ['url' => 'borrower/notificationlist', 'icon' => 'bi-bell-fill', 'text' => 'Notifications History'],
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
                <span class="text-secondary fw-bold d-none d-sm-block">STUDENT PORTAL</span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle p-2 position-relative shadow-sm" data-bs-toggle="dropdown" id="notifBell">
                        <i class="bi bi-bell text-secondary"></i>
                        <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                            <span id="badge-counter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                <?= $unreadCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0" style="width: 320px; max-height: 450px; overflow-y: auto;">
                        <li class="p-3 border-bottom"><h6 class="mb-0 fw-bold">Recent Notifications</h6></li>
                        <?php if (!empty($notifs)): ?>
                            <?php foreach ($notifs as $n): ?>
                                <li>
                                    <a class="dropdown-item py-2 border-bottom notif-item" href="<?= base_url('borrower/notificationlist') ?>" data-id="<?= $n['id'] ?>" data-status="<?= $n['status'] ?>">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2 mt-1">
                                                <i class="bi <?= ($n['type'] == 'alert') ? 'bi-exclamation-circle text-danger' : 'bi-info-circle text-info' ?>"></i>
                                            </div>
                                            <div class="text-wrap">
                                                <div class="small <?= ($n['status'] == 'unread') ? 'fw-bold text-dark' : 'text-muted' ?>"><?= esc($n['message']) ?></div>
                                                <small class="text-muted" style="font-size: 0.7rem;"><?= date('M d, g:i A', strtotime($n['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="p-4 text-center text-muted small">No notifications yet</li>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-center text-primary small fw-bold py-2" href="<?= base_url('borrower/notificationlist') ?>">See All</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" data-bs-toggle="dropdown">
                        <div class="text-end d-none d-md-block">
                            <span class="d-block fw-bold text-dark small" style="line-height: 1;"><?= session()->get('fullname') ?></span>
                            <small class="text-muted" style="font-size: 0.7rem;">Student Borrower</small>
                        </div>
                        <img src="<?= base_url('images/default-avatar.png') ?>" alt="User" class="user-avatar shadow-sm">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2">
                        <li><a class="dropdown-item rounded py-2 small" href="/borrower/profile"><i class="bi bi-person me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger rounded py-2 small" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="p-4">
            <?= $this->renderSection('content') ?>
        </div>
    </main> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

        // Handle Badge Clear on Dropdown Open
        const notifBell = document.getElementById('notifBell');
        if (notifBell) {
            notifBell.addEventListener('click', function() {
                const badge = document.getElementById('badge-counter');
                if (badge) {
                    badge.style.display = 'none';
                    fetch('<?= base_url('borrower/markNotificationsRead') ?>', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' }
                    });
                }
            });
        }
    });
</script>
</body>
</html>