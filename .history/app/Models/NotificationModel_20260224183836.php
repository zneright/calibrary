<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';

    // MUST MATCH YOUR DB EXACTLY
    protected $allowedFields = [
        'target_audience', 
        'recipient', 
        'type', 
        'message', 
        'status', 
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; 
}