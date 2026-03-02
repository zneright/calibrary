<?php
namespace App\Models;
use CodeIgniter\Model;

class NotificationModel extends Model {
    protected $table = 'notifications';
    protected $allowedFields = ['target_audience', 'recipient', 'type', 'message', 'status'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
}