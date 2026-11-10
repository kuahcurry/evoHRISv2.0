<?php

namespace App\Controllers;

use App\Models\MKonfigurasi;

class Konfigurasi extends BaseController
{
    protected $mKonfig;

    public function __construct()
    {
        $this->mKonfig = new MKonfigurasi();
    }

    /**
     * Master Dropdowns
     * GET /konfigurasi/master
     */
    public function getMaster()
    {
        try {
            $data = $this->mKonfig->getMaster();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * USERS
     * ============================================================ */

    /**
     * GET /konfigurasi/users/list
     */
    public function getUsers()
    {
        try {
            $search = $this->request->getGet('search') ?? '';
            $roleId = $this->request->getGet('role_id') ?? null;
            $status = $this->request->getGet('status') ?? '';
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);

            $result = $this->mKonfig->getUsers($search, $roleId, $status, $page, $perPage);
            return sendResponse(200, $result['users'], true, 'OK', $result['total'], [
                'page' => $result['page'],
                'per_page' => $result['perPage'],
                'stats' => $result['stats']
            ]);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * GET /konfigurasi/users/detail/(:num)
     */
    public function getUserDetail($id)
    {
        try {
            $data = $this->mKonfig->getUserDetail($id);
            if (!$data) {
                return sendResponse(404, null, false, 'User tidak ditemukan');
            }
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/users/save
     */
    public function saveUser()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['nip']) || empty($input['nama_lengkap'])) {
                return sendResponse(400, null, false, 'NIP dan Nama Lengkap wajib diisi');
            }

            $id = $this->mKonfig->saveUser($input);
            return sendResponse(200, ['id' => $id], true, 'Data pengguna berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/users/status
     */
    public function updateUserStatus()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['id']) || empty($input['status_user'])) {
                return sendResponse(400, null, false, 'ID dan Status wajib diisi');
            }

            $this->mKonfig->updateUserStatus($input['id'], $input['status_user']);
            return sendResponse(200, null, true, 'Status pengguna berhasil diperbarui');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/users/reset-password
     */
    public function resetPassword()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['id']) || empty($input['password'])) {
                return sendResponse(400, null, false, 'ID dan Password Baru wajib diisi');
            }

