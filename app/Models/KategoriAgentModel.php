<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriAgentModel extends Model
{
    protected $table = 'kategori_agent';
    protected $primaryKey = 'id_kategori';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nama_kategori',   // sesuaikan dengan field di tabel
        'is_active'        // ✅ pastikan ini ada
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
