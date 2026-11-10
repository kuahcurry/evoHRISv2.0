<?php

namespace App\Models;

use CodeIgniter\Model;

class MUser extends Model
{
  public function getUserUnit($dataset)
  {
    $params = array($dataset['kd_user']);
    $query = "SELECT
                u.kd_unit,
                u.nama_unit,
                u.nama_unit tag_unit,
                i.kd_instalasi,
                i.nama_instalasi,
                ki.tag tag_instalasi,
                uu.all_dpjp 
              FROM
                master.user_unit uu
                INNER JOIN unit u ON u.kd_unit = uu.kd_unit
                INNER JOIN instalasi i ON i.kd_instalasi = u.kd_instalasi
                INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
              WHERE
                uu.aktif = '1' 
                AND uu.kd_user = ?";
    $res = $this->db->query($query, $params);
    if ($res->getNumRows() == 0) {
      $params = array($dataset['kd_user']);
      $query = "SELECT
                  u.kd_unit,
                  u.nama_unit,
                  u.nama_unit tag_unit,
                  i.kd_instalasi,
                  i.nama_instalasi,
                  ki.tag tag_instalasi,
                  FALSE all_dpjp 
                FROM
                  users us
                  INNER JOIN unit u ON u.kd_unit = us.unit
                  INNER JOIN instalasi i ON i.kd_instalasi = u.kd_instalasi
                INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
                WHERE
                  us.kd_user = ?";
      $res = $this->db->query($query, $params);
    }
    foreach ($res->getResult() as $row) {
      $row->all_dpjp = $row->all_dpjp == 'f' ? false : true;
    }
    $result = $res->getResultArray();
    return $result;
  }
    
  public function getRuanganUser($dataset)
  {
    $params = array($dataset['kd_user']);
    $query = "SELECT
                rp.kd_ruangperawatan,
                rp.nama_ruangperawatan
              FROM
                master.user_unit uu
                INNER JOIN unit u ON u.kd_unit = uu.kd_unit
                INNER JOIN ruang_perawatan rp ON rp.kd_ruangperawatan = u.kd_ruangperawatan
              WHERE
                uu.kd_user = ?
                AND uu.aktif = '1'
                AND u.status_aktif = 1
                AND rp.status_aktif = 1
              GROUP BY
                rp.kd_ruangperawatan,
                rp.nama_ruangperawatan
              ORDER BY
                rp.nama_ruangperawatan";
    $res = $this->db->query($query, $params);
    $result = $res->getResultArray();
    return $result;
  }

  public function saveDefaultUnit($dataset)
  {
    $params = array($dataset['kd_unit'], $dataset['kd_user']);
    $query = "select kd_user, kd_unit from master.user_unit where kd_unit = ? and kd_user = ?";
    $result = array();
    $res = $this->db->query($query, $params);
    if ($res->getNumRows() > 0) {
      $query = "update users set unit = ? where kd_user = ?";
      $res = $this->db->query($query, $params);
      $result = array(true);
    }
    return $result;
  }

  public function getKelPasien($dataset)
  {
    $result = array();
    $strquery = "select * from gizi_jenis_pasien order by kd_jenis asc";
    $res = $this->db->query($strquery);
    if ($res->getNumRows() > 0) {
      foreach ($res->getResult() as $item) {
        $data = array(
          'kd_jenis' => $item->kd_jenis,
          'jenis_pasien' => $item->jenis_pasien
        );

        $result[] = $data;
      }
    }
    return $result;
  }

  public function getJKelPasien($dataset)
  {
    $result = array();
    $strquery = "select * from jenis_kelamin order by kd_jenis asc";
    $res = $this->db->query($strquery);
    if ($res->getNumRows() > 0) {
      foreach ($res->getResult() as $item) {
        $data = array(
          'kd_jenis' => $item->kd_jenis,
          'jenis' => $item->jenis
        );

        $result[] = $data;
      }
    }
    return $result;
  }

  public function getPekerjaan($dataset)
  {
    $result = array();
    $strquery = "select * from pekerjaan order by kd_pekerjaan asc";
    $res = $this->db->query($strquery);
    if ($res->getNumRows() > 0) {
      foreach ($res->getResult() as $item) {
        $data = array(
          'kd_pekerjaan' => $item->kd_pekerjaan,
          'pekerjaan' => $item->nama_pekerjaan
        );

        $result[] = $data;
      }
    }
    return $result;
  }

