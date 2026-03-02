<?php

namespace App\Controllers;

use App\Models\NotificationModel;   
use App\Models\LogModel; 
use App\Models\UserModel;

class Notifications extends BaseController
{

    public function index()
    {
        $notificationModel = new NotificationModel();
        $userModel = new UserModel(); 
        
        $data = [
            'notifications' => $notificationModel->orderBy('id', 'DESC')->findAll(),
            'users'         => $userModel->orderBy('fullname', 'ASC')->findAll() 
        ];

        return view('admin/notifications', $data);
    }

 public function store()
{
    $notificationModel = new \App\Models\NotificationModel();
    $userModel = new \App\Models\UserModel();
    
    $targetAudience = $this->request->getPost('target_audience');
    $message = $this->request->getPost('message');
    $type = $this->request->getPost('type');
    
    $recipientName = '';

    if ($targetAudience === 'all_users') {
        $recipientName = 'All Registered Users';
    } elseif ($targetAudience === 'all_admins') {
        $recipientName = 'All System Admins';
    } elseif ($targetAudience === 'specific_user') {
        $rawInput = $this->request->getPost('user_id'); // Format from datalist: "2026-001 | Renz Jericho Buday"
        
        if (strpos($rawInput, ' | ') !== false) {
            // Option A: Successfully picked from the datalist
            $parts = explode(' | ', $rawInput);
            $userId = trim($parts[0]); 
            $userName = trim($parts[1]);
            $recipientName = $userName . " (" . $userId . ")";
        } else {
            // Option B: Fallback - Search database using the 'user_id' column from your screenshot
            $userIdClean = trim($rawInput);
            $user = $userModel->where('user_id', $userIdClean)->first();
            
            if ($user) {
                // Found them! Use the fullname from your DB
                $recipientName = $user['fullname'] . " (" . $user['user_id'] . ")";
            } else {
                // Not found in DB, just show the ID they typed
                $recipientName = "User ID: " . $userIdClean;
            }
        }
    }

    $data = [
        'target_audience' => $targetAudience,
        'recipient'       => $recipientName,
        'type'            => $type,
        'message'         => $message,
        'status'          => 'unread'
    ];

    if ($notificationModel->insert($data)) {
        return redirect()->back()->with('success', 'Notification sent to ' . $recipientName);
    }
    
    return redirect()->back()->with('error', 'Failed to send notification.');
}

    public function delete()
    {
        $model = new NotificationModel();
        $id = $this->request->getPost('id');
        if ($model->delete($id)) {
            return redirect()->back()->with('success', 'Notification deleted.');
        }
        return redirect()->back()->with('error', 'Delete failed.');
    }
}