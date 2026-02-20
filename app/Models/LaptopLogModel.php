<?php

namespace App\Models;

use CodeIgniter\Model;

class LaptopLogModel extends Model
{
    protected $table = 'laptop_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'laptop_id', 'no_registrasi', 'aksi', 'keterangan', 'created_at'
    ];

    // Tidak menggunakan timestamps otomatis karena kita mengelola created_at manual
    protected $useTimestamps = false;
    
    // Format tanggal
    protected $dateFormat = 'datetime';

    /**
     * Get logs with laptop info
     */
    public function getWithLaptop($laptopId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('laptop_logs.*, laptop.nama_pengguna, laptop.merek, laptop.tipe_laptop, laptop.nomor_seri, laptop.jenis, laptop.no_registrasi as laptop_no_registrasi');
        $builder->join('laptop', 'laptop.id = laptop_logs.laptop_id', 'left');
        
        if ($laptopId) {
            $builder->where('laptop_logs.laptop_id', $laptopId);
        }
        
        $builder->orderBy('laptop_logs.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get logs by laptop ID
     */
    public function getByLaptopId($laptopId)
    {
        return $this->where('laptop_id', $laptopId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get logs by no_registrasi
     */
    public function getByNoRegistrasi($noRegistrasi)
    {
        return $this->where('no_registrasi', $noRegistrasi)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get recent logs with limit
     */
    public function getRecent($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Add log entry
     */
    public function addLog($laptopId, $aksi, $keterangan = null, $noRegistrasi = null)
    {
        $data = [
            'laptop_id' => $laptopId,
            'aksi' => $aksi,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($noRegistrasi) {
            $data['no_registrasi'] = $noRegistrasi;
        }
        
        return $this->insert($data);
    }

    /**
     * Get logs by date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate . ' 00:00:00')
                    ->where('created_at <=', $endDate . ' 23:59:59')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get logs by action type
     */
    public function getByAction($aksi)
    {
        return $this->where('aksi', $aksi)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Count logs by laptop
     */
    public function countByLaptop($laptopId)
    {
        return $this->where('laptop_id', $laptopId)->countAllResults();
    }

    /**
     * Delete old logs (untuk maintenance)
     */
    public function deleteOldLogs($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $date)->delete();
    }
}