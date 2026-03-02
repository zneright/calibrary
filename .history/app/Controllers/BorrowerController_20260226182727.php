<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\TransactionModel;
use App\Models\CollectionModel;
use App\Models\LogModel;

class BorrowerController extends BaseController
{
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

    public function index()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $today = date('Y-m-d');

        // NEW: Fetch the current user's profile to get the avatar
        $userModel = new \App\Models\UserModel();
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
            'currentUser'   => $currentUser, // NEW: Pass user data to view
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

        $transactionModel = new \App\Models\TransactionModel();
        
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
            'globalActiveItems' => $globalActiveItems // Pass to view to block others
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
        $transactionModel = new \App\Models\TransactionModel();
        $logModel = new \App\Models\LogModel();

        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => 'Renewing']);

            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Transactions',
                'action'      => 'Update',
                'details'     => "Borrower requested renewal for: '{$trans['collection_title']}'."
            ]);

            return redirect()->back()->with('success', 'Renewal request sent to Admin.');
        }
        return redirect()->back()->with('error', 'Item not found.');
    }

    public function profile()
    {
        $userModel = new \App\Models\UserModel();
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
            $notificationModel = new \App\Models\NotificationModel();
            $notificationModel->update($id, ['status' => 'read']);
            return $this->response->setJSON(['status' => 'success']);
        }
    }

    public function markNotificationsRead()
    {
        if ($this->request->isAJAX()) {
            $notificationModel = new \App\Models\NotificationModel();
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
        $transactionModel = new TransactionModel();
        $db = \Config\Database::connect();
        
        $collectionId = $this->request->getPost('collection_id');
        $sourceTable  = $this->request->getPost('source_table'); 
        
        $item = null;
        $itemTitle = '';
        
        // 1. Determine if this is a Journal or a Book
        if ($sourceTable === 'journals') {
            $item = $db->table('journals')->where('id', $collectionId)->get()->getRowArray();
            if ($item) $itemTitle = $item['subject']; 
        } else {
            $collectionModel = new CollectionModel();
            $item = $collectionModel->find($collectionId);
            if ($item) $itemTitle = $item['title'];
        }

        $itemStatus = $item ? strtoupper($item['status']) : '';
        
        // Block if lost or doesn't exist
        if (!$item || !in_array($itemStatus, ['AVAILABLE', 'DAMAGED'])) {
            return redirect()->back()->with('error', 'Sorry, this item is currently unavailable.');
        }

        // 2. CHECK IF ANY USER ALREADY HAS THIS ITEM
        $globalExisting = $transactionModel->where('collection_id', $collectionId)
                                           ->whereIn('status', ['Pending', 'Approved', 'Borrowed', 'Renewing'])
                                           ->first();
                                           
        if ($globalExisting) {
            $userId = session()->get('user_id');
            // If the current user has it
            if ($globalExisting['user_id_num'] == $userId) {
                return redirect()->back()->with('error', 'You already have an active request for this item.');
            } else {
                // If someone else has it
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

        return redirect()->back()->with('success', 'Your request has been submitted successfully!');
    }

    public function cancelRequest()
    {
        $transactionModel = new TransactionModel();
        $logModel = new LogModel();

        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => 'Cancelled']);
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Transactions',
                'action'      => 'Update',
                'details'     => "Borrower cancelled their pending request for: '{$trans['collection_title']}'."
            ]);
            return redirect()->back()->with('success', 'Request cancelled.');
        }
        return redirect()->back()->with('error', 'Request not found.');
    }

    public function updateProfile()
    {
        $model = new \App\Models\UserModel();
        $id = session()->get('id');

        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'contact'  => $this->request->getPost('contact'),
        ];

        if ($model->update($id, $data)) {
            session()->set('fullname', $data['fullname']); 
            return redirect()->back()->with('success', 'Profile updated!');
        }
        return redirect()->back()->with('error', 'Update failed.');
    }

    public function updatePassword()
    {
        return redirect()->to('/borrower/profile')->with('success', 'Your password has been changed successfully.');
    }

    public function uploadAvatar()
    {
        $file = $this->request->getFile('avatar');
        if (! $file->isValid() || $file->hasMoved()) {
             return redirect()->back()->with('error', 'Please select a valid image file.');
        }
        
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/avatars', $newName);
        
        $userModel = new \App\Models\UserModel();
        $id = session()->get('id');
        $userModel->update($id, ['avatar' => $newName]);
        
        return redirect()->to('/borrower/profile')->with('success', 'Profile picture updated successfully!');
    }
}