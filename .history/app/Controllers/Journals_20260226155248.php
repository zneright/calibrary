<?php
namespace App\Controllers;

use App\Models\JournalModel;
use App\Models\LogModel; 

class Journals extends BaseController
{
    public function index()
    {
        $journalModel = new JournalModel();
        
        $data = [
            'journals' => $journalModel->orderBy('id', 'DESC')->findAll(),
            
            // Datalists for dropdown+textbox
            'subjects' => $journalModel->select('subject')->distinct()->where('subject !=', '')->findAll(),
            'authors'  => $journalModel->select('author')->distinct()->where('author !=', '')->findAll(),
            'sources'  => $journalModel->select('source')->distinct()->where('source !=', '')->findAll(),
            'sessions' => $journalModel->select('session')->distinct()->where('session !=', '')->findAll(),
            'volumes'  => $journalModel->select('volume')->distinct()->where('volume !=', '')->findAll(),
            'pages'    => $journalModel->select('page')->distinct()->where('page !=', '')->findAll()
        ];

        return view('admin/journals', $data);
    }

    public function store()
    {
        $journalModel = new JournalModel();
        
        $data = [
            'subject' => $this->request->getPost('subject'),
            'author'  => $this->request->getPost('author'),
            'date'    => $this->request->getPost('date') ?: null,
            'source'  => $this->request->getPost('source'),
            'session' => $this->request->getPost('session'),
            'volume'  => $this->request->getPost('volume'),
            'page'    => $this->request->getPost('page'),
            'status'  => $this->request->getPost('status') // <-- Added Status
        ];

        if ($journalModel->insert($data)) {
            $adminName = session()->get('fullname');
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Journals',
                'action'      => 'Add',
                'details'     => "Admin ($adminName) added a new journal: '{$data['subject']}'."
            ]);

            return redirect()->back()->with('success', 'New journal added successfully!');
        }
        return redirect()->back()->with('error', 'Failed to add journal.');
    }

    public function update()
    {
        $journalModel = new JournalModel();
        $id = $this->request->getPost('id');
        
        $data = [
            'subject' => $this->request->getPost('subject'),
            'author'  => $this->request->getPost('author'),
            'date'    => $this->request->getPost('date') ?: null,
            'source'  => $this->request->getPost('source'),
            'session' => $this->request->getPost('session'),
            'volume'  => $this->request->getPost('volume'),
            'page'    => $this->request->getPost('page'),
            'status'  => $this->request->getPost('status') 
        ];

        if ($journalModel->update($id, $data)) {
            $adminName = session()->get('fullname');
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Journals',
                'action'      => 'Update',
                'details'     => "Admin ($adminName) updated journal details for: '{$data['subject']}'."
            ]);

            return redirect()->back()->with('success', 'Journal updated successfully!');
        }
        return redirect()->back()->with('error', 'Failed to update journal.');
    }

    public function delete()
    {
        $journalModel = new JournalModel();
        $id = $this->request->getPost('id');
        $subject = $this->request->getPost('subject');

        if ($journalModel->delete($id)) {
            $adminName = session()->get('fullname');
            $logModel = new LogModel();
            $logModel->insert([
                'user_name'   => $adminName,
                'user_id_num' => session()->get('user_id'),
                'module'      => 'Journals',
                'action'      => 'Delete',
                'details'     => "Admin ($adminName) deleted journal: '$subject'."
            ]);

            return redirect()->back()->with('success', 'Journal deleted successfully!');
        }
        return redirect()->back()->with('error', 'Failed to delete journal.');
    }
}