<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;
use App\Models\TransactionModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportReport extends BaseController
{
    public function pdf()
    {
        // Check if DOMPDF is installed
        if (!class_exists('\Dompdf\Dompdf')) {
            echo "<h1>DOMPDF not found!</h1>";
            echo "<p>Please run this command in your VS Code terminal:</p>";
            echo "<pre>composer require dompdf/dompdf</pre>";
            echo "<a href='" . base_url('admin/reports') . "'>Go Back</a>";
            return;
        }

        $data = $this->getFilteredData();
        $html = $this->generateHtml($data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Landscape is better for tables
        $dompdf->render();
        
        // Stream the PDF to the browser
        $dompdf->stream("CALIS_Library_Report_" . date('Ymd') . ".pdf", ["Attachment" => true]);
    }

    public function excel()
    {
        $data = $this->getFilteredData();

        // 1. Force download as Excel file
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=CALIS_Library_Report_" . date('Ymd') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        // 2. Build the table output
        echo "<table border='1'>";
        echo "<tr><th colspan='6' style='font-size: 16px; font-weight: bold;'>CALIS Library System - Generated Report</th></tr>";
        echo "<tr><th colspan='6'>Date Generated: " . date('F d, Y h:i A') . "</th></tr>";
        echo "<tr><th colspan='6'></th></tr>"; // Empty row for spacing

        // Collections Data
        if ($data['showCollections'] && !empty($data['collections'])) {
            echo "<tr><th colspan='6' style='background-color: #d9e1f2; font-weight: bold;'>Collections Inventory Report</th></tr>";
            echo "<tr style='background-color: #1e3a8a; color: #ffffff;'>
                    <th>Accession No.</th><th>Title</th><th>Author</th><th>Call No.</th><th>Issued Date</th><th>Status</th>
                  </tr>";
            foreach ($data['collections'] as $row) {
                echo "<tr>
                        <td>" . esc($row['accession_no']) . "</td>
                        <td>" . esc($row['title']) . "</td>
                        <td>" . esc($row['author']) . "</td>
                        <td>" . esc($row['class']) . "</td>
                        <td>" . esc($row['issued_date']) . "</td>
                        <td>" . esc($row['status']) . "</td>
                      </tr>";
            }
            echo "<tr><th colspan='6'></th></tr>"; // Empty row
        }

        // Journals Data
        if ($data['showJournals'] && !empty($data['journals'])) {
            echo "<tr><th colspan='6' style='background-color: #d9e1f2; font-weight: bold;'>Journals Directory Report</th></tr>";
            echo "<tr style='background-color: #17a2b8; color: #ffffff;'>
                    <th>Subject / Title</th><th>Author</th><th>Source</th><th>Volume</th><th>Page</th><th>Status</th>
                  </tr>";
            foreach ($data['journals'] as $row) {
                echo "<tr>
                        <td>" . esc($row['subject']) . "</td>
                        <td>" . esc($row['author']) . "</td>
                        <td>" . esc($row['source']) . "</td>
                        <td>" . esc($row['volume']) . "</td>
                        <td>" . esc($row['page']) . "</td>
                        <td>" . esc($row['status'] ?? 'AVAILABLE') . "</td>
                      </tr>";
            }
            echo "<tr><th colspan='6'></th></tr>"; // Empty row
        }

        // Transactions Data
        if ($data['showTransactions'] && !empty($data['transactions'])) {
            echo "<tr><th colspan='6' style='background-color: #d9e1f2; font-weight: bold;'>Library Transactions Report</th></tr>";
            echo "<tr style='background-color: #0f766e; color: #ffffff;'>
                    <th>Trans. ID</th><th>Borrower Name</th><th>Item Details</th><th>Requested On</th><th>Due Date</th><th>Status</th>
                  </tr>";
            foreach ($data['transactions'] as $row) {
                $tid = 'TRX-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                echo "<tr>
                        <td>" . $tid . "</td>
                        <td>" . esc($row['user_name']) . " (ID: " . esc($row['user_id_num']) . ")</td>
                        <td>" . esc($row['collection_title']) . "</td>
                        <td>" . date('M d, Y', strtotime($row['date_requested'])) . "</td>
                        <td>" . ($row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '---') . "</td>
                        <td>" . esc($row['status']) . "</td>
                      </tr>";
            }
        }

        echo "</table>";
        exit; // Stop execution to prevent CI4 from appending debug toolbars to the file
    }
    // --- HELPER: FETCH THE EXACT DATA BASED ON URL FILTERS ---
    // --- HELPER: FETCH THE EXACT DATA BASED ON URL FILTERS ---
    private function getFilteredData()
    {
        $data = [
            'showCollections'  => false,
            'showJournals'     => false,
            'showTransactions' => false,
            'collections'      => [],
            'journals'         => [],
            'transactions'     => [],
        ];

        // FIX: Default to all categories if the user hasn't actively filtered yet
        $categories = $this->request->getGet('categories');
        if (empty($categories)) {
            $categories = ['collections', 'journals', 'transactions'];
        }

        // 1. Collections Filter
        if (in_array('collections', $categories)) {
            $data['showCollections'] = true;
            $db = new CollectionModel();
            $f = $this->request->getGet('collections') ?? [];
            
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

        // 2. Journals Filter
        if (in_array('journals', $categories)) {
            $data['showJournals'] = true;
            $db = new JournalModel();
            $f = $this->request->getGet('journals') ?? [];

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

        // 3. Transactions Filter
        if (in_array('transactions', $categories)) {
            $data['showTransactions'] = true;
            $db = new TransactionModel();
            $f = $this->request->getGet('transactions') ?? [];

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

        return $data;
    }

    private function generateHtml($data)
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
                <p><strong>Official Generated Report</strong></p>
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
                            <td>' . esc($row['accession_no'] ?: 'N/A') . '</td>
                            <td>' . esc($row['title']) . '</td>
                            <td>' . esc($row['author']) . '</td>
                            <td>' . esc($row['class'] ?: 'N/A') . '</td>
                            <td>' . ($row['issued_date'] ? date('Y-m-d', strtotime($row['issued_date'])) : '---') . '</td>
                            <td>' . esc($row['status']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        }

        if ($data['showJournals'] && !empty($data['journals'])) {
            $html .= '<div class="section-title">Journals Directory Report (' . count($data['journals']) . ' items)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th>Subject / Title</th>
                                <th>Author</th>
                                <th>Source</th>
                                <th>Vol & Page</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['journals'] as $row) {
                $html .= '<tr>
                            <td>' . esc($row['subject']) . '</td>
                            <td>' . esc($row['author']) . '</td>
                            <td>' . esc($row['source']) . '</td>
                            <td>Vol ' . esc($row['volume'] ?: '-') . ', Pg ' . esc($row['page'] ?: '-') . '</td>
                            <td>' . ($row['date'] ? date('Y-m-d', strtotime($row['date'])) : '---') . '</td>
                            <td>' . esc($row['status'] ?? 'AVAILABLE') . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        }

        if ($data['showTransactions'] && !empty($data['transactions'])) {
            $html .= '<div class="section-title">Library Transactions Report (' . count($data['transactions']) . ' items)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th>Trans. ID</th>
                                <th>Borrower Name</th>
                                <th>Item Details</th>
                                <th>Requested On</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['transactions'] as $row) {
                $tid = 'TRX-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                $borrower = esc($row['user_name']) . ' (' . esc($row['user_id_num']) . ')';
                $html .= '<tr>
                            <td>' . $tid . '</td>
                            <td>' . $borrower . '</td>
                            <td>' . esc($row['collection_title']) . '</td>
                            <td>' . date('M d, Y', strtotime($row['date_requested'])) . '</td>
                            <td>' . ($row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '---') . '</td>
                            <td>' . esc($row['status']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        }

        if (!$data['showCollections'] && !$data['showJournals'] && !$data['showTransactions']) {
            $html .= '<p style="text-align:center; padding: 30px;">No records matched your filter criteria.</p>';
        }

        $html .= '</body></html>';

        return $html;
    }
}