<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed Qby all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
{
    // Do not edit this line
    parent::initController($request, $response, $logger);

    // 1. Load the Notification Model
    $notificationModel = new \App\Models\NotificationModel();
    $session = service('session');
    $userId = $session->get('user_id');
    $role = $session->get('role'); // Assuming you store 'Admin' or 'Borrower' in session

    $notifs = [];
    $unreadCount = 0;

    if ($userId) {
        if ($role === 'Admin') {
            // Logic for Admins: Show system-wide admin alerts
            $notifs = $notificationModel->where('target_audience', 'all_admins')
                                        ->orderBy('created_at', 'DESC')
                                        ->limit(5)
                                        ->findAll();

            $unreadCount = $notificationModel->where('target_audience', 'all_admins')
                                             ->where('status', 'unread')
                                             ->countAllResults();
        } else {
            // Logic for Borrowers: Show personal + general alerts
            $notifs = $notificationModel->groupStart()
                                        ->where('target_audience', 'all_users')
                                        ->orLike('recipient', $userId)
                                    ->groupEnd()
                                    ->orderBy('created_at', 'DESC')
                                    ->limit(5)
                                    ->findAll();

            $unreadCount = 0;
            foreach ($notifs as $n) {
                if ($n['status'] === 'unread') $unreadCount++;
            }
        }
    }

    // 2. Share variables globally to ALL views
    // This removes the need to pass 'notifs' in every $data array!
    $globals = [
        'notifs'      => $notifs,
        'unreadCount' => $unreadCount,
    ];

    view()->setData($globals);
}
}
