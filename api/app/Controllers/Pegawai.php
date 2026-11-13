<?php

namespace App\Controllers;

use App\Models\MPegawai;

class Pegawai extends BaseController
{
    protected $mPegawai;

    public function __construct()
    {
        $this->mPegawai = new MPegawai();
    }

    public function getPegawaiUnit()
    {
        $keyword = $this->request->getGet('keyword') ?? $this->request->getGet('search');
        $params  = $keyword !== null ? ['keyword' => $keyword] : [];
        $data    = $this->mPegawai->getPegawaiUnit($params);
        return sendResponse(200, $data);
    }

    public function postPegawai()
    {
        $jsonData = $this->request->getJSON(true);
        $data = !empty($jsonData) ? $jsonData : $this->request->getPost();
        if (empty($data)) {
            $data = $this->request->getRawInput() ?? [];
        }

        // VALIDASI INPUT & PARAMETER
        if (empty($data['NAMA']) || strlen(trim($data['NAMA'])) < 3) {
            return sendResponse(400, null, 'Nama lengkap wajib diisi minimal 3 karakter');
        }

        if (!empty($data['NO_IDENTITAS'])) {
            $nik = preg_replace('/[^0-9]/', '', $data['NO_IDENTITAS']);
            if (strlen($nik) !== 16) {
                return sendResponse(400, null, 'NIK / No. KTP harus terdiri dari 16 digit angka');
            }
            $data['NO_IDENTITAS'] = $nik;
        }

        if (!empty($data['EMAIL']) && !filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
            return sendResponse(400, null, 'Format alamat email tidak valid (contoh: pegawai@rumahsakit.com)');
        }

        if (isset($data['TANGGAL_LAHIR'])) {
            $data['TANGGAL_LAHIR'] = $this->normalizeDateInput($data['TANGGAL_LAHIR']);
        }

        $result = $this->mPegawai->postPegawai($data);
        return sendResponse($result['code'], $result, $result['message']);
    }

    public function updatePegawai($id = null)
    {
        $jsonData = $this->request->getJSON(true);
        $data = !empty($jsonData) ? $jsonData : $this->request->getPost();
        if (empty($data)) {
            $data = $this->request->getRawInput() ?? [];
        }

        $id = $id ?? ($data['ID_PEGAWAI'] ?? null);

        if (empty($id)) {
            return sendResponse(400, null, 'ID Pegawai diperlukan untuk update');
        }

        // VALIDASI INPUT & PARAMETER
        if (empty($data['NAMA']) || strlen(trim($data['NAMA'])) < 3) {
            return sendResponse(400, null, 'Nama lengkap wajib diisi minimal 3 karakter');
        }

        if (!empty($data['NO_IDENTITAS'])) {
            $nik = preg_replace('/[^0-9]/', '', $data['NO_IDENTITAS']);
            if (strlen($nik) !== 16) {
                return sendResponse(400, null, 'NIK / No. KTP harus terdiri dari 16 digit angka');
            }
            $data['NO_IDENTITAS'] = $nik;
        }

        if (!empty($data['EMAIL']) && !filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
            return sendResponse(400, null, 'Format alamat email tidak valid (contoh: pegawai@rumahsakit.com)');
        }

        if (isset($data['TANGGAL_LAHIR'])) {
            $data['TANGGAL_LAHIR'] = $this->normalizeDateInput($data['TANGGAL_LAHIR']);
        }

        $result = $this->mPegawai->updatePegawai($id, $data);
        return sendResponse($result['code'], $result, $result['message']);
    }

