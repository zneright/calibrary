<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
        return view('login');
    }

    // Process the Login Form
public function attemptLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        // Dummy account for admin
        if ($username === 'admin' && $password === '1234') {
            return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, System Administrator!');
        } 
        // Dummy account for borrower
        elseif ($username === 'borrower' && $password === '1234') {
            return redirect()->to('/borrower/dashboard')->with('success', 'Welcome to the CALIS Employee Portal!');
        } 
        // Validation
        else {
            return redirect()->back()->with('error', 'Invalid ID or password.');
        }
    }

    public function register()
    {
        return view('signup');
    }

   public function storeUser()
    {
        // 1. Updated Validation (Added Email)
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
        $token = bin2hex(random_bytes(32)); // Generates a secure random token

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
}