<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nama',
        'nik',
        'password',
        'role',
        'kategori_agent_id',   // relasi ke tabel kategori_agent
        'team_leader_id',      // relasi ke tabel team_leader
        'must_change_password',
        'last_password_change',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // ✅ timestamps otomatis
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ✅ hook sebelum insert/update
    protected $beforeInsert = ['prepareInsert'];
    protected $beforeUpdate = ['prepareUpdate'];

    /** 🔧 Insert: hash password jika plain, paksa must_change_password = 1, biarkan last_password_change NULL */
    protected function prepareInsert(array $data): array
    {
        // Hash password hanya jika masih plain (belum berbentuk bcrypt)
        if (!empty($data['data']['password']) && !$this->looksHashed($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        // Akun baru wajib ganti password
        $data['data']['must_change_password'] = 1;

        // Biarkan last_password_change NULL saat akun baru
        if (isset($data['data']['last_password_change'])) {
            unset($data['data']['last_password_change']);
        }

        // Default aktif bila tidak diset
        if (!isset($data['data']['is_active'])) {
            $data['data']['is_active'] = 1;
        }

        return $data;
    }

    /** 🔧 Update: hanya hash jika password diisi, catat last_password_change; jangan ubah must_change_password di sini */
    protected function prepareUpdate(array $data): array
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            // Hash hanya jika masih plain
            if (!$this->looksHashed($data['data']['password'])) {
                $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            }
            // Password benar-benar diganti → catat waktu perubahan
            $data['data']['last_password_change'] = date('Y-m-d H:i:s');
            // Jangan menyentuh must_change_password di update umum
        } else {
            // Jika password kosong, jangan ubah kolom password / last_password_change
            unset($data['data']['password'], $data['data']['last_password_change']);
        }

        return $data;
    }

    /** 🔎 Deteksi sederhana hash bcrypt ($2y$ / $2a$ / $2b$) */
    protected function looksHashed(string $value): bool
    {
        return (bool) preg_match('/^\$2[ayb]\$.{56,}$/', $value);
    }

    // ✅ Hitung jumlah user aktif dalam satu team
    public function countActiveByTeam(int $teamId): int
    {
        return $this->where('team_leader_id', $teamId)
                    ->where('is_active', 1)
                    ->countAllResults();
    }

    // ✅ Cari user by NIK (buat login)
    public function getUserByNik(string $nik): ?array
    {
        return $this->where('nik', $nik)
                    ->where('is_active', 1) // hanya user aktif
                    ->first();
    }

    // ✅ Cek apakah user harus ganti password
    public function mustChangePassword(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['must_change_password'] == 1;
    }

    // ✅ Ambil detail user + kategori agent (buat dashboard/profil)
    public function getUserWithKategori(int $userId): ?array
    {
        return $this->select('users.*, kategori_agent.nama_kategori as kategori_nama')
                    ->join('kategori_agent', 'kategori_agent.id_kategori = users.kategori_agent_id', 'left')
                    ->where('users.id', $userId)
                    ->first();
    }

    public function uploadKuis($id)
    {
        return $this->update($id, ['status' => 'active']);
    }
}
