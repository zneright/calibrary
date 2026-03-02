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

    // 1. Initialize variables
    $notificationModel = new \App\Models\NotificationModel();
    $session = service('session');
    $userId = $session->get('user_id');
    $role = $session->get('role'); 

    $notifs = [];
    $unreadCount = 0;

    // 2. Fetch notifications logic
    if ($userId) {
        if ($role === 'Admin') {
            $notifs = $notificationModel->where('target_audience', 'all_admins')
                                        ->orderBy('created_at', 'DESC')
                                        ->limit(5)
                                        ->findAll();

            $unreadCount = $notificationModel->where('target_audience', 'all_admins')
                                             ->where('status', 'unread')
                                             ->countAllResults();
        } else {
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

    // 3. FIX: Properly share variables globally without calling view() incorrectly
    $renderer = \Config\Services::renderer();
    $renderer->setVar('notifs', $notifs);
    $renderer->setVar('unreadCount', $unreadCount);
}

 
}
