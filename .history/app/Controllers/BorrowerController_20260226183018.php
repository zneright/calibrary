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
     * Helper: Reusable logging function for Borrower Actions
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

        $colQuery = $db->table('collections')
            ->select('id, title, author, subject, status, type, class, issued_date, cover_photo, "collections" as source_table');

        if (!empty($search)) {
            $colQuery->groupStart()
                    ->like('title', $search)->orLike('author', $search)->orLike('subject', $search)
                    ->groupEnd();
        }
        if ($status === 'available') $colQuery->whereIn('status', ['AVAILABLE', 'DAMAGED']);
        if (!empty($selectedTypes)) $colQuery->whereIn('type', $selectedTypes);

        $jrQuery = $db->table('journals')
            ->select('id, subject as title, author, source as subject, status, "Journal" as type, volume as class, date as issued_date, NULL as cover_photo, "journals" as source_table');

        if (!empty($search)) {
            $jrQuery->groupStart()
                    ->like('subject', $search)->orLike('author', $search)->orLike('source', $search)
                    ->groupEnd();
        }
        
        if ($status === 'available') $jrQuery->whereIn('status', ['AVAILABLE', 'DAMAGED']);

        if (!empty($selectedTypes) && !in_array('Journal', $selectedTypes) && count($selectedTypes) > 0) {
            $jrQuery->where('1=0'); 
        }

        $unionQuery = $colQuery->union($jrQuery)->getCompiledSelect();
        $finalResults = $db->query("$unionQuery ORDER BY id DESC")->getResultArray();

        $transactionModel = new TransactionModel();
        
        // Detailed transaction map for the current user
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
        
        // 1. UPDATED VALIDATION: Block ONLY if physically unavailable (BORROWED or LOST)
        if (!$item || in_array($itemStatus, ['BORROWED', 'LOST'])) {
            return redirect()->back()->with('error', 'Sorry, this item is currently on hand by another user or lost.');
        }

        $userId = session()->get('user_id');
        
        // 2. Double-Request Prevention (Check if THIS user already has an active request)
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

    public function updateProfile()
    {
        $rules = [
            'fullname' => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email',
            'contact'  => 'required|numeric|exact_length[11]' 
        ];

        $messages = ['contact' => ['numeric' => 'Number only.', 'exact_length' => 'Must be 11 digits.']];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $model = new UserModel();
        if ($model->update(session()->get('id'), $this->request->getPost(['fullname', 'email', 'contact']))) {
            session()->set('fullname', $this->request->getPost('fullname')); 
            $this->logAction('User Profile', 'Update', "Updated profile info.");
            return redirect()->back()->with('success', 'Profile updated!');
        }
        return redirect()->back()->with('error', 'Update failed.');
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
        $user = $userModel->find(session()->get('id'));

        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $userModel->update(session()->get('id'), ['password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)]);
        $this->logAction('User Profile', 'Update', "Changed password.");
        return redirect()->to('/borrower/profile')->with('success', 'Password changed.');
    }

    public function uploadAvatar()
    {
        $validationRule = ['avatar' => ['rules' => 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]|max_size[avatar,2048]']];
        if (!$this->validate($validationRule)) return redirect()->back()->with('error', $this->validator->getError('avatar'));

        $file = $this->request->getFile('avatar');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/avatars', $newName);
            (new UserModel())->update(session()->get('id'), ['avatar' => $newName]);
            $this->logAction('User Profile', 'Update', "Uploaded new avatar.");
            return redirect()->to('/borrower/profile')->with('success', 'Avatar updated!');
        }
        return redirect()->back()->with('error', 'Upload failed.');
    }
}