<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CALIS - Student Portal' ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .nav-link {
            font-weight: 500;
            color: #495057;
            padding: 0.8rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #f1f3f5;
            color: #0d6efd;
        }
        
        .page-header {
            background: linear-gradient(135deg, #1a2942 0%, #0f172a 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 1.5rem 1.5rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.2);
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
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="/borrower/dashboard">
            <i class="bi bi-book-half fs-3 me-2"></i> CALIS
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#borrowerNav">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="borrowerNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                <li class="nav-item">
                    <a class="nav-link active" href="/borrower/dashboard"><i class="bi bi-house me-1"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/borrower/catalog"><i class="bi bi-search me-1"></i> Catalog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/borrower/my-books"><i class="bi bi-journal-bookmark me-1"></i> My Books</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle p-2 position-relative shadow-sm" data-bs-toggle="dropdown">
                        <i class="bi bi-bell text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">1</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item small py-2" href="#"><i class="bi bi-exclamation-circle text-warning me-2"></i> Book due tomorrow!</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 border rounded-pill shadow-sm bg-white" href="#" data-bs-toggle="dropdown">
                        <img src="<?= base_url('images/default-avatar.png') ?>" alt="User" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                        <span class="d-none d-sm-block fw-semibold text-dark pe-2">Nishia Pinlac</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li><a class="dropdown-item py-2" href="/borrower/profile"><i class="bi bi-person me-2"></i> My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger py-2" href="/"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<main>
    <?= $this->renderSection('content') ?>
</main>

<footer class="text-center py-4 mt-5 text-muted small">
    &copy; <?= date('Y') ?> Commission on Appointments Library System
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toaster 
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(() => {
            document.querySelectorAll('#toastContainer .alert').forEach(alertEl => new bootstrap.Alert(alertEl).close());
        }, 4000);
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>