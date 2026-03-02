<?php
namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $data = [
            'title'         => 'Admin Dashboard',
            'borrowedCount' => $db->table('transactions')->where('status', 'Borrowed')->countAllResults(),
            'pendingCount'  => $db->table('transactions')->where('status', 'Pending')->countAllResults(),
            'overdueCount'  => $db->table('transactions')->where('status', 'Borrowed')->where('due_date <', $today)->countAllResults(),
            'activeBorrows' => $db->table('transactions')
                                ->select('transactions.*, collections.class, users.fullname as borrower_name')
                                ->join('collections', 'collections.id = transactions.collection_id', 'left')
                                ->join('users', 'users.user_id = transactions.user_id_num', 'left')
                                ->orderBy('transactions.id', 'DESC')->limit(10)->get()->getResultArray(),
            'today'         => $today
        ];

        return view('admin/admindashboard', $data); // FIX: Points to admindashboard.php
    }
}