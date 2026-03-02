<?php

namespace App\Controllers;

use App\Models\NotificationModel;   
use App\Models\LogModel; 
use App\Models\UserModel;

class Notifications extends BaseController
{
    //fetch all notifications and users for the datalist in the view
    public function index()
    {
        $notificationModel = new NotificationModel();
        $userModel = new UserModel(); 
        //fetch all notifications (newest first)
        $data = [
            'notifications' => $notificationModel->orderBy('id', 'DESC')->findAll(),
            'users'         => $userModel->orderBy('fullname', 'ASC')->findAll() 
        ];

        return view('admin/notifications', $data);
    }
    //saving new notification into db
    public function store()
    {
        $notificationModel = new NotificationModel();
        $userModel = new UserModel();
        // Get form data
        $targetAudience = $this->request->getPost('target_audience');
        $message = $this->request->getPost('message');
        $type = $this->request->getPost('type');
        
        $recipientName = '';
        //where to send
        if ($targetAudience === 'all_users') {
            $recipientName = 'All Registered Users';
        } elseif ($targetAudience === 'specific_user') {
            $rawInput = $this->request->getPost('user_id'); // Format: "ID | Name"
            
            if (strpos($rawInput, ' | ') !== false) {
                // Successfully picked from datalist
                $parts = explode(' | ', $rawInput);
                $userId = trim($parts[0]); 
                $userName = trim($parts[1]);
                // Formatting it so the Borrower's 'orLike' query finds it exactly inside the parentheses
                $recipientName = $userName . " (" . $userId . ")";
            } else {
                // User typed manually, try to find the name in DB
                $user = $userModel->where('user_id', trim($rawInput))->first();
                if ($user) {
                    $recipientName = $user['fullname'] . " (" . $user['user_id'] . ")";
                } else {
                    $recipientName = "User ID: (" . trim($rawInput) . ")";
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
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Notifications',
                'action'      => 'Add',
                'details'     => "Sent $type notification to: $recipientName"
            ]);

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