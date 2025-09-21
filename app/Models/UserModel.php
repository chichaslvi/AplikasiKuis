<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nama',
        'nik',
        'password',
        'role',
        'kategori_agent_id',   // relasi ke tabel kategori_agent
        'team_leader_id',      // relasi ke tabel team_leader
        'must_change_password',
        'last_password_change',
        'created_at',
        'updated_at',
        'is_active'            // ✅ tambahin agar bisa update/insert kolom ini
    ];

    // ✅ otomatis isi created_at & updated_at
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // bisa juga 'date' atau 'int'

    // ✅ format nama kolom created_at & updated_at sesuai database
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Opsional: biar password otomatis di-hash kalau di-insert/update
    protected function beforeInsert(array $data)
    {
        return $this->hashPassword($data);
    }

    protected function beforeUpdate(array $data)
    {
        return $this->hashPassword($data);
    }

    private function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            // ✅ update otomatis kalau password diubah
            $data['data']['last_password_change'] = date('Y-m-d H:i:s');
            $data['data']['must_change_password'] = 0; // tandai sudah diganti
        }
        return $data;
    }

    // ✅ Cek jumlah user aktif di bawah Team Leader
    public function countActiveByTeam($teamId)
    {
        return $this->where('team_leader_id', $teamId)
                    ->where('is_active', 1)
                    ->countAllResults();
    }

    // ✅ Tambahan: ambil user by NIK (untuk login)
    public function getUserByNik($nik)
    {
        return $this->where('nik', $nik)
                    ->where('is_active', 1) // hanya user aktif yang bisa login
                    ->first();
    }

    // ✅ Helper: cek apakah user wajib ganti password
    public function mustChangePassword($userId)
    {
        $user = $this->find($userId);
        return $user && $user['must_change_password'] == 1;
    }
}
