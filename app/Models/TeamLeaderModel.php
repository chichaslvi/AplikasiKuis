<?php
namespace App\Models;

use CodeIgniter\Model;

class TeamLeaderModel extends Model
{
   protected $table = 'team_leader';
protected $primaryKey = 'id';
protected $allowedFields = ['nama', 'created_at', 'updated_at', 'deleted_at', 'is_active'];

}
