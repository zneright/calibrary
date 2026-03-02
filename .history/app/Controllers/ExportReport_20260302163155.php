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

        // --- LOGGING ACTION ---
        $adminName = session()->get('fullname');
        $logModel = new \App\Models\LogModel();
        $logModel->insert([
            'user_name'   => $adminName,
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Reports',
            'action'      => 'Download',
            'details'     => "Admin ($adminName) exported a PDF Library Report with Logo and Footer."
        ]);

        $dompdf->stream("CALIS_Report_" . date('Ymd') . ".pdf", ["Attachment" => true]);
    }
    //based on the filtereddata
    private function getFilteredData()
    {
        //default
        $data = [
            'showCollections'  => false,
            'showJournals'     => false,
            'showTransactions' => false,
            'collections'      => [],
            'journals'         => [],
            'transactions'     => [],
        ];

        //check kung anong checkboxes ang selected
        $categories = $this->request->getGet('categories');
        if (empty($categories)) {
            $categories = ['collections', 'journals', 'transactions']; 
        }

        // collection filter (recent first)
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
            
            $data['collections'] = $db->orderBy('id', 'DESC')->findAll();
        }

        // journal filter (recent first)
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
            
            $data['journals'] = $db->orderBy('id', 'DESC')->findAll();
        }

        // transaction filter (recent first)
        if (in_array('transactions', $categories)) {
            $data['showTransactions'] = true;
            $db = new TransactionModel();
            $f = $this->request->getGet('transactions') ?? [];

            if (!empty($f['trans_id'])) {
                // If user typed "TRX-00045", we convert it to "45"
                $tid = (int) str_ireplace('TRX-', '', $f['trans_id']);
                if ($tid > 0) {
                    $db->where('id', $tid);
                }
            }
            
            if (!empty($f['borrower'])) {
                $db->groupStart()
                ->like('user_name', $f['borrower'])
                ->orLike('user_id_num', $f['borrower'])
                ->groupEnd();
            }
            
            // Make sure 'status' is filtered correctly
            if (!empty($f['status'])) {
                $db->where('status', $f['status']);
            }

            if (!empty($f['item_details']))  $db->like('collection_title', $f['item_details']);
            if (!empty($f['requested_on']))  $db->where('DATE(date_requested)', $f['requested_on']);
            if (!empty($f['due_date']))      $db->where('due_date', $f['due_date']);
            if (!empty($f['date_returned'])) $db->where('DATE(date_returned)', $f['date_returned']);
            
            // Sort Newest First
            $data['transactions'] = $db->orderBy('id', 'DESC')->findAll();
        }

        return $data;
    }
    //html design para sa pdf
private function generateHtml($data)
{
    $adminName = session()->get('fullname') ?? 'Administrator';
    $currentDate = date('F d, Y h:i A');

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
            @page { margin: 120px 25px 60px 25px; }
            body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11px; color: #333; }
            
            /* Fixed Header Setup */
            header { 
                position: fixed; 
                top: -100px; 
                left: 0px; 
                right: 0px; 
                height: 100px; 
                border-bottom: 2px solid #1e3a8a;
                text-align: center;
            }

            /* Fixed Footer Setup */
            footer { 
                position: fixed; 
                bottom: -40px; 
                left: 0px; 
                right: 0px; 
                height: 40px; 
                border-top: 1px solid #ddd; 
                font-size: 10px; 
                color: #777; 
                padding-top: 5px;
            }

            .logo { position: absolute; left: 0; top: 10px; width: 65px; }
            .header-text { margin-top: 15px; }
            .header-text h2 { margin: 0; color: #1e3a8a; font-size: 18px; text-transform: uppercase; }
            
            /* Page Numbering Logic */
            .page-number:after { content: counter(page); }
            .footer-left { float: left; }
            .footer-right { float: right; }

            /* Table Styles */
            table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
            th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
            th { background-color: #1e3a8a; color: #ffffff; font-size: 10px; text-transform: uppercase; }
            .section-title { background-color: #e2e8f0; padding: 6px 10px; font-weight: bold; font-size: 13px; margin-top: 20px; color: #0f172a; }
            .sub-text { font-size: 9px; color: #64748b; display: block; margin-top: 3px; }
        </style>
    </head>
    <body>
        <header>
            <img src="' . $logoBase64 . '" class="logo">
            <div class="header-text">
                <h2>Commission on Appointments</h2>
                <p style="margin:0; font-size:11px;">Library Information System (CALIS v2.0)</p>
                <small>Date Generated: ' . $currentDate . ' | Prepared By: ' . $adminName . '</small>
            </div>
        </header>

        <footer>
            <div class="footer-left">
                Commission on Appointments - Library Information System
            </div>
            <div class="footer-right">
                Page <span class="page-number"></span>
            </div>
        </footer>

        <main>';

        // --- Collections Table ---
        if ($data['showCollections'] && !empty($data['collections'])) {
            $html .= '<div class="section-title">Collections Inventory (' . count($data['collections']) . ' records)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="12%">Acc / Call No.</th>
                                <th width="32%">Title & Subject</th>
                                <th width="16%">Author & Publisher</th>
                                <th width="28%">Details & Dates</th>
                                <th width="12%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['collections'] as $row) {
                $html .= '<tr>
                            <td><strong>' . esc($row['accession_no'] ?: 'N/A') . '</strong></td>
                            <td><strong>' . esc($row['title']) . '</strong></td>
                            <td>' . esc($row['author']) . '</td>
                            <td>Type: ' . esc($row['type']) . '</td>
                            <td><strong>' . esc($row['status']) . '</strong></td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        }

        // --- Repeat similar logic for Journals and Transactions ---

        if (!$data['showCollections'] && !$data['showJournals'] && !$data['showTransactions']) {
            $html .= '<p style="text-align:center; padding: 30px; font-size: 14px;">No records matched your filter criteria.</p>';
        }

    $html .= '
        </main>
    </body>
    </html>';

    return $html;
}
}