<?php

namespace App\Models;

use CodeIgniter\Model;

class MLogin extends Model
{
  public function authv2($credential)
  {
    $params = array($credential['username'], MD5($credential['password']));
    $strquery = "SELECT
                  u.kd_user,
                  u.kd_paramedis,
                  u.nama_user,
                  jp.kd_jabatanparamedis kd_jabatan,
                  jp.tag jabatan,
                  un.kd_unit,
                  un.nama_unit,
                  un.nama_unit tag_unit,
                  un.kd_instalasi,
                  i.nama_instalasi,
                  ki.tag tag_instalasi,
                  FALSE all_dpjp,M
                  u.PASSWORD pwd_user,
                  du.status status_dokter_umum 
                FROM
                  users u
                  INNER JOIN unit un ON un.kd_unit = u.unit
                  INNER JOIN instalasi i ON i.kd_instalasi = un.kd_instalasi
                  INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
                  INNER JOIN master.user_role ur ON ur.kd_user = u.kd_user
                  INNER JOIN jabatan_paramedis jp ON jp.kd_jabatanparamedis = ur.kd_jabatan
                  LEFT JOIN master.user_dokter_umum du ON du.kd_user = u.kd_user 
                WHERE
                  u.username = ?
                  AND u.PASSWORD = ?
                  AND ur.aktif = 1
                ORDER BY
                  u.kd_user DESC 
                  LIMIT 1";
                // var_dump(logger($strquery,$params));die();
    $res = $this->db->query($strquery, $params);
    return $res->getResultArray();
  }
  
  public function isValidUser($kd_user, $token)
  {
    if (empty($kd_user) || empty($token)) {
      return false;
    }
    try {
      $row = $this->db->table('tb_users')
        ->select('id as kd_user, username as nama_user, pass_login as password, token')
        ->where('id', $kd_user)
        ->orWhere('username', $kd_user)
        ->get()->getRowArray();
      if (!$row) {
        $row = $this->db->table('tbl_user_login')
          ->select('id_user_login as kd_user, username as nama_user, password')
          ->where('id_user_login', $kd_user)
          ->orWhere('username', $kd_user)
          ->get()->getRowArray();
      }
      if (!$row) {
        return false;
      }
      $fromtoken = base64_encode((string)$kd_user) . $token;
      $generated = base64_encode((string)$row['kd_user']) . tokenHash($row['nama_user'], $row['password']);
      return ($fromtoken === $generated || (isset($row['token']) && $row['token'] === $token));
    } catch (\Throwable $t) {
      return false;
    }
  }

  public function getLandingPage($kd_unit)
  {
    $params = array($kd_unit);
    $strquery = "select setting from setting where lower(komponen) = 'unit_triage' and setting::int4 = ?";
    $rs1 = $this->db->query($strquery, $params);
    $strquery = "select setting from setting where lower(komponen) = 'unit_rekam_medis' and setting::int4 = ?";
    $rs2 = $this->db->query($strquery, $params);
    $landing_page = count($rs1->getResultArray()) > 0 ? 'skrining' : (count($rs2->getResultArray()) > 0 ? 'rekam_medis' : 'dashboard');
    return $landing_page;
  }

  public function getParentUnit($kd_unit)
  {
    $params = array($kd_unit);
    $strquery = "select setting from setting where lower(komponen) = 'unit_triage' and setting::int4 = ?";
    $rs = $this->db->query($strquery, $params);
    $parent_unit = $kd_unit;
    if (count($rs->getResultArray()) > 0) {
      $strquery = "select setting from setting where lower(komponen) = 'unit_igd' limit 1";
      $rss = $this->db->query($strquery, $params);
      $parent_unit = $rss->getResultArray()[0]['setting'];
    }
    return $parent_unit;
  }
  
  public function getModuleGroup()
  {
    $strquery = "select * from master.modules_group where aktif = '1' order by urutan asc";
    $res = $this->db->query($strquery);
    return $res->getResultArray();
  }
  
  public function getModules($kd_instalasi, $kd_unit, $kd_jabatan, $kd_user, $group, $autoshow = false)
  {
    $filter = $autoshow ? " and m.autoshow = true " : " and m.module_group = ? ";
    if ($autoshow) {
      $params = array("%$kd_instalasi,%", "%,$kd_instalasi%", $kd_instalasi, "%$kd_unit,%", "%,$kd_unit%", $kd_unit, $kd_jabatan, "%$kd_user,%", "%,$kd_user%", $kd_user);
    } else {
      $params = array("%$kd_instalasi,%", "%,$kd_instalasi%", $kd_instalasi, "%$kd_unit,%", "%,$kd_unit%", $kd_unit, $kd_jabatan, "%$kd_user,%", "%,$kd_user%", $kd_user, $group);
    }
    $strquery = "SELECT 
                  m.title,
                  m.module_group,
                  m.tag,
                  m.module_filename,
                  m.readwrite,
                  m.icon,
                  CASE 
                    WHEN virtual = 'f' AND m.instalasi != '*' THEN (
                        SELECT
                          ki.tag
                        FROM
                          instalasi i
                          INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
                        WHERE
                          i.kd_instalasi = m.instalasi::INT
                    )
                    WHEN virtual = 't' THEN
                      CASE
                        WHEN m.instalasi_asal != '*' THEN (
                          SELECT
                            ki.tag
                          FROM
                            instalasi i
                            INNER JOIN kelompok_instalasi ki ON ki.kd_kelompok_instalasi = i.kd_kelompok_instalasi
                          WHERE
                            TRIM(i.kd_instalasi::TEXT) = TRIM(m.instalasi_asal)
                        )
                        ELSE NULL
                      END
                    ELSE m.instalasi
                  END instalasi
                FROM
                  master.modules m
                WHERE
                  m.aktif = '1'
                  AND ( ( m.instalasi ILIKE ? OR m.instalasi ILIKE ? OR m.instalasi = ? ) OR m.instalasi = '*' ) 
                  AND ( ( m.units ILIKE ? OR m.units ILIKE ? OR m.units = ? ) OR (m.units = '*')) 
                  AND ( ( ? = ANY(string_to_array(m.user_role, ',')) ) OR M.user_role = '*' ) 
                  AND ( ( m.users ILIKE ? OR m.users ILIKE ? OR m.users = ? ) OR m.users = '*' ) 
                  {$filter}
                ORDER BY
                  m.module_group ASC,
                  m.urutan ASC";
                  // var_dump(logger($strquery, $params));die();
    $res = $this->db->query($strquery, $params);
    return $res->getResultArray();
  }
}
