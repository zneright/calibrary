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
        //VALIDATION
        $rules = [
            'fullname'         => 'required|min_length[3]',
            'user_id'          => 'required',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]' 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        return redirect()->to('/login')->with('success', 'Account created successfully! You may now sign in.');
    }
}