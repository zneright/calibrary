<?php

namespace App\Controllers;

use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;  // ADD THIS
use PHPMailer\PHPMailer\Exception;  // ADD THIS

class Auth extends BaseController
{
    public function index()
    {
        return view('login');
    }

    // Process the Login Form
// Process the Login Form (Real MySQL Database Version)
    public function attemptLogin()
    {
        $session = session();
        
        // Ensure you have "use App\Models\UserModel;" at the top of this file!
        $userModel = new UserModel();

        // Grab input from the form (name="username" is used for the User ID)
        $userIdInput = $this->request->getPost('username'); 
        $passwordInput = $this->request->getPost('password');
        
        // 1. Find the user in the MySQL database by their user_id
     // New flexible query
$user = $userModel->where('user_id', $userIdInput)
                  ->orWhere('email', $userIdInput)
                  ->first();
        // 2. Check if the user exists AND the password matches the hash in the DB
        if ($user && password_verify($passwordInput, $user['password'])) {
            
            // 3. SECURITY CHECK: Did they click the email link?
            if ($user['is_verified'] == 0) {
                $session->setFlashdata('error', 'Your account is not verified. Please check your email inbox.');
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
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]' 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Prepare Data for Database
        $userModel = new UserModel();
        $token = bin2hex(random_bytes(32)); 

        $data = [
            'fullname'           => $this->request->getPost('fullname'),
            'user_id'            => $this->request->getPost('user_id'),
            'email'              => $this->request->getPost('email'),
            'role'               => $this->request->getPost('role'),
            'password'           => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'verification_token' => $token,
            'is_verified'        => 0
        ];

        // 3. Save to MySQL
        if ($userModel->insert($data)) {
            // 4. Send Confirmation Email
            if ($this->sendVerificationEmail($data['email'], $data['fullname'], $token)) {
                return redirect()->to('/login')->with('success', 'Account created! Please check your email to verify your account before logging in.');
            } else {
                return redirect()->to('/login')->with('error', 'Account created, but failed to send verification email. Contact admin.');
            }
        }
    }
    private function sendVerificationEmail($recipientEmail, $recipientName, $token)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings (Example using Gmail SMTP)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'ivcfiqpztkwapymf';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('your_email@gmail.com', 'CALIS Library');
            $mail->addAddress($recipientEmail, $recipientName);

            // Content
            $verifyLink = base_url("auth/verify/" . $token);
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your CALIS Account';
            $mail->Body    = "
                <h3>Welcome to CALIS v2.0, {$recipientName}!</h3>
                <p>Thank you for registering. Please click the button below to verify your email address and activate your account:</p>
                <br>
                <a href='{$verifyLink}' style='background-color:#0d6efd; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Verify My Account</a>
                <br><br>
                <p>If the button doesn't work, copy and paste this link into your browser: <br> {$verifyLink}</p>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            log_message('error', "Mail could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    // --- EMAIL VERIFICATION METHOD ---
    public function verify($token = null)
    {
        // 1. Check if a token was actually provided in the URL
        if (empty($token)) {
            return redirect()->to('/login')->with('error', 'Invalid verification link.');
        }

        $userModel = new UserModel();
        
        // 2. Search the database for a user with this exact token
        $user = $userModel->where('verification_token', $token)->first();

        if ($user) {
            // 3. User found! Update their account to verified and erase the token so it can't be used again
            $userModel->update($user['id'], [
                'is_verified'        => 1,
                'verification_token' => null
            ]);

            // 4. Send them to the login page with a success message
            return redirect()->to('/login')->with('success', 'Your email has been successfully verified! You may now log in.');
        } else {
            // 5. Token not found (maybe they already clicked it, or it's fake)
            return redirect()->to('/login')->with('error', 'Invalid or expired verification link. Your account may already be verified.');
        }
    }
}