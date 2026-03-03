    <?php

    use CodeIgniter\Router\RouteCollection;

    /**
     * @var RouteCollection $routes
     */
    // --- Public Routes ---
    $routes->get('/', 'Home::index');            
    $routes->get('home/search', 'Home::search');    
    $routes->get('verify-reset', 'BorrowerController::verifyResetView');
    $routes->post('process-reset', 'BorrowerController::processReset');
    $routes->post('send-reset-code', 'BorrowerController::sendPublicResetCode');
    $routes->post('verify-reset-code-ajax', 'BorrowerController::verifyResetCodeAjax');
    $routes->post('verify-reset-code-ajax', 'BorrowerController::verifyResetCodeAjax');
    $routes->get('cancel-reset', 'BorrowerController::cancelReset');
    // --- Authentication---
    $routes->get('login', 'Auth::index'); 
    $routes->post('login/attempt', 'Auth::attemptLogin');
    $routes->get('signup', 'Auth::register'); 
    $routes->post('signup/store', 'Auth::storeUser'); 
    $routes->get('auth/verify/(:any)', 'Auth::verify/$1');
    $routes->get('logout', 'Auth::logout');
    $routes->post('process-reset', 'BorrowerController::processReset');
    // ADMIN PORTAL ROUTES
    $routes->group('admin', static function ($routes) {
        $routes->get('dashboard', 'Dashboard::index');
        
        
        // Collections
        $routes->get('collections', 'Collections::index');
        $routes->get('collections/create', 'Collections::create');
        $routes->post('collections/store', 'Collections::store');
        $routes->post('collections/delete', 'Collections::delete');
        $routes->post('collections/update', 'Collections::update');
        // Journals
        $routes->get('journals', 'Journals::index');
        $routes->post('journals/store', 'Journals::store');
        $routes->post('journals/update', 'Journals::update');
        $routes->post('journals/delete', 'Journals::delete');
        // Notifications
    $routes->get('notifications', 'Notifications::index');
    $routes->post('notifications/store', 'Notifications::store');
    $routes->post('notifications/delete', 'Notifications::delete');

    $routes->post('admin/notifications/markRead', 'Notifications::markAllRead');
        // Requests & Reports
    $routes->get('reports', 'Reports::index');
    $routes->post('reports/store', 'Reports::store');
    $routes->post('reports/approve', 'Reports::approve');
    $routes->post('reports/reject', 'Reports::reject');
    $routes->get('reports/export', 'Reports::exportPdf'); 
    $routes->get('reports/exportPdf', 'ExportReport::pdf');
    $routes->get('reports/exportExcel', 'ExportReport::excel');
        // Transactions
        $routes->get('transactions', 'Transactions::index');
        $routes->post('transactions/store', 'Transactions::store');
        $routes->post('transactions/approveRenewal', 'Transactions::approveRenewal');
        $routes->post('transactions/reject', 'Transactions::reject');
        $routes->post('transactions/reportIssue', 'Transactions::reportIssue');
        // Logs Routes
        $routes->get('logs', 'Logs::index');
        $routes->post('logs/clear', 'Logs::clear');
        $routes->get('transactions', 'Transactions::index');

        // Transacation
        $routes->post('transactions/approve', 'Transactions::approve');
        $routes->post('transactions/handover', 'Transactions::handover');
        $routes->post('transactions/return', 'Transactions::processReturn'); 
        $routes->post('transactions/sendManualReminder', 'Transactions::sendManualReminder');
        $routes->post('transactions/processReturn', 'Transactions::processReturn');
        // Users Routes
        $routes->get('users', 'Users::index');
        $routes->post('users/store', 'Users::store');          // For Add User Modal
        $routes->post('users/update', 'Users::update');        // For Edit User Modal
        $routes->post('users/approve', 'Users::approve');      // For Approve Modal
        $routes->post('users/delete', 'Users::delete');        // For Delete/Reject Modal
        // Profile Routes
        $routes->get('profile', 'Profile::index');
        $routes->post('profile/update', 'Profile::updateInfo');
        $routes->post('profile/password', 'Profile::updatePassword');
        $routes->post('profile/upload-avatar', 'Profile::uploadAvatar');
        $routes->get('forgot-password', 'Auth::forgotPassword');
        $routes->post('profile/process-reset', 'Profile::processReset');
        $routes->post('profile/request-reset-code', 'Profile::requestResetCode');
        $routes->get('verify-reset', 'Profile::verifyResetView');
    });


    // BORROWER PORTAL ROUTES
    $routes->group('borrower', static function ($routes) {
        $routes->get('dashboard', 'BorrowerController::index');
        $routes->get('catalog', 'BorrowerController::catalog');
        $routes->get('my-books', 'BorrowerController::myBooks');
        
        // Transactions
        $routes->post('request/submit', 'BorrowerController::submitRequest');
        $routes->post('request/cancel', 'BorrowerController::cancelRequest');
        $routes->post('renew/submit', 'BorrowerController::submitRenewal');
        
        // Profile
        $routes->get('profile', 'BorrowerController::profile');
        $routes->post('profile/update', 'BorrowerController::updateProfile');
        $routes->post('profile/password', 'BorrowerController::updatePassword');
        $routes->post('profile/upload-avatar', 'BorrowerController::uploadAvatar');

        // --- Notifications ---
        $routes->get('notificationlist', 'BorrowerController::notificationlist');
        $routes->post('markSingleRead/(:num)', 'BorrowerController::markSingleRead/$1');
        $routes->post('notifications/markRead', 'BorrowerController::markNotificationsRead');

        // ---Profile---
        $routes->post('profile/request-reset-code', 'BorrowerController::requestResetCode');
        $routes->get('verify-reset', 'BorrowerController::verifyResetView');
        $routes->post('profile/process-reset', 'BorrowerController::processReset');
    });

