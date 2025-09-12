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
        'kategori_agent',
        'batas_pengulangan'
    ];

    // Ambil data kuis beserta kategori
    public function getKuisWithKategori()
    {
        return $this->select('kuis.*, kategori_agent.nama_kategori, kategori_agent.deskripsi')
                    ->join('kategori_agent', 'kategori_agent.id_kategori = kuis.id_kategori')
                    ->findAll();
    }
}
