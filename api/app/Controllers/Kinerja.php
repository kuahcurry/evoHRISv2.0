<?php

namespace App\Controllers;

use App\Models\MKinerja;

class Kinerja extends BaseController
{
    protected $mKinerja;

    public function __construct()
    {
        $this->mKinerja = new MKinerja();
    }

    /**
     * Master data for filters & modal selects
     * GET /api/kinerja/master
     */
    public function getMaster()
    {
        $bagian = $this->mKinerja->getBagianList();
        $satuan = $this->mKinerja->getMasterSatuan();
        $perilaku = $this->mKinerja->getMasterPerilaku();
        $standar = $this->mKinerja->getMasterStandarPenilaian();

        return sendResponse(200, [
            'bagian'            => $bagian,
            'satuan_output'     => $satuan['output'],
            'satuan_waktu'      => $satuan['waktu'],
            'master_perilaku'   => $perilaku,
            'standar_penilaian' => $standar
        ]);
    }

    /**
     * Overview stats summary
     * GET /api/kinerja/summary
     */
    public function summary()
    {
        $stats = $this->mKinerja->getKinerjaSummary();
        return sendResponse(200, $stats);
    }

    /**
     * 1. PROGRAM KERJA
     * GET /api/kinerja/program-kerja
     */
    public function programKerja()
    {
        $filters = [
            'kd_bagian' => $this->request->getGet('kd_bagian') ?? $this->request->getGet('bagian'),
            'search'    => $this->request->getGet('search')
        ];
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mKinerja->getProgramKerja($filters, $limit, $offset);
        return sendResponse(200, $result);
    }

    public function saveProgramKerja()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($data['DESC_KGT'])) {
            return sendResponse(400, null, 'Uraian program kerja / kegiatan wajib diisi');
        }

        $id = $this->mKinerja->saveProgramKerja($data);
        return sendResponse(200, ['id' => $id], 'Program kerja berhasil disimpan');
    }

    public function deleteProgramKerja()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $id = (int) ($data['ID_KGT'] ?? 0);
        if ($id <= 0) {
            return sendResponse(400, null, 'ID kegiatan tidak valid');
        }

        $this->mKinerja->deleteProgramKerja($id);
        return sendResponse(200, null, 'Program kerja berhasil dihapus');
    }

    /**
     * 2. TARGET SKP
     * GET /api/kinerja/target-skp
     */
    public function targetSkp()
    {
        $filters = [
            'tahun'      => $this->request->getGet('tahun'),
            'status'     => $this->request->getGet('status'),
            'id_pegawai' => $this->request->getGet('id_pegawai'),
            'kd_bagian'  => $this->request->getGet('kd_bagian') ?? $this->request->getGet('bagian'),
            'search'     => $this->request->getGet('search')
        ];
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mKinerja->getTargetSkp($filters, $limit, $offset);
        return sendResponse(200, $result);
    }

    public function saveTargetSkp()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($data['uraian'])) {
            return sendResponse(400, null, 'Uraian target SKP wajib diisi');
        }

        $id = $this->mKinerja->saveTargetSkp($data);
        return sendResponse(200, ['id' => $id], 'Target SKP berhasil disimpan');
    }

    public function approveTargetSkp()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $id = (int) ($data['id_targetskp'] ?? 0);
        $status = (string) ($data['status'] ?? '1');
        $catatan = $data['catatan'] ?? null;

        if ($id <= 0) {
            return sendResponse(400, null, 'ID target SKP tidak valid');
        }

        $this->mKinerja->approveTargetSkp($id, $status, $catatan);
        $msg = ($status === '1') ? 'Target SKP berhasil disetujui' : 'Target SKP ditolak';
        return sendResponse(200, null, $msg);
    }

    /**
     * 3. AKTIFITAS (LOGBOOK)
     * GET /api/kinerja/aktifitas
     */
    public function aktifitas()
    {
        $filters = [
            'bulan'      => $this->request->getGet('bulan'),
            'tahun'      => $this->request->getGet('tahun'),
            'tgl_mulai'  => $this->request->getGet('tgl_mulai'),
            'tgl_sampai' => $this->request->getGet('tgl_sampai') ?? $this->request->getGet('tgl_selesai'),
            'kd_bagian'  => $this->request->getGet('kd_bagian') ?? $this->request->getGet('bagian'),
            'status'     => $this->request->getGet('status'),
            'id_pegawai' => $this->request->getGet('id_pegawai'),
            'search'     => $this->request->getGet('search')
        ];
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mKinerja->getAktifitas($filters, $limit, $offset);
        return sendResponse(200, $result);
    }

    public function saveAktifitas()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($data['aktifitas'])) {
            return sendResponse(400, null, 'Uraian aktifitas harian wajib diisi');
        }

        $id = $this->mKinerja->saveAktifitas($data);
        return sendResponse(200, ['id' => $id], 'Aktifitas harian berhasil dicatat');
    }

    public function approveAktifitas()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $id = (int) ($data['id_aktifitas'] ?? 0);
        $status = (string) ($data['status'] ?? '1');
        $catatan = $data['catatan'] ?? null;

        if ($id <= 0) {
            return sendResponse(400, null, 'ID aktifitas tidak valid');
        }

        $this->mKinerja->approveAktifitas($id, $status, $catatan);
        $msg = ($status === '1') ? 'Aktifitas berhasil disetujui' : 'Aktifitas ditolak';
        return sendResponse(200, null, $msg);
    }

    /**
     * 4. REALISASI SKP
     * GET /api/kinerja/realisasi-skp
     */
    public function realisasiSkp()
    {
        $filters = [
            'tahun'      => $this->request->getGet('tahun'),
            'kd_bagian'  => $this->request->getGet('kd_bagian') ?? $this->request->getGet('bagian'),
            'status'     => $this->request->getGet('status'),
            'id_pegawai' => $this->request->getGet('id_pegawai'),
            'search'     => $this->request->getGet('search')
        ];
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mKinerja->getRealisasiSkp($filters, $limit, $offset);
        return sendResponse(200, $result);
    }

    public function saveRealisasiSkp()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($data['id_target'])) {
            return sendResponse(400, null, 'Target SKP rujukan wajib dipilih');
        }

        $id = $this->mKinerja->saveRealisasiSkp($data);
        return sendResponse(200, ['id' => $id], 'Realisasi SKP berhasil disimpan');
    }

    /**
     * 5. PENILAIAN & EVALUASI KINERJA
     * GET /api/kinerja/penilaian
     */
    public function penilaian()
    {
        $filters = [
            'periode'    => $this->request->getGet('periode'),
            'id_pegawai' => $this->request->getGet('id_pegawai'),
            'search'     => $this->request->getGet('search')
        ];
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mKinerja->getPenilaian($filters, $limit, $offset);
        return sendResponse(200, $result);
    }

    public function detailPerilaku()
    {
        $idPegawai = $this->request->getGet('id_pegawai') ?? '1';
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $result = $this->mKinerja->getDetailPerilaku($idPegawai, $tahun);
        return sendResponse(200, $result);
    }

    public function saveDetailPerilaku()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($data['id_pegawai'])) {
            return sendResponse(400, null, 'ID Pegawai wajib diisi');
        }

        $this->mKinerja->saveDetailPerilaku($data);
        return sendResponse(200, null, 'Nilai perilaku pegawai berhasil disimpan');
    }
}
