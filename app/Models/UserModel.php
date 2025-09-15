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
        'updated_at'
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
}