  public function getPendidikan($dataset)
  {
    $result = array();
    $strquery = "select * from pendidikan order by kd_pendidikan asc";
    $res = $this->db->query($strquery);
    if ($res->getNumRows() > 0) {
      foreach ($res->getResult() as $item) {
        $data = array(
          'kd_pendidikan' => $item->kd_pendidikan,
          'pendidikan' => $item->nama_pendidikan,
          'kd_pendidikan_map' => $item->kd_pendidikan_map
        );

        $result[] = $data;
      }
    }
    return $result;
  }

  public function getDokumen($dataset)
  {
    $dokumen = array();
    $params = array();
    $arrfilters = array();
    $mdr_unit = "";

    if (!empty($dataset['single_id'])) {
      array_push($params, $dataset['single_id']);
      array_push($arrfilters, "k.single_id = ?");
    }
    
    if (!empty($dataset['kd_instalasi'])) {
      array_push($params, $dataset['kd_instalasi']);
      array_push($arrfilters, "COALESCE(i2.kd_kelompok_instalasi, i.kd_kelompok_instalasi) = (SELECT kd_kelompok_instalasi FROM instalasi WHERE kd_instalasi = ?)");
    }
    
    if (!empty($dataset['no_kunjungan'])) {
      array_push($params, $dataset['no_kunjungan']);
      array_push($arrfilters, "k.no_registrasikunjungan = ?");
      $mdr_unit = " OR mdr.kd_unit IN ( SELECT kd_unit FROM history_gantikamar WHERE no_registrasikunjungan = '{$dataset['no_kunjungan']}' ) ";
    }
    
    if (count($params) > 0) $strfilters = ' AND ' . implode(" AND ", $arrfilters);
    $strquery = "SELECT
                  k.no_registrasikunjungan,
                  COALESCE(u2.kd_unit, u.kd_unit) kd_unit,
                  COALESCE(u2.kd_instalasi, u.kd_instalasi) kd_instalasi,
                  COALESCE(u2.nama_unit, u.nama_unit) nama_unit,
                  COALESCE(i2.nama_instalasi, i.nama_instalasi) nama_instalasi,
                  mdr.kd_dokumen,
                  mdr.nama_dokumen,
                  mdr.url,
                  mdr.api_v2,
                  mdr.kd_rm,
                  k.single_id,
                  pm.kd_paramedis,
                  pm.nama_paramedis,
                  k.no_medicalrecord,
                  t.no_transaksi,
                  k.tgl_masuk AS tgl_pendaftaran,
                  rp.kd_ruangperawatan,
                  CASE WHEN mdr.tabel_utama != '' THEN mdr.tabel_utama ELSE '-' END AS tabel_utama,
                  mdr.filter_sql,
                  mdr.single_row,
                  CASE WHEN mdr.kd_jabatan IS NOT NULL THEN mdr.kd_jabatan ELSE 0 END AS kd_jabatan
                FROM
                  kunjungan k
                  INNER JOIN unit u ON u.kd_unit = k.kd_unit
                  INNER JOIN instalasi i ON i.kd_instalasi = u.kd_instalasi
                  INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
                  LEFT JOIN para_medis pm ON k.kd_paramedis = pm.kd_paramedis
                  LEFT JOIN history_gantikamar hg ON hg.no_registrasikunjungan = k.no_registrasikunjungan AND hg.urut_masuk = (SELECT urut_masuk FROM history_gantikamar WHERE no_registrasikunjungan = k.no_registrasikunjungan ORDER BY urut_masuk DESC LIMIT 1)
                  INNER JOIN transaksi t ON CASE WHEN ki.tag = 'rawat_inap' THEN (t.no_transaksi = hg.no_transaksi OR t.no_registrasikunjungan = k.no_registrasikunjungan) ELSE t.no_registrasikunjungan = k.no_registrasikunjungan END
                  LEFT JOIN kamar_perawatan kpr ON kpr.kd_kamarperawatan = hg.kd_kamarperawatan
                  INNER JOIN unit u2 ON u2.kd_unit = t.kd_unit
                  INNER JOIN instalasi i2 ON i2.kd_instalasi = u2.kd_instalasi AND CASE WHEN ki.tag = 'rawat_inap' THEN (i2.kd_instalasi NOT IN (i.kd_instalasi) OR t.no_transaksi = hg.no_transaksi) ELSE 1 = 1 END
                  INNER JOIN kelompok_instalasi ki2 ON ki2.kd_kelompok_instalasi = i2.kd_kelompok_instalasi AND CASE WHEN ki.tag = 'rawat_inap' THEN (ki2.kd_kelompok_instalasi NOT IN (ki.kd_kelompok_instalasi) OR t.no_transaksi = hg.no_transaksi) ELSE 1 = 1 END
                  LEFT JOIN ruang_perawatan rp ON rp.kd_ruangperawatan = u2.kd_ruangperawatan
                  INNER JOIN master.master_dokumen_rme mdr ON 
                    (
                      (
                        CASE
                          WHEN ki.tag = 'rawat_inap' AND i.kd_kelompok_instalasi != i2.kd_kelompok_instalasi 
                            THEN mdr.kd_kelompok_instalasi IN (i2.kd_kelompok_instalasi::TEXT)
                            ELSE mdr.kd_kelompok_instalasi IN (i.kd_kelompok_instalasi::TEXT)
                        END
                      )
                      OR mdr.kd_instalasi = '0'
                    )
                    AND (
                      mdr.kd_unit = k.kd_unit
                      OR mdr.kd_unit = '0'
                      {$mdr_unit}
                    )
                WHERE
                  k.status = 0
                  AND t.kd_unit NOT IN (SELECT UNNEST(string_to_array(mdr.EXCLUDE, ',') :: INT []))
                  {$strfilters}
                ORDER BY
                  mdr.nama_dokumen";
    // var_dump(logger($strquery, $params));
    // die();
    $res = $this->db->query($strquery, $params);
    if ($res->getNumRows() > 0) {
      foreach ($res->getResult() as $item) {
        $nama_user = '-';
        if ($item->tabel_utama != '-') {
          $tabel = $item->tabel_utama;
          $no_kunjungan = $item->no_registrasikunjungan;
          $params2 = array();
          array_push($params2, $no_kunjungan);
          $filter_jabatan = '';
          $filter_join = '';
          if ($item->kd_jabatan != 0) {
            $kd_jabatan = $item->kd_jabatan;
            array_push($params2, $kd_jabatan);
            $filter_join = 'INNER JOIN master.user_role rol ON rol.kd_user = isi.kd_user';
            $filter_jabatan = ' AND rol.kd_jabatan = ?';
          }

          //kondisi karena tidak punya tgl entry
          $tgl_entry = "tgl_entry";
          $isi_kd_user = "isi.kd_user";
          $usr_kd_user = "usr.kd_user";
          if ($tabel == 'resume_medis_log') {
            $tgl_entry = "tgl_update";
          } else if ($tabel == 'history_konsul_rwi') {
            $tgl_entry = "tgl_input";
            $isi_kd_user = "isi.kd_dpjp";
            $usr_kd_user = "usr.kd_paramedis";
          } else if ($tabel == 'rawat_jalan_rme.konsul_detail') {
            $tgl_entry = "tgl_konsul";
          }

          $filter_khusus = '';
          $filter_sql = $item->filter_sql;
          if (!empty($filter_sql)) {
            $filter_khusus = 'and '. $filter_sql;
          }

          $single_row = $item->single_row;
          if($single_row == '1') {
              $strquery2 = "SELECT
                              pm.nama_paramedis nama_user
                            FROM
                              PUBLIC.kunjungan k
                              INNER JOIN {$tabel} isi ON k.no_registrasikunjungan = isi.no_registrasikunjungan
                              INNER JOIN para_medis pm ON pm.kd_paramedis = k.kd_paramedis
                            WHERE
                              k.no_registrasikunjungan = ? {$filter_khusus}
                            LIMIT 1";
          } else {
            $strquery2 = "SELECT
                            usr.nama_user 
                          FROM
                            PUBLIC.users usr
                            INNER JOIN {$tabel} isi ON {$isi_kd_user} = {$usr_kd_user}
                            {$filter_join} 
                          WHERE
                            no_registrasikunjungan = ? {$filter_jabatan} {$filter_khusus}
                          ORDER BY
                            {$tgl_entry} DESC 
                          LIMIT 1";
          }
          $res2 = $this->db->query($strquery2, $params2);
          if ($res2->getNumRows() > 0) {
            foreach ($res2->getResult() as $item2) {
              $nama_user = $item2->nama_user;
            }
          }
        }

        $data = array(
          'no_registrasikunjungan' => $item->no_registrasikunjungan,
          'kd_dokumen' => $item->kd_dokumen,
          'kd_rm' => $item->kd_rm,
          'nama_dokumen' => $item->nama_dokumen,
          'nama_instalasi' => $item->nama_instalasi,
          'nama_unit' => $item->nama_unit,
          'kd_instalasi' => $item->kd_instalasi,
          'kd_unit' => $item->kd_unit,
          'kd_ruangperawatan' => $item->kd_ruangperawatan,
          'single_id' => $item->single_id,
          'nama_paramedis' => $item->nama_paramedis,
          'no_medicalrecord' => $item->no_medicalrecord,
          'no_transaksi' => $item->no_transaksi,
          'tgl_pendaftaran' => $item->tgl_pendaftaran,
          'nama_user' => $nama_user,
          'customProperties' => array('url' => $item->url, 'api_v2' => $item->api_v2)
        );

        $dokumen[] = $data;
      }
    }
    return null_to_empty($dokumen);
  }

