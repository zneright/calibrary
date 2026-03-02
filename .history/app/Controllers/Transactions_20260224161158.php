<?php

namespace App\Controllers;

class Transactions extends BaseController
{
public function index()
{
    $data = [
        'transactions' => []
    ];

    // Tell CodeIgniter to look inside the admin folder!
    return view('admin/transactions', $data); 
}

    public function store()
    {
        // Database saving logic goes here later
        return redirect()->to('admin/transactions')->with('success', 'Transaction successfully recorded!');
    }
}