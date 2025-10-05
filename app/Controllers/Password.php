<?php

namespace App\Controllers;

use App\Models\UserModel;

class Password extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // 🟦 Tampilkan form ganti password
    public function index()
    {
        return view('agent/ganti_password');
    }

    // 🟩 Proses update password
    public function update()
    {
        $userId          = session()->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword     = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak sama');
        }

        $this->userModel->update($userId, [
            'password'             => password_hash($newPassword, PASSWORD_DEFAULT),
            'last_password_change' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/agent/dashboard')->with('success', 'Password berhasil diubah!');
    }
}
