<?php

namespace App\Controllers;

class Journals extends BaseController
{
    public function index()
    {

        $data = [
            'journals' => [] 
        ];

        // This loads the journals.php view file we just made
        return view('admin/journals', $data);
    }

    public function store()
    {
        // DATABASE LOGI DITO
        

        return redirect()->to('/journals')->with('success', 'New journal added successfully!');
    }
}