<?php

namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;
use App\Models\TransactionModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportReport extends BaseController
{
    //converts html to pdf
    public function pdf()
    {
        //check if dompdf is installed
        if (!class_exists('\Dompdf\Dompdf')) {
            echo "<h1>DOMPDF not found!</h1>";
            echo "<p>Please run: <b>composer require dompdf/dompdf</b></p>";
            return;
        }

        //grab the filtered data
        $data = $this->getFilteredData();
        $html = $this->generateHtml($data);
        //remove images and fonts
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        //create the pdf and set it to a4 
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4' ); 
        $dompdf->render();
        //filename and into pdf
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

        return $data;
    }
    //html design para sa pdf
    private function generateHtml($data)
    {
        $adminName = session()->get('fullname') ?? 'Administrator';
        $currentDate = date('F d, Y h:i A');
        //styling setup
        $html = '
        <html>
        <head>
            <style>
                body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11px; color: #333; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
                .header h2 { margin: 0; color: #1e3a8a; font-size: 22px; }
                .header p { margin: 5px 0 0 0; font-size: 11px; color: #666; }
                .section-title { background-color: #e2e8f0; padding: 6px 10px; font-weight: bold; font-size: 13px; margin-top: 20px; color: #0f172a; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
                th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
                th { background-color: #1e3a8a; color: #ffffff; font-size: 10px; text-transform: uppercase; }
                .sub-text { font-size: 9px; color: #64748b; display: block; margin-top: 3px; }
                .link-text { color: #2563eb; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Commisions on Appointments</h2>
                <p><strong>Detailed Export Report</strong></p>
                <p>Date Generated: ' . $currentDate . ' | Prepared By: ' . $adminName . '</p>
            </div>';

        //paggawa ng collection table
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
                //url and dates format
                $urlHtml = !empty($row['url']) ? '<span class="sub-text">Link: <a href="'.esc($row['url']).'" class="link-text">'.esc($row['url']).'</a></span>' : '';
                $acqDate = !empty($row['date_acquired']) ? date('M d, Y', strtotime($row['date_acquired'])) : 'N/A';
                $recDate = !empty($row['date_received']) ? date('M d, Y', strtotime($row['date_received'])) : 'N/A';

                $html .= '<tr>
                            <td><strong>' . esc($row['accession_no'] ?: 'N/A') . '</strong><span class="sub-text">Class: ' . esc($row['class'] ?: 'N/A') . '</span></td>
                            <td><strong>' . esc($row['title']) . '</strong><span class="sub-text">' . esc($row['subject']) . '</span>' . $urlHtml . '</td>
                            <td>' . esc($row['author']) . '<span class="sub-text">Pub: ' . esc($row['publisher'] ?: 'N/A') . '</span></td>
                            <td>Type: ' . esc($row['type']) . '<span class="sub-text">Loc: ' . esc($row['location'] ?: 'N/A') . '</span><span class="sub-text">Acq: ' . $acqDate . ' | Rec: ' . $recDate . '</span></td>
                            <td><strong>' . esc($row['status']) . '</strong></td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        }

        //paggawa ng journal table
        if ($data['showJournals'] && !empty($data['journals'])) {
            $html .= '<div class="section-title">Journals Directory (' . count($data['journals']) . ' records)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="35%">Subject / Title</th>
                                <th width="20%">Author</th>
                                <th width="15%">Source & Session</th>
                                <th width="15%">Vol & Page</th>
                                <th width="15%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['journals'] as $row) {
                $html .= '<tr>
                            <td><strong>' . esc($row['subject']) . '</strong><span class="sub-text">Date: ' . ($row['date'] ? date('M d, Y', strtotime($row['date'])) : 'N/A') . '</span></td>
                            <td>' . esc($row['author']) . '</td>
                            <td>' . esc($row['source']) . '<span class="sub-text">Sess: ' . esc($row['session'] ?: 'N/A') . '</span></td>
                            <td>Vol ' . esc($row['volume'] ?: '-') . '<span class="sub-text">Pg: ' . esc($row['page'] ?: '-') . '</span></td>
                            <td><strong>' . esc($row['status'] ?? 'AVAILABLE') . '</strong></td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        }

        //paggawa ng transaction table
        if ($data['showTransactions'] && !empty($data['transactions'])) {
            $html .= '<div class="section-title">Library Transactions (' . count($data['transactions']) . ' records)</div>';
            $html .= '<table>
                        <thead>
                            <tr>
                                <th width="12%">Trans. ID</th>
                                <th width="25%">Borrower</th>
                                <th width="33%">Item Requested</th>
                                <th width="20%">Dates</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data['transactions'] as $row) {
                $tid = 'TRX-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                $retDate = !empty($row['date_returned']) ? date('M d, Y', strtotime($row['date_returned'])) : '---';
                
                $html .= '<tr>
                            <td><strong>' . $tid . '</strong></td>
                            <td>' . esc($row['user_name']) . '<span class="sub-text">ID: ' . esc($row['user_id_num']) . '</span></td>
                            <td>' . esc($row['collection_title']) . '<span class="sub-text">Reason: ' . esc($row['reason'] ?: 'N/A') . '</span></td>
                            <td>Req: ' . date('M d, Y', strtotime($row['date_requested'])) . '<span class="sub-text">Due: ' . ($row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : '---') . '</span><span class="sub-text">Ret: ' . $retDate . '</span></td>
                            <td><strong>' . esc($row['status']) . '</strong></td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        }

        //if walang matched sa filter! fallback!
        if (!$data['showCollections'] && !$data['showJournals'] && !$data['showTransactions']) {
            $html .= '<p style="text-align:center; padding: 30px; font-size: 14px;">No records matched your filter criteria, or no categories were selected.</p>';
        }

        $html .= '</body></html>';

        return $html;
    }
}