<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama',
        'nik',
        'password',
        'role',
        'must_change_password',
        'last_password_change',
        'created_at', 'updated_at'
    ];
}