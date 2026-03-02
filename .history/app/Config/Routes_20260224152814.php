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
$routes->get('logout', 'Auth::logout');        // <--- ADDED LOGOUT ROUTE

$routes->get('signup', 'Auth::register');      // Shows signup page
$routes->post('signup/store', 'Auth::storeUser'); 
$routes->get('auth/verify/(:any)', 'Auth::verify/$1');

// ==============================================   
// ADMIN PORTAL ROUTES
// ==============================================
// Removed 'static' to prevent "$this" context errors
$routes->group('admin', function ($routes) {
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

    // Requests & Reports
    $routes->get('reports', 'Reports::index');
    $routes->post('reports/store', 'Reports::store');
    $routes->post('reports/approve', 'Reports::approve');
    $routes->post('reports/reject', 'Reports::reject');
    $routes->get('reports/export', 'Reports::exportPdf'); 

    // Admin Transactions (Manage Requests)
    $routes->get('transactions', 'Transactions::index');
    $routes->post('transactions/store', 'Transactions::store');
    $routes->post('transactions/updateStatus', 'Transactions::updateStatus'); // <--- ADDED FOR ADMIN

    // Logs Routes
    $routes->get('logs', 'Logs::index');
    $routes->post('logs/clear', 'Logs::clear');

    // Users Routes
    $routes->get('users', 'Users::index');
    $routes->post('users/store', 'Users::store');          
    $routes->post('users/update', 'Users::update');        
    $routes->post('users/approve', 'Users::approve');      
    $routes->post('users/delete', 'Users::delete');        

    // Profile Routes
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::updateInfo');
    $routes->post('profile/password', 'Profile::updatePassword');
    $routes->post('profile/upload-avatar', 'Profile::uploadAvatar');
});

// ==============================================
// BORROWER PORTAL ROUTES
// ==============================================
// Removed 'static' here as well
$routes->group('borrower', function ($routes) {

    $routes->get('dashboard', 'BorrowerController::index');
    $routes->get('catalog', 'BorrowerController::catalog');
    $routes->get('my-books', 'BorrowerController::myBooks');
    
    // Borrower Actions
    $routes->post('request/submit', 'BorrowerController::submitRequest');
    $routes->post('request/cancel', 'BorrowerController::cancelRequest');
    $routes->post('renew/submit', 'BorrowerController::submitRenewal');
    
    // Borrower Profile
    $routes->get('profile', 'BorrowerController::profile');
    $routes->post('profile/update', 'BorrowerController::updateProfile');
    $routes->post('profile/password', 'BorrowerController::updatePassword');
    $routes->post('profile/upload-avatar', 'BorrowerController::uploadAvatar');
});