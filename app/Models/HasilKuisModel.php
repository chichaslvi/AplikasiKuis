<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilKuisModel extends Model
{
    protected $table      = 'kuis_hasil';
    protected $primaryKey = 'id_hasil';
    protected $allowedFields = [
        'id_user',
        'id_kuis',
        'jumlah_soal',
        'jawaban_benar',
        'jawaban_salah',
        'total_skor',
        'tanggal_pengerjaan',
        'jumlah_pengerjaan'
    ];

    public function getRiwayatByUser($userId)
    {
        return $this->select('kuis_hasil.*, kuis.nama_kuis')
            ->join('kuis', 'kuis.id_kuis = kuis_hasil.id_kuis')
            ->where('kuis_hasil.id_user', $userId)
            ->orderBy('kuis_hasil.tanggal_pengerjaan', 'DESC')
            ->findAll();
    }
}