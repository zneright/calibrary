<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Ensure all these variables are actually fetched
        $data = [
            'totalCollections'  => $db->table('collections')->countAllResults(),
            'totalJournals'     => $db->table('collections')->where('type', 'Journal')->countAllResults(),
            'totalDecrees'      => $db->table('collections')->where('type', 'Presidential Decree')->countAllResults(),
            'totalOrders'       => $db->table('collections')->where('type', 'Executive Order')->countAllResults(),
            'totalTransactions' => $db->table('transactions')->countAllResults(),
            'totalRequests'     => $db->table('transactions')->where('status', 'Pending')->countAllResults(),
            'recentBorrowers'   => $db->table('transactions')
                                    ->select('transactions.*, users.fullname as borrower_name')
                                    ->join('users', 'users.user_id = transactions.user_id_num', 'left')
                                    ->orderBy('transactions.id', 'DESC')->limit(5)->get()->getResultArray()
        ];

        // IMPORTANT: Must use 'return'
        return view('admin/admindashboard', $data); 
    }
}