  public function loadUsers($dataset)
  {
    $filter = "";
    $kd_jabatan = " AND jt.kd_jabatan != '13' ";
    if (isset($dataset['keyword']) && !empty($dataset['keyword'])) {
      $filter = " AND us.nama_user ilike '%{$dataset['keyword']}%'";
    }
    if (isset($dataset['kd_jabatan'])) {
      $kd_jabatan = " AND jt.kd_jabatan in ({$dataset['kd_jabatan']}) ";
    }
    $strquery = "SELECT
                  *
                FROM
                  users us
                  INNER JOIN master.user_role ur ON ur.kd_user = us.kd_user 
                  INNER JOIN master.jabatan_tag jt ON jt.kd_jabatan = ur.kd_jabatan
                WHERE
                  ur.aktif = '1' {$kd_jabatan}{$filter}
                  ORDER BY us.nama_user asc";
    $res = $this->db->query($strquery)->getResultArray();
    return $res;
  }

  public function cekSpMata($dataset)
  {
    $result = array();
    $params = array($dataset['kd_paramedis']);
    $strquery = "select kd_spesialisasi FROM para_medis where kd_paramedis = ?";
    $res = $this->db->query($strquery, $params)->getResultArray();
    $nilai = 0;
    if (count($res) > 0) {
      if ($res[0]['kd_spesialisasi'] == 16) $nilai = 1;
      array_push($result, array("nilai" => $nilai));
    }
    return $result;
  }

