<?php

namespace App\Controllers;

use App\Models\MLogin;
use App\Models\MUser;
use App\Models\RawatInap\MRuangan;

class Login extends BaseController
{
  private function getDefaultModules($model, $rowset): array
  {
    $modules = array();
    $rsm = $model->getModules($rowset['kd_instalasi'], $rowset['kd_unit'], $rowset['kd_jabatan'], $rowset['kd_user'], null, true);
    foreach ($rsm as $key => $value) {
      $readwrite = json_decode($value['readwrite'], true);
      $mode = findIndexInArray($readwrite, 'id', intval($rowset['kd_jabatan']));
      $permission = 'ro';
      if ($mode !== false) $permission = $readwrite[$mode]['mode'];
      $filename = $value['instalasi'] == '*' ? $value['module_filename'] : $value['instalasi'] . '/' . $value['module_filename'];
      array_push($modules, array("title" => $value['title'], "tag" => $value['tag'], "filename" => $filename, "icon" => $value['icon'], "mode" => $permission));
    }
    return $modules;
  }

  private function getPrivileges($model, $rowset): array
  {
    $rsg = $model->getModuleGroup();
    $groups = array();
    foreach ($rsg as $key => $value) {
      $rsm = $model->getModules($rowset['kd_instalasi'], $rowset['kd_unit'], $rowset['kd_jabatan'], $rowset['kd_user'], $value['id']);
      $modules = array();
      foreach ($rsm as $key_ => $value_) {
        $readwrite = json_decode($value_['readwrite'], true);
        $mode = findIndexInArray($readwrite, 'id', intval($rowset['kd_jabatan']));
        $permission = 'ro';
        if ($mode !== false) $permission = $readwrite[$mode]['mode'];
        $filename = $value_['instalasi'] == '*' ? $value_['module_filename'] : $value_['instalasi'] . '/' . $value_['module_filename'];
        array_push($modules, array("title" => $value_['title'], "tag" => $value_['tag'], "filename" => $filename, "icon" => $value_['icon'], "mode" => $permission));
      }
      array_push($groups, array("id" => $value['id'], "title" => $value['title'], "tag" => $value['tag'], "modules" => $modules, "evo" => $value['evo'], "icon" => $value['icon']));
    }
    return $groups;
  }

  public function defaultModules(): string
  {
    $request = request();
    $model = new MLogin;
    $code = 204;
    $results = array();
    $rs = $this->getDefaultModules($model, $request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function auth(): string
  {
    $request = request();
    $model = new MLogin;
    $userModel = new MUser;
    $ruanganModel = new MRuangan;
    $param = array("username" => $request->getPost('username'), "password" => $request->getPost('password'));
    $code = 204;
    $results = array();
    $rs = $model->authv2($param);
    if (count($rs) > 0) {
      $rs[0]['all_dpjp'] = $rs[0]['all_dpjp'] == 'f' ? false : true;
      $units = $userModel->getUserUnit($rs[0]);
      if ($rs[0]['kd_unit'] == '0') {
        $rs[0]['kd_unit'] = $units[0]['kd_unit'];
        $rs[0]['nama_unit'] = $units[0]['nama_unit'];
        $rs[0]['tag_unit'] = $units[0]['tag_unit'];
        $rs[0]['kd_instalasi'] = $units[0]['kd_instalasi'];
        $rs[0]['nama_instalasi'] = $units[0]['nama_instalasi'];
        $rs[0]['tag_instalasi'] = $units[0]['tag_instalasi'];
        $rs[0]['kd_ruang'] = $units[0]['kd_ruang'];
        $rs[0]['nama_ruang'] = $units[0]['nama_ruang'];
        $rs[0]['all_dpjp'] = $units[0]['all_dpjp'] == 'f' ? false : true;
      }
      $parent_unit = $model->getParentUnit($rs[0]['kd_unit']);
      $landing_page = $model->getLandingPage($rs[0]['kd_unit']);
      $rs[0]['parent_unit'] = $parent_unit;
      $rs[0]['units'] = $units;
      if ($rs[0]['jabatan'] == "dokter") {
        $ruangan = $ruanganModel->getRuangan();
        foreach ($ruangan as $key => $value) {
          $ruangan[$key]['ruangan'] = $ruanganModel->getRuanganByTag(array("kd_ruang" => $value['kd_ruangperawatan']));
        }
      } else {
        $ruangan = $userModel->getRuanganUser(array("kd_user" => $rs[0]['kd_user']));
        foreach ($ruangan as $key => $value) {
          $ruangan[$key]['ruangan'] = $ruanganModel->getRuanganByTag(array("kd_ruang" => $value['kd_ruangperawatan'], "kd_user" => $rs[0]['kd_user']));
        }
      }
      $rs[0]['ruangan'] = $ruangan;
      $rowset['kd_instalasi'] = $rs[0]['kd_instalasi'];
      $rowset['kd_unit'] = $rs[0]['kd_unit'];
      $rowset['kd_jabatan'] = $rs[0]['kd_jabatan'];
      $rowset['kd_user'] = $rs[0]['kd_user'];
      $rs[0]['landing_page'] = $landing_page;
      $rs[0]['token'] = tokenHash($rs[0]['nama_user'], $rs[0]['pwd_user']);
      unset($rs[0]['pwd_user']);
      $defaults = $this->getDefaultModules($model, $rowset);
      $rs[0]['defaults'] = $defaults;
      $rs[0]['validtologin'] = true;

      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function privileges(): string
  {
    $request = request();
    $model = new MLogin();
    $u = $model->isValidUser($request->getGet('kd_user'), $request->getGet('token'));
    $code = 401;
    $results = array();
    if ($u) {
      $code = 200;
      $rowset['kd_instalasi'] = $request->getGet('kd_instalasi');
      $rowset['kd_unit'] = $request->getGet('kd_unit');
      $rowset['kd_jabatan'] = $request->getGet('kd_jabatan');
      $rowset['kd_user'] = $request->getGet('kd_user');
      $results = $this->getPrivileges($model, $rowset);
    }
    return sendResponse($code, $results);
  }
}
