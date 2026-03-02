<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    protected $allowedFields = [
        'fullname', 
        'user_id', 
        'email',
        'contact',
        'department',
        'role', 
        'password',
        'avatar',
        'verification_token',
        'is_verified'
    ];
}