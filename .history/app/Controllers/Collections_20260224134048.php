<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\LogModel; // Needed for the Audit Trail

class Collections extends BaseController
{
   public function index()
    {
        $collectionModel = new CollectionModel();
        
        $data = [
            // 1. Get all books for the table
            'collections' => $collectionModel->orderBy('id', 'DESC')->findAll(),
            
            // 2. Get unique lists for the dropdowns (ignores empty ones)
            'authors'  => $collectionModel->select('author')->distinct()->where('author !=', '')->findAll(),
            'subjects' => $collectionModel->select('subject')->distinct()->where('subject !=', '')->findAll(),
            'classes'  => $collectionModel->select('class')->distinct()->where('class !=', '')->findAll()
        ];

        return view('admin/collections', $data);
    }

    public function store()
    {
        $collectionModel = new CollectionModel();
        
        // 1. Basic validation (Ensure Title is provided)
        $rules = [
            'title' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Handle File Uploads (Optional: Cover Photo & Soft Copy)
        $coverPhotoName = null;
        $softCopyName = null;

        $coverFile = $this->request->getFile('cover_photo');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $coverPhotoName = $coverFile->getRandomName();
            $coverFile->move('uploads/covers', $coverPhotoName);
        }

        $softFile = $this->request->getFile('soft_copy');
        if ($softFile && $softFile->isValid() && !$softFile->hasMoved()) {
            $softCopyName = $softFile->getRandomName();
            $softFile->move('uploads/softcopies', $softCopyName);
        }

        // 3. Prepare the Data Array
        $data = [
            'type'          => $this->request->getPost('type'),
            'reading'       => $this->request->getPost('reading'),
            'class'         => $this->request->getPost('class'),
            'status'        => $this->request->getPost('status'),
            'title'         => $this->request->getPost('title'),
            'subject'       => $this->request->getPost('subject'),
            'author'        => $this->request->getPost('author'),
            'publisher'     => $this->request->getPost('publisher'),
            'place'         => $this->request->getPost('place'),
            'issued_date'   => $this->request->getPost('issued_date') ?: null,
            'accession_no'  => $this->request->getPost('accession_no'),
            'volume'        => $this->request->getPost('volume'),
            'isbn'          => $this->request->getPost('isbn'),
            'series'        => $this->request->getPost('series'),
            'location'      => $this->request->getPost('location'),
            'date_acquired' => $this->request->getPost('date_acquired') ?: null,
            'date_received' => $this->request->getPost('date_received') ?: null,
            'remarks'       => $this->request->getPost('remarks'),
            'url'           => $this->request->getPost('url'),
            'cover_photo'   => $coverPhotoName,
            'soft_copy'     => $softCopyName
        ];

        // 4. Save to Database & Log the Action
        if ($collectionModel->insert($data)) {
            
            $adminName = session()->get('fullname');
            $bookTitle = $data['title'];

            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Collections',
                'action'      => 'Add',
                'details'     => "Admin ($adminName) added a new collection item: '$bookTitle'."
            ]);

            return redirect()->back()->with('success', 'Collection added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add collection.');
        }
    }
}