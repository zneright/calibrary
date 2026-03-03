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

        // 4. Build the HTML for the PDF
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; }
                h2 { color: #1a2942; margin-bottom: 5px; }
                .subtitle { font-size: 11px; color: #555; margin-top: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f4f4f4; color: #333; }
                .timestamp { font-size: 10px; color: #555; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>System Activity Logs</h2>
                <p class="subtitle">Generated on: ' . date('F d, Y h:i A') . '</p>
                <p class="subtitle" style="font-weight:bold; color:#b91c1c;">' . $filterDisplay . '</p>
            </div>
            <table>
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
                        <td class="timestamp">' . date('M d, Y h:i A', strtotime($log['created_at'])) . '</td>
                        <td>' . esc($log['user_name']) . ' <br><small>(' . esc($log['user_id_num']) . ')</small></td>
                        <td>' . esc($log['module']) . '</td>
                        <td>' . esc($log['action']) . '</td>
                        <td>' . esc($log['details']) . '</td>
                    </tr>';
            }
        } else {
            $html .= '<tr><td colspan="5" style="text-align:center; padding: 20px;">No logs found matching your filters.</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $newLogModel = new LogModel();
        $newLogModel->insert([
            'user_name'   => session()->get('fullname') ?? 'Admin',
            'user_id_num' => session()->get('user_id') ?? 'N/A',
            'module'      => 'Reports',
            'action'      => 'Download',
            'details'     => 'Exported System Activity Logs to PDF. Filters: [' . $filterDisplay . ']'
        ]);
        // -----------------------------------------------------

        $dompdf->stream("System_Logs_" . date('Ymd_His') . ".pdf", array("Attachment" => 1));
        
        exit(); 
    }
}