<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// --- Public Routes ---
$routes->get('/', 'Home::index');            // The landing page (Library Catalog)
$routes->get('home/search', 'Home::search'); // The search functionality

// --- Authentication Routes ---
$routes->get('login', 'Auth::index');          // Shows the login page
$routes->post('login/attempt', 'Auth::attemptLogin'); // Processes the login

$routes->get('signup', 'Auth::register');      // Shows signup page
$routes->post('signup/store', 'Auth::storeUser'); 
$routes->get('auth/verify/(:any)', 'Auth::verify/$1');

$routes->get('logout', 'Auth::logout');
// ==============================================   
// ADMIN PORTAL ROUTES
// ==============================================
$routes->group('admin', static function ($routes) {
    $routes->get('admindashboard', 'Dashboard::index');
    
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
$routes->get('reports/export', 'Reports::exportPdf'); // PDF route

    // Transactions
    $routes->get('transactions', 'Transactions::index');
    $routes->post('transactions/store', 'Transactions::store');

    // Logs Routes
    $routes->get('logs', 'Logs::index');
    $routes->post('logs/clear', 'Logs::clear');
    $routes->get('transactions', 'Transactions::index');

    // Transacation
    $routes->post('transactions/approve', 'Transactions::approve'); // Stage 1
    $routes->post('transactions/handover', 'Transactions::handover'); // Stage 2
    $routes->post('transactions/return', 'Transactions::processReturn'); // Stage 3
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
});

// ==============================================
// BORROWER PORTAL ROUTES
// ==============================================
$routes->group('borrower', static function ($routes) {

    $routes->get('borrowerdashboard', 'BorrowerController::index');
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

    // --- FIX: Notifications ---
    // This will map to: domain.com/borrower/notificationlist
    $routes->get('notificationlist', 'BorrowerController::notificationlist');
    
    // This will map to: domain.com/borrower/markSingleRead/1
    $routes->post('markSingleRead/(:num)', 'BorrowerController::markSingleRead/$1');
    
    // Optional: Keep markRead for "Mark all as read" logic
    $routes->post('notifications/markRead', 'BorrowerController::markNotificationsRead');
});