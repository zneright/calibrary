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
            // Find the user by their ID number stored in the transaction
            // We use 'user_id' because that's the field in your UserModel
            $user = $userModel->where('user_id', $trans['user_id_num'])->first();

            if (!$user || empty($user['email'])) {
                return redirect()->back()->with('error', 'Error: No email found for User ID ' . $trans['user_id_num']);
            }

            $recipientEmail = $user['email'];

            // Update status
            $transactionModel->update($id, ['status' => 'Approved']);
            
            $this->logAction("Approved '{$trans['collection_title']}'. Notification sent to $recipientEmail.");
            
            // Send the email to the actual email address found
            $this->sendEmail(
                $recipientEmail, 
                "Resource Ready for Pickup", 
                "Hi {$trans['user_name']}, your item <b>'{$trans['collection_title']}'</b> is approved and ready for pickup!"
            );

            return redirect()->back()->with('success', 'Approved! Email sent to ' . $recipientEmail);
        }
        return redirect()->back()->with('error', 'Transaction not found.');
    }

    public function handover()
    {
        $transactionModel = new \App\Models\TransactionModel();
        $collectionModel = new \App\Models\CollectionModel();
        $userModel = new \App\Models\UserModel();

        $id = $this->request->getPost('id');
        $dueDate = $this->request->getPost('due_date');
        $trans = $transactionModel->find($id);

        if ($trans) {
            // Find user email again for the handover confirmation
            $user = $userModel->where('user_id', $trans['user_id_num'])->first();
            $recipientEmail = ($user) ? $user['email'] : null;

            $transactionModel->update($id, [
                'status' => 'Borrowed',
                'due_date' => $dueDate
            ]);
            
            $collectionModel->update($trans['collection_id'], ['status' => 'BORROWED']);

            if ($recipientEmail) {
                $this->sendEmail(
                    $recipientEmail, 
                    "Resource Checkout Confirmed", 
                    "You have claimed <b>'{$trans['collection_title']}'</b>. Please return it by: <b>" . date('M d, Y', strtotime($dueDate)) . "</b>."
                );
            }

            return redirect()->back()->with('success', 'Handover complete. Due date set to ' . $dueDate);
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
        // --- ADD DEBUGGING ---
        $mail->SMTPDebug = 2; // 2 = Detailed client/server messages
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'zneright2@gmail.com'; 
        $mail->Password   = 'ivcfiqpztkwapymf'; 
        $mail->SMTPSecure = 'tls'; // Try 'ssl' if tls fails
        $mail->Port       = 587;   // Use 465 if you change to ssl

        $mail->setFrom('zneright2@gmail.com', 'CA Library');
        $mail->addAddress($to);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        $mail->send();
        // If it works, it will print a lot of text then stop.
    } catch (Exception $e) {
        // This will print the EXACT error on your screen
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        die(); 
    }
}
}