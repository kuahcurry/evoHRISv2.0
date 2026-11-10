<?php

namespace App\Controllers;

use CodeIgniter\CodeIgniter;
use App\Models\MCommon;

class Common extends BaseController
{
    public function __construct()
    {
        $this->model = new MCommon();
    }
    public function index(): string
    {
        $version = CodeIgniter::CI_VERSION;
        return json_encode(array('code' => 200, 'status' => 'Welcome to our services, powered by CodeIgniter v' . $version, 'version' => 'v1.0.2'));
    }
    public function getCurrentShift()
    {
        try {
            return sendResponse(200, [
                'shift' => $this->model->getShift(),
                'date'  => $this->model->getCurrentDate(),
                'datetime' => \App\Models\MCommon::currentDateTime()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Current shift: '.$e->getMessage()
            );

            return sendResponse(500, [
                'message' => 'Shift berjalan gagal dimuat.',
            ]);
        }
    }
    public function getUsia(): string
    {
        $request = request();
        $date_format = array("dd-mm-yyyy" => "d-m-Y", "dd/mm/yyyy" => "d/m/Y", "yyyy-mm-dd" => "Y-m-d", "yyyy/mm/dd" => "Y/m/d");
        $tgl_lahir = $request->getGet('tgl_lahir');
        $format = ($date_format[$request->getGet('format')] == null) ? "Y-m-d" : $date_format[$request->getGet('format')];
        return json_encode(array("code" => 200, "status" => "ok", "data" => array(calculateAge($tgl_lahir, $format))));
    }
    public function getTglLahir(): string
    {
        $request = request();
        $param = array();
        $code = 204;
        $results = array();
        $rs = $this->model->getTglLahir($request->getGet());
        if (count($rs) > 0) {
            $code = 200;
            $results = $rs;
        }
        return sendResponse($code, $results);
    }

    public function getJenisKelamin()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbjeniskelamin($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getGolonganDarah()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbgolongandarah($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getBahasa()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbbahasa($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getAgama()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbagama($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getPendidikan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbpendidikan($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getPekerjaan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbpekerjaan($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getKebangsaan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbkebangsaan($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getStatusPasien()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbstatuspasien($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getSuku()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbsuku($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getPropinsi()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbpropinsi($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getKabupaten()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $kdPropinsi = trim((string)($request->getGet('kd_propinsi') ?? ''));
        $rs = $this->model->mgetcmbkabupaten($keyword, $kdPropinsi);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getKecamatan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $kdKabupaten = trim((string)($request->getGet('kd_kabupaten') ?? ''));
        $rs = $this->model->mgetcmbkecamatan($keyword, $kdKabupaten);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getKelurahan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $kdKecamatan = trim((string)($request->getGet('kd_kecamatan') ?? ''));
        $rs = $this->model->mgetcmbkelurahan($keyword, $kdKecamatan);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getIdentitas()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbidentitas($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getDokter()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbdokter($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getDokterPengirim()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbdokterkirim($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getInstalasi()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $in = trim((string)($request->getGet('in') ?? ''));
        $visibleOnly = filter_var($request->getGet('view') ?? false, FILTER_VALIDATE_BOOLEAN);
        $rs = $this->model->mgetcmbinstalasi($keyword, $in, $visibleOnly);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getUnit()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $kdInstalasi = trim((string)($request->getGet('kd_instalasi') ?? ''));
        $rs = $this->model->mgetcmbunit($keyword, $kdInstalasi);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getUser()
    {
        $dataset = request()->getGet();
        $rs = $this->model->mgetcmbuser($dataset);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getKelPenjamin()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbkelpenjamin($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getPenjamin()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbpenjamin($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getPerusahaan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $kdKelpenjamin = trim((string)($request->getGet('kd_kelpenjamin') ?? ''));
        $rs = $this->model->mgetcmbperusahaan($keyword, $kdKelpenjamin);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getPenanggungJawab()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbpenanggungjwb($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getCaraMasuk()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbrujukanasal($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getRujukan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $kdRujukanAsal = trim((string)($request->getGet('kd_rujukanasal') ?? ''));
        $rs = $this->model->mgetcmbrujukandetail($keyword, $kdRujukanAsal);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getNama()
    {
        return $this->getRujukan();
    }

    public function getTujuanKunjungan()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbtujuankunjungan($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getShift()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbshift($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getCaraKeluar()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetCaraKeluar($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getDiagnosa()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $rs = $this->model->mgetcmbdiagnosa($keyword);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getJenisAkses(): string
    {
        $request = request();

        $rs = $this->model->mgetJenisAkses(
            $request->getGet('keyword') ?? ''
        );

        return sendResponse(
            $rs['code'],
            $rs['data']
        );
    }

    public function getRujukanAsal(): string
    {
        $request = request();

        $rs = $this->model->mgetRujukanAsal(
            $request->getGet('keyword') ?? ''
        );

        return sendResponse(
            $rs['code'],
            $rs['data']
        );
    }


    public function getRujukanDetail(): string
    {
        $request = request();

        $rs = $this->model->mgetRujukanDetail(
            $request->getGet('kd_rujukanasal') ?? '',
            $request->getGet('keyword') ?? ''
        );

        return sendResponse(
            $rs['code'],
            $rs['data']
        );
    }

    public function getRuangPerawatan(): string
    {
        $request = request();

        $keyword =
            $request->getGet('keyword')
            ?? '';

        $rs =
            $this->model
                ->mgetRuangPerawatan($keyword);

        return sendResponse(
            $rs['code'],
            $rs['data']
        );
    }

    public function getCaraPemesanan(): string
    {
        $request = request();

        $keyword =
            $request->getGet('keyword')
            ?? '';

        $rs =
            $this->model
                ->mgetCaraPemesanan(
                    $keyword
                );

        return sendResponse(
            $rs['code'],
            $rs['data']
        );
    }

    public function getCaraBayar()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $in = trim((string)($request->getGet('in') ?? ''));
        $rs = $this->model->mgetcmbbayar($keyword,$in);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }

    public function getCounter()
    {
        $request = request();
        $keyword = trim((string)($request->getGet('keyword') ?? ''));
        $in = trim((string)($request->getGet('in') ?? ''));
        $rs = $this->model->mgetcmbcounter($keyword, $in);
        $code = intval($rs['code'] ?? 204);
        return sendResponse($code, $rs['data'] ?? []);
    }
     public function getKelurahanGrid()
    {
        $request = request();
        $rs = $this->model->mgetcmbkelurahanGrid(
            trim((string)($request->getGet('keyword') ?? ''))
        );
        return sendResponse($rs['code'], $rs['data']);
    }
     public function getReferensiBarangFarmasi()
    {
        return sendResponse(200, $this->model->getReferensiBarangFarmasi());
    }

    public function getBarangFarmasi()
    {
        return sendResponse(200, $this->model->getBarangFarmasi(request()->getGet()));
    }

    public function getAkunTransaksi()
{
    return sendResponse(
        200,
        $this->model->getAkunTransaksi(
            trim((string)(request()->getGet('keyword') ?? ''))
        )
    );
}

public function getVendorFarmasi()
{
    return sendResponse(
        200,
        $this->model->getVendorFarmasi(request()->getGet())
    );
}

public function saveDefaultSetting()
    {
        try {
            $encoded = request()->getPost('values');
            if (!is_string($encoded)) throw new \DomainException('Format setting tidak valid.');
            $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);
            if ($decoded === false) throw new \DomainException('Format setting tidak valid.');
            $values = json_decode($decoded, true);
            return sendResponse(200, $this->model->saveDefaultSetting(
                request()->getPost('menu'), request()->getPost('kd_user'), $values
            ));
        } catch (\DomainException $e) {
            return sendResponse(422, ['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            log_message('error', 'Simpan default setting gagal: ' . $e->getMessage());
            return sendResponse(500, ['message' => 'Default setting gagal disimpan.']);
        }
    }

    public function getDefaultSetting()
    {
        try {
            return sendResponse(200, $this->model->getDefaultSetting(
                request()->getGet('menu'),
                request()->getGet('kd_user')
            ));
        } catch (\DomainException $e) {
            return sendResponse(422, ['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            log_message('error', 'Default setting gagal: ' . $e->getMessage());
            return sendResponse(500, ['message' => 'Default setting gagal dimuat.']);
        }
    }
}
