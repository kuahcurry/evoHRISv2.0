<?php

namespace App\Controllers\RawatDarurat;

use CodeIgniter\Controller;
use App\Models\RawatDarurat\MPerencanaan;

class Perencanaan extends Controller
{
    public function __construct()
    {
        $this->model = new MPerencanaan();
    }
    public function save(): string
    {
        $request = request();
        $param = array();
        $results = array();
        $rs = $this->model->saveData($request->getPost());
        if (count($rs) > 0) {
            $code = $rs['code'];
        }
        return sendResponse($code, $results);
    }
    public function load(): string
    {
        $request = request();
        $param = array();
        $code = 204;
        $results = array();
        $rs = $this->model->loadData($request->getGet());
        if (count($rs) > 0) {
            $code = 200;
            $results = $rs;
        }
        return sendResponse($code, $results);
    }
}
