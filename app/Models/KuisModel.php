<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table      = 'kuis';
    protected $primaryKey = 'id_kuis';
    protected $returnType = 'array';

    protected $allowedFields = [
        'nama_kuis',
        'topik',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'nilai_minimum',
        'batas_pengulangan',
        'status',
        'file_excel',
        'start_at',   // penting: biar tidak NULL
        'end_at',     // penting: biar tidak NULL
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    /**
     * Turunkan active -> inactive jika sudah lewat end_at.
     * Tidak mem-promote draft -> active.
     */
    private function updateStatusList(array &$list): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ($list as &$kuis) {
            $statusDb = isset($kuis['status_db'])
                ? strtolower((string) $kuis['status_db'])
                : (isset($kuis['status']) ? strtolower((string) $kuis['status']) : 'draft');

            // bangun waktu mulai/selesai (kalau ada)
            $mulai = !empty($kuis['start_at'])
                ? $kuis['start_at']
                : (!empty($kuis['tanggal']) && !empty($kuis['waktu_mulai']) ? $kuis['tanggal'].' '.$kuis['waktu_mulai'] : null);

            $selesai = !empty($kuis['end_at'])
                ? $kuis['end_at']
                : (!empty($kuis['tanggal']) && !empty($kuis['waktu_selesai']) ? $kuis['tanggal'].' '.$kuis['waktu_selesai'] : null);

            // kalau data waktu tidak lengkap → JANGAN ubah status
            if (!$mulai || !$selesai) {
                $kuis['status'] = $statusDb;
                continue;
            }

            // hanya handle yang active
            if ($statusDb === 'active') {
                if (strtotime($now) >= strtotime($selesai)) {
                    $this->update($kuis['id_kuis'], [
                        'status'     => 'inactive',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $kuis['status'] = 'inactive';
                } else {
                    $kuis['status'] = 'active';
                }
                continue;
            }

            // status lain tidak diutak-atik
            $kuis['status'] = $statusDb;
        }
    }

    /** List untuk admin */
    public function getAllKuisWithKategori(): array
    {
        $db = \Config\Database::connect();
        $b  = $db->table($this->table.' k');
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
        $b  = $db->table($this->table.' k');
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
        $b  = $db->table($this->table.' k');
        $b->select("k.*, GROUP_CONCAT(DISTINCT ka.nama_kategori ORDER BY ka.nama_kategori SEPARATOR ', ') AS kategori, k.status AS status_db");
        $b->join('kuis_kategori kk', 'k.id_kuis = kk.id_kuis', 'left');
        $b->join('kategori_agent ka', 'kk.id_kategori = ka.id_kategori', 'left');

        $b->where('k.status', 'active');
        // ⛔ pastikan tidak tampil jika sudah lewat waktu
        $b->where("(k.end_at IS NULL OR k.end_at > NOW())", null, false);

        if ($userId > 0) {
            $b->join('kuis_hasil h', 'h.id_kuis = k.id_kuis AND h.id_user = '.$db->escape($userId), 'left');
            $b->where('h.id_kuis IS NULL', null, false);
        }

        $b->groupBy('k.id_kuis')->orderBy('k.tanggal ASC, k.waktu_mulai ASC');

        $result = $b->get()->getResultArray();
        $this->updateStatusList($result);
        return $result;
    }

    /** Upload = set active */
    public function uploadKuis(int $id): bool
    {
        return $this->update($id, [
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
