<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index()
    {
        $data = [
            'users' => [] 
        ];

        return view('admin/users', $data);
    }

    public function store()
    {
        // Database logic for inserting the new user will go here
        
        return redirect()->to('/admin/users')->with('success', 'New user has been successfully added to the system!');
    }
}