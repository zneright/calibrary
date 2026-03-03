<?php
namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;

class Home extends BaseController
{
    private function formatItem($r, $type, $icon) 
    {
        return [
            'id'          => $r['id'], 
            'title'       => $r['title'] ?? $r['subject'] ?? 'No Title',
            'author'      => $r['author'] ?: 'Unknown Author',
            'status'      => $r['status'] ?? 'Available',
            'identifier'  => isset($r['accession_no']) ? 'Acc No: ' . $r['accession_no'] : 'Vol: ' . ($r['volume'] ?? '-') . ' | Page: ' . ($r['page'] ?? '-'),
            'icon'        => $icon,
            'cover_photo' => $r['cover_photo'] ?? null,
            'publisher'   => $r['publisher'] ?? $r['source'] ?? 'N/A',
            'issued_date' => $r['issued_date'] ?? $r['date'] ?? 'N/A',
            'type_label'  => $r['type'] ?? $type,
            'location'    => $r['location'] ?? 'N/A'
        ];
    }

    //newest items
    private function getNewAcquisitions()
    {
        $collectionModel = new CollectionModel();
        $journalModel    = new JournalModel();

        // Fetch the 5 absolute newest from BOTH tables
        $newCol  = $collectionModel->orderBy('id', 'DESC')->findAll(5);
        $newJour = $journalModel->orderBy('id', 'DESC')->findAll(5);

        $latest = [];
        foreach($newCol as $r)  { $latest[] = $this->formatItem($r, 'Collection', 'bi-book'); }
        foreach($newJour as $r) { $latest[] = $this->formatItem($r, 'Journal', 'bi-journal-text'); }

        // Sort the combined array so the absolute highest IDs (newest) are at the very top
        usort($latest, function($a, $b) {
            return $b['id'] <=> $a['id']; 
        });

        // Return only the top 5 absolute newest items
        return array_slice($latest, 0, 5);
    }

    public function index()
    {
        $collectionModel = new CollectionModel();
        $journalModel    = new JournalModel();
        
        // 1. Fetch perfectly ordered new acquisitions for the sidebar
        $data['latest_books'] = $this->getNewAcquisitions();
        
        // 2. Fetch a pool of recent items to Randomize (Foolproof PHP randomization)
        $poolCols  = $collectionModel->orderBy('id', 'DESC')->findAll(30);
        $poolJours = $journalModel->orderBy('id', 'DESC')->findAll(20);
        
        $results = [];
        foreach($poolCols as $r)  { $results[] = $this->formatItem($r, 'Collection', 'bi-book'); }
        foreach($poolJours as $r) { $results[] = $this->formatItem($r, 'Journal', 'bi-journal-text'); }
        
        // Shuffle the array perfectly every time the page loads
        shuffle($results);

        // Pick the top 12 from the shuffled deck
        $data['results']       = array_slice($results, 0, 12); 
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

        return view('landing_page', [
            'results'       => $results, 
            'query'         => $query,
            'selected_type' => $type,
            // Keep the sidebar populated dynamically even during a search
            'latest_books'  => $this->getNewAcquisitions() 
        ]);
    }
}