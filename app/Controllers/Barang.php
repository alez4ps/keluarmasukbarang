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

    public function index()
    {
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

        // AMBIL DATA LAPTOP
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
                ->groupEnd();
        }

        if ($statusLaptop && $statusLaptop != 'Semua') {
            $builderLaptop = $builderLaptop->where('status', $statusLaptop);
        }

        $laptops = $builderLaptop->orderBy('id', 'DESC')->findAll();

        $data['barangs'] = $barangs;
        $data['laptops'] = $laptops;
        $data['keyword'] = $keyword;
        $data['keywordLaptop'] = $keywordLaptop;
        $data['statusLaptop'] = $statusLaptop;

        $db = \Config\Database::connect();

        $q = $db->query("
            SELECT IFNULL(MAX(CAST(SUBSTRING(no_agenda,4) AS UNSIGNED)),0) + 1 AS next
            FROM barang
            WHERE no_agenda LIKE 'M-%'
              AND YEAR(tanggal) = YEAR(CURDATE())
        ");
        $data['noAgenda'] = 'M-' . str_pad($q->getRow()->next, 4, '0', STR_PAD_LEFT);

        $d = $db->query("
            SELECT IFNULL(MAX(CAST(SUBSTRING(no_agenda,4) AS UNSIGNED)),0) + 1 AS next
            FROM barang
            WHERE no_agenda LIKE 'K-%'
              AND YEAR(tanggal) = YEAR(CURDATE())
        ");
        $data['noAgendaKeluar'] = 'K-' . str_pad($d->getRow()->next, 4, '0', STR_PAD_LEFT);

        $data['tanggal'] = date('Y-m-d H:i:s');

        return view('registrasi/index', $data);
    }

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

    public function edit($id)
    {
        return view('registrasi/edit', [
            'barang' => $this->barang->find($id)
        ]);
    }

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

    public function laptop()
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
                ->groupEnd();
        }

        if ($status && $status != 'Semua') {
            $builder = $builder->where('status', $status);
        }

        $data['laptops'] = $builder->orderBy('id', 'DESC')->findAll();
        $data['keyword'] = $keyword;
        $data['status'] = $status;
        $data['status_options'] = ['Masih Berlaku', 'Tidak Berlaku'];

        return view('laptop/index', $data);
    }

    public function storeLaptop()
    {
        $nomorSeri = $this->request->getPost('nomor_seri');
        $existing = $this->laptop->where('nomor_seri', $nomorSeri)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Nomor Seri sudah terdaftar! Setiap laptop harus memiliki nomor seri unik.');
        }

        $rules = [
            'nama_pengguna'   => 'required|min_length[3]|max_length[255]',
            'nomor_id_card'   => 'required|min_length[3]|max_length[50]|is_unique[laptop.nomor_id_card]',
            'instansi_divisi' => 'required|max_length[255]',
            'merek'           => 'required|max_length[100]',
            'nomor_seri'      => 'required|max_length[100]|is_unique[laptop.nomor_seri]',
            'berlaku_sampai'  => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_pengguna'    => $this->request->getPost('nama_pengguna'),
            'nomor_id_card'    => $this->request->getPost('nomor_id_card'),
            'instansi_divisi'  => $this->request->getPost('instansi_divisi'),
            'merek'            => $this->request->getPost('merek'),
            'tipe_laptop'      => $this->request->getPost('tipe_laptop'),
            'nomor_seri'       => $this->request->getPost('nomor_seri'),
            'berlaku_sampai'   => $this->request->getPost('berlaku_sampai'),
            'spesifikasi_lain' => $this->request->getPost('spesifikasi_lain'),
            'status'           => 'Masih Berlaku',
            'keterangan'       => 'Registrasi baru'
        ];

        $id = $this->laptop->insert($data);

        $this->laptopLog->insert([
            'laptop_id'   => $id,
            'aksi'        => 'Registrasi',
            'keterangan'  => 'Registrasi laptop baru - ' . $data['merek'] . ' ' . $data['tipe_laptop'],
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Laptop berhasil diregistrasi');
    }

    public function editLaptop($id)
    {
        $laptop = $this->laptop->find($id);
        
        if (!$laptop) {
            return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
        }

        return view('laptop/edit', ['laptop' => $laptop]);
    }

    public function updateLaptop($id)
    {
        $laptop = $this->laptop->find($id);
        
        if (!$laptop) {
            return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
        }

        $nomorSeri = $this->request->getPost('nomor_seri');
        $existing = $this->laptop->where('nomor_seri', $nomorSeri)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Nomor Seri sudah digunakan laptop lain!');
        }

        $rules = [
            'nama_pengguna'   => 'required|min_length[3]|max_length[255]',
            'nomor_id_card'   => "required|min_length[3]|max_length[50]|is_unique[laptop.nomor_id_card,id,{$id}]",
            'instansi_divisi' => 'required|max_length[255]',
            'merek'           => 'required|max_length[100]',
            'nomor_seri'      => "required|max_length[100]",
            'berlaku_sampai'  => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_pengguna'    => $this->request->getPost('nama_pengguna'),
            'nomor_id_card'    => $this->request->getPost('nomor_id_card'),
            'instansi_divisi'  => $this->request->getPost('instansi_divisi'),
            'merek'            => $this->request->getPost('merek'),
            'tipe_laptop'      => $this->request->getPost('tipe_laptop'),
            'nomor_seri'       => $this->request->getPost('nomor_seri'),
            'berlaku_sampai'   => $this->request->getPost('berlaku_sampai'),
            'spesifikasi_lain' => $this->request->getPost('spesifikasi_lain'),
            'status'           => $this->request->getPost('status') ?: 'Masih Berlaku',
            'keterangan'       => $this->request->getPost('keterangan')
        ];

        $this->laptop->update($id, $data);

        $this->laptopLog->insert([
            'laptop_id'   => $id,
            'aksi'        => 'Update',
            'keterangan'  => 'Data laptop diperbarui',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Data laptop berhasil diperbarui');
    }

    public function deleteLaptop($id)
    {
        $laptop = $this->laptop->find($id);
        
        if (!$laptop) {
            return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
        }

        $this->laptopLog->insert([
            'laptop_id'   => $id,
            'aksi'        => 'Hapus',
            'keterangan'  => 'Laptop dihapus dari sistem',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        $this->laptop->delete($id);

        return redirect()->to('/registrasi')->with('success', 'Laptop berhasil dihapus');
    }

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
            'laptop_id'   => $id,
            'aksi'        => 'Ubah Status',
            'keterangan'  => "Status diubah menjadi {$status}" . ($keterangan ? ": {$keterangan}" : ''),
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Status laptop berhasil diubah');
    }

    public function detailLaptop($id)
    {
        $laptop = $this->laptop->find($id);
        
        if (!$laptop) {
            return redirect()->to('/registrasi')->with('error', 'Laptop tidak ditemukan');
        }

        // Ambil logs dari LaptopLogModel
        $logs = $this->laptopLog->where('laptop_id', $id)->orderBy('created_at', 'DESC')->findAll();

        return view('laptop/detail', [
            'laptop' => $laptop,
            'logs'   => $logs
        ]);
    }

    public function exportLaptop()
    {
        $laptops = $this->laptop->orderBy('id', 'DESC')->findAll();

        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="laptops_' . date('Ymd') . '.csv"');

        $output = fopen('php://output', 'w');
        
        fputcsv($output, [
            'No', 'Nama Pengguna', 'Nomor ID Card', 'Instansi/Divisi', 
            'Merek', 'Tipe', 'Nomor Seri', 'Berlaku Sampai', 
            'Spesifikasi', 'Status', 'Keterangan'
        ]);

        foreach ($laptops as $i => $l) {
            fputcsv($output, [
                $i + 1,
                $l['nama_pengguna'],
                $l['nomor_id_card'],
                $l['instansi_divisi'],
                $l['merek'],
                $l['tipe_laptop'],
                $l['nomor_seri'],
                $l['berlaku_sampai'],
                $l['spesifikasi_lain'],
                $l['status'],
                $l['keterangan']
            ]);
        }

        fclose($output);
        exit;
    }

    public function logs()
    {
        $keyword = $this->request->getGet('keyword');
        $aksi = $this->request->getGet('aksi');
        $type = $this->request->getGet('type');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $this->barangLog->select('barang_logs.*, 
                                      barang.nama_barang, barang.no_agenda, barang.satuan as barang_satuan,
                                      laptop.nama_pengguna, laptop.merek, laptop.tipe_laptop, laptop.nomor_seri')
                            ->join('barang', 'barang.id = barang_logs.barang_id', 'left')
                            ->join('laptop', 'laptop.id = barang_logs.laptop_id', 'left');

        if ($keyword) {
            $builder = $builder->groupStart()
                ->like('barang.nama_barang', $keyword)
                ->orLike('barang.no_agenda', $keyword)
                ->orLike('laptop.nama_pengguna', $keyword)
                ->orLike('laptop.merek', $keyword)
                ->orLike('laptop.nomor_seri', $keyword)
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

    public function exportLogs()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        $logs = $this->barangLog->select('barang_logs.*, 
                                   barang.nama_barang, barang.no_agenda,
                                   laptop.nama_pengguna, laptop.merek, laptop.tipe_laptop, laptop.nomor_seri')
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
            'No', 'Tanggal', 'Tipe', 'Item', 'Aksi', 
            'Jumlah', 'Sisa', 'Keterangan'
        ]);

        foreach ($logs as $i => $log) {
            $tipe = $log['barang_id'] ? 'Barang' : ($log['laptop_id'] ? 'Laptop' : '-');
            $item = $log['barang_id'] 
                ? $log['nama_barang'] . ' (' . $log['no_agenda'] . ')'
                : ($log['laptop_id'] 
                    ? $log['nama_pengguna'] . ' - ' . $log['merek'] . ' ' . $log['tipe_laptop'] . ' (' . $log['nomor_seri'] . ')'
                    : '-');

            fputcsv($output, [
                $i + 1,
                date('d/m/Y H:i:s', strtotime($log['created_at'])),
                $tipe,
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

    public function dashboard()
    {
        $data['total_barang'] = $this->barang->countAll();
        $data['total_laptop'] = $this->laptop->countAll();
        $data['barang_selesai'] = $this->barang->where('status', 'Selesai')->countAllResults();
        $data['barang_proses'] = $this->barang->where('status', 'Belum Selesai')->countAllResults();
        $data['laptop_aktif'] = $this->laptop->where('status', 'Masih Berlaku')->countAllResults();
        $data['laptop_nonaktif'] = $this->laptop->where('status', 'Tidak Berlaku')->countAllResults();
        
        $data['recent_logs'] = $this->barangLog->orderBy('created_at', 'DESC')->limit(10)->findAll();
        
        $data['estimasi_hari_ini'] = $this->barang
            ->where('estimasi_kembali', date('Y-m-d'))
            ->where('status !=', 'Selesai')
            ->findAll();

        return view('dashboard', $data);
    }
}