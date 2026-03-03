<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\TransactionModel;
use App\Models\CollectionModel;
use App\Models\LogModel;
use App\Models\UserModel;

class BorrowerController extends BaseController
{
    //fetch notifications for borrower dashboard and notification list
    private function getNotificationData()
    {
        $notificationModel = new NotificationModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            return ['notifs' => [], 'unreadCount' => 0];
        }

        $userId = (string) $userId;

        $notifs = $notificationModel->groupStart()
                ->where('target_audience', 'all_users')
                ->orLike('recipient', $userId) 
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $unreadCount = 0;
        foreach ($notifs as $n) {
            if ($n['status'] === 'unread') {
                $unreadCount++;
            }
        }

        return ['notifs' => $notifs, 'unreadCount' => $unreadCount];
    }

    private function logAction($module, $action, $details) 
    {
        $logModel = new LogModel();
        $logModel->insert([
            'user_name'   => session()->get('fullname'),
            'user_id_num' => session()->get('user_id'),
            'module'      => $module, 
            'action'      => $action, 
            'details'     => $details
        ]);
    }
    //summary of borrower's dashboard
    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $today = date('Y-m-d');

        $userModel = new UserModel();
        $currentUser = $userModel->find(session()->get('id'));
        
        $borrowedCount = $db->table('transactions')
            ->where('user_id_num', $userId)
            ->groupStart()
                ->where('status', 'Borrowed')
                ->orWhere('status', 'Renewing')
            ->groupEnd()
            ->countAllResults();

        $pendingCount = $db->table('transactions')
            ->where(['user_id_num' => $userId, 'status' => 'Pending'])
            ->countAllResults();

        $pickupCount = $db->table('transactions')
            ->where(['user_id_num' => $userId, 'status' => 'Approved'])
            ->countAllResults();

        $overdueCount = $db->table('transactions')
            ->where(['user_id_num' => $userId, 'status' => 'Borrowed', 'due_date <' => $today])
            ->countAllResults();
        
        $activeBorrows = $db->table('transactions')
            ->select('
                transactions.*, 
                COALESCE(collections.class, journals.volume) as class,
                COALESCE(collections.type, "Journal") as type,
                COALESCE(collections.author, journals.author) as author,
                COALESCE(collections.cover_photo, journals.cover_photo) as cover_photo
            ')
            ->join('collections', 'collections.id = transactions.collection_id', 'left')
            ->join('journals', 'journals.id = transactions.collection_id AND collections.id IS NULL', 'left')
            ->where('transactions.user_id_num', $userId)
            ->whereIn('transactions.status', ['Pending', 'Approved', 'Borrowed', 'Renewing']) 
            ->orderBy('transactions.id', 'DESC')
            ->get()->getResultArray();

        $data = array_merge($this->getNotificationData(), [
            'title'         => 'Borrower Dashboard',
            'currentUser'   => $currentUser, 
            'borrowedCount' => $borrowedCount,
            'pendingCount'  => $pendingCount,
            'pickupCount'   => $pickupCount,
            'overdueCount'  => $overdueCount,
            'activeBorrows' => $activeBorrows,
            'today'         => $today
        ]);

        return view('borrower/borrowerdashboard', $data); 
    }

    //seach para sa journal and collections!
    public function catalog()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status');
        $selectedTypes = $this->request->getGet('type') ?? [];
        
        // Collections Query
        $colQuery = $db->table('collections')
            ->select('id, title, author, subject, status, type, class, issued_date, cover_photo, publisher, "collections" as source_table');

        if (!empty($search)) {
            $colQuery->groupStart()
            
                    ->like('title', $search)->orLike('author', $search)->orLike('subject', $search)
                    ->groupEnd();
        }
        if ($status === 'available') $colQuery->whereIn('status', ['AVAILABLE', 'DAMAGED']);
        if (!empty($selectedTypes)) $colQuery->whereIn('type', $selectedTypes);

        // Journals Query
        $jrQuery = $db->table('journals')
            ->select('id, subject as title, author, source as subject, status, "Journal" as type, volume as class, date as issued_date, cover_photo, source as publisher, "journals" as source_table');

        if (!empty($search)) {
            $jrQuery->groupStart()
                    ->like('subject', $search)->orLike('author', $search)->orLike('source', $search)
                    ->groupEnd();
        }
        
        if ($status === 'available') $jrQuery->whereIn('status', ['AVAILABLE', 'DAMAGED']);

        if (!empty($selectedTypes) && !in_array('Journal', $selectedTypes) && count($selectedTypes) > 0) {
            $jrQuery->where('1=0'); 
        }
        
        // Combine both queries (newest first)
        $unionQuery = $colQuery->union($jrQuery)->getCompiledSelect();
        $finalResults = $db->query("$unionQuery ORDER BY id DESC")->getResultArray();

        $transactionModel = new TransactionModel();
        
        $userTransactions = [];
        $userTrans = $transactionModel->where('user_id_num', $userId)
                                      ->whereIn('status', ['Pending', 'Approved', 'Borrowed', 'Renewing'])
                                      ->findAll();
        foreach ($userTrans as $t) {
            $userTransactions[$t['collection_id']] = $t['status'];
        }

        $data = array_merge($this->getNotificationData(), [
            'items'             => $finalResults,
            'total_results'     => count($finalResults), 
            'search'            => $search,
            'selectedStatus'    => $status ?? 'all',
            'selectedTypes'     => $selectedTypes,
            'userTransactions'  => $userTransactions,
        ]);

        return view('borrower/catalog', $data);
    }

    public function myBooks()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $today = date('Y-m-d');

        $fetchTransactions = function($statusArray) use ($db, $userId) {
            return $db->table('transactions')
                ->select('
                    transactions.*, 
                    COALESCE(collections.class, journals.volume) as class,
                    COALESCE(collections.type, "Journal") as type,
                    COALESCE(collections.author, journals.author) as author,
                    COALESCE(collections.cover_photo, journals.cover_photo) as cover_photo
                ')
                ->join('collections', 'collections.id = transactions.collection_id', 'left')
                ->join('journals', 'journals.id = transactions.collection_id AND collections.id IS NULL', 'left')
                ->where('transactions.user_id_num', $userId)
                ->whereIn('transactions.status', $statusArray)
                ->orderBy('transactions.id', 'DESC')
                ->get()->getResultArray();
        };

        $data = array_merge($this->getNotificationData(), [
            'active_borrows'   => $fetchTransactions(['Approved', 'Borrowed', 'Renewing']),
            'pending_requests' => $fetchTransactions(['Pending']),
            'history'          => $fetchTransactions(['Returned', 'Rejected', 'Cancelled', 'Extension Rejected']),
            'today'            => $today
        ]);

        return view('borrower/my_books', $data);
    }

    public function submitRenewal()
    {
        $transactionModel = new TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => 'Renewing']);
            $this->logAction('Transactions', 'Update', "Borrower requested renewal for: '{$trans['collection_title']}'.");
            return redirect()->back()->with('success', 'Renewal request sent to Admin.');
        }
        return redirect()->back()->with('error', 'Item not found.');
    }

    public function profile()
    {
        $userModel = new UserModel();
        $id = session()->get('id'); 
        
        $data = array_merge($this->getNotificationData(), [
            'title' => 'My Profile Settings',
            'user'  => $userModel->find($id)
        ]);

        return view('borrower/profile', $data);
    }

    public function notificationlist()
    {
        $data = $this->getNotificationData();
        return view('borrower/notificationlist', $data);
    }

    public function markSingleRead($id)
    {
        if ($this->request->isAJAX()) {
            $notificationModel = new NotificationModel();
            $notificationModel->update($id, ['status' => 'read']);
            return $this->response->setJSON(['status' => 'success']);
        }
    }

    public function markNotificationsRead()
    {
        if ($this->request->isAJAX()) {
            $notificationModel = new NotificationModel();
            $userId = session()->get('user_id');

            $notificationModel->where('status', 'unread')
                ->groupStart()
                    ->where('target_audience', 'all_users')
                    ->orLike('recipient', $userId)
                ->groupEnd()
                ->set(['status' => 'read'])
                ->update();

            return $this->response->setJSON(['status' => 'success']);
        }
    }
    //create new borrow request
    public function submitRequest()
    {
        $rules = [
            'date_needed' => 'required|valid_date',
            'reason'      => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Please provide a valid Date Needed and Reason.');
        }

        $transactionModel = new TransactionModel();
        $db = \Config\Database::connect();
        
        $collectionId = $this->request->getPost('collection_id');
        $sourceTable  = $this->request->getPost('source_table'); 
        
        $item = ($sourceTable === 'journals') 
            ? $db->table('journals')->where('id', $collectionId)->get()->getRowArray()
            : (new CollectionModel())->find($collectionId);

        $itemTitle = ($sourceTable === 'journals') ? ($item['subject'] ?? '') : ($item['title'] ?? '');
        $itemStatus = $item ? strtoupper($item['status']) : '';
        
        if (!$item || in_array($itemStatus, ['BORROWED', 'LOST'])) {
            return redirect()->back()->with('error', 'Sorry, this item is currently on hand or lost.');
        }

        $userId = session()->get('user_id');
        
        $existing = $transactionModel->where(['user_id_num' => $userId, 'collection_id' => $collectionId])
                                    ->whereIn('status', ['Pending', 'Approved', 'Borrowed', 'Renewing'])
                                    ->first();
        if ($existing) {
            return redirect()->back()->with('error', 'You already have an active request for this item.');
        }

        $transactionModel->insert([
            'user_id_num'      => $userId,
            'user_name'        => session()->get('fullname'),
            'collection_id'    => $collectionId,
            'collection_title' => $itemTitle,
            'status'           => 'Pending',
            'date_requested'   => date('Y-m-d'),
            'date_needed'      => $this->request->getPost('date_needed'),
            'reason'           => $this->request->getPost('reason')
        ]);

        $this->logAction('Transactions', 'Create', "Borrower submitted a new request for: '{$itemTitle}'.");

        return redirect()->back()->with('success', 'Your request has been submitted successfully!');
    }

    public function cancelRequest()
    {
        $transactionModel = new TransactionModel();
        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => 'Cancelled']);
            $this->logAction('Transactions', 'Update', "Borrower cancelled their pending request for: '{$trans['collection_title']}'.");
            return redirect()->back()->with('success', 'Request cancelled.');
        }
        return redirect()->back()->with('error', 'Request not found.');
    }

    public function updateProfile()
    {
        $userId = session()->get('id'); 
        $userModel = new \App\Models\UserModel(); 

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'contact'  => $this->request->getPost('contact')
        ];

        if ($userModel->update($userId, $data)) {
            session()->set('fullname', $data['fullname']);
            -
            $this->logAction('User Profile', 'Update', "User updated their personal profile details.");
            return redirect()->back()->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update profile. Please try again.');
        }
    }
    public function updatePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $userModel = new UserModel();
        $id = session()->get('id');
        $user = $userModel->find($id);

        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $userModel->update($id, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        $this->logAction('User Profile', 'Update', "Borrower updated their account password.");
        
        return redirect()->to('/borrower/profile')->with('success', 'Your password has been changed successfully.');
    }
    //update profile picture
    public function uploadAvatar()
    {
        $validationRule = [
            'avatar' => [
                'rules' => 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]|max_size[avatar,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('error', $this->validator->getError('avatar'));
        }

        $file = $this->request->getFile('avatar');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/avatars', $newName);
            
            $userModel = new UserModel();
            $userModel->update(session()->get('id'), ['avatar' => $newName]);
            
            $this->logAction('User Profile', 'Update', "Uploaded new profile picture.");
            return redirect()->to('/borrower/profile')->with('success', 'Avatar updated!');
        }
        return redirect()->back()->with('error', 'Upload failed.');
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
            
            $this->logAction('Security', 'Reset Request', "User requested a password reset code from their profile.");

            return redirect()->to('/borrower/verify-reset')->with('success', 'Reset code sent to your email.');
        }

        return redirect()->back()->with('error', 'Failed to send email.');
    }

    private function sendResetEmail($recipientEmail, $recipientName, $code)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'ivcfiqpztkwapymf';    
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('zneright2@gmail.com', 'CALIS Admin');
            $mail->addAddress($recipientEmail, $recipientName);

            $mail->isHTML(true);
            $mail->Subject = 'Borrower Password Reset - CALIS v2.0';
            $mail->Body    = "<h3>Hello, {$recipientName}</h3>
                            <p>You requested a password reset for your borrower account.</p>
                            <p>Your code is: <b style='font-size: 20px; color: #1e3a8a;'>{$code}</b></p>
                            <p>This code will expire in 15 minutes.</p>";

            $mail->send();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyResetView()
{
    $isLoggedIn = session()->get('id');
    $navData = $isLoggedIn ? $this->getNotificationData() : ['notifs' => [], 'unreadCount' => 0];

    $data = array_merge($navData, [
        'title' => 'Verify Identity'
    ]);

    return view('verify_reset', $data); 
}

    public function processReset()
    {
        $userModel = new UserModel();
        
        $id = session()->get('id') ?? session()->get('temp_reset_user_id');
        
        if (!$id) {
            return redirect()->to('/login')->with('error', 'Session expired. Please start over.');
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

        $updateData = [
            'password'      => password_hash($newPass, PASSWORD_DEFAULT),
            'reset_token'   => null,
            'reset_expires' => null
        ];

        if ($userModel->update($id, $updateData)) {
            
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $user['fullname'], 
                'user_id_num' => $user['id_num'] ?? $id, 
                'module'      => 'Security', 
                'action'      => 'Reset Password', 
                'details'     => "User successfully verified code and reset their password."
            ]);
            session()->remove('temp_reset_user_id');
            if (session()->get('id')) {
                session()->destroy(); 
            }

            return redirect()->to('/login')->with('success', 'Password updated! Please log in with your new password.');
        } else {
            return redirect()->back()->with('error', 'Failed to update password. Please try again.');
        }
    }
    public function sendPublicResetCode()
    {
        $email = $this->request->getPost('email');
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'No account found with that email.');
        }

        $resetCode = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $userModel->update($user['id'], [
            'reset_token'   => $resetCode,
            'reset_expires' => $expires
        ]);

        session()->set('temp_reset_user_id', $user['id']);

        if ($this->sendResetEmail($user['email'], $user['fullname'], $resetCode)) {
            
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $user['fullname'],
                'user_id_num' => $user['id_num'] ?? $user['id'],
                'module'      => 'Security',
                'action'      => 'Forgot Password',
                'details'     => "User requested a password reset code via the Public Login page."
            ]);

            return redirect()->to('/verify-reset')->with('success', 'Reset code sent! Please check your inbox.');
        }

        return redirect()->to('/login')->with('error', 'Failed to send email. Please try again later.');
    }
    public function verifyResetCodeAjax()
    {
        $userModel = new \App\Models\UserModel();
        
        $id = session()->get('id') ?? session()->get('temp_reset_user_id');
        
        if (!$id) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Session expired. Please request a new code.',
                'csrf_hash' => csrf_hash() 
            ]);
        }

        $user = $userModel->find($id);
        $inputCode = $this->request->getPost('reset_code');

        // Check if code matches OR if the time has expired
        if ($user['reset_token'] !== $inputCode || strtotime(date('Y-m-d H:i:s')) > strtotime($user['reset_expires'])) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Invalid or expired verification code.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'csrf_hash' => csrf_hash()
        ]);
    }
    public function cancelReset()
    {
        $userModel = new \App\Models\UserModel();
        
        $id = session()->get('id') ?? session()->get('temp_reset_user_id');

        if ($id) {
            $userModel->update($id, [
                'reset_token'   => null,
                'reset_expires' => null
            ]);

            $user = $userModel->find($id);
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $user['fullname'] ?? 'Unknown',
                'user_id_num' => $user['id_num'] ?? $id,
                'module'      => 'Security',
                'action'      => 'Cancel Reset',
                'details'     => "User cancelled their password reset process and invalidated the token."
            ]);
        }

        session()->remove('temp_reset_user_id');

        if (session()->get('id')) {
            $role = session()->get('role');
            $redirectPath = ($role === 'Admin') ? 'admin/profile' : 'borrower/profile';
            return redirect()->to('/' . $redirectPath)->with('error', 'Password reset cancelled.');
        }

        return redirect()->to('/login')->with('error', 'Forgot password cancelled.');
    }
}