<?php
namespace App\Models;

use CodeIgniter\Model;

class SoalModel extends Model
{
    protected $table      = 'soal_kuis';   // ✅ sesuaikan dengan nama tabel di database
    protected $primaryKey = 'id_soal';

    protected $allowedFields = [
        'id_kuis',
        'soal',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'jawaban'
    ];

    // Kalau mau otomatis pakai created_at & updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
