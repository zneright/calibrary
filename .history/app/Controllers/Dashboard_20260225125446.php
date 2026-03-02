<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Fetch counts for the Admin Dashboard Cards
        $borrowedCount = $db->table('transactions')->where('status', 'Borrowed')->countAllResults();
        $pendingCount  = $db->table('transactions')->where('status', 'Pending')->countAllResults();
        $overdueCount  = $db->table('transactions')
                            ->where('status', 'Borrowed')
                            ->where('due_date <', $today)
                            ->countAllResults();

        // Fetch recent transactions for the table
        $activeBorrows = $db->table('transactions')
            ->select('transactions.*, collections.class, users.fullname as borrower_name')
            ->join('collections', 'collections.id = transactions.collection_id', 'left')
            ->join('users', 'users.user_id = transactions.user_id_num', 'left')
            ->orderBy('transactions.id', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $data = [
            'title'         => 'Admin Dashboard',
            'borrowedCount' => $borrowedCount,
            'pendingCount'  => $pendingCount,
            'overdueCount'  => $overdueCount,
            'activeBorrows' => $activeBorrows,
            'today'         => $today
        ];

        return view('admin/admindashboard', $data);
    }
}