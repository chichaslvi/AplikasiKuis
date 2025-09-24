<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Agent extends BaseController
{
    public function __construct()
    {
        // Pastikan hanya agent yang bisa akses controller ini
        if (session()->get('role') !== 'agent') {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function dashboard()
    {
        // tampilkan view dashboard agent
        return view('agent/dashboard');
    }

    public function soal()
    {
        // tampilkan view soal agent
        return view('agent/soal');
    }

    public function ulangiQuiz()
    {
        // kalau ada data quiz di session, hapus dulu biar fresh
        session()->remove('jawaban');
        session()->remove('current_question');
        session()->remove('score');

        // redirect balik ke halaman soal
        return redirect()->to(base_url('soal'));
    }

    public function riwayat()
    {
        // tampilkan view riwayat kuis agent
        return view('agent/riwayat');
    }
}
