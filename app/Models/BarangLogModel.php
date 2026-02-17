<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangLogModel extends Model
{
    protected $table            = 'barang_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'barang_id',
        'laptop_id',
        'aksi',
        'jumlah',
        'sisa',
        'keterangan',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'aksi'   => 'required|in_list[Registrasi,Masuk,Keluar,Kembali,Selesai,Update,Aktif,Nonaktif,Perbaikan,Hilang]',
        'jumlah' => 'permit_empty|integer'
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Get logs with related data
     */
    public function getLogsWithRelations($limit = 100)
    {
        return $this->select('barang_logs.*, 
                             barang.nama_barang, barang.no_agenda,
                             laptop.nama_pengguna, laptop.merek, laptop.tipe_laptop, laptop.nomor_seri')
            ->join('barang', 'barang.id = barang_logs.barang_id', 'left')
            ->join('laptop', 'laptop.id = barang_logs.laptop_id', 'left')
            ->orderBy('barang_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get logs by barang ID
     */
    public function getByBarang($barangId)
    {
        return $this->where('barang_id', $barangId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get logs by laptop ID
     */
    public function getByLaptop($laptopId)
    {
        return $this->where('laptop_id', $laptopId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 20)
    {
        return $this->select('barang_logs.*, 
                             COALESCE(barang.nama_barang, CONCAT(laptop.merek, " ", laptop.tipe_laptop)) as item_name,
                             COALESCE(barang.no_agenda, laptop.nomor_seri) as item_code,
                             CASE 
                                WHEN barang_logs.barang_id IS NOT NULL THEN "Barang"
                                WHEN barang_logs.laptop_id IS NOT NULL THEN "Laptop"
                                ELSE "-"
                             END as item_type')
            ->join('barang', 'barang.id = barang_logs.barang_id', 'left')
            ->join('laptop', 'laptop.id = barang_logs.laptop_id', 'left')
            ->where('(barang_logs.barang_id IS NOT NULL OR barang_logs.laptop_id IS NOT NULL)')
            ->orderBy('barang_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get statistics by action type
     */
    public function getActionStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('aksi, COUNT(*) as total')
                       ->groupBy('aksi');

        if ($startDate && $endDate) {
            $builder->where('created_at >=', $startDate . ' 00:00:00')
                    ->where('created_at <=', $endDate . ' 23:59:59');
        }

        return $builder->findAll();
    }

    /**
     * Hapus logs lama (maintenance)
     */
    public function clearOldLogs($days = 90)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('created_at <', $date)
                    ->where('aksi !=', 'Registrasi') // Keep registrasi logs
                    ->delete();
    }
}