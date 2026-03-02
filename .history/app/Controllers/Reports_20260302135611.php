<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;
use App\Models\TransactionModel;

class Reports extends BaseController
{
    public function index()
    {
        $collectionModel = new CollectionModel();
        $journalModel = new JournalModel();
        $transactionModel = new TransactionModel();

        // Default empty state
        $data = [
            'showReports'      => false,
            'showCollections'  => false,
            'showJournals'     => false,
            'showTransactions' => false,
            'collections'      => [],
            'journals'         => [],
            'transactions'     => [],
            // Remember user input to keep the form sticky
            'cFilter'          => $this->request->getGet('collections') ?? [],
            'jFilter'          => $this->request->getGet('journals') ?? [],
            'tFilter'          => $this->request->getGet('transactions') ?? [],
            'selectedCats'     => $this->request->getGet('categories') ?? ['collections', 'journals', 'transactions']
        ];

        // If categories are passed, the user clicked "Process Report Data"
        if ($this->request->getGet('categories')) {
            $data['showReports'] = true;
            $categories = $this->request->getGet('categories');

            // 1. Process Collections Filter (Sorted Recent First)
            if (in_array('collections', $categories)) {
                $data['showCollections'] = true;
                $db = $collectionModel;
                $f = $data['cFilter'];
                
                if (!empty($f['type']))          $db->where('type', $f['type']);
                if (!empty($f['reading']))       $db->where('reading', $f['reading']);
                if (!empty($f['class']))         $db->where('class', $f['class']);
                if (!empty($f['status']))        $db->where('status', $f['status']);
                if (!empty($f['title']))         $db->like('title', $f['title']);
                if (!empty($f['subject']))       $db->like('subject', $f['subject']);
                if (!empty($f['author']))        $db->like('author', $f['author']);
                if (!empty($f['publisher']))     $db->like('publisher', $f['publisher']);
                if (!empty($f['issued_date']))   $db->where('issued_date', $f['issued_date']);
                if (!empty($f['accession_no']))  $db->where('accession_no', $f['accession_no']);
                if (!empty($f['volume']))        $db->where('volume', $f['volume']);
                if (!empty($f['isbn']))          $db->where('isbn', $f['isbn']);
                if (!empty($f['series']))        $db->where('series', $f['series']);
                if (!empty($f['location']))      $db->like('location', $f['location']);
                if (!empty($f['date_acquired'])) $db->where('date_acquired', $f['date_acquired']);
                if (!empty($f['date_received'])) $db->where('date_received', $f['date_received']);
                
                $data['collections'] = $db->orderBy('id', 'DESC')->findAll();
            }

            // 2. Process Journals Filter (Sorted Recent First)
            if (in_array('journals', $categories)) {
                $data['showJournals'] = true;
                $db = $journalModel;
                $f = $data['jFilter'];

                if (!empty($f['subject_title'])) $db->like('subject', $f['subject_title']);
                if (!empty($f['status']))        $db->where('status', $f['status']);
                if (!empty($f['author']))        $db->like('author', $f['author']);
                if (!empty($f['date']))          $db->where('date', $f['date']);
                if (!empty($f['source']))        $db->like('source', $f['source']);
                if (!empty($f['session']))       $db->where('session', $f['session']);
                if (!empty($f['volume']))        $db->where('volume', $f['volume']);
                if (!empty($f['page']))          $db->where('page', $f['page']);
                
                $data['journals'] = $db->orderBy('id', 'DESC')->findAll();
            }

            // 3. Process Transactions Filter (Sorted Recent First)
            if (in_array('transactions', $categories)) {
                $data['showTransactions'] = true;
                $db = $transactionModel;
                $f = $data['tFilter'];

                if (!empty($f['trans_id'])) {
                    $tid = (int) str_replace('TRX-', '', strtoupper($f['trans_id']));
                    $db->where('id', $tid);
                }
                if (!empty($f['borrower'])) {
                    $db->groupStart()
                       ->like('user_name', $f['borrower'])
                       ->orLike('user_id_num', $f['borrower'])
                       ->groupEnd();
                }
                if (!empty($f['item_details']))  $db->like('collection_title', $f['item_details']);
                if (!empty($f['requested_on']))  $db->where('DATE(date_requested)', $f['requested_on']);
                if (!empty($f['due_date']))      $db->where('due_date', $f['due_date']);
                if (!empty($f['status']))        $db->where('status', $f['status']);
                if (!empty($f['date_returned'])) $db->where('DATE(date_returned)', $f['date_returned']);
                
                $data['transactions'] = $db->orderBy('id', 'DESC')->findAll();
            }
        }

        return view('admin/reports', $data);
    }
}