<?php
namespace App\Models;
use CodeIgniter\Model;

class CollectionModel extends Model
{
    protected $table = 'collections'; 
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    // Every field from your HTML form must be listed here!
    protected $allowedFields = [
        'type', 'reading', 'class', 'status', 'title', 'subject', 'author', 
        'publisher', 'place', 'issued_date', 'accession_no', 'volume', 
        'isbn', 'series', 'location', 'date_acquired', 'date_received', 
        'remarks', 'cover_photo', 'soft_copy', 'url'
    ];
}