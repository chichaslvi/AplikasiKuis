<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table      = 'kuis';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama_kuis', 
        'topik', 
        'tanggal', 
        'waktu_mulai', 
        'waktu_selesai',
        'nilai_minimum', 
        'batas_pengulangan', 
        'id_kategori',
        'status'
    ];
}
