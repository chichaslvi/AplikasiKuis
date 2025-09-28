<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table         = 'kuis';
    protected $primaryKey    = 'id_kuis';
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
    protected $useTimestamps = true;

    /**
     * 🔄 Update status kuis berdasarkan waktu sekarang
     */
   private function updateStatusList(array &$list): void
{
    $now = date('Y-m-d H:i:s');

    foreach ($list as &$kuis) {
        $statusDb = isset($kuis['status_db'])
            ? strtolower($kuis['status_db'])
            : (isset($kuis['status']) ? strtolower($kuis['status']) : 'draft');

        // 🔧 Fix: jangan paksa balik ke draft kalau sudah active
        if ($statusDb === 'active' || strtolower($kuis['status']) === 'active') {
            // Jika status sudah active, biarkan tetap active
            continue;
        }

        $mulai   = !empty($kuis['start_at'])
                    ? $kuis['start_at']
                    : ($kuis['tanggal'] . ' ' . $kuis['waktu_mulai']);
        $selesai = !empty($kuis['end_at'])
                    ? $kuis['end_at']
                    : ($kuis['tanggal'] . ' ' . $kuis['waktu_selesai']);

        $newStatus = (strtotime($now) > strtotime($selesai)) ? 'inactive' : 'active';
        $kuis['status'] = $newStatus;

        if ($newStatus !== $statusDb) {
            $this->update($kuis['id_kuis'], ['status' => $newStatus]);
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

    /**
     * 📌 Ambil kuis yang tersedia untuk agent (status Active)
     */
   public function getAvailableKuisForAgent(int $userId = 0): array
{
    $db = \Config\Database::connect();

    $builder = $db->table($this->table . ' k');
    $builder->select("
        k.*,
        GROUP_CONCAT(DISTINCT ka.nama_kategori ORDER BY ka.nama_kategori SEPARATOR ', ') AS kategori,
        k.status AS status_db
    ");
    $builder->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
    $builder->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');
    $builder->where('k.status', 'active');

    // ➕ filter kuis sudah dikerjakan user
    if ($userId > 0) {
        $builder->join(
            'kuis_hasil h',
            'h.id_kuis = k.id_kuis AND h.id_user = ' . $db->escape($userId),
            'left'
        );
        $builder->where('h.id IS NULL');
    }

    $builder->groupBy('k.id_kuis');
    $builder->orderBy('k.tanggal ASC, k.waktu_mulai ASC');

    $result = $builder->get()->getResultArray();
    $this->updateStatusList($result);

    return $result;
}



    /**
     * 📌 Ubah status kuis jadi Active (Upload)
     */
    public function uploadKuis(int $id): bool
{
    return $this->update($id, ['status' => 'active']);
}


}
