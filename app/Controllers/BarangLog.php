<?php

namespace App\Controllers;

use App\Models\BarangLogModel;
use App\Models\BarangModel;
use App\Models\LaptopLogModel;
use App\Models\LaptopModel;

class BarangLog extends BaseController
{
    protected $barangLog;
    protected $barang;
    protected $laptopLog;
    protected $laptop;

    public function __construct()
    {
        $this->barangLog = new BarangLogModel();
        $this->barang = new BarangModel();
        $this->laptopLog = new LaptopLogModel();
        $this->laptop = new LaptopModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // Get parameters
        $logType = $this->request->getGet('log_type') ?? 'barang';
        $keyword = $this->request->getGet('keyword') ?? '';
        $startDate = $this->request->getGet('start_date') ?? '';
        $endDate = $this->request->getGet('end_date') ?? '';
        
        // Parameters for barang logs
        $activeTab = $this->request->getGet('tab') ?? 'semua';
        
        // Parameters for laptop logs
        $laptopActiveTab = $this->request->getGet('laptop_tab') ?? 'semua';

        $data = [
            'logType' => $logType,
            'keyword' => $keyword,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'activeTab' => $activeTab,
            'laptopActiveTab' => $laptopActiveTab
        ];

        // Get BARANG logs data
        $barangData = $this->getBarangLogsData($db, $keyword, $startDate, $endDate, $activeTab);
        $data = array_merge($data, $barangData);

        // Get LAPTOP logs data
        $laptopData = $this->getLaptopLogsData($db, $keyword, $startDate, $endDate, $laptopActiveTab);
        $data = array_merge($data, $laptopData);

        return view('logs/index', $data);
    }

    /**
     * BARANG LOGS SECTION
     */
    private function getBarangLogsData($db, $keyword, $startDate, $endDate, $activeTab)
    {
        // Hitung total semua logs barang
        $totalAllBarang = $this->barangLog->countAllResults();
        
        // Hitung total masuk barang (Masuk + Kembali)
        $totalMasukBarang = $this->barangLog->whereIn('aksi', ['Masuk', 'Kembali'])->countAllResults();
        
        // Hitung total keluar barang
        $totalKeluarBarang = $this->barangLog->where('aksi', 'Keluar')->countAllResults();
        
        // Hitung barang aktif (belum selesai)
        $barangAktif = $this->barang->where('status !=', 'Selesai')->countAllResults();

        // Base query builder untuk SEMUA LOGS BARANG
        $builderSemua = $this->getBarangBaseQuery($db, $keyword, $startDate, $endDate);
        $logsSemuaBarang = $builderSemua
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Query untuk LOGS MASUK BARANG (Masuk + Kembali)
        $builderMasuk = $this->getBarangBaseQuery($db, $keyword, $startDate, $endDate);
        $builderMasuk->whereIn('l.aksi', ['Masuk', 'Kembali']);
        $logsMasukBarang = $builderMasuk
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Query untuk LOGS KELUAR BARANG
        $builderKeluar = $this->getBarangBaseQuery($db, $keyword, $startDate, $endDate);
        $builderKeluar->where('l.aksi', 'Keluar');
        $logsKeluarBarang = $builderKeluar
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $totalRowsBarang = count($logsSemuaBarang);

        return [
            'logsSemuaBarang'   => $logsSemuaBarang,
            'logsMasukBarang'   => $logsMasukBarang,
            'logsKeluarBarang'  => $logsKeluarBarang,
            'totalAllBarang'    => $totalAllBarang,
            'totalMasukBarang'  => $totalMasukBarang,
            'totalKeluarBarang' => $totalKeluarBarang,
            'barangAktif'       => $barangAktif,
            'totalRowsBarang'   => $totalRowsBarang
        ];
    }

