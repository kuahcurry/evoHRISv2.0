<?php

namespace App\Models;

use CodeIgniter\Model;

class mArsip extends Model
{
    protected $table = 'tb_rak';
    protected $primaryKey = 'no_rak';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'no_rak',
        'no_lemari',
        'nama_rak'
    ];

    public function get_data_rak($limit = null, $offset = null)
    {
        $builder = $this->builder();
        if ($limit !== null) {
            $builder->limit($limit, $offset ?? 0);
        }
        return $builder->get()->getResultArray();
    }

    public function get_rak($no_rak)
    {
        return $this->where('no_rak', $no_rak)->first();
    }

    public function add_rak(array $data)
    {
        return $this->insert($data);
    }

    public function edit_rak($no_rak, array $data)
    {
        return $this->update($no_rak, $data);
    }

    public function delete_rak($no_rak)
    {
        return $this->delete($no_rak);
    }
}
