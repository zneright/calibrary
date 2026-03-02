<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\LogModel;
// Import these since you already have them in your vendor folder
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Reports extends BaseController
{
    public function approve()
    {
        $transactionModel = new TransactionModel();
        $logModel = new LogModel();
        
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if (!$trans) {
            return redirect()->back()->with('error', 'Transaction not found.');
        }

        // 1. Update status to 'Approved'
        $transactionModel->update($id, [
            'status'   => 'Approved',
            'due_date' => date('Y-m-d', strtotime('+3 days'))
        ]);

        // 2. Log Activity
        $logModel->insert([
            'user_name'   => session()->get('fullname'),
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Transactions',
            'action'      => 'Update',
            'details'     => "Approved '{$trans['collection_title']}' for {$trans['user_name']}."
        ]);

        // 3. Trigger your existing PHPMailer Logic
        $this->sendApprovalEmail($trans);

        return redirect()->back()->with('success', 'Request Approved & Email Sent!');
    }

    private function sendApprovalEmail($trans)
    {
        $mail = new PHPMailer(true);
        try {
            // Use your existing SMTP settings here
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'your-app-password'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('zneright2@gmail.com', 'Library Admin');
            $mail->addAddress($trans['user_id_num']); // Assuming ID num is their email

            $mail->isHTML(true);
            $mail->Subject = 'Book Ready for Pickup';
            $mail->Body    = "Hi {$trans['user_name']}, your request for <b>{$trans['collection_title']}</b> is approved and ready for pickup!";

            $mail->send();
        } catch (Exception $e) {
            // Silent fail or log error
        }
    }
}