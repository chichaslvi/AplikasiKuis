<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table      = 'kuis';
    protected $primaryKey = 'id_kuis';
    protected $returnType = 'array';

    // 🔁 Gabungan allowedFields dari KuisModel versi 1 & 2 (tanpa duplikasi)
    protected $allowedFields = [
        'nama_kuis',
        'topik',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_menit',       // ← dari versi 2
        'nilai_minimum',
        'batas_pengulangan',
        'status',
        'file_excel',
        'start_at',
        'end_at',
        'created_at',
        'updated_at',
        'published_at',       // ← dari versi 2
    ];

    protected $useTimestamps = true;

    /**
     * ✅ Hanya ubah status di hasil array (untuk tampilan),
     * tanpa mengupdate database langsung.
     */
    private function updateStatusList(array &$list): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ($list as &$kuis) {
            $statusDb = isset($kuis['status_db'])
                ? strtolower((string) $kuis['status_db'])
                : (isset($kuis['status']) ? strtolower((string) $kuis['status']) : 'draft');

            $mulai = !empty($kuis['start_at'])
                ? $kuis['start_at']
                : (!empty($kuis['tanggal']) && !empty($kuis['waktu_mulai'])
                    ? $kuis['tanggal'] . ' ' . $kuis['waktu_mulai']
                    : null);

            $selesai = !empty($kuis['end_at'])
                ? $kuis['end_at']
                : (!empty($kuis['tanggal']) && !empty($kuis['waktu_selesai'])
                    ? $kuis['tanggal'] . ' ' . $kuis['waktu_selesai']
                    : null);

            if (!$mulai || !$selesai) {
                $kuis['status'] = $statusDb;
                continue;
            }

            // 🚀 tampilkan status tanpa update DB
            if ($statusDb === 'active' && strtotime($now) >= strtotime($selesai)) {
                $kuis['status'] = 'inactive';
            } else {
                $kuis['status'] = $statusDb;
            }
        }
    }

    /** List untuk admin */
    public function getAllKuisWithKategori(): array
    {
        $db = \Config\Database::connect();
        $b  = $db->table($this->table . ' k');
        $b->select("k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori, k.status AS status_db");
        $b->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
        $b->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');
        $b->groupBy('k.id_kuis')->orderBy('k.id_kuis', 'DESC');

        $result = $b->get()->getResultArray();
        $this->updateStatusList($result);
        return $result;
    }

    /** Detail satu kuis */
    public function getKuisByIdWithKategori(int $id): ?array
    {
        $db = \Config\Database::connect();
        $b  = $db->table($this->table . ' k');
        $b->select("k.*, GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori, k.status AS status_db");
        $b->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
        $b->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');
        $b->where('k.id_kuis', $id)->groupBy('k.id_kuis');

        $row = $b->get()->getRowArray();
        if ($row) {
            $arr = [$row];
            $this->updateStatusList($arr);
            return $arr[0];
        }
        return null;
    }

    /** List untuk agent: hanya active & belum lewat end_at */
    public function getAvailableKuisForAgent(int $userId = 0): array
    {
        $db = \Config\Database::connect();
        $b  = $db->table($this->table . ' k');
        $b->select("k.*, GROUP_CONCAT(DISTINCT ka.nama_kategori ORDER BY ka.nama_kategori SEPARATOR ', ') AS kategori, k.status AS status_db");
        $b->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
        $b->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');

        $b->where('k.status', 'active');
        $b->where("(k.end_at IS NULL OR k.end_at > NOW())", null, false);

        if ($userId > 0) {
            $b->join('kuis_hasil h', 'h.id_kuis = k.id_kuis AND h.id_user = ' . $db->escape($userId), 'left');
            $b->where('h.id_kuis IS NULL', null, false);
        }

        $b->groupBy('k.id_kuis')->orderBy('k.tanggal ASC, k.waktu_mulai ASC');

        $result = $b->get()->getResultArray();
        $this->updateStatusList($result);
        return $result;
    }

    /**
     * ✅ Kuis untuk dashboard agent berdasarkan kategori tertentu
     */
    public function getKuisByKategoriForNow(int $idKategori): array
    {
        $db = \Config\Database::connect();
        $b  = $db->table($this->table . ' k');
        $b->select("k.*, GROUP_CONCAT(DISTINCT ka.nama_kategori ORDER BY ka.nama_kategori SEPARATOR ', ') AS kategori");
        $b->join('kuis_kategori kk', 'kk.id_kuis = k.id_kuis', 'inner');
        $b->join('kategori_agent ka', 'ka.id_kategori = kk.id_kategori', 'left');

        $b->where('kk.id_kategori', $idKategori);
        $b->where('k.status', 'active');
        $b->where('k.start_at <= NOW()', null, false);
        $b->where('k.end_at > NOW()', null, false);

        $b->groupBy('k.id_kuis');
        $b->orderBy('k.start_at', 'ASC');

        return $b->get()->getResultArray();
    }

    /** Upload = set active */
    public function uploadKuis(int $id): bool
    {
        return $this->update($id, [
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * ✅ Kuis untuk dashboard agent sesuai kategori agent
     */
    public function getAvailableKuisForAgentKategori(int $id_kategori): array
    {
        return $this->db->table('kuis k')
            ->select('k.*')
            ->join('kuis_kategori kk', 'kk.id_kuis = k.id_kuis')
            ->where('k.status', 'active')
            ->where('kk.id_kategori', $id_kategori)
            // ⬇️ pastikan tidak menampilkan kuis yang sudah lewat waktu
            ->where("(COALESCE(k.end_at, CONCAT(k.tanggal,' ',k.waktu_selesai)) > NOW())", null, false)
            ->orderBy('k.tanggal', 'ASC')
            ->orderBy('k.waktu_mulai', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * ✅ Detail kuis per kategori (hanya kuis sesuai kategori agent)
     */
    public function getKuisByIdForAgent(int $kuisId, int $id_kategori): ?array
    {
        $row = $this->db->table('kuis k')
            ->select('k.*')
            ->join('kuis_kategori kk', 'kk.id_kuis = k.id_kuis') // ✅ fix join
            ->where('k.id_kuis', $kuisId)
            ->where('kk.id_kategori', $id_kategori)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }
}
