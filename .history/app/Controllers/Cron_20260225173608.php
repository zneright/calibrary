<?php
namespace App\Controllers;

class Cron extends BaseController {
    
    public function sendDueAlerts() {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // 1. Find items due today or tomorrow
        $items = $db->table('transactions')
            ->select('transactions.*, users.email')
            ->join('users', 'users.user_id = transactions.user_id_num')
            ->whereIn('transactions.status', ['Borrowed'])
            ->groupStart()
                ->where('due_date', $today)
                ->orWhere('due_date', $tomorrow)
            ->groupEnd()
            ->get()->getResultArray();

        foreach ($items as $item) {
            $dueLabel = ($item['due_date'] == $today) ? "TODAY" : "TOMORROW";
            $subject = "URGENT: Library Book Due $dueLabel";
            $message = "Hello {$item['user_name']},<br><br>The resource <b>'{$item['collection_title']}'</b> is due for return $dueLabel (" . date('M d', strtotime($item['due_date'])) . "). Please return it to avoid penalties.";
            
            $this->sendEmail($item['email'], $subject, $message);
        }
        
        echo "Alerts sent for " . count($items) . " items.";
    }

    private function sendEmail($to, $subj, $msg) {
        // Use your existing PHPMailer logic here...
    }
}