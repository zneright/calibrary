<?php

namespace App\Controllers;

use App\Models\NotificationModel;   
use App\Models\LogModel; 
use App\Models\UserModel; // <-- ADDED THIS TO FETCH USERS!

class Notifications extends BaseController
{
    public function index()
    {
        $notificationModel = new NotificationModel();
        $userModel = new UserModel(); 
        
        $data = [
            'notifications' => $notificationModel->orderBy('id', 'DESC')->findAll(),
            // Fetch users so we can populate the searchable dropdown
            'users'         => $userModel->orderBy('fullname', 'ASC')->findAll() 
        ];

        return view('admin/notifications', $data);
    }

    public function store()
{
    $notificationModel = new NotificationModel();
    
    $targetAudience = $this->request->getPost('target_audience');
    $message = $this->request->getPost('message');
    
    $recipientName = '';
    if ($targetAudience === 'all_users') {
        $recipientName = 'All Registered Users';
    } elseif ($targetAudience === 'all_admins') {
        $recipientName = 'All System Admins';
    } elseif ($targetAudience === 'specific_user') {
        $rawInput = $this->request->getPost('user_id'); // e.g., "2026-001 | Renz Jericho"
        
        if (strpos($rawInput, ' | ') !== false) {
            $parts = explode(' | ', $rawInput);
            $userId = trim($parts[0]); 
            $userName = trim($parts[1]);
            // Now we save both ID and Name!
            $recipientName = "[$userId] $userName";
        } else {
            $recipientName = "User ID: " . $rawInput;
        }
    }

    $data = [
        'target_audience' => $targetAudience,
        'recipient'       => $recipientName,
        'type'            => $this->request->getPost('type'),
        'message'         => $message,
        'status'          => 'unread'
    ];

    if ($notificationModel->insert($data)) {
        // Log with the full name for better audit trails
        $adminName = session()->get('fullname');
        $logModel = new \App\Models\LogModel();
        $logModel->insert([
            'user_name'   => $adminName,
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Notifications',
            'action'      => 'Add',
            'details'     => "Admin ($adminName) sent a {$data['type']} notification to: $recipientName."
        ]);

        return redirect()->back()->with('success', 'Notification sent to ' . $recipientName);
    }
    
    return redirect()->back()->with('error', 'Failed to send notification.');
}

    public function delete()
    {
        $notificationModel = new NotificationModel();
        $id = $this->request->getPost('id');

        if ($notificationModel->delete($id)) {
            // --- LOG THE ACTION ---
            $adminName = session()->get('fullname');

            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Notifications',
                'action'      => 'Delete',
                'details'     => "Admin ($adminName) deleted a system notification."
            ]);

            return redirect()->back()->with('success', 'Notification deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete notification.');
        }
    }
}