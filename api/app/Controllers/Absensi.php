<?php

namespace App\Controllers;

use App\Models\MAbsensi;

class Absensi extends BaseController
{
    protected $mAbsensi;

    public function __construct()
    {
        $this->mAbsensi = new MAbsensi();
    }

    /**
     * Master data for Absensi filters (shifts, bagian)
     */
    public function getMaster()
    {
        $shifts = $this->mAbsensi->getMasterShift();
        $bagian = $this->mAbsensi->getBagianList();

        return sendResponse(200, [
            'shifts' => $shifts,
            'bagian' => $bagian
        ]);
    }

    /**
     * Monthly attendance recap matrix
     * GET /api/absensi/rekap-bulanan?bulan=YYYY-MM&kd_bagian=...&id_pegawai=...
     */
    public function getRekapBulanan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $kdBagian = $this->request->getGet('kd_bagian');
        $idPegawai = $this->request->getGet('id_pegawai');

        $result = $this->mAbsensi->getRekapBulanan($bulan, $kdBagian, $idPegawai);
        return sendResponse(200, $result);
    }

    /**
     * Attendance status daily/range list
     * GET /api/absensi/status?tgl_mulai=...&tgl_sampai=...&kd_bagian=...&status=...&search=...&limit=50&offset=0
     */
    public function getStatus()
    {
        $tglMulai = $this->request->getGet('tgl_mulai') ?? date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?? date('Y-m-d');
        $kdBagian = $this->request->getGet('kd_bagian');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search') ?? $this->request->getGet('keyword');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getStatusRekap($tglMulai, $tglSampai, $kdBagian, $status, $search, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Attendance status counter summary
     * GET /api/absensi/status-summary?tgl_mulai=...&tgl_sampai=...&kd_bagian=...
     */
    public function getStatusSummary()
    {
        $tglMulai = $this->request->getGet('tgl_mulai') ?? date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?? date('Y-m-d');
        $kdBagian = $this->request->getGet('kd_bagian');

        $result = $this->mAbsensi->getStatusSummary($tglMulai, $tglSampai, $kdBagian);
        return sendResponse(200, $result);
    }

    /**
     * Scheduling progress by department
     * GET /api/absensi/jadwal-monitoring?bulan=YYYY-MM
     */
    public function getJadwalMonitoring()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $result = $this->mAbsensi->getJadwalMonitoring($bulan);
        return sendResponse(200, $result);
    }

    /**
     * Shift swap applications
     * GET /api/absensi/tukar-shift?kd_bagian=...&status=...&limit=50&offset=0
     */
    public function getTukarShift()
    {
        $kdBagian = $this->request->getGet('kd_bagian');
        $status = $this->request->getGet('status');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getTukarShift($kdBagian, $status, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Overtime requests
     * GET /api/absensi/lembur?bulan=YYYY-MM&kd_bagian=...&status=...&limit=50&offset=0
     */
    public function getLembur()
    {
        $bulan = $this->request->getGet('bulan');
        $kdBagian = $this->request->getGet('kd_bagian');
        $status = $this->request->getGet('status');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getLembur($bulan, $kdBagian, $status, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Morning assembly attendance events
     * GET /api/absensi/apel?tgl_mulai=...&tgl_sampai=...
     */
    public function getApel()
    {
        $tglMulai = $this->request->getGet('tgl_mulai');
        $tglSampai = $this->request->getGet('tgl_sampai');

        $result = $this->mAbsensi->getApel($tglMulai, $tglSampai);
        return sendResponse(200, $result);
    }

    /**
     * Raw biometric punch logs from machines
     * GET /api/absensi/log-mesin?tanggal=YYYY-MM-DD&pin=...&sn=...&limit=50&offset=0
     */
    public function getLogMesin()
    {
        $tanggal = $this->request->getGet('tanggal');
        $pin = $this->request->getGet('pin');
        $sn = $this->request->getGet('sn');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getLogMesin($tanggal, $pin, $sn, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Detailed punch scans for an employee on a specific date
     * GET /api/absensi/log-detail?pin=...&tanggal=YYYY-MM-DD
     */
    public function getLogDetail()
    {
        $pin = $this->request->getGet('pin');
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        if (empty($pin)) {
            return sendResponse(400, null, 'Parameter pin wajib disertakan');
        }

        $result = $this->mAbsensi->getLogDetail($pin, $tanggal);
        return sendResponse(200, $result);
    }
}
