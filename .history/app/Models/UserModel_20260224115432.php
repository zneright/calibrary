<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    // These MUST match the columns in your MySQL database exactly
    protected $allowedFields = [
        'fullname', 
        'user_id', 
        'email',
        'department',
        'role', 
        'password',
        'verification_token',
        'is_verified'
    ];
}