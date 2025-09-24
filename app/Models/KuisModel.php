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
     * Auto update status kuis berdasarkan tanggal & waktu
     */
    private function updateStatusList(&$list)
    {
        $now = date('Y-m-d H:i:s');
        foreach ($list as &$kuis) {
            $mulai   = $kuis['tanggal'] . ' ' . $kuis['waktu_mulai'];
            $selesai = $kuis['tanggal'] . ' ' . $kuis['waktu_selesai'];

            if ($now < $mulai) {
                $kuis['status'] = 'upcoming';
            } elseif ($now >= $mulai && $now <= $selesai) {
                $kuis['status'] = 'active';
            } else {
                $kuis['status'] = 'inactive';
            }

            // sinkronkan ke DB kalau berubah
            $this->update($kuis['id_kuis'], ['status' => $kuis['status']]);
        }
    }

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
        $result = $query->getResultArray();

        $this->updateStatusList($result);
        return $result;
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

        $row = $query->getRowArray();
        if ($row) {
            $list = [$row];
            $this->updateStatusList($list);
            $row = $list[0];
        }

        return $row;
    }
    public function getAvailableKuisForAgent()
{
    $db = \Config\Database::connect();
    $query = $db->query("
        SELECT k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori
        FROM kuis k
        LEFT JOIN kuis_kategori kk ON k.id_kuis = kk.id_kuis
        LEFT JOIN kategori_agent ka ON kk.id_kategori = ka.id_kategori
        WHERE k.status IN ('upcoming','active')
        GROUP BY k.id_kuis
        ORDER BY k.tanggal ASC, k.waktu_mulai ASC
    ");
    $result = $query->getResultArray();

    $this->updateStatusList($result);
    return $result;
}

}
