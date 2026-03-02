<?= $this->extend('layouts/borrower') ?>

<?= $this->section('content') ?>

<div class="page-header text-center px-3">
    <div class="container">
        <?php 
            $fullName = session()->get('fullname');
            $firstName = explode(' ', trim($fullName))[0]; 
        ?>
        <h2 class="fw-bold mb-3">Hello, <?= esc($firstName) ?>! 👋</h2>
        <p class="fs-5 text-white-50 mb-4">What would you like to search today?</p>
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form action="/borrower/catalog" method="GET" class="d-flex shadow-lg rounded-pill bg-white p-1">
                    <input type="text" name="q" class="form-control border-0 rounded-pill px-4" placeholder="Search for books, journals, authors..." style="box-shadow: none;">
                    <button class="btn btn-dark rounded-pill px-4 fw-semibold" type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row g-4 mb-5">
        
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #1e3a8a !important;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center me-4" style="width: 60px; height: 60px; background-color: rgba(30, 58, 138, 0.1); color: #1e3a8a;">
                        <i class="bi bi-book fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1">CURRENTLY BORROWED</h6>
                        <h3 class="fw-bold mb-0 text-dark">2 <span class="fs-6 text-muted fw-normal">Items</span></h3>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <a href="/borrower/my-books" class="text-decoration-none fw-semibold" style="color: #1e3a8a;">View due dates <i class="bi bi-arrow-right small"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0f766e !important;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center me-4" style="width: 60px; height: 60px; background-color: rgba(15, 118, 110, 0.1); color: #0f766e;">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1">PENDING REQUESTS</h6>
                        <h3 class="fw-bold mb-0 text-dark">1 <span class="fs-6 text-muted fw-normal">Item</span></h3>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <a href="/borrower/my-books" class="text-decoration-none fw-semibold" style="color: #0f766e;">Check status <i class="bi bi-arrow-right small"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-white" style="background-color: #7f1d1d !important;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-white text-danger rounded-circle d-flex justify-content-center align-items-center me-4 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi bi-exclamation-triangle-fill fs-3" style="color: #7f1d1d;"></i>
                    </div>
                    <div>
                        <h6 class="text-white-50 fw-bold mb-1">ACTION REQUIRED</h6>
                        <h5 class="fw-bold mb-0">1 Item Overdue</h5>
                        <small class="text-white-50">Please return immediately.</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">My Active Borrows</h5>
            <a href="/borrower/my-books" class="btn btn-sm btn-outline-dark shadow-sm">View All History</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">BOOK / ITEM</th>
                            <th>DATE BORROWED</th>
                            <th>DUE DATE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3"><i class="bi bi-book fs-4" style="color: #1e3a8a;"></i></div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">GRANTING SALARY INCREASE TO CAREER EXEC. SERVICE OFFICERS OF THE GOVERNMENT</h6>
                                        <small class="text-muted">Call No: QA76.73</small>
                                    </div>
                                </div>
                            </td>
                            <td>Feb 18, 2026</td>
                            <td class="fw-semibold">Feb 21, 2026</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">On Hand</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3"><i class="bi bi-book fs-4" style="color: #1e3a8a;"></i></div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold" style="color: #7f1d1d;">CA MEMBERS -- HOUSE OF REP. -- 8TH CONGRESS</h6>
                                        <small class="text-muted">Call No: QA76.73.P2</small>
                                    </div>
                                </div>
                            </td>
                            <td>Feb 10, 2026</td>
                            <td class="fw-bold" style="color: #7f1d1d;">Feb 15, 2026</td>
                            <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Overdue</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>