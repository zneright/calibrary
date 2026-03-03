    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CALIS - Public Catalog</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
            
        <style>
            body { background-color: #ffffff; font-family: 'Segoe UI', system-ui, sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
            .ca-header { border-bottom: 4px solid #b91c1c; padding-bottom: 20px; margin-bottom: 30px; }
            .agency-title { font-family: 'Times New Roman', serif; color: #1a2942; text-transform: uppercase; line-height: 1.1; }
            .search-container { max-width: 900px; }
            .form-check-input:checked { background-color: #1e3a8a; border-color: #1e3a8a; }
            .sidebar-title { font-family: 'Times New Roman', serif; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; color: #333; }
            .book-cover { box-shadow: 0 4px 8px rgba(0,0,0,0.15); transition: transform 0.2s; background: #eee; }
            .book-cover:hover { transform: scale(1.02); }
            .cover-img-wrapper { height: 120px; width: 100%; overflow: hidden; display: flex; align-items: center; justify-content: center; }
            .cursor-pointer { cursor: pointer; transition: background 0.2s; }
            .cursor-pointer:hover { background-color: #f8f9fa; }
            
            .hover-white:hover { color: #ffffff !important; text-decoration: underline !important; }
        </style>
    </head>
    <body>

    <div class="position-absolute top-0 end-0 p-3">
        <a href="/login" class="btn btn-outline-dark btn-sm fw-semibold rounded-pill px-4">
            <i class="bi bi-person-lock me-1"></i> Login
        </a>
    </div>

    <div class="container py-5 flex-grow-1">
        <div class="row align-items-center ca-header">
            <div class="col-md-1 text-center text-md-end">
                <img src="<?= base_url('images/logo.png') ?>" alt="CA Logo" style="width: 80px;">
            </div>
            <div class="col-md-11 text-center text-md-start">
                <h4 class="agency-title fw-bold mb-0" style="letter-spacing: 1px;">Commission on Appointments</h4>
                <h2 class="agency-title fw-normal text-primary mb-0" style="color: #1e3a8a !important;">Library Information System</h2>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-9">
                
                <form action="/home/search" method="GET">
                    <?php $selType = $selected_type ?? 'collections'; ?>
                    <div class="d-flex gap-4 mb-3 ps-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" value="collections" id="filter1" <?= $selType == 'collections' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="filter1">Collections</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" value="journals" id="filter2" <?= $selType == 'journals' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="filter2">Journals</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" value="pd" id="filter3" <?= $selType == 'pd' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="filter3">Presidential Decree</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" value="eo" id="filter4" <?= $selType == 'eo' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="filter4">Executive Order</label>
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
                    
                    <?php if (isset($is_default) && $is_default): ?>
                        <h5 class="text-muted mb-4"><i class="bi bi-stars me-2 text-warning"></i>Discover Resources</h5>
                    <?php else: ?>
                        <h5 class="text-muted mb-4">Found <?= count($results) ?> Results</h5>
                    <?php endif; ?>
                    
                    <?php foreach ($results as $book): ?>
                    <?php $bookJson = htmlspecialchars(json_encode($book), ENT_QUOTES, 'UTF-8'); ?>
                    <div class="card border-0 shadow-sm mb-3 cursor-pointer" onclick="openDetailsModal(<?= $bookJson ?>)">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <?php if (!empty($book['cover_photo'])): ?>
                                        <div class="cover-img-wrapper rounded bg-light">
                                            <img src="<?= base_url('uploads/covers/' . esc($book['cover_photo'])) ?>" class="img-fluid" style="max-height: 100%; object-fit: cover;" alt="Cover">
                                        </div>
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded cover-img-wrapper">
                                            <i class="bi <?= $book['icon'] ?> text-secondary fs-1"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-7">
                                    <h5 class="fw-bold text-dark mb-1"><?= esc($book['title']) ?></h5>
                                    <p class="text-muted mb-1 small"><?= esc($book['author']) ?></p>
                                    <p class="text-secondary small mb-2"><i class="bi bi-tag"></i> <?= esc($book['identifier']) ?></p>
                                    
                                    <?php if(strtoupper($book['status']) == 'AVAILABLE'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-circle me-1"></i> Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-x-circle me-1"></i> <?= esc($book['status']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3 text-end">
                                    <button class="btn btn-outline-primary btn-sm w-100 mb-2 fw-semibold">
                                        <i class="bi bi-arrows-fullscreen me-1"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                <?php elseif (isset($_GET['type'])): ?>
                    <div class="alert alert-light border text-center py-5 shadow-sm">
                        <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-secondary">No results found <?= !empty($query) ? 'for "' . esc($query) . '"' : 'in this category' ?></h5>
                        <p class="text-muted">Try checking your spelling or selecting a different category.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted opacity-50">
                        <i class="bi bi-journal-bookmark fs-1 mb-3 d-block"></i>
                        <p>Enter a keyword above to search the library catalog.</p>
                    </div>
                <?php endif; ?>

            </div>

            <div class="col-lg-3">
                <h5 class="sidebar-title">New Acquisitions</h5>
                <?php if(!empty($latest_books)): ?>
                    <?php foreach($latest_books as $newBook): ?>
                    <?php 
                        $newBookJson = htmlspecialchars(json_encode($newBook), ENT_QUOTES, 'UTF-8'); 
                    ?>
                    <div class="mb-4 text-center p-3 border rounded shadow-sm bg-light cursor-pointer" onclick="openDetailsModal(<?= $newBookJson ?>)">
                        <?php if(!empty($newBook['cover_photo'])): ?>
                            <img src="<?= base_url('uploads/covers/' . esc($newBook['cover_photo'])) ?>" class="img-fluid book-cover mb-3 rounded" style="width: 100px; height: 140px; object-fit: cover;" alt="Cover">
                        <?php else: ?>
                            <div class="book-cover rounded mx-auto mb-3 d-flex align-items-center justify-content-center bg-white" style="width: 100px; height: 140px; border: 1px solid #ddd;">
                                <i class="bi <?= $newBook['icon'] ?> fs-1 text-secondary"></i>
                            </div>
                        <?php endif; ?>

                        <h6 class="fw-bold mb-1 text-dark small text-truncate" title="<?= esc($newBook['title']) ?>"><?= esc($newBook['title']) ?></h6>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: 0.75rem;"><?= esc($newBook['author']) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small text-center">No new acquisitions yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="text-white pt-5 pb-4 mt-auto shadow-lg" style="background-color: #1a2942; border-top: 4px solid #b91c1c;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= base_url('images/logo.png') ?>" alt="CA Logo" style="width: 55px;" class="me-3 bg-white rounded-circle p-1 shadow-sm">
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-family: 'Times New Roman', serif; letter-spacing: 1px;">COMMISSION ON APPOINTMENTS</h6>
                            <small class="text-white-50">Library Information System</small>
                        </div>
                    </div>
                    <p class="small text-white-50 lh-lg mb-0 pe-md-4">
                        The Data Bank and Library Services (DBLS) provides access to a comprehensive collection of books, journals, and legislative documents to support the research and information needs of the Commission.
                    </p>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold mb-3 text-uppercase" style="color: #93c5fd; letter-spacing: 0.5px;">Quick Links</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><a href="<?= base_url('/') ?>" class="text-white-50 text-decoration-none hover-white transition"><i class="bi bi-chevron-right me-1 small"></i> Home</a></li>
                        <li class="mb-2"><a href="<?= base_url('/home/search?type=collections') ?>" class="text-white-50 text-decoration-none hover-white transition"><i class="bi bi-chevron-right me-1 small"></i> Collections</a></li>
                        <li class="mb-2"><a href="<?= base_url('/home/search?type=journals') ?>" class="text-white-50 text-decoration-none hover-white transition"><i class="bi bi-chevron-right me-1 small"></i> Journals</a></li>
                        <li class="mb-2 mt-3"><a href="<?= base_url('/login') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 mt-1"><i class="bi bi-box-arrow-in-right me-1"></i> Employee Login</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-12">
                    <h6 class="fw-bold mb-3 text-uppercase" style="color: #93c5fd; letter-spacing: 0.5px;">Contact Us</h6>
                    <ul class="list-unstyled small mb-0 text-white-50 lh-lg">
                        <li class="mb-2 d-flex"><i class="bi bi-geo-alt-fill me-2 mt-1"></i> <span>GSIS Bldg., Financial Center, Roxas Blvd., Pasay City, Metro Manila</span></li>
                        <li class="mb-2"><i class="bi bi-envelope-fill me-2"></i> library@comappt.gov.ph</li>
                        <li class="mb-2"><i class="bi bi-telephone-fill me-2"></i> (02) 8888-8888</li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-secondary mt-4 mb-4 opacity-25">
            
            <div class="row align-items-center small text-white-50">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    &copy; <?= date('Y') ?> Commission on Appointments. All rights reserved.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    Developed for the Data Bank and Library Services (DBLS)
                </div>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-primary" id="itemDetailsModalLabel">Resource Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4 border-end" id="modalCoverContainer" style="min-height: 300px;">
                            </div>
                        
                        <div class="col-md-7 p-4">
                            <span id="modalStatusBadge" class="badge bg-success mb-2 px-3 py-2">Available</span>
                            
                            <h4 id="modalTitle" class="fw-bold text-dark mb-1">Title</h4>
                            <p id="modalAuthor" class="text-primary fw-semibold mb-3">Author</p>
                            
                            <hr>
                            
                            <div class="mb-2">
                                <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Resource Type</small>
                                <span id="modalType" class="fw-bold text-dark">Type</span>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Identifier / Call No.</small>
                                <span id="modalIdentifier" class="fw-bold text-dark">Identifier</span>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Publisher / Source</small>
                                    <span id="modalPublisher" class="text-dark">Publisher</span>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Date / Year</small>
                                    <span id="modalDate" class="text-dark">Date</span>
                                </div>
                                <div class="col-12 mt-2">
                                    <small class="text-muted d-block" style="font-size: 0.75rem; text-transform: uppercase;">Library Location</small>
                                    <span id="modalLocation" class="text-dark">Location</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <a href="/login" class="btn btn-primary w-100 fw-bold py-2 shadow-sm" style="background-color: #1e3a8a;">
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
            document.getElementById('modalTitle').innerText = book.title;
            document.getElementById('modalAuthor').innerText = book.author;
            document.getElementById('modalIdentifier').innerText = book.identifier;
            document.getElementById('modalType').innerText = book.type_label;
            document.getElementById('modalPublisher').innerText = book.publisher;
            document.getElementById('modalDate').innerText = book.issued_date;
            document.getElementById('modalLocation').innerText = book.location;
            
            let formData = new FormData();
            formData.append('id', book.id);
            formData.append('type', book.type_label || book.type);
            fetch('<?= base_url('track-view') ?>', { method: 'POST', body: formData });
            //Set Status 
            
            let statusBadge = document.getElementById('modalStatusBadge');
            statusBadge.innerText = book.status;
            if(book.status.toUpperCase() === 'AVAILABLE') {
                statusBadge.className = "badge bg-success bg-opacity-10 text-success border border-success mb-2 px-3 py-2";
            } else {
                statusBadge.className = "badge bg-danger bg-opacity-10 text-danger border border-danger mb-2 px-3 py-2";
            }

            // Cover Photo
            let coverContainer = document.getElementById('modalCoverContainer');
            if (book.cover_photo) {
                let imgUrl = "<?= base_url('uploads/covers/') ?>" + book.cover_photo;
                coverContainer.innerHTML = `<img src="${imgUrl}" class="img-fluid rounded shadow" style="max-height: 350px; object-fit: contain;" alt="Cover">`;
            } else {
                coverContainer.innerHTML = `<i class="bi ${book.icon} text-secondary" style="font-size: 8rem;"></i>`;
            }

            var myModal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
            myModal.show();
        }
    </script>

    </body>
    </html>