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
            $rawInput = $this->request->getPost('user_id');
            
            //check if they clicked from option or typed
            if (strpos($rawInput, ' | ') !== false) {
                $parts = explode(' | ', $rawInput);
                $userId = trim($parts[0]); 
                $userName = trim($parts[1]);
                // format recipient name as "Full Name (User ID)"
                $recipientName = $userName . " (" . $userId . ")";
            } else {
                // typed manually, try to find user by ID
                $user = $userModel->where('user_id', trim($rawInput))->first();
                if ($user) {
                    $recipientName = $user['fullname'] . " (" . $user['user_id'] . ")";
                } else {
                    $recipientName = "User ID: (" . trim($rawInput) . ")";
                }
            }
        }
        //prep for db
        $data = [
            'target_audience' => $targetAudience,
            'recipient'       => $recipientName,
            'type'            => $type,
            'message'         => $message,
            'status'          => 'unread'
        ];
        //save notif
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

    //re
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