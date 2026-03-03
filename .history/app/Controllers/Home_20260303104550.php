<?php
namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;

class Home extends BaseController
{
    // Helper function so both Main Feed and Sidebar have identical data for the Modal
    private function formatCollectionRow($r) 
    {
        return [
            'title'       => $r['title'] ?? 'No Title',
            'author'      => $r['author'] ?: 'Unknown Author',
            'status'      => $r['status'] ?? 'Available',
            'identifier'  => 'Acc No: ' . ($r['accession_no'] ?? 'N/A'),
            'icon'        => 'bi-book',
            'cover_photo' => $r['cover_photo'] ?? null,
            'publisher'   => $r['publisher'] ?? 'N/A',
            'issued_date' => $r['issued_date'] ?? 'N/A',
            'type_label'  => $r['type'] ?? 'Collection',
            'location'    => $r['location'] ?? 'N/A'
        ];
    }

    public function index()
    {
        $collectionModel = new CollectionModel();
        
        // Fetch 3 newest items for the sidebar & format them for the modal
        $rawLatest = $collectionModel->orderBy('id', 'DESC')->findAll(3);
        $latest_books = [];
        foreach($rawLatest as $r) {
            $latest_books[] = $this->formatCollectionRow($r);
        }
        
        // Fetch 10 RANDOM items for the main feed on first load
        $rawResults = $collectionModel->orderBy('RAND()')->findAll(10);
        $results = [];
        foreach($rawResults as $r) {
            $results[] = $this->formatCollectionRow($r);
        }

        return view('landing_page', [
            'results'       => $results,
            'latest_books'  => $latest_books,
            'is_default'    => true,
            'selected_type' => 'collections'
        ]);
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

                $rawResults = $builder->orderBy('id', 'DESC')->findAll(50);

                foreach($rawResults as $r) {
                    $results[] = $this->formatCollectionRow($r);
                }
            } elseif ($type === 'journals') {
                $rawResults = $journalModel->groupStart()
                                           ->like('subject', $query)
                                           ->orLike('author', $query)
                                           ->groupEnd()
                                           ->orderBy('id', 'DESC')
                                           ->findAll(50);
                
                foreach($rawResults as $r) {
                    $results[] = [
                        'title'       => $r['subject'], 
                        'author'      => $r['author'] ?: 'Unknown Author',
                        'status'      => $r['status'] ?? 'Available',
                        'identifier'  => 'Vol: ' . $r['volume'] . ' | Page: ' . $r['page'],
                        'icon'        => 'bi-journal-text',
                        'cover_photo' => $r['cover_photo'] ?? null,
                        'publisher'   => $r['source'] ?? 'N/A',
                        'issued_date' => $r['date'] ?? 'N/A',
                        'type_label'  => 'Journal',
                        'location'    => 'N/A'
                    ];
                }
            }
        }

        // Fetch 3 newest items for the sidebar & format them for the modal on the search page too
        $rawLatest = $collectionModel->orderBy('id', 'DESC')->findAll(3);
        $latest_books = [];
        foreach($rawLatest as $r) {
            $latest_books[] = $this->formatCollectionRow($r);
        }

        return view('landing_page', [
            'results'       => $results, 
            'query'         => $query,
            'selected_type' => $type,
            'latest_books'  => $latest_books
        ]);
    }
}