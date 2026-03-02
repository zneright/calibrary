<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\LogModel;
use App\Models\CollectionModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Transactions extends BaseController
{
    public function index()
    {
        $transactionModel = new TransactionModel();
        $data = [
            'transactions' => $transactionModel->orderBy('created_at', 'ASC')->findAll() 
        ];
        return view('admin/transactions', $data);
    }

    // Step 1: Admin Approves (Status: Approved / Ready for Pickup)
    public function approve()
    {
        $transactionModel = new TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, [
                'status' => 'Approved',
                'due_date' => date('Y-m-d', strtotime('+3 days'))
            ]);

            $this->logAction("Approved request for '{$trans['collection_title']}'. Status: Ready for Pickup.");
            
            // Trigger your PHPMailer logic here
            $this->sendNotification($trans); 

            return redirect()->back()->with('success', 'Request approved and borrower notified!');
        }
        return redirect()->back()->with('error', 'Transaction not found.');
    }

    // Step 2 & 3: Mark as 'Borrowed' (On Hand) or 'Returned'
    public function updateStatus()
    {
        $transactionModel = new TransactionModel();
        $collectionModel = new CollectionModel();
        
        $id = $this->request->getPost('id');
        $newStatus = $this->request->getPost('status');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => $newStatus]);

            // If Mark as Returned, make book AVAILABLE again
            if ($newStatus === 'Returned') {
                $collectionModel->update($trans['collection_id'], ['status' => 'AVAILABLE']);
                $this->logAction("Marked '{$trans['collection_title']}' as Returned.");
            }
            
            // If Mark as Borrowed (On Hand), update book status to BORROWED
            if ($newStatus === 'Borrowed') {
                $collectionModel->update($trans['collection_id'], ['status' => 'BORROWED']);
                $this->logAction("Handed over '{$trans['collection_title']}' to borrower. Status: On Hand.");
            }

            return redirect()->back()->with('success', "Status updated to $newStatus.");
        }
        return redirect()->back()->with('error', 'Update failed.');
    }

    private function logAction($details)
    {
        $logModel = new LogModel();
        $logModel->insert([
            'user_name'   => session()->get('fullname'),
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Transactions',
            'action'      => 'Update',
            'details'     => $details
        ]);
    }

    private function sendNotification($trans) {
        // Your PHPMailer logic goes here...
        $mail = new PHPMailer(true);
        try {
            // Use your existing SMTP settings here
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'ivcfiqpztkwapymf'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('zneright2@gmail.com', 'CALIS Library');
            $mail->addAddress($trans['email']); 

            $mail->isHTML(true);
            $mail->Subject = 'Book Ready for Pickup';
            $mail->Body    = "Hi {$trans['user_name']}, your request for <b>{$trans['collection_title']}</b> is approved and ready for pickup!";

            $mail->send();
        } catch (Exception $e) {
            // Silent fail or log error
        }
    }
}