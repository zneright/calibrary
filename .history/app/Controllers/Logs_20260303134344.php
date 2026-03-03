<?php
namespace App\Controllers;
use App\Models\LogModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Logs extends BaseController
{
    public function index()
    {
        $logModel = new LogModel();
        
        // Fetch all logs initially for the view
        $data = [
            'logs' => $logModel->orderBy('id', 'DESC')->findAll()
        ];

        return view('admin/logs', $data);
    }

    public function exportPdf()
    {
        $logModel = new LogModel();
        
        // 1. Capture all filters from the form
        $moduleFilter = $this->request->getGet('module');
        $actionFilter = $this->request->getGet('action');
        $startDate    = $this->request->getGet('start_date');
        $endDate      = $this->request->getGet('end_date');
        
        // 2. Apply filters to the database query
        if (!empty($moduleFilter)) {
            $logModel->where('module', $moduleFilter);
        }
        if (!empty($actionFilter)) {
            $logModel->like('action', $actionFilter); // Using like() to catch variations
        }
        if (!empty($startDate)) {
            $logModel->where('DATE(created_at) >=', $startDate);
        }
        if (!empty($endDate)) {
            $logModel->where('DATE(created_at) <=', $endDate);
        }

        // Fetch the filtered results
        $logs = $logModel->orderBy('id', 'DESC')->findAll();

        // 3. Create a nice subtitle for the PDF showing what filters were applied
        $filterStrings = [];
        if (!empty($startDate)) $filterStrings[] = "From: " . date('M d, Y', strtotime($startDate));
        if (!empty($endDate))   $filterStrings[] = "To: " . date('M d, Y', strtotime($endDate));
        if (!empty($moduleFilter)) $filterStrings[] = "Module: " . $moduleFilter;
        if (!empty($actionFilter)) $filterStrings[] = "Action: " . $actionFilter;
        
        $filterDisplay = empty($filterStrings) ? "Showing All Logs" : implode(' | ', $filterStrings);

        $adminName = session()->get('fullname') ?? 'Administrator';
        $currentDate = date('F d, Y h:i A');

        $path = FCPATH . 'images/logo.png'; 
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
                body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
                
                /* Logo and Header Centering */
                .header { 
                    text-align: center; 
                    margin-bottom: 20px; 
                    border-bottom: 2px solid #1e3a8a; 
                    padding-bottom: 10px; 
                }
                .logo-container {
                    text-align: center;
                    width: 100%;
                    margin-bottom: 5px;
                }
                .logo { 
                    width: 70px; /* Adjusted size */
                    height: auto;
                }
                .header-text h2 { 
                    margin: 5px 0 0 0; 
                    color: #1e3a8a; 
                    font-size: 18px; 
                    text-transform: uppercase; 
                }
                .header-text p { margin: 2px 0; font-size: 11px; color: #444; }
                .header-text small { color: #666; font-size: 9px; }

                /* Table Styling */
                .section-title { background-color: #e2e8f0; padding: 6px 10px; font-weight: bold; font-size: 13px; margin-top: 20px; color: #0f172a; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
                th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
                th { background-color: #1e3a8a; color: #ffffff; font-size: 10px; text-transform: uppercase; }
                
                .sub-text { font-size: 9px; color: #64748b; display: block; margin-top: 3px; }
                
                @page { margin: 40px 25px; }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . $logoBase64 . '" class="logo">
                <div class="header-text">
                    <h2>Commission on Appointments</h2>
                    <p style="margin:0; font-size:11px;">Library Information System (CALIS v2.0)</p>
                    <small>Date Generated: ' . $currentDate . ' | Prepared By: ' . $adminName . '</small>
                </div>
            </div>';

        $html .= '<div class="section-title">System Activity Logs (' . count($logs) . ' records)</div>';
        $html .= '<p style="margin-top: 5px; font-size: 10px; font-weight: bold; color: #b91c1c;">Filters Applied: ' . $filterDisplay . '</p>';

        $html .= '<table>
                    <thead>
                        <tr>
                            <th width="15%">Timestamp</th>
                            <th width="20%">User (ID)</th>
                            <th width="15%">Module</th>
                            <th width="15%">Action</th>
                            <th width="35%">Details</th>
                        </tr>
                    </thead>
                    <tbody>';

        if (!empty($logs)) {
            foreach ($logs as $log) {
                $html .= '
                    <tr>
                        <td><strong>' . date('M d, Y', strtotime($log['created_at'])) . '</strong><span class="sub-text">' . date('h:i A', strtotime($log['created_at'])) . '</span></td>
                        <td><strong>' . esc($log['user_name']) . '</strong><span class="sub-text">ID: ' . esc($log['user_id_num']) . '</span></td>
                        <td>' . esc($log['module']) . '</td>
                        <td><strong>' . esc($log['action']) . '</strong></td>
                        <td>' . esc($log['details']) . '</td>
                    </tr>';
            }
        } else {
            $html .= '<tr><td colspan="5" style="text-align:center; padding: 20px;">No logs found matching your filters.</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $newLogModel = new LogModel();
        $newLogModel->insert([
            'user_name'   => $adminName,
            'user_id_num' => session()->get('user_id') ?? 'N/A',
            'module'      => 'Reports',
            'action'      => 'Download',
            'details'     => 'Exported System Activity Logs to PDF. Filters: [' . $filterDisplay . ']'
        ]);

        $dompdf->stream("System_Logs_" . date('Ymd') . ".pdf", ["Attachment" => true]);
        
        exit(); 
    }
}