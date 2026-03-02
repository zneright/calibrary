<?php
namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('landing_page');
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        
        // Mock data for your library system
        $results = [];
        if ($query) {
            $results = [
                [
                    'title' => 'The 1987 Constitution of the Philippines',
                    'author' => 'De Leon, Hector S.',
                    'status' => 'Available',
                    'cover' => 'constitution.jpg'
                ],
                [
                    'title' => 'Administrative Law: Text and Cases',
                    'author' => 'Agpalo, Ruben E.',
                    'status' => 'Borrowed',
                    'cover' => 'admin_law.jpg'
                ]
            ];
        }

        return view('landing_page', ['results' => $results, 'query' => $query]);
    }
}