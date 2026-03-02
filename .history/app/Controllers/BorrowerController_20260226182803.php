<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\TransactionModel;
use App\Models\CollectionModel;
use App\Models\LogModel;
use App\Models\UserModel;

class BorrowerController extends BaseController
{
    /**
     * Helper to fetch notifications for the logged-in user
     */
    private function getNotificationData()
    {
        $notificationModel = new NotificationModel();
        $userId = session()->get('user_id');

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

    /**
     * NEW HELPER: Reusable logging function for Borrower Actions
     */
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

    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $today = date('Y-m-d');

        // Fetch the current user's profile to get the avatar
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
            ->select('transactions.*, collections.class')
            ->join('collections', 'collections.id = transactions.collection_id', 'left')
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

    public function catalog()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status');
        $selectedTypes = $this->request->getGet('type') ?? [];

        // 1. Setup Collections Query
        $colQuery = $db->table('collections')
            ->select('id, title, author, subject, status, type, class, issued_date, cover_photo, "collections" as source_table');

        if (!empty($search)) {
            $colQuery->groupStart()
                    ->like('title', $search)->orLike('author', $search)->orLike('subject', $search)
                    ->groupEnd();
        }
        if ($status === 'available') $colQuery->where('status', 'AVAILABLE');
        if (!empty($selectedTypes)) $colQuery->whereIn('type', $selectedTypes);

        // 2. Setup Journals Query
        $jrQuery = $db->table('journals')
            ->select('id, subject as title, author, source as subject, status, "Journal" as type, volume as class, date as issued_date, NULL as cover_photo, "journals" as source_table');

        if (!empty($search)) {
            $jrQuery->groupStart()
                    ->like('subject', $search)->orLike('author', $search)->orLike('source', $search)
                    ->groupEnd();
        }
        
        if ($status === 'available') {
            $jrQuery->where('status', 'AVAILABLE');
        }

        if (!empty($selectedTypes) && !in_array('Journal', $selectedTypes) && count($selectedTypes) > 0) {
            $jrQuery->where('1=0'); 
        }

        // 3. Combine and Execute
        $unionQuery = $colQuery->union($jrQuery)->getCompiledSelect();
        $finalResults = $db->query("$unionQuery ORDER BY id DESC")->getResultArray();

        $transactionModel = new TransactionModel();
        
        // 4. Get EXACT transaction statuses specifically for the CURRENT USER
        $userTransactions = [];
        $userTrans = $transactionModel->where('user_id_num', $userId)
                                      ->whereIn('status', ['Pending', 'Approved', 'Borrowed', 'Renewing'])
                                      ->findAll();
        foreach ($userTrans as $t) {
            $userTransactions[$t['collection_id']] = $t['status'];
        }

        // 5. NEW: Get ALL items tied up in active transactions by ANYONE
        $globalActiveItems = [];
        $allTrans = $transactionModel->whereIn('status', ['Pending', 'Approved', 'Borrowed', 'Renewing'])
                                     ->findAll();
        foreach ($allTrans as $t) {
            $globalActiveItems[$t['collection_id']] = $t['status'];
        }

        $data = array_merge($this->getNotificationData(), [
            'items'             => $finalResults,
            'total_results'     => count($finalResults), 
            'search'            => $search,
            'selectedStatus'    => $status ?? 'all',
            'selectedTypes'     => $selectedTypes,
            'userTransactions'  => $userTransactions,
            'globalActiveItems' => $globalActiveItems 
        ]);

        return view('borrower/catalog', $data);
    }

