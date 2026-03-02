<?php

namespace App\Controllers;

class Profile extends BaseController
{
    public function index()
    {
        return view('admin/profile');
    }

    public function updateInfo()
    {
        // Database logic to update the user's name/email goes here
        
        return redirect()->to('/admin/profile')->with('success', 'Your personal information has been updated!');
    }

    public function updatePassword()
    {
        // Database logic to verify old password and hash the new one goes here
        
        return redirect()->to('/admin/profile')->with('success', 'Your password has been successfully changed!');
    }

    // --------------------------------------------------------------------
    // Handle Avatar Uploads
    // --------------------------------------------------------------------
    public function uploadAvatar()
    {
        // 1. Get the file from the form post
        $file = $this->request->getFile('avatar');

        // 2. Basic Validation Rules
        // Check if a file was actually uploaded and if it's a valid image type
        if (! $file->isValid() || $file->hasMoved()) {
             return redirect()->back()->with('error', $file->getErrorString());
        }
        
        // Optional: Check file size (e.g., max 2MB) to save server space
        if ($file->getSizeByUnit('mb') > 2) {
             return redirect()->back()->with('error', 'Image is too large. Max size is 2MB.');
        }

        // 3. Generate a new secure name and move the file
        // We use getRandomName() to prevent filename conflicts
        $newName = $file->getRandomName();
        
        // Move to public/uploads/avatars folder
        $file->move(FCPATH . 'uploads/avatars', $newName);

        // --------------------------------------------------------------
        // IMPORTANT DATABASE NOTE:
        // At this point, the file is saved on the server. 
        // In the next phase, we will write code here to save the 
        // '$newName' variable into the 'users' database table so 
        // we remember which image belongs to this user.
        // --------------------------------------------------------------

        return redirect()->to('/admin/profile')->with('success', 'Profile picture updated successfully!');
    }
}