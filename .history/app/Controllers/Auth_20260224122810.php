<?php

namespace App\Controllers;

use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;  

class Auth extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function attemptLogin()
    {
        $session = session();
        
        
        $userModel = new UserModel();

        // Grab input from the form (name="username" is used for the User ID)
        $userIdInput = $this->request->getPost('username'); 
        $passwordInput = $this->request->getPost('password');
        
        $user = $userModel->where('user_id', $userIdInput)
                  ->orWhere('email', $userIdInput)
                  ->first();
        // 2. Check if the user exists AND the password matches the hash in the DB
        if ($user && password_verify($passwordInput, $user['password'])) {
            
            // 3. SECURITY CHECK: Did they click the email link?
          if ($user['is_verified'] == 0) {
                $session->setFlashdata('error', 'Your account is pending admin approval. Please wait until your access is granted.');
                return redirect()->back();
            }

            // 4. Set Session Data (The user is now officially logged in)
            $ses_data = [
                'id'         => $user['id'],
                'user_id'    => $user['user_id'],
                'fullname'   => $user['fullname'],
                'role'       => $user['role'],
                'isLoggedIn' => TRUE
            ];
            $session->set($ses_data);

            // 5. Redirect them to the correct dashboard based on their role
            if ($user['role'] === 'Admin') {
                return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, ' . $user['fullname'] . '!');
            } else {
                return redirect()->to('/borrower/dashboard')->with('success', 'Welcome to the CALIS Library Portal!');
            }

        } else {
            // Either the ID doesn't exist, or the password was wrong
            $session->setFlashdata('error', 'Invalid ID or password.');
            return redirect()->back();
        }
    }

    public function register()
    {
        return view('signup');
    }

   public function storeUser()
    {
      $rules = [
            'fullname'         => 'required|min_length[3]',
            'user_id'          => 'required|is_unique[users.user_id]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'department'       => 'required',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]' 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

       $data = [
            'fullname'           => $this->request->getPost('fullname'),
            'user_id'            => $this->request->getPost('user_id'),
            'email'              => $this->request->getPost('email'),
            'department'         => $this->request->getPost('department'), // <-- ADD THIS LINE
            'role'               => $this->request->getPost('role'),
            'password'           => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'verification_token' => null,
            'is_verified'        => 0
        ];

        if ($userModel->insert($data)) {
            // Send the "Wait for Approval" email
            if ($this->sendApprovalWaitEmail($data['email'], $data['fullname'])) {
                return redirect()->to('/login')->with('success', 'Registration submitted! Please wait for an admin to approve your account.');
            } else {
                return redirect()->to('/login')->with('error', 'Account registered, but failed to send the notification email.');
            }
        }
    }
   private function sendApprovalWaitEmail($recipientEmail, $recipientName)
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
            $mail->setFrom('zneright2@gmail.com', 'CALIS Library');
            $mail->addAddress($recipientEmail, $recipientName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account Pending Approval - CALIS v2.0';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
                    <h3 style='color: #0d6efd;'>Welcome to CALIS v2.0, {$recipientName}!</h3>
                    <p style='color: #333;'>We have successfully received your registration request for the Data Bank and Library Services (DBLS).</p>
                    <p style='color: #333;'><b>Your account is currently pending approval from a System Administrator.</b></p>
                    <p style='color: #333;'>You will not be able to log in until your access is granted. We will notify you once your account is ready.</p>
                    <br>
                    <p style='color: #6c757d; font-size: 0.9em;'>Thank you for your patience!</p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            log_message('error', "Mail could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
   
}