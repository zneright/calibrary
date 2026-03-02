<?php

namespace App\Controllers;

class BorrowerController extends BaseController
{
    public function index()
    {
        return view('borrower/dashboard');
    }

        public function catalog()
    {
        $collectionModel = new \App\Models\CollectionModel();

        // 1. Grab parameters from the URL (Search & Filters)
        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status'); // 'all' or 'available'
        $types  = $this->request->getGet('type');   // Array of checkboxes

        // 2. Start building the database query
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
        $data = [
            'items'          => $query->orderBy('id', 'DESC')->paginate(10),
            'pager'          => $collectionModel->pager,
            'total_results'  => $collectionModel->countAllResults(false),
            'search'         => $search,
            'selectedStatus' => $status ?? 'all',
            'selectedTypes'  => $types ?? [] // Default to empty array if none checked
        ];

        return view('borrower/catalog', $data);
    }
public function submitRequest()
    {
        $transactionModel = new \App\Models\TransactionModel();
        
        // 1. Grab the form data
        $collectionId = $this->request->getPost('collection_id');
        $dateNeeded   = $this->request->getPost('date_needed');
        $reason       = $this->request->getPost('reason');
        $userId       = session()->get('user_id'); 
        $userName     = session()->get('fullname');

        // We need the Book Title for the transaction log, so let's quickly grab it from the database!
        $collectionModel = new \App\Models\CollectionModel();
        $book = $collectionModel->find($collectionId);
        $bookTitle = $book ? $book['title'] : 'Unknown Book';

        // 2. Save to the Transactions Table
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
        
        // 3. Log the action so Admins can see it in the Activity Logs
        $logModel = new \App\Models\LogModel();
        $logModel->insert([
            'user_name'   => $userName,
            'user_id_num' => $userId,
            'module'      => 'Borrower Catalog',
            'action'      => 'Request',
            'details'     => "Borrower requested: '$bookTitle' for $dateNeeded."
        ]);

        return redirect()->back()->with('success', 'Your request has been submitted successfully and is pending admin approval.');
    }
   public function myBooks()
    {
        $transactionModel = new \App\Models\TransactionModel();
        $userId = session()->get('user_id');

        // 1. Fetch Active Borrows (Approved but not returned yet)
        $data['active_borrows'] = $transactionModel->where('user_id_num', $userId)
                                                   ->whereIn('status', ['Approved', 'Borrowed'])
                                                   ->orderBy('id', 'DESC')
                                                   ->findAll();

        // 2. Fetch Pending Requests
        $data['pending_requests'] = $transactionModel->where('user_id_num', $userId)
                                                     ->where('status', 'Pending')
                                                     ->orderBy('id', 'DESC')
                                                     ->findAll();

        // 3. Fetch Borrow History (Returned or Rejected)
        $data['history'] = $transactionModel->where('user_id_num', $userId)
                                            ->whereIn('status', ['Returned', 'Rejected', 'Cancelled'])
                                            ->orderBy('id', 'DESC')
                                            ->findAll();

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