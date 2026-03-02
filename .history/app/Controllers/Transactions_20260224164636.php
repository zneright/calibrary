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
        $transactionModel = new TransactionModel();
        // 1. Load the User Model to find the email
        $userModel = new \App\Models\UserModel(); 
        
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            // 2. Fetch the user's actual email from the users table
            $user = $userModel->where('user_id_num', $trans['user_id_num'])->first();
            $recipientEmail = $user ? $user['email'] : null;

            if (!$recipientEmail) {
                return redirect()->back()->with('error', 'Could not find an email address for this user.');
            }

            $transactionModel->update($id, ['status' => 'Approved']);
            
            $this->logAction("Approved '{$trans['collection_title']}'. Status: Ready for Pickup.");
            
            // 3. Send to the REAL email, not the ID
            $this->sendEmail(
                $recipientEmail, 
                "Resource Ready for Pickup", 
                "Hi {$trans['user_name']}, your item <b>'{$trans['collection_title']}'</b> is ready for pickup!"
            );

            return redirect()->back()->with('success', 'Approved! Notification sent to ' . $recipientEmail);
        }
        return redirect()->back()->with('error', 'Not found.');
    }

    public function handover()
    {
        $transactionModel = new TransactionModel();
        $collectionModel = new \App\Models\CollectionModel();
        $userModel = new \App\Models\UserModel();

        $id = $this->request->getPost('id');
        $dueDate = $this->request->getPost('due_date');
        $trans = $transactionModel->find($id);

        if ($trans) {
            // Fetch the user's email
            $user = $userModel->where('user_id_num', $trans['user_id_num'])->first();
            $recipientEmail = $user ? $user['email'] : null;

            $transactionModel->update($id, [
                'status' => 'Borrowed',
                'due_date' => $dueDate
            ]);
            $collectionModel->update($trans['collection_id'], ['status' => 'BORROWED']);

            if ($recipientEmail) {
                $this->sendEmail(
                    $recipientEmail, 
                    "Resource Checkout Confirmed", 
                    "You claimed <b>'{$trans['collection_title']}'</b>. Return by: <b>" . date('M d, Y', strtotime($dueDate)) . "</b>."
                );
            }

            return redirect()->back()->with('success', 'Handover complete. Email sent.');
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