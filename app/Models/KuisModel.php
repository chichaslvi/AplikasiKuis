<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table         = 'kuis';
    protected $primaryKey    = 'id_kuis';
    protected $returnType    = 'array';

    // Pastikan semua kolom yang di-insert/update dari controller terdaftar di sini
    protected $allowedFields = [
        'nama_kuis',
        'topik',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'nilai_minimum',
        'batas_pengulangan',
        'status',
        'file_excel',     // ⬅️ dipakai saat upload/update file excel
        'updated_at',     // optional, biar aman kalau kamu set manual
        'created_at'      // optional
    ];

    // Pakai timestamps default CI4 (created_at, updated_at)
    protected $useTimestamps = true;

    /**
     * 🔄 Update status kuis berdasarkan waktu sekarang (draft/active/inactive)
     * - Tidak akan menimpa jika status di DB sudah "active" (manual oleh admin).
     * - Aman dari notice/undefined index.
     */
    private function updateStatusList(array &$list): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ($list as &$kuis) {
            // Ambil status existing dari DB (fallback ke 'status'), default 'draft'
            $statusDb = isset($kuis['status_db'])
                ? strtolower((string) $kuis['status_db'])
                : (isset($kuis['status']) ? strtolower((string) $kuis['status']) : 'draft');

            // 🔒 Jika sudah active (misalnya diaktifkan admin lewat tombol Upload), JANGAN diubah
            if ($statusDb === 'active') {
                $kuis['status'] = 'active';
                continue;
            }

            // Bangun waktu mulai & selesai dengan fallback aman
            $mulai   = null;
            $selesai = null;

            if (!empty($kuis['start_at'])) {
                $mulai = $kuis['start_at'];
            } elseif (!empty($kuis['tanggal']) && !empty($kuis['waktu_mulai'])) {
                $mulai = $kuis['tanggal'] . ' ' . $kuis['waktu_mulai'];
            }

            if (!empty($kuis['end_at'])) {
                $selesai = $kuis['end_at'];
            } elseif (!empty($kuis['tanggal']) && !empty($kuis['waktu_selesai'])) {
                $selesai = $kuis['tanggal'] . ' ' . $kuis['waktu_selesai'];
            }

            // Kalau waktu tidak lengkap, biarkan status existing
            if (!$mulai || !$selesai) {
                $kuis['status'] = $statusDb;
                continue;
            }

            $nowTs    = strtotime($now);
            $mulaiTs  = strtotime($mulai);
            $selesaiTs= strtotime($selesai);

            // Tri-state: draft (belum mulai), active (berlangsung), inactive (sudah lewat)
            if ($nowTs < $mulaiTs) {
                $newStatus = 'draft';
            } elseif ($nowTs >= $mulaiTs && $nowTs <= $selesaiTs) {
                $newStatus = 'active';
            } else {
                $newStatus = 'inactive';
            }

            // Update DB kalau berubah
            if ($newStatus !== $statusDb) {
                $this->update($kuis['id_kuis'], [
                    'status'     => $newStatus,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Sinkronkan nilai di array yang dikembalikan
            $kuis['status'] = $newStatus;
        }
    }

    /**
     * 📌 Ambil semua kuis beserta kategori agent (untuk halaman admin)
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
     * 📌 Ambil detail kuis by ID (dengan kategori)
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
     * 📌 Ambil kuis yang tersedia untuk agent (status active), exclude yang sudah dikerjakan user (opsional)
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
        $builder->where('k.status', 'active'); // hanya active yang muncul di agent

        // ➕ optional: filter kuis yang belum pernah dikerjakan user tertentu
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
     * 📌 Ubah status kuis jadi Active (aksi Upload oleh admin)
     */
    public function uploadKuis(int $id): bool
    {
        return $this->update($id, [
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s') // biar kelihatan ada perubahan
        ]);
    }
}
