<?php

namespace App\Controllers;

use App\Models\MPayroll;

class Payroll extends BaseController
{
    protected $mPayroll;

    public function __construct()
    {
        $this->mPayroll = new MPayroll();
    }

    /**
     * Master dropdowns & filters
     * GET /api/payroll/master
     */
    public function getMaster()
    {
        $kdBagian = $this->request->getGet('kd_bagian');

        $bagian  = $this->mPayroll->getBagianList();
        $jabatan = $this->mPayroll->getJabatanList();
        $pegawai = $this->mPayroll->getPegawaiList($kdBagian);

        return sendResponse(200, [
            'bagian'  => $bagian,
            'jabatan' => $jabatan,
            'pegawai' => $pegawai
        ]);
    }

    /**
     * List payroll records with filters & summary
     * GET /api/payroll/list
     */
    public function list()
    {
        $filters = [
            'kd_bagian'      => $this->request->getGet('kd_bagian'),
            'id_pegawai'     => $this->request->getGet('id_pegawai'),
            'status_approve' => $this->request->getGet('status_approve'),
            'periode'        => $this->request->getGet('periode'),
            'tgl_mulai'      => $this->request->getGet('tgl_mulai'),
            'tgl_sampai'     => $this->request->getGet('tgl_sampai'),
            'search'         => $this->request->getGet('search'),
        ];

        $limit  = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mPayroll->getPayrollList($filters, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Get single payroll detail
     * GET /api/payroll/detail/(:num)
     */
    public function detail($idGaji = null)
    {
        if (empty($idGaji)) {
            return sendResponse(400, ['message' => 'Parameter id_gaji diperlukan']);
        }

        $detail = $this->mPayroll->getPayrollDetail((int)$idGaji);
        if (!$detail) {
            return sendResponse(204, ['message' => 'Data penggajian tidak ditemukan']);
        }

        return sendResponse(200, $detail);
    }

    /**
     * Batch generate payroll for period
     * POST /api/payroll/generate
     */
    public function generate()
    {
        $raw = $this->request->getJSON(true) ?? $this->request->getPost();

        $periode      = $raw['periode'] ?? date('Y-m');
        $kdBagian     = !empty($raw['kd_bagian']) ? $raw['kd_bagian'] : null;
        $defaultGapok = !empty($raw['default_gapok']) ? (float)$raw['default_gapok'] : 3500000;
        $defaultUmk   = !empty($raw['default_umk']) ? (float)$raw['default_umk'] : 3000000;
        $nipFilter    = $raw['nip_filter'] ?? 'all';

        $res = $this->mPayroll->generatePayrollBatch($periode, $kdBagian, $defaultGapok, $defaultUmk, $nipFilter);
        return sendResponse(200, [
            'message' => "Penggajian periode {$periode} berhasil diproses",
            'result'  => $res
        ]);
    }

    /**
     * Save/update single payroll record
     * POST /api/payroll/save
     */
    public function save()
    {
        $raw = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($raw['id_pegawai'])) {
            return sendResponse(400, ['message' => 'ID Pegawai wajib diisi']);
        }

        $res = $this->mPayroll->savePayrollItem($raw);
        return sendResponse(200, [
            'message' => 'Data penggajian berhasil disimpan',
            'result'  => $res
        ]);
    }

    /**
     * Delete payroll record
     * POST /api/payroll/delete/(:num) or DELETE
     */
    public function delete($idGaji = null)
    {
        if (empty($idGaji)) {
            $raw = $this->request->getJSON(true) ?? $this->request->getPost();
            $idGaji = $raw['id_gaji'] ?? null;
        }

        if (empty($idGaji)) {
            return sendResponse(400, ['message' => 'ID Gaji diperlukan']);
        }

        $this->mPayroll->deletePayrollItem((int)$idGaji);
        return sendResponse(200, ['message' => 'Data penggajian berhasil dihapus']);
    }

    /**
     * Batch approve payroll records
     * POST /api/payroll/approve
     */
    public function approve()
    {
        $raw = $this->request->getJSON(true) ?? $this->request->getPost();
        $ids = $raw['ids'] ?? [];
        $status = isset($raw['status']) ? (int)$raw['status'] : 1;

        if (empty($ids) || !is_array($ids)) {
            return sendResponse(400, ['message' => 'Pilih data gaji yang akan diapprove']);
        }

        $affected = $this->mPayroll->approvePayroll($ids, $status);
        return sendResponse(200, [
            'message' => "Sebanyak {$affected} data gaji berhasil diperbarui statusnya",
            'affected' => $affected
        ]);
    }

    /**
     * Rekapitulasi Gaji per Bagian
     * GET /api/payroll/rekap
     */
    public function rekap()
    {
        $periode  = $this->request->getGet('periode') ?? date('Y-m');
        $kdBagian = $this->request->getGet('kd_bagian');

        $result = $this->mPayroll->getRekapGaji($periode, $kdBagian);
        return sendResponse(200, $result);
    }

    /**
     * Rekap Potongan Absensi & Keterlambatan
     * GET /api/payroll/potongan-absensi
     */
    public function potonganAbsensi()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-t');
        $kdBagian  = $this->request->getGet('kd_bagian');

        $result = $this->mPayroll->getPotonganAbsensi($startDate, $endDate, $kdBagian);
        return sendResponse(200, $result);
    }

