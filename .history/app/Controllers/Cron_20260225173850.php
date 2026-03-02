<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Cron extends BaseController
{
    /**
     * This function will be called by the server daily
     * Command: php index.php cron sendDueAlerts
     */
    public function sendDueAlerts()
    {
        // Only allow CLI (Command Line) access for security
        if (!is_cli()) {
            return "This can only be run via command line.";
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // Find items due today or tomorrow that are still "Borrowed"
        $items = $db->table('transactions')
            ->select('transactions.*, users.email, users.fullname')
            ->join('users', 'users.user_id = transactions.user_id_num')
            ->where('transactions.status', 'Borrowed')
            ->groupStart()
                ->where('due_date', $today)
                ->orWhere('due_date', $tomorrow)
            ->groupEnd()
            ->get()->getResultArray();

        if (empty($items)) {
            echo "No alerts to send for today.";
            return;
        }

        foreach ($items as $item) {
            $dueLabel = ($item['due_date'] == $today) ? "TODAY" : "TOMORROW";
            $subject = "REMINDER: Library Book Due $dueLabel";
            
            $message = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px;'>
                    <h3 style='color: #dc3545;'>Return Reminder</h3>
                    <p>Hello <b>{$item['fullname']}</b>,</p>
                    <p>This is a friendly reminder that the resource: <b>\"{$item['collection_title']}\"</b> 
                       is scheduled to be returned <b>$dueLabel</b> (" . date('M d, Y', strtotime($item['due_date'])) . ").</p>
                    <p>Please return it to the Data Bank and Library Services to avoid penalties.</p>
                    <br>
                    <p style='color: #777; font-size: 0.8em;'>This is an automated system alert.</p>
                </div>";

            $this->sendEmail($item['email'], $item['fullname'], $subject, $message);
        }

        echo "Success: Sent " . count($items) . " alert(s).";
    }

    private function sendEmail($to, $name, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            // Use your specific SMTP settings from Auth.php
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zneright2@gmail.com'; 
            $mail->Password   = 'ivcfiqpztkwapymf'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('zneright2@gmail.com', 'CALIS Library');
            $mail->addAddress($to, $name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            log_message('error', "Cron Email failed: {$mail->ErrorInfo}");
            return false;
        }
    }
}