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

        // Grab input from the form
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

            // ==============================================
            // LOG THE SUCCESSFUL LOGIN
            // ==============================================
            $logModel = new \App\Models\LogModel();
            $logModel->insert([
                'user_name'   => $user['fullname'],
                'user_id_num' => $user['user_id'],
                'module'      => 'Authentication',
                'action'      => 'Login',
                'details'     => "User ({$user['fullname']}) successfully logged into the system."
            ]);

            // 5. Redirect them to the correct dashboard based on their role
            if ($user['role'] === 'Admin') {
                return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, ' . $user['fullname'] . '!');
            } else {
                return redirect()->to('/borrower/dashboard')->with('success', 'Welcome to the CALIS Library Portal!');
            }

        } else {
            // ==============================================
            // LOG THE FAILED LOGIN ATTEMPT
            // ==============================================
            $logModel = new \App\Models\LogModel();
            $logModel->insert([
                'user_name'   => 'Unknown/Unverified',
                'user_id_num' => $userIdInput, // Records what they typed into the ID box
                'module'      => 'Authentication',
                'action'      => 'Failed',
                'details'     => "Failed login attempt using ID/Email: $userIdInput."
            ]);

            $session->setFlashdata('error', 'Invalid ID or password.');
            return redirect()->back();
        }
    }
   
}