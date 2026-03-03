<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="py-4 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #1a2942 0%, #0f172a 100%);">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <h3 class="fw-bold mb-1">Library Catalog</h3>
                <p class="mb-0 text-white-50 small">Search and request resources from the Commission on Appointments</p>
            </div>
            <form action="/borrower/catalog" method="GET" class="d-flex w-100" style="max-width: 400px;">
                <?php if(!empty($selectedTypes)): foreach($selectedTypes as $t): ?>
                    <input type="hidden" name="type[]" value="<?= esc($t) ?>">
                <?php endforeach; endif; ?>
                <input type="hidden" name="status" value="<?= esc($selectedStatus) ?>">
                
                <input type="text" name="q" class="form-control border-0 shadow-sm" placeholder="Search title, author, or keyword..." value="<?= esc($search) ?>" style="border-radius: 0.375rem 0 0 0.375rem;">
                <button class="btn btn-primary fw-semibold shadow-sm" type="submit" style="background-color: #1e3a8a; border-color: #1e3a8a; border-radius: 0 0.375rem 0.375rem 0;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="container mb-5">
    
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4 mt-1">
        
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-funnel me-2" style="color: #1e3a8a;"></i>Filter Results</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/borrower/catalog" method="GET">
                        <?php if(!empty($search)): ?>
                            <input type="hidden" name="q" value="<?= esc($search) ?>">
                        <?php endif; ?>

                        <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Resource Type</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Book" id="typeBook" <?= in_array('Book', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typeBook">Collections</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Journal" id="typeJournal" <?= in_array('Journal', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typeJournal">Journals</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Presidential Decree" id="typePD" <?= in_array('Presidential Decree', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typePD">Presidential Decrees</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input shadow-sm" type="checkbox" name="type[]" value="Executive Order" id="typeEO" <?= in_array('Executive Order', $selectedTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="typeEO">Executive Orders</label>
                        </div>

                        <h6 class="fw-bold text-secondary small mb-3 text-uppercase">Availability</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input shadow-sm" type="radio" name="status" value="all" id="statusAll" <?= $selectedStatus == 'all' ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="statusAll">Show All</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input shadow-sm" type="radio" name="status" value="available" id="statusAvail" <?= $selectedStatus == 'available' ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="statusAvail">Available Only</label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 fw-semibold shadow-sm text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted">Found <strong><?= $total_results ?></strong> resources</p>
            </div>

            <?php if(!empty($items)): ?>
                <?php foreach($items as $item): ?>
                    <?php 
                        $borderColor = '#0f766e'; // Default
                        if($item['type'] == 'Journal') $borderColor = '#7f1d1d';
                        if($item['type'] == 'Executive Order') $borderColor = '#1e3a8a';
                        
                        $itemStatus = strtoupper($item['status']);
                        $displayStatus = $item['status'];
                        $statusBadgeClass = 'bg-success bg-opacity-10 text-success border-success';
                        
                        // NEW LOGIC: Is someone using this?
                        $userTransStatus = $userTransactions[$item['id']] ?? null;
                        
                        // Note: $globalActiveItems is not passed from your controller to this view. Assuming it's meant to be managed elsewhere.
                        $globalTransStatus = $globalActiveItems[$item['id']] ?? null; 
                        
                        // If it's active in the system, but NOT owned by the current user:
                        $isReservedByOther = ($globalTransStatus && !$userTransStatus);

                        if ($isReservedByOther) {
                            $statusBadgeClass = 'bg-secondary bg-opacity-10 text-secondary border-secondary';
                            $displayStatus = 'UNAVAILABLE';
                        } elseif ($itemStatus === 'BORROWED') {
                            $statusBadgeClass = 'bg-warning bg-opacity-10 text-dark border-warning';
                        } elseif ($itemStatus === 'LOST') {
                            $statusBadgeClass = 'bg-secondary bg-opacity-10 text-secondary border-secondary';
                            $displayStatus = 'UNAVAILABLE';
                        } elseif ($itemStatus === 'DAMAGED') {
                            $statusBadgeClass = 'bg-danger bg-opacity-10 text-danger border-danger';
                            $displayStatus = 'DAMAGED';
                        }

                        // Encode data for the JS modal
                        $itemJson = htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8');
                    ?>

                    <div class="card border-0 shadow-sm mb-3 position-relative overflow-hidden cursor-pointer item-card" style="transition: transform 0.2s;" onclick="openDetailsModal(<?= $itemJson ?>, '<?= $displayStatus ?>')">
                        <div class="position-absolute top-0 start-0 h-100" style="width: 4px; background-color: <?= $borderColor ?>;"></div>
                        <div class="card-body p-4 ms-2">
                            <div class="row align-items-center">
                                <div class="col-md-9 d-flex align-items-start">
                                    
                                    <?php if(!empty($item['cover_photo'])): ?>
                                        <img src="<?= base_url('uploads/covers/'.$item['cover_photo']) ?>" class="rounded shadow-sm me-4" style="width: 70px; height: 95px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex justify-content-center align-items-center me-4 shadow-sm" style="width: 70px; height: 95px; color: <?= $borderColor ?>;">
                                            <i class="bi <?= ($item['type'] == 'Journal') ? 'bi-journal-bookmark' : 'bi-book' ?> fs-2"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-light text-dark border me-2"><?= esc($item['type']) ?></span>
                                            <span class="badge border <?= $statusBadgeClass ?> border-opacity-25"><?= esc($displayStatus) ?></span>
                                        </div>
                                        
                                        <h5 class="fw-bold mb-1" style="color: #1a2942;"><?= esc($item['title']) ?></h5>
                                        <p class="text-muted small mb-2">
                                            <?= $item['type'] == 'Journal' ? 'Volume: ' : 'Class: ' ?> <?= esc($item['class'] ?? 'N/A') ?> 
                                            • Author: <?= esc($item['author'] ?? 'Unknown') ?>
                                            <?php if($item['issued_date']): ?>
                                                • Date: <?= date('M Y', strtotime($item['issued_date'])) ?>
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-secondary small mb-0 d-none d-md-block text-truncate" style="max-width: 500px;">
                                            <?= esc($item['subject']) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <button class="btn btn-outline-secondary btn-sm w-100 fw-bold shadow-sm">View Details</button>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 border rounded bg-white shadow-sm">
                    <i class="bi bi-search text-muted fs-1"></i>
                    <p class="mt-3 text-muted">No resources found matching your search.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold text-primary">Resource Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    
                    <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4 border-end" id="modalCoverContainer" style="min-height: 300px;">
                        </div>
                    
                    <div class="col-md-7 p-4 d-flex flex-column justify-content-between">
                        
                        <div>
                            <span id="modalStatusBadge" class="badge mb-2 px-3 py-2"></span>
                            <span id="modalTypeBadge" class="badge bg-light border text-dark mb-2 px-3 py-2 ms-1"></span>
                            
                            <h4 id="modalTitle" class="fw-bold text-dark mb-1"></h4>
                            <p id="modalAuthor" class="text-primary fw-semibold mb-3"></p>
                            
                            <hr>
                            
                            <div class="row mt-3 text-muted small">
                                <div class="col-6 mb-2">
                                    <span class="d-block text-uppercase" style="font-size: 0.7rem;">Class / Vol</span>
                                    <span id="modalClass" class="fw-bold text-dark"></span>
                                </div>
                                <div class="col-6 mb-2">
                                    <span class="d-block text-uppercase" style="font-size: 0.7rem;">Date / Year</span>
                                    <span id="modalDate" class="text-dark fw-bold"></span>
                                </div>
                                <div class="col-12 mt-2">
                                    <span class="d-block text-uppercase" style="font-size: 0.7rem;">Publisher / Subject</span>
                                    <span id="modalSubject" class="text-dark"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top" id="modalActionContainer">
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Replaced standard cards with hover state cursor logic via CSS class .cursor-pointer
    document.querySelectorAll('.item-card').forEach(card => {
        card.addEventListener('mouseover', () => card.classList.add('bg-light'));
        card.addEventListener('mouseout', () => card.classList.remove('bg-light'));
    });

    function openDetailsModal(item, calculatedStatus) {
        
        // 1. Populate Text
        document.getElementById('modalTitle').innerText = item.title;
        document.getElementById('modalAuthor').innerText = item.author || 'Unknown';
        document.getElementById('modalClass').innerText = item.class || 'N/A';
        document.getElementById('modalDate').innerText = item.issued_date ? new Date(item.issued_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : 'N/A';
        document.getElementById('modalSubject').innerText = item.subject || 'N/A';
        document.getElementById('modalTypeBadge').innerText = item.type;

        // 2. Set Status Badge
        let badge = document.getElementById('modalStatusBadge');
        badge.innerText = calculatedStatus;
        if(calculatedStatus === 'AVAILABLE') {
            badge.className = 'badge bg-success bg-opacity-10 text-success border border-success mb-2 px-3 py-2';
        } else if (calculatedStatus === 'DAMAGED') {
            badge.className = 'badge bg-danger bg-opacity-10 text-danger border border-danger mb-2 px-3 py-2';
        } else {
            badge.className = 'badge bg-secondary bg-opacity-10 text-secondary border border-secondary mb-2 px-3 py-2';
        }

        // 3. Set Image
        let coverContainer = document.getElementById('modalCoverContainer');
        if (item.cover_photo) {
            let imgUrl = "<?= base_url('uploads/covers/') ?>" + item.cover_photo;
            coverContainer.innerHTML = `<img src="${imgUrl}" class="img-fluid rounded shadow" style="max-height: 350px; object-fit: contain;" alt="Cover">`;
        } else {
            let icon = (item.type === 'Journal') ? 'bi-journal-bookmark' : 'bi-book';
            coverContainer.innerHTML = `<i class="bi ${icon} text-secondary" style="font-size: 8rem;"></i>`;
        }

        // 4. Build Action Form Logic
        let actionHtml = '';

        // Safely encode title for JS injection
        let safeTitle = item.title.replace(/'/g, "\\'"); 

        if (calculatedStatus === 'AVAILABLE' || calculatedStatus === 'DAMAGED') {
            
            let damageWarning = '';
            let btnClass = 'btn-primary';
            let btnBg = 'background-color: #1e3a8a; border-color: #1e3a8a;';
            let titlePrefix = '';
            
            if (calculatedStatus === 'DAMAGED') {
                titlePrefix = '[DAMAGED COPY] ';
                btnClass = 'btn-danger';
                btnBg = '';
                damageWarning = `
                    <div class="alert alert-danger small p-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Notice: You are requesting a Damaged copy.
                    </div>
                `;
            }

            // Build Form
            actionHtml = `
                <form action="/borrower/request/submit" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="collection_id" value="${item.id}">
                    <input type="hidden" name="source_table" value="${item.source_table}">
                    <input type="hidden" name="hidden_title" value="${titlePrefix}${safeTitle}">

                    ${damageWarning}

                    <div class="mb-2">
                        <label class="form-label fw-bold small">Date Needed <span class="text-danger">*</span></label>
                        <input type="date" name="date_needed" class="form-control form-control-sm shadow-sm" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Reason for Borrowing <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control form-control-sm shadow-sm" rows="2" placeholder="Brief explanation..." required></textarea>
                    </div>

                    <button type="submit" class="btn ${btnClass} w-100 fw-bold shadow-sm" style="${btnBg}">
                        <i class="bi bi-send me-2"></i> Submit Request
                    </button>
                </form>
            `;
        } else {
            actionHtml = `
                <div class="alert alert-light text-center border text-muted w-100 mb-0">
                    <i class="bi bi-slash-circle me-1"></i> This item is currently ${calculatedStatus.toLowerCase()} and cannot be requested.
                </div>
            `;
        }

        document.getElementById('modalActionContainer').innerHTML = actionHtml;

        //Show Modal
        var myModal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
        myModal.show();
    }
</script>
<?= $this->endSection() ?>