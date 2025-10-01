<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Reviewer extends BaseController
{
    public function __construct()
    {
        // Hanya role reviewer yang boleh masuk
        if (session()->get('role') !== 'reviewer') {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function dashboard()
    {
        $notifikasi = [
            ['judul' => 'Ada kuis baru menunggu review', 'created_at' => '2025-09-24 14:30:00'],
            ['judul' => 'Deadline review Batch 2 besok', 'created_at' => '2025-09-23 09:15:00'],
            ['judul' => 'Sistem maintenance jam 22:00', 'created_at' => '2025-09-22 20:00:00'],
        ];

        return view('reviewer/dashboard', ['notifikasi' => $notifikasi]);
    }

    // 🔥 Tambahin ini
    public function kuis()
    {
        return view('reviewer/kuis/index'); 
    }
    public function reports()
{
    // Contoh data kuis (nanti ambil dari DB)
    $kuis = [
        [
            'id_kuis' => 1,
            'nama_kuis' => 'Kuis Dasar PHP',
            'topik' => 'PHP Dasar',
            'tanggal' => '2025-09-25',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '09:00'
        ],
        [
            'id_kuis' => 2,
            'nama_kuis' => 'Kuis JS Lanjutan',
            'topik' => 'JavaScript',
            'tanggal' => '2025-09-26',
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '11:00'
        ]
    ];

    return view('reviewer/reports/index', ['kuis' => $kuis]);
}

}
