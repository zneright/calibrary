<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;
use App\Models\TransactionModel;
use App\Models\LogModel;    
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportReport extends BaseController
{
    //converts html to pdf
  public function pdf()
    {
        if (!class_exists('\Dompdf\Dompdf')) {
            echo "<h1>DOMPDF not found!</h1>";
            return;
        }

        $data = $this->getFilteredData();
        $html = $this->generateHtml($data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4'); 
        $dompdf->render();

        $adminName = session()->get('fullname');
        $logModel = new LogModel();
        $logModel->insert([
            'user_name'   => $adminName,
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Reports',
            'action'      => 'Download',
            'details'     => "Admin ($adminName) exported a PDF Library Report with Logo and Footer."
        ]);

        $dompdf->stream("CALIS_Report_" . date('Ymd') . ".pdf", ["Attachment" => true]);
    }

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

        $categories = $this->request->getGet('categories');
        if (empty($categories)) {
            $categories = ['collections', 'journals', 'transactions']; 
        }

        if (in_array('collections', $categories)) {
            $data['showCollections'] = true;
            $db = new CollectionModel();
            $f = $this->request->getGet('collections') ?? [];
            if (!empty($f['type']))          $db->where('type', $f['type']);
            if (!empty($f['status']))        $db->where('status', $f['status']);
            if (!empty($f['title']))         $db->like('title', $f['title']);
            if (!empty($f['author']))        $db->like('author', $f['author']);
            $data['collections'] = $db->orderBy('id', 'DESC')->findAll();
        }

        if (in_array('journals', $categories)) {
            $data['showJournals'] = true;
            $db = new JournalModel();
            $f = $this->request->getGet('journals') ?? [];
            if (!empty($f['subject_title'])) $db->like('subject', $f['subject_title']);
            $data['journals'] = $db->orderBy('id', 'DESC')->findAll();
        }

        if (in_array('transactions', $categories)) {
            $data['showTransactions'] = true;
            $db = new TransactionModel();
            $f = $this->request->getGet('transactions') ?? [];
            if (!empty($f['trans_id'])) {
                $tid = (int) str_ireplace('TRX-', '', $f['trans_id']);
                if ($tid > 0) $db->where('id', $tid);
            }
            $data['transactions'] = $db->orderBy('id', 'DESC')->findAll();
        }

        return $data;
    }
    //html design para sa pdf
private function generateHtml($data)
    {
        $adminName = session()->get('fullname') ?? 'Administrator';
        $currentDate = date('F d, Y h:i A');

        // Logo Base64 Conversion
        $path = FCPATH . 'images/ca_logo.png'; 
        $logoBase64 = '';
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        $html = '
        <html>
        <head>
            <style>
                @page { margin: 110px 25px 50px 25px; }
                header { position: fixed; top: -90px; left: 0px; right: 0px; height: 90px; border-bottom: 2px solid #1e3a8a; text-align: center; }
                footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 30px; border-top: 1px solid #ddd; font-size: 9px; color: #777; padding-top: 5px; }
                
                body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11px; color: #333; }
                .logo { position: absolute; left: 0; top: 0; width: 60px; }
                .header-title h2 { margin: 0; color: #1e3a8a; font-size: 18px; text-transform: uppercase; }
                
                .float-left { float: left; }
                .float-right { float: right; }
                .page-number:after { content: "Page " counter(page); }

                .section-title { background-color: #e2e8f0; padding: 6px 10px; font-weight: bold; font-size: 13px; margin-top: 20px; color: #0f172a; border-left: 4px solid #1e3a8a; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
                th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
                th { background-color: #1e3a8a; color: #ffffff; font-size: 10px; text-transform: uppercase; }
                .sub-text { font-size: 9px; color: #64748b; display: block; margin-top: 3px; }
            </style>
        </head>
        <body>
            <header>
                <img src="' . $logoBase64 . '" class="logo">
                <div class="header-title">
                    <h2>Commission on Appointments</h2>
                    <p style="margin:0; font-size:11px;">Library Information System (CALIS v2.0)</p>
                    <small>Generated: ' . $currentDate . '</small>
                </div>
            </header>

            <footer>
                <div class="float-left">Commission on Appointments Library Information System | Prepared by: ' . $adminName . '</div>
                <div class="float-right page-number"></div>
            </footer>

            <main>';

        if ($data['showCollections'] && !empty($data['collections'])) {
            $html .= '<div class="section-title">Collections Inventory (' . count($data['collections']) . ' records)</div>';
            $html .= '<table><thead><tr><th width="15%">Acc No.</th><th width="35%">Title & Subject</th><th width="20%">Author</th><th width="20%">Details</th><th width="10%">Status</th></tr></thead><tbody>';
            foreach ($data['collections'] as $row) {
                $html .= '<tr><td>' . esc($row['accession_no']) . '</td><td>' . esc($row['title']) . '</td><td>' . esc($row['author']) . '</td><td>' . esc($row['type']) . '</td><td>' . esc($row['status']) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        }

        if ($data['showJournals'] && !empty($data['journals'])) {
            $html .= '<div class="section-title">Journals Directory (' . count($data['journals']) . ' records)</div>';
            $html .= '<table><thead><tr><th width="40%">Subject / Title</th><th width="25%">Author</th><th width="20%">Source</th><th width="15%">Status</th></tr></thead><tbody>';
            foreach ($data['journals'] as $row) {
                $html .= '<tr><td>' . esc($row['subject']) . '</td><td>' . esc($row['author']) . '</td><td>' . esc($row['source']) . '</td><td>' . esc($row['status'] ?? 'AVAILABLE') . '</td></tr>';
            }
            $html .= '</tbody></table>';
        }

        if ($data['showTransactions'] && !empty($data['transactions'])) {
            $html .= '<div class="section-title">Library Transactions (' . count($data['transactions']) . ' records)</div>';
            $html .= '<table><thead><tr><th width="15%">Trans. ID</th><th width="25%">Borrower</th><th width="35%">Item</th><th width="15%">Due Date</th><th width="10%">Status</th></tr></thead><tbody>';
            foreach ($data['transactions'] as $row) {
                $html .= '<tr><td>TRX-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT) . '</td><td>' . esc($row['user_name']) . '</td><td>' . esc($row['collection_title']) . '</td><td>' . ($row['due_date'] ?? '---') . '</td><td>' . esc($row['status']) . '</td></tr>';
            }
            $html .= '</tbody></table>';
        }

        if (!$data['showCollections'] && !$data['showJournals'] && !$data['showTransactions']) {
            $html .= '<p style="text-align:center; padding: 30px; font-size: 14px;">No records matched your filter criteria.</p>';
        }

        $html .= '</main></body></html>';

        return $html;
    }
}