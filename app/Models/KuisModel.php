<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table      = 'kuis';
    protected $primaryKey = 'id_kuis';
    protected $allowedFields = [
        'nama_kuis', 
        'topik', 
        'tanggal', 
        'waktu_mulai', 
        'waktu_selesai',
        'nilai_minimum', 
        'batas_pengulangan', 
        'status'
    ];

    /**
     * Ambil semua kuis beserta kategori agent terkait
     */
    public function getAllKuisWithKategori()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori
            FROM kuis k
            LEFT JOIN kuis_kategori kk ON k.id_kuis = kk.id_kuis
            LEFT JOIN kategori_agent ka ON kk.id_kategori = ka.id_kategori
            GROUP BY k.id_kuis
            ORDER BY k.id_kuis DESC
        ");
        return $query->getResultArray();
    }

    /**
     * Ambil satu kuis beserta kategori agent terkait
     */
    public function getKuisByIdWithKategori($id)
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori
            FROM kuis k
            LEFT JOIN kuis_kategori kk ON k.id_kuis = kk.id_kuis
            LEFT JOIN kategori_agent ka ON kk.id_kategori = ka.id_kategori
            WHERE k.id_kuis = ?
            GROUP BY k.id_kuis
        ", [$id]);

        return $query->getRowArray();
    }
}

