<?php
namespace App\Controllers;

class Reports extends BaseController
{
    public function index()
    {
        // Mock data to populate the table dynamically
        $data = [
            'reports' => [
                [
                    'id'             => 1,
                    'requester_name' => 'Juan Dela Cruz',
                    'requester_id'   => '2024-0192',
                    'type'           => 'book_borrow',
                    'details'        => 'Data Structures & Algorithms',
                    'status'         => 'pending',
                    'created_at'     => '2026-02-18 14:00:00'
                ],
                [
                    'id'             => 2,
                    'requester_name' => 'Maria Santos',
                    'requester_id'   => '2025-0844',
                    'type'           => 'journal_access',
                    'details'        => 'Presidential Decree No. 1081',
                    'status'         => 'approved',
                    'created_at'     => '2026-02-17 09:30:00'
                ]
            ]
        ];

        return view('admin/reports', $data);
    }

    public function store()
    {
        return redirect()->to('/reports')->with('success', 'New request submitted successfully!');
    }

    // --- NEW APPROVAL & REJECTION METHODS --- //

    public function approve()
    {
        $id = $this->request->getPost('id');
        // TODO: MySQL logic to update status to 'approved'
        return redirect()->to('/reports')->with('success', 'Request has been approved.');
    }

    public function reject()
    {
        $id = $this->request->getPost('id');
        // TODO: MySQL logic to update status to 'rejected'
        return redirect()->to('/reports')->with('success', 'Request has been rejected.');
    }

    // --- PDF EXPORT (Unchanged) --- //
    public function exportPdf() { /* ... */ }
}