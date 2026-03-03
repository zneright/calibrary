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
        
        // Fetch 10 default/random items for the main feed before searching
        // Using DESC here to show the newest items, but you can change 'id', 'DESC' to 'RAND()' if you want purely random books
        $rawResults = $collectionModel->orderBy('id', 'DESC')->findAll(10);
        
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
                        'cover_photo' => $r['cover_photo'] ?? null,
                        // Extra details for the Modal
                        'publisher'   => $r['publisher'] ?? 'N/A',
                        'issued_date' => $r['issued_date'] ?? 'N/A',
                        'type_label'  => $r['type'] ?? 'Collection',
                        'location'    => $r['location'] ?? 'N/A'
                    ];
                }
            } elseif ($type === 'journals') {
                // FIXED: Removed the strict 'AVAILABLE' where clause that was hiding journals
                $rawResults = $journalModel->groupStart()
                                           ->like('subject', $query)
                                           ->orLike('author', $query)
                                           ->groupEnd()
                                           ->findAll();
                
                foreach($rawResults as $r) {
                    $results[] = [
                        'title'       => $r['subject'], 
                        'author'      => $r['author'] ?: 'Unknown Author',
                        'status'      => $r['status'] ?? 'Available',
                        'identifier'  => 'Vol: ' . $r['volume'] . ' | Page: ' . $r['page'],
                        'icon'        => 'bi-journal-text',
                        'cover_photo' => $r['cover_photo'] ?? null,
                        // Extra details for the Modal
                        'publisher'   => $r['source'] ?? 'N/A',
                        'issued_date' => $r['date'] ?? 'N/A',
                        'type_label'  => 'Journal',
                        'location'    => 'N/A'
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