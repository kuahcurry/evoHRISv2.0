<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MDashboard;

class Dashboard extends BaseController
{
    protected MDashboard $dashboardModel;

    public function __construct()
    {
        helper('response');
        $this->dashboardModel = new MDashboard();
    }

    /**
     * Bundle all dashboard data for instant SPA initialization
     */
    public function all(): string
    {
        try {
            $data = [
                'summary' => $this->dashboardModel->getSummary(),
                'bagian' => $this->dashboardModel->getPegawaiByBagian(),
                'pertumbuhan' => $this->dashboardModel->getPertumbuhanPegawai(),
                'status_pegawai' => $this->dashboardModel->getStatusPegawaiDistribution(),
                'pendidikan' => $this->dashboardModel->getPendidikanDistribution(),
                'kehadiran' => $this->dashboardModel->getKehadiranSummary(),
            ];
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function summary(): string
    {
        try {
            $data = $this->dashboardModel->getSummary();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function bagian(): string
    {
        try {
            $data = $this->dashboardModel->getPegawaiByBagian();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function pertumbuhan(): string
    {
        try {
            $data = $this->dashboardModel->getPertumbuhanPegawai();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function statusPegawai(): string
    {
        try {
            $data = $this->dashboardModel->getStatusPegawaiDistribution();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function pendidikan(): string
    {
        try {
            $data = $this->dashboardModel->getPendidikanDistribution();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function kehadiran(): string
    {
        try {
            $data = $this->dashboardModel->getKehadiranSummary();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function detailUltah(): string
    {
        try {
            $bulan = $this->request->getGet('bulan') !== '0';
            $data = $this->dashboardModel->getDetailUltah($bulan);
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function detailPensiun(): string
    {
        try {
            $data = $this->dashboardModel->getDetailPensiun();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }

    public function detailStrSip(): string
    {
        try {
            $data = $this->dashboardModel->getDetailStrSipExpiring();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, $th->getMessage());
        }
    }
}
