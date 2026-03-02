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
        
        session()->set('avatar', $newName);

        return redirect()->back()->with('success', 'Avatar updated!');
    }
    public function sendResetLink() 
{
    $userModel = new UserModel();
    $id = session()->get('id');
    $user = $userModel->find($id);

    if (!$user) {
        return redirect()->back()->with('error', 'User session not found.');
    }

    // 1. Generate Secure Token & Expiry
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // 2. Save to Database
    $userModel->update($id, [
        'reset_token' => $token,
        'reset_expires' => $expires
    ]);

    $resetLink = base_url("reset-password/$token");

    // 3. Send Email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'zneright2@gmail.com'; 
        $mail->Password = 'ivcfiqpztkwapymf'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('zneright2@gmail.com', 'CALIS Library');
        $mail->addAddress($user['email'], $user['fullname']);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - CALIS';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd;'>
                <h3 style='color: #1e3a8a;'>Security Verification</h3>
                <p>Hello {$user['fullname']},</p>
                <p>Click the button below to reset your administrator password. This link is valid for 1 hour.</p>
                <a href='$resetLink' style='background:#1e3a8a; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>Reset My Password</a>
                <p style='margin-top:20px; font-size:12px; color:#888;'>If you did not request this, please ignore this email.</p>
            </div>
        ";
        
        $mail->send();
        return redirect()->back()->with('success', 'A reset link has been sent to your email: ' . $user['email']);
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Mailer Error: ' . $mail->ErrorInfo);
    }
}
}