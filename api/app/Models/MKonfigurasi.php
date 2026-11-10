<?php

namespace App\Models;

use CodeIgniter\Model;

class MKonfigurasi extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Master dropdown data
     */
    public function getMaster()
    {
        $roles = $this->db->table('user_level')->orderBy('ID', 'ASC')->get()->getResultArray();
        $kelompok = $this->db->table('kelompok')->orderBy('KD_KELOMPOK', 'ASC')->get()->getResultArray();
        $standarisasi = $this->db->table('standarisasi')->orderBy('KD_STANDARISASI', 'ASC')->get()->getResultArray();
        $pegawai = $this->db->table('pegawai')
            ->select('ID_PEGAWAI, NIP, NAMA')
            ->orderBy('NAMA', 'ASC')
            ->get()->getResultArray();

        return [
            'roles' => $roles,
            'kelompok' => $kelompok,
            'standarisasi' => $standarisasi,
            'pegawai' => $pegawai
        ];
    }

    /* ============================================================
     * USER MANAGEMENT (tb_users)
     * ============================================================ */

    public function getUsers($search = '', $roleId = null, $status = '', $page = 1, $perPage = 10)
    {
        $builder = $this->db->table('tb_users u')
            ->select('u.id, u.nip, u.nama_lengkap, u.level_akses, u.kd_userlevel, u.status_user, u.status, u.login_terakhir, l.LEVEL as role_name, p.ID_PEGAWAI')
            ->join('user_level l', 'u.kd_userlevel = l.KD_LEVEL', 'left')
            ->join('pegawai p', 'u.nip = p.NIP', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.nama_lengkap', $search)
                ->orLike('u.nip', $search)
                ->groupEnd();
        }

        if (!empty($roleId)) {
            $builder->where('u.kd_userlevel', $roleId);
        }

        if (!empty($status)) {
            $builder->where('u.status_user', $status);
        }

        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults();

        $offset = ($page - 1) * $perPage;
        $users = $builder->orderBy('u.id', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

        // Summary stats
        $totalUsers = $this->db->table('tb_users')->countAllResults();
        $activeUsers = $this->db->table('tb_users')->where('status_user', 'valid')->countAllResults();
        $blockedUsers = $this->db->table('tb_users')->where('status_user !=', 'valid')->countAllResults();
        $totalRoles = $this->db->table('user_level')->countAllResults();

        return [
            'users' => $users,
            'total' => $total,
            'page' => (int)$page,
            'perPage' => (int)$perPage,
            'stats' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'blocked_users' => $blockedUsers,
                'total_roles' => $totalRoles
            ]
        ];
    }

    public function getUserDetail($id)
    {
        return $this->db->table('tb_users u')
            ->select('u.id, u.nip, u.nama_lengkap, u.level_akses, u.kd_userlevel, u.status_user, u.status, u.login_terakhir, l.LEVEL as role_name')
            ->join('user_level l', 'u.kd_userlevel = l.KD_LEVEL', 'left')
            ->where('u.id', $id)
            ->get()->getRowArray();
    }

    public function saveUser($data)
    {
        $roleId = $data['kd_userlevel'] ?? null;
        $roleName = 'viewer';
        if ($roleId) {
            $roleRow = $this->db->table('user_level')->where('KD_LEVEL', $roleId)->get()->getRowArray();
            if ($roleRow) {
                if ($roleId == 14) $roleName = 'super_admin';
                else if ($roleId == 13) $roleName = 'admin';
                else if ($roleId == 12) $roleName = 'kepala';
                else $roleName = strtolower(str_replace(' ', '_', $roleRow['LEVEL']));
            }
        }

        $userData = [
            'nip' => $data['nip'],
            'nama_lengkap' => $data['nama_lengkap'],
            'level_akses' => $roleName,
            'kd_userlevel' => $roleId,
            'status_user' => $data['status_user'] ?? 'valid',
            'status' => $data['status'] ?? 'offline'
        ];

        if (!empty($data['password'])) {
            $userData['pass_login'] = md5($data['password']);
            $userData['pass_real'] = $data['password'];
        }

        if (!empty($data['id'])) {
            $this->db->table('tb_users')->where('id', $data['id'])->update($userData);
            return $data['id'];
        } else {
            // Check if user exists by NIP
            $existing = $this->db->table('tb_users')->where('nip', $data['nip'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('tb_users')->where('id', $existing['id'])->update($userData);
                return $existing['id'];
            }
            if (empty($userData['pass_login'])) {
                $userData['pass_login'] = md5('123456');
                $userData['pass_real'] = '123456';
            }
            $userData['foto'] = 'null';
            $userData['login_terakhir'] = date('Y-m-d H:i:s');
            $this->db->table('tb_users')->insert($userData);
            return $this->db->insertID();
        }
    }

    public function updateUserStatus($id, $status)
    {
        return $this->db->table('tb_users')->where('id', $id)->update(['status_user' => $status]);
    }

    public function resetPassword($id, $newPassword)
    {
        return $this->db->table('tb_users')->where('id', $id)->update([
            'pass_login' => md5($newPassword),
            'pass_real' => $newPassword
        ]);
    }

    public function deleteUser($id)
    {
        return $this->db->table('tb_users')->where('id', $id)->delete();
    }

    /* ============================================================
     * USER LEVELS / ROLES (user_level)
     * ============================================================ */

    public function getRoles()
    {
        $roles = $this->db->table('user_level')->orderBy('KD_LEVEL', 'ASC')->get()->getResultArray();
        foreach ($roles as &$role) {
            $kd = $role['KD_LEVEL'] ?? $role['ID'];
            $role['user_count'] = $this->db->table('tb_users')->where('kd_userlevel', $kd)->countAllResults();
        }
        return $roles;
    }

    public function saveRole($data)
    {
        $roleData = [
            'LEVEL' => $data['LEVEL'],
            'BAGIAN' => $data['BAGIAN'] ?? '',
            'SUB_BAGIAN' => $data['SUB_BAGIAN'] ?? '',
            'KETERANGAN_BAGIAN' => $data['KETERANGAN_BAGIAN'] ?? '',
            'KETERANGAN_SUB_BAGIAN' => $data['KETERANGAN_SUB_BAGIAN'] ?? '',
            'STATUS_AKTIF' => $data['STATUS_AKTIF'] ?? 1
        ];

        if (!empty($data['ID'])) {
            $this->db->table('user_level')->where('ID', $data['ID'])->update($roleData);
            return $data['ID'];
        } else {
            // Find max KD_LEVEL
            $maxKd = $this->db->table('user_level')->selectMax('KD_LEVEL')->get()->getRowArray();
            $roleData['KD_LEVEL'] = ($maxKd['KD_LEVEL'] ?? 20) + 1;
            $this->db->table('user_level')->insert($roleData);
            return $this->db->insertID();
        }
    }

    public function deleteRole($id)
    {
        $role = $this->db->table('user_level')->where('ID', $id)->get()->getRowArray();
        if ($role) {
            $kd = $role['KD_LEVEL'] ?? $role['ID'];
            // Clean up permissions
            $this->db->table('user_hak_akses')->where('id_user_level', $kd)->delete();
            return $this->db->table('user_level')->where('ID', $id)->delete();
        }
        return false;
    }

    /* ============================================================
     * KELOMPOK KERJA / UNIT (kelompok)
     * ============================================================ */

    public function getKelompok()
    {
        $kelompok = $this->db->table('kelompok')->orderBy('KD_KELOMPOK', 'ASC')->get()->getResultArray();
        foreach ($kelompok as &$k) {
            $k['shift_count'] = $this->db->table('standarisasi')->where('KD_KELOMPOK', $k['KD_KELOMPOK'])->countAllResults();
        }
        return $kelompok;
    }

    public function saveKelompok($data)
    {
        $kelData = [
            'KELOMPOK' => $data['KELOMPOK']
        ];

        if (!empty($data['KD_KELOMPOK'])) {
            $this->db->table('kelompok')->where('KD_KELOMPOK', $data['KD_KELOMPOK'])->update($kelData);
            return $data['KD_KELOMPOK'];
        } else {
            $maxKd = $this->db->table('kelompok')->selectMax('KD_KELOMPOK')->get()->getRowArray();
            $kelData['KD_KELOMPOK'] = ($maxKd['KD_KELOMPOK'] ?? 0) + 1;
            $this->db->table('kelompok')->insert($kelData);
            return $kelData['KD_KELOMPOK'];
        }
    }

    public function deleteKelompok($id)
    {
        return $this->db->table('kelompok')->where('KD_KELOMPOK', $id)->delete();
    }

    /* ============================================================
     * HAK AKSES MATRIX (tb_menu & user_hak_akses)
     * ============================================================ */

    public function getHakAksesMatrix($idUserLevel)
    {
        $parents = $this->db->table('tb_menu')
            ->where('parent_id', 0)
            ->orderBy('menu_order', 'ASC')
            ->get()->getResultArray();

        $submenus = $this->db->query("
            SELECT m.id_menu, m.parent_id, m.menu, m.menu_order, m.icon, m.active_controller, m.link_controller,
                   COALESCE(h.flag_view, 0) as flag_view,
                   COALESCE(h.flag_add, 0) as flag_add,
                   COALESCE(h.flag_edit, 0) as flag_edit,
                   COALESCE(h.flag_delete, 0) as flag_delete
            FROM tb_menu m
            LEFT JOIN user_hak_akses h ON m.id_menu = h.id_menu AND h.id_user_level = ?
            WHERE m.parent_id != 0
            ORDER BY m.parent_id ASC, m.menu_order ASC
        ", [$idUserLevel])->getResultArray();

        // Group submenus under parent
        $subByParent = [];
        foreach ($submenus as $sub) {
            $subByParent[$sub['parent_id']][] = $sub;
        }

        $matrix = [];
        foreach ($parents as $p) {
            $pId = $p['id_menu'];
            // Check parent view permission
            $parentPerm = $this->db->table('user_hak_akses')
                ->where('id_user_level', $idUserLevel)
                ->where('id_menu', $pId)
                ->get()->getRowArray();

            $matrix[] = [
                'parent_id' => $pId,
                'parent_menu' => $p['menu'],
                'icon' => $p['icon'],
                'parent_view' => $parentPerm['flag_view'] ?? 0,
                'children' => $subByParent[$pId] ?? []
            ];
        }

        return $matrix;
    }

    public function saveHakAksesSingle($idUserLevel, $idMenu, $field, $value)
    {
        $validFields = ['flag_view', 'flag_add', 'flag_edit', 'flag_delete'];
        if (!in_array($field, $validFields)) {
            return false;
        }

        $val = $value ? 1 : 0;
        $row = $this->db->table('user_hak_akses')
            ->where('id_user_level', $idUserLevel)
            ->where('id_menu', $idMenu)
            ->get()->getRowArray();

        if ($row) {
            $this->db->table('user_hak_akses')
                ->where('id_user_level', $idUserLevel)
                ->where('id_menu', $idMenu)
                ->update([$field => $val]);
        } else {
            $insertData = [
                'id_user_level' => $idUserLevel,
                'id_menu' => $idMenu,
                'flag_view' => 0,
                'flag_add' => 0,
                'flag_edit' => 0,
                'flag_delete' => 0
            ];
            $insertData[$field] = $val;
            $this->db->table('user_hak_akses')->insert($insertData);
        }

        // If enabling view on child, also ensure parent menu has flag_view = 1
        if ($field === 'flag_view' && $val === 1) {
            $menu = $this->db->table('tb_menu')->where('id_menu', $idMenu)->get()->getRowArray();
            if ($menu && !empty($menu['parent_id'])) {
                $pRow = $this->db->table('user_hak_akses')
                    ->where('id_user_level', $idUserLevel)
                    ->where('id_menu', $menu['parent_id'])
                    ->get()->getRowArray();
                if ($pRow) {
                    $this->db->table('user_hak_akses')
                        ->where('id_user_level', $idUserLevel)
                        ->where('id_menu', $menu['parent_id'])
                        ->update(['flag_view' => 1]);
                } else {
                    $this->db->table('user_hak_akses')->insert([
                        'id_user_level' => $idUserLevel,
                        'id_menu' => $menu['parent_id'],
                        'flag_view' => 1,
                        'flag_add' => 0,
                        'flag_edit' => 0,
                        'flag_delete' => 0
                    ]);
                }
            }
        }

        return true;
    }

    public function saveHakAksesBatch($idUserLevel, $items)
    {
        foreach ($items as $item) {
            $idMenu = $item['id_menu'];
            $flagView = !empty($item['flag_view']) ? 1 : 0;
            $flagAdd = !empty($item['flag_add']) ? 1 : 0;
            $flagEdit = !empty($item['flag_edit']) ? 1 : 0;
            $flagDelete = !empty($item['flag_delete']) ? 1 : 0;

            $row = $this->db->table('user_hak_akses')
                ->where('id_user_level', $idUserLevel)
                ->where('id_menu', $idMenu)
                ->get()->getRowArray();

            if ($row) {
                $this->db->table('user_hak_akses')
                    ->where('id_user_level', $idUserLevel)
                    ->where('id_menu', $idMenu)
                    ->update([
                        'flag_view' => $flagView,
                        'flag_add' => $flagAdd,
                        'flag_edit' => $flagEdit,
                        'flag_delete' => $flagDelete
                    ]);
            } else {
                $this->db->table('user_hak_akses')->insert([
                    'id_user_level' => $idUserLevel,
                    'id_menu' => $idMenu,
                    'flag_view' => $flagView,
                    'flag_add' => $flagAdd,
                    'flag_edit' => $flagEdit,
                    'flag_delete' => $flagDelete
                ]);
            }

            // Sync parent view
            if ($flagView === 1) {
                $menu = $this->db->table('tb_menu')->where('id_menu', $idMenu)->get()->getRowArray();
                if ($menu && !empty($menu['parent_id'])) {
                    $pRow = $this->db->table('user_hak_akses')
                        ->where('id_user_level', $idUserLevel)
                        ->where('id_menu', $menu['parent_id'])
                        ->get()->getRowArray();
                    if ($pRow) {
                        $this->db->table('user_hak_akses')
                            ->where('id_user_level', $idUserLevel)
                            ->where('id_menu', $menu['parent_id'])
                            ->update(['flag_view' => 1]);
                    } else {
                        $this->db->table('user_hak_akses')->insert([
                            'id_user_level' => $idUserLevel,
                            'id_menu' => $menu['parent_id'],
                            'flag_view' => 1,
                            'flag_add' => 0,
                            'flag_edit' => 0,
                            'flag_delete' => 0
                        ]);
                    }
                }
            }
        }
        return true;
    }

    /* ============================================================
     * STANDARISASI JAM KERJA & SHIFT (standarisasi)
     * ============================================================ */

    public function getStandarisasi()
    {
        $rows = $this->db->table('standarisasi s')
            ->select('s.*, k.KELOMPOK as nama_kelompok')
            ->join('kelompok k', 's.KD_KELOMPOK = k.KD_KELOMPOK', 'left')
            ->orderBy('s.KD_STANDARISASI', 'ASC')
            ->get()->getResultArray();

        return array_map(function($r) {
            $r['id_standarisasi'] = $r['KD_STANDARISASI'];
            $r['nama_standarisasi'] = $r['STANDARISASI'];
            $r['jam_mulai'] = $r['JAM_MASUK'];
            $r['jam_selesai'] = $r['JAM_PULANG'];
            $r['durasi_jam'] = !empty($r['TOTAL_JAM']) ? floatval($r['TOTAL_JAM']) : (
                (!empty($r['JAM_MASUK']) && !empty($r['JAM_PULANG']) && strtotime($r['JAM_PULANG']) > strtotime($r['JAM_MASUK']))
                ? round((strtotime($r['JAM_PULANG']) - strtotime($r['JAM_MASUK'])) / 3600, 1)
                : 7.0
            );
            $r['is_active'] = 1;
            $r['kd_kelompok'] = $r['KD_KELOMPOK'];
            return $r;
        }, $rows);
    }

    public function saveStandarisasi($data)
    {
        $stanData = [
            'STANDARISASI' => $data['STANDARISASI'] ?? $data['nama_standarisasi'] ?? '',
            'KD_KELOMPOK' => $data['KD_KELOMPOK'] ?? $data['kd_kelompok'] ?? null,
            'JAM_MASUK' => $data['JAM_MASUK'] ?? $data['jam_mulai'] ?? '08:00:00',
            'JAM_PULANG' => $data['JAM_PULANG'] ?? $data['jam_selesai'] ?? '16:00:00',
            'TOLERANSI' => $data['TOLERANSI'] ?? 0,
            'PENDEK' => $data['PENDEK'] ?? $data['nama_standarisasi'] ?? '',
            'TOTAL_JAM' => $data['TOTAL_JAM'] ?? $data['durasi_jam'] ?? 7,
            'STATUS_LIBUR' => $data['STATUS_LIBUR'] ?? 0,
            'TOLERANSI_MENIT' => $data['TOLERANSI_MENIT'] ?? 0,
            'TOLERANSI_MASUK' => $data['TOLERANSI_MASUK'] ?? 0,
            'TOLERANSI_PULANG' => $data['TOLERANSI_PULANG'] ?? 0
        ];

        $id = $data['KD_STANDARISASI'] ?? $data['id_standarisasi'] ?? null;
        if (!empty($id)) {
            $this->db->table('standarisasi')->where('KD_STANDARISASI', $id)->update($stanData);
            return $id;
        } else {
            $maxKd = $this->db->table('standarisasi')->selectMax('KD_STANDARISASI')->get()->getRowArray();
            $stanData['KD_STANDARISASI'] = ($maxKd['KD_STANDARISASI'] ?? 0) + 1;
            $this->db->table('standarisasi')->insert($stanData);
            return $stanData['KD_STANDARISASI'];
        }
    }

    public function deleteStandarisasi($id)
    {
        return $this->db->table('standarisasi')->where('KD_STANDARISASI', $id)->delete();
    }

    /* ============================================================
     * JADWAL ADMINISTRASI (jadwal_administrasi)
     * ============================================================ */

    public function getJadwalAdministrasi()
    {
        $rows = $this->db->table('jadwal_administrasi j')
            ->select('j.*, s.STANDARISASI as nama_standarisasi')
            ->join('standarisasi s', 'j.KD_STANDARISASI = s.KD_STANDARISASI', 'left')
            ->orderBy('j.ID', 'ASC')
            ->get()->getResultArray();

        return array_map(function($r) {
            $r['id'] = $r['ID'];
            $r['hari'] = $r['HARI'];
            $r['jam_masuk'] = $r['JAM_MASUK'];
            $r['jam_pulang'] = $r['JAM_PULANG'];
            $r['kd_standarisasi'] = $r['KD_STANDARISASI'];
            $r['toleransi_menit'] = $r['TOLERANSI_MENIT'] ?? 0;
            $r['keterangan'] = $r['KETERANGAN'] ?? '';
            return $r;
        }, $rows);
    }

    public function saveJadwalAdministrasi($data)
    {
        $jadwalData = [
            'HARI' => $data['HARI'] ?? $data['hari'] ?? '',
            'JAM_MASUK' => $data['JAM_MASUK'] ?? $data['jam_masuk'] ?? '08:00:00',
            'JAM_PULANG' => $data['JAM_PULANG'] ?? $data['jam_pulang'] ?? '16:00:00',
            'KD_STANDARISASI' => $data['KD_STANDARISASI'] ?? $data['kd_standarisasi'] ?? null
        ];

        $id = $data['ID'] ?? $data['id'] ?? null;
        if (!empty($id)) {
            $this->db->table('jadwal_administrasi')->where('ID', $id)->update($jadwalData);
            return $id;
        } else {
            $this->db->table('jadwal_administrasi')->insert($jadwalData);
            return $this->db->insertID();
        }
    }

    public function deleteJadwalAdministrasi($id)
    {
        return $this->db->table('jadwal_administrasi')->where('ID', $id)->delete();
    }

    /* ============================================================
     * TARGET JAM KERJA SKP (target)
     * ============================================================ */

    public function getTarget()
    {
        return $this->db->table('target t')
            ->select('t.*, s.STANDARISASI as nama_standarisasi, s.JAM_MASUK, s.JAM_PULANG, s.PENDEK')
            ->join('standarisasi s', 't.KD_STANDARISASI = s.KD_STANDARISASI', 'left')
            ->orderBy('t.KD_TARGET', 'ASC')
            ->get()->getResultArray();
    }

    public function saveTarget($data)
    {
        $targetData = [
            'KD_STANDARISASI' => $data['KD_STANDARISASI'],
            'TARGET' => $data['TARGET']
        ];

        if (!empty($data['KD_TARGET'])) {
            $this->db->table('target')->where('KD_TARGET', $data['KD_TARGET'])->update($targetData);
            return $data['KD_TARGET'];
        } else {
            $this->db->table('target')->insert($targetData);
            return $this->db->insertID();
        }
    }

    public function deleteTarget($id)
    {
        return $this->db->table('target')->where('KD_TARGET', $id)->delete();
    }

    /* ============================================================
     * SETTING & PEJABAT RS (setting)
     * ============================================================ */

    public function getSettings()
    {
        return $this->db->table('setting')->orderBy('id', 'ASC')->get()->getResultArray();
    }

    public function saveSetting($data)
    {
        $updateData = [
            'setting' => $data['setting'] ?? '',
            'setting2' => $data['setting2'] ?? '',
            'parameter' => $data['parameter'] ?? '',
            'aktif' => $data['aktif'] ?? 1
        ];

        if (!empty($data['id'])) {
            return $this->db->table('setting')->where('id', $data['id'])->update($updateData);
        } else if (!empty($data['komponen'])) {
            return $this->db->table('setting')->where('komponen', $data['komponen'])->update($updateData);
        }
        return false;
    }

    /* ============================================================
     * APPROVAL ROUTE (ekinerja_route_approval & tb_approval_config)
     * ============================================================ */

    public function getApprovalRoutes($search = '', $page = 1, $perPage = 10)
    {
        $builder = $this->db->table('ekinerja_route_approval r')
            ->select('r.id_route, r.nip, r.nip_atasan, r.level_atasan, p1.NAMA as nama_pegawai, p2.NAMA as nama_atasan')
            ->join('pegawai p1', 'r.nip = p1.NIP', 'left')
            ->join('pegawai p2', 'r.nip_atasan = p2.NIP', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('p1.NAMA', $search)
                ->orLike('p2.NAMA', $search)
                ->orLike('r.nip', $search)
                ->orLike('r.nip_atasan', $search)
                ->groupEnd();
        }

        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults();

        $offset = ($page - 1) * $perPage;
        $routes = $builder->orderBy('r.id_route', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

        return [
            'routes' => $routes,
            'total' => $total,
            'page' => (int)$page,
            'perPage' => (int)$perPage
        ];
    }

    public function saveApprovalRoute($data)
    {
        $routeData = [
            'nip' => $data['nip'],
            'nip_atasan' => $data['nip_atasan'],
            'level_atasan' => $data['level_atasan'] ?? 1
        ];

        if (!empty($data['id_route'])) {
            $this->db->table('ekinerja_route_approval')->where('id_route', $data['id_route'])->update($routeData);
            $id = $data['id_route'];
        } else {
            $this->db->table('ekinerja_route_approval')->insert($routeData);
            $id = $this->db->insertID();
        }

        // Also sync to tb_approval_config
        $existsConfig = $this->db->table('tb_approval_config')->where('ID_PEGAWAI', $data['nip'])->get()->getRowArray();
        if ($existsConfig) {
            $this->db->table('tb_approval_config')->where('ID_PEGAWAI', $data['nip'])->update(['ID_ATASAN' => $data['nip_atasan']]);
        } else {
            $this->db->table('tb_approval_config')->insert([
                'ID_PEGAWAI' => $data['nip'],
                'ID_ATASAN' => $data['nip_atasan']
            ]);
        }

        return $id;
    }

    public function deleteApprovalRoute($id)
    {
        $route = $this->db->table('ekinerja_route_approval')->where('id_route', $id)->get()->getRowArray();
        if ($route) {
            $this->db->table('tb_approval_config')->where('ID_PEGAWAI', $route['nip'])->delete();
            return $this->db->table('ekinerja_route_approval')->where('id_route', $id)->delete();
        }
        return false;
    }
}
