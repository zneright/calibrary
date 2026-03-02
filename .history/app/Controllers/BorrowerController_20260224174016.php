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

        // Fetch notifications for 'all_users' OR specifically for this user's ID
        $notifs = $notificationModel->groupStart()
                ->where('target_audience', 'all_users')
                ->orLike('recipient', $userId) 
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $unreadCount = 0;
        foreach ($notifs as $n) {
            if ($n['status'] === 'unread') $unreadCount++;
        }

        return [
            'notifs'      => $notifs,
            'unreadCount' => $unreadCount
        ];
    }

    public function index()
    {
        $data = $this->getNotificationData();
        return view('borrower/dashboard', $data);
    }

    public function catalog()
    {
        $collectionModel = new CollectionModel();
        $transactionModel = new TransactionModel();
        $userId = session()->get('user_id');

        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status');
        $types  = $this->request->getGet('type');

        $query = $collectionModel;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('title', $search)
                           ->orLike('author', $search)
                           ->orLike('subject', $search)
                           ->orLike('class', $search)
                           ->groupEnd();
        }

        if ($status === 'available') {
            $query = $query->where('status', 'AVAILABLE');
        }

        if (!empty($types) && is_array($types)) {
            $query = $query->whereIn('type', $types);
        }

        $existingRequests = $transactionModel->where('user_id_num', $userId)
                                             ->whereIn('status', ['Pending', 'Approved', 'Borrowed'])
                                             ->findColumn('collection_id') ?: [];

        $data = array_merge($this->getNotificationData(), [
            'items'            => $query->orderBy('id', 'DESC')->paginate(10),
            'pager'            => $collectionModel->pager,
            'total_results'    => $collectionModel->countAllResults(false),
            'search'           => $search,
            'selectedStatus'   => $status ?? 'all',
            'selectedTypes'    => $types ?? [],
            'existingRequests' => $existingRequests 
        ]);

        return view('borrower/catalog', $data);
    }

    public function myBooks()
    {
        $transactionModel = new TransactionModel();
        $userId = session()->get('user_id');

        $data = array_merge($this->getNotificationData(), [
            'active_borrows'   => $transactionModel->where('user_id_num', $userId)
                                                   ->whereIn('status', ['Approved', 'Borrowed'])
                                                   ->orderBy('id', 'DESC')->findAll(),
            'pending_requests' => $transactionModel->where('user_id_num', $userId)
                                                   ->where('status', 'Pending')
                                                   ->orderBy('id', 'DESC')->findAll(),
            'history'          => $transactionModel->where('user_id_num', $userId)
                                                   ->whereIn('status', ['Returned', 'Rejected', 'Cancelled'])
                                                   ->orderBy('id', 'DESC')->findAll(),
        ]);

        return view('borrower/my_books', $data);
    }

    public function profile()
    {
        $data = $this->getNotificationData();
        return view('borrower/profile', $data);
    }=

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

    public function submitRenewal()
    {
        $logModel = new LogModel();
        $title = $this->request->getPost('book_title');
        $newDate = $this->request->getPost('new_due_date');

        $logModel->insert([
            'user_name'   => session()->get('fullname'),
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Transactions',
            'action'      => 'Update',
            'details'     => "Borrower requested a renewal for '$title' until $newDate."
        ]);

        return redirect()->back()->with('success', 'Renewal request submitted to Admin.');
    }

    public function updateProfile()
    {
        return redirect()->to('/borrower/profile')->with('success', 'Your personal information has been updated.');
    }

    public function updatePassword()
    {
        return redirect()->to('/borrower/profile')->with('success', 'Your password has been changed successfully.');
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
    public function uploadAvatar()
    {
        $file = $this->request->getFile('avatar');
        if (! $file->isValid() || $file->hasMoved()) {
             return redirect()->back()->with('error', 'Please select a valid image file.');
        }
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/avatars', $newName);
        return redirect()->to(  '/borrower/profile')->with('success', 'Profile picture updated successfully!');
    }
}