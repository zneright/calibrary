<?php
namespace App\Controllers;

// Don't forget these imports at the top!
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Users extends BaseController
{
    public function index() 
    {
        // 1. Connect to your database Model
        $userModel = new UserModel();

        // 2. Fetch ALL real users from the MySQL database
        $realUsers = $userModel->findAll();

        // 3. Pass the real data to the View
        return view('admin/users', ['users' => $realUsers]);
    }

    public function approve()
    {
        $id = $this->request->getPost('id');
        $email = $this->request->getPost('email');
        $fullname = $this->request->getPost('fullname');

        $userModel = new UserModel();

        // Update database to active
        if ($userModel->update($id, ['is_verified' => 1])) {
            
            // Send the approval email
            $this->sendApprovalSuccessEmail($email, $fullname);
            
            return redirect()->back()->with('success', 'User has been approved and notified!');
        } else {
            return redirect()->back()->with('error', 'Failed to approve user.');
        }
    }

    private function sendApprovalSuccessEmail($recipientEmail, $recipientName)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'ivcfiqpztkwapymf';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('zneright2@gmail.com', 'CALIS Admin');
            $mail->addAddress($recipientEmail, $recipientName);

            $loginLink = base_url("login");
            $mail->isHTML(true);
            $mail->Subject = 'Account Approved! - CALIS v2.0';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
                    <h3 style='color: #198754;'>Great news, {$recipientName}!</h3>
                    <p style='color: #333;'>Your account for the Data Bank and Library Services (DBLS) has been approved by a System Administrator.</p>
                    <p style='color: #333;'>You now have full access to the portal. You can log in immediately using your ID and the password you created.</p>
                    <br>
                    <a href='{$loginLink}' style='background-color:#0d6efd; color:white; padding:12px 24px; text-decoration:none; border-radius:5px; font-weight: bold;'>Log In Now</a>
                    <br><br><br>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            log_message('error', "Mail could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}