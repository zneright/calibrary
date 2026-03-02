<?php
namespace App\Controllers;
use App\Models\TransactionModel;
use App\Models\CollectionModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Transactions extends BaseController {
    
    public function index() {
        $model = new TransactionModel();
        $data['transactions'] = $model->orderBy('created_at', 'ASC')->findAll();
        return view('admin/transactions', $data);
    }

  public function approve()
    {
        $transactionModel = new \App\Models\TransactionModel();
        $userModel = new \App\Models\UserModel();
        
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $user = $userModel->where('user_id', $trans['user_id_num'])->first();
            $recipientEmail = $user ? $user['email'] : null;

            if (!$recipientEmail) {
                return redirect()->back()->with('error', 'Error: No email found for User ID ' . $trans['user_id_num']);
            }

            $transactionModel->update($id, ['status' => 'Approved']);
            $this->logAction("Approved '{$trans['collection_title']}'. Status: Ready for Pickup.");

            // Styled Email Content
            $subject = 'Resource Ready for Pickup - CALIS v2.0';
            $message = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;'>
                    <h3 style='color: #0d6efd;'>Good Day, {$trans['user_name']}!</h3>
                    <p style='color: #333;'>Your request for the resource: <b style='color: #0d6efd;'>\"{$trans['collection_title']}\"</b> has been approved.</p>
                    <p style='color: #333; background-color: #e7f1ff; padding: 10px; border-left: 5px solid #0d6efd;'>
                        <b>Status:</b> Ready for Pickup<br>
                        Please visit the Data Bank and Library Services (DBLS) to claim your item.
                    </p>
                    <p style='color: #6c757d; font-size: 0.9em;'>Please present your ID to the librarian upon arrival.</p>
                    <br>
                    <p style='color: #6c757d; font-size: 0.8em; border-top: 1px solid #dee2e6; padding-top: 10px;'>This is an automated message from the CALIS Library Portal.</p>
                </div>
            ";

            $this->sendEmail($recipientEmail, $subject, $message);

            return redirect()->back()->with('success', 'Approved! Pickup email sent to ' . $recipientEmail);
        }
        return redirect()->back()->with('error', 'Transaction not found.');
    }

    // STEP 2: Admin clicks "Mark as On Hand" (Sets Due Date)
    public function handover()
    {
        $transactionModel = new \App\Models\TransactionModel();
        $collectionModel = new \App\Models\CollectionModel();
        $userModel = new \App\Models\UserModel();

        $id = $this->request->getPost('id');
        $dueDate = $this->request->getPost('due_date');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $user = $userModel->where('user_id', $trans['user_id_num'])->first();
            $recipientEmail = $user ? $user['email'] : null;

            $transactionModel->update($id, [
                'status' => 'Borrowed',
                'due_date' => $dueDate
            ]);
            $collectionModel->update($trans['collection_id'], ['status' => 'BORROWED']);

            $this->logAction("Handed over '{$trans['collection_title']}' to {$trans['user_name']}. Due: $dueDate");

            if ($recipientEmail) {
                $formattedDate = date('M d, Y', strtotime($dueDate));
                $subject = 'Resource Checkout Confirmed - CALIS v2.0';
                $message = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;'>
                        <h3 style='color: #198754;'>Checkout Confirmed!</h3>
                        <p style='color: #333;'>You have successfully claimed the resource: <b style='color: #333;'>\"{$trans['collection_title']}\"</b>.</p>
                        <div style='background-color: #fff; padding: 15px; border-radius: 5px; border: 1px dashed #198754;'>
                            <p style='margin: 0; color: #d63384; font-size: 1.1em; text-align: center;'>
                                <b>Return Deadline: {$formattedDate}</b>
                            </p>
                        </div>
                        <p style='color: #333; margin-top: 15px;'>Please ensure the resource is returned on or before the due date to avoid any penalties.</p>
                        <br>
                        <p style='color: #6c757d; font-size: 0.8em; border-top: 1px solid #dee2e6; padding-top: 10px;'>Thank you for using the CALIS Library Portal!</p>
                    </div>
                ";
                $this->sendEmail($recipientEmail, $subject, $message);
            }

            return redirect()->back()->with('success', 'Book is now On Hand. Due date set to ' . $dueDate);
        }
        return redirect()->back()->with('error', 'Handover failed.');
    }

    // STAGE 3: Return
    public function processReturn() {
        $model = new TransactionModel();
        $colModel = new CollectionModel();
        $id = $this->request->getPost('id');
        $trans = $model->find($id);

        $model->update($id, ['status' => 'Returned', 'date_returned' => date('Y-m-d')]);
        $colModel->update($trans['collection_id'], ['status' => 'AVAILABLE']);

        return redirect()->back()->with('success', 'Book returned and available.');
    }

 private function sendEmail($to, $subject, $message) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'zneright2@gmail.com'; 
            $mail->Password = 'ivcfiqpztkwapymf';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('zneright2@gmail.com', 'CALIS Library');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            log_message('error', "Email failed: {$mail->ErrorInfo}");
            return false;
        }
    }
}