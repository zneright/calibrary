public function verifyResetCodeAjax()
    {
        // Ensure this is only accessed via AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Direct access not allowed');
        }

        $userModel = new \App\Models\UserModel();
        $id = session()->get('id') ?? session()->get('temp_reset_user_id');
        
        if (!$id) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Session expired. Please request a new code.',
                'csrf_hash' => csrf_hash() // Always return a fresh CSRF token
            ]);
        }

        $user = $userModel->find($id);
        $inputCode = $this->request->getPost('reset_code');

        // Check if code matches OR if the time has expired
        if ($user['reset_token'] !== $inputCode || strtotime(date('Y-m-d H:i:s')) > strtotime($user['reset_expires'])) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Invalid or expired verification code.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        // Code is correct! Tell the UI to proceed to Step 2.
        return $this->response->setJSON([
            'success' => true,
            'csrf_hash' => csrf_hash()
        ]);
    }