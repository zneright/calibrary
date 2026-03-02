<?php

namespace App\Controllers;

class Notifications extends BaseController
{
    public function index()
    {
        $data = [
            'notifications' => []
        ];

        return view('admin/notifications', $data);
    }

    public function store()
    {
            //this is where you will put your database ngga
        return redirect()->to('/notifications')->with('success', 'Notification sent successfully!');
    }
}