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
        
        $data = [
            'logs' => $logModel->orderBy('id', 'DESC')->findAll()
        ];

        return view('admin/logs', $data);
    }

    public function exportPdf()
    {
        $logModel = new LogModel();
        
        // Grab optional filters from the GET request (e.g. ?module=Transactions)
        $moduleFilter = $this->request->getGet('module');
        $actionFilter = $this->request->getGet('action');
        
        if (!empty($moduleFilter)) {
            $logModel->where('module', $moduleFilter);
        }
        if (!empty($actionFilter)) {
            $logModel->where('action', $actionFilter);
        }

        $logs = $logModel->orderBy('id', 'DESC')->findAll();

        // Build the HTML for the PDF
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                h2 { text-align: center; color: #1a2942; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f4f4f4; color: #333; }
                .timestamp { font-size: 10px; color: #555; }
            </style>
        </head>
        <body>
            <h2>System Activity Logs</h2>
            <p>Generated on: ' . date('F d, Y h:i A') . '</p>
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
            $html .= '<tr><td colspan="5" style="text-align:center;">No logs found matching the criteria.</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        
        $dompdf->setPaper('A4');

        $dompdf->render();

        $dompdf->stream("System_Logs_" . date('Ymd_His') . ".pdf", array("Attachment" => 1));

        exit(); 
    }
}