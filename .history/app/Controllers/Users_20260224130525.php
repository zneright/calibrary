<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LogModel; // Added LogModel import
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Users extends BaseController
{
    public function index() 
    {
        $userModel = new UserModel();
        $realUsers = $userModel->findAll();
        
        return view('admin/users', ['users' => $realUsers]);
    }

   public function store()
    {
        $userModel = new UserModel();
        $fullname = $this->request->getPost('fullname');
        $user_id = $this->request->getPost('user_id');
        
        $data = [
            'fullname'    => $fullname,
            'user_id'     => $user_id,
            'email'       => $this->request->getPost('email'),
            'role'        => $this->request->getPost('role'),
            'department'  => $this->request->getPost('department'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_verified' => 1 
        ];

        if ($userModel->insert($data)) {
            // Grab the Admin's name from the active session
            $adminName = session()->get('fullname');

            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'User Management',
                'action'      => 'Create',
                'details'     => "Admin ($adminName) manually created user account for $fullname ($user_id)."
            ]);

            return redirect()->back()->with('success', 'New user added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add user.');
        }
    }

    public function update()
    {
        $userModel = new UserModel();
        $id = $this->request->getPost('id');
        $fullname = $this->request->getPost('fullname');
        $user_id = $this->request->getPost('user_id');

        $data = [
            'fullname'   => $fullname,
            'user_id'    => $user_id,
            'role'       => $this->request->getPost('role'),
            'department' => $this->request->getPost('department')
        ];

        if ($userModel->update($id, $data)) {
            // Grab the Admin's name from the active session
            $adminName = session()->get('fullname');

            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'), 
                'module'      => 'User Management',
                'action'      => 'Update',
                'details'     => "Admin ($adminName) updated profile information for user $fullname ($user_id)."
            ]);

            return redirect()->back()->with('success', 'User updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }

    // APPROVE PENDING USER
    public function approve()
    {
        $id = $this->request->getPost('id');
        $email = $this->request->getPost('email');
        $fullname = $this->request->getPost('fullname');

        $userModel = new UserModel();

        if ($userModel->update($id, ['is_verified' => 1])) {
            // --- LOG THE ACTION ---
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'), 
                'module'      => 'User Management',
                'action'      => 'Approve',
                'details'     => "Admin approved system access for $fullname ($email)."
            ]);
            // ----------------------
            $this->sendEmailNotification($email, $fullname, 'approved');
            return redirect()->back()->with('success', 'User has been approved and notified via email!');
        } else {
            return redirect()->back()->with('error', 'Failed to approve user.');
        }
    }

    // DELETE / REJECT USER
    public function delete()
    {
        $id = $this->request->getPost('id');
        $email = $this->request->getPost('email');
        $fullname = $this->request->getPost('fullname');

        $userModel = new UserModel();

        if ($userModel->delete($id)) {
            // --- LOG THE ACTION ---
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'), 
                'module'      => 'User Management',
                'action'      => 'Delete',
                'details'     => "Admin rejected/deleted account for $fullname ($email)."
            ]);
            // ----------------------
            $this->sendEmailNotification($email, $fullname, 'rejected');
            return redirect()->back()->with('success', 'User account has been deleted and notified via email.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }

    private function sendEmailNotification($recipientEmail, $recipientName, $actionType)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'ivcfiqpztkwapymf';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('zneright2@gmail.com', 'CALIS Admin');
            $mail->addAddress($recipientEmail, $recipientName);
            $mail->isHTML(true);

            // Determine which email template to send
            if ($actionType === 'approved') {
                $loginLink = base_url("login");
                $mail->Subject = 'Account Approved! - CALIS v2.0';
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
                        <h3 style='color: #198754;'>Great news, {$recipientName}!</h3>
                        <p style='color: #333;'>Your account for the Data Bank and Library Services (DBLS) has been approved by a System Administrator.</p>
                        <p style='color: #333;'>You now have full access to the portal. You can log in immediately using your email address and the password you created.</p>
                        <br>
                        <a href='{$loginLink}' style='background-color:#0d6efd; color:white; padding:12px 24px; text-decoration:none; border-radius:5px; font-weight: bold;'>Log In Now</a>
                        <br><br><br>
                    </div>
                ";
            } else if ($actionType === 'rejected') {
                $mail->Subject = 'Account Registration Update - CALIS v2.0';
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
                        <h3 style='color: #dc3545;'>Hello {$recipientName},</h3>
                        <p style='color: #333;'>We are writing to inform you that your account registration for the Data Bank and Library Services (DBLS) could not be approved at this time, and the request has been removed from our system.</p>
                        <p style='color: #333;'>If you believe this was a mistake or need further clarification, please contact the library administration.</p>
                        <br><br>
                        <p style='color: #6c757d; font-size: 0.9em;'>Thank you.</p>
                    </div>
                ";
            }

            $mail->send();
        } catch (Exception $e) {
            log_message('error', "Mail could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}