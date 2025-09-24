<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Reviewer extends BaseController
{
    public function __construct()
    {
        // Pastikan hanya reviewer yang bisa akses controller ini
        if (session()->get('role') !== 'reviewer') {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function dashboard()
    {
        // tampilkan view dashboard reviewer
        return view('reviewer/dashboard');
    }

    public function laporan()
    {
        // tampilkan view laporan reviewer
        return view('reviewer/laporan');
    }

    public function detailLaporan($id)
    {
        // contoh untuk menampilkan detail laporan
        return view('reviewer/detail_laporan', ['id' => $id]);
    }
}
