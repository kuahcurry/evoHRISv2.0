<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MMaster;
use Config\MasterTables;

class Master extends BaseController
{
    protected MasterTables $masterConfig;
    protected MMaster $model;

    public function __construct()
    {
        $this->masterConfig = config('MasterTables');
        $this->model = new MMaster();
    }

    /**
     * Get table configuration for a specific entity
     */
    protected function getTableConfig(string $entity): ?array
    {
        return $this->masterConfig->tables[$entity] ?? null;
    }

    /**
     * List all records of a master entity
     */
    public function index(string $entity = ''): string
    {
        $tableConf = $this->getTableConfig($entity);
        if (!$tableConf) {
            return sendResponse(404, 'Entitas master "' . esc($entity) . '" tidak ditemukan');
        }

        $data = $this->model->getData($tableConf);
        return sendResponse(count($data) > 0 ? 200 : 204, $data);
    }

    /**
     * Get single record by ID
     */
    public function show(string $entity = '', $id = null): string
    {
        $tableConf = $this->getTableConfig($entity);
        if (!$tableConf) {
            return sendResponse(404, 'Entitas master "' . esc($entity) . '" tidak ditemukan');
        }

        $row = $this->model->getById($tableConf, $id);
        return sendResponse($row ? 200 : 204, $row);
    }

    /**
     * Save (insert or update) record
     */
    public function save(string $entity = ''): string
    {
        $tableConf = $this->getTableConfig($entity);
        if (!$tableConf) {
            return sendResponse(404, 'Entitas master "' . esc($entity) . '" tidak ditemukan');
        }

        $request = request();
        $input = $request->getPost();
        if (empty($input)) {
            $json = $request->getJSON(true);
            if (is_array($json)) {
                $input = $json;
            }
        }

        $pk = $tableConf['primary_key'];
        $id = $input[$pk] ?? ($input['id'] ?? null);
        if ($id === '' || $id === '0' || $id === 0) {
            $id = null;
        }

        // Filter input based on allowed fields in config
        $saveData = [];
        foreach ($tableConf['fields'] as $field) {
            if (isset($input[$field])) {
                $saveData[$field] = trim($input[$field]);
            }
        }

        if (empty($saveData)) {
            return sendResponse(400, 'Data input tidak valid atau kosong');
        }

        $success = $this->model->saveData($tableConf, $saveData, $id);
        if ($success) {
            return sendResponse(200, ['status' => true, 'message' => 'Data berhasil disimpan']);
        }

        return sendResponse(304, 'Gagal menyimpan data');
    }

    /**
     * Delete record by ID
     */
    public function delete(string $entity = ''): string
    {
        $tableConf = $this->getTableConfig($entity);
        if (!$tableConf) {
            return sendResponse(404, 'Entitas master "' . esc($entity) . '" tidak ditemukan');
        }

        $request = request();
        $input = $request->getPost();
        if (empty($input)) {
            $json = $request->getJSON(true);
            if (is_array($json)) {
                $input = $json;
            }
        }

        $pk = $tableConf['primary_key'];
        $id = $input['id'] ?? ($input[$pk] ?? null);
        if (!$id) {
            return sendResponse(400, 'Parameter ID tidak ditemukan');
        }

        $success = $this->model->deleteData($tableConf, $id);
        if ($success) {
            return sendResponse(200, ['status' => true, 'message' => 'Data berhasil dihapus']);
        }

        return sendResponse(305, 'Gagal menghapus data');
    }
}
