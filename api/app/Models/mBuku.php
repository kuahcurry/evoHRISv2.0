<?php

namespace App\Models;

use CodeIgniter\Model;

class mBuku extends Model
{
    protected $table = 'tb_buku_tanah';
    protected $primaryKey = 'id_bukutanah';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_bukutanah',
        'id_hak',
        'no_hakbuku',
        'tahun',
        'luas',
        'id_kecamatan',
        'id_desa',
        'no208',
        'status_buku',
        'status_entry',
        'pemilik_awal',
        'catatan_buku',
        'jenis_asuransi',
        'produkasuransi',
        'tanggal_kontrak',
        'tanggal_expired_kontrak',
        'tanggal_premi',
        'tgl_lahir',
        'alamat',
        'no_telpn',
        'email'
    ];

    public function get_data_buku($limit = null, $offset = null)
    {
        $builder = $this->builder();
        if ($limit !== null) {
            $builder->limit($limit, $offset ?? 0);
        }
        return $builder->get()->getResultArray();
    }

    public function get_buku($id_bukutanah)
    {
        return $this->where('id_bukutanah', $id_bukutanah)->first();
    }

    public function add_buku(array $data)
    {
        return $this->insert($data);
    }

    public function edit_buku($id_bukutanah, array $data)
    {
        return $this->update($id_bukutanah, $data);
    }

    public function delete_buku($id_bukutanah)
    {
        return $this->delete($id_bukutanah);
    }
}
