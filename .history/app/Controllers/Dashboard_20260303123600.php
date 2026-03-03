<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // --- 1. EXISTING STATS ---
        $data = [
            'totalCollections' => $db->table('collections')->countAllResults(),
            'totalJournals'    => $db->table('journals')->countAllResults(),
            'totalDecrees'     => $db->table('collections')->where('type', 'Presidential Decree')->countAllResults(),
            'totalOrders'      => $db->table('collections')->where('type', 'Executive Order')->countAllResults(),
            'pendingApproval'  => $db->table('transactions')->whereIn('status', ['Pending', 'Renewing'])->countAllResults(),
            'overdueItems'     => $db->table('transactions')->where('status', 'Borrowed')->where('due_date <', date('Y-m-d'))->countAllResults(),
        ];

        // --- 2. WEEKLY CHART DATA ---
        $weeks = [3, 2, 1, 0];
        $addedData = [];
        $borrowedData = [];
        $returnedData = [];

        foreach ($weeks as $w) {
            $addedData[] = $db->table('collections')
                ->where('YEARWEEK(created_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();

            $borrowedData[] = $db->table('transactions')
                ->where('YEARWEEK(created_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();
                
            $returnedData[] = $db->table('transactions')
                ->where('status', 'Returned')
                ->where('YEARWEEK(updated_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();
        }

        $data['addedData']    = json_encode($addedData);
        $data['borrowedData'] = json_encode($borrowedData);
        $data['returnedData'] = json_encode($returnedData);

        // --- 3. RECENT ACTIVITY ---
        $data['recentBorrowers'] = $db->table('transactions')
            ->select('transactions.*, users.fullname as borrower_name')
            ->join('users', 'users.user_id = transactions.user_id_num', 'left')
            ->orderBy('transactions.id', 'DESC')->limit(5)->get()->getResultArray();

        // --- 4. NEW ANALYTICS: MOST REQUESTED ---
        // Groups transactions by book to see which ones get borrowed the most
        $data['mostRequested'] = $db->table('transactions')
            ->select('collection_title, COUNT(*) as request_count')
            ->groupBy('collection_id, collection_title')
            ->orderBy('request_count', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // --- 5. NEW ANALYTICS: TOP SEARCHES ---
        // Counts the most popular search terms from the new search_logs table
        $data['topSearches'] = $db->table('search_logs')
            ->select('search_query, COUNT(*) as search_count')
            ->groupBy('search_query')
            ->orderBy('search_count', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // --- 6. NEW ANALYTICS: MOST VIEWED (CLICKED) ---
        // Grabs the top 5 most clicked items from collections
        $data['mostViewed'] = $db->table('collections')
            ->select('title, views, type')
            ->orderBy('views', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return view('admin/admindashboard', $data);
    }
}