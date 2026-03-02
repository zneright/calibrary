<?php
namespace App\Models;
use CodeIgniter\Model;

class JournalModel extends Model
{
    protected $table = 'journals'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    protected $allowedFields = [
        'subject', 'author', 'date', 'source', 'session', 'volume', 'page'
    ];
}