    private function getBarangBaseQuery($db, $keyword, $startDate, $endDate)
    {
        $builder = $db->table('barang_logs l')
            ->select([
                'l.id AS log_id',
                'l.created_at',
                'l.aksi',
                'l.jumlah',
                'l.sisa',
                'l.keterangan AS log_keterangan',

                'b.id AS barang_id',
                'b.no_agenda',
                'b.no_spb',
                'b.tanggal AS barang_tanggal',
                'b.nama_barang',
                'b.jumlah AS barang_jumlah',
                'b.satuan',
                'b.asal',
                'b.tujuan',
                'b.tipe',
                'b.is_partial',
                'b.status',
                'b.jumlah_kembali',
                'b.jumlah_keluar'
            ])
            ->join('barang b', 'b.id = l.barang_id');

        if ($startDate) {
            $builder->where('DATE(l.created_at) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(l.created_at) <=', $endDate);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('b.no_agenda', $keyword)
                ->orLike('b.no_spb', $keyword)
                ->orLike('b.nama_barang', $keyword)
                ->orLike('l.keterangan', $keyword)
                ->orLike('b.asal', $keyword)
                ->orLike('b.tujuan', $keyword)
                ->orLike('l.aksi', $keyword)
            ->groupEnd();
        }

        return $builder;
    }

    /**
     * LAPTOP LOGS SECTION - DENGAN REGISTRASI_KE
     */
    private function getLaptopLogsData($db, $keyword, $startDate, $endDate, $laptopActiveTab)
    {
        // Hitung total semua logs laptop
        $totalAllLaptop = $this->laptopLog->countAllResults();
        
        // Hitung total registrasi laptop
        $totalRegistrasiLaptop = $this->laptopLog->where('aksi', 'Registrasi')->countAllResults();
        
        // Hitung total perpanjangan laptop
        $totalPerpanjanganLaptop = $this->laptopLog->where('aksi', 'Perpanjangan')->countAllResults();
        
        // Hitung total update laptop
        $totalUpdateLaptop = $this->laptopLog->where('aksi', 'Update')->countAllResults();
        
        // Hitung laptop aktif (status Masih Berlaku)
        $laptopAktif = $this->laptop->where('status', 'Masih Berlaku')->countAllResults();

        // Base query builder untuk SEMUA LOGS LAPTOP
        $builderSemua = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $logsSemuaLaptop = $builderSemua
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Query untuk LOGS REGISTRASI LAPTOP
        $builderRegistrasi = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $builderRegistrasi->where('l.aksi', 'Registrasi');
        $logsRegistrasiLaptop = $builderRegistrasi
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Query untuk LOGS PERPANJANGAN LAPTOP
        $builderPerpanjangan = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $builderPerpanjangan->where('l.aksi', 'Perpanjangan');
        $logsPerpanjanganLaptop = $builderPerpanjangan
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Query untuk LOGS UPDATE LAPTOP
        $builderUpdate = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $builderUpdate->where('l.aksi', 'Update');
        $logsUpdateLaptop = $builderUpdate
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $totalRowsLaptop = count($logsSemuaLaptop);

        return [
            'logsSemuaLaptop'        => $logsSemuaLaptop,
            'logsRegistrasiLaptop'   => $logsRegistrasiLaptop,
            'logsPerpanjanganLaptop' => $logsPerpanjanganLaptop,
            'logsUpdateLaptop'       => $logsUpdateLaptop,
            'totalAllLaptop'         => $totalAllLaptop,
            'totalRegistrasiLaptop'  => $totalRegistrasiLaptop,
            'totalPerpanjanganLaptop'=> $totalPerpanjanganLaptop,
            'totalUpdateLaptop'      => $totalUpdateLaptop,
            'laptopAktif'            => $laptopAktif,
            'totalRowsLaptop'        => $totalRowsLaptop
        ];
    }

