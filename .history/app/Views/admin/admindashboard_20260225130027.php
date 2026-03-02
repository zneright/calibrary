<?php
namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Top Row Info Boxes (Collections by Type)
        $totalCollections = $db->table('collections')->countAllResults();
        $totalJournals     = $db->table('collections')->where('type', 'Journal')->countAllResults();
        $totalDecrees      = $db->table('collections')->where('type', 'Presidential Decree')->countAllResults();
        $totalOrders       = $db->table('collections')->where('type', 'Executive Order')->countAllResults();

        // Middle Row Small Boxes (Transaction Totals)
        $totalTransactions = $db->table('transactions')->countAllResults();
        $totalRequests     = $db->table('transactions')->where('status', 'Pending')->countAllResults();

        // Right Sidebar (Recent Borrowers)
        // Adjust column names 'collection_title' and 'user_id_num' based on your actual table
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