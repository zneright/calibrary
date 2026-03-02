<?php

namespace App\Models;
use CodeIgniter\Model;

class CollectionModel extends Model
{
    protected $table            = 'collections'; // Your database table name
    protected $primaryKey       = 'id';
    
    // These are the columns we are allowing the form to save
    protected $allowedFields    = ['title', 'subject', 'call_no', 'author', 'status'];
    
    // Automatically add created_at and updated_at timestamps if your table has them
    protected $useTimestamps    = false; 
}