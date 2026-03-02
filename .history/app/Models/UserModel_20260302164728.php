<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
  protected $allowedFields = [
    'fullname', 'user_id', 'email', 'contact', 
    'role', 'department', 'password', 'avatar', 
    'is_verified', 'status', 
    'reset_token', 'reset_expires' // <--- ADD THESE TWO
];
}