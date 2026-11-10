<?php

namespace App\Models;

use CodeIgniter\Model;

class MMaster extends Model
{
    /**
     * Get all active records for a table (filters out soft-deleted if column exists)
     */
    public function getData(array $tableConf): array
    {
        $builder = $this->db->table($tableConf['table']);

        // Check for soft delete flags
        $query = $this->db->query("SHOW COLUMNS FROM `{$tableConf['table']}` WHERE `Field` IN ('status', 'status_hapus', 'is_deleted')");
        $cols = $query->getResultArray();
        if (!empty($cols)) {
            $fieldNames = array_column($cols, 'Field');
            if (in_array('status_hapus', $fieldNames)) {
                $builder->where('status_hapus', 0);
            }
            if (in_array('status', $fieldNames)) {
                $builder->where('status', 1);
            }
            if (in_array('is_deleted', $fieldNames)) {
                $builder->where('is_deleted', 0);
            }
        }

        $builder->orderBy($tableConf['primary_key'], 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get single record by primary key
     */
    public function getById(array $tableConf, $id): ?array
    {
        $builder = $this->db->table($tableConf['table']);
        $builder->where($tableConf['primary_key'], $id);
        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * Determine if a column is AUTO_INCREMENT
     */
    protected function isAutoIncrement(string $table, string $pkCol): bool
    {
        $query = $this->db->query("SHOW COLUMNS FROM `{$table}` WHERE `Field` = ?", [$pkCol]);
        $row = $query->getRowArray();
        return $row && strpos($row['Extra'], 'auto_increment') !== false;
    }

    /**
     * Generate next numeric ID for non-auto_increment legacy tables
     */
    public function getNextId(string $table, string $pkCol): int
    {
        $builder = $this->db->table($table);
        $builder->selectMax($pkCol, 'max_id');
        $row = $builder->get()->getRowArray();
        $max = isset($row['max_id']) ? (int) $row['max_id'] : 0;
        return $max + 1;
    }

    /**
     * Insert or update record
     */
    public function saveData(array $tableConf, array $data, $id = null): bool
    {
        $builder = $this->db->table($tableConf['table']);
        $pk = $tableConf['primary_key'];

        if (!empty($id)) {
            // Update
            $builder->where($pk, $id);
            return $builder->update($data);
        } else {
            // Insert: Check if manual ID generation is needed
            if (!$this->isAutoIncrement($tableConf['table'], $pk) && !isset($data[$pk])) {
                $data[$pk] = $this->getNextId($tableConf['table'], $pk);
            }
            return $builder->insert($data);
        }
    }

    /**
     * Delete record by primary key (soft delete if status/status_hapus/is_deleted column exists)
     */
    public function deleteData(array $tableConf, $id): bool
    {
        $builder = $this->db->table($tableConf['table']);
        $pk = $tableConf['primary_key'];

        // Soft-delete if table has status or status_hapus or is_deleted column
        $query = $this->db->query("SHOW COLUMNS FROM `{$tableConf['table']}` WHERE `Field` IN ('status', 'status_hapus', 'is_deleted')");
        $cols = $query->getResultArray();
        if (!empty($cols)) {
            $fieldNames = array_column($cols, 'Field');
            if (in_array('status_hapus', $fieldNames)) {
                $builder->where($pk, $id);
                return $builder->update(['status_hapus' => 1]);
            }
            if (in_array('status', $fieldNames)) {
                $builder->where($pk, $id);
                return $builder->update(['status' => 0]);
            }
            if (in_array('is_deleted', $fieldNames)) {
                $builder->where($pk, $id);
                return $builder->update(['is_deleted' => 1]);
            }
        }

        // Fallback to permanent delete if table has no soft delete flag
        $builder->where($pk, $id);
        return $builder->delete();
    }
}
