<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\LogModel;
use App\Models\CollectionModel;

class Transactions extends BaseController
{
    public function index()
    {
        $transactionModel = new TransactionModel();
        
        $data = [
            // Oldest first as requested, to handle first-come-first-served
            'transactions' => $transactionModel->orderBy('created_at', 'ASC')->findAll() 
        ];

        return view('admin/transactions', $data);
    }

    // Handles the status updates (Approve, Reject, Return)
    public function updateStatus()
    {
        $transactionModel = new TransactionModel();
        $collectionModel = new CollectionModel();
        $logModel = new LogModel();

        $id = $this->request->getPost('id');
        $newStatus = $this->request->getPost('status');
        $trans = $transactionModel->find($id);

        if (!$trans) return redirect()->back()->with('error', 'Transaction not found.');

        $updateData = ['status' => $newStatus];

        // If returning, set the return date and set book back to AVAILABLE
        if ($newStatus === 'Returned') {
            $updateData['date_returned'] = date('Y-m-d');
            $collectionModel->update($trans['collection_id'], ['status' => 'AVAILABLE']);
        }
        
        // If approving, set the book to BORROWED
        if ($newStatus === 'Borrowed') {
            $collectionModel->update($trans['collection_id'], ['status' => 'BORROWED']);
        }

        if ($transactionModel->update($id, $updateData)) {
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Transactions',
                'action'      => 'Update',
                'details'     => "Admin updated Trans ID: $id to $newStatus for borrower: {$trans['user_name']}."
            ]);

            return redirect()->back()->with('success', "Transaction updated to $newStatus.");
        }
    }
}