    private function getLaptopBaseQuery($db, $keyword, $startDate, $endDate)
    {
        $builder = $db->table('laptop_logs l')
            ->select([
                'l.id AS log_id',
                'l.created_at',
                'l.aksi',
                'l.no_registrasi',
                'l.registrasi_ke',
                'l.keterangan AS log_keterangan',

                'lp.id AS laptop_id',
                'lp.no_registrasi AS laptop_no_registrasi',
                'lp.nama_pengguna',
                'lp.nomor_id_card',
                'lp.instansi_divisi',
                'lp.merek',
                'lp.tipe_laptop',
                'lp.nomor_seri',
                'lp.berlaku_sampai',
                'lp.spesifikasi_lain',
                'lp.status AS laptop_status',
                'lp.jenis',
                'lp.registrasi_ke AS laptop_registrasi_ke',
                'lp.created_at AS laptop_created_at',
                'lp.updated_at AS laptop_updated_at'
            ])
            ->join('laptop lp', 'lp.id = l.laptop_id');

        if ($startDate) {
            $builder->where('DATE(l.created_at) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(l.created_at) <=', $endDate);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('lp.nama_pengguna', $keyword)
                ->orLike('lp.nomor_id_card', $keyword)
                ->orLike('lp.instansi_divisi', $keyword)
                ->orLike('lp.merek', $keyword)
                ->orLike('lp.tipe_laptop', $keyword)
                ->orLike('lp.nomor_seri', $keyword)
                ->orLike('lp.no_registrasi', $keyword)
                ->orLike('l.no_registrasi', $keyword)
                ->orLike('lp.jenis', $keyword)
                ->orLike('l.keterangan', $keyword)
                ->orLike('l.aksi', $keyword)
            ->groupEnd();
        }

        return $builder;
    }

    /**
     * EXPORT METHODS
     */
    public function export()
    {
        $db = \Config\Database::connect();

        $logType = $this->request->getGet('log_type') ?? 'barang';
        $keyword = $this->request->getGet('keyword') ?? '';
        $startDate = $this->request->getGet('start_date') ?? '';
        $endDate = $this->request->getGet('end_date') ?? '';

        if ($logType == 'laptop') {
            return $this->exportLaptopLogs($db, $keyword, $startDate, $endDate);
        } else {
            return $this->exportBarangLogs($db, $keyword, $startDate, $endDate);
        }
    }

    private function exportBarangLogs($db, $keyword, $startDate, $endDate)
    {
        $tab = $this->request->getGet('tab') ?? 'semua';

        // Query untuk semua logs barang
        $builderSemua = $this->getBarangBaseQuery($db, $keyword, $startDate, $endDate);
        $logsSemua = $builderSemua->orderBy('l.created_at', 'DESC')->get()->getResultArray();

        // Query untuk logs masuk barang
        $builderMasuk = $this->getBarangBaseQuery($db, $keyword, $startDate, $endDate);
        $builderMasuk->whereIn('l.aksi', ['Masuk', 'Kembali']);
        $logsMasuk = $builderMasuk->orderBy('l.created_at', 'DESC')->get()->getResultArray();

        // Query untuk logs keluar barang
        $builderKeluar = $this->getBarangBaseQuery($db, $keyword, $startDate, $endDate);
        $builderKeluar->where('l.aksi', 'Keluar');
        $logsKeluar = $builderKeluar->orderBy('l.created_at', 'DESC')->get()->getResultArray();

        $filename = 'log_barang_' . date('Ymd_His') . '.xlsx';
        
        return $this->exportBarangToExcel($logsSemua, $logsMasuk, $logsKeluar, $filename);
    }

