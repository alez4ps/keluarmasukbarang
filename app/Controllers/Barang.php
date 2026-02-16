<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\BarangLogModel;

class Barang extends BaseController
{
    protected $barang;
    protected $log;

    public function __construct()
    {
        $this->barang = new BarangModel();
        $this->log    = new BarangLogModel();
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
            $logData = $this->log
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

        $data['barangs'] = $barangs;
        $data['keyword'] = $keyword;

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
            'jumlah_kembali'  => $jumlahMasuk,  // Hanya untuk barang masuk
            'jumlah_keluar'   => $jumlahKeluar, // Hanya untuk barang keluar
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

        $this->log->insert([
            'barang_id'  => $id,
            'aksi'       => 'Registrasi',
            'jumlah'     => $data['jumlah'],
            'sisa'       => $data['jumlah'],
            'keterangan' => 'Registrasi Awal',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($jumlahMasuk > 0) {
            $this->log->insert([
                'barang_id'  => $id,
                'aksi'       => 'Masuk',
                'jumlah'     => $jumlahMasuk,
                'sisa'       => $data['jumlah'] - $jumlahMasuk,
                'keterangan' => 'Masuk Awal',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        if ($jumlahKeluar > 0) {
            $this->log->insert([
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
            'akan_kembali' => $this->request->getPost('akan_kembali'),
            'estimasi_kembali' => $this->request->getPost('estimasi_kembali')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Data diperbarui');
    }

    public function selesai($id)
    {
        $barang = $this->barang->find($id);
        if (!$barang || $barang['status'] === 'Selesai') {
            return redirect()->to('/registrasi');
        }

        $this->barang->update($id, [
            'status' => 'Selesai',
            'keterangan' => date('Y-m-d H:i:s')
        ]);

        $this->log->insert([
            'barang_id'  => $id,
            'aksi'       => 'Selesai',
            'jumlah'     => 0,
            'sisa'       => max(0, $barang['jumlah'] - $barang['jumlah_kembali']),
            'keterangan' => 'Barang telah selesai',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Status berhasil diubah menjadi Selesai');
    }

    public function masuk($id)
    {
        return view('registrasi/masuk', [
            'barang' => $this->barang->find($id)
        ]);
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

        $this->log->insert([
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

    public function masukTidakKembali($id)
    {
        $barang = $this->barang->find($id);

        if (!$barang) {
            return redirect()->to('/registrasi')->with('error', 'Barang tidak ditemukan');
        }

        return view('registrasi/masuk_tidak_kembali', ['barang' => $barang]);
    }

    public function prosesMasukTidakKembali($id)
    {
        $barang = $this->barang->find($id);
        $masuk  = (int) $this->request->getPost('jumlah_masuk');

        if ($masuk <= 0) {
            return redirect()->back()->with('error', 'Jumlah tidak valid');
        }

        $totalMasuk = $barang['jumlah_kembali'] + $masuk;

        if ($totalMasuk > $barang['jumlah']) {
            return redirect()->back()->with('error', 'Jumlah masuk melebihi total');
        }

        $masukPenuh = $totalMasuk == $barang['jumlah'];
        $status = $masukPenuh ? 'Selesai' : 'Belum Selesai';
        $keterangan = $masukPenuh ? date('Y-m-d H:i:s') : 'Masuk Sebagian';

        $update = [
            'jumlah_kembali' => $totalMasuk,
            'masuk_penuh'    => $masukPenuh ? 1 : 0,
            'status'         => $status,
            'keterangan'     => $keterangan
        ];

        $this->barang->update($id, $update);

        $this->log->insert([
            'barang_id'  => $id,
            'aksi'       => 'Masuk',
            'jumlah'     => $masuk,
            'sisa'       => $barang['jumlah'] - $totalMasuk,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/registrasi')->with('success', 'Barang berhasil masuk');
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

        $this->log->insert([
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

        $this->log->insert([
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

        $this->log->insert([
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

    public function kembali($id)
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

        return view('registrasi/kembali', [
            'barang' => $barang
        ]);
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

        $this->log->insert([
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

    public function keluar($id)
    {
        $barang = $this->barang->find($id);

        if ($barang['is_partial'] !== 'Ya') {
            return redirect()->to('/registrasi')->with('error', 'Barang tidak partial');
        }

        return view('registrasi/keluar', ['barang' => $barang]);
    }

    public function kembaliLangsung($id)
    {
        $barang = $this->barang->find($id);

        if (!$barang) {
            return redirect()->to('/registrasi');
        }

        // Kalau sudah selesai, stop
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
        
        $this->log->insert([
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
}