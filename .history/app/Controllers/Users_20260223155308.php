<?php
namespace App\Controllers;

class Users extends BaseController{
    public function users() // Or whatever you named the method that loads the users page
    {
        // 1. Create the Mock Data Array
        $mockUsers = [
            [
                'id'         => 1,
                'user_id'    => 'ADMIN-001',
                'fullname'   => 'Bien',
                'department' => 'System Administrator',
                'role'       => 'Admin'
            ],
            [
                'id'         => 2,
                'user_id'    => '2026-0451',
                'fullname'   => 'Nishia Pinlac',
                'department' => 'Student Trainee - DBLS',
                'role'       => 'Borrower'
            ],
            [
                'id'         => 3,
                'user_id'    => '2026-0452',
                'fullname'   => 'Renz Jericho Buday',
                'department' => 'Student Trainee - DBLS',
                'role'       => 'Borrower'
            ]
        ];

        // 2. Pass the data to the View
        // The array key 'users' becomes the $users variable in your HTML foreach loop
        return view('admin/users', ['users' => $mockUsers]);
    }

    // ... your update() method is down here ...
}