<?php

namespace App\Controllers;

class Logs extends BaseController
{
    public function index()
    {
        $data = [
            'logs' => [] // Empty array until we connect the database
        ];

        return view('admin/logs', $data);
    }

    public function clear()
    {
        // Database logic to truncate the logs table will go here later!
        
        // Redirect back with the global floating toaster
        return redirect()->to('/admin/logs')->with('success', 'System logs have been successfully cleared.');
    }
}