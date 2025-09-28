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

    // ✅ hook sebelum insert/update → hash password
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // 🔧 perbaikan: jangan pakai private, ganti jadi protected/public
    protected function hashPassword(array $data): array
    {
        if (!empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            $data['data']['last_password_change'] = date('Y-m-d H:i:s');
            $data['data']['must_change_password'] = 0; // tandai sudah diganti
        }
        return $data;
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
