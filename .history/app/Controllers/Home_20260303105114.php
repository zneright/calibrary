<?php
namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;

class Home extends BaseController
{
    // Helper function to format Books and Journals exactly the same way for the View Modal
    private function formatItem($r, $type, $icon) 
    {
        return [
            'title'       => $r['title'] ?? $r['subject'] ?? 'No Title',
            'author'      => $r['author'] ?: 'Unknown Author',
            'status'      => $r['status'] ?? 'Available',
            'identifier'  => isset($r['accession_no']) ? 'Acc No: ' . $r['accession_no'] : 'Vol: ' . ($r['volume'] ?? '-') . ' | Page: ' . ($r['page'] ?? '-'),
            'icon'        => $icon,
            'cover_photo' => $r['cover_photo'] ?? null,
            'publisher'   => $r['publisher'] ?? $r['source'] ?? 'N/A',
            'issued_date' => $r['issued_date'] ?? $r['date'] ?? 'N/A',
            'type_label'  => $r['type'] ?? $type, // Use db type if available, else fallback
            'location'    => $r['location'] ?? 'N/A'
        ];
    }

    public function index()
    {
        $collectionModel = new CollectionModel();
        $journalModel    = new JournalModel();
        
        // 1. Fetch 3 newest Collections and 2 newest Journals for the Sidebar
        $latestCols  = $collectionModel->orderBy('id', 'DESC')->findAll(3);
        $latestJours = $journalModel->orderBy('id', 'DESC')->findAll(2);
        
        $latest_books = [];
        foreach($latestCols as $r)  { $latest_books[] = $this->formatItem($r, 'Collection', 'bi-book'); }
        foreach($latestJours as $r) { $latest_books[] = $this->formatItem($r, 'Journal', 'bi-journal-text'); }
        
        // 2. Fetch RANDOM items for the main feed (Mix of 8 Collections and 4 Journals)
        // Using RAND() for MySQL randomization
        $randCols  = $collectionModel->orderBy('RAND()')->findAll(8);
        $randJours = $journalModel->orderBy('RAND()')->findAll(4);
        
        $results = [];
        foreach($randCols as $r)  { $results[] = $this->formatItem($r, 'Collection', 'bi-book'); }
        foreach($randJours as $r) { $results[] = $this->formatItem($r, 'Journal', 'bi-journal-text'); }
        
        // Shuffle to perfectly mix the books and journals together on the page
        shuffle($results);

        $data['latest_books']  = $latest_books;
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
                    $results[] = $this->formatItem($r, 'Collection', 'bi-book');
                }
            } elseif ($type === 'journals') {
                $rawResults = $journalModel->groupStart()
                                           ->like('subject', $query)
                                           ->orLike('author', $query)
                                           ->groupEnd()
                                           ->orderBy('id', 'DESC')
                                           ->findAll(50);
                
                foreach($rawResults as $r) {
                    $results[] = $this->formatItem($r, 'Journal', 'bi-journal-text');
                }
            }
        }

        // Keep sidebar populated during a search
        $latestCols  = $collectionModel->orderBy('id', 'DESC')->findAll(3);
        $latestJours = $journalModel->orderBy('id', 'DESC')->findAll(2);
        $latest_books = [];
        foreach($latestCols as $r)  { $latest_books[] = $this->formatItem($r, 'Collection', 'bi-book'); }
        foreach($latestJours as $r) { $latest_books[] = $this->formatItem($r, 'Journal', 'bi-journal-text'); }

        return view('landing_page', [
            'results'       => $results, 
            'query'         => $query,
            'selected_type' => $type,
            'latest_books'  => $latest_books
        ]);
    }
}