<?php 

namespace App\Controllers;

use App\Models\UserModel;
use DateTime;


class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // =========================
    // TAMPILAN FORM LOGIN
    // =========================
    public function login()
    {
        return view('auth/login');
    }

    // =========================
    // PROSES LOGIN
    // =========================
    public function doLogin()
    {
        $nik      = $this->request->getPost('nik');
        $password = $this->request->getPost('password');

        // ✅ hanya user aktif yang bisa login
        $user = $this->userModel
            ->where('nik', $nik)
            ->where('is_active', 1)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'NIK tidak ditemukan atau akun tidak aktif');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password salah');
        }

        // 1. Login pertama kali -> wajib ganti password
        if ($user['must_change_password']) {
            session()->set('temp_user_id', $user['id']);
            return redirect()->to('/auth/changePassword')->with('info', 'Silakan ubah password Anda.');
        }

        // 2. Cek masa berlaku password (180 hari ≈ 6 bulan)
        if ($user['last_password_change']) {
            $lastChange = new DateTime($user['last_password_change']);
            $now = new DateTime();
            $days = $lastChange->diff($now)->days;

            if ($days >= 180) {
                session()->set('temp_user_id', $user['id']);
                return redirect()->to('/auth/changePassword')->with('info', 'Password sudah lebih dari 6 bulan, ubah password dulu.');
            }
        }

        // 3. Jika valid -> login normal
session()->regenerate();

session()->set([
    'user_id'    => $user['id'],
    'username'   => $user['username'] ?? null,
    'nama'       => $user['nama'],
    'nik'        => $user['nik'],
    'role'       => $user['role'],
    'isLoggedIn' => true
]);


        // Redirect sesuai role
        switch (strtolower($user['role'])) {
            case 'admin':
                return redirect()->to('/admin/dashboard');
            case 'reviewer':
                return redirect()->to('/reviewer/dashboard');
            case 'agent':
                return redirect()->to('/agent/dashboard');
            default:
                return redirect()->to('/')->with('error', 'Role tidak dikenal');
        }
    }

    // =========================
    // FORM GANTI PASSWORD
    // =========================
    public function changePassword()
    {
        return view('auth/change_password');
    }

    // =========================
    // PROSES UPDATE PASSWORD
    // =========================
    public function updatePassword()
    {
        $newPass     = $this->request->getPost('new_password');
        $confirmPass = $this->request->getPost('confirm_password');
        $userId      = session()->get('temp_user_id');

        if ($newPass !== $confirmPass) {
            return redirect()->back()->with('error', 'Password tidak sama!');
        }

        $this->userModel->update($userId, [
            'password'             => password_hash($newPass, PASSWORD_DEFAULT),
            'must_change_password' => false,
            'last_password_change' => date('Y-m-d H:i:s')
        ]);

        session()->remove('temp_user_id');

        return redirect()->to('/')->with('success', 'Password berhasil diubah, silakan login kembali.');
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Anda sudah logout.');
    }
}
