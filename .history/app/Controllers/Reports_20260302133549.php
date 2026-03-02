<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;
use App\Models\TransactionModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Reports extends BaseController
{
    public function index()
    {
        $data = $this->getReportData();
        return view('admin/reports', $data);
    }

    // -------------------------------------------------------------
    // EXPORT TO PDF
    // -------------------------------------------------------------
    public function exportPdf()
    {
        if (!class_exists('\Dompdf\Dompdf')) {
            return "Please run 'composer require dompdf/dompdf' in your terminal first.";
        }

        $data = $this->getReportData();
        $html = $this->generateReportHtml($data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Landscape fits tables better
        $dompdf->render();
        
        $dompdf->stream("CALIS_Library_Report.pdf", ["Attachment" => true]);
    }

    // -------------------------------------------------------------
    // EXPORT TO EXCEL
    // -------------------------------------------------------------
    public function exportExcel()
    {
        $data = $this->getReportData();
        $html = $this->generateReportHtml($data);

        // Tell the browser to treat this HTML as an Excel file
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=CALIS_Library_Report.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $html;
        exit;
    }

    // -------------------------------------------------------------
    // HELPER: FETCH FILTERED DATA
    // -------------------------------------------------------------
    private function getReportData()
    {
        $collectionModel = new CollectionModel();
        $journalModel = new JournalModel();
        $transactionModel = new TransactionModel();

        $data = [
            'showReports'      => false,
            'showCollections'  => false,
            'showJournals'     => false,
            'showTransactions' => false,
            'collections'      => [],
            'journals'         => [],
            'transactions'     => [],
            'cFilter'          => $this->request->getGet('collections') ?? [],
            'jFilter'          => $this->request->getGet('journals') ?? [],
            'tFilter'          => $this->request->getGet('transactions') ?? [],
            'selectedCats'     => $this->request->getGet('categories') ?? ['collections', 'journals', 'transactions']
        ];

        if ($this->request->getGet('categories') || $this->request->getGet('export')) {
            $data['showReports'] = true;
            $categories = $this->request->getGet('categories') ?? [];

            // 1. Collections
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
                
                $data['collections'] = $db->findAll();
            }

            // 2. Journals
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
                
                $data['journals'] = $db->findAll();
            }

            // 3. Transactions
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
                
                $data['transactions'] = $db->findAll();
            }
        }

        return $data;
    }

    // -------------------------------------------------------------
    // HELPER: BUILD HTML STRUCTURE FOR EXPORT
    // -------------------------------------------------------------
    private function generateReportHtml($data)
    {
        $adminName = session()->get('fullname') ?? 'Administrator';
        $currentDate = date('F d, Y h:i A');

        $html = '
        <html>
        <head>
            <style>
                body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #333; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
                .header h2 { margin: 0; color: #1e3a8a; font-size: 24px; }
                .header p { margin: 5px 0 0 0; font-size: 12px; color: #666; }
                .section-title { background-color: #f4f7f9; padding: 8px; font-weight: bold; font-size: 14px; margin-top: 20px; border-left: 4px solid #1e3a8a; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #1e3a8a; color: #ffffff; font-size: 11px; text-transform: uppercase; }
                td { font-size: 11px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>CALIS Library System</h2>
                <p><strong>Generated Report</strong></p>
                <p>Date Generated: ' . $currentDate . ' | Prepared By: ' . $adminName . '</p>
            </div>';

        if ($data['showCollections'] && !empty($data['collections'])) {
            $html .= '<div class="section-title">Collections Inventory Report (' . count($data['collections']) . ' items)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th>Accession No.</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Call No.</th>
                                <th>Issued Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['collections'] as $row) {
                $html .= '<tr>