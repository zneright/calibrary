<?php
namespace App\Controllers;

use App\Models\CollectionModel;
use App\Models\JournalModel;

class Home extends BaseController
{
    public function index()
    {
        $collectionModel = new CollectionModel();
        
        // 5 newest items for the "New Acquisitions" 
        $data['latest_books'] = $collectionModel->orderBy('id', 'DESC')->findAll(5);
        
        // 12 RANDOM items 
        $rawResults = $collectionModel->orderBy('RAND()')->findAll(12);
        
        $results = [];
        foreach($rawResults as $r) {
            $results[] = [
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

        $data['results']    = $results;
        $data['is_default'] = true;
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

                // Limit search results to 50 to maintain fast loading times
                $rawResults = $builder->orderBy('id', 'DESC')->findAll(50);

                foreach($rawResults as $r) {
                    $results[] = [
                        'title'       => $r['title'],
                        'author'      => $r['author'] ?: 'Unknown Author',
                        'status'      => $r['status'] ?? 'Available',
                        'identifier'  => 'Acc No: ' . $r['accession_no'],
                        'icon'        => 'bi-book',
                        'cover_photo' => $r['cover_photo'] ?? null,
                        'publisher'   => $r['publisher'] ?? 'N/A',
                        'issued_date' => $r['issued_date'] ?? 'N/A',
                        'type_label'  => $r['type'] ?? 'Collection',
                        'location'    => $r['location'] ?? 'N/A'
                    ];
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

        $latest_books = $collectionModel->orderBy('id', 'DESC')->findAll(5);

        return view('landing_page', [
            'results'       => $results, 
            'query'         => $query,
            'selected_type' => $type,
            'latest_books'  => $latest_books
        ]);
    }
}