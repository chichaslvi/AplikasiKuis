<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ReportController extends BaseController
{
    public function index()
    {
        // Dummy data kuis
      $data['kuis'] = [
    [
        'id' => 1,
        'nama_kuis'   => 'Kuis A',
        'sub_soal'    => 'Kuis Peningkatan Mutu',
        'tanggal'     => 'Kamis, 25 Januari 2024',
        'waktu_mulai' => '11:00',
        'waktu_selesai' => '12:00',
    ],
    [
        'id' => 2,
        'nama_kuis'   => 'Kuis B',
        'sub_soal'    => 'Kuis Peningkatan Mutu',
        'tanggal'     => 'Kamis, 25 Januari 2024',
        'waktu_mulai' => '11:00',
        'waktu_selesai' => '12:00',
    ],
    // lanjutkan untuk C, D, E, F, G
];

    

        return view('admin/report/index', $data);
    }
   public function detail($id)
{
    $detail = [
        'id' => $id,
        'nama_kuis' => 'Kuis ' . chr(64 + $id),
        'sub_soal' => 'Kuis Peningkatan Mutu',
        'tanggal' => 'Kamis, 25 Januari 2024',
        'waktu_mulai' => '11:00',
        'waktu_selesai' => '12:00',
        'peserta' => [
            ['nama' => 'Rina', 'username' => 'rina', 'nilai' => 87, 'pengulangan' => 2],
            ['nama' => 'Budi', 'username' => 'budi', 'nilai' => 90, 'pengulangan' => 1],
            ['nama' => 'Sari', 'username' => 'sari', 'nilai' => 75, 'pengulangan' => 3],
        ]
    ];

    return view('admin/report/detail', ['detail' => $detail]);
}
}