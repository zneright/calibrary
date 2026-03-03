<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CALIS - Public Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        /* Hero Section */
        .hero-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            padding: 6rem 0 4rem;
            position: relative;
            color: white;
            border-bottom: 5px solid #b91c1c; /* Official Red Accent */
        }
        .agency-title { font-family: 'Times New Roman', serif; letter-spacing: 1px; }
        
        /* Integrated Search Bar */
        .integrated-search {
            background: white;
            border-radius: 50px;
            padding: 6px 6px 6px 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .integrated-search:focus-within { box-shadow: 0 10px 30px rgba(30, 58, 138, 0.4); transform: translateY(-2px); }
        .search-select { border: none; background: transparent; font-weight: 600; color: #4b5563; cursor: pointer; outline: none; }
        .search-input { border: none; background: transparent; box-shadow: none !important; }
        
        /* Book Grid Cards */
        .book-card {
            transition: all 0.2s ease;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
            border-color: #1e3a8a !important;
        }
        .cover-img-wrapper { height: 140px; width: 100px; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Sidebar */
        .sidebar-title { font-family: 'Times New Roman', serif; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 20px; color: #111827; }
    </style>
</head>
<body>

<div class="position-absolute top-0 end-0 p-4 z-3">
    <a href="/login" class="btn btn-light btn-sm fw-bold rounded-pill px-4 shadow-sm text-primary">
        <i class="bi bi-person-lock me-1"></i> Login
    </a>
</div>

<div class="hero-banner text-center shadow">
    <div class="container">
        <img src="<?= base_url('images/ca_logo.png') ?>" alt="CA Logo" style="width: 90px;" class="mb-3 drop-shadow">
        <h2 class="agency-title fw-bold mb-1">Commission on Appointments</h2>
        <h5 class="fw-light text-white-50 mb-4">Library Information System</h5>

        <div class="row justify-content-center mt-5">
            <div class="col-lg-8 col-md-10">
                <form action="/home/search" method="GET" class="integrated-search d-flex align-items-center">
                    <?php $selType = $selected_type ?? 'collections'; ?>
                    
                    <select name="type" class="search-select form-select form-select-sm w-auto pe-4 border-end border-2 rounded-0">
                        <option value="collections" <?= $selType == 'collections' ? 'selected' : '' ?>>Collections</option>
                        <option value="journals" <?= $selType == 'journals' ? 'selected' : '' ?>>Journals</option>
                        <option value="pd" <?= $selType == 'pd' ? 'selected' : '' ?>>Presidential Decrees</option>
                        <option value="eo" <?= $selType == 'eo' ? 'selected' : '' ?>>Executive Orders</option>
                    </select>

                    <input type="text" name="q" class="search-input form-control form-control-lg px-3" placeholder="Search by Title, Author, or Keyword..." value="<?= isset($query) ? esc($query) : '' ?>">
                    
                    <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold" type="submit" style="background-color: #1e3a8a; border-color: #1e3a8a;">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        
        <div class="col-lg-9">
            
            <?php if (isset($results) && !empty($results)): ?>
                
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <?php if (isset($is_default) && $is_default): ?>
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-stars me-2 text-warning"></i>Featured Resources</h5>
                        <span class="text-muted small">Randomized Selection</span>
                    <?php else: ?>
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2 text-primary"></i>Search Results</h5>
                        <span class="badge bg-secondary rounded-pill"><?= count($results) ?> Found</span>
                    <?php endif; ?>
                </div>
                
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <?php foreach ($results as $book): ?>
                    <?php $bookJson = htmlspecialchars(json_encode($book), ENT_QUOTES, 'UTF-8'); ?>
                    <div class="col">
                        <div class="card h-100 border book-card bg-white" onclick="openDetailsModal(<?= $bookJson ?>)">
                            <div class="row g-0 h-100">
                                <div class="col-4 bg-light d-flex align-items-center justify-content-center p-2 border-end">
                                    <?php if (!empty($book['cover_photo'])): ?>
                                        <div class="cover-img-wrapper rounded shadow-sm bg-white">
                                            <img src="<?= base_url('uploads/covers/' . esc($book['cover_photo'])) ?>" class="img-fluid" style="height: 100%; object-fit: cover;" alt="Cover">
                                        </div>
                                    <?php else: ?>
                                        <div class="cover-img-wrapper rounded bg-white shadow-sm text-secondary">
                                            <i class="bi <?= $book['icon'] ?>" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-8">
                                    <div class="card-body p-3 d-flex flex-column h-100">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate-2" title="<?= esc($book['title']) ?>"><?= esc($book['title']) ?></h6>
                                        <p class="text-muted small mb-2 text-truncate"><?= esc($book['author']) ?></p>
                                        
                                        <div class="mt-auto">
                                            <p class="text-secondary small mb-2" style="font-size: 0.7rem;"><i class="bi bi-tag me-1"></i><?= esc($book['identifier']) ?></p>
                                            <?php if(strtoupper($book['status']) == 'AVAILABLE'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success w-100 py-2"><i class="bi bi-check-circle me-1"></i> Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger w-100 py-2"><i class="bi bi-x-circle me-1"></i> <?= esc($book['status']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif (isset($query)): ?>
                <div class="alert bg-white border text-center py-5 shadow-sm rounded-4">
                    <i class="bi bi-search text-muted mb-3 d-block" style="font-size: 4rem;"></i>
                    <h4 class="text-dark fw-bold">No results found for "<?= esc($query) ?>"</h4>
                    <p class="text-muted">Try checking your spelling or select a different resource category.</p>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted opacity-50">
                    <i class="bi bi-journal-bookmark fs-1 mb-3 d-block"></i>
                    <p>Enter a keyword above to search the library catalog.</p>
                </div>
            <?php endif; ?>

        </div>

        <div class="col-lg-3">
            <div class="bg-white rounded-4 shadow-sm p-4 border">
                <h5 class="sidebar-title"><i class="bi bi-stars text-warning me-2"></i>New Arrivals</h5>
                
                <?php if(!empty($latest_books)): ?>
                    <?php foreach($latest_books as $newBook): ?>
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <?php if(!empty($newBook['cover_photo'])): ?>
                            <img src="<?= base_url('uploads/covers/' . esc($newBook['cover_photo'])) ?>" class="rounded shadow-sm me-3" style="width: 50px; height: 70px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 shadow-sm text-secondary border" style="width: 50px; height: 70px;">
                                <i class="bi bi-book fs-4"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="overflow-hidden">
                            <h6 class="fw-bold mb-1 text-dark small text-truncate" title="<?= esc($newBook['title']) ?>"><?= esc($newBook['title']) ?></h6>
                            <p class="text-muted mb-0 text-truncate" style="font-size: 0.7rem;"><?= esc($newBook['author']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small text-center my-4">No new acquisitions yet.</p>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>

<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4 border-end" id="modalCoverContainer" style="min-height: 350px;">
                        </div>
                    
                    <div class="col-md-7 p-5">
                        <span id="modalStatusBadge" class="badge mb-2 px-3 py-2 rounded-pill"></span>
                        
                        <h3 id="modalTitle" class="fw-bold text-dark mb-1" style="line-height: 1.2;">Title</h3>
                        <p id="modalAuthor" class="text-primary fw-semibold mb-4">Author</p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Resource Type</small>
                                <span id="modalType" class="text-dark fw-medium">Type</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Identifier / Call No.</small>
                                <span id="modalIdentifier" class="text-dark fw-medium font-monospace">Identifier</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Publisher / Source</small>
                                <span id="modalPublisher" class="text-dark fw-medium">Publisher</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Date / Year</small>
                                <span id="modalDate" class="text-dark fw-medium">Date</span>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Library Location</small>
                                <span id="modalLocation" class="text-dark fw-medium">Location</span>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top">
                            <a href="/login" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-pill" style="background-color: #1e3a8a;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login to Request Item
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function openDetailsModal(book) {
        // 1. Set text details
        document.getElementById('modalTitle').innerText = book.title;
        document.getElementById('modalAuthor').innerText = book.author;
        document.getElementById('modalIdentifier').innerText = book.identifier;
        document.getElementById('modalType').innerText = book.type_label;
        document.getElementById('modalPublisher').innerText = book.publisher;
        document.getElementById('modalDate').innerText = book.issued_date;
        document.getElementById('modalLocation').innerText = book.location;

        // 2. Set Status Badge
        let statusBadge = document.getElementById('modalStatusBadge');
        statusBadge.innerText = book.status;
        if(book.status.toUpperCase() === 'AVAILABLE') {
            statusBadge.className = "badge bg-success bg-opacity-10 text-success border border-success mb-2 px-3 py-2 rounded-pill";
        } else {
            statusBadge.className = "badge bg-danger bg-opacity-10 text-danger border border-danger mb-2 px-3 py-2 rounded-pill";
        }

        // 3. Set Cover Photo
        let coverContainer = document.getElementById('modalCoverContainer');
        if (book.cover_photo) {
            let imgUrl = "<?= base_url('uploads/covers/') ?>" + book.cover_photo;
            coverContainer.innerHTML = `<img src="${imgUrl}" class="img-fluid rounded shadow-lg" style="max-height: 400px; object-fit: contain;" alt="Cover">`;
        } else {
            coverContainer.innerHTML = `<i class="bi ${book.icon} text-secondary opacity-50" style="font-size: 10rem;"></i>`;
        }

        // 4. Open Modal
        var myModal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
        myModal.show();
    }
</script>

</body>
</html>