    /**
     * Master Tarif & Komponen
     * GET /api/payroll/master-tarif
     */
    public function masterTarif()
    {
        $data = $this->mPayroll->getMasterTarif();
        return sendResponse(200, $data);
    }

    /**
     * Save Master Gaji Pokok
     * POST /api/payroll/master-tarif/gaji
     */
    public function saveTarifGaji()
    {
        $raw = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($raw['kd_jabatan']) || !isset($raw['gaji_pokok'])) {
            return sendResponse(400, ['message' => 'Jabatan dan Gaji Pokok wajib diisi']);
        }

        $res = $this->mPayroll->saveMasterTarifGaji($raw);
        return sendResponse(200, ['message' => 'Tarif gaji pokok berhasil disimpan', 'data' => $res]);
    }

    /**
     * Save Master UMK
     * POST /api/payroll/master-tarif/umk
     */
    public function saveTarifUmk()
    {
        $raw = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($raw['kd_jabatan']) || empty($raw['tahun_umk']) || !isset($raw['umk'])) {
            return sendResponse(400, ['message' => 'Jabatan, Tahun, dan Nominal UMK wajib diisi']);
        }

        $res = $this->mPayroll->saveMasterTarifUmk($raw);
        return sendResponse(200, ['message' => 'Data UMK berhasil disimpan', 'data' => $res]);
    }

    /**
     * Save Master Potongan
     * POST /api/payroll/master-tarif/potongan
     */
    public function saveTarifPotongan()
    {
        $raw = $this->request->getJSON(true) ?? $this->request->getPost();
        if (!isset($raw['menit_awal']) || !isset($raw['menit_akhir']) || empty($raw['Jenis_potongan']) || !isset($raw['presentase_pot'])) {
            return sendResponse(400, ['message' => 'Parameter aturan potongan tidak lengkap']);
        }

        $res = $this->mPayroll->saveMasterPotongan($raw);
        return sendResponse(200, ['message' => 'Aturan potongan absensi berhasil disimpan', 'data' => $res]);
    }

    /**
     * Delete Master Potongan
     * POST /api/payroll/master-tarif/potongan/delete/(:num)
     */
    public function deleteTarifPotongan($idPotongan = null)
    {
        if (empty($idPotongan)) {
            $raw = $this->request->getJSON(true) ?? $this->request->getPost();
            $idPotongan = $raw['id_potongan'] ?? null;
        }

        if (empty($idPotongan)) {
            return sendResponse(400, ['message' => 'ID Potongan diperlukan']);
        }

        $this->mPayroll->deleteMasterPotongan((int)$idPotongan);
        return sendResponse(200, ['message' => 'Aturan potongan berhasil dihapus']);
    }
}
