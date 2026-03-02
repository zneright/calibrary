<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // 1. Fetch Counts for Info Boxes
        $totalCollections = $db->table('collections')->countAllResults();
        
        // Count from the actual 'journals' table
        $totalJournals = $db->table('journals')->countAllResults();
        
        // Count specific types from collections table
        $totalDecrees = $db->table('collections')->where('type', 'Presidential Decree')->countAllResults();
        $totalOrders  = $db->table('collections')->where('type', 'Executive Order')->countAllResults();

        // 2. Fetch Counts for Small Boxes
        $totalTransactions = $db->table('transactions')->countAllResults();
        $totalRequests     = $db->table('transactions')->where('status', 'Pending')->countAllResults();

        // 3. Fetch Recent Borrowers
        $recentBorrowers = $db->table('transactions')
            ->select('transactions.*, users.fullname as borrower_name')
            ->join('users', 'users.user_id = transactions.user_id_num', 'left')
            ->orderBy('transactions.id', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $data = [
            'title'             => 'Admin Dashboard',
            'totalCollections'  => $totalCollections,
            'totalJournals'     => $totalJournals, 
            'totalDecrees'      => $totalDecrees,
            'totalOrders'       => $totalOrders,
            'totalTransactions' => $totalTransactions,
            'totalRequests'     => $totalRequests,
            'recentBorrowers'   => $recentBorrowers
        ];

        return view('admin/admindashboard', $data);
    }
}