    private function exportBarangToExcel($logsSemua, $logsMasuk, $logsKeluar, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Buat 3 sheet
        $spreadsheet->removeSheetByIndex(0);
        
        // Sheet 1: Semua Logs Barang
        $sheetSemua = $spreadsheet->createSheet();
        $sheetSemua->setTitle('Semua Logs Barang');
        $this->fillBarangExcelSheet($sheetSemua, $logsSemua, 'SEMUA LOGS BARANG');
        
        // Sheet 2: Barang Masuk
        $sheetMasuk = $spreadsheet->createSheet();
        $sheetMasuk->setTitle('Barang Masuk');
        $this->fillBarangExcelSheet($sheetMasuk, $logsMasuk, 'BARANG MASUK');
        
        // Sheet 3: Barang Keluar
        $sheetKeluar = $spreadsheet->createSheet();
        $sheetKeluar->setTitle('Barang Keluar');
        $this->fillBarangExcelSheet($sheetKeluar, $logsKeluar, 'BARANG KELUAR');
        
        $spreadsheet->setActiveSheetIndex(0);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function fillBarangExcelSheet($sheet, $data, $title)
    {
        // Judul sheet
        $sheet->setCellValue('A1', strtoupper($title));
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Header kolom
        $sheet->setCellValue('A3', 'TANGGAL');
        $sheet->setCellValue('B3', 'AKSI');
        $sheet->setCellValue('C3', 'NO AGENDA');
        $sheet->setCellValue('D3', 'NO SPB');
        $sheet->setCellValue('E3', 'NAMA BARANG');
        $sheet->setCellValue('F3', 'JUMLAH');
        $sheet->setCellValue('G3', 'SISA');
        $sheet->setCellValue('H3', 'SATUAN');
        $sheet->setCellValue('I3', 'ASAL');
        $sheet->setCellValue('J3', 'TUJUAN');
        $sheet->setCellValue('K3', 'TIPE');
        $sheet->setCellValue('L3', 'STATUS');
        $sheet->setCellValue('M3', 'KETERANGAN');
        
        $sheet->getStyle('A3:M3')->getFont()->setBold(true);
        $sheet->getStyle('A3:M3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        $row = 4;
        foreach ($data as $log) {
            $sheet->setCellValue('A' . $row, $log['created_at']);
            $sheet->setCellValue('B' . $row, $log['aksi']);
            $sheet->setCellValue('C' . $row, $log['no_agenda']);
            $sheet->setCellValue('D' . $row, $log['no_spb']);
            $sheet->setCellValue('E' . $row, $log['nama_barang']);
            $sheet->setCellValue('F' . $row, $log['jumlah']);
            $sheet->setCellValue('G' . $row, $log['sisa']);
            $sheet->setCellValue('H' . $row, $log['satuan']);
            $sheet->setCellValue('I' . $row, $log['asal']);
            $sheet->setCellValue('J' . $row, $log['tujuan']);
            $sheet->setCellValue('K' . $row, $log['tipe']);
            $sheet->setCellValue('L' . $row, $log['status']);
            $sheet->setCellValue('M' . $row, $log['log_keterangan']);
            $row++;
        }
        
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        if (!empty($data)) {
            $lastRow = $row;
            $sheet->setCellValue('A' . ($lastRow + 2), 'TOTAL DATA: ' . count($data));
            $sheet->mergeCells('A' . ($lastRow + 2) . ':M' . ($lastRow + 2));
            $sheet->getStyle('A' . ($lastRow + 2))->getFont()->setBold(true);
        }
    }

    private function exportLaptopLogs($db, $keyword, $startDate, $endDate)
    {
        $laptopActiveTab = $this->request->getGet('laptop_tab') ?? 'semua';

        // Query untuk semua logs laptop
        $builderSemua = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $logsSemua = $builderSemua->orderBy('l.created_at', 'DESC')->get()->getResultArray();

        // Query untuk logs registrasi laptop
        $builderRegistrasi = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $builderRegistrasi->where('l.aksi', 'Registrasi');
        $logsRegistrasi = $builderRegistrasi->orderBy('l.created_at', 'DESC')->get()->getResultArray();

        // Query untuk logs perpanjangan laptop
        $builderPerpanjangan = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $builderPerpanjangan->where('l.aksi', 'Perpanjangan');
        $logsPerpanjangan = $builderPerpanjangan->orderBy('l.created_at', 'DESC')->get()->getResultArray();

        // Query untuk logs update laptop
        $builderUpdate = $this->getLaptopBaseQuery($db, $keyword, $startDate, $endDate);
        $builderUpdate->where('l.aksi', 'Update');
        $logsUpdate = $builderUpdate->orderBy('l.created_at', 'DESC')->get()->getResultArray();

        $filename = 'log_laptop_' . date('Ymd_His') . '.xlsx';
        
        return $this->exportLaptopToExcel($logsSemua, $logsRegistrasi, $logsPerpanjangan, $logsUpdate, $filename);
    }

    private function exportLaptopToExcel($logsSemua, $logsRegistrasi, $logsPerpanjangan, $logsUpdate, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Buat 4 sheet
        $spreadsheet->removeSheetByIndex(0);
        
        // Sheet 1: Semua Logs Laptop
        $sheetSemua = $spreadsheet->createSheet();
        $sheetSemua->setTitle('Semua Logs Laptop');
        $this->fillLaptopExcelSheet($sheetSemua, $logsSemua, 'SEMUA LOGS LAPTOP');
        
        // Sheet 2: Registrasi Laptop
        $sheetRegistrasi = $spreadsheet->createSheet();
        $sheetRegistrasi->setTitle('Registrasi Laptop');
        $this->fillLaptopExcelSheet($sheetRegistrasi, $logsRegistrasi, 'REGISTRASI LAPTOP');
        
        // Sheet 3: Perpanjangan Laptop
        $sheetPerpanjangan = $spreadsheet->createSheet();
        $sheetPerpanjangan->setTitle('Perpanjangan Laptop');
        $this->fillLaptopExcelSheet($sheetPerpanjangan, $logsPerpanjangan, 'PERPANJANGAN LAPTOP');
        
        // Sheet 4: Update Laptop
        $sheetUpdate = $spreadsheet->createSheet();
        $sheetUpdate->setTitle('Update Laptop');
        $this->fillLaptopExcelSheet($sheetUpdate, $logsUpdate, 'UPDATE LAPTOP');
        
        $spreadsheet->setActiveSheetIndex(0);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function fillLaptopExcelSheet($sheet, $data, $title)
    {
        // Judul sheet
        $sheet->setCellValue('A1', strtoupper($title));
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Header kolom dengan REGISTRASI_KE
        $sheet->setCellValue('A3', 'TANGGAL');
        $sheet->setCellValue('B3', 'AKSI');
        $sheet->setCellValue('C3', 'NO REGISTRASI');
        $sheet->setCellValue('D3', 'REGISTRASI KE');
        $sheet->setCellValue('E3', 'JENIS');
        $sheet->setCellValue('F3', 'NAMA PENGGUNA');
        $sheet->setCellValue('G3', 'NOMOR ID CARD');
        $sheet->setCellValue('H3', 'INSTANSI/DIVISI');
        $sheet->setCellValue('I3', 'MEREK');
        $sheet->setCellValue('J3', 'TIPE');
        $sheet->setCellValue('K3', 'NOMOR SERI');
        $sheet->setCellValue('L3', 'BERLAKU SAMPAI');
        $sheet->setCellValue('M3', 'SPESIFIKASI');
        $sheet->setCellValue('N3', 'STATUS LAPTOP');
        $sheet->setCellValue('O3', 'KETERANGAN');
        
        $sheet->getStyle('A3:O3')->getFont()->setBold(true);
        $sheet->getStyle('A3:O3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        $row = 4;
        foreach ($data as $log) {
            $sheet->setCellValue('A' . $row, $log['created_at']);
            $sheet->setCellValue('B' . $row, $log['aksi']);
            $sheet->setCellValue('C' . $row, $log['no_registrasi'] ?? $log['laptop_no_registrasi'] ?? '-');
            $sheet->setCellValue('D' . $row, $log['registrasi_ke'] ?? $log['laptop_registrasi_ke'] ?? '1');
            $sheet->setCellValue('E' . $row, $log['jenis'] ?? '-');
            $sheet->setCellValue('F' . $row, $log['nama_pengguna'] ?? '-');
            $sheet->setCellValue('G' . $row, $log['nomor_id_card'] ?? '-');
            $sheet->setCellValue('H' . $row, $log['instansi_divisi'] ?? '-');
            $sheet->setCellValue('I' . $row, $log['merek'] ?? '-');
            $sheet->setCellValue('J' . $row, $log['tipe_laptop'] ?? '-');
            $sheet->setCellValue('K' . $row, $log['nomor_seri'] ?? '-');
            $sheet->setCellValue('L' . $row, $log['berlaku_sampai'] ?? '-');
            $sheet->setCellValue('M' . $row, $log['spesifikasi_lain'] ?? '-');
            $sheet->setCellValue('N' . $row, $log['laptop_status'] ?? '-');
            $sheet->setCellValue('O' . $row, $log['log_keterangan'] ?? '-');
            $row++;
        }
        
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        if (!empty($data)) {
            $lastRow = $row;
            $sheet->setCellValue('A' . ($lastRow + 2), 'TOTAL DATA: ' . count($data));
            $sheet->mergeCells('A' . ($lastRow + 2) . ':O' . ($lastRow + 2));
            $sheet->getStyle('A' . ($lastRow + 2))->getFont()->setBold(true);
            
            // Hitung berdasarkan jenis
            $pegawai = 0;
            $nonPegawai = 0;
            foreach ($data as $log) {
                if (isset($log['jenis']) && $log['jenis'] == 'Pegawai') $pegawai++;
                if (isset($log['jenis']) && $log['jenis'] == 'Non Pegawai') $nonPegawai++;
            }
            
            $lastRow += 2;
            $sheet->setCellValue('A' . $lastRow, 'Pegawai: ' . $pegawai);
            $sheet->setCellValue('B' . $lastRow, 'Non Pegawai: ' . $nonPegawai);
        }
    }

    /**
     * DETAIL METHODS
     */
    public function detailBarang($barangId)
    {
        $logs = $this->barangLog
            ->where('barang_id', $barangId)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $barang = $this->barang->find($barangId);

        return view('logs/detail_barang', [
            'logs' => $logs,
            'barang' => $barang
        ]);
    }

    public function detailLaptop($laptopId)
    {
        $db = \Config\Database::connect();
        
        $logs = $db->table('laptop_logs l')
            ->select('l.*, lp.nama_pengguna, lp.merek, lp.tipe_laptop, lp.nomor_seri, lp.no_registrasi, lp.jenis, lp.registrasi_ke')
            ->join('laptop lp', 'lp.id = l.laptop_id')
            ->where('l.laptop_id', $laptopId)
            ->orderBy('l.created_at', 'ASC')
            ->get()
            ->getResultArray();

        $laptop = $this->laptop->find($laptopId);

        return view('logs/detail_laptop', [
            'logs' => $logs,
            'laptop' => $laptop
        ]);
    }

    /**
     * DELETE METHODS
     */
    public function deleteBarangLog($id)
    {
        $log = $this->barangLog->find($id);
        
        if (!$log) {
            return redirect()->back()->with('error', 'Log tidak ditemukan');
        }

        $this->barangLog->delete($id);
        
        return redirect()->to('/logs?log_type=barang')->with('success', 'Log barang berhasil dihapus');
    }

    public function deleteLaptopLog($id)
    {
        $log = $this->laptopLog->find($id);
        
        if (!$log) {
            return redirect()->back()->with('error', 'Log tidak ditemukan');
        }

        $this->laptopLog->delete($id);
        
        return redirect()->to('/logs?log_type=laptop')->with('success', 'Log laptop berhasil dihapus');
    }
}