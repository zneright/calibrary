<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\LogModel; 
use App\Models\TransactionModel; 

class Collections extends BaseController
{
    public function index()
    {
        $collectionModel = new CollectionModel();
        // Grab all collections
        $data = [
            'collections' => $collectionModel->orderBy('id', 'DESC')->findAll(),
            'types'       => $collectionModel->select('type')->distinct()->where('type !=', '')->findAll(),
            'readings'    => $collectionModel->select('reading')->distinct()->where('reading !=', '')->findAll(),
            'classes'     => $collectionModel->select('class')->distinct()->where('class !=', '')->findAll(),
            'subjects'    => $collectionModel->select('subject')->distinct()->where('subject !=', '')->findAll(),
            'authors'     => $collectionModel->select('author')->distinct()->where('author !=', '')->findAll(),
            'publishers'  => $collectionModel->select('publisher')->distinct()->where('publisher !=', '')->findAll() 
        ];

        return view('admin/collections', $data);
    }

    //for storing in db
    public function store()
    {
        $collectionModel = new CollectionModel();
        
        //basic validation pede pa mag add for other inputs
        $rules = [
            'title' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        //file upload handling
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
        //all data to be stored in db
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
        //save to db and logs
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

    //inuupdate ang collection details sa existing
    public function update()
    {
        $collectionModel = new CollectionModel();
        $logModel = new LogModel();

        $id = $this->request->getPost('id');
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid Item ID.');
        }

        
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
            'url'           => $this->request->getPost('url')
        ];

        //upload new cover photo if meron
        $cover = $this->request->getFile('cover_photo');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $newName = $cover->getRandomName();
            $cover->move('uploads/covers', $newName);
            $data['cover_photo'] = $newName;
        }

        // Upload new Soft Copy if meronn
        $soft = $this->request->getFile('soft_copy');
        if ($soft && $soft->isValid() && !$soft->hasMoved()) {
            $softName = $soft->getRandomName();
            $soft->move('uploads/softcopies', $softName);
            $data['soft_copy'] = $softName;
        }

        // Update the collection item
        if ($collectionModel->update($id, $data)) {
            
            // If status is LOST or DAMAGED, update transactions
            if (in_array($data['status'], ['LOST', 'DAMAGED'])) {
                $transModel = new TransactionModel();
                
                //find active transactions
                $activeTrans = $transModel->where('collection_id', $id)
                                          ->whereIn('status', ['Borrowed', 'Renewing', 'Approved', 'Pending'])
                                          ->findAll();
                
                //update from LOST to lost (lowercase lang)
                $issueStatus = ucfirst(strtolower($data['status'])); 
                
                foreach ($activeTrans as $t) {
                    $transModel->update($t['id'], ['status' => $issueStatus]);
                }
            }

            //logs the update
            $logModel->insert([
                'user_name'   => session()->get('fullname'),
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Collections',
                'action'      => 'Update',
                'details'     => "Updated item: " . $data['title'] . " (Status: " . $data['status'] . ")"
            ]);
            return redirect()->back()->with('success', 'Collection updated successfully!');
        }

        return redirect()->back()->with('error', 'Update failed.');
    }

    //delete
    public function delete()
    {
        $collectionModel = new CollectionModel();
        $id = $this->request->getPost('id');
        
        if ($id && $collectionModel->delete($id)) {
            return redirect()->back()->with('success', 'Collection deleted.');
        }
        return redirect()->back()->with('error', 'Delete failed.');
    }
}