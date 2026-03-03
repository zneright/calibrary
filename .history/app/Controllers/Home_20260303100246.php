<?php
namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;

class Home extends BaseController
{
    public function index()
    {
        $collectionModel = new CollectionModel();
        
        // Fetch the 3 newest items for the sidebar
        $data['latest_books'] = $collectionModel->orderBy('id', 'DESC')->findAll(3);
        
        return view('landing_page', $data);
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        $type  = $this->request->getGet('type') ?? 'collections'; 
        
        $collectionModel = new CollectionModel();
        $journalModel    = new JournalModel();
        
        $results = [];

        if (!empty($query)) {
            if ($type === 'collections' || $type === 'pd' || $type === 'eo') {
                $builder = $collectionModel->groupStart()
                                           ->like('title', $query)
                                           ->orLike('author', $query)
                                           ->orLike('accession_no', $query)
                                           ->groupEnd();
                
                if ($type === 'pd') $builder->where('type', 'Presidential Decree');
                if ($type === 'eo') $builder->where('type', 'Executive Order');

                $rawResults = $builder->findAll();

                foreach($rawResults as $r) {
                    $results[] = [
                        'title'       => $r['title'],
                        'author'      => $r['author'] ?: 'Unknown Author',
                        'status'      => $r['status'] ?? 'Available',
                        'identifier'  => 'Acc No: ' . $r['accession_no'],
                        'icon'        => 'bi-book',
                        'cover_photo' => $r['cover_photo'] ?? null // Added cover_photo
                    ];
                }
            } elseif ($type === 'journals') {
                $rawResults = $journalModel->groupStart()
                                           ->like('subject', $query)
                                           ->orLike('author', $query)
                                           ->groupEnd()->findAll();
                
                foreach($rawResults as $r) {
                    $results[] = [
                        'title'       => $r['subject'], 
                        'author'      => $r['author'] ?: 'Unknown Author',
                        'status'      => $r['status'] ?? 'AVAILABLE',
                        'identifier'  => 'Vol: ' . $r['volume'] . ' | Page: ' . $r['page'],
                        'icon'        => 'bi-journal-text',
                        'cover_photo' => $r['cover_photo'] ?? null // Added cover_photo
                    ];
                }
            }
        }

        $latest_books = $collectionModel->orderBy('id', 'DESC')->findAll(3);

        return view('landing_page', [
            'results'       => $results, 
            'query'         => $query,
            'selected_type' => $type,
            'latest_books'  => $latest_books
        ]);
    }
}