<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangLogModel extends Model
{
    protected $table = 'barang_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'barang_id', 'aksi', 'jumlah', 'sisa', 'keterangan', 'created_at'
    ];
    protected $useTimestamps = false;
}