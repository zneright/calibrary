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
        
        // Forced to A4 Portrait to avoid horizontal "vacant" gaps
        $dompdf->setPaper('A4', 'portrait'); 
        $dompdf->render();

        $adminName = session()->get('fullname');
        $logModel = new LogModel();
        $logModel->insert([
            'user_name'   => $adminName,
            'user_id_num' => session()->get('user_id'),
            'module'      => 'Reports',
            'action'      => 'Download',
            'details'     => "Admin ($adminName) exported a compact A4 Library Report."
        ]);

        $dompdf->stream("CALIS_Report_" . date('Ymd') . ".pdf", ["Attachment" => true]);
    }

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
                /* Tight A4 Portrait Layout */
                @page { margin: 90px 30px 40px 30px; }
                
                header { 
                    position: fixed; top: -75px; left: 0px; right: 0px; height: 70px; 
                    border-bottom: 1.5px solid #1e3a8a; text-align: center; 
                }
                
                footer { 
                    position: fixed; bottom: -25px; left: 0px; right: 0px; height: 20px; 
                    border-top: 1px solid #eee; font-size: 7pt; color: #888; 
                }
                
                body { font-family: "Helvetica", sans-serif; font-size: 8.5pt; color: #222; line-height: 1.1; margin: 0; }
                
                .logo { position: absolute; left: 0; top: -5px; width: 45px; }
                
                .header-title h2 { margin: 0; color: #1e3a8a; font-size: 14pt; text-transform: uppercase; }
                .header-title p { margin: 1px 0; font-size: 9pt; color: #444; }
                
                .page-number:after { content: "Page " counter(page); }
                .float-left { float: left; }
                .float-right { float: right; }

                /* Compact Table Spacing */
                .section-title { 
                    background-color: #f8fafc; padding: 4px 8px; font-weight: bold; font-size: 9pt; 
                    margin-top: 12px; color: #1e3a8a; border-left: 3px solid #1e3a8a; 
                    border-bottom: 1px solid #cbd5e1;
                }
                
                table { width: 100%; border-collapse: collapse; margin-top: 5px; }
                th, td { border: 0.1pt solid #aaa; padding: 4px 6px; text-align: left; vertical-align: top; }
                th { background-color: #f1f5f9; color: #1e3a8a; font-size: 8pt; text-transform: uppercase; }
                
                .sub-text { font-size: 7pt; color: #666; display: block; margin-top: 1px; }
                .fw-bold { font-weight: bold; }
                .text-center { text-align: center; }
            </style>
        </head>
        <body>
            <header>
                <img src="' . $logoBase64 . '" class="logo">
                <div class="header-title">
                    <h2>Commission on Appointments</h2>
                    <p>Library Information System (CALIS v2.0)</p>
                    <small style="font-size: 7pt; color: #666;">Report Exported on ' . $currentDate . '</small>
                </div>
            </header>

            <footer>
                <div class="float-left">CALIS v2.0 | Prepared by: ' . $adminName . '</div>
                <div class="float-right page-number"></div>
            </footer>

            <main>';

        // COLLECTIONS - Compacted
        if ($data['showCollections'] && !empty($data['collections'])) {
            $html .= '<div class="section-title">Collections Inventory (' . count($data['collections']) . ')</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="15%">Ref No. / Class</th>
                                <th>Title & Subject</th>
                                <th width="20%">Author / Type</th>
                                <th width="12%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['collections'] as $row) {
                $html .= '<tr>
                            <td><span class="fw-bold">' . esc($row['accession_no']) . '</span><span class="sub-text">' . esc($row['class']) . '</span></td>
                            <td><span class="fw-bold">' . esc($row['title']) . '</span><span class="sub-text">' . esc($row['subject']) . '</span></td>
                            <td>' . esc($row['author']) . '<span class="sub-text">Type: ' . esc($row['type']) . '</span></td>
                            <td class="text-center">' . esc($row['status']) . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
        }

        // JOURNALS - Compacted
        if ($data['showJournals'] && !empty($data['journals'])) {
            $html .= '<div class="section-title">Journals Directory (' . count($data['journals']) . ')</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="40%">Subject / Title</th>
                                <th width="25%">Author / Source</th>
                                <th width="20%">Volume / Page</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['journals'] as $row) {
                $html .= '<tr>
                            <td><span class="fw-bold">' . esc($row['subject']) . '</span></td>
                            <td>' . esc($row['author']) . '<span class="sub-text">' . esc($row['source']) . '</span></td>
                            <td>Vol ' . esc($row['volume']) . '<span class="sub-text">Pg: ' . esc($row['page']) . '</span></td>
                            <td class="text-center">' . esc($row['status'] ?? 'AVAILABLE') . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
        }

        // TRANSACTIONS - Compacted
        if ($data['showTransactions'] && !empty($data['transactions'])) {
            $html .= '<div class="section-title">Library Transactions (' . count($data['transactions']) . ')</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="15%">Trans ID</th>
                                <th width="30%">Borrower Info</th>
                                <th>Item Details</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['transactions'] as $row) {
                $html .= '<tr>
                            <td><span class="fw-bold">TRX-' . str_pad($row['id'], 4, '0', STR_PAD_LEFT) . '</span></td>
                            <td>' . esc($row['user_name']) . '<span class="sub-text">ID: ' . esc($row['user_id_num']) . '</span></td>
                            <td>' . esc($row['collection_title']) . '</td>
                            <td class="text-center">' . esc($row['status']) . '</td>
                        </tr>';
            }
            $html .= '</tbody></table>';
        }

        if (empty($data['collections']) && empty($data['journals']) && empty($data['transactions'])) {
            $html .= '<p style="text-align:center; margin-top: 50px; color: #888;">No data records found for the selected criteria.</p>';
        }

        $html .= '</main></body></html>';

        return $html;
    }

    private function getFilteredData() {
        $data = [
            'showCollections'  => false,
            'showJournals'     => false,
            'showTransactions' => false,
            'collections'      => [],
            'journals'         => [],
            'transactions'     => [],
        ];

        $categories = $this->request->getGet('categories');
        if (empty($categories)) { $categories = ['collections', 'journals', 'transactions']; }

        if (in_array('collections', $categories)) {
            $data['showCollections'] = true;
            $db = new CollectionModel();
            $f = $this->request->getGet('collections') ?? [];
            if (!empty($f['type'])) $db->where('type', $f['type']);
            if (!empty($f['status'])) $db->where('status', $f['status']);
            if (!empty($f['title'])) $db->like('title', $f['title']);
            if (!empty($f['author'])) $db->like('author', $f['author']);
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