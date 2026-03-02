<?php

namespace App\Controllers;

class Notifications extends BaseController
{
    public function index()
    {
        // 1. Mock Data array that matches your View's foreach loop
        $data = [
            'notifications' => [
                [
                    'id'              => 1,
                    'recipient'       => 'User: 2024-0192',
                    'target_audience' => 'specific_user',
                    'type'            => 'alert',
                    'message'         => 'Please return "Data Structures" immediately. It is 2 days overdue.',
                    'status'          => 'unread',
                    'created_at'      => '2026-02-18 10:30:00'
                ],
                [
                    'id'              => 2,
                    'recipient'       => 'All Admins',
                    'target_audience' => 'all_admins',
                    'type'            => 'info',
                    'message'         => 'Server maintenance scheduled for tonight at 11:00 PM.',
                    'status'          => 'read',
                    'created_at'      => '2026-02-18 09:15:00'
                ],
                [
                    'id'              => 3,
                    'recipient'       => 'All Registered Users',
                    'target_audience' => 'all_users',
                    'type'            => 'warning',
                    'message'         => 'The library will be closed this Friday due to a local holiday.',
                    'status'          => 'unread',
                    'created_at'      => date('Y-m-d H:i:s') // Today's date
                ]
            ]
        ];

        return view('admin/notifications', $data);
    }

    public function store()
    {
        // You can grab the data like this when you are ready:
        // $target_audience = $this->request->getPost('target_audience');
        // $message = $this->request->getPost('message');
        
        // TODO: This is where you will put your database insert logic
        
        return redirect()->to('/notifications')->with('success', 'Notification sent successfully!');
    }

    // 2. The method to handle your new Delete Modal
    public function delete()
    {
        // Grab the hidden ID sent from the modal
        $id = $this->request->getPost('id');
        
        // TODO: This is where you will put your database delete logic
        // Example: $notificationModel->delete($id);
        
        return redirect()->to('/notifications')->with('success', 'Notification deleted successfully!');
    }
}