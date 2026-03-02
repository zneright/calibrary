<?php

namespace App\Controllers;

class BorrowerController extends BaseController
{
    public function index()
    {
        $data = [
            'notifs'      => $this->getNotifications(),
            'unreadCount' => $this->countUnread($this->getNotifications())
        ];
        return view('borrower/dashboard');
    }
    private function countUnread($notifications)
    {
        $count = 0;
        foreach ($notifications as $n) {
            if ($n['status'] === 'unread') $count++;
        }
        return $count;
    }
    private function getNotifications()
        {
            $notificationModel = new \App\Models\NotificationModel();
            $userId = session()->get('user_id'); // e.g., 2026-001

            return $notificationModel->groupStart()
                    ->where('target_audience', 'all_users')
                    // This searches for the ID regardless of where the name is placed
                    ->orLike('recipient', $userId) 
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }

       public function catalog()
{
    $collectionModel = new \App\Models\CollectionModel();
    $transactionModel = new \App\Models\TransactionModel();
    $userId = session()->get('user_id');

    $search = $this->request->getGet('q');
    $status = $this->request->getGet('status');
    $types  = $this->request->getGet('type');

    $query = $collectionModel;

        // Apply Search Keyword
        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('title', $search)
                           ->orLike('author', $search)
                           ->orLike('subject', $search)
                           ->orLike('class', $search)
                           ->groupEnd();
        }

        // Apply Availability Filter
        if ($status === 'available') {
            $query = $query->where('status', 'AVAILABLE');
        }

        // Apply Resource Type Filter (Books, Journals, EOs, PDs)
        if (!empty($types) && is_array($types)) {
            $query = $query->whereIn('type', $types);
        }

        // 3. Fetch data with Pagination (10 items per page)
      $existingRequests = $transactionModel->where('user_id_num', $userId)
                                         ->whereIn('status', ['Pending', 'Approved', 'Borrowed'])
                                         ->findColumn('collection_id') ?: [];

    $data = [
        'items'            => $query->orderBy('id', 'DESC')->paginate(10),
        'pager'            => $collectionModel->pager,
        'total_results'    => $collectionModel->countAllResults(false),
        'search'           => $search,
        'selectedStatus'   => $status ?? 'all',
        'selectedTypes'    => $types ?? [],
        'existingRequests' => $existingRequests 
    ];

    return view('borrower/catalog', $data);
}
    // Handle the Request from the Catalog
    public function submitRequest()
    {
        $transactionModel = new \App\Models\TransactionModel();
        $logModel = new \App\Models\LogModel();
        
        $collectionId = $this->request->getPost('collection_id');
        $dateNeeded   = $this->request->getPost('date_needed');
        $reason       = $this->request->getPost('reason');
        $userId       = session()->get('user_id'); 
        $userName     = session()->get('fullname');

        $collectionModel = new \App\Models\CollectionModel();
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

    // Handle Request Cancellation (From My Books)
    public function cancelRequest()
    {
        $transactionModel = new \App\Models\TransactionModel();
        $logModel = new \App\Models\LogModel();

        $id = $this->request->getPost('id');
        $trans = $transactionModel->find($id);

        if ($trans) {
            $transactionModel->update($id, ['status' => 'Cancelled']);

            // --- LOG THE UPDATE ---
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
        $logModel = new \App\Models\LogModel();
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
   public function myBooks()
    {
        $transactionModel = new TransactionModel();
        $userId = session()->get('user_id');

        $notifs = $this->getNotifications();

        $data = [
            'active_borrows'   => $transactionModel->where('user_id_num', $userId)
                                                   ->whereIn('status', ['Approved', 'Borrowed'])
                                                   ->orderBy('id', 'DESC')->findAll(),
            'pending_requests' => $transactionModel->where('user_id_num', $userId)
                                                   ->where('status', 'Pending')
                                                   ->orderBy('id', 'DESC')->findAll(),
            'history'          => $transactionModel->where('user_id_num', $userId)
                                                   ->whereIn('status', ['Returned', 'Rejected', 'Cancelled'])
                                                   ->orderBy('id', 'DESC')->findAll(),
            'notifs'           => $notifs,
            'unreadCount'      => $this->countUnread($notifs)
        ];

        return view('borrower/my_books', $data);
    }
    public function profile()
    {
        return view('borrower/profile');
    }

    public function updateProfile()
    {
        // Database logic goes here 
        return redirect()->to('/borrower/profile')->with('success', 'Your personal information has been updated.');
    }

    public function updatePassword()
    {
        // Database logic goes here 
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

        // Database logic goes here 
        return redirect()->to('/borrower/profile')->with('success', 'Profile picture updated successfully!');
    }

}