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
   public function getRiwayatByUser($userId)
{
    return $this->select('kuis_hasil.id_kuis, kuis_hasil.jumlah_soal, kuis_hasil.total_skor, kuis_hasil.tanggal_pengerjaan, kuis.nama_kuis')
        ->join('kuis', 'kuis.id_kuis = kuis_hasil.id_kuis')
        ->where('kuis_hasil.id_user', $userId)
        ->orderBy('kuis_hasil.tanggal_pengerjaan', 'DESC')
        ->findAll();
}
}
/**
 * Alias untuk kompatibilitas lama
 */
class HasilKuisModel extends KuisHasilModel
{
    // Tidak perlu isi ulang apa pun — semua warisan dari KuisHasilModel
}