  public function getChangeUnit($dataset)
  {
    $filterRuang = '';
    if (isset($dataset['tag'])) {
      if ($dataset['tag'] != 'semua') $filterRuang = "AND rp.tag = '{$dataset['tag']}'";
    }

    $filterKelas = '';
    if (isset($dataset['kd_ruang'])) {
      if ($dataset['kd_ruang'] != 'semua') $filterKelas = "AND u.kd_unit = '{$dataset['kd_ruang']}'";
    }
    
    $strquery = "SELECT
                  u.kd_unit,
                  u.nama_unit,
                  u.nama_unit tag_unit,
                  i.kd_instalasi,
                  i.nama_instalasi,
                  ki.tag tag_instalasi,
                  u.kd_unit parent_unit
                FROM
                  ruang_perawatan rp
                  INNER JOIN unit u ON u.kd_ruangperawatan = rp.kd_ruangperawatan
                  INNER JOIN master.unit_tag ut ON ut.kd_unit = u.kd_unit
                  INNER JOIN instalasi i ON i.kd_instalasi = u.kd_instalasi
                  INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
                WHERE
                  1 = 1
                  AND rp.status_aktif = 1
                  {$filterRuang}
                  {$filterKelas}";
    $res = $this->db->query($strquery);
    return $res->getResultArray();
  }
    
