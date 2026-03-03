<?php
namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;

class Home extends BaseController
{
    // --- HELPER FUNCTIONS to ensure Collections and Journals format identically ---
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

    private function formatJournalRow($r) 
    {
        return [
            'title'       => $r['subject'] ?? 'No Title', 
            'author'      => $r['author'] ?: 'Unknown Author',
            'status'      => $r['status'] ?? 'Available',
            'identifier'  => 'Vol: ' . ($r['volume'] ?? '-') . ' | Page: ' . ($r['page'] ?? '-'),
            'icon'        => 'bi-journal-text',
            'cover_photo' => $r['cover_photo'] ?? null,
            'publisher'   => $r['source'] ?? 'N/A',
            'issued_date' => $r['date'] ?? 'N/A',
            'type_label'  => 'Journal',
            'location'    => 'N/A'
        ];
    }

    // Helper to fetch the sidebar items (Mix of 3 Books and 2 Journals)
    private function getNewAcquisitions()
    {
        $collectionModel = new CollectionModel();
        $journalModel    = new JournalModel();

        $newCol = $collectionModel->orderBy('id', 'DESC')->findAll(3);
        $newJour = $journalModel->orderBy('id', 'DESC')->findAll(2);

        $latest = [];
        foreach($newCol as $r) { $latest[] = $this->formatCollectionRow($r); }
        foreach($newJour as $r) { $latest[] = $this->formatJournalRow($r); }

        return $latest;
    }

    // --- MAIN METHODS ---
    
    public function index()
    {
        $collectionModel = new CollectionModel();
        $journalModel    = new JournalModel();
        
        // 1. Fetch mixed new acquisitions for the sidebar
        $data['latest_books'] = $this->getNewAcquisitions();
        
        // 2. Fetch RANDOM items for the main feed (Mix of 8 Collections and 4 Journals)
        $randCol  = $collectionModel->orderBy('RAND()')->findAll(8);
        $randJour = $journalModel->orderBy('RAND()')->findAll(4);
        
        $results = [];
        foreach($randCol as $r) { $results[] = $this->formatCollectionRow($r); }
        foreach($randJour as $r) { $results[] = $this->formatJournalRow($r); }
        
        // Shuffle the array so the journals and books are mixed randomly together
        shuffle($results);

        $data['results']       = $results;
        $data['is_default']    = true;
        $data['selected_type'] = 'collections'; 

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
                    $results[] = $this->formatJournalRow($r);
                }
            }
        }

        return view('landing_page', [
            'results'       => $results, 
            'query'         => $query,
            'selected_type' => $type,
            // Re-use the helper to keep sidebar populated during a search
            'latest_books'  => $this->getNewAcquisitions() 
        ]);
    }
}