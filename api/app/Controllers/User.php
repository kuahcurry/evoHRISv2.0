<?php

namespace App\Controllers;

use CodeIgniter\CodeIgniter;
use App\Models\MUser;

class User extends BaseController
{
  public function __construct()
  {
    $this->model = new MUser();
  }

  public function getUserUnit(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getUserUnit($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function saveDefaultUnit(): string
  {
    $request = request();
    $code = 304;
    $results = array();
    $rs = $this->model->saveDefaultUnit($request->getPost());
    if ($rs) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function getKelPasien(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getKelPasien($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function getJKelPasien(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getJKelPasien($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function getPekerjaan(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getPekerjaan($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function getPendidikan(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getPendidikan($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function getDokumen(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getDokumen($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function loadUsers(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->loadUsers($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function cekSpMata(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->cekSpMata($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function getChangeUnit(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getChangeUnit($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function changePassword(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->changePassword($request->getPost());
    if (count($rs) > 0) {
      $code = $rs['code'];
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function getInstalasi(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->getInstalasi($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function loadUserRole(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->loadUserRole($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }

  public function changeRole(): string
  {
    $request = request();
    $code = 204;
    $results = array();
    $rs = $this->model->changeRole($request->getGet());
    if (count($rs) > 0) {
      $code = 200;
      $results = $rs;
    }
    return sendResponse($code, $results);
  }
}
