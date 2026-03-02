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

    // ADD NEW USER (From Admin Panel)
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
            // --- LOG THE ACTION ---
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'),
                'module'      => 'User Management',
                'action'      => 'Create',
                'details'     => "Admin manually created user account for $fullname ($user_id)."
            ]);
            // ----------------------
            return redirect()->back()->with('success', 'New user added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add user.');
        }
    }

    // UPDATE EXISTING USER
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
            // --- LOG THE ACTION ---
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'), 
                'module'      => 'User Management',
                'action'      => 'Update',
                'details'     => "Admin updated profile information for user $fullname ($user_id)."
            ]);
            // ----------------------
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

    // ... Keep your sendEmailNotification method here exactly as it is ...