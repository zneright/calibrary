<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\CollectionModel;
use App\Models\LogModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Transactions extends BaseController
{
    public function index()
    {
        $transactionModel = new TransactionModel();
        $data = [
            // Oldest first so Admin can process early requests first
            'transactions' => $transactionModel->orderBy('created_at', 'ASC')->findAll()
        ];
        return view('admin/transactions', $data); 
    }

    // STEP 1: Admin clicks Approve (Moves to Ready for Pickup)
    public function approve()
    {
        $transactionModel = new TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => 'Approved']);
            
            $this->logAction("Approved request for '{$trans['collection_title']}'. Status: Ready for Pickup.");
            
            // Email 1: Notification that it is ready
            $this->sendEmail(
                $trans['user_id_num'], 
                "Resource Ready for Pickup", 
                "Hi {$trans['user_name']}, your requested item <b>'{$trans['collection_title']}'</b> is now approved and ready for pickup at the library!"
            );

            return redirect()->back()->with('success', 'Approved! Pickup email sent.');
        }
        return redirect()->back()->with('error', 'Not found.');
    }

    // STEP 2: Admin clicks "Mark as On Hand" (Sets Due Date)
    // STEP 3: Admin clicks "Return" (Frees up the book)
    public function updateStatus()
    {
        $transactionModel = new TransactionModel();
        $collectionModel = new CollectionModel();
        
        $id = $this->request->getPost('id');
        $newStatus = $this->request->getPost('status');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $updateData = ['status' => $newStatus];

            if ($newStatus === 'Borrowed') {
                $dueDate = date('Y-m-d', strtotime('+3 days')); // Set 3-day limit
                $updateData['due_date'] = $dueDate;
                
                // Set book to BORROWED in collections table
                $collectionModel->update($trans['collection_id'], ['status' => 'BORROWED']);
                
                $this->logAction("Handed over '{$trans['collection_title']}' to {$trans['user_name']}. Due: $dueDate");

                // Email 2: Confirmed On Hand with Due Date
                $this->sendEmail(
                    $trans['user_id_num'], 
                    "Resource Checkout Confirmed", 
                    "You have successfully claimed <b>'{$trans['collection_title']}'</b>. Please return it on or before <b>" . date('M d, Y', strtotime($dueDate)) . "</b>."
                );
            }

            if ($newStatus === 'Returned') {
                $updateData['date_returned'] = date('Y-m-d');
                // Set book back to AVAILABLE
                $collectionModel->update($trans['collection_id'], ['status' => 'AVAILABLE']);
                $this->logAction("Resource '{$trans['collection_title']}' returned by {$trans['user_name']}.");
            }

            $transactionModel->update($id, $updateData);
            return redirect()->back()->with('success', "Transaction updated to $newStatus.");
        }
        return redirect()->back()->with('error', 'Update failed.');
    }

    private function logAction($details) {
        $logModel = new LogModel();
        $logModel->insert([
            'user_name' => session()->get('fullname'),
            'user_id_num' => session()->get('user_id'),
            'module' => 'Transactions', 'action' => 'Update', 'details' => $details
        ]);
    }

    private function sendEmail($to, $subject, $message) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'zneright2@gmail.com'; 
            $mail->Password = 'your-app-password'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('zneright2@gmail.com', 'CA Library');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();
        } catch (Exception $e) { }
    }
}