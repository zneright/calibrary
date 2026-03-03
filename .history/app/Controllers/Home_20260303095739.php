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
        $type  = $this->request->getGet('type') ?? 'collections'; // Default to collections
        
        $collectionModel = new CollectionModel();
        $journalModel    = new JournalModel();
        
        $results = [];

        if (!empty($query)) {
            if ($type === 'collections' || $type === 'pd' || $type === 'eo') {
                // Query Collections Table
                $builder = $collectionModel->groupStart()
                                           ->like('title', $query)
                                           ->orLike('author', $query)
                                           ->orLike('accession_no', $query)
                                           ->groupEnd();
                
                // If they specifically selected PD or EO filters
                if ($type === 'pd') $builder->where('type', 'Presidential Decree');
                if ($type === 'eo') $builder->where('type', 'Executive Order');

                $rawResults = $builder->findAll();

                // Standardize format for the view
                foreach($rawResults as $r) {
                    $results[] = [
                        'title'      => $r['title'],
                        'author'     => $r['author'] ?: 'Unknown Author',
                        'status'     => $r['status'] ?? 'Available',
                        'identifier' => 'Acc No: ' . $r['accession_no'],
                        'icon'       => 'bi-book'
                    ];
                }
            } elseif ($type === 'journals') {
                // Query Journals Table
                $rawResults = $journalModel->groupStart()
                                           ->like('subject', $query)
                                           ->orLike('author', $query)
                                           ->groupEnd()->findAll();
                
                // Standardize format for the view
                foreach($rawResults as $r) {
                    $results[] = [
                        'title'      => $r['subject'], // Journals use 'subject' for title
                        'author'     => $r['author'] ?: 'Unknown Author',
                        'status'     => $r['status'] ?? 'AVAILABLE',
                        'identifier' => 'Vol: ' . $r['volume'] . ' | Page: ' . $r['page'],
                        'icon'       => 'bi-journal-text'
                    ];
                }
            }
        }

        // Fetch latest books again so the sidebar still works on the search page
        $latest_books = $collectionModel->orderBy('id', 'DESC')->findAll(3);

        return view('landing_page', [
            'results'       => $results, 
            'query'         => $query,
            'selected_type' => $type,
            'latest_books'  => $latest_books
        ]);
    }
}