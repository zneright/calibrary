<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\LogModel; // Added LogModel for the Audit Trail

class Collections extends BaseController
{
    public function index()
    {
        $collectionModel = new CollectionModel();
        
        $data = [
            // Get all books for the table
            'collections' => $collectionModel->orderBy('id', 'DESC')->findAll(),
            
            // Get unique lists for the <datalist> dropdowns
            'types'       => $collectionModel->select('type')->distinct()->where('type !=', '')->findAll(),
            'readings'    => $collectionModel->select('reading')->distinct()->where('reading !=', '')->findAll(),
            'classes'     => $collectionModel->select('class')->distinct()->where('class !=', '')->findAll(),
            'subjects'    => $collectionModel->select('subject')->distinct()->where('subject !=', '')->findAll(),
            'authors'     => $collectionModel->select('author')->distinct()->where('author !=', '')->findAll()
        ];

        return view('admin/collections', $data);
    }

    // ==============================================
    // ADD NEW COLLECTION
    // ==============================================
    public function store()
    {
        $collectionModel = new CollectionModel();
        
        // Basic validation
        $rules = [
            'title' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle File Uploads
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

        // Prepare Data
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

        if ($collectionModel->insert($data)) {
            // --- LOG THE ACTION ---
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

    // ==============================================
    // DELETE COLLECTION
    // ==============================================
    public function delete()
    {
        $collectionModel = new CollectionModel();
        
        $id = $this->request->getPost('id');
        $title = $this->request->getPost('title');

        if ($collectionModel->delete($id)) {
            // --- LOG THE ACTION ---
            $adminName = session()->get('fullname');

            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Collections',
                'action'      => 'Delete',
                'details'     => "Admin ($adminName) deleted collection item: '$title'."
            ]);

            return redirect()->back()->with('success', 'Collection deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete collection.');
        }
    }

    public function update()
    {
        $collectionModel = new \App\Models\CollectionModel();
        
        $id = $this->request->getPost('id');
        $title = $this->request->getPost('title');

        // Prepare the Data Array with all text fields
        $data = [
            'type'          => $this->request->getPost('type'),
            'reading'       => $this->request->getPost('reading'),
            'class'         => $this->request->getPost('class'),
            'status'        => $this->request->getPost('status'),
            'title'         => $title,
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
            'url'           => $this->request->getPost('url')
        ];

        // Only update files IF they uploaded new ones
        $coverFile = $this->request->getFile('cover_photo');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $data['cover_photo'] = $coverFile->getRandomName();
            $coverFile->move('uploads/covers', $data['cover_photo']);
        }

        $softFile = $this->request->getFile('soft_copy');
        if ($softFile && $softFile->isValid() && !$softFile->hasMoved()) {
            $data['soft_copy'] = $softFile->getRandomName();
            $softFile->move('uploads/softcopies', $data['soft_copy']);
        }

        if ($collectionModel->update($id, $data)) {
            // --- LOG THE ACTION ---
            $adminName = session()->get('fullname');
            $logModel = new \App\Models\LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Collections',
                'action'      => 'Update',
                'details'     => "Admin ($adminName) updated details for collection item: '$title'."
            ]);

            return redirect()->back()->with('success', 'Collection updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update collection.');
        }
    }
}