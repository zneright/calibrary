<?php

namespace App\Controllers;
use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $id = session()->get('id'); // Internal database ID
        
        $data['user'] = $userModel->find($id);
        $data['title'] = "Profile Settings";

        return view('admin/profile', $data);
    }

    public function updateInfo()
    {
        $userModel = new UserModel();
        $id = session()->get('id');

        $data = [
            'fullname'   => $this->request->getPost('fullname'),
            'email'      => $this->request->getPost('email'),
            'contact'    => $this->request->getPost('contact'),
        ];

        if ($userModel->update($id, $data)) {
            // Update session name just in case it changed
            session()->set('fullname', $data['fullname']);
            return redirect()->back()->with('success', 'Information updated!');
        }
        return redirect()->back()->with('error', 'Update failed.');
    }

    public function updatePassword()
    {
        $userModel = new UserModel();
        $id = session()->get('id');
        $user = $userModel->find($id);

        $current = $this->request->getPost('current_password');
        $new     = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        if (!password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Current password incorrect.');
        }

        if ($new !== $confirm) {
            return redirect()->back()->with('error', 'New passwords do not match.');
        }

        $userModel->update($id, ['password' => password_hash($new, PASSWORD_DEFAULT)]);
        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    public function uploadAvatar()
    {
        $file = $this->request->getFile('avatar');
        if (!$file->isValid()) return redirect()->back()->with('error', 'Invalid file.');

        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/avatars', $newName);

        $userModel = new UserModel();
        $userModel->update(session()->get('id'), ['avatar' => $newName]);
        
        // Update session to reflect new image immediately
        session()->set('avatar', $newName);

        return redirect()->back()->with('success', 'Avatar updated!');
    }
}