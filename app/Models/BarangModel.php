<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'no_agenda', 'no_spb', 'tanggal', 'nama_barang', 'jumlah', 'satuan',
        'asal', 'tujuan', 'tipe', 'is_partial', 'keterangan', 'jumlah_kembali',
        'jumlah_keluar', 'status', 'masuk_penuh', 'estimasi_kembali', 'akan_kembali'
    ];
    protected $useTimestamps = false;
}