<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LogModel;

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
        $logModel = new LogModel();

        // --- SECURITY: Wipe previous session data to prevent account bleeding ---
        $session->destroy();
        $session = session(); 

        $userIdInput = trim($this->request->getPost('username')); 
        $passwordInput = $this->request->getPost('password');
        
        $user = $userModel->where('user_id', $userIdInput)
                          ->orWhere('email', $userIdInput)
                          ->first();

        if ($user && password_verify($passwordInput, $user['password'])) {
            
            if ($user['is_verified'] == 0) {
                $session->setFlashdata('error', 'Your account is pending admin approval.');
                return redirect()->to('/login');
            }

            // SUCCESS - Set Specific User Data
            $session->set([
                'id'         => $user['id'],
                'user_id'    => $user['user_id'],
                'fullname'   => $user['fullname'],
                'role'       => $user['role'],
                'avatar'     => $user['avatar'], // Store avatar filename
                'isLoggedIn' => TRUE
            ]);

            $logModel->insert([
                'user_name'   => $user['fullname'],
                'user_id_num' => $user['user_id'],
                'module'      => 'Authentication',
                'action'      => 'Login',
                'details'     => "User successfully logged into the system."
            ]);

            if ($user['role'] === 'Admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/borrower/dashboard');
            }

        } else {
            $session->setFlashdata('error', 'Invalid ID or password.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $session->get('fullname'),
                'user_id_num' => $session->get('user_id'),
                'module'      => 'Authentication',
                'action'      => 'Logout',
                'details'     => "User logged out."
            ]);
        }
        $session->destroy();
        return redirect()->to('/login')->with('success', 'Logged out successfully.');
    }
}