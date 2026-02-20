<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\BarangLogModel;
use App\Models\LaptopModel;
use App\Models\LaptopLogModel;

class Barang extends BaseController
{
    protected $barang;
    protected $barangLog;
    protected $laptop;
    protected $laptopLog;

    public function __construct()
    {
        $this->barang = new BarangModel();
        $this->barangLog = new BarangLogModel();
        $this->laptop = new LaptopModel();
        $this->laptopLog = new LaptopLogModel();
    }

    /**
     * HALAMAN UTAMA REGISTRASI
     */
public function index()
{
    // ========== DATA BARANG ==========
    $keyword = $this->request->getGet('keyword');
    $builder = $this->barang;

    if ($keyword) {
        $builder = $builder
            ->groupStart()
                ->like('no_agenda', $keyword)
                ->orLike('no_spb', $keyword)
                ->orLike('tanggal', $keyword)
                ->orLike('nama_barang', $keyword)
                ->orLike('jumlah', $keyword)
                ->orLike('satuan', $keyword)
                ->orLike('asal', $keyword)
                ->orLike('tujuan', $keyword)
                ->orLike('tipe', $keyword)
                ->orLike('is_partial', $keyword)
                ->orLike('keterangan', $keyword)
                ->orLike('status', $keyword)
            ->groupEnd();
    }

    $barangs = $builder->orderBy('tanggal', 'DESC')->findAll();

    $barangIds = array_column($barangs, 'id');

    $logs = [];
    if (!empty($barangIds)) {
        $logData = $this->barangLog
            ->whereIn('barang_id', $barangIds)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        foreach ($logData as $log) {
            $logs[$log['barang_id']][] = $log;
        }
    }

    foreach ($barangs as &$barang) {
        $barang['history'] = $logs[$barang['id']] ?? [];
        $barang['masuk_penuh'] = $barang['masuk_penuh'] == 1;
    }

    // ========== DATA LAPTOP ==========
    $keywordLaptop = $this->request->getGet('keyword_laptop');
    $statusLaptop = $this->request->getGet('status_laptop');
    
    $builderLaptop = $this->laptop;

    if ($keywordLaptop) {
        $builderLaptop = $builderLaptop->groupStart()
            ->like('nama_pengguna', $keywordLaptop)
            ->orLike('nomor_id_card', $keywordLaptop)
            ->orLike('instansi_divisi', $keywordLaptop)
            ->orLike('merek', $keywordLaptop)
            ->orLike('tipe_laptop', $keywordLaptop)
            ->orLike('nomor_seri', $keywordLaptop)
            ->orLike('spesifikasi_lain', $keywordLaptop)
            ->orLike('no_registrasi', $keywordLaptop)
            ->orLike('jenis', $keywordLaptop)
            ->groupEnd();
    }

    if ($statusLaptop && $statusLaptop != 'Semua') {
        $builderLaptop = $builderLaptop->where('status', $statusLaptop);
    }

    $laptops = $builderLaptop->orderBy('created_at', 'DESC')->findAll();

    // ========== HITUNG REGISTRASI KE UNTUK SETIAP LAPTOP ==========
    foreach ($laptops as &$laptop) {
        // Ambil logs untuk setiap laptop
        $laptop['logs'] = $this->laptopLog->where('laptop_id', $laptop['id'])
                                          ->orderBy('created_at', 'DESC')
                                          ->findAll();
        
        // Hitung registrasi ke berapa untuk nomor seri ini
        $riwayat = $this->laptop->where('nomor_seri', $laptop['nomor_seri'])
                                ->orderBy('created_at', 'ASC')
                                ->findAll();
        
        // Cari posisi laptop ini dalam riwayat
        $registrasiKe = 1;
        foreach ($riwayat as $index => $item) {
            if ($item['id'] == $laptop['id']) {
                $registrasiKe = $index + 1;
                break;
            }
        }
        
        $laptop['registrasi_ke_sekarang'] = $registrasiKe;
        $laptop['total_registrasi'] = count($riwayat);
        $laptop['registrasi_selanjutnya'] = count($riwayat) + 1;
    }

    // ========== GENERATE NO AGENDA BARANG ==========
    $db = \Config\Database::connect();

    $q = $db->query("
        SELECT IFNULL(MAX(CAST(SUBSTRING(no_agenda,4) AS UNSIGNED)),0) + 1 AS next
        FROM barang
        WHERE no_agenda LIKE 'M-%'
          AND YEAR(tanggal) = YEAR(CURDATE())
    ");
    $noAgenda = 'M-' . str_pad($q->getRow()->next, 4, '0', STR_PAD_LEFT);

    $d = $db->query("
        SELECT IFNULL(MAX(CAST(SUBSTRING(no_agenda,4) AS UNSIGNED)),0) + 1 AS next
        FROM barang
        WHERE no_agenda LIKE 'K-%'
          AND YEAR(tanggal) = YEAR(CURDATE())
    ");
    $noAgendaKeluar = 'K-' . str_pad($d->getRow()->next, 4, '0', STR_PAD_LEFT);

    // ========== DATA UNTUK VIEW ==========
    $data = [
        'barangs'         => $barangs,
        'laptops'         => $laptops,
        'keyword'         => $keyword,
        'keywordLaptop'   => $keywordLaptop,
        'statusLaptop'    => $statusLaptop,
        'noAgenda'        => $noAgenda,
        'noAgendaKeluar'  => $noAgendaKeluar,
        'tanggal'         => date('Y-m-d H:i:s')
    ];

    return view('registrasi/index', $data);
}

    // ============================================
    // FUNGSI BARANG (REGISTRASI)
    // ============================================

    /**
     * STORE BARANG (Registrasi Barang Masuk/Keluar)
     */
    public function store()
    {
        $isPartialChecked = $this->request->getPost('is_partial') ? true : false;
        $akanKembaliChecked = $this->request->getPost('akan_kembali') ? true : false;
        $noAgenda = $this->request->getPost('no_agenda');
        
        $isPartial = $isPartialChecked ? 'Ya' : 'Tidak';
        $akanKembali = $akanKembaliChecked ? 'Ya' : 'Tidak';
        
        $isMasuk = str_starts_with($noAgenda, 'M-');
        $isKeluar = str_starts_with($noAgenda, 'K-');
        
        $jumlahMasuk = 0;
        $jumlahKeluar = 0;
        $status = 'Belum Selesai';
        $keterangan = '';
        $masukPenuh = false;
        
        if ($isMasuk) {
            if ($akanKembaliChecked) {
                $keterangan = 'Belum Kembali';
            } else {
                $keterangan = 'Tidak Kembali';
                $status = ($isPartial === 'Tidak') ? 'Selesai' : 'Belum Selesai';
            }
            
            if ($isPartialChecked && $this->request->getPost('jumlah_masuk')) {
                $jumlahMasuk = (int) $this->request->getPost('jumlah_masuk');
                if ($jumlahMasuk > (int) $this->request->getPost('jumlah')) {
                    $jumlahMasuk = (int) $this->request->getPost('jumlah');
                }
            } else if (!$isPartialChecked) {
                $jumlahMasuk = (int) $this->request->getPost('jumlah');
            }
            
            $masukPenuh = ($jumlahMasuk >= (int) $this->request->getPost('jumlah'));
        }
        
        elseif ($isKeluar) {
            if ($akanKembaliChecked) {
                $keterangan = 'Belum Kembali';
            } else {
                $keterangan = 'Tidak Kembali';
                $status = ($isPartial === 'Tidak') ? 'Selesai' : 'Belum Selesai';
            }
            
            if ($isPartialChecked && $this->request->getPost('jumlah_masuk')) {
                $jumlahKeluar = (int) $this->request->getPost('jumlah_masuk');
                if ($jumlahKeluar > (int) $this->request->getPost('jumlah')) {
                    $jumlahKeluar = (int) $this->request->getPost('jumlah');
                }
            } else if (!$isPartialChecked) {
                $jumlahKeluar = (int) $this->request->getPost('jumlah');
            }
            
            $masukPenuh = false;
        }

        $data = [
            'no_agenda'       => $noAgenda,
            'tanggal'         => $this->request->getPost('tanggal'),
            'tipe'            => $this->request->getPost('tipe'),
            'no_spb'          => $this->request->getPost('no_spb'),
            'nama_barang'     => $this->request->getPost('nama_barang'),
            'jumlah'          => (int) $this->request->getPost('jumlah'),
            'jumlah_kembali'  => $jumlahMasuk,
            'jumlah_keluar'   => $jumlahKeluar,
            'satuan'          => $this->request->getPost('satuan'),
            'asal'            => $this->request->getPost('asal'),
            'tujuan'          => $this->request->getPost('tujuan'),
            'is_partial'      => $isPartial,
            'keterangan'      => $keterangan,
            'status'          => $status,
            'masuk_penuh'     => $masukPenuh ? 1 : 0,
            'akan_kembali'    => $akanKembali,
            'estimasi_kembali' => $akanKembaliChecked ? $this->request->getPost('estimasi_kembali') : null
        ];

        $id = $this->barang->insert($data);

        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Registrasi',
            'jumlah'     => $data['jumlah'],
            'sisa'       => $data['jumlah'],
            'keterangan' => 'Registrasi Awal',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($jumlahMasuk > 0) {
            $this->barangLog->insert([
                'barang_id'  => $id,
                'aksi'       => 'Masuk',
                'jumlah'     => $jumlahMasuk,
                'sisa'       => $data['jumlah'] - $jumlahMasuk,
                'keterangan' => 'Masuk Awal',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        if ($jumlahKeluar > 0) {
            $this->barangLog->insert([
                'barang_id'  => $id,
                'aksi'       => 'Keluar',
                'jumlah'     => $jumlahKeluar,
                'sisa'       => $data['jumlah'] - $jumlahKeluar,
                'keterangan' => 'Keluar Awal',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('/registrasi')->with('success', 'Registrasi berhasil');
    }

    /**
     * EDIT BARANG
     */
    public function edit($id)
    {
        return view('registrasi/edit', [
            'barang' => $this->barang->find($id)
        ]);
    }

    /**
     * UPDATE BARANG
     */
    public function update($id)
    {
        $this->barang->update($id, [
            'no_spb'      => $this->request->getPost('no_spb'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah'      => $this->request->getPost('jumlah'),
            'satuan'      => $this->request->getPost('satuan'),
            'asal'        => $this->request->getPost('asal'),
            'tujuan'      => $this->request->getPost('tujuan'),
            'tipe'        => $this->request->getPost('tipe'),
            'akan_kembali' => $this->request->getPost('akan_kembali') ? 'Ya' : 'Tidak',
            'estimasi_kembali' => $this->request->getPost('estimasi_kembali')
        ]);

        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Update',
            'jumlah'     => 0,
            'sisa'       => 0,
            'keterangan' => 'Data barang diperbarui',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Data diperbarui');
    }

    /**
     * SELESAIKAN BARANG
     */
    public function selesai($id)
    {
        $barang = $this->barang->find($id);
        
        if (!$barang) {
            return redirect()->to('/registrasi')->with('error', 'Barang tidak ditemukan');
        }
        
        if ($barang['status'] === 'Selesai') {
            return redirect()->to('/registrasi')->with('warning', 'Barang sudah berstatus Selesai');
        }

        $jumlahTotal = (int) $barang['jumlah'];
        $jumlahMasuk = (int) $barang['jumlah_kembali'];
        $jumlahKeluar = (int) $barang['jumlah_keluar'];
        
        $isMasuk = str_starts_with($barang['no_agenda'], 'M-');
        $isKeluar = str_starts_with($barang['no_agenda'], 'K-');
        $akanKembali = ($barang['akan_kembali'] ?? 'Tidak') === 'Ya';
        
        $keteranganLog = [];
        $updateData = [
            'status' => 'Selesai',
            'keterangan' => date('Y-m-d H:i:s')
        ];
        
        if ($isMasuk) {
            if ($jumlahMasuk < $jumlahTotal) {
                $sisaMasuk = $jumlahTotal - $jumlahMasuk;
                
                $updateData['jumlah_kembali'] = $jumlahTotal;
                $updateData['masuk_penuh'] = 1;
                
                $keteranganLog[] = "Sisa masuk: {$sisaMasuk} {$barang['satuan']}";
                
                $this->barangLog->insert([
                    'barang_id'  => $id,
                    'aksi'       => 'Masuk',
                    'jumlah'     => $sisaMasuk,
                    'sisa'       => 0,
                    'keterangan' => "Sisa masuk",
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            if ($akanKembali) {
                if ($jumlahKeluar < $jumlahTotal) {
                    $sisaKeluar = $jumlahTotal - $jumlahKeluar;
                    
                    $updateData['jumlah_keluar'] = $jumlahTotal;
                    
                    $keteranganLog[] = "Sisa keluar: {$sisaKeluar} {$barang['satuan']}";
                    
                    $this->barangLog->insert([
                        'barang_id'  => $id,
                        'aksi'       => 'Keluar',
                        'jumlah'     => $sisaKeluar,
                        'sisa'       => 0,
                        'keterangan' => "Sisa keluar",
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        
        elseif ($isKeluar) {
            if ($jumlahKeluar < $jumlahTotal) {
                $sisaKeluar = $jumlahTotal - $jumlahKeluar;
                
                $updateData['jumlah_keluar'] = $jumlahTotal;
                
                $keteranganLog[] = "Sisa keluar: {$sisaKeluar} {$barang['satuan']}";
                
                $this->barangLog->insert([
                    'barang_id'  => $id,
                    'aksi'       => 'Keluar',
                    'jumlah'     => $sisaKeluar,
                    'sisa'       => 0,
                    'keterangan' => "Sisa keluar",
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            if ($akanKembali) {
                if ($jumlahMasuk < $jumlahTotal) {
                    $sisaMasuk = $jumlahTotal - $jumlahMasuk;
                    
                    $updateData['jumlah_kembali'] = $jumlahTotal;
                    $updateData['masuk_penuh'] = 1;
                    
                    $keteranganLog[] = "Sisa masuk: {$sisaMasuk} {$barang['satuan']}";
                    
                    $this->barangLog->insert([
                        'barang_id'  => $id,
                        'aksi'       => 'Masuk',
                        'jumlah'     => $sisaMasuk,
                        'sisa'       => 0,
                        'keterangan' => "Sisa masuk",
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        
        $this->barang->update($id, $updateData);
        
        $logKeterangan = 'Barang telah selesai';
        
        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Selesai',
            'jumlah'     => 0,
            'sisa'       => 0,
            'keterangan' => $logKeterangan,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $pesan = 'Status berhasil diubah menjadi Selesai';
        if (!empty($keteranganLog)) {
            $pesan .= ' (' . implode(', ', $keteranganLog) . ')';
        }

        return redirect()->to('/registrasi')->with('success', $pesan);
    }

    /**
     * PROSES MASUK BARANG
     */
    public function prosesMasuk($id)
    {
        $barang = $this->barang->find($id);

        if (!$barang) {
            return redirect()->back()->with('error', 'Data barang tidak ditemukan');
        }

        $masuk = (int) $this->request->getPost('jumlah_masuk');

        if ($masuk <= 0) {
            return redirect()->back()->with('error', 'Jumlah masuk tidak valid');
        }

        $jumlahTotal  = (int) $barang['jumlah'];
        $sudahMasuk   = (int) ($barang['jumlah_kembali'] ?? 0);
        $totalMasuk   = $sudahMasuk + $masuk;

        if ($totalMasuk > $jumlahTotal) {
            return redirect()->back()
                ->with('error', 'Jumlah masuk melebihi jumlah barang');
        }

        $status     = 'Belum Selesai';
        $keterangan = $barang['keterangan'];
        $masukPenuh = false;

        if ($totalMasuk >= $jumlahTotal) {
            $totalMasuk = $jumlahTotal;
            $masukPenuh = true;
            $keterangan = 'Masuk Penuh';

            if ($barang['akan_kembali'] === 'Tidak') {
                $status = 'Selesai';
                $keterangan = date('Y-m-d H:i:s');
            }
        }

        $this->barang->update($id, [
            'jumlah_kembali' => $totalMasuk,
            'masuk_penuh'    => $masukPenuh ? 1 : 0,
            'status'         => $status,
            'keterangan'     => $keterangan
        ]);

        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Masuk',
            'jumlah'     => $masuk,
            'sisa'       => $jumlahTotal - $totalMasuk,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')
            ->with('success', 'Barang berhasil masuk');
    }

    /**
     * PROSES KELUAR BARANG
     */
    public function prosesKeluar($id)
    {
        $barang = $this->barang->find($id);
        $keluar = (int) $this->request->getPost('jumlah_keluar');

        if (!$barang || $keluar <= 0) {
            return redirect()->back()->with('error', 'Jumlah tidak valid');
        }

        $sisa = (int)$barang['jumlah'] - (int)$barang['jumlah_keluar'];

        if ($keluar > $sisa) {
            return redirect()->back()
                ->with('error', "Jumlah keluar melebihi sisa ($sisa)");
        }

        $totalKeluar = (int)$barang['jumlah_keluar'] + $keluar;
        if ($totalKeluar > $barang['jumlah']) {
            $totalKeluar = $barang['jumlah'];
        }

        $status     = 'Belum Selesai';
        $keterangan = $barang['keterangan'];

        if ($totalKeluar == $barang['jumlah']) {
            if ($barang['akan_kembali'] === 'Tidak' || 
                ($barang['masuk_penuh'] && $barang['akan_kembali'] === 'Ya')) {
                $status     = 'Selesai';
                $keterangan = date('Y-m-d H:i:s');
            }
        }

        $this->barang->update($id, [
            'jumlah_keluar' => $totalKeluar,
            'status'        => $status,
            'keterangan'    => $keterangan
        ]);

        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Keluar',
            'jumlah'     => $keluar,
            'sisa'       => $barang['jumlah'] - $totalKeluar,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')
            ->with('success', 'Barang berhasil keluar');
    }

    /**
     * PROSES KELUAR LANGSUNG
     */
    public function prosesKeluarLangsung($id)
    {
        $barang = $this->barang->find($id);

        if (!$barang) {
            return redirect()->to('/registrasi')->with('error', 'Data tidak ditemukan');
        }

        $status     = 'Belum Selesai';
        $keterangan = $barang['keterangan'];

        if ($barang['akan_kembali'] === 'Tidak' || 
            ($barang['masuk_penuh'] && $barang['akan_kembali'] === 'Ya')) {
            $status     = 'Selesai';
            $keterangan = date('Y-m-d H:i:s');
        }

        $this->barang->update($id, [
            'jumlah_keluar' => $barang['jumlah'],
            'status'        => $status,
            'keterangan'    => $keterangan
        ]);

        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Keluar',
            'jumlah'     => $barang['jumlah'],
            'sisa'       => 0,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')
            ->with('success', 'Barang berhasil keluar');
    }

    /**
     * MASUK LANGSUNG
     */
    public function masukLangsung($id)
    {
        $barang = $this->barang->find($id);

        if (!$barang) {
            return redirect()->back()->with('error', 'Data barang tidak ditemukan');
        }

        $jumlahTotal   = (int) $barang['jumlah'];
        $sudahKembali  = (int) ($barang['jumlah_kembali'] ?? 0);
        $masuk         = $jumlahTotal - $sudahKembali;

        if ($masuk <= 0) {
            return redirect()->back()->with('error', 'Tidak ada barang yang bisa masuk');
        }

        $this->barang->update($id, [
            'jumlah_kembali' => $jumlahTotal,
            'masuk_penuh'    => 1,
            'status'         => 'Belum Selesai',
            'keterangan'     => 'Masuk Penuh'
        ]);

        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Masuk',
            'jumlah'     => $masuk,
            'sisa'       => 0,
            'keterangan' => 'Masuk Penuh',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')
            ->with('success', 'Barang berhasil masuk');
    }

    /**
     * PROSES KEMBALI
     */
    public function prosesKembali($id)
    {
        $barang = $this->barang->find($id);
        $input  = (int) $this->request->getPost('jumlah_kembali');

        if (!$barang || $input <= 0) {
            return redirect()->back()->with('error', 'Jumlah tidak valid');
        }

        $jumlahAwal   = (int) $barang['jumlah'];
        $sudahKembali = (int) $barang['jumlah_kembali'];

        $sisa = $jumlahAwal - $sudahKembali;

        if ($input > $sisa) {
            return redirect()->back()
                ->with('error', "Jumlah kembali melebihi sisa ($sisa)");
        }

        $total = $sudahKembali + $input;

        $status     = 'Belum Selesai';
        $keterangan = 'Belum Kembali';
        $masukPenuh = false;

        if ($total >= $jumlahAwal) {
            $total = $jumlahAwal;
            $masukPenuh = true;
            
            if ($barang['jumlah_keluar'] >= $jumlahAwal) {
                $status = 'Selesai';
                $keterangan = date('Y-m-d H:i:s');
            } else {
                $keterangan = 'Masuk Penuh';
            }
        }

        $this->barang->update($id, [
            'jumlah_kembali' => $total,
            'masuk_penuh'    => $masukPenuh ? 1 : 0,
            'status'         => $status,
            'keterangan'     => $keterangan
        ]);

        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Kembali',
            'jumlah'     => $input,
            'sisa'       => $jumlahAwal - $total,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')
            ->with('success', 'Barang berhasil kembali');
    }

    /**
     * KEMBALI LANGSUNG
     */
    public function kembaliLangsung($id)
    {
        $barang = $this->barang->find($id);

        if (!$barang) {
            return redirect()->to('/registrasi');
        }

        if ($barang['status'] === 'Selesai') {
            return redirect()->to('/registrasi')
                ->with('error', 'Barang sudah selesai');
        }

        if ($barang['akan_kembali'] !== 'Ya') {
            return redirect()->to('/registrasi')
                ->with('error', 'Barang tidak direncanakan untuk kembali');
        }

        $jumlah = (int) $barang['jumlah'];

        $this->barang->update($id, [
            'jumlah_kembali' => $jumlah,
            'masuk_penuh'    => 1,
            'status'         => $barang['jumlah_keluar'] >= $jumlah ? 'Selesai' : 'Belum Selesai',
            'keterangan'     => $barang['jumlah_keluar'] >= $jumlah ? date('Y-m-d H:i:s') : 'Masuk Penuh'
        ]);

        $sisaSebelum = $jumlah - $barang['jumlah_kembali'];
        
        $this->barangLog->insert([
            'barang_id'  => $id,
            'aksi'       => 'Kembali',
            'jumlah'     => $sisaSebelum,
            'sisa'       => 0,
            'keterangan' => 'Kembali Penuh',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')
            ->with('success', 'Barang berhasil kembali');
    }

    // ============================================
    // FUNGSI LAPTOP
    // ============================================

    /**
     * STORE LAPTOP - dengan nomor registrasi otomatis
     */
    /**
 * STORE LAPTOP - dengan nomor registrasi otomatis
 */
public function storeLaptop()
{
    $nomorSeri = $this->request->getPost('nomor_seri');
    $jenis = $this->request->getPost('jenis');
    
    // Validasi nomor seri - cek apakah sudah ada di database
    $existing = $this->laptop->where('nomor_seri', $nomorSeri)->first();
    if ($existing) {
        return redirect()->back()->withInput()->with('error', 'Nomor Seri ' . $nomorSeri . ' sudah terdaftar! Setiap laptop harus memiliki nomor seri unik.');
    }

    // Validasi input lainnya
    $rules = [
        'jenis'           => 'required|in_list[Pegawai,Non Pegawai]',
        'nama_pengguna'   => 'required|min_length[3]|max_length[255]',
        'nomor_id_card'   => 'required|min_length[3]|max_length[50]|is_unique[laptop.nomor_id_card]',
        'instansi_divisi' => 'required|max_length[255]',
        'merek'           => 'required|max_length[100]',
        'nomor_seri'      => 'required|max_length[100]', // Hapus is_unique karena kita validasi manual
        'berlaku_sampai'  => 'required|valid_date'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    // Generate nomor registrasi otomatis
    $noRegistrasi = $this->generateNoRegistrasi($jenis);

    $data = [
        'no_registrasi'    => $noRegistrasi,
        'jenis'            => $jenis,
        'nama_pengguna'    => $this->request->getPost('nama_pengguna'),
        'nomor_id_card'    => $this->request->getPost('nomor_id_card'),
        'instansi_divisi'  => $this->request->getPost('instansi_divisi'),
        'merek'            => $this->request->getPost('merek'),
        'tipe_laptop'      => $this->request->getPost('tipe_laptop'),
        'nomor_seri'       => $nomorSeri,
        'berlaku_sampai'   => $this->request->getPost('berlaku_sampai'),
        'spesifikasi_lain' => $this->request->getPost('spesifikasi_lain'),
        'status'           => 'Masih Berlaku',
        'keterangan'       => 'Registrasi baru',
        'registrasi_ke'    => 1
    ];

    $id = $this->laptop->insert($data);

    if (!$id) {
        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $this->laptop->errors());
    }

    // Catat log registrasi
    $this->laptopLog->insert([
        'laptop_id'     => $id,
        'no_registrasi' => $noRegistrasi,
        'registrasi_ke' => 1,
        'aksi'          => 'Registrasi',
        'keterangan'    => 'Registrasi laptop baru - ' . $data['merek'] . ' ' . $data['tipe_laptop'] . ' (Jenis: ' . $jenis . ')',
        'created_at'    => date('Y-m-d H:i:s')
    ]);

    return redirect()->to('/registrasi')->with('success', 'Laptop berhasil diregistrasi. Nomor Registrasi: ' . $noRegistrasi . ' (Registrasi ke-1)');
}

    /**
 * UPDATE LAPTOP
 */
public function updateLaptop($id)
{
    $laptop = $this->laptop->find($id);
    
    if (!$laptop) {
        return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
    }

    $nomorSeri = $this->request->getPost('nomor_seri');
    $jenis = $this->request->getPost('jenis');
    $nomorIdCard = $this->request->getPost('nomor_id_card');
    
    // Validasi nomor seri unik (kecuali untuk laptop ini sendiri)
    $existing = $this->laptop->where('nomor_seri', $nomorSeri)->where('id !=', $id)->first();
    if ($existing) {
        return redirect()->back()->withInput()->with('error', 'Nomor Seri ' . $nomorSeri . ' sudah digunakan laptop lain!');
    }

    // Validasi nomor ID card unik (kecuali untuk laptop ini sendiri)
    $existingIdCard = $this->laptop->where('nomor_id_card', $nomorIdCard)->where('id !=', $id)->first();
    if ($existingIdCard) {
        return redirect()->back()->withInput()->with('error', 'Nomor ID Card ' . $nomorIdCard . ' sudah digunakan laptop lain!');
    }

    $rules = [
        'jenis'           => 'required|in_list[Pegawai,Non Pegawai]',
        'nama_pengguna'   => 'required|min_length[3]|max_length[255]',
        'nomor_id_card'   => 'required|min_length[3]|max_length[50]',
        'instansi_divisi' => 'required|max_length[255]',
        'merek'           => 'required|max_length[100]',
        'nomor_seri'      => 'required|max_length[100]',
        'berlaku_sampai'  => 'required|valid_date'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $data = [
        'jenis'            => $jenis,
        'nama_pengguna'    => $this->request->getPost('nama_pengguna'),
        'nomor_id_card'    => $nomorIdCard,
        'instansi_divisi'  => $this->request->getPost('instansi_divisi'),
        'merek'            => $this->request->getPost('merek'),
        'tipe_laptop'      => $this->request->getPost('tipe_laptop'),
        'nomor_seri'       => $nomorSeri,
        'berlaku_sampai'   => $this->request->getPost('berlaku_sampai'),
        'spesifikasi_lain' => $this->request->getPost('spesifikasi_lain'),
        'status'           => $this->request->getPost('status') ?: 'Masih Berlaku',
        'keterangan'       => $this->request->getPost('keterangan')
    ];

    $updated = $this->laptop->update($id, $data);
    
    if (!$updated) {
        return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data: ' . $this->laptop->errors());
    }

    // Catat log perubahan
    $this->laptopLog->insert([
        'laptop_id'     => $id,
        'no_registrasi' => $laptop['no_registrasi'],
        'registrasi_ke' => $laptop['registrasi_ke'] ?? 1,
        'aksi'          => 'Update',
        'keterangan'    => 'Data laptop diperbarui - Jenis: ' . $jenis,
        'created_at'    => date('Y-m-d H:i:s')
    ]);

    return redirect()->to('/registrasi')->with('success', 'Data laptop berhasil diperbarui');
}
/**
 * Cek apakah nomor seri sudah ada (untuk AJAX)
 */
public function cekNomorSeri()
{
    $nomorSeri = $this->request->getGet('nomor_seri');
    $id = $this->request->getGet('id'); // untuk edit, kecualikan ID ini
    
    $builder = $this->laptop->where('nomor_seri', $nomorSeri);
    
    if ($id) {
        $builder->where('id !=', $id);
    }
    
    $exists = $builder->first();
    
    return $this->response->setJSON([
        'exists' => $exists ? true : false,
        'message' => $exists ? 'Nomor seri sudah digunakan' : 'Nomor seri tersedia'
    ]);
}
/**
 * PERPANJANG LAPTOP - dengan nomor registrasi baru
 */
/**
 * PERPANJANG LAPTOP - menyimpan riwayat dalam JSON
 */
public function perpanjangLaptop()
{
    $id = $this->request->getPost('laptop_id');
    $berlakuSampaiBaru = $this->request->getPost('berlaku_sampai_baru');
    $keterangan = $this->request->getPost('keterangan');
    
    $laptop = $this->laptop->find($id);
    
    if (!$laptop) {
        return redirect()->back()->with('error', 'Data laptop tidak ditemukan');
    }
    
    // Ambil riwayat perpanjangan yang sudah ada
    $riwayat = json_decode($laptop['riwayat_perpanjangan'] ?? '[]', true);
    
    // Tambahkan riwayat baru
    $riwayat[] = [
        'no_registrasi' => $laptop['no_registrasi'],
        'tanggal_perpanjangan' => date('Y-m-d H:i:s'),
        'berlaku_sampai_lama' => $laptop['berlaku_sampai'],
        'berlaku_sampai_baru' => $berlakuSampaiBaru,
        'keterangan' => $keterangan,
        'registrasi_ke' => count($riwayat) + 1
    ];
    
    // Generate nomor registrasi baru
    $noRegistrasiBaru = $this->generateNoRegistrasi($laptop['jenis']);
    
    // Update laptop yang sama (bukan buat baru)
    $this->laptop->update($id, [
        'no_registrasi' => $noRegistrasiBaru,
        'berlaku_sampai' => $berlakuSampaiBaru,
        'riwayat_perpanjangan' => json_encode($riwayat),
        'keterangan' => 'Diperpanjang - ' . $keterangan
    ]);
    
    // Catat log
    $this->laptopLog->insert([
        'laptop_id' => $id,
        'no_registrasi' => $noRegistrasiBaru,
        'registrasi_ke' => count($riwayat),
        'aksi' => 'Perpanjangan',
        'keterangan' => $keterangan ?: 'Perpanjangan ke-' . count($riwayat),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    return redirect()->back()->with('success', 'Masa berlaku laptop berhasil diperpanjang. Nomor Registrasi baru: ' . $noRegistrasiBaru);
}
    /**
     * DETAIL LAPTOP dengan logs dan riwayat perpanjangan
     */
    public function detailLaptop($id)
    {
        $laptop = $this->laptop->find($id);
        
        if (!$laptop) {
            return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
        }

        // Ambil logs
        $logs = $this->laptopLog->where('laptop_id', $id)
                                ->orderBy('created_at', 'DESC')
                                ->findAll();
        
        // Ambil riwayat semua registrasi berdasarkan nomor seri
        $riwayatRegistrasi = $this->laptop
            ->where('nomor_seri', $laptop['nomor_seri'])
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('laptop/detail', [
            'laptop' => $laptop,
            'logs' => $logs,
            'riwayatRegistrasi' => $riwayatRegistrasi
        ]);
    }

    /**
     * SEARCH LAPTOP - AJAX
     */
    public function searchLaptop()
    {
        $keyword = $this->request->getGet('keyword');
        $status = $this->request->getGet('status');

        $builder = $this->laptop;

        if ($keyword) {
            $builder = $builder->groupStart()
                ->like('nama_pengguna', $keyword)
                ->orLike('nomor_id_card', $keyword)
                ->orLike('instansi_divisi', $keyword)
                ->orLike('merek', $keyword)
                ->orLike('tipe_laptop', $keyword)
                ->orLike('nomor_seri', $keyword)
                ->orLike('spesifikasi_lain', $keyword)
                ->orLike('no_registrasi', $keyword)
                ->groupEnd();
        }

        if ($status && $status != 'Semua' && $status != '') {
            $builder = $builder->where('status', $status);
        }

        $laptops = $builder->orderBy('created_at', 'DESC')->findAll();

        // Ambil logs untuk setiap laptop
        foreach ($laptops as &$laptop) {
            $laptop['logs'] = $this->laptopLog->where('laptop_id', $laptop['id'])
                                              ->orderBy('created_at', 'DESC')
                                              ->findAll();
        }

        $data['laptops'] = $laptops;

        // Kembalikan hanya partial view untuk tabel
        return view('laptop/table', $data);
    }

    /**
     * DELETE LAPTOP
     */
    public function deleteLaptop($id)
    {
        $laptop = $this->laptop->find($id);
        
        if (!$laptop) {
            return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
        }

        // Catat log sebelum dihapus
        $this->laptopLog->insert([
            'laptop_id'     => $id,
            'no_registrasi' => $laptop['no_registrasi'],
            'registrasi_ke' => $laptop['registrasi_ke'] ?? 1,
            'aksi'          => 'Hapus',
            'keterangan'    => 'Laptop dihapus dari sistem',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        $this->laptop->delete($id);

        return redirect()->to('/registrasi')->with('success', 'Laptop berhasil dihapus');
    }

    /**
     * CHANGE LAPTOP STATUS
     */
    public function changeLaptopStatus($id)
    {
        $laptop = $this->laptop->find($id);
        
        if (!$laptop) {
            return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
        }

        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');

        $this->laptop->update($id, [
            'status'     => $status,
            'keterangan' => $keterangan ?: 'Status diubah menjadi ' . $status
        ]);

        $this->laptopLog->insert([
            'laptop_id'     => $id,
            'no_registrasi' => $laptop['no_registrasi'],
            'registrasi_ke' => $laptop['registrasi_ke'] ?? 1,
            'aksi'          => 'Ubah Status',
            'keterangan'    => "Status diubah menjadi {$status}" . ($keterangan ? ": {$keterangan}" : ''),
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Status laptop berhasil diubah');
    }

    /**
     * EXPORT LAPTOP TO EXCEL
     */
    public function exportLaptop()
    {
        $laptops = $this->laptop->orderBy('created_at', 'DESC')->findAll();
        $totalData = count($laptops);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Data Laptop');
        
        // Header dengan REGISTRASI KE
        $headers = [
            'NO', 
            'NO. REGISTRASI', 
            'REGISTRASI KE', 
            'JENIS', 
            'NAMA PENGGUNA', 
            'NOMOR ID CARD', 
            'INSTANSI/DIVISI', 
            'MEREK', 
            'TIPE', 
            'NOMOR SERI', 
            'BERLAKU SAMPAI', 
            'SPESIFIKASI', 
            'STATUS', 
            'KETERANGAN', 
            'TANGGAL REGISTRASI'
        ];
        
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index); // A, B, C, ...
            $sheet->setCellValue($col . '1', $header);
        }
        
        // Style header
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->getStyle('A1:O1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0066B3');
        $sheet->getStyle('A1:O1')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        // Data
        $row = 2;
        foreach ($laptops as $i => $l) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $l['no_registrasi']);
            $sheet->setCellValue('C' . $row, $l['registrasi_ke'] ?? 1);
            $sheet->setCellValue('D' . $row, $l['jenis'] ?? '-');
            $sheet->setCellValue('E' . $row, $l['nama_pengguna']);
            $sheet->setCellValue('F' . $row, $l['nomor_id_card']);
            $sheet->setCellValue('G' . $row, $l['instansi_divisi']);
            $sheet->setCellValue('H' . $row, $l['merek']);
            $sheet->setCellValue('I' . $row, $l['tipe_laptop']);
            $sheet->setCellValue('J' . $row, $l['nomor_seri']);
            $sheet->setCellValue('K' . $row, date('d-m-Y', strtotime($l['berlaku_sampai'])));
            $sheet->setCellValue('L' . $row, $l['spesifikasi_lain']);
            $sheet->setCellValue('M' . $row, $l['status']);
            $sheet->setCellValue('N' . $row, $l['keterangan']);
            $sheet->setCellValue('O' . $row, date('d-m-Y H:i', strtotime($l['created_at'])));
            
            // Warna status
            if ($l['status'] == 'Masih Berlaku') {
                $sheet->getStyle('M' . $row)->getFont()->getColor()->setARGB('FF008000');
            } else if ($l['status'] == 'Tidak Berlaku') {
                $sheet->getStyle('M' . $row)->getFont()->getColor()->setARGB('FFFF0000');
            } else if ($l['status'] == 'Diperpanjang') {
                $sheet->getStyle('M' . $row)->getFont()->getColor()->setARGB('FF0000FF');
            }
            
            $row++;
        }
        
        // Statistik
        $lastRow = $row + 2;
        $sheet->setCellValue('A' . $lastRow, 'STATISTIK DATA:');
        $sheet->getStyle('A' . $lastRow)->getFont()->setBold(true);
        
        $lastRow++;
        $sheet->setCellValue('A' . $lastRow, 'Total Laptop:');
        $sheet->setCellValue('B' . $lastRow, $totalData);
        
        $lastRow++;
        $pegawai = $this->laptop->where('jenis', 'Pegawai')->countAllResults();
        $nonPegawai = $this->laptop->where('jenis', 'Non Pegawai')->countAllResults();
        $sheet->setCellValue('A' . $lastRow, 'Jenis Pegawai:');
        $sheet->setCellValue('B' . $lastRow, $pegawai);
        
        $lastRow++;
        $sheet->setCellValue('A' . $lastRow, 'Jenis Non Pegawai:');
        $sheet->setCellValue('B' . $lastRow, $nonPegawai);
        
        $lastRow++;
        $aktif = $this->laptop->where('status', 'Masih Berlaku')->countAllResults();
        $nonaktif = $this->laptop->where('status', 'Tidak Berlaku')->countAllResults();
        $diperpanjang = $this->laptop->where('status', 'Diperpanjang')->countAllResults();
        $sheet->setCellValue('A' . $lastRow, 'Status Masih Berlaku:');
        $sheet->setCellValue('B' . $lastRow, $aktif);
        $sheet->getStyle('A' . $lastRow)->getFont()->getColor()->setARGB('FF008000');
        
        $lastRow++;
        $sheet->setCellValue('A' . $lastRow, 'Status Tidak Berlaku:');
        $sheet->setCellValue('B' . $lastRow, $nonaktif);
        $sheet->getStyle('A' . $lastRow)->getFont()->getColor()->setARGB('FFFF0000');
        
        $lastRow++;
        $sheet->setCellValue('A' . $lastRow, 'Status Diperpanjang:');
        $sheet->setCellValue('B' . $lastRow, $diperpanjang);
        $sheet->getStyle('A' . $lastRow)->getFont()->getColor()->setARGB('FF0000FF');
        
        $lastRow++;
        $sheet->setCellValue('A' . $lastRow, 'Tanggal Export:');
        $sheet->setCellValue('B' . $lastRow, date('d-m-Y H:i:s'));
        
        // Auto size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Border
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:O' . ($row - 1))->applyFromArray($styleArray);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'data_laptop_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    // ============================================
    // FUNGSI LOGS
    // ============================================

    /**
     * LOGS
     */
    public function logs()
    {
        $keyword = $this->request->getGet('keyword');
        $aksi = $this->request->getGet('aksi');
        $type = $this->request->getGet('type');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $this->barangLog->select('barang_logs.*, 
                                      barang.nama_barang, barang.no_agenda, barang.satuan as barang_satuan,
                                      laptop.nama_pengguna, laptop.merek, laptop.tipe_laptop, laptop.nomor_seri, laptop.no_registrasi, laptop.jenis, laptop.registrasi_ke')
                            ->join('barang', 'barang.id = barang_logs.barang_id', 'left')
                            ->join('laptop', 'laptop.id = barang_logs.laptop_id', 'left');

        if ($keyword) {
            $builder = $builder->groupStart()
                ->like('barang.nama_barang', $keyword)
                ->orLike('barang.no_agenda', $keyword)
                ->orLike('laptop.nama_pengguna', $keyword)
                ->orLike('laptop.merek', $keyword)
                ->orLike('laptop.nomor_seri', $keyword)
                ->orLike('laptop.no_registrasi', $keyword)
                ->orLike('barang_logs.keterangan', $keyword)
                ->groupEnd();
        }

        if ($aksi && $aksi != 'Semua') {
            $builder = $builder->where('barang_logs.aksi', $aksi);
        }

        if ($type && $type != 'Semua') {
            if ($type == 'barang') {
                $builder = $builder->where('barang_logs.barang_id IS NOT NULL');
            } else if ($type == 'laptop') {
                $builder = $builder->where('barang_logs.laptop_id IS NOT NULL');
            }
        }

        if ($startDate && $endDate) {
            $builder = $builder->where('barang_logs.created_at >=', $startDate . ' 00:00:00')
                               ->where('barang_logs.created_at <=', $endDate . ' 23:59:59');
        }

        $data['logs'] = $builder->orderBy('barang_logs.created_at', 'DESC')
                                ->paginate(50);
        
        $data['pager'] = $this->barangLog->pager;
        $data['keyword'] = $keyword;
        $data['aksi'] = $aksi;
        $data['type'] = $type;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        
        $data['actions'] = $this->barangLog->distinct()->select('aksi')->findAll();

        return view('logs/index', $data);
    }

    /**
     * EXPORT LOGS TO CSV
     */
    public function exportLogs()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        $logs = $this->barangLog->select('barang_logs.*, 
                                   barang.nama_barang, barang.no_agenda,
                                   laptop.nama_pengguna, laptop.merek, laptop.tipe_laptop, laptop.nomor_seri, laptop.no_registrasi, laptop.jenis, laptop.registrasi_ke')
                         ->join('barang', 'barang.id = barang_logs.barang_id', 'left')
                         ->join('laptop', 'laptop.id = barang_logs.laptop_id', 'left')
                         ->where('barang_logs.created_at >=', $startDate . ' 00:00:00')
                         ->where('barang_logs.created_at <=', $endDate . ' 23:59:59')
                         ->orderBy('barang_logs.created_at', 'DESC')
                         ->findAll();

        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="logs_' . date('Ymd') . '.csv"');

        $output = fopen('php://output', 'w');
        
        fputcsv($output, [
            'No', 'Tanggal', 'Tipe', 'No Registrasi', 'Registrasi Ke', 'Item', 'Aksi', 
            'Jumlah', 'Sisa', 'Keterangan'
        ]);

        foreach ($logs as $i => $log) {
            $tipe = $log['barang_id'] ? 'Barang' : ($log['laptop_id'] ? 'Laptop' : '-');
            $noRegistrasi = $log['laptop_id'] ? $log['no_registrasi'] : $log['no_agenda'];
            $registrasiKe = $log['laptop_id'] ? ($log['registrasi_ke'] ?? 1) : '-';
            $item = $log['barang_id'] 
                ? $log['nama_barang'] . ' (' . $log['no_agenda'] . ')'
                : ($log['laptop_id'] 
                    ? $log['nama_pengguna'] . ' - ' . $log['merek'] . ' ' . $log['tipe_laptop'] . ' (' . $log['nomor_seri'] . ') - ' . $log['jenis']
                    : '-');

            fputcsv($output, [
                $i + 1,
                date('d/m/Y H:i:s', strtotime($log['created_at'])),
                $tipe,
                $noRegistrasi,
                $registrasiKe,
                $item,
                $log['aksi'],
                $log['jumlah'],
                $log['sisa'],
                $log['keterangan']
            ]);
        }

        fclose($output);
        exit;
    }

    // ============================================
    // FUNGSI DASHBOARD
    // ============================================

    /**
     * DASHBOARD
     */
    public function dashboard()
    {
        $data = [
            'total_barang' => $this->barang->countAll(),
            'total_laptop' => $this->laptop->countAll(),
            'barang_selesai' => $this->barang->where('status', 'Selesai')->countAllResults(),
            'barang_proses' => $this->barang->where('status', 'Belum Selesai')->countAllResults(),
            'laptop_aktif' => $this->laptop->where('status', 'Masih Berlaku')->countAllResults(),
            'laptop_nonaktif' => $this->laptop->where('status', 'Tidak Berlaku')->countAllResults(),
            'laptop_diperpanjang' => $this->laptop->where('status', 'Diperpanjang')->countAllResults(),
            'laptop_pegawai' => $this->laptop->where('jenis', 'Pegawai')->countAllResults(),
            'laptop_nonpegawai' => $this->laptop->where('jenis', 'Non Pegawai')->countAllResults(),
            'recent_logs' => $this->barangLog->orderBy('created_at', 'DESC')->limit(10)->findAll(),
            'estimasi_hari_ini' => $this->barang
                ->where('estimasi_kembali', date('Y-m-d'))
                ->where('status !=', 'Selesai')
                ->findAll()
        ];

        return view('dashboard', $data);
    }

    // ============================================
    // FUNGSI BANTUAN
    // ============================================

    /**
     * Generate nomor registrasi otomatis
     */
    /**
 * Generate nomor registrasi otomatis yang UNIK
 */
private function generateNoRegistrasi($jenis)
{
    $prefix = $jenis === 'Pegawai' ? 'PEG' : 'NPEG';
    $tahun = date('Y');
    $bulan = date('m');
    $maxAttempts = 100; // Hindari infinite loop
    $attempt = 0;
    
    do {
        // Cari nomor urut terakhir untuk tahun dan bulan ini
        $lastReg = $this->laptop
            ->select('no_registrasi')
            ->like('no_registrasi', $prefix . $tahun . $bulan, 'after')
            ->orderBy('no_registrasi', 'DESC')
            ->first();
        
        if ($lastReg) {
            // Ambil 4 digit terakhir
            $lastNumber = (int)substr($lastReg['no_registrasi'], -4);
            $newNumber = str_pad($lastNumber + 1 + $attempt, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = str_pad(1 + $attempt, 4, '0', STR_PAD_LEFT);
        }
        
        $noRegistrasi = $prefix . $tahun . $bulan . $newNumber;
        
        // Cek apakah nomor registrasi sudah ada
        $exists = $this->laptop->where('no_registrasi', $noRegistrasi)->first();
        $attempt++;
        
        if ($attempt >= $maxAttempts) {
            // Fallback: tambahkan timestamp
            $noRegistrasi = $prefix . $tahun . $bulan . date('His');
            break;
        }
        
    } while ($exists);
    
    return $noRegistrasi;
}
}