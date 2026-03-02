<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // --- Existing Counts ---
        $totalCollections = $db->table('collections')->countAllResults();
        $totalJournals    = $db->table('journals')->countAllResults();
        $totalDecrees     = $db->table('collections')->where('type', 'Presidential Decree')->countAllResults();
        $totalOrders      = $db->table('collections')->where('type', 'Executive Order')->countAllResults();
        $totalTransactions = $db->table('transactions')->countAllResults();
        $totalRequests     = $db->table('transactions')->where('status', 'Pending')->countAllResults();

        // --- Live Chart Data (Last 4 Weeks) ---
        // 1. Get counts of Collections Added per week
        $collectionsAdded = $db->query("
            SELECT COUNT(id) as count 
            FROM collections 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 4 WEEK)
            GROUP BY YEARWEEK(created_at) 
            ORDER BY YEARWEEK(created_at) ASC 
            LIMIT 4
        ")->getResultArray();

        // 2. Get counts of Transactions (Borrowed) per week
        $itemsBorrowed = $db->query("
            SELECT COUNT(id) as count 
            FROM transactions 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 4 WEEK)
            GROUP BY YEARWEEK(created_at) 
            ORDER BY YEARWEEK(created_at) ASC 
            LIMIT 4
        ")->getResultArray();

        // Flatten arrays for Chart.js [e.g., 5, 12, 8, 15]
        $addedData = array_column($collectionsAdded, 'count');
        $borrowedData = array_column($itemsBorrowed, 'count');

        // Pad with zeros if less than 4 weeks of data exists
        $addedData = array_pad($addedData, -4, 0);
        $borrowedData = array_pad($borrowedData, -4, 0);

        // --- Recent Borrowers ---
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
            'recentBorrowers'   => $recentBorrowers,
            'addedData'         => json_encode($addedData),
            'borrowedData'      => json_encode($borrowedData)
        ];

        return view('admin/admindashboard', $data);
    }
}