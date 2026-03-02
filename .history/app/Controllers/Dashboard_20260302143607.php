<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        //connect to db
        $db = \Config\Database::connect();
        
        //fetch data for dashboard cards
        $data = [
            'totalCollections' => $db->table('collections')->countAllResults(),
            'totalJournals'    => $db->table('journals')->countAllResults(),
            'totalDecrees'     => $db->table('collections')->where('type', 'Presidential Decree')->countAllResults(),
            'totalOrders'      => $db->table('collections')->where('type', 'Executive Order')->countAllResults(),
            
            // count pending approval
            'pendingApproval'  => $db->table('transactions')->whereIn('status', ['Pending', 'Renewing'])->countAllResults(),

            //count overdue items
            'overdueItems'     => $db->table('transactions')->where('status', 'Borrowed')->where('due_date <', date('Y-m-d'))->countAllResults(),
        ];

        //Showing activity over the last 4 weeks, week 0 is the current week
        $weeks = [3, 2, 1, 0];

        $addedData = [];
        $borrowedData = [];
        $returnedData = [];

        foreach ($weeks as $w) {
            // count Collections Added per week
            $addedData[] = $db->table('collections')
                ->where('YEARWEEK(created_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();

            // count Items Borrowed per week
            $borrowedData[] = $db->table('transactions')
                ->where('YEARWEEK(created_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();
                
            // count Items Returned per week
            $returnedData[] = $db->table('transactions')
                ->where('status', 'Returned')
                ->where('YEARWEEK(updated_at, 1) = YEARWEEK(DATE_SUB(NOW(), INTERVAL '.$w.' WEEK), 1)')
                ->countAllResults();
        }

        //fetch recent activity (last 5 transactions)
        $data['recentBorrowers'] = $db->table('transactions')
            ->select('transactions.*, users.fullname as borrower_name')
            ->join('users', 'users.user_id = transactions.user_id_num', 'left')
            ->orderBy('transactions.id', 'DESC')->limit(5)->get()->getResultArray();

        // Pass Chart Arrays to View
        $data['addedData']    = json_encode($addedData);
        $data['borrowedData'] = json_encode($borrowedData);
        $data['returnedData'] = json_encode($returnedData);

        return view('admin/admindashboard', $data);
    }
}