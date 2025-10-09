<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model gabungan KuisHasilModel & HasilKuisModel.
 * 
 * - Disatukan agar tidak duplikat
 * - Sudah disesuaikan dengan controller Agent::submitKuis()
 * - Menggunakan alias HasilKuisModel agar backward compatible
 */
class KuisHasilModel extends Model
{
    protected $table            = 'kuis_hasil';
    protected $primaryKey       = 'id_hasil';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    // Field yang bisa diisi (harus sesuai nama kolom di DB)
    protected $allowedFields = [
        'id_user',
        'id_kuis',
        'jumlah_soal',
        'jawaban',
        'jawaban_benar',
        'jawaban_salah',
        'total_skor',
        'status',              // in_progress / finished / abandoned
        'tanggal_pengerjaan',
        'started_at',
        'finished_at',
        'jumlah_pengerjaan',
    ];

    // Nonaktifkan timestamps otomatis CI4 (karena kamu pakai finished_at manual)
    protected $useTimestamps = false;

    /**
     * Hitung total attempt user pada kuis tertentu
     */
    public function countUserAttempts(int $userId, int $kuisId): int
    {
        return $this->where('id_user', $userId)
                    ->where('id_kuis', $kuisId)
                    ->countAllResults();
    }

    /**
     * Ambil riwayat pengerjaan kuis berdasarkan user
     */
// Di HasilModel
public function getRiwayatByUser($userId)
{
    return $this->db->table('kuis_hasil h')
        ->select('h.*, k.nama_kuis, k.topik, k.tanggal, k.waktu_mulai, k.waktu_selesai')
        ->join('kuis k', 'k.id_kuis = h.id_kuis')
        ->where('h.id_user', $userId)
        ->where('h.status', 'finished')
        ->whereIn('h.id_hasil', function($builder) use ($userId) {
            return $builder->select('MAX(id_hasil)')
                          ->from('kuis_hasil')
                          ->where('id_user', $userId)
                          ->where('status', 'finished')
                          ->groupBy('id_kuis');
        })
        ->orderBy('h.finished_at', 'DESC')
        ->get()
        ->getResultArray();
}}
/**
 * Alias untuk kompatibilitas lama
 */
class HasilKuisModel extends KuisHasilModel
{
    // Tidak perlu isi ulang apa pun — semua warisan dari KuisHasilModel
}
