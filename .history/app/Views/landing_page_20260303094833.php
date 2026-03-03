<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CALIS - Public Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #ffffff; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        .ca-header {
            border-bottom: 4px solid #b91c1c; /* Official Red Underline */
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .agency-title {
            font-family: 'Times New Roman', serif;
            color: #1a2942;
            text-transform: uppercase;
            line-height: 1.1;
        }
        
        .search-container {
            max-width: 900px;
        }
        
        .form-check-input:checked {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
        
        .sidebar-title {
            font-family: 'Times New Roman', serif;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .book-cover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: transform 0.2s;
        }
        .book-cover:hover { transform: scale(1.02); }
    </style>
</head>
<body>

<div class="position-absolute top-0 end-0 p-3">
    <a href="/login" class="btn btn-outline-dark btn-sm fw-semibold rounded-pill px-4">
        <i class="bi bi-person-lock me-1"></i> Login
    </a>
</div>

<div class="container py-5">
    
    <div class="row align-items-center ca-header">
        <div class="col-md-1 text-center text-md-end">
            <img src="images/logo.png" alt="CA Logo" style="width: 80px;">
        </div>
        <div class="col-md-11 text-center text-md-start">
            <h4 class="agency-title fw-bold mb-0" style="letter-spacing: 1px;">Commission on Appointments</h4>
            <h2 class="agency-title fw-normal text-primary mb-0" style="color: #1e3a8a !important;">Library Information System</h2>
        </div>
    </div>

    <div class="row g-5">
        
        <div class="col-lg-9">
            
        <div class="d-flex gap-4 mb-3 ps-2">
    <div class="form-check">
        <input class="form-check-input" type="radio" name="type" id="filter1" value="All" checked>
        <label class="form-check-label" for="filter1">All</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="type" id="filter2" value="Collections">
        <label class="form-check-label" for="filter2">Books</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="type" id="filter3" value="Journal">
        <label class="form-check-label" for="filter3">Journals</label>
    </div>
</div>

                <div class="input-group input-group-lg shadow-sm mb-5">
                    <input type="text" name="q" class="form-control border-secondary" placeholder="Search by Title, Author, or Call Number..." value="<?= isset($query) ? esc($query) : '' ?>">
                    <button class="btn btn-primary" type="submit" style="background-color: #1e3a8a;">
                        <i class="bi bi-search px-3"></i>
                    </button>
                </div>
            </form>

            <?php if (isset($results) && !empty($results)): ?>
                <h5 class="text-muted mb-4">Search Results</h5>
                
                <?php foreach ($results as $book): ?>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 120px;">
                                    <i class="bi bi-book text-secondary fs-1"></i>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h4 class="fw-bold text-dark mb-1"><?= esc($book['title']) ?></h4>
                                <p class="text-muted mb-2"><?= esc($book['author']) ?></p>
                                
                                <?php if($book['status'] == 'Available'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-circle me-1"></i> Available</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-x-circle me-1"></i> Borrowed</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 text-end">
                                <a href="/login" class="btn btn-primary w-100 shadow-sm fw-semibold mb-2" style="background-color: #1e3a8a;">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Login to Borrow
                                </a>
                                <small class="text-muted d-block text-center" style="font-size: 0.75rem;">Log in with your employee ID</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php elseif (isset($query)): ?>
                <div class="alert alert-light border text-center py-5">
                    <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-secondary">No results found for "<?= esc($query) ?>"</h5>
                    <p class="text-muted">Try checking your spelling or use general keywords.</p>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted opacity-50">
                    <i class="bi bi-journal-bookmark fs-1 mb-3 d-block"></i>
                    <p>Enter a keyword above to search the library catalog.</p>
                </div>
            <?php endif; ?>

        </div>

        <div class="col-lg-3">
            <h5 class="sidebar-title">New Acquisition</h5>
            
            <div class="mb-4 text-center">
                <img src="https://m.media-amazon.com/images/I/71J192P2u9L._AC_UF1000,1000_QL80_.jpg" class="img-fluid book-cover mb-2" style="max-width: 140px;" alt="Book 1">
                <h6 class="fw-bold mb-0 text-dark small">NEWSWEEK</h6>
                <p class="text-muted small mb-0" style="font-size: 0.8rem;">AGBAYANI, AGUEDO F.</p>
            </div>

            <div class="mb-4 text-center">
                <img src="https://m.media-amazon.com/images/I/81+y+rX-1LL._AC_UF1000,1000_QL80_.jpg" class="img-fluid book-cover mb-2" style="max-width: 140px;" alt="Book 2">
                <h6 class="fw-bold mb-0 text-dark small">LAW BOOKS IN ACTION</h6>
                <p class="text-muted small mb-0" style="font-size: 0.8rem;">MODERN PHILIPPINE LEGAL FORMS</p>
            </div>
            
            <div class="mb-4 text-center">
                <img src="https://m.media-amazon.com/images/I/71Y-3V6QWzL._AC_UF1000,1000_QL80_.jpg" class="img-fluid book-cover mb-2" style="max-width: 140px;" alt="Book 3">
                <h6 class="fw-bold mb-0 text-dark small">ADMINISTRATIVE LAW</h6>
                <p class="text-muted small mb-0" style="font-size: 0.8rem;">DE LEON, HECTOR S.</p>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>