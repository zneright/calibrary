<?php
namespace App\Controllers;

use App\Models\CollectionModel;

class Home extends BaseController
{
    public function index()
    {
        // Fetch 3 most recent items for the sidebar "New Acquisition"
        $db = \Config\Database::connect();
        $data['newAcquisitions'] = $db->table('collections')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();

        return view('landing_page', $data);
    }

    public function search()
    {
        $db = \Config\Database::connect();
        $query = $this->request->getGet('q');
        $type = $this->request->getGet('type');
        $builder->select('id, title, author, status, type, cover_photo'); // Ensure cover_photo is selected 
        $builder = $db->table('collections');
        
        if ($query) {
            $builder->groupStart()
                ->like('title', $query)
                ->orLike('author', $query)
                ->orLike('subject', $query)
            ->groupEnd();
        }

        // Apply radio button filter
        if ($type === 'Journal') {
            $builder->where('type', 'Journal');
        } elseif ($type === 'Collections') {
            $builder->where('type !=', 'Journal');
        }

        $data['results'] = $builder->get()->getResultArray();
        $data['query'] = $query;
        
        // Keep sidebar data consistent
        $data['newAcquisitions'] = $db->table('collections')->orderBy('id', 'DESC')->limit(3)->get()->getResultArray();

        return view('landing_page', $data);
    }
}