<?php

namespace App\Models;

use CodeIgniter\Model;

class LaptopModel extends Model
{
    protected $table = 'laptop';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'no_registrasi', 'nama_pengguna', 'nomor_id_card', 'instansi_divisi',
        'merek', 'tipe_laptop', 'nomor_seri', 'berlaku_sampai',
        'spesifikasi_lain', 'status', 'keterangan', 'jenis'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Generate nomor registrasi otomatis
    public function generateNoRegistrasi($jenis)
    {
        $prefix = $jenis === 'Pegawai' ? 'PEG' : 'NPEG';
        $tahun = date('Y');
        $bulan = date('m');
        
        // Cari nomor urut terakhir untuk tahun ini
        $lastReg = $this->select('no_registrasi')
                        ->like('no_registrasi', $prefix . $tahun . $bulan, 'after')
                        ->orderBy('no_registrasi', 'DESC')
                        ->first();
        
        if ($lastReg) {
            // Ambil 4 digit terakhir
            $lastNumber = (int)substr($lastReg['no_registrasi'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $tahun . $bulan . $newNumber;
    }

    // Get laptop dengan logs
    public function getWithLogs($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('laptop.*');
        
        if ($id) {
            $builder->where('laptop.id', $id);
            $data = $builder->get()->getRowArray();
            
            if ($data) {
                // Ambil logs
                $logBuilder = $this->db->table('laptop_logs');
                $logBuilder->select('*');
                $logBuilder->where('laptop_id', $id);
                $logBuilder->orderBy('created_at', 'DESC');
                $data['logs'] = $logBuilder->get()->getResultArray();
            }
            
            return $data;
        }
        
        return $builder->get()->getResultArray();
    }

    // Search laptop
    public function search($keyword, $status = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        
        if ($keyword) {
            $builder->groupStart()
                    ->like('nama_pengguna', $keyword)
                    ->orLike('nomor_id_card', $keyword)
                    ->orLike('instansi_divisi', $keyword)
                    ->orLike('merek', $keyword)
                    ->orLike('tipe_laptop', $keyword)
                    ->orLike('nomor_seri', $keyword)
                    ->orLike('no_registrasi', $keyword)
                    ->groupEnd();
        }
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}