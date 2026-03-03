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

    public function requestResetCode()
    {
        $userModel = new UserModel();
        $id = session()->get('id');
        $user = $userModel->find($id);

        $resetCode = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $userModel->update($id, [
            'reset_token'   => $resetCode,
            'reset_expires' => $expires
        ]);

        if ($this->sendResetEmail($user['email'], $user['fullname'], $resetCode)) {
            // Updated to point to the unified view route
            return redirect()->to('/verify-reset')->with('success', 'Reset code sent to your email!');
        }

        return redirect()->back()->with('error', 'Failed to send email. Please check your connection.');
    }

private function sendResetEmail($recipientEmail, $recipientName, $code)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'ivcfiqpztkwapymf';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('zneright2@gmail.com', 'CALIS Library');
            $mail->addAddress($recipientEmail, $recipientName);

            $mail->isHTML(true);
            $mail->Subject = 'Admin Password Reset - CALIS v2.0';
            $mail->Body    = "<h3>Hello, {$recipientName}</h3>
                              <p>You requested to reset your Admin password.</p>
                              <p>Your verification code is: <b style='font-size: 24px; color: #1e3a8a;'>{$code}</b></p>
                              <p>This code will expire in 15 minutes.</p>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log the actual error to writable/logs
            log_message('error', 'Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public function processReset()
    {
        $userModel = new UserModel();
        $id = session()->get('id') ?? session()->get('temp_reset_user_id');

        if (!$id) {
            return redirect()->to('/login')->with('error', 'Session expired.');
        }

        $user = $userModel->find($id);
        $inputCode = $this->request->getPost('reset_code');
        $newPass   = $this->request->getPost('new_password');
        $confirm   = $this->request->getPost('confirm_password');

        if ($user['reset_token'] !== $inputCode || strtotime(date('Y-m-d H:i:s')) > strtotime($user['reset_expires'])) {
            return redirect()->back()->with('error', 'Invalid or expired code.');
        }

        if ($newPass !== $confirm) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        $userModel->update($id, [
            'password'      => password_hash($newPass, PASSWORD_DEFAULT),
            'reset_token'   => null,
            'reset_expires' => null
        ]);

        // Clear session and force return to login for unified design
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Password updated successfully! Please log in again.');
    }
}


}