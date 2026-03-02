<?php

namespace App\Controllers;

class Collections extends BaseController
{
    public function create()
    {
        $data['title'] = 'Add New Collection';
        return view('collections_create', $data);
    }

    
    public function index()
    {
        $data = [
            'collections' => [] 
        ];

return view('admin/collections', $data);} }