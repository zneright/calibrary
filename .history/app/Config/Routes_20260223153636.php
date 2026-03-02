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
$routes->post('signup/store', 'Auth::storeUser'); // Processes signup

// ==============================================
// ADMIN PORTAL ROUTES
// ==============================================
$routes->group('admin', static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    
    // Collections
    $routes->get('collections', 'Collections::index');
    $routes->get('collections/create', 'Collections::create');
    $routes->post('collections/store', 'Collections::store');

    // Journals
    $routes->get('journals', 'Journals::index');
    $routes->post('journals/store', 'Journals::store');

    // Notifications
    $routes->get('notifications', 'Notifications::index');
    $routes->post('notifications/store', 'Notifications::store');

    // Requests & Reports
    $routes->get('reports', 'Reports::index');
    $routes->post('reports/store', 'Reports::store');
    $routes->get('reports/export', 'Reports::exportPdf'); 

    // Transactions
    $routes->get('transactions', 'Transactions::index');
    $routes->post('transactions/store', 'Transactions::store');

    // Logs Routes
    $routes->get('logs', 'Logs::index');
    $routes->post('logs/clear', 'Logs::clear');

    // Users Routes
    $routes->get('users', 'Users::index');
    $routes->post('users/store', 'Users::store');

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

    $routes->get('dashboard', 'BorrowerController::index');

    $routes->get('catalog', 'BorrowerController::catalog');

    $routes->get('my-books', 'BorrowerController::myBooks');
    
    // profile routes
    $routes->get('profile', 'BorrowerController::profile');
    $routes->post('profile/update', 'BorrowerController::updateProfile');
    $routes->post('profile/password', 'BorrowerController::updatePassword');
    $routes->post('profile/upload-avatar', 'BorrowerController::uploadAvatar');

});