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

    // STAGE 1: Approve (Ready for Pickup)
    public function approve() {
        $model = new TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $model->find($id);

        $model->update($id, ['status' => 'Approved']);
        $this->sendEmail($trans['user_id_num'], "Ready for Pickup", "Your request for '{$trans['collection_title']}' is ready for pickup!");
        
        return redirect()->back()->with('success', 'Approved! Notification sent.');
    }

    // STAGE 2: Pick up (Set Due Date & Mark On Hand)
    public function handover() {
        $model = new TransactionModel();
        $colModel = new CollectionModel();
        $id = $this->request->getPost('id');
        $dueDate = $this->request->getPost('due_date');
        $trans = $model->find($id);

        $model->update($id, [
            'status' => 'Borrowed',
            'due_date' => $dueDate
        ]);
        $colModel->update($trans['collection_id'], ['status' => 'BORROWED']);

        $this->sendEmail($trans['user_id_num'], "On Hand", "You have picked up '{$trans['collection_title']}'. Due date: $dueDate");

        return redirect()->back()->with('success', 'Book handed over. Due date set.');
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