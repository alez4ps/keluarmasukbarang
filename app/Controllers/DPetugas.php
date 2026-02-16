<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BarangModel;

class DPetugas extends BaseController
{
    protected $barang;

    public function __construct()
    {
        $this->barang = new BarangModel();
    }

public function index()
{
    $db = \Config\Database::connect();

    $totalUsers  = $db->table('users')->countAll();

    $totalMasuk  = $db->table('barang')->like('no_agenda','M-','after')->countAllResults();
    $totalKeluar = $db->table('barang')->like('no_agenda','K-','after')->countAllResults();

    $today = date('Y-m-d');
    $masukHariIni  = $db->table('barang')
                        ->like('no_agenda','M-','after')
                        ->where('DATE(tanggal)', $today)
                        ->countAllResults();

    $keluarHariIni = $db->table('barang')
                        ->like('no_agenda','K-','after')
                        ->where('DATE(tanggal)', $today)
                        ->countAllResults();

    $totalAktivitas = $db->table('barang_logs')->countAll();

    return view('Petugas/dashboard', [
        'totalUsers' => $totalUsers,
        'totalMasuk' => $totalMasuk,
        'totalKeluar' => $totalKeluar,
        'masukHariIni' => $masukHariIni,
        'keluarHariIni' => $keluarHariIni,
        'totalAktivitas' => $totalAktivitas
    ]);
}

}