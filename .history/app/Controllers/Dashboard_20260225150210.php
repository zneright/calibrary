<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // --- Stats Count ---
        $data = [
            'totalCollections'  => $db->table('collections')->countAllResults(),
            'totalJournals'     => $db->table('journals')->countAllResults(),
            'totalDecrees'      => $db->table('collections')->where('type', 'Presidential Decree')->countAllResults(),
            'totalOrders'       => $db->table('collections')->where('type', 'Executive Order')->countAllResults(),
            'totalTransactions' => $db->table('transactions')->countAllResults(),
            'totalRequests'     => $db->table('transactions')->where('status', 'Pending')->countAllResults(),
        ];

        // --- Live Chart Data Logic ---
        // We fetch the count of items added and borrowed per week for the last 4 weeks
        $weeks = [3, 2, 1, 0];
        $addedData = [];
        $borrowedData = [];

        foreach ($weeks as $w) {
            // Collections Added per week
            $added = $db->table('collections')
                ->where('YEARWEEK(created_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();
            $addedData[] = $added;

            // Items Borrowed (Transactions) per week
            $borrowed = $db->table('transactions')
                ->where('YEARWEEK(created_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();
            $borrowedData[] = $borrowed;
        }

        // --- Recent Borrowers ---
        $data['recentBorrowers'] = $db->table('transactions')
            ->select('transactions.*, users.fullname as borrower_name')
            ->join('users', 'users.user_id = transactions.user_id_num', 'left')
            ->orderBy('transactions.id', 'DESC')->limit(5)->get()->getResultArray();

        // Pass Chart Arrays to View
        $data['addedData'] = json_encode($addedData);
        $data['borrowedData'] = json_encode($borrowedData);

        return view('admin/admindashboard', $data);
    }
}