    private function normalizeDateInput($dateStr)
    {
        if (empty($dateStr)) {
            return null;
        }
        $dateStr = trim($dateStr);
        if ($dateStr === '' || $dateStr === '-' || $dateStr === '0000-00-00') {
            return null;
        }

        // Format DD-MM-YYYY atau DD/MM/YYYY
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $dateStr, $m)) {
            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $year = $m[3];
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        // Format YYYY-MM-DD atau YYYY/MM/DD
        if (preg_match('/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/', $dateStr, $m)) {
            $year = $m[1];
            $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $day = str_pad($m[3], 2, '0', STR_PAD_LEFT);
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        $ts = strtotime($dateStr);
        if ($ts !== false && $ts > 0) {
            return date('Y-m-d', $ts);
        }

        return null;
    }

    public function deletePegawai($id = null)
    {
        if (empty($id)) {
            $jsonData = $this->request->getJSON(true);
            $id = $jsonData['ID_PEGAWAI'] ?? $this->request->getPost('ID_PEGAWAI') ?? ($this->request->getRawInput()['ID_PEGAWAI'] ?? null);
        }

        if (empty($id)) {
            return sendResponse(400, null, 'ID Pegawai diperlukan untuk proses penghapusan');
        }

        $res = $this->mPegawai->deletePegawai($id);
        if (is_array($res) && isset($res['code'])) {
            return sendResponse($res['code'], $res, $res['message']);
        }
        if ($res) {
            return sendResponse(200, ['message' => 'Pegawai berhasil dinonaktifkan (soft delete)'], 'Pegawai berhasil dinonaktifkan');
        } else {
            return sendResponse(500, null, 'Gagal menonaktifkan data pegawai');
        }
    }

    public function getProvinsi()
    {
        return sendResponse(200, $this->mPegawai->getProvinsi());
    }

    public function getKabupaten($kdProvinsi = null)
    {
        $kdProvinsi = $kdProvinsi ?? $this->request->getGet('kd_provinsi') ?? $this->request->getGet('KD_PROVINSI');
        if (empty($kdProvinsi)) {
            return sendResponse(400, null, 'Kode Provinsi diperlukan');
        }
        return sendResponse(200, $this->mPegawai->getKabupaten($kdProvinsi));
    }

    public function getKecamatan($kdKabupaten = null)
    {
        $kdKabupaten = $kdKabupaten ?? $this->request->getGet('kd_kabupaten') ?? $this->request->getGet('KD_KABUPATEN');
        if (empty($kdKabupaten)) {
            return sendResponse(400, null, 'Kode Kabupaten diperlukan');
        }
        return sendResponse(200, $this->mPegawai->getKecamatan($kdKabupaten));
    }

    public function getKelurahan($kdKecamatan = null)
    {
        $kdKecamatan = $kdKecamatan ?? $this->request->getGet('kd_kecamatan') ?? $this->request->getGet('KD_KECAMATAN');
        if (empty($kdKecamatan)) {
            return sendResponse(400, null, 'Kode Kecamatan diperlukan');
        }
        return sendResponse(200, $this->mPegawai->getKelurahan($kdKecamatan));
    }

    public function getAgama()
    {
        return sendResponse(200, $this->mPegawai->getAgama());
    }

    // ==========================================
    // LOOKUP & REFERENCE HELPERS
    // ==========================================
    public function getLookup()
    {
        return sendResponse(200, $this->mPegawai->getPegawaiLookup());
    }

    public function snapshot($id = null)
    {
        $idPegawai = $id ?? $this->request->getGet('id') ?? $this->request->getGet('nip');
        if (empty($idPegawai)) {
            return sendResponse(400, null, 'ID atau NIP diperlukan');
        }
        $snapshot = $this->mPegawai->getPegawaiSnapshot($idPegawai);
        return sendResponse(200, $snapshot);
    }

    public function getRefData()
    {
        return sendResponse(200, $this->mPegawai->getRefData());
    }

    private function getPayload()
    {
        $json = $this->request->getJSON(true);
        if (!empty($json)) return $json;
        $post = $this->request->getPost();
        if (!empty($post)) return $post;
        return $this->request->getRawInput() ?? [];
    }

    // ==========================================
    // 1. PENEMPATAN
    // ==========================================
    public function penempatan()
    {
        $params = [
            'search'    => $this->request->getGet('search') ?? $this->request->getGet('keyword'),
            'kd_bagian' => $this->request->getGet('kd_bagian')
        ];
        return sendResponse(200, $this->mPegawai->getPenempatan($params));
    }

    public function save_penempatan()
    {
        $data = $this->getPayload();

        if (empty($data['ID_PEGAWAI'])) {
            return sendResponse(422, null, 'Pegawai wajib dipilih');
        }
        if (empty($data['KD_BAGIAN'])) {
            return sendResponse(422, null, 'Bagian / Unit Kerja wajib dipilih');
        }

        // Normalisasi format tanggal input (DD-MM-YYYY -> YYYY-MM-DD atau null)
        if (isset($data['TANGGAL_MULAI'])) {
            $data['TANGGAL_MULAI'] = $this->normalizeDateInput($data['TANGGAL_MULAI']);
        }
        if (isset($data['TANGGAL_SAMPAI'])) {
            $data['TANGGAL_SAMPAI'] = $this->normalizeDateInput($data['TANGGAL_SAMPAI']);
        }
        if (isset($data['TANGGAL_SK_PENGANGKATAN'])) {
            $data['TANGGAL_SK_PENGANGKATAN'] = $this->normalizeDateInput($data['TANGGAL_SK_PENGANGKATAN']);
        }
        if (isset($data['TGL_MULAI']) && !isset($data['TANGGAL_MULAI'])) {
            $data['TANGGAL_MULAI'] = $this->normalizeDateInput($data['TGL_MULAI']);
        }
        if (isset($data['TGL_SAMPAI']) && !isset($data['TANGGAL_SAMPAI'])) {
            $data['TANGGAL_SAMPAI'] = $this->normalizeDateInput($data['TGL_SAMPAI']);
        }
        if (isset($data['TGL_SURAT']) && !isset($data['TANGGAL_SK_PENGANGKATAN'])) {
            $data['TANGGAL_SK_PENGANGKATAN'] = $this->normalizeDateInput($data['TGL_SURAT']);
        }

        $res = $this->mPegawai->savePenempatan($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_penempatan($id = null)
    {
        if (empty($id)) {
            $payload = $this->getPayload();
            $id = $payload['KD_PENEMPATAN'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID penempatan diperlukan');
        $res = $this->mPegawai->deletePenempatan($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 2. STR
    // ==========================================
    public function str()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword'),
            'status' => $this->request->getGet('status')
        ];
        return sendResponse(200, $this->mPegawai->getSTR($params));
    }

    public function save_str()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->saveSTR($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_str($id = null)
    {
        if (empty($id)) return sendResponse(400, null, 'ID STR diperlukan');
        $res = $this->mPegawai->deleteSTR($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 3. SIP
    // ==========================================
    public function sip()
    {
        $params = [
            'search'       => $this->request->getGet('search') ?? $this->request->getGet('keyword'),
            'kd_jenis_sip' => $this->request->getGet('kd_jenis_sip')
        ];
        return sendResponse(200, $this->mPegawai->getSIP($params));
    }

    public function save_sip()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->saveSIP($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_sip($id = null)
    {
        if (empty($id)) return sendResponse(400, null, 'ID SIP diperlukan');
        $res = $this->mPegawai->deleteSIP($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 4. PENDIDIKAN
    // ==========================================
    public function pendidikan()
    {
        $params = [
            'search'     => $this->request->getGet('search') ?? $this->request->getGet('keyword'),
            'kd_jenjang' => $this->request->getGet('kd_jenjang')
        ];
        return sendResponse(200, $this->mPegawai->getPendidikan($params));
    }

    public function save_pendidikan()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->savePendidikan($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_pendidikan($id = null)
    {
        if (empty($id)) return sendResponse(400, null, 'ID pendidikan diperlukan');
        $res = $this->mPegawai->deletePendidikan($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 5. PELATIHAN
    // ==========================================
    public function pelatihan()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getPelatihan($params));
    }

    public function save_pelatihan()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->savePelatihan($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_pelatihan($id = null)
    {
        if (empty($id)) return sendResponse(400, null, 'ID pelatihan diperlukan');
        $res = $this->mPegawai->deletePelatihan($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 6. MUTASI
    // ==========================================
    public function mutasi()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getMutasi($params));
    }

    public function save_mutasi()
    {
        $data = $this->getPayload();

        $idPegawai = $data['ID_PEGAWAI'] ?? $data['replacement_id_pegawai'] ?? $data['id_pegawai'] ?? null;
        if (empty($idPegawai) && !empty($data['replacement_NIP'] ?? $data['NIP'] ?? null)) {
            $nip = trim($data['replacement_NIP'] ?? $data['NIP']);
            $pRow = $this->mPegawai->db->table('pegawai')->where('NIP', $nip)->get()->getRowArray();
            if ($pRow) {
                $idPegawai = $pRow['ID_PEGAWAI'];
            }
        }
        $data['ID_PEGAWAI'] = $idPegawai;

        if (empty($data['ID_PEGAWAI'])) {
            return sendResponse(422, null, 'Pegawai wajib dipilih');
        }

        $kdBagianBaru = $data['KD_BAGIAN_BARU'] ?? $data['KD_BAGIAN_TUJUAN'] ?? $data['KD_BAGIAN'] ?? $data['replacement_bagian_menjadi'] ?? null;
        if (empty($kdBagianBaru) || $kdBagianBaru === '-') {
            return sendResponse(422, null, 'Bagian / Unit Baru wajib dipilih');
        }
        $data['KD_BAGIAN_BARU'] = $kdBagianBaru;

        $kdJabatanBaru = $data['KD_JABATAN_BARU'] ?? $data['KD_JABATAN_TUJUAN'] ?? $data['KD_JABATAN'] ?? $data['replacement_jabatan_menjadi'] ?? null;
        if ($kdJabatanBaru === null || $kdJabatanBaru === '' || $kdJabatanBaru === '-') {
            return sendResponse(422, null, 'Jabatan Baru wajib dipilih');
        }
        $data['KD_JABATAN_BARU'] = $kdJabatanBaru;

        $tglMutasiInput = $data['TGL_MUTASI'] ?? $data['replacement_tanggal_mutasi'] ?? null;
        if (!empty($tglMutasiInput)) {
            $normalizedMutasi = $this->normalizeDateInput($tglMutasiInput);
            if (!$normalizedMutasi) {
                return sendResponse(422, null, 'Format tanggal mutasi tidak valid (gunakan DD-MM-YYYY)');
            }
            $data['TGL_MUTASI'] = $normalizedMutasi;
        } else {
            $data['TGL_MUTASI'] = date('Y-m-d');
        }

        $tglSuratInput = $data['TGL_SURAT'] ?? $data['replacement_tanggal_surat'] ?? null;
        if (!empty($tglSuratInput)) {
            $normalizedSurat = $this->normalizeDateInput($tglSuratInput);
            $data['TGL_SURAT'] = $normalizedSurat ?: $data['TGL_MUTASI'];
        } else {
            $data['TGL_SURAT'] = $data['TGL_MUTASI'];
        }

        $res = $this->mPegawai->saveMutasi($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_mutasi($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_MUTASI_DARI'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID mutasi diperlukan');
        $res = $this->mPegawai->deleteMutasi($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 7. PROMOSI
    // ==========================================
    public function promosi()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getPromosi($params));
    }

    public function save_promosi()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->savePromosi($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_promosi($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_PROMOSI_DARI'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID promosi diperlukan');
        $res = $this->mPegawai->deletePromosi($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 8. KONTRAK
    // ==========================================
    public function kontrak()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getKontrak($params));
    }

    public function save_kontrak()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->saveKontrak($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_kontrak($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_KONTRAK'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID kontrak diperlukan');
        $res = $this->mPegawai->deleteKontrak($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 9. PHK
    // ==========================================
    public function phk()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getPHK($params));
    }

    public function save_phk()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->savePHK($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_phk($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_PHK'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID PHK diperlukan');
        $res = $this->mPegawai->deletePHK($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 10. PENGHARGAAN, SANKSI
    // ==========================================
    public function penghargaan()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getPenghargaan($params));
    }

    public function save_penghargaan()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->savePenghargaan($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_penghargaan($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_PENGHARGAAN'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID penghargaan diperlukan');
        $res = $this->mPegawai->deletePenghargaan($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function sanksi()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getSanksi($params));
    }

    public function save_sanksi()
    {
        $data = $this->getPayload();

        $file = $this->request->getFile('file') ?? $this->request->getFile('upload_file') ?? $this->request->getFile('berkas');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'sanksi_' . time() . '_' . $file->getRandomName();
            $uploadPath = ROOTPATH . '../uploads';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
            }
            $file->move($uploadPath, $newName);
            $data['FILE'] = $newName;
        }

        $res = $this->mPegawai->saveSanksi($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_sanksi($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_SANKSI'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID sanksi diperlukan');
        $res = $this->mPegawai->deleteSanksi($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 11. CUTI
    // ==========================================
    public function cuti()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getCuti($params));
    }

    public function sisa_cuti()
    {
        $idPegawai = $this->request->getGet('id_pegawai') ?? $this->request->getGet('id');
        $nip = $this->request->getGet('nip');
        $res = $this->mPegawai->getSisaCuti($idPegawai, $nip);
        return sendResponse(200, $res);
    }

    public function save_cuti()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->saveCuti($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_cuti($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_CUTI'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID cuti diperlukan');
        $res = $this->mPegawai->deleteCuti($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function approve_cuti_hrd()
    {
        $data = $this->getPayload();
        $kdCuti = $data['kd_cuti'] ?? $data['KD_CUTI'] ?? null;
        $catatan = $data['catatan_hrd'] ?? $data['CATATAN_HRD'] ?? '';

        if (empty($kdCuti)) {
            return sendResponse(400, null, 'KD_CUTI diperlukan');
        }

        $res = $this->mPegawai->updateApprovalHRD($kdCuti, 1, $catatan);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function reject_cuti_hrd()
    {
        $data = $this->getPayload();
        $kdCuti = $data['kd_cuti'] ?? $data['KD_CUTI'] ?? null;
        $catatan = $data['catatan_hrd'] ?? $data['CATATAN_HRD'] ?? '';

        if (empty($kdCuti)) {
            return sendResponse(400, null, 'KD_CUTI diperlukan');
        }

        $res = $this->mPegawai->updateApprovalHRD($kdCuti, 2, $catatan);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function action_cuti_hrd()
    {
        $data = $this->getPayload();
        $kdCuti = $data['kd_cuti'] ?? $data['KD_CUTI'] ?? null;
        $status = $data['status'] ?? $data['approval_hrd'] ?? $data['APPROVAL_HRD'] ?? 1;
        $catatan = $data['catatan_hrd'] ?? $data['CATATAN_HRD'] ?? '';

        if (empty($kdCuti)) {
            return sendResponse(400, null, 'KD_CUTI diperlukan');
        }

        $res = $this->mPegawai->updateApprovalHRD($kdCuti, $status, $catatan);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function monitoring_cuti()
    {
        $params = [
            'tahun'   => $this->request->getGet('tahun') ?? $this->request->getGet('year'),
            'bulan'   => $this->request->getGet('bulan') ?? $this->request->getGet('month'),
            'search'  => $this->request->getGet('search') ?? $this->request->getGet('keyword') ?? $this->request->getGet('unit')
        ];
        $res = $this->mPegawai->getMonitoringCuti($params);
        return sendResponse(200, $res);
    }

    // ==========================================
    // IJIN KEHADIRAN / PULANG AWAL
    // ==========================================
    public function ijin()
    {
        $search = $this->request->getGet('search') ?? $this->request->getGet('q');
        $nip = $this->request->getGet('nip');
        $bagian = $this->request->getGet('bagian');
        $data = $this->mPegawai->getIjin($search, $nip, $bagian);
        return sendResponse(200, $data, count($data));
    }

    public function jenis_ijin()
    {
        $data = $this->mPegawai->getJenisIjin();
        return sendResponse(200, $data, count($data));
    }

    public function save_ijin()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->saveIjin($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_ijin($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_CUTI'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID ijin diperlukan');
        $res = $this->mPegawai->deleteIjin($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 12. DEMOSI
    // ==========================================
    public function demosi()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getDemosi($params));
    }

    public function save_demosi()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->saveDemosi($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_demosi($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_DEMOSI_DARI'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID demosi diperlukan');
        $res = $this->mPegawai->deleteDemosi($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // 13. CATATAN
    // ==========================================
    public function catatan()
    {
        $params = [
            'search' => $this->request->getGet('search') ?? $this->request->getGet('keyword')
        ];
        return sendResponse(200, $this->mPegawai->getCatatan($params));
    }

    public function save_catatan()
    {
        $data = $this->getPayload();

        $file = $this->request->getFile('file') ?? $this->request->getFile('upload_file') ?? $this->request->getFile('berkas');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'catatan_' . time() . '_' . $file->getRandomName();
            $uploadPath = ROOTPATH . '../uploads';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
            }
            $file->move($uploadPath, $newName);
            $data['FILE'] = $newName;
        }

        $res = $this->mPegawai->saveCatatan($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_catatan($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['KD_CATATAN'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID catatan diperlukan');
        $res = $this->mPegawai->deleteCatatan($id);
        return sendResponse($res['code'], $res, $res['message']);
    }

    // ==========================================
    // MODUL LEMBUR PEGAWAI
    // ==========================================
    public function lembur()
    {
        $search = $this->request->getGet('search') ?? $this->request->getGet('q');
        $nip = $this->request->getGet('nip');
        $bagian = $this->request->getGet('bagian') ?? $this->request->getGet('kd_bagian');
        $status = $this->request->getGet('status');
        $progress = $this->request->getGet('progress');
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $tglAwal = $this->request->getGet('tgl_awal') ?? $this->request->getGet('start_date') ?? $this->request->getGet('tgl_mulai');
        $tglAkhir = $this->request->getGet('tgl_akhir') ?? $this->request->getGet('end_date') ?? $this->request->getGet('tgl_selesai');
        $data = $this->mPegawai->getLembur($search, $nip, $bagian, $status, $progress, $bulan, $tahun, $tglAwal, $tglAkhir);
        return sendResponse(200, $data, count($data));
    }

    public function monitoring_lembur()
    {
        $params = [
            'tgl_awal'  => $this->request->getGet('tgl_awal') ?? $this->request->getGet('start_date') ?? $this->request->getGet('tgl_mulai'),
            'tgl_akhir' => $this->request->getGet('tgl_akhir') ?? $this->request->getGet('end_date') ?? $this->request->getGet('tgl_selesai'),
            'search'    => $this->request->getGet('search') ?? $this->request->getGet('unit') ?? $this->request->getGet('q')
        ];
        $res = $this->mPegawai->getMonitoringLembur($params);
        return sendResponse(200, $res, count($res['units'] ?? []));
    }

    public function ket_hari_lembur()
    {
        $data = $this->mPegawai->getKetHari();
        return sendResponse(200, $data, count($data));
    }

    public function save_lembur()
    {
        $data = $this->getPayload();
        $res = $this->mPegawai->saveLembur($data);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function approve_lembur_atasan()
    {
        $data = $this->getPayload();
        $id = $data['id_lembur'] ?? $data['id'] ?? null;
        $idAtasan = $data['id_atasan'] ?? $data['nip_atasan'] ?? null;
        $catatan = $data['catatan'] ?? '';
        $res = $this->mPegawai->updateApprovalLembur($id, 'atasan', 1, $idAtasan, $catatan);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function reject_lembur_atasan()
    {
        $data = $this->getPayload();
        $id = $data['id_lembur'] ?? $data['id'] ?? null;
        $idAtasan = $data['id_atasan'] ?? $data['nip_atasan'] ?? null;
        $catatan = $data['catatan'] ?? '';
        $res = $this->mPegawai->updateApprovalLembur($id, 'atasan', 2, $idAtasan, $catatan);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function approve_lembur_hrd()
    {
        $data = $this->getPayload();
        $id = $data['id_lembur'] ?? $data['id'] ?? null;
        $catatan = $data['catatan'] ?? '';
        $extra = [
            'jam_mulai_realisasi' => $data['jam_mulai_realisasi'] ?? null,
            'jam_selesai_realisasi' => $data['jam_selesai_realisasi'] ?? null,
            'tgl_mulai_realisasi' => $data['tgl_mulai_realisasi'] ?? null,
            'tgl_selesai_realisasi' => $data['tgl_selesai_realisasi'] ?? null,
        ];
        $res = $this->mPegawai->updateApprovalLembur($id, 'hrd', 1, null, $catatan, $extra);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function reject_lembur_hrd()
    {
        $data = $this->getPayload();
        $id = $data['id_lembur'] ?? $data['id'] ?? null;
        $catatan = $data['catatan'] ?? '';
        $res = $this->mPegawai->updateApprovalLembur($id, 'hrd', 2, null, $catatan);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function action_lembur()
    {
        $data = $this->getPayload();
        $id = $data['id_lembur'] ?? $data['id'] ?? null;
        $level = $data['level'] ?? 'atasan';
        $status = isset($data['status']) ? (int)$data['status'] : 1;
        $idAtasan = $data['id_atasan'] ?? null;
        $catatan = $data['catatan'] ?? '';
        $res = $this->mPegawai->updateApprovalLembur($id, $level, $status, $idAtasan, $catatan);
        return sendResponse($res['code'], $res, $res['message']);
    }

    public function delete_lembur($id = null)
    {
        if (empty($id)) {
            $data = $this->getPayload();
            $id = $data['id_lembur'] ?? $data['id'] ?? null;
        }
        if (empty($id)) return sendResponse(400, null, 'ID lembur diperlukan');
        $res = $this->mPegawai->deleteLembur($id);
        return sendResponse($res['code'], $res, $res['message']);
    }
}