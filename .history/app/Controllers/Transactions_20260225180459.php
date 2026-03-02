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
private function getEmailTemplate($title, $content) {
        return "
        <div style='background-color: #f4f7f9; padding: 40px 10px; font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                <div style='background-color: #1a2942; padding: 30px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px;'>CALIS v2.0</h1>
                    <p style='color: rgba(255,255,255,0.7); margin: 5px 0 0 0; font-size: 14px;'>Library Management System</p>
                </div>
                <div style='padding: 40px 30px;'>
                    <h2 style='color: #333333; margin-top: 0; font-size: 20px;'>$title</h2>
                    <div style='color: #555555; line-height: 1.6; font-size: 16px;'>
                        $content
                    </div>
                    <hr style='border: none; border-top: 1px solid #eeeeee; margin: 30px 0;'>
                    <p style='color: #888888; font-size: 13px; text-align: center; margin-bottom: 0;'>
                        Commission on Appointments - Library Services<br>
                        This is an automated message. Please do not reply.
                    </p>
                </div>
            </div>
        </div>";
    }
public function approveRenewal()
{
    $transactionModel = new \App\Models\TransactionModel();
    $id = $this->request->getPost('id');
    $newDate = $this->request->getPost('new_due_date');

    if ($transactionModel->update($id, [
        'status' => 'Borrowed', 
        'due_date' => $newDate
    ])) {
        $this->logAction("Renewed book until $newDate.");
        return redirect()->back()->with('success', 'Renewal successful! New due date: ' . $newDate);
    }
    return redirect()->back()->with('error', 'Renewal failed.');
}

 public function approve() {
        $transactionModel = new \App\Models\TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $recipientEmail = $this->getUserEmail($trans['user_id_num']);
            if (!$recipientEmail) return redirect()->back()->with('error', 'No email found.');

            $transactionModel->update($id, ['status' => 'Approved']);
            $this->logAction("Approved '{$trans['collection_title']}'. Status: Ready for Pickup.");

            $title = "Request Approved! 🎉";
            $content = "
                <p>Good Day, <strong>{$trans['user_name']}</strong>!</p>
                <p>We are happy to inform you that your request for the resource: <span style='color: #1a2942; font-weight: bold;'>\"{$trans['collection_title']}\"</span> has been approved.</p>
                <div style='background-color: #e7f1ff; border-left: 4px solid #0d6efd; padding: 15px; margin: 20px 0;'>
                    <strong>Next Step:</strong> Please visit the <strong>Data Bank and Library Services (DBLS)</strong> desk to claim your item. Bring your institutional ID.
                </div>";

            $this->sendEmail($recipientEmail, 'Resource Ready for Pickup - CALIS', $this->getEmailTemplate($title, $content));
            return redirect()->back()->with('success', 'Approved and email sent.');
        }
    }

    public function handover() {
        $transactionModel = new \App\Models\TransactionModel();
        $id = $this->request->getPost('id');
        $dueDate = $this->request->getPost('due_date');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => 'Borrowed', 'due_date' => $dueDate]);
            $recipientEmail = $this->getUserEmail($trans['user_id_num']);

            $title = "Book Checkout Confirmed";
            $content = "
                <p>The checkout for <strong>\"{$trans['collection_title']}\"</strong> is complete.</p>
                <div style='text-align: center; padding: 25px; background-color: #fff9e6; border: 1px solid #ffeeba; border-radius: 8px; margin: 25px 0;'>
                    <p style='margin: 0; font-size: 14px; color: #856404;'>PLEASE RETURN ON OR BEFORE:</p>
                    <h2 style='margin: 10px 0 0 0; color: #d63384; font-size: 28px;'>" . date('M d, Y', strtotime($dueDate)) . "</h2>
                </div>
                <p style='font-size: 14px; color: #666;'>To avoid penalties, please ensure the resource is returned to the library by the deadline stated above.</p>";

            $this->sendEmail($recipientEmail, 'Checkout Confirmed - CALIS', $this->getEmailTemplate($title, $content));
            return redirect()->back()->with('success', 'Handover successful.');
        }
    }

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
    private function logAction($details) 
{
    $logModel = new \App\Models\LogModel();
    $logModel->insert([
        'user_name'   => session()->get('fullname'),
        'user_id_num' => session()->get('user_id'),
        'module'      => 'Transactions', 
        'action'      => 'Update', 
        'details'     => $details
    ]);
}
public function reject() {
        $transactionModel = new \App\Models\TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $recipientEmail = $this->getUserEmail($trans['user_id_num']);
            if ($trans['status'] === 'Renewing') {
                $transactionModel->update($id, ['status' => 'Borrowed']);
                $title = "Extension Request Declined";
                $content = "<p>Your request to extend the duration for <strong>\"{$trans['collection_title']}\"</strong> was declined.</p>
                            <p>Please return the item by its original due date: <strong>" . date('M d, Y', strtotime($trans['due_date'])) . "</strong>.</p>";
            } else {
                $transactionModel->update($id, ['status' => 'Rejected']);
                $title = "Request Declined";
                $content = "<p>We regret to inform you that your request for <strong>\"{$trans['collection_title']}\"</strong> was not approved at this time.</p>";
            }
            $this->sendEmail($recipientEmail, 'Update on your Resource Request - CALIS', $this->getEmailTemplate($title, $content));
            return redirect()->back()->with('success', 'Declined and email sent.');
        }
    }

public function sendManualReminder()
{
    $transactionModel = new \App\Models\TransactionModel();
    $userModel = new \App\Models\UserModel();
    
    $id = $this->request->getPost('id');
    $trans = $transactionModel->find($id);

    if ($trans) {
        $user = $userModel->where('user_id', $trans['user_id_num'])->first();
        
        if ($user && !empty($user['email'])) {
            $dueDate = date('M d, Y', strtotime($trans['due_date']));
            $subject = 'Reminder: Library Resource Return - CALIS';
            $message = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px;'>
                    <h3 style='color: #1e3a8a;'>Return Reminder</h3>
                    <p>Hello <b>{$trans['user_name']}</b>,</p>
                    <p>This is a reminder to return the resource: <b>\"{$trans['collection_title']}\"</b>.</p>
                    <p style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'>
                        <b>Due Date:</b> {$dueDate}
                    </p>
                    <p>Please return it to the Data Bank and Library Services (DBLS) as soon as possible.</p>
                </div>";

            if ($this->sendEmail($user['email'], $subject, $message)) {
                return redirect()->back()->with('success', 'Reminder email sent to ' . $user['email']);
            } else {
                return redirect()->back()->with('error', 'Failed to send email. Check SMTP settings.');
            }
        }
        return redirect()->back()->with('error', 'No email found for this user.');
    }
    return redirect()->back()->with('error', 'Transaction not found.');
}
private function getUserEmail($userIdNum) {
    $userModel = new \App\Models\UserModel();
    $user = $userModel->where('user_id', $userIdNum)->first();
    return $user ? $user['email'] : null;
}
}