<?php
namespace App\Controllers;
use App\Models\TransactionModel;
use App\Models\CollectionModel;
use App\Models\NotificationModel; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Transactions extends BaseController {
    //fetch all transactions and display in view    
    public function index() {
        $model = new TransactionModel();
        //fetch all transactions (newest first)
        $data['transactions'] = $model->orderBy('created_at', 'ASC')->findAll();
        return view('admin/transactions', $data);
    }

    //creates a new in-app notifications for specific user
    private function sendNotification($userId, $type, $message) {
        $notifModel = new NotificationModel();
        $notifModel->insert([
            'target_audience' => 'user', 
            'recipient'       => $userId,
            'type'            => $type, 
            'message'         => $message,
            'status'          => 'unread'
        ]);
    }
    //sends email to user using PHPMailer
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
    //approve renewal request and update due date
    public function approveRenewal()
    {
        $transactionModel = new \App\Models\TransactionModel();
        $id = $this->request->getPost('id');
        $newDate = $this->request->getPost('new_due_date');
        
        $trans = $transactionModel->find($id);
        //update transaction status to Borrowed 
        if ($trans && $transactionModel->update($id, [
            'status' => 'Borrowed', 
            'due_date' => $newDate
        ])) {
            $this->logAction("Renewed book until $newDate.");
            
            // notify user
            $formattedDate = date('M d, Y', strtotime($newDate));
            $this->sendNotification($trans['user_id_num'], 'success', "Your renewal for '{$trans['collection_title']}' has been approved. New due date: {$formattedDate}.");

            return redirect()->back()->with('success', 'Renewal successful! New due date: ' . $newDate);
        }
        return redirect()->back()->with('error', 'Renewal failed.');
    }
    //approve a request from a user
    public function approve() {
        $transactionModel = new \App\Models\TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $recipientEmail = $this->getUserEmail($trans['user_id_num']);
            if (!$recipientEmail) return redirect()->back()->with('error', 'No email found.');

            //Approve the selected transaction
            $transactionModel->update($id, ['status' => 'Approved']);
            $this->logAction("Approved '{$trans['collection_title']}'. Status: Ready for Pickup.");

            // Send Notif & Email
            $this->sendNotification($trans['user_id_num'], 'success', "Your request for '{$trans['collection_title']}' is approved and Ready for Pickup.");
            
            $title = "Request Approved! 🎉";
            $content = "
                <p>Good Day, <strong>{$trans['user_name']}</strong>!</p>
                <p>We are happy to inform you that your request for the resource: <span style='color: #1a2942; font-weight: bold;'>\"{$trans['collection_title']}\"</span> has been approved.</p>
                <div style='background-color: #e7f1ff; border-left: 4px solid #0d6efd; padding: 15px; margin: 20px 0;'>
                    <strong>Next Step:</strong> Please visit the <strong>Data Bank and Library Services (DBLS)</strong> desk to claim your item. Bring your institutional ID.
                </div>";
            $this->sendEmail($recipientEmail, 'Resource Ready for Pickup - CALIS', $this->getEmailTemplate($title, $content));

            // AUTO-DECLINE other pending requests for the same book
            $duplicates = $transactionModel->where('collection_id', $trans['collection_id'])
                                           ->where('status', 'Pending')
                                           ->where('id !=', $id)
                                           ->findAll();

            foreach ($duplicates as $dup) {
                $transactionModel->update($dup['id'], ['status' => 'Rejected']);
                $this->logAction("Auto-declined request for '{$dup['collection_title']}' by {$dup['user_name']} due to prior approval.");

                // Send Notif & Email to declined users
                $this->sendNotification($dup['user_id_num'], 'danger', "Your request for '{$dup['collection_title']}' was declined because another user reserved it first.");

                $dupEmail = $this->getUserEmail($dup['user_id_num']);
                if ($dupEmail) {
                    $dupTitle = "Resource Unavailable";
                    $dupContent = "
                        <p>Good Day, <strong>{$dup['user_name']}</strong>,</p>
                        <p>We regret to inform you that your request for <strong>\"{$dup['collection_title']}\"</strong> could not be approved.</p>
                        <p>This item has already been reserved/approved for another user and is currently unavailable.</p>
                        <p>Please check back later or browse our library for alternative resources.</p>";
                    $this->sendEmail($dupEmail, 'Update on your Resource Request - CALIS', $this->getEmailTemplate($dupTitle, $dupContent));
                }
            }

            return redirect()->back()->with('success', 'Approved. Other pending requests for this item were auto-declined.');
        }
    }
    //confirm user physically received the book and update status to Borrowed
    public function handover() {
        $transactionModel = new \App\Models\TransactionModel();
        $colModel = new CollectionModel();

        $id = $this->request->getPost('id');
        $dueDate = $this->request->getPost('due_date');
        $trans = $transactionModel->find($id);

        if ($trans) {
            // Update Transaction to Borrowed
            $transactionModel->update($id, ['status' => 'Borrowed', 'due_date' => $dueDate]);
            
            // Update the Book Status in Collections Table
            $colModel->update($trans['collection_id'], ['status' => 'BORROWED']);

            // Send Notif & Email
            $formattedDate = date('M d, Y', strtotime($dueDate));
            $this->sendNotification($trans['user_id_num'], 'info', "You have checked out '{$trans['collection_title']}'. Please return it by {$formattedDate}.");

            $recipientEmail = $this->getUserEmail($trans['user_id_num']);
            if ($recipientEmail) {
                $title = "Book Checkout Confirmed";
                $content = "
                    <p>The checkout for <strong>\"{$trans['collection_title']}\"</strong> is complete.</p>
                    <div style='text-align: center; padding: 25px; background-color: #fff9e6; border: 1px solid #ffeeba; border-radius: 8px; margin: 25px 0;'>
                        <p style='margin: 0; font-size: 14px; color: #856404;'>PLEASE RETURN ON OR BEFORE:</p>
                        <h2 style='margin: 10px 0 0 0; color: #d63384; font-size: 28px;'>" . $formattedDate . "</h2>
                    </div>
                    <p style='font-size: 14px; color: #666;'>To avoid penalties, please ensure the resource is returned to the library by the deadline stated above.</p>";
                $this->sendEmail($recipientEmail, 'Checkout Confirmed - CALIS', $this->getEmailTemplate($title, $content));
            }
            
            return redirect()->back()->with('success', 'Handover successful. Item marked as Borrowed.');
        }
    }
    //confirm return of book 
    public function processReturn() {
        $model = new TransactionModel();
        $colModel = new CollectionModel();
        $id = $this->request->getPost('id');
        $trans = $model->find($id);

        if($trans) {
            $model->update($id, ['status' => 'Returned', 'date_returned' => date('Y-m-d')]);
            
            // Update the Book Status to Available
            $colModel->update($trans['collection_id'], ['status' => 'AVAILABLE']);

            // Send Notif
            $this->sendNotification($trans['user_id_num'], 'success', "Your return of '{$trans['collection_title']}' is complete. Thank you!");

            return redirect()->back()->with('success', 'Book returned and is now Available.');
        }
    }

    public function reportIssue() {
        $model = new TransactionModel();
        $colModel = new CollectionModel();
        
        $id = $this->request->getPost('id');
        $issueType = $this->request->getPost('issue_type'); 
        
        $trans = $model->find($id);

        if($trans) {
            $model->update($id, ['status' => $issueType]);
            $colModel->update($trans['collection_id'], ['status' => strtoupper($issueType)]);

            $this->logAction("Marked '{$trans['collection_title']}' as $issueType.");
            
            // Send Notif
            $this->sendNotification($trans['user_id_num'], 'warning', "The item '{$trans['collection_title']}' has been marked as {$issueType}. Please contact the library administrator.");

            return redirect()->back()->with('success', "Item marked as $issueType.");
        }
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
                
                $this->sendNotification($trans['user_id_num'], 'danger', "Your extension request for '{$trans['collection_title']}' was declined.");
            } else {
                $transactionModel->update($id, ['status' => 'Rejected']);
                $title = "Request Declined";
                $content = "<p>We regret to inform you that your request for <strong>\"{$trans['collection_title']}\"</strong> was not approved at this time.</p>";
                
                $this->sendNotification($trans['user_id_num'], 'danger', "Your request for '{$trans['collection_title']}' was declined.");
            }

            if ($recipientEmail) {
                $this->sendEmail($recipientEmail, 'Update on your Resource Request - CALIS', $this->getEmailTemplate($title, $content));
            }
            return redirect()->back()->with('success', 'Declined and email sent.');
        }
    }

    public function sendManualReminder() {
        $transactionModel = new \App\Models\TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $formattedDate = date('M d, Y', strtotime($trans['due_date']));
            
            // Send Notif
            $this->sendNotification($trans['user_id_num'], 'warning', "Reminder: Please return '{$trans['collection_title']}' on or before {$formattedDate} to avoid penalties.");

            // Send Email
            $recipientEmail = $this->getUserEmail($trans['user_id_num']);
            if ($recipientEmail) {
                $title = "Library Return Reminder";
                $content = "
                    <p>Hello {$trans['user_name']},</p>
                    <p>This is a friendly reminder to return the following item to the library:</p>
                    <p style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Resource:</strong> {$trans['collection_title']}</p>
                    <p style='padding: 10px; color: #dc3545;'><strong>Due Date:</strong> " . $formattedDate . "</p>
                    <p style='margin-top: 20px;'>If you have already returned this item, please disregard this message.</p>";

                $this->sendEmail($recipientEmail, 'Reminder: Item Due Back Soon', $this->getEmailTemplate($title, $content));
            }
            
            return redirect()->back()->with('success', 'Reminder sent.');
        }
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
        } catch (Exception $e) { return false; }
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

    private function getUserEmail($userIdNum) {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('user_id', $userIdNum)->first();
        return $user ? $user['email'] : null;
    }
}   