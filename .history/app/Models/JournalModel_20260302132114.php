<?php
namespace App\Models;
use CodeIgniter\Model;

class JournalModel extends Model
{
    protected $table = 'journals'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    // IF 'status' IS NOT HERE, CODEIGNITER WILL NEVER SAVE IT TO THE DB!
    protected $allowedFields = [
        'subject', 'author', 'date', 'source', 'session', 'volume', 'page', 'status' 
    ];
}