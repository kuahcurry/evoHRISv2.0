<?php

namespace App\Models\RawatDarurat;

use CodeIgniter\Model;

class MPerencanaan extends Model
{
    protected function getCounter($params)
    {
        $strquery = "select counter from rawat_darurat_rme.perencanaan where single_id = ? and no_registrasikunjungan = ? order by counter desc limit 1";
        $rs = $this->db->query($strquery, $params);
        $number = 1;
        if ($rs->getNumRows() > 0) $number = $rs->getResultArray()[0]['counter'] + 1;
        return $number;
    }
    public function saveData($dataset)
    {
        $zona = intval(getenv('app.zona'));
        try {
            $single_id = $dataset['single_id'];
            $no_registrasikunjungan = $dataset['no_kunjungan'];
            $counter = $this->getCounter(array($single_id, $no_registrasikunjungan));
            $tgl_entry = gmdate("Y-m-d H:i:s", time() + 60 * 60 * $zona);
            $kd_user = $dataset['kd_user'];
            $isi_perencanaan = $dataset['isi_perencanaan'];
            $penanggungjawab = $dataset['penanggungjawab'];
        } catch (\Exception $e) {
            return array("code" => 400);
        }
        $this->db->transBegin();
        $params = array($single_id, $no_registrasikunjungan, $counter, $tgl_entry, $isi_perencanaan, $kd_user, $penanggungjawab);
        $strquery = "insert into rawat_darurat_rme.perencanaan (single_id,no_registrasikunjungan,counter,tgl_entry,isi_perencanaan,kd_user,penanggungjawab) values(?,?,?,?,?,?,?)";
        $this->db->query($strquery, $params);
        if ($this->db->transStatus() === false && $this->db->error()) {
            $this->db->transRollback();
            $result = array("code" => 304);
        } else {
            $this->db->transCommit();
            $result = array("code" => 200);
        }
        return $result;
    }
    public function loadData($dataset)
    {
        $params = array();
        $arrfilters = array();
        $strfilters = "";
        if (isset($dataset['single_id'])) {
            array_push($params, $dataset['single_id']);
            array_push($arrfilters, "single_id = ?");
        }
        if (isset($dataset['no_kunjungan'])) {
            array_push($params, $dataset['no_kunjungan']);
            array_push($arrfilters, "no_registrasikunjungan = ?");
        }
        if (count($params) > 0) $strfilters = " where " . implode(" and ", $arrfilters);
        $strquery = "select * from rawat_darurat_rme.perencanaan{$strfilters} order by tgl_entry desc, counter desc limit 1";
        $res = $this->db->query($strquery, $params);
        return $res->getResultArray();
    }
}
