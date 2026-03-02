<?php
namespace App\Models;
use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    protected $allowedFields = [
        'user_id_num', 'user_name', 'collection_id', 'collection_title', 
        'status', 'date_requested', 'date_needed', 'due_date', 
        'date_returned', 'reason'
    ];
}