            $this->mKonfig->resetPassword($input['id'], $input['password']);
            return sendResponse(200, null, true, 'Password pengguna berhasil direset');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * DELETE /konfigurasi/users/delete/(:num)
     */
    public function deleteUser($id)
    {
        try {
            $this->mKonfig->deleteUser($id);
            return sendResponse(200, null, true, 'Pengguna berhasil dihapus');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * ROLES (user_level)
     * ============================================================ */

    /**
     * GET /konfigurasi/roles/list
     */
    public function getRoles()
    {
        try {
            $roles = $this->mKonfig->getRoles();
            return sendResponse(200, $roles);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/roles/save
     */
    public function saveRole()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['LEVEL'])) {
                return sendResponse(400, null, false, 'Nama Level Role wajib diisi');
            }

            $id = $this->mKonfig->saveRole($input);
            return sendResponse(200, ['id' => $id], true, 'Role berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * DELETE /konfigurasi/roles/delete/(:num)
     */
    public function deleteRole($id)
    {
        try {
            $this->mKonfig->deleteRole($id);
            return sendResponse(200, null, true, 'Role berhasil dihapus');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * KELOMPOK KERJA (kelompok)
     * ============================================================ */

    /**
     * GET /konfigurasi/kelompok/list
     */
    public function getKelompok()
    {
        try {
            $kelompok = $this->mKonfig->getKelompok();
            return sendResponse(200, $kelompok);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/kelompok/save
     */
    public function saveKelompok()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['KELOMPOK'])) {
                return sendResponse(400, null, false, 'Nama Kelompok Kerja wajib diisi');
            }

            $id = $this->mKonfig->saveKelompok($input);
            return sendResponse(200, ['id' => $id], true, 'Kelompok kerja berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * DELETE /konfigurasi/kelompok/delete/(:num)
     */
    public function deleteKelompok($id)
    {
        try {
            $this->mKonfig->deleteKelompok($id);
            return sendResponse(200, null, true, 'Kelompok kerja berhasil dihapus');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * HAK AKSES MATRIX
     * ============================================================ */

    /**
     * GET /konfigurasi/hak-akses/matrix/(:num)
     */
    public function getHakAkses($idUserLevel)
    {
        try {
            $matrix = $this->mKonfig->getHakAksesMatrix($idUserLevel);
            return sendResponse(200, $matrix);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/hak-akses/toggle
     */
    public function saveHakAkses()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            $idUserLevel = $input['id_user_level'] ?? null;
            $idMenu = $input['id_menu'] ?? null;
            $field = $input['field'] ?? null;
            $value = $input['value'] ?? 0;

            if (!$idUserLevel || !$idMenu || !$field) {
                return sendResponse(400, null, false, 'Parameter hak akses tidak lengkap');
            }

            $this->mKonfig->saveHakAksesSingle($idUserLevel, $idMenu, $field, $value);
            return sendResponse(200, null, true, 'Hak akses berhasil diperbarui');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/hak-akses/save-batch
     */
    public function saveHakAksesBatch()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            $idUserLevel = $input['id_user_level'] ?? null;
            $items = $input['items'] ?? [];

            if (!$idUserLevel || !is_array($items)) {
                return sendResponse(400, null, false, 'Data batch hak akses tidak valid');
            }

            $this->mKonfig->saveHakAksesBatch($idUserLevel, $items);
            return sendResponse(200, null, true, 'Semua perubahan hak akses berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * STANDARISASI JAM KERJA
     * ============================================================ */

    /**
     * GET /konfigurasi/standarisasi/list
     */
    public function getStandarisasi()
    {
        try {
            $data = $this->mKonfig->getStandarisasi();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/standarisasi/save
     */
    public function saveStandarisasi()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['STANDARISASI']) || empty($input['JAM_MASUK']) || empty($input['JAM_PULANG'])) {
                return sendResponse(400, null, false, 'Nama Shift, Jam Masuk, dan Jam Pulang wajib diisi');
            }

            $id = $this->mKonfig->saveStandarisasi($input);
            return sendResponse(200, ['id' => $id], true, 'Standarisasi shift berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * DELETE /konfigurasi/standarisasi/delete/(:num)
     */
    public function deleteStandarisasi($id)
    {
        try {
            $this->mKonfig->deleteStandarisasi($id);
            return sendResponse(200, null, true, 'Standarisasi berhasil dihapus');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * JADWAL ADMINISTRASI
     * ============================================================ */

    /**
     * GET /konfigurasi/jadwal-admin/list
     */
    public function getJadwalAdministrasi()
    {
        try {
            $data = $this->mKonfig->getJadwalAdministrasi();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/jadwal-admin/save
     */
    public function saveJadwalAdministrasi()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['HARI']) || empty($input['JAM_MASUK']) || empty($input['JAM_PULANG'])) {
                return sendResponse(400, null, false, 'Hari, Jam Masuk, dan Jam Pulang wajib diisi');
            }

            $id = $this->mKonfig->saveJadwalAdministrasi($input);
            return sendResponse(200, ['id' => $id], true, 'Jadwal administrasi berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * DELETE /konfigurasi/jadwal-admin/delete/(:num)
     */
    public function deleteJadwalAdministrasi($id)
    {
        try {
            $this->mKonfig->deleteJadwalAdministrasi($id);
            return sendResponse(200, null, true, 'Jadwal administrasi berhasil dihapus');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * TARGET JAM KERJA SKP
     * ============================================================ */

    /**
     * GET /konfigurasi/target/list
     */
    public function getTarget()
    {
        try {
            $data = $this->mKonfig->getTarget();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/target/save
     */
    public function saveTarget()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['KD_STANDARISASI']) || !isset($input['TARGET'])) {
                return sendResponse(400, null, false, 'Shift Standarisasi dan Target Jam Kerja wajib diisi');
            }

            $id = $this->mKonfig->saveTarget($input);
            return sendResponse(200, ['id' => $id], true, 'Target jam kerja berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * DELETE /konfigurasi/target/delete/(:num)
     */
    public function deleteTarget($id)
    {
        try {
            $this->mKonfig->deleteTarget($id);
            return sendResponse(200, null, true, 'Target jam kerja berhasil dihapus');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * SETTING & PEJABAT RS
     * ============================================================ */

    /**
     * GET /konfigurasi/settings/list
     */
    public function getSettings()
    {
        try {
            $data = $this->mKonfig->getSettings();
            return sendResponse(200, $data);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/settings/save
     */
    public function saveSetting()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            $this->mKonfig->saveSetting($input);
            return sendResponse(200, null, true, 'Pengaturan pejabat/sistem berhasil diperbarui');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /* ============================================================
     * APPROVAL ROUTING
     * ============================================================ */

    /**
     * GET /konfigurasi/approval/list
     */
    public function getApproval()
    {
        try {
            $search = $this->request->getGet('search') ?? '';
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 10);

            $result = $this->mKonfig->getApprovalRoutes($search, $page, $perPage);
            return sendResponse(200, $result['routes'], true, 'OK', $result['total'], [
                'page' => $result['page'],
                'per_page' => $result['perPage']
            ]);
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * POST /konfigurasi/approval/save
     */
    public function saveApproval()
    {
        try {
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            if (empty($input['nip']) || empty($input['nip_atasan'])) {
                return sendResponse(400, null, false, 'Pegawai dan Atasan wajib dipilih');
            }

            $id = $this->mKonfig->saveApprovalRoute($input);
            return sendResponse(200, ['id' => $id], true, 'Routing hirarki approval berhasil disimpan');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }

    /**
     * DELETE /konfigurasi/approval/delete/(:num)
     */
    public function deleteApproval($id)
    {
        try {
            $this->mKonfig->deleteApprovalRoute($id);
            return sendResponse(200, null, true, 'Routing approval berhasil dihapus');
        } catch (\Throwable $th) {
            return sendResponse(500, null, false, $th->getMessage());
        }
    }
}
