<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\TransactionModel;
use App\Models\CollectionModel;
use App\Models\LogModel;

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

 public function index()
{
    $db = \Config\Database::connect();
    $userId = session()->get('user_id');
    $today = date('Y-m-d');

    // 1. UPDATED: Count Borrowed OR Renewing as "On Hand"
    $borrowedCount = $db->table('transactions')
        ->where('user_id_num', $userId)
        ->groupStart()
            ->where('status', 'Borrowed')
            ->orWhere('status', 'Renewing')
        ->groupEnd()
        ->countAllResults();

    $activeBorrows = $db->table('transactions')
        ->select('transactions.*, collections.class')
        ->join('collections', 'collections.id = transactions.collection_id', 'left')
        ->where('transactions.user_id_num', $userId)
        ->whereIn('transactions.status', ['Pending', 'Approved', 'Borrowed'])
        ->orderBy('transactions.id', 'DESC')
        ->get()->getResultArray();

    $data = array_merge($this->getNotificationData(), [
        'title'         => 'Borrower Dashboard',
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
        if ($status === 'available') $colQuery->where('status', 'AVAILABLE');
        if (!empty($selectedTypes)) $colQuery->whereIn('type', $selectedTypes);

        $jrQuery = $db->table('journals')
            ->select('id, subject as title, author, source as subject, "AVAILABLE" as status, "Journal" as type, volume as class, date as issued_date, NULL as cover_photo, "journals" as source_table');

        if (!empty($search)) {
            $jrQuery->groupStart()
                    ->like('subject', $search)->orLike('author', $search)->orLike('source', $search)
                    ->groupEnd();
        }
        if (!empty($selectedTypes) && !in_array('Journal', $selectedTypes) && count($selectedTypes) > 0) {
            $jrQuery->where('1=0'); 
        }

        $unionQuery = $colQuery->union($jrQuery)->getCompiledSelect();
        $finalResults = $db->query("$unionQuery ORDER BY id DESC")->getResultArray();

        $transactionModel = new \App\Models\TransactionModel();
        $existingRequests = $transactionModel->where('user_id_num', $userId)
                            ->whereIn('status', ['Pending', 'Approved', 'Borrowed'])
                            ->findColumn('collection_id') ?: [];

        $data = array_merge($this->getNotificationData(), [
            'items'            => $finalResults,
            'total_results'    => count($finalResults), 
            'search'           => $search,
            'selectedStatus'   => $status ?? 'all',
            'selectedTypes'    => $selectedTypes,
            'existingRequests' => $existingRequests 
        ]);

        return view('borrower/catalog', $data);
    }

 public function myBooks()
{
    $transactionModel = new \App\Models\TransactionModel();
    $userId = session()->get('user_id');
    $today = date('Y-m-d');

    $data = array_merge($this->getNotificationData(), [
        // ADD 'Renewing' here to keep it in the Active tab
        'active_borrows'   => $transactionModel->where('user_id_num', $userId)
                                               ->whereIn('status', ['Approved', 'Borrowed', 'Renewing'])
                                               ->orderBy('id', 'DESC')->findAll(),
        
        'pending_requests' => $transactionModel->where('user_id_num', $userId)
                                               ->where('status', 'Pending')
                                               ->orderBy('id', 'DESC')->findAll(),
        
        'history'          => $transactionModel->where('user_id_num', $userId)
                                               ->whereIn('status', ['Returned', 'Rejected', 'Cancelled'])
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
        // Change status to 'Renewal Requested'
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
    // Reuses your existing getNotificationData helper
    $data = $this->getNotificationData();
    return view('borrower/notificationlist', $data);
}

public function markSingleRead($id)
{
    if ($this->request->isAJAX()) {
        $notificationModel = new \App\Models\NotificationModel();
        
        // Update only the clicked item
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
        $collectionModel = new CollectionModel();
        $logModel = new LogModel();
        
        $collectionId = $this->request->getPost('collection_id');
        $dateNeeded   = $this->request->getPost('date_needed');
        $reason       = $this->request->getPost('reason');
        $userId       = session()->get('user_id'); 
        $userName     = session()->get('fullname');

        $book = $collectionModel->find($collectionId);
        $bookTitle = $book ? $book['title'] : 'Unknown Resource';

        $transactionModel->insert([
            'user_id_num'      => $userId,
            'user_name'        => $userName,
            'collection_id'    => $collectionId,
            'collection_title' => $bookTitle,
            'status'           => 'Pending',
            'date_requested'   => date('Y-m-d'),
            'date_needed'      => $dateNeeded,
            'reason'           => $reason
        ]);
        
        $logModel->insert([
            'user_name'   => $userName,
            'user_id_num' => $userId,
            'module'      => 'Transactions',
            'action'      => 'Add',
            'details'     => "Borrower requested resource: '$bookTitle' for use on $dateNeeded."
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
        session()->set('fullname', $data['fullname']); // Sync session name
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
        return redirect()->to('/borrower/profile')->with('success', 'Profile picture updated successfully!');
    }
}