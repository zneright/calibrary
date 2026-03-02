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
        $dompdf->setPaper('A4', 'landscape'); // Landscape is best for multi-column library tables
        $dompdf->render();

        $adminName = session()->get('fullname');
        $logModel = new LogModel();
        $logModel->insert([
            'user_name'   => $adminName,
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Reports',
            'action'      => 'Download',
            'details'     => "Admin ($adminName) exported a PDF Library Report."
        ]);

        $dompdf->stream("CALIS_Report_" . date('Ymd') . ".pdf", ["Attachment" => true]);
    }

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
                /* Setup page margins - matches header/footer height to prevent overlapping */
                @page { margin: 100px 30px 60px 30px; }

                /* Fixed Header */
                header { 
                    position: fixed; top: -80px; left: 0px; right: 0px; height: 80px; 
                    border-bottom: 2px solid #1e3a8a; text-align: center; 
                }
                
                /* Fixed Footer */
                footer { 
                    position: fixed; bottom: -40px; left: 0px; right: 0px; height: 30px; 
                    border-top: 1px solid #ddd; font-size: 9px; color: #777; padding-top: 5px; 
                }
                
                body { font-family: "Helvetica", "Arial", sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
                
                /* Header Content */
                .logo { position: absolute; left: 0; top: 0; width: 60px; }
                .header-title { margin-top: 5px; }
                .header-title h2 { margin: 0; color: #1e3a8a; font-size: 18px; text-transform: uppercase; }
                .header-title p { margin: 2px 0; font-size: 11px; font-weight: bold; }
                
                /* Footer Utils */
                .page-number:after { content: "Page " counter(page); }
                .float-left { float: left; }
                .float-right { float: right; }

                /* Table Styling */
                .section-title { 
                    background-color: #f1f5f9; padding: 6px 10px; font-weight: bold; font-size: 12px; 
                    margin-top: 15px; color: #0f172a; border-left: 4px solid #1e3a8a; 
                }
                table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
                th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
                th { background-color: #1e3a8a; color: #ffffff; font-size: 9px; text-transform: uppercase; }
                .sub-text { font-size: 8px; color: #64748b; display: block; margin-top: 2px; }
            </style>
        </head>
        <body>
            <header>
                <img src="' . $logoBase64 . '" class="logo">
                <div class="header-title">
                    <h2>Commission on Appointments</h2>
                    <p>Library Information System (CALIS v2.0)</p>
                    <small>Report Generated: ' . $currentDate . '</small>
                </div>
            </header>

            <footer>
                <div class="float-left">Commission on Appointments Library Information System | Prepared by: ' . $adminName . '</div>
                <div class="float-right page-number"></div>
            </footer>

            <main>';

        // COLLECTIONS
        if ($data['showCollections'] && !empty($data['collections'])) {
            $html .= '<div class="section-title">Collections Inventory (' . count($data['collections']) . ' items)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="12%">Acc No.</th>
                                <th width="33%">Title & Subject</th>
                                <th width="20%">Author</th>
                                <th width="25%">Details</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['collections'] as $row) {
                $html .= '<tr>
                            <td><strong>' . esc($row['accession_no']) . '</strong></td>
                            <td><strong>' . esc($row['title']) . '</strong><span class="sub-text">' . esc($row['subject']) . '</span></td>
                            <td>' . esc($row['author']) . '</td>
                            <td>Type: ' . esc($row['type']) . '<br>Acquired: ' . ($row['date_acquired'] ?? 'N/A') . '</td>
                            <td>' . esc($row['status']) . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
        }

        // JOURNALS
        if ($data['showJournals'] && !empty($data['journals'])) {
            $html .= '<div class="section-title">Journals Directory (' . count($data['journals']) . ' items)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="40%">Subject / Title</th>
                                <th width="20%">Author</th>
                                <th width="20%">Source</th>
                                <th width="10%">Vol / Page</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['journals'] as $row) {
                $html .= '<tr>
                            <td><strong>' . esc($row['subject']) . '</strong></td>
                            <td>' . esc($row['author']) . '</td>
                            <td>' . esc($row['source']) . '</td>
                            <td>' . esc($row['volume']) . ' / ' . esc($row['page']) . '</td>
                            <td>' . esc($row['status'] ?? 'AVAILABLE') . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
        }

        // TRANSACTIONS
        if ($data['showTransactions'] && !empty($data['transactions'])) {
            $html .= '<div class="section-title">Library Transactions (' . count($data['transactions']) . ' items)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="15%">Trans. ID</th>
                                <th width="25%">Borrower</th>
                                <th width="35%">Item Requested</th>
                                <th width="15%">Due Date</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['transactions'] as $row) {
                $html .= '<tr>
                            <td><strong>TRX-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT) . '</strong></td>
                            <td>' . esc($row['user_name']) . '</td>
                            <td>' . esc($row['collection_title']) . '</td>
                            <td>' . ($row['due_date'] ?? '---') . '</td>
                            <td>' . esc($row['status']) . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
        }

        if (!$data['showCollections'] && !$data['showJournals'] && !$data['showTransactions']) {
            $html .= '<p style="text-align:center; padding-top: 50px;">No records matched your selection.</p>';
        }

        $html .= '</main></body></html>';

        return $html;
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
            if (!empty($f['type'])) $db->where('type', $f['type']);
            if (!empty($f['title'])) $db->like('title', $f['title']);
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
}