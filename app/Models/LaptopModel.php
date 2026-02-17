<?php

namespace App\Models;

use CodeIgniter\Model;

class LaptopModel extends Model
{
    protected $table = 'laptop';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama_pengguna', 'nomor_id_card', 'instansi_divisi', 'merek', 'tipe_laptop', 'nomor_seri',
        'berlaku_sampai', 'spesifikasi_lain', 'status', 'keterangan'
    ];
    protected $useTimestamps = false;
}