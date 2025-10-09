<?php

namespace App\Controllers\Reviewer;

use App\Models\UserModel;
use App\Controllers\BaseController;

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
        return view('reviewer/ganti_password');
    }

    // 🟩 Proses update password
    public function update()
    {

        $userId = session()->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validasi input tidak kosong
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return redirect()->back()->with('error', 'Semua field harus diisi');
        }

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

        // Validasi panjang password baru (minimal 8 karakter)
        if (strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'Password baru minimal 6 karakter');
        }

        // Update password
        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'last_password_change' => date('Y-m-d H:i:s'),
        ]);

        // Jika ingin logout setelah ganti password, gunakan ini:
        // session()->destroy();
        // return redirect()->to('/login')->with('success', 'Password berhasil diubah! Silakan login kembali.');

        // Jika tidak ingin logout, gunakan ini:
        return redirect()->to('/reviewer/dashboard')->with('success', 'Password berhasil diubah!');
    }
}