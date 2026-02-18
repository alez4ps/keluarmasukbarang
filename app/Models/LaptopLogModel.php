<?php

namespace App\Models;

use CodeIgniter\Model;

class LaptopLogModel extends Model
{
    protected $table = 'laptop_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'laptop_id', 'aksi', 'keterangan', 'created_at'
    ];
    protected $useTimestamps = false;
}