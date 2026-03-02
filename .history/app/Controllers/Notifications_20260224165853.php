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
        
        // Figure out the display name for the "Recipient" column
        $recipientName = '';
        if ($targetAudience === 'all_users') {
            $recipientName = 'All Registered Users';
        } elseif ($targetAudience === 'all_admins') {
            $recipientName = 'All System Admins';
        } elseif ($targetAudience === 'specific_user') {
            // The datalist submits "ID | Fullname". We use explode() to split it and grab just the ID!
            $rawInput = $this->request->getPost('user_id');
            $parts = explode(' | ', $rawInput);
            $userId = trim($parts[0]); 
            
            $recipientName = 'User ID: ' . $userId;
        }

        $data = [
            'target_audience' => $targetAudience,
            'recipient'       => $recipientName,
            'type'            => $this->request->getPost('type'),
            'message'         => $message,
            'status'          => 'unread'
        ];

        if ($notificationModel->insert($data)) {
            // --- LOG THE ACTION ---
            $adminName = session()->get('fullname');

            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Notifications',
                'action'      => 'Add',
                'details'     => "Admin ($adminName) dispatched a notification to: $recipientName."
            ]);

            return redirect()->back()->with('success', 'Notification sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to send notification.');
        }
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