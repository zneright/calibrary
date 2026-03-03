<?php
namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        // Sidebar: Get 3 most recent books
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

        // 1. Build Query for Collections (Books)
        $colQuery = $db->table('collections')
            ->select('id, title, author, status, type, cover_photo');

        if ($query) {
            $colQuery->groupStart()
                ->like('title', $query)->orLike('author', $query)->orLike('subject', $query)
            ->groupEnd();
        }

        // 2. Build Query for Journals
        $jrQuery = $db->table('journals')
            ->select('id, subject as title, author, status, "Journal" as type, NULL as cover_photo');

        if ($query) {
            $jrQuery->groupStart()
                ->like('subject', $query)->orLike('author', $query)
            ->groupEnd();
        }

        // Apply filters based on radio buttons
        if ($type === 'Journal') {
            $results = $jrQuery->get()->getResultArray();
        } elseif ($type === 'Collections') {
            $results = $colQuery->get()->getResultArray();
        } else {
            // Combine both if "All" is selected
            $unionSql = $colQuery->union($jrQuery)->getCompiledSelect();
            $results = $db->query($unionSql)->getResultArray();
        }

        $data['results'] = $results;
        $data['query'] = $query;
        $data['newAcquisitions'] = $db->table('collections')->orderBy('id', 'DESC')->limit(3)->get()->getResultArray();

        return view('landing_page', $data);
    }
}