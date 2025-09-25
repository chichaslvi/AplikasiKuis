<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table            = 'kuis';
    protected $primaryKey       = 'id_kuis';
    protected $allowedFields    = [
        'nama_kuis',
        'topik',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'nilai_minimum',
        'batas_pengulangan',
        'status'
    ];
    protected $useTimestamps    = false;

    /**
     * 🔄 Update status kuis berdasarkan waktu sekarang
     */private function updateStatusList(array &$list): void
{
    $now = date('Y-m-d H:i:s');

    foreach ($list as &$kuis) {
        // ✅ kalau masih draft, jangan diutak-atik
        if ($kuis['status_db'] === 'draft') {
            $kuis['status'] = 'draft';
            continue;
        }

        $mulai   = $kuis['tanggal'] . ' ' . $kuis['waktu_mulai'];
        $selesai = $kuis['tanggal'] . ' ' . $kuis['waktu_selesai'];

        if ($now < $mulai) {
            $kuis['status'] = 'draft'; // sebelum mulai
        } elseif ($now >= $mulai && $now <= $selesai) {
            $kuis['status'] = 'active'; // sedang berlangsung
        } else {
            $kuis['status'] = 'inactive'; // sudah selesai
        }

        // ✅ update hanya jika berbeda dari DB
        if (!isset($kuis['status_db']) || $kuis['status'] !== $kuis['status_db']) {
            $this->update($kuis['id_kuis'], ['status' => $kuis['status']]);
        }
    }
}


    /**
     * 📌 Ambil semua kuis beserta kategori agent
     */
    public function getAllKuisWithKategori(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' k');
        $builder->select("k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori, k.status AS status_db");
        $builder->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
        $builder->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');
        $builder->groupBy('k.id_kuis');
        $builder->orderBy('k.id_kuis', 'DESC');

        $result = $builder->get()->getResultArray();
        $this->updateStatusList($result);

        return $result;
    }

    /**
     * 📌 Ambil detail kuis by ID
     */
    public function getKuisByIdWithKategori(int $id): ?array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' k');
        $builder->select("k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori, k.status AS status_db");
        $builder->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
        $builder->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');
        $builder->where('k.id_kuis', $id);
        $builder->groupBy('k.id_kuis');

        $row = $builder->get()->getRowArray();

        if ($row) {
            $list = [$row];
            $this->updateStatusList($list);
            return $list[0];
        }

        return null;
    }

public function getAvailableKuisForAgent(): array
{
    $db = \Config\Database::connect();
    $builder = $db->table($this->table . ' k');
    $builder->select("k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori, k.status AS status_db");
    $builder->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
    $builder->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');
    $builder->where('k.status', 'active');  // ✅ hanya kuis active yg tampil
    $builder->groupBy('k.id_kuis');
    $builder->orderBy('k.tanggal ASC, k.waktu_mulai ASC');

    $result = $builder->get()->getResultArray();

    return $result;
}

    public function uploadKuis(int $id): bool
{
    return $this->update($id, ['status' => 'active']);
}

}