    public function myBooks()
    {
        $transactionModel = new TransactionModel();
        $userId = session()->get('user_id');
        $today = date('Y-m-d');

        $data = array_merge($this->getNotificationData(), [
            'active_borrows'   => $transactionModel->where('user_id_num', $userId)
                                                ->whereIn('status', ['Approved', 'Borrowed', 'Renewing'])
                                                ->orderBy('id', 'DESC')->findAll(),
            
            'pending_requests' => $transactionModel->where('user_id_num', $userId)
                                                ->where('status', 'Pending')
                                                ->orderBy('id', 'DESC')->findAll(),
            
            'history'          => $transactionModel->where('user_id_num', $userId)
                                                ->whereIn('status', ['Returned', 'Rejected', 'Cancelled', 'Extension Rejected'])
                                                ->orderBy('id', 'DESC')->findAll(),
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

    public function submitRequest()
    {
        // 1. VALIDATION ADDED
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
        
        $item = null;
        $itemTitle = '';
        
        if ($sourceTable === 'journals') {
            $item = $db->table('journals')->where('id', $collectionId)->get()->getRowArray();
            if ($item) $itemTitle = $item['subject']; 
        } else {
            $collectionModel = new CollectionModel();
            $item = $collectionModel->find($collectionId);
            if ($item) $itemTitle = $item['title'];
        }

        $itemStatus = $item ? strtoupper($item['status']) : '';
        
        if (!$item || !in_array($itemStatus, ['AVAILABLE', 'DAMAGED'])) {
            return redirect()->back()->with('error', 'Sorry, this item is currently unavailable.');
        }

        $globalExisting = $transactionModel->where('collection_id', $collectionId)
                                           ->whereIn('status', ['Pending', 'Approved', 'Borrowed', 'Renewing'])
                                           ->first();
                                           
        if ($globalExisting) {
            $userId = session()->get('user_id');
            if ($globalExisting['user_id_num'] == $userId) {
                return redirect()->back()->with('error', 'You already have an active request for this item.');
            } else {
                return redirect()->back()->with('error', 'Sorry, this item is currently reserved or borrowed by another user.');
            }
        }

        $userId = session()->get('user_id');
        $userName = session()->get('fullname');
        $dateNeeded = $this->request->getPost('date_needed');
        $reason = $this->request->getPost('reason');

        $transactionModel->insert([
            'user_id_num'      => $userId,
            'user_name'        => $userName,
            'collection_id'    => $collectionId,
            'collection_title' => $itemTitle,
            'status'           => 'Pending',
            'date_requested'   => date('Y-m-d'),
            'date_needed'      => $dateNeeded,
            'reason'           => $reason
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
        // 1. VALIDATION ADDED
        $rules = [
            'fullname' => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email',
            'contact'  => 'required|min_length[10]|max_length[15]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $model = new UserModel();
        $id = session()->get('id');

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'contact'  => $this->request->getPost('contact'),
        ];

        if ($model->update($id, $data)) {
            session()->set('fullname', $data['fullname']); 
            $this->logAction('User Profile', 'Update', "Borrower updated their profile information.");
            
            return redirect()->back()->with('success', 'Profile updated!');
        }
        return redirect()->back()->with('error', 'Update failed.');
    }

    public function updatePassword()
    {
        // 1. VALIDATION ADDED
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

        // 2. VERIFY PASSWORD & UPDATE
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $userModel->update($id, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        $this->logAction('User Profile', 'Update', "Borrower updated their account password.");
        
        return redirect()->to('/borrower/profile')->with('success', 'Your password has been changed successfully.');
    }

    public function uploadAvatar()
    {
        // 1. VALIDATION ADDED
        $validationRule = [
            'avatar' => [
                'label' => 'Image File',
                'rules' => 'uploaded[avatar]'
                    . '|is_image[avatar]'
                    . '|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]'
                    . '|max_size[avatar,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('error', $this->validator->getError('avatar'));
        }

        $file = $this->request->getFile('avatar');
        if (! $file->isValid() || $file->hasMoved()) {
             return redirect()->back()->with('error', 'Please select a valid image file.');
        }
        
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/avatars', $newName);
        
        $userModel = new UserModel();
        $id = session()->get('id');
        $userModel->update($id, ['avatar' => $newName]);
        
        $this->logAction('User Profile', 'Update', "Borrower uploaded a new profile picture.");
        
        return redirect()->to('/borrower/profile')->with('success', 'Profile picture updated successfully!');
    }
}