<?php

namespace App\Models;
use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table = 'kuis';
    protected $primaryKey = 'id_kuis';

    protected $allowedFields = [
        'id_kategori',
        'nama_kuis',
        'topik',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'nilai_minimum',
        'batas_pengulangan'
    ];

    // optional kalau mau timestamps
    // protected $useTimestamps = true;
}