  public function changePassword($dataset)
  {
    $zona = intval(getenv('app.zona'));
    try {
      $tgl_entry = gmdate("Y-m-d H:i:s", time() + 60 * 60 * $zona);
      $kd_user = $dataset['kd_user'];
      $kd_instalasi = $dataset['kd_instalasi'];
      $kd_unit = $dataset['kd_unit'];
      $password_lama = $dataset['password_lama'];
      $password_baru = $dataset['password_baru'];
      $password_baru_ulangi = $dataset['password_baru_ulangi'];
    } catch (\Exception $e) {
      echo 'ini yg kurang besti: ' . $e->getMessage();
      return array("code" => 400, "msg" => "");
    }

    $this->db->transBegin();

    $params = array($kd_user);
    $strquery = "select username from users where kd_user = ?";
    $res = $this->db->query($strquery, $params);
    $username = '';
    if ($res->getNumRows() > 0) {
      foreach ($res->getResult() as $row) {
        $username = $row->username;
      }
    }

    $params = array(pg_escape_string($username), md5(pg_escape_string($password_lama)), $kd_user);
    $strquery = "select * from users where username = ? and password = ? and kd_user = ? and status_hapus = '1'";
    $res = $this->db->query($strquery, $params);
    $ada = 0;
    if ($res->getNumRows() > 0) {
      $ada = 1;
    }

    if ($ada == 1) {
      if ($password_baru != $password_baru_ulangi) {
        return array("code" => 406, "msg" => "Ulangi password tidak sama!!!");
      }

      //update password
      $params = array(md5($password_baru), $kd_user);
      $strquery = "update users set password = ? where kd_user = ?";
      $this->db->query($strquery, $params);

      //log
      $params = array($kd_user, $tgl_entry, $password_lama, $password_baru, $kd_instalasi, $kd_unit);
      $strquery = "insert into rme.ganti_password_log (kd_user, tgl_entry, password_lama, password_baru, kd_instalasi, kd_unit) values (?, ?, ?, ?, ?, ?)";
      $this->db->query($strquery, $params);
    } else {
      return array("code" => 406, "msg" => "Password lama tidak sesuai!!!");
    }

    if ($this->db->transStatus() === false && $this->db->error()) {
      $this->db->transRollback();
      $result = array("code" => 304, "msg" => "Ganti password gagal!!");
    } else {
      $this->db->transCommit();
      $result = array("code" => 200, "msg" => "Ganti password sukses");
    }
    return $result;
  }

  public function getInstalasi($dataset)
  {
    $filter = "";
    if (isset($dataset['khusus'])) {
      if ($dataset['khusus'] == 'rehab') {
        $filter .= "AND kd_instalasi in (1,3)";
      }
    }
    $strquery = "SELECT * FROM instalasi WHERE 1=1 {$filter} ORDER BY kd_instalasi ASC";
    $res = $this->db->query($strquery);
    return $res->getResultArray();
  }

  public function loadUserRole($dataset)
  {
    $params = array($dataset['kd_user']);
    $strquery = "SELECT
                  ur.*,
                  jp.nama_jabatanparamedis,
                  jp.rme
                FROM
                  master.user_role ur
                  INNER JOIN jabatan_paramedis jp ON jp.kd_jabatanparamedis = ur.kd_jabatan
                WHERE
                  ur.aktif = 1
                  AND ur.kd_user = ?
                ORDER BY
                  jp.nama_jabatanparamedis";
    $res = $this->db->query($strquery, $params);
    return $res->getResultArray();
  }

  public function changeRole($dataset)
  {
    $params = array($dataset['kd_user'], $dataset['kd_jabatan']);
    $strquery = "SELECT
                  jp.kd_jabatanparamedis,
                  jp.nama_jabatanparamedis,
                  jp.rme,
                  COALESCE(jp.kd_instalasi, u.kd_instalasi) kd_instalasi,
                  COALESCE(i2.nama_instalasi, i.nama_instalasi) nama_instalasi,
                  COALESCE(ki2.tag, ki.tag) tag_instalasi,
                  COALESCE(jp.kd_unit, u.kd_unit) kd_unit,
                  COALESCE(u2.nama_unit, u.nama_unit) nama_unit
                FROM
                  users us
                  INNER JOIN master.user_role ur ON ur.kd_user = us.kd_user
                  INNER JOIN jabatan_paramedis jp ON jp.kd_jabatanparamedis = ur.kd_jabatan
                  INNER JOIN unit u ON u.kd_unit = us.unit
                  INNER JOIN instalasi i ON i.kd_instalasi = u.kd_instalasi
                  INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
                  LEFT JOIN unit u2 ON u2.kd_unit = jp.kd_unit
                  LEFT JOIN instalasi i2 ON i2.kd_instalasi = jp.kd_instalasi
                  LEFT JOIN kelompok_instalasi ki2 ON ki2.kd_kelompok_instalasi = i2.kd_kelompok_instalasi
                WHERE
                  us.kd_user = ?
                  AND ur.kd_jabatan = ?";
    $res = $this->db->query($strquery, $params);
    return $res->getResultArray();
  }
}
