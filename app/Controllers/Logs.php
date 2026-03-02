<?php
namespace App\Controllers;
use App\Models\LogModel;

class Logs extends BaseController
{
    public function index()
    {
        $logModel = new LogModel();
        
        // Fetch all logs, ordering by the newest first
        $data = [
            'logs' => $logModel->orderBy('id', 'DESC')->findAll()
        ];

        return view('admin/logs', $data);
    }

    public function clear()
    {
        $logModel = new LogModel();
        
        // Truncate empties the entire table
        $logModel->truncate(); 
        
        return redirect()->to('/admin/logs')->with('success', 'System logs have been successfully cleared.');
    }
}