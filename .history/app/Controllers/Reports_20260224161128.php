<?php

namespace App\Controllers;

class Reports extends BaseController
{
    public function index()
    {
        $data = [
            'reports' => [] // Empty array for now
        ];

        return view('admin/reports', $data);
    }

    public function store()
    {
        // Save logic goes here later
        return redirect()->to('/reports')->with('success', 'New request submitted successfully!');
    }

    // -------------------------------------------------------------
    // PDF GENERATION METHOD
    // -------------------------------------------------------------
    public function exportPdf()
    {
        // NOTE: To make this actually generate a PDF file, 
        // you will need to install a library like Dompdf via Composer.
        
        // For right now, clicking the button will just show this message:
        echo "<h1>PDF Export Feature</h1>";
        echo "<p>To complete this feature, open your terminal in VS Code and run: <b>composer require dompdf/dompdf</b></p>";
        echo "<p>Once installed, we will update this function to convert your HTML table into a downloadable PDF!</p>";
        echo "<br><a href='/reports'>Go Back</a>";
    }
}