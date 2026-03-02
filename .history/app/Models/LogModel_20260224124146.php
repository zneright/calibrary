<?php
namespace App\Models;
use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table = 'logs'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    protected $allowedFields = [
        'user_name', 
        'user_id_num', 
        'module', 
        'action', 
        'details', 
        'created_at'
    ];
}