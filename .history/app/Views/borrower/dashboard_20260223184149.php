<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Dashboard - CALIS v2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .navbar { background-color: #0d6efd; }
        .welcome-card {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            border: none;
            border-radius: 12px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-book-half me-2"></i>CALIS v2.0</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="/borrower/dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/borrower/catalog">Library Catalog</a></li>
                <li class="nav-item"><a class="nav-link" href="/borrower/my-books">My Borrowed Books</a></li>
                
                <li class="nav-item dropdown ms-3">
                    <a class="nav-link dropdown-toggle btn btn-light text-primary px-3 fw-semibold rounded-pill" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> <?= esc(session()->get('fullname')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="/borrower/profile"><i class="bi bi-person-gear me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/login"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card welcome-card shadow mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">Welcome back, <?= esc(session()->get('fullname')) ?>!</h3>
                <p class="mb-0 text-light opacity-75">
                    <i class="bi bi-person-badge me-1"></i> ID: <?= esc(session()->get('user_id')) ?> | 
                    <i class="bi bi-shield-check ms-2 me-1"></i> Role: <?= esc(session()->get('role')) ?>
                </p>
            </div>
            <div class="d-none d-md-block">
                <i class="bi bi-book text-white opacity-25" style="font-size: 4rem;"></i>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3 text-center">
                <i class="bi bi-journal-bookmark text-primary mb-2" style="font-size: 2.5rem;"></i>
                <h5 class="fw-bold">0</h5>
                <p class="text-muted small mb-0">Currently Borrowed</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3 text-center">
                <i class="bi bi-exclamation-octagon text-danger mb-2" style="font-size: 2.5rem;"></i>
                <h5 class="fw-bold">0</h5>
                <p class="text-muted small mb-0">Overdue Items</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3 text-center">
                <i class="bi bi-clock-history text-warning mb-2" style="font-size: 2.5rem;"></i>
                <h5 class="fw-bold">0</h5>
                <p class="text-muted small mb-0">Pending Requests</p>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>