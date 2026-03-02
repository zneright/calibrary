<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    // 1. Define the table name in your MySQL database
    protected $table = 'users'; 
    
    // 2. Define the primary key (usually 'id')
    protected $primaryKey = 'id';
    
    // 3. Enable auto-increment if your ID column uses it
    protected $useAutoIncrement = true;
    
    // 4. THIS IS CRUCIAL: Allow these fields to be inserted/updated
    protected $allowedFields = [
        'fullname', 
        'user_id', 
        'role', 
        'department', 
        'password'
    ];

    // Optional: If you want CodeIgniter to [ handle created_at and updated_at timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}