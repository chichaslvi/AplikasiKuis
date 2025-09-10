<?php

namespace App\Models;
use CodeIgniter\Model;

class KategoriAgentModel extends Model
{
    protected $table = 'kategori_agent';   // nama tabel
    protected $primaryKey = 'id_kategori'; // primary key

    protected $allowedFields = [
        'nama_kategori',
        'deskripsi'
    ];

    // optional: aktifkan timestamps kalau tabelmu ada created_at / updated_at
    // protected $useTimestamps = true;
}
