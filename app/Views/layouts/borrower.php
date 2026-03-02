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
        body { 
            background-color: #f8fafc; 
            font-family: 'Inter', sans-serif; 
            color: #1e293b;
        }
        
        .navbar-custom { 
            background-color: #ffffff; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1); 
            border-bottom: 1px solid #e2e8f0;
        }

        .navbar-brand {
            letter-spacing: -0.5px;
        }

        .nav-link { 
            font-weight: 500; 
            color: #64748b; 
            padding: 0.7rem 1.2rem !important; 
            border-radius: 0.6rem; 
            transition: all 0.2s ease; 
            font-size: 0.95rem;
        }

        .nav-link:hover { 
            background-color: #f1f5f9; 
            color: #3b82f6; 
        }

        .nav-link.active { 
            background-color: #eff6ff; 
            color: #2563eb !important; 
        }

        /* Beautified Dropdown */
        .dropdown-menu {
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
            transition: background 0.2s;
        }

        /* Profile Pill */
        .profile-pill {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 4px 12px 4px 4px;
            border-radius: 50px;
            transition: all 0.2s;
        }

        .profile-pill:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .notif-item {
            border-radius: 0.5rem;
            margin-bottom: 2px;
        }

        .notif-item:last-child {
            border-bottom: none !important;
        }

        main {
            min-height: 80vh;
            padding-top: 2rem;
        }

        footer {
            border-top: 1px solid #e2e8f0;
            background: white;
        }

        /* Toaster Progress Bar Animation */
        .alert-toast {
            position: relative;
            overflow: hidden !important; 
        }
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 5px; 
            background-color: rgba(0, 0, 0, 0.25); 
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

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-lg border-0 d-flex align-items-center px-4 py-3 alert-toast" role="alert" style="min-width: 300px;">
                <div class="toast-progress"></div>
                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-3"></i>
                <div>
                    <strong class="d-block mb-0 text-dark">Oops!</strong>
                    <span class="text-muted small"><?= session()->getFlashdata('error') ?></span>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary fs-4" href="/borrower/dashboard">
            <img src="<?= base_url('images/logo.png') ?>" alt="CALIS Logo" width="35" height="35" class="me-2" style="object-fit: contain;">
            CALIS
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#borrowerNav">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="borrowerNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-2">
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'borrower/dashboard') ? 'active' : '' ?>" href="/borrower/dashboard">
                        <i class="bi bi-house-door me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'borrower/catalog') ? 'active' : '' ?>" href="/borrower/catalog">
                        <i class="bi bi-compass me-1"></i> Catalog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'borrower/my-books') ? 'active' : '' ?>" href="/borrower/my-books">
                        <i class="bi bi-journal-bookmark me-1"></i> My Books
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <div class="dropdown" id="notificationDropdown">
                    <button class="btn btn-light rounded-circle p-2 position-relative shadow-sm border" data-bs-toggle="dropdown" id="bellButton">
                        <i class="bi bi-bell text-secondary"></i>
                        <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                            <span id="badge-counter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white border-2" style="font-size: 0.6rem; padding: 0.35em 0.5em;">
                                <?= $unreadCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-3 border-0" style="width: 320px; max-height: 450px; overflow-y: auto;">
                        <li class="px-3 py-2 border-bottom"><h6 class="mb-0 fw-bold small text-uppercase text-muted">Notifications</h6></li>
                        
                        <?php if (!empty($notifs)): ?>
                            <?php foreach ($notifs as $n): ?>
                              <li>
                                    <a class="dropdown-item py-3 border-bottom notif-item" 
                                    href="<?= base_url('borrower/notificationlist') ?>" 
                                    data-id="<?= $n['id'] ?>" 
                                    data-status="<?= $n['status'] ?>">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3 mt-1">
                                                <i class="bi <?= ($n['type'] == 'alert') ? 'bi-exclamation-circle-fill text-danger' : (($n['type'] == 'warning') ? 'bi-exclamation-triangle-fill text-warning' : 'bi-info-circle-fill text-info') ?> fs-5"></i>
                                            </div>
                                            <div class="text-wrap">
                                                <div class="small <?= ($n['status'] == 'unread') ? 'fw-bold text-dark' : 'text-muted' ?>">
                                                    <?= esc($n['message']) ?>
                                                </div>
                                                <small class="text-muted opacity-75" style="font-size: 0.75rem;"><?= date('M d, g:i A', strtotime($n['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="px-3 py-5 text-center text-muted">
                                <i class="bi bi-bell-slash d-block fs-2 mb-2 opacity-25"></i>
                                <small>All caught up!</small>
                            </li>
                        <?php endif; ?>
                        <li class="text-center pt-2"><a href="<?= base_url('borrower/notificationlist') ?>" class="text-primary small text-decoration-none fw-bold">View History</a></li>
                    </ul>
                </div>

            <div class="dropdown">
                <a class="nav-link dropdown-toggle profile-pill d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                    <?php 
                        // ALWAYS FETCH FRESH DATA FROM DB TO ENSURE AVATAR IS ACCURATE
                        $userModel = new \App\Models\UserModel();
                        $currentUser = $userModel->find(session()->get('id'));
                        $dbAvatar = $currentUser['avatar'] ?? null;
                        
                        if ($dbAvatar && file_exists(FCPATH . 'uploads/avatars/' . $dbAvatar)) {
                            $userImage = base_url('uploads/avatars/' . $dbAvatar);
                        } else {
                            // FALLBACK: Generates an image with the user's initials!
                            $userImage = 'https://ui-avatars.com/api/?name=' . urlencode(session()->get('fullname')) . '&background=e2e8f0&color=1e3a8a&bold=true';
                        }
                    ?>
                    <img src="<?= $userImage ?>" alt="User" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #e2e8f0;">
                    <span class="d-none d-sm-block fw-semibold text-dark pe-1 small">
                        <?= esc(session()->get('fullname')) ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-3 border-0">
                    <li><a class="dropdown-item" href="<?= base_url('borrower/profile') ?>"><i class="bi bi-person me-2 text-muted"></i> My Profile</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</nav>

<main>
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>

<footer class="text-center py-5 mt-5 text-muted">
    <div class="container small">
        &copy; <?= date('Y') ?> Commission on Appointments <span class="mx-1">•</span> CALIS v2.0
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mark as Read and Badge Reset Logic
    const notifDropdown = document.getElementById('notificationDropdown');
    if (notifDropdown) {
        notifDropdown.addEventListener('show.bs.dropdown', function () {
            const badge = document.getElementById('badge-counter');
            if (badge && badge.style.display !== 'none') {
                // 1. Visually hide immediately
                badge.style.display = 'none';

                // 2. AJAX to Mark Read
                fetch('<?= base_url('borrower/markNotificationsRead') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                })
                .then(response => response.json())
                .then(data => console.log('Notifications marked read'))
                .catch(err => console.error('Error:', err));
            }
        });
    }

    // Auto-dismiss Toasts
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(() => {
            document.querySelectorAll('#toastContainer .alert').forEach(alertEl => {
                const alert = new bootstrap.Alert(alertEl);
                alert.close();
            });
        }, 4000);
    });

    document.querySelectorAll('.notif-item').forEach(item => {
        item.addEventListener('click', function(e) {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            const badge = document.getElementById('badge-counter');

            if (status === 'unread') {
                // Visually decrement the badge count
                if (badge) {
                    let currentCount = parseInt(badge.innerText);
                    if (currentCount > 1) {
                        badge.innerText = currentCount - 1;
                    } else {
                        badge.remove();
                    }
                }

                // AJAX call to mark this specific notification as read
                fetch('<?= base_url('borrower/markSingleRead') ?>/' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                });
            }
        });
    });
</script>

<?= $this->renderSection('scripts') ?>
<script>
        document.addEventListener("DOMContentLoaded", function() {
            const toastAlerts = document.querySelectorAll('#toastContainer .alert');
            if (toastAlerts.length > 0) {
                setTimeout(() => {
                    toastAlerts.forEach(alertEl => {
                        const bsAlert = new bootstrap.Alert(alertEl);
                        bsAlert.close();
                    });
                }, 5000); 
            }
        });
    </script>

</body>
</html>