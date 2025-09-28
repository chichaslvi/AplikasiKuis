<?php
namespace App\Models;

use CodeIgniter\Model;

class HasilKuisModel extends Model
{
    protected $table         = 'kuis_hasil';
    protected $primaryKey    = 'id_hasil';
    protected $returnType    = 'array';
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
    protected $useTimestamps = false;
}
