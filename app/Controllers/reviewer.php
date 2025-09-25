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
    // contoh data notifikasi (nanti bisa ambil dari DB kalau sudah ada tabel notifikasi)
    $notifikasi = [
        [
            'judul' => 'Ada kuis baru menunggu review',
            'created_at' => '2025-09-24 14:30:00'
        ],
        [
            'judul' => 'Deadline review Batch 2 besok',
            'created_at' => '2025-09-23 09:15:00'
        ],
        [
            'judul' => 'Sistem maintenance jam 22:00',
            'created_at' => '2025-09-22 20:00:00'
        ],
    ];

    return view('reviewer/dashboard', [
        'notifikasi' => $notifikasi
    ]);
}
}