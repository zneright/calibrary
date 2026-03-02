<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';

    // FIX: Remove 'reading' and ensure 'status' is here
    protected $allowedFields = [
        'target_audience', 
        'recipient', 
        'type', 
        'message', 
        'status', // Ensure this matches your DB exactly
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Set to empty if you don't have an updated_at column
}