<?php

namespace App\Controllers;

use App\Models\BarangLogModel;

class BarangLog extends BaseController
{
    protected $log;

    public function __construct()
    {
        $this->log = new BarangLogModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $aksi    = $this->request->getGet('aksi') ?? '';
        $keyword = $this->request->getGet('keyword') ?? '';
        $startDate = $this->request->getGet('start_date') ?? '';
        $endDate = $this->request->getGet('end_date') ?? '';

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

        if ($aksi === 'Masuk') {
            $builder->where('l.aksi', 'Masuk');
        } elseif ($aksi === 'Keluar') {
            $builder->where('l.aksi', 'Keluar');
        } elseif ($aksi === 'Kembali') {
            $builder->where('l.aksi', 'Kembali');
        } elseif ($aksi === 'Registrasi') {
            $builder->where('l.aksi', 'Registrasi');
        } elseif ($aksi === 'Selesai') {
            $builder->where('l.aksi', 'Selesai');
        }

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

        $logs = $builder
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $totalRows = $builder->countAllResults(false);

        return view('logs/index', [
            'logs'      => $logs,
            'aksi'      => $aksi,
            'keyword'   => $keyword,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'totalRows' => $totalRows
        ]);
    }

    public function export()
    {
        $db = \Config\Database::connect();

        $aksi    = $this->request->getGet('aksi') ?? '';
        $keyword = $this->request->getGet('keyword') ?? '';
        $startDate = $this->request->getGet('start_date') ?? '';
        $endDate = $this->request->getGet('end_date') ?? '';

        $builder = $db->table('barang_logs l')
            ->select([
                'l.created_at',
                'l.aksi',
                'l.jumlah',
                'l.sisa',
                'l.keterangan AS log_keterangan',

                'b.no_agenda',
                'b.no_spb',
                'b.nama_barang',
                'b.satuan',
                'b.asal',
                'b.tujuan',
                'b.tipe',
                'b.status'
            ])
            ->join('barang b', 'b.id = l.barang_id');

        if ($aksi === 'Masuk') {
            $builder->where('l.aksi', 'Masuk');
        } elseif ($aksi === 'Keluar') {
            $builder->where('l.aksi', 'Keluar');
        } elseif ($aksi === 'Kembali') {
            $builder->where('l.aksi', 'Kembali');
        }

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
            ->groupEnd();
        }

        $logs = $builder
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $filename = 'log_barang_' . date('Ymd_His') . '.xlsx';
        
        return $this->exportToExcel($logs, $filename);
    }

    private function exportToExcel($data, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'TANGGAL');
        $sheet->setCellValue('B1', 'AKSI');
        $sheet->setCellValue('C1', 'NO AGENDA');
        $sheet->setCellValue('D1', 'NO SPB');
        $sheet->setCellValue('E1', 'NAMA BARANG');
        $sheet->setCellValue('F1', 'JUMLAH');
        $sheet->setCellValue('G1', 'SISA');
        $sheet->setCellValue('H1', 'SATUAN');
        $sheet->setCellValue('I1', 'ASAL');
        $sheet->setCellValue('J1', 'TUJUAN');
        $sheet->setCellValue('K1', 'TIPE');
        $sheet->setCellValue('L1', 'STATUS');
        $sheet->setCellValue('M1', 'KETERANGAN');

        $row = 2;
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

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function detail($barangId)
    {
        $logs = $this->log
            ->where('barang_id', $barangId)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $barang = $this->log->db
            ->table('barang')
            ->where('id', $barangId)
            ->get()
            ->getRowArray();

        return view('logs/detail', [
            'logs' => $logs,
            'barang' => $barang
        ]);
    }

    public function delete($id)
    {
        $log = $this->log->find($id);
        
        if (!$log) {
            return redirect()->back()->with('error', 'Log tidak ditemukan');
        }

        $this->log->delete($id);
        
        return redirect()->to('/logs')->with('success', 'Log berhasil dihapus');
    }
}