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
            $rawInput = $this->request->getPost('user_id');
            if (strpos($rawInput, ' | ') !== false) {
                $parts = explode(' | ', $rawInput);
                $userId = trim($parts[0]); 
                $userName = trim($parts[1]);
                $recipientName = $userName . " (" . $userId . ")";
            } else {
                $userIdClean = trim($rawInput);
                $user = $userModel->where('user_id', $userIdClean)->first();
                $recipientName = $user ? $user['fullname'] . " (" . $user['user_id'] . ")" : "User ID: " . $userIdClean;
            }
        }

        $data = [
            'target_audience' => $targetAudience,
            'recipient'       => $recipientName,
            'type'            => $type,
            'message'         => $message,
            'status'          => 'unread' // Fixed column name
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