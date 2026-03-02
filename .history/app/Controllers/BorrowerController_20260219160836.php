<?php

namespace App\Controllers;

class BorrowerController extends BaseController
{
    public function index()
    {
        return view('borrower/dashboard');
    }

        public function catalog()
    {
        return view('borrower/catalog');
    }

    public function myBooks()
    {
        return view('borrower/my_books');
    }

    public function profile()
    {
        return view('borrower/profile');
    }

    public function updateProfile()
    {
        // Database logic goes here 
        return redirect()->to('/borrower/profile')->with('success', 'Your personal information has been updated.');
    }

    public function updatePassword()
    {
        // Database logic goes here 
        return redirect()->to('/borrower/profile')->with('success', 'Your password has been changed successfully.');
    }

    public function uploadAvatar()
    {
        $file = $this->request->getFile('avatar');

        if (! $file->isValid() || $file->hasMoved()) {
             return redirect()->back()->with('error', 'Please select a valid image file.');
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/avatars', $newName);

        // Database logic goes here 
        return redirect()->to('/borrower/profile')->with('success', 'Profile picture updated successfully!');
    }

}