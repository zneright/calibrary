<?php
namespace App\Controllers;
use App\Models\UserModel;

class Admin extends BaseController 
{
    // ... your index() and store() methods ...

    public function update() 
    {
        $userModel = new UserModel();
        
        // Get the hidden ID from the form
        $id = $this->request->getPost('id');

        // Get the rest of the form data
        $data = [
            'fullname'   => $this->request->getPost('fullname'),
            'user_id'    => $this->request->getPost('user_id'),
            'role'       => $this->request->getPost('role'),
            'department' => $this->request->getPost('department'),
        ];

        // Update the database
        if ($userModel->update($id, $data)) {
            return redirect()->back()->with('success', 'User updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }
}