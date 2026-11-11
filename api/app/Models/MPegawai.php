<?php

namespace App\Models;

use CodeIgniter\Model;

class MPegawai extends Model
{
    public function getProvinsi()
    {
        return $this->db->query("SELECT KD_PROVINSI, PROVINSI FROM provinsi WHERE STATUS = 1 ORDER BY PROVINSI ASC")->getResultArray();
    }

    public function getKabupaten($kdProvinsi)
    {
        return $this->db->query("SELECT KD_KABUPATEN, KABUPATEN FROM kabupaten WHERE KD_PROVINSI = ? AND STATUS = 1 ORDER BY KABUPATEN ASC", [$kdProvinsi])->getResultArray();
    }

    public function getKecamatan($kdKabupaten)
    {
        return $this->db->query("SELECT KD_KECAMATAN, KECAMATAN FROM kecamatan WHERE KD_KABUPATEN = ? AND STATUS = 1 ORDER BY KECAMATAN ASC", [$kdKabupaten])->getResultArray();
    }

    public function getKelurahan($kdKecamatan)
    {
        return $this->db->query("SELECT KD_KELURAHAN, KELURAHAN FROM kelurahan WHERE KD_KECAMATAN = ? AND STATUS = 1 ORDER BY KELURAHAN ASC", [$kdKecamatan])->getResultArray();
    }

    public function getAgama()
    {
        return $this->db->query("SELECT KD_AGAMA, AGAMA FROM agama ORDER BY AGAMA ASC")->getResultArray();
    }

    public function getPegawaiUnit($params = [])
    {
        $builder = $this->db->table('pegawai');
        $builder->select('ID_PEGAWAI, NIP, NAMA, NO_IDENTITAS, JENIS_KELAMIN, TEMPAT_LAHIR, TANGGAL_LAHIR,
                         KD_AGAMA, KD_PROVINSI, KD_KABUPATEN, KD_KECAMATAN, KD_KELURAHAN,
                         ALAMAT, HANDPHONE, EMAIL, status_aktif');
        $builder->where('status_aktif', 1);

        $keyword = '';
        if (is_array($params)) {
            $keyword = trim($params['keyword'] ?? $params['search'] ?? '');
        } elseif (is_string($params)) {
            $keyword = trim($params);
        }

        if ($keyword !== '') {
            $builder->groupStart()
                    ->like('NAMA', $keyword)
                    ->orLike('NIP', $keyword)
                    ->orLike('ID_PEGAWAI', $keyword)
                    ->orLike('NO_IDENTITAS', $keyword)
                    ->orLike('ALAMAT', $keyword)
                    ->groupEnd();
        }

        $builder->orderBy('NAMA', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function normalizeDate($dateStr, $default = null)
    {
        if (empty($dateStr)) {
            return $default;
        }
        $dateStr = trim((string)$dateStr);
        if ($dateStr === '' || $dateStr === '-' || $dateStr === '0000-00-00') {
            return $default;
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

        return $default;
    }

    public function getNextId($table, $pk)
    {
        $row = $this->db->query("SELECT COALESCE(MAX(`$pk`), 0) + 1 AS next_id FROM `$table`")->getRowArray();
        return (int)($row['next_id'] ?? 1);
    }

    public function getPegawaiSnapshot($idPegawai)
    {
        $pegawai = $this->db->table('pegawai')
            ->select('ID_PEGAWAI, NAMA, NIP')
            ->where('ID_PEGAWAI', $idPegawai)
            ->get()->getRowArray();

        $nama = $pegawai['NAMA'] ?? '';
        $nip  = $pegawai['NIP'] ?? '';

        $penempatan = $this->db->table('detail_penempatan_pegawai')
            ->where('ID_PEGAWAI', $idPegawai)
            ->orderBy('KD_PENEMPATAN', 'DESC')
            ->get()->getRowArray();

        $kdPangkat       = !empty($penempatan['KD_PANGKAT']) ? (int)$penempatan['KD_PANGKAT'] : 1;
        $kdGolongan      = !empty($penempatan['KD_GOLONGAN']) ? (int)$penempatan['KD_GOLONGAN'] : 1;
        $kdBagian        = !empty($penempatan['KD_BAGIAN']) ? (int)$penempatan['KD_BAGIAN'] : 1;
        $kdJabatan       = isset($penempatan['KD_JABATAN']) && $penempatan['KD_JABATAN'] !== null && $penempatan['KD_JABATAN'] !== '' ? (int)$penempatan['KD_JABATAN'] : 0;
        $kdEkselon       = !empty($penempatan['KD_EKSELON']) ? (int)$penempatan['KD_EKSELON'] : 1;
        $kdStatusPegawai = isset($penempatan['KD_STATUS_PEGAWAI']) && $penempatan['KD_STATUS_PEGAWAI'] !== null && $penempatan['KD_STATUS_PEGAWAI'] !== '' ? (int)$penempatan['KD_STATUS_PEGAWAI'] : 1;
        $kdJenisPegawai  = !empty($penempatan['KD_JENIS_PEGAWAI']) ? (int)$penempatan['KD_JENIS_PEGAWAI'] : 1;
        $kdLokasi        = !empty($penempatan['KD_LOKASI']) ? (int)$penempatan['KD_LOKASI'] : 1;

        return [
            'ID_PEGAWAI'        => (string)$idPegawai,
            'NAMA'              => $nama,
            'NIP'               => $nip,
            'KD_PANGKAT'        => $kdPangkat,
            'KD_GOLONGAN'       => $kdGolongan,
            'KD_BAGIAN'         => $kdBagian,
            'KD_JABATAN'        => $kdJabatan,
            'KD_EKSELON'        => $kdEkselon,
            'KD_STATUS_PEGAWAI' => $kdStatusPegawai,
            'KD_JENIS_PEGAWAI'  => $kdJenisPegawai,
            'KD_LOKASI'         => $kdLokasi,
        ];
    }

    public function postPegawai($data)
    {
        try {
            $nextIdRow = $this->db->query("SELECT IFNULL(MAX(CAST(ID_PEGAWAI AS UNSIGNED)), 0) + 1 AS next_id FROM pegawai")->getRowArray();
            $nextId = (string) $nextIdRow['next_id'];

            $tglLahir = $this->normalizeDate($data['TANGGAL_LAHIR'] ?? null);

            $sql = "INSERT INTO pegawai (
                        ID_PEGAWAI, NIP, NAMA, NO_IDENTITAS, KD_IDENTITAS, JENIS_KELAMIN, TEMPAT_LAHIR, TANGGAL_LAHIR,
                        KD_AGAMA, KD_GOLDA, KD_STATUS_PERKAWINAN,
                        KD_PROVINSI, KD_KABUPATEN, KD_KECAMATAN, KD_KELURAHAN, ALAMAT, HANDPHONE, EMAIL, status_aktif
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $this->db->query($sql, [
                $nextId,
                $data['NIP'] ?? '',
                $data['NAMA'] ?? '',
                !empty($data['NO_IDENTITAS']) ? $data['NO_IDENTITAS'] : null,
                !empty($data['NO_IDENTITAS']) ? 1 : null,
                !empty($data['JENIS_KELAMIN']) ? $data['JENIS_KELAMIN'] : null,
                $data['TEMPAT_LAHIR'] ?? null,
                $tglLahir,
                !empty($data['KD_AGAMA']) ? $data['KD_AGAMA'] : null,
                !empty($data['KD_GOLDA']) ? $data['KD_GOLDA'] : null,
                !empty($data['KD_STATUS_PERKAWINAN']) ? $data['KD_STATUS_PERKAWINAN'] : null,
                !empty($data['KD_PROVINSI']) ? $data['KD_PROVINSI'] : null,
                !empty($data['KD_KABUPATEN']) ? $data['KD_KABUPATEN'] : null,
                !empty($data['KD_KECAMATAN']) ? $data['KD_KECAMATAN'] : null,
                !empty($data['KD_KELURAHAN']) ? $data['KD_KELURAHAN'] : null,
                $data['ALAMAT'] ?? null,
                $data['HANDPHONE'] ?? null,
                $data['EMAIL'] ?? null,
                1
            ]);

            return ['code' => 200, 'message' => 'Sukses menambah pegawai', 'id' => $nextId];
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'foreign key constraint fails') !== false) {
                $msg = 'Data relasi wilayah atau master data yang dipilih tidak valid dalam sistem database.';
            }
            return ['code' => 500, 'message' => $msg];
        }
    }

    public function updatePegawai($id, $data)
    {
        try {
            $updateData = [
                'NIP'                  => $data['NIP'] ?? '',
                'NAMA'                 => $data['NAMA'] ?? '',
                'NO_IDENTITAS'         => !empty($data['NO_IDENTITAS']) ? $data['NO_IDENTITAS'] : null,
                'JENIS_KELAMIN'        => !empty($data['JENIS_KELAMIN']) ? $data['JENIS_KELAMIN'] : null,
                'TEMPAT_LAHIR'         => $data['TEMPAT_LAHIR'] ?? null,
                'TANGGAL_LAHIR'        => $this->normalizeDate($data['TANGGAL_LAHIR'] ?? null),
                'KD_AGAMA'             => !empty($data['KD_AGAMA']) ? $data['KD_AGAMA'] : null,
                'KD_PROVINSI'          => !empty($data['KD_PROVINSI']) ? $data['KD_PROVINSI'] : null,
                'KD_KABUPATEN'         => !empty($data['KD_KABUPATEN']) ? $data['KD_KABUPATEN'] : null,
                'KD_KECAMATAN'         => !empty($data['KD_KECAMATAN']) ? $data['KD_KECAMATAN'] : null,
                'KD_KELURAHAN'         => !empty($data['KD_KELURAHAN']) ? $data['KD_KELURAHAN'] : null,
                'ALAMAT'               => $data['ALAMAT'] ?? null,
                'HANDPHONE'            => $data['HANDPHONE'] ?? null,
                'EMAIL'                => $data['EMAIL'] ?? null
            ];
            if (!empty($data['NO_IDENTITAS'])) {
                $updateData['KD_IDENTITAS'] = 1;
            }

            $builder = $this->db->table('pegawai');
            $builder->where('ID_PEGAWAI', $id);
            $builder->update($updateData);

            return ['code' => 200, 'message' => 'Data pegawai berhasil diperbarui'];
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'foreign key constraint fails') !== false) {
                $msg = 'Data relasi wilayah atau master data yang dipilih tidak valid dalam sistem database.';
            }
            return ['code' => 500, 'message' => $msg];
        }
    }

    public function deletePegawai($id)
    {
        try {
            $check = $this->db->table('pegawai')->where('ID_PEGAWAI', $id)->get()->getRowArray();
            if (!$check) {
                return ['code' => 404, 'message' => 'Data pegawai dengan ID ' . $id . ' tidak ditemukan'];
            }

            $builder = $this->db->table('pegawai');
            $builder->where('ID_PEGAWAI', $id);
            $builder->update(['status_aktif' => 0]);

            return ['code' => 200, 'message' => 'Data pegawai berhasil dinonaktifkan'];
        } catch (\Throwable $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // LOOKUP & REFERENCE HELPERS
    // ==========================================
    public function getPegawaiLookup()
    {
        return $this->db->query("
            SELECT ID_PEGAWAI, NIP, NAMA 
            FROM pegawai 
            WHERE status_aktif = 1 
            ORDER BY NAMA ASC
        ")->getResultArray();
    }

    public function getRefData()
    {
        return [
            'bagian' => $this->db->query("SELECT KD_BAGIAN, BAGIAN FROM bagian WHERE STATUS_AKTIF = 1 ORDER BY BAGIAN ASC")->getResultArray(),
            'jabatan' => $this->db->query("SELECT KD_JABATAN, JABATAN FROM jabatan ORDER BY JABATAN ASC")->getResultArray(),
            'pangkat' => $this->db->query("SELECT KD_PANGKAT, PANGKAT FROM pangkat ORDER BY KD_PANGKAT ASC")->getResultArray(),
            'golongan' => $this->db->query("SELECT KD_GOLONGAN, GOLONGAN FROM golongan ORDER BY KD_GOLONGAN ASC")->getResultArray(),
            'jenis_sip' => $this->db->query("SELECT KD_JENIS_SIP, JENIS_SIP FROM jenis_sip ORDER BY JENIS_SIP ASC")->getResultArray(),
            'jenis_kontrak' => $this->db->query("SELECT KD_JENIS_KONTRAK, JENIS_KONTRAK FROM jenis_kontrak ORDER BY JENIS_KONTRAK ASC")->getResultArray(),
            'jenis_phk' => $this->db->query("SELECT KD_JENIS_PHK, JENIS_PHK FROM jenis_phk ORDER BY JENIS_PHK ASC")->getResultArray(),
            'jenis_sanksi' => $this->db->query("SELECT KD_JENIS_SANKSI, JENIS_SANKSI FROM jenis_sanksi ORDER BY JENIS_SANKSI ASC")->getResultArray(),
            'jenis_penghargaan' => $this->db->query("SELECT KD_JENIS_PENGHARGAAN, JENIS_PENGHARGAAN FROM jenis_penghargaan ORDER BY JENIS_PENGHARGAAN ASC")->getResultArray(),
            'jenis_catatan' => $this->db->query("SELECT KD_JENIS_CATATAN, JENIS_CATATAN FROM jenis_catatan ORDER BY JENIS_CATATAN ASC")->getResultArray(),
            'jenis_cuti' => $this->db->query("SELECT KD_JENIS_CUTI, JENIS_CUTI, TOTAL_CUTI FROM jenis_cuti ORDER BY JENIS_CUTI ASC")->getResultArray(),
            'jenjang_pendidikan' => $this->db->query("SELECT KD_JENJANG, JENJANG_PENDIDIKAN FROM jenjang_pendidikan ORDER BY KD_JENJANG ASC")->getResultArray(),
        ];
    }

    // ==========================================
    // ==========================================
    // 1. PENEMPATAN & UNIT KERJA
    // ==========================================
    public function getPenempatan($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');
        $kd_bagian = trim($params['kd_bagian'] ?? '');

        $sql = "SELECT dp.*, 
                       p.NAMA, 
                       p.NIP, 
                       b.BAGIAN, 
                       j.JABATAN, 
                       pkt.PANGKAT, 
                       g.GOLONGAN, 
                       e.EKSELON, 
                       sp.STATUS_PEGAWAI, 
                       jp.JENIS_PEGAWAI, 
                       l.LOKASI
                FROM detail_penempatan_pegawai dp
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = dp.ID_PEGAWAI
                LEFT JOIN bagian b ON b.KD_BAGIAN = dp.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = dp.KD_JABATAN
                LEFT JOIN pangkat pkt ON pkt.KD_PANGKAT = dp.KD_PANGKAT
                LEFT JOIN golongan g ON g.KD_GOLONGAN = dp.KD_GOLONGAN
                LEFT JOIN ekselon e ON e.KD_EKSELON = dp.KD_EKSELON
                LEFT JOIN status_pegawai sp ON sp.KD_STATUS_PEGAWAI = dp.KD_STATUS_PEGAWAI
                LEFT JOIN jenis_pegawai jp ON jp.KD_JENIS_PEGAWAI = dp.KD_JENIS_PEGAWAI
                LEFT JOIN lokasi l ON l.KD_LOKASI = dp.KD_LOKASI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR p.NIP LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        if ($kd_bagian !== '') {
            $sql .= " AND dp.KD_BAGIAN = ?";
            $binds[] = $kd_bagian;
        }

        $sql .= " ORDER BY dp.KD_PENEMPATAN DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function savePenempatan($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_PENEMPATAN'] ?? '';

            $tglMulai = $this->normalizeDate($data['TANGGAL_MULAI'] ?? null, date('Y-m-d'));
            $tglSampai = !empty($data['TANGGAL_SAMPAI']) ? $this->normalizeDate($data['TANGGAL_SAMPAI'], null) : null;
            $tglSk = !empty($data['TANGGAL_SK_PENGANGKATAN']) ? $this->normalizeDate($data['TANGGAL_SK_PENGANGKATAN'], null) : null;

            $record = [
                'ID_PEGAWAI'              => $idPegawai,
                'KD_BAGIAN'               => !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $snapshot['KD_BAGIAN'],
                'KD_GOLONGAN'             => !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $snapshot['KD_GOLONGAN'],
                'KD_JABATAN'              => isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $snapshot['KD_JABATAN'],
                'KD_PANGKAT'              => !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : $snapshot['KD_PANGKAT'],
                'KD_EKSELON'              => !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $snapshot['KD_EKSELON'],
                'KD_JENIS_PEGAWAI'        => !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $snapshot['KD_JENIS_PEGAWAI'],
                'KD_STATUS_PEGAWAI'       => isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $snapshot['KD_STATUS_PEGAWAI'],
                'KD_LOKASI'               => !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $snapshot['KD_LOKASI'],
                'TANGGAL_MULAI'           => $tglMulai,
                'TANGGAL_SAMPAI'          => $tglSampai,
                'TANGGAL_SK_PENGANGKATAN' => $tglSk,
                'TANGGAL_PENSIUN'         => !empty($data['TANGGAL_PENSIUN']) ? $this->normalizeDate($data['TANGGAL_PENSIUN'], null) : null,
                'TANGGAL_KONTRAK_AWAL'    => !empty($data['TANGGAL_KONTRAK_AWAL']) ? $this->normalizeDate($data['TANGGAL_KONTRAK_AWAL'], null) : null,
                'TANGGAL_KONTRAK_AKHIR'   => !empty($data['TANGGAL_KONTRAK_AKHIR']) ? $this->normalizeDate($data['TANGGAL_KONTRAK_AKHIR'], null) : null,
                'KD_SUBBAGIAN'            => !empty($data['KD_SUBBAGIAN']) ? (int)$data['KD_SUBBAGIAN'] : null,
                'JENIS_PENEMPATAN'        => isset($data['JENIS_PENEMPATAN']) && $data['JENIS_PENEMPATAN'] !== '' ? (int)$data['JENIS_PENEMPATAN'] : 1,
            ];

            if (!empty($data['IMAGE'])) {
                $record['IMAGE'] = $data['IMAGE'];
            }

            if (empty($kd)) {
                $record['KD_PENEMPATAN'] = $this->getNextId('detail_penempatan_pegawai', 'KD_PENEMPATAN');
                $this->db->table('detail_penempatan_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Data penempatan berhasil disimpan'];
            } else {
                $this->db->table('detail_penempatan_pegawai')->where('KD_PENEMPATAN', $kd)->update($record);
                return ['code' => 200, 'message' => 'Data penempatan berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deletePenempatan($id)
    {
        try {
            $this->db->table('detail_penempatan_pegawai')->where('KD_PENEMPATAN', $id)->delete();
            return ['code' => 200, 'message' => 'Data penempatan berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 2. STR (SURAT TANDA REGISTRASI)
    // ==========================================
    public function getSTR($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');
        $status = trim($params['status'] ?? '');

        $sql = "SELECT s.*,
                       s.MULAI AS TGL_MULAI,
                       s.SAMPAI AS TGL_SAMPAI,
                       COALESCE(p.NAMA, s.NAMA_STR) AS NAMA,
                       p.NIP,
                       DATEDIFF(s.SAMPAI, CURDATE()) AS SISA_HARI,
                       CASE 
                           WHEN s.SAMPAI < CURDATE() THEN 'EXPIRED'
                           WHEN DATEDIFF(s.SAMPAI, CURDATE()) <= 90 THEN 'WARNING'
                           ELSE 'AKTIF'
                       END AS STATUS_MASA
                FROM detail_str_pegawai s
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = s.ID_PEGAWAI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR p.NIP LIKE ? OR s.NO_STR LIKE ? OR s.NAMA_STR LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        if ($status === 'EXPIRED') {
            $sql .= " AND s.SAMPAI < CURDATE()";
        } elseif ($status === 'WARNING') {
            $sql .= " AND s.SAMPAI >= CURDATE() AND DATEDIFF(s.SAMPAI, CURDATE()) <= 90";
        } elseif ($status === 'AKTIF') {
            $sql .= " AND DATEDIFF(s.SAMPAI, CURDATE()) > 90";
        }

        $sql .= " ORDER BY s.SAMPAI ASC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveSTR($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai wajib dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_STR'] ?? '';

            $noStr = !empty($data['NO_STR']) ? trim($data['NO_STR']) : '-';
            $namaStr = !empty($data['NAMA_STR']) ? trim($data['NAMA_STR']) : (!empty($data['NAMA']) ? trim($data['NAMA']) : 'STR ' . $snapshot['NAMA']);
            $pengesah = !empty($data['YANG_MENGESAHKAN']) ? trim($data['YANG_MENGESAHKAN']) : 'KTKI / KKI';
            $mulai = $this->normalizeDate($data['MULAI'] ?? $data['TGL_MULAI'] ?? null, date('Y-m-d'));
            $sampai = $this->normalizeDate($data['SAMPAI'] ?? $data['TGL_SAMPAI'] ?? null, date('Y-m-d', strtotime('+5 years')));

            $record = [
                'ID_PEGAWAI'       => $idPegawai,
                'NO_STR'           => $noStr,
                'NAMA_STR'         => $namaStr,
                'YANG_MENGESAHKAN' => $pengesah,
                'MULAI'            => $mulai,
                'SAMPAI'           => $sampai,
            ];

            if (isset($data['IMAGE']) && $data['IMAGE'] !== '') {
                $record['IMAGE'] = $data['IMAGE'];
            }

            if (empty($kd)) {
                $this->db->table('detail_str_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'STR berhasil disimpan'];
            } else {
                $this->db->table('detail_str_pegawai')->where('KD_STR', $kd)->update($record);
                return ['code' => 200, 'message' => 'STR berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteSTR($id)
    {
        try {
            $this->db->table('detail_str_pegawai')->where('KD_STR', $id)->delete();
            return ['code' => 200, 'message' => 'STR berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 3. SIP (SURAT IZIN PRAKTIK)
    // ==========================================
    public function getSIP($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');
        $kd_jenis_sip = trim($params['kd_jenis_sip'] ?? '');
        $status = trim($params['status'] ?? '');

        $sql = "SELECT s.*,
                       s.MULAI AS TGL_MULAI,
                       s.SAMPAI AS TGL_SAMPAI,
                       js.JENIS_SIP,
                       COALESCE(p.NAMA, s.NAMA_SIP) AS NAMA,
                       p.NIP,
                       DATEDIFF(s.SAMPAI, CURDATE()) AS SISA_HARI,
                       CASE 
                           WHEN s.SAMPAI < CURDATE() THEN 'EXPIRED'
                           WHEN DATEDIFF(s.SAMPAI, CURDATE()) <= 90 THEN 'WARNING'
                           ELSE 'AKTIF'
                       END AS STATUS_MASA
                FROM detail_sip_pegawai s
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = s.ID_PEGAWAI
                LEFT JOIN jenis_sip js ON js.KD_JENIS_SIP = s.KD_JENIS_SIP
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR p.NIP LIKE ? OR s.NO_SIP LIKE ? OR s.NAMA_SIP LIKE ? OR js.JENIS_SIP LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        if ($kd_jenis_sip !== '') {
            $sql .= " AND s.KD_JENIS_SIP = ?";
            $binds[] = $kd_jenis_sip;
        }

        if ($status === 'EXPIRED') {
            $sql .= " AND s.SAMPAI < CURDATE()";
        } elseif ($status === 'WARNING') {
            $sql .= " AND s.SAMPAI >= CURDATE() AND DATEDIFF(s.SAMPAI, CURDATE()) <= 90";
        } elseif ($status === 'AKTIF') {
            $sql .= " AND DATEDIFF(s.SAMPAI, CURDATE()) > 90";
        }

        $sql .= " ORDER BY s.SAMPAI ASC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveSIP($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai wajib dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_SIP'] ?? '';

            $noSip = !empty($data['NO_SIP']) ? trim($data['NO_SIP']) : '-';
            $namaSip = !empty($data['NAMA_SIP']) ? trim($data['NAMA_SIP']) : (!empty($data['NAMA']) ? trim($data['NAMA']) : 'SIP ' . $snapshot['NAMA']);
            $pengesah = !empty($data['YANG_MENGESAHKAN']) ? trim($data['YANG_MENGESAHKAN']) : 'Dinas Kesehatan';
            $mulai = $this->normalizeDate($data['MULAI'] ?? $data['TGL_MULAI'] ?? null, date('Y-m-d'));
            $sampai = $this->normalizeDate($data['SAMPAI'] ?? $data['TGL_SAMPAI'] ?? null, date('Y-m-d', strtotime('+5 years')));
            $kdJenisSip = !empty($data['KD_JENIS_SIP']) ? (int)$data['KD_JENIS_SIP'] : 1;

            $record = [
                'ID_PEGAWAI'       => $idPegawai,
                'NO_SIP'           => $noSip,
                'NAMA_SIP'         => $namaSip,
                'KD_JENIS_SIP'     => $kdJenisSip,
                'YANG_MENGESAHKAN' => $pengesah,
                'MULAI'            => $mulai,
                'SAMPAI'           => $sampai,
            ];

            if (isset($data['IMAGE']) && $data['IMAGE'] !== '') {
                $record['IMAGE'] = $data['IMAGE'];
            }

            if (empty($kd)) {
                $this->db->table('detail_sip_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'SIP berhasil disimpan'];
            } else {
                $this->db->table('detail_sip_pegawai')->where('KD_SIP', $kd)->update($record);
                return ['code' => 200, 'message' => 'SIP berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteSIP($id)
    {
        try {
            $this->db->table('detail_sip_pegawai')->where('KD_SIP', $id)->delete();
            return ['code' => 200, 'message' => 'SIP berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 4. RIWAYAT PENDIDIKAN
    // ==========================================
    public function getPendidikan($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');
        $kd_jenjang = trim($params['kd_jenjang'] ?? '');

        $sql = "SELECT dp.*,
                       dp.NAMA_LEMBAGA AS SEKOLAH_INSTANSI,
                       dp.PERIODE_SAMPAI AS TGL_LULUS,
                       dp.PERIODE_MULAI AS TGL_MULAI,
                       dp.KD_STATUS_LULUS AS STATUS_LULUS,
                       p.NAMA,
                       p.NIP,
                       jp.JENJANG_PENDIDIKAN
                FROM detail_pendidikan_pegawai dp
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = dp.ID_PEGAWAI
                LEFT JOIN jenjang_pendidikan jp ON jp.KD_JENJANG = dp.KD_JENJANG
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR p.NIP LIKE ? OR dp.NAMA_LEMBAGA LIKE ? OR dp.JURUSAN LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        if ($kd_jenjang !== '') {
            $sql .= " AND dp.KD_JENJANG = ?";
            $binds[] = $kd_jenjang;
        }

        $sql .= " ORDER BY dp.KD_PENDIDIKAN DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function savePendidikan($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai wajib dipilih'];
            }

            $kd = $data['KD_PENDIDIKAN'] ?? '';
            $namaLembaga = !empty($data['NAMA_LEMBAGA']) ? trim($data['NAMA_LEMBAGA']) : (!empty($data['SEKOLAH_INSTANSI']) ? trim($data['SEKOLAH_INSTANSI']) : '-');
            $kdJenjang = !empty($data['KD_JENJANG']) ? (int)$data['KD_JENJANG'] : 1;
            $jurusan = !empty($data['JURUSAN']) ? trim($data['JURUSAN']) : '-';
            $alamat = !empty($data['ALAMAT']) ? trim($data['ALAMAT']) : '-';
            $noIjazah = !empty($data['NO_IJAZAH']) ? trim($data['NO_IJAZAH']) : '-';
            $ipk = !empty($data['IPK']) ? (string)$data['IPK'] : '0.00';
            $periodeMulai = $this->normalizeDate($data['PERIODE_MULAI'] ?? $data['TGL_MULAI'] ?? null, date('Y-m-d'));
            $periodeSampai = $this->normalizeDate($data['PERIODE_SAMPAI'] ?? $data['TGL_LULUS'] ?? $data['TGL_SAMPAI'] ?? null, date('Y-m-d'));
            $statusLulus = !empty($data['KD_STATUS_LULUS']) ? (int)$data['KD_STATUS_LULUS'] : (!empty($data['STATUS_LULUS']) ? (int)$data['STATUS_LULUS'] : 1);
            $jenisPendidikan = !empty($data['KD_JENIS_PENDIDIKAN']) ? (int)$data['KD_JENIS_PENDIDIKAN'] : 1;
            $namaPejabat = !empty($data['NAMA_PEJABAT']) ? trim($data['NAMA_PEJABAT']) : '-';

            $record = [
                'ID_PEGAWAI'          => $idPegawai,
                'NAMA_LEMBAGA'        => $namaLembaga,
                'KD_JENJANG'          => $kdJenjang,
                'ALAMAT'              => $alamat,
                'PERIODE_MULAI'       => $periodeMulai,
                'PERIODE_SAMPAI'      => $periodeSampai,
                'IPK'                 => $ipk,
                'KD_STATUS_LULUS'     => $statusLulus,
                'KD_JENIS_PENDIDIKAN' => $jenisPendidikan,
                'NAMA_PEJABAT'        => $namaPejabat,
                'NO_IJAZAH'           => $noIjazah,
                'JURUSAN'             => $jurusan
            ];

            if (isset($data['KD_JURUSAN']) && $data['KD_JURUSAN'] !== '') {
                $record['KD_JURUSAN'] = (int)$data['KD_JURUSAN'];
            }
            if (isset($data['GAMBAR_IJAZAH']) && $data['GAMBAR_IJAZAH'] !== '') {
                $record['GAMBAR_IJAZAH'] = $data['GAMBAR_IJAZAH'];
            }

            if (empty($kd)) {
                $record['KD_PENDIDIKAN'] = $this->getNextId('detail_pendidikan_pegawai', 'KD_PENDIDIKAN');
                $this->db->table('detail_pendidikan_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Riwayat pendidikan berhasil ditambahkan'];
            } else {
                $this->db->table('detail_pendidikan_pegawai')->where('KD_PENDIDIKAN', $kd)->update($record);
                return ['code' => 200, 'message' => 'Riwayat pendidikan berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deletePendidikan($id)
    {
        try {
            $this->db->table('detail_pendidikan_pegawai')->where('KD_PENDIDIKAN', $id)->delete();
            return ['code' => 200, 'message' => 'Data pendidikan berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 5. PELATIHAN / DIKLAT
    // ==========================================
    public function getPelatihan($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT dp.*,
                       dp.LOKASI_PELATIHAN AS LOKASI,
                       dp.NARA_SUMBER AS PENYELENGGARA,
                       p.NAMA,
                       p.NIP
                FROM detail_pelatihan_pegawai dp
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = dp.ID_PEGAWAI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR p.NIP LIKE ? OR dp.NAMA_PELATIHAN LIKE ? OR dp.NARA_SUMBER LIKE ? OR dp.LOKASI_PELATIHAN LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY dp.KD_PELATIHAN DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function savePelatihan($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai wajib dipilih'];
            }

            $kd = $data['KD_PELATIHAN'] ?? '';
            $namaPelatihan = !empty($data['NAMA_PELATIHAN']) ? trim($data['NAMA_PELATIHAN']) : '-';
            $lokasi = !empty($data['LOKASI_PELATIHAN']) ? trim($data['LOKASI_PELATIHAN']) : (!empty($data['LOKASI']) ? trim($data['LOKASI']) : '-');
            $narasumber = !empty($data['NARA_SUMBER']) ? trim($data['NARA_SUMBER']) : (!empty($data['PENYELENGGARA']) ? trim($data['PENYELENGGARA']) : '-');
            $tglPelatihan = $this->normalizeDate($data['TGL_PELATIHAN'] ?? null, date('Y-m-d'));
            $tglAkhir = $this->normalizeDate($data['TGL_PELATIHAN_AKHIR'] ?? $data['TGL_SELESAI'] ?? $data['TGL_PELATIHAN'] ?? null, $tglPelatihan);
            $pointSkp = isset($data['POINT_SKP']) ? (int)$data['POINT_SKP'] : 0;

            $record = [
                'ID_PEGAWAI'          => $idPegawai,
                'TGL_PELATIHAN'       => $tglPelatihan,
                'TGL_PELATIHAN_AKHIR' => $tglAkhir,
                'NAMA_PELATIHAN'      => $namaPelatihan,
                'LOKASI_PELATIHAN'    => $lokasi,
                'NARA_SUMBER'         => $narasumber,
                'POINT_SKP'           => $pointSkp,
            ];

            if (isset($data['IMAGE']) && $data['IMAGE'] !== '') {
                $record['IMAGE'] = $data['IMAGE'];
            }

            if (empty($kd)) {
                $this->db->table('detail_pelatihan_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Pelatihan berhasil ditambahkan'];
            } else {
                $this->db->table('detail_pelatihan_pegawai')->where('KD_PELATIHAN', $kd)->update($record);
                return ['code' => 200, 'message' => 'Pelatihan berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deletePelatihan($id)
    {
        try {
            $this->db->table('detail_pelatihan_pegawai')->where('KD_PELATIHAN', $id)->delete();
            return ['code' => 200, 'message' => 'Data pelatihan berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 6. MUTASI PEGAWAI
    // ==========================================
    public function getMutasi($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT md.*,
                       mm.KD_MUTASI_MENJADI,
                       mm.TGL_MUTASI,
                       mm.KD_BAGIAN AS KD_BAGIAN_BARU,
                       mm.KD_JABATAN AS KD_JABATAN_BARU,
                       mm.KD_BAGIAN AS KD_BAGIAN_TUJUAN,
                       mm.KD_JABATAN AS KD_JABATAN_TUJUAN,
                       mm.KETERANGAN,
                       p.NAMA AS NAMA_PEGAWAI,
                       b_asal.BAGIAN AS BAGIAN_ASAL,
                       b_baru.BAGIAN AS BAGIAN_BARU,
                       j_asal.JABATAN AS JABATAN_ASAL,
                       j_baru.JABATAN AS JABATAN_BARU
                FROM detail_mutasi_dari_pegawai md
                LEFT JOIN detail_mutasi_menjadi_pegawai mm ON mm.KD_MUTASI_DARI = md.KD_MUTASI_DARI
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = md.ID_PEGAWAI
                LEFT JOIN bagian b_asal ON b_asal.KD_BAGIAN = md.KD_BAGIAN
                LEFT JOIN bagian b_baru ON b_baru.KD_BAGIAN = mm.KD_BAGIAN
                LEFT JOIN jabatan j_asal ON j_asal.KD_JABATAN = md.KD_JABATAN
                LEFT JOIN jabatan j_baru ON j_baru.KD_JABATAN = mm.KD_JABATAN
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR md.NIP LIKE ? OR md.NO_SURAT LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY md.KD_MUTASI_DARI DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveMutasi($data)
    {
        $this->db->transBegin();
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);

            $kdDari = $data['KD_MUTASI_DARI'] ?? null;
            $isUpdate = !empty($kdDari);

            $tglMutasi = $this->normalizeDate($data['TGL_MUTASI'] ?? null, date('Y-m-d'));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? null, $tglMutasi);
            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : ('MUT/' . date('Ymd') . '/' . $idPegawai);

            // Asal
            $kdBagianAsal  = !empty($data['KD_BAGIAN_ASAL']) ? (int)$data['KD_BAGIAN_ASAL'] : $snapshot['KD_BAGIAN'];
            $kdJabatanAsal = isset($data['KD_JABATAN_ASAL']) && $data['KD_JABATAN_ASAL'] !== '' ? (int)$data['KD_JABATAN_ASAL'] : $snapshot['KD_JABATAN'];
            $kdEkselonAsal = !empty($data['KD_EKSELON_ASAL']) ? (int)$data['KD_EKSELON_ASAL'] : $snapshot['KD_EKSELON'];
            $kdStatusAsal  = isset($data['KD_STATUS_PEGAWAI_ASAL']) && $data['KD_STATUS_PEGAWAI_ASAL'] !== '' ? (int)$data['KD_STATUS_PEGAWAI_ASAL'] : $snapshot['KD_STATUS_PEGAWAI'];
            $kdGolAsal     = !empty($data['KD_GOLONGAN_ASAL']) ? (int)$data['KD_GOLONGAN_ASAL'] : $snapshot['KD_GOLONGAN'];
            $kdPangkatAsal = !empty($data['KD_PANGKAT_ASAL']) ? (int)$data['KD_PANGKAT_ASAL'] : $snapshot['KD_PANGKAT'];
            $kdJenisAsal   = !empty($data['KD_JENIS_PEGAWAI_ASAL']) ? (int)$data['KD_JENIS_PEGAWAI_ASAL'] : $snapshot['KD_JENIS_PEGAWAI'];
            $kdLokasiAsal  = !empty($data['KD_LOKASI_ASAL']) ? (int)$data['KD_LOKASI_ASAL'] : $snapshot['KD_LOKASI'];

            // Tujuan / Baru
            $kdBagianBaru = !empty($data['KD_BAGIAN_BARU']) ? (int)$data['KD_BAGIAN_BARU'] : (!empty($data['KD_BAGIAN_TUJUAN']) ? (int)$data['KD_BAGIAN_TUJUAN'] : (!empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $kdBagianAsal));
            $kdJabatanBaru = isset($data['KD_JABATAN_BARU']) && $data['KD_JABATAN_BARU'] !== '' ? (int)$data['KD_JABATAN_BARU'] : (isset($data['KD_JABATAN_TUJUAN']) && $data['KD_JABATAN_TUJUAN'] !== '' ? (int)$data['KD_JABATAN_TUJUAN'] : (isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $kdJabatanAsal));
            $kdEkselonBaru = !empty($data['KD_EKSELON_BARU']) ? (int)$data['KD_EKSELON_BARU'] : $kdEkselonAsal;
            $kdStatusBaru  = isset($data['KD_STATUS_PEGAWAI_BARU']) && $data['KD_STATUS_PEGAWAI_BARU'] !== '' ? (int)$data['KD_STATUS_PEGAWAI_BARU'] : $kdStatusAsal;
            $kdGolBaru     = !empty($data['KD_GOLONGAN_BARU']) ? (int)$data['KD_GOLONGAN_BARU'] : $kdGolAsal;
            $kdPangkatBaru = !empty($data['KD_PANGKAT_BARU']) ? (int)$data['KD_PANGKAT_BARU'] : $kdPangkatAsal;
            $kdJenisBaru   = !empty($data['KD_JENIS_PEGAWAI_BARU']) ? (int)$data['KD_JENIS_PEGAWAI_BARU'] : $kdJenisAsal;
            $kdLokasiBaru  = !empty($data['KD_LOKASI_BARU']) ? (int)$data['KD_LOKASI_BARU'] : $kdLokasiAsal;

            $nipPimpinan   = !empty($data['NIP_PIMPINAN']) ? $data['NIP_PIMPINAN'] : '-';
            $namaPimpinan  = !empty($data['NAMA_PIMPINAN']) ? $data['NAMA_PIMPINAN'] : '-';
            $keterangan    = !empty($data['KETERANGAN']) ? $data['KETERANGAN'] : '-';

            $dtDari = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $snapshot['NIP'],
                'NAMA'              => $snapshot['NAMA'],
                'KD_BAGIAN'         => $kdBagianAsal,
                'KD_JABATAN'        => $kdJabatanAsal,
                'KD_EKSELON'        => $kdEkselonAsal,
                'KD_STATUS_PEGAWAI' => $kdStatusAsal,
                'KD_GOLONGAN'       => $kdGolAsal,
                'KD_PANGKAT'        => $kdPangkatAsal,
                'KD_JENIS_PEGAWAI'  => $kdJenisAsal,
                'KD_LOKASI'         => $kdLokasiAsal,
            ];

            if (!$isUpdate) {
                $nextDari = $this->getNextId('detail_mutasi_dari_pegawai', 'KD_MUTASI_DARI');
                $dtDari['KD_MUTASI_DARI'] = $nextDari;
                $this->db->table('detail_mutasi_dari_pegawai')->insert($dtDari);
                $kdDari = $nextDari;

                $nextMenjadi = $this->getNextId('detail_mutasi_menjadi_pegawai', 'KD_MUTASI_MENJADI');
                $dtMenjadi = [
                    'KD_MUTASI_MENJADI' => $nextMenjadi,
                    'KD_MUTASI_DARI'    => $kdDari,
                    'NIP_PIMPINAN'      => $nipPimpinan,
                    'NAMA_PIMPINAN'     => $namaPimpinan,
                    'TGL_MUTASI'        => $tglMutasi,
                    'KD_BAGIAN'         => $kdBagianBaru,
                    'KD_JABATAN'        => $kdJabatanBaru,
                    'KD_EKSELON'        => $kdEkselonBaru,
                    'KD_STATUS_PEGAWAI' => $kdStatusBaru,
                    'KD_GOLONGAN'       => $kdGolBaru,
                    'KD_PANGKAT'        => $kdPangkatBaru,
                    'KD_JENIS_PEGAWAI'  => $kdJenisBaru,
                    'KD_LOKASI'         => $kdLokasiBaru,
                    'KETERANGAN'        => $keterangan,
                    'ID_PEGAWAI'        => $idPegawai,
                ];
                $this->db->table('detail_mutasi_menjadi_pegawai')->insert($dtMenjadi);
            } else {
                $this->db->table('detail_mutasi_dari_pegawai')->where('KD_MUTASI_DARI', $kdDari)->update($dtDari);

                $dtMenjadi = [
                    'NIP_PIMPINAN'      => $nipPimpinan,
                    'NAMA_PIMPINAN'     => $namaPimpinan,
                    'TGL_MUTASI'        => $tglMutasi,
                    'KD_BAGIAN'         => $kdBagianBaru,
                    'KD_JABATAN'        => $kdJabatanBaru,
                    'KD_EKSELON'        => $kdEkselonBaru,
                    'KD_STATUS_PEGAWAI' => $kdStatusBaru,
                    'KD_GOLONGAN'       => $kdGolBaru,
                    'KD_PANGKAT'        => $kdPangkatBaru,
                    'KD_JENIS_PEGAWAI'  => $kdJenisBaru,
                    'KD_LOKASI'         => $kdLokasiBaru,
                    'KETERANGAN'        => $keterangan,
                    'ID_PEGAWAI'        => $idPegawai,
                ];
                $exists = $this->db->table('detail_mutasi_menjadi_pegawai')->where('KD_MUTASI_DARI', $kdDari)->countAllResults();
                if ($exists > 0) {
                    $this->db->table('detail_mutasi_menjadi_pegawai')->where('KD_MUTASI_DARI', $kdDari)->update($dtMenjadi);
                } else {
                    $dtMenjadi['KD_MUTASI_MENJADI'] = $this->getNextId('detail_mutasi_menjadi_pegawai', 'KD_MUTASI_MENJADI');
                    $dtMenjadi['KD_MUTASI_DARI'] = $kdDari;
                    $this->db->table('detail_mutasi_menjadi_pegawai')->insert($dtMenjadi);
                }
            }

            // Catat ke detail penempatan
            $this->savePenempatan([
                'ID_PEGAWAI'              => $idPegawai,
                'KD_BAGIAN'               => $kdBagianBaru,
                'KD_GOLONGAN'             => $kdGolBaru,
                'KD_JABATAN'              => $kdJabatanBaru,
                'KD_PANGKAT'              => $kdPangkatBaru,
                'KD_EKSELON'              => $kdEkselonBaru,
                'KD_JENIS_PEGAWAI'        => $kdJenisBaru,
                'KD_STATUS_PEGAWAI'       => $kdStatusBaru,
                'KD_LOKASI'               => $kdLokasiBaru,
                'TANGGAL_MULAI'           => $tglMutasi,
                'NO_SK_PENGANGKATAN'      => $noSurat,
                'TANGGAL_SK_PENGANGKATAN' => $tglSurat,
                'KETERANGAN'              => 'Mutasi: ' . $keterangan
            ]);

            $this->db->transCommit();
            return ['code' => 200, 'message' => $isUpdate ? 'Mutasi berhasil diperbarui' : 'Mutasi berhasil dicatat'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteMutasi($id)
    {
        try {
            $this->db->table('detail_mutasi_menjadi_pegawai')->where('KD_MUTASI_DARI', $id)->delete();
            $this->db->table('detail_mutasi_dari_pegawai')->where('KD_MUTASI_DARI', $id)->delete();
            return ['code' => 200, 'message' => 'Mutasi berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 7. PROMOSI PEGAWAI
    // ==========================================
    public function getPromosi($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT pd.*,
                       pm.KD_PROMOSI_MENJADI,
                       pm.TGL_PROMOSI,
                       pm.KD_BAGIAN AS KD_BAGIAN_BARU,
                       pm.KD_JABATAN AS KD_JABATAN_BARU,
                       pm.KD_PANGKAT AS KD_PANGKAT_BARU,
                       pm.KD_GOLONGAN AS KD_GOLONGAN_BARU,
                       pm.KETERANGAN,
                       p.NAMA AS NAMA_PEGAWAI,
                       b_asal.BAGIAN AS BAGIAN_ASAL,
                       b_baru.BAGIAN AS BAGIAN_BARU,
                       j_asal.JABATAN AS JABATAN_ASAL,
                       j_baru.JABATAN AS JABATAN_BARU,
                       pkt_asal.PANGKAT AS PANGKAT_ASAL,
                       pkt_baru.PANGKAT AS PANGKAT_BARU
                FROM detail_promosi_dari_pegawai pd
                LEFT JOIN detail_promosi_menjadi_pegawai pm ON pm.KD_PROMOSI_DARI = pd.KD_PROMOSI_DARI
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = pd.ID_PEGAWAI
                LEFT JOIN bagian b_asal ON b_asal.KD_BAGIAN = pd.KD_BAGIAN
                LEFT JOIN bagian b_baru ON b_baru.KD_BAGIAN = pm.KD_BAGIAN
                LEFT JOIN jabatan j_asal ON j_asal.KD_JABATAN = pd.KD_JABATAN
                LEFT JOIN jabatan j_baru ON j_baru.KD_JABATAN = pm.KD_JABATAN
                LEFT JOIN pangkat pkt_asal ON pkt_asal.KD_PANGKAT = pd.KD_PANGKAT
                LEFT JOIN pangkat pkt_baru ON pkt_baru.KD_PANGKAT = pm.KD_PANGKAT
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR pd.NIP LIKE ? OR pd.NO_SURAT LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY pd.KD_PROMOSI_DARI DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function savePromosi($data)
    {
        $this->db->transBegin();
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);

            $kdDari = $data['KD_PROMOSI_DARI'] ?? null;
            $isUpdate = !empty($kdDari);

            $tglPromosi = $this->normalizeDate($data['TGL_PROMOSI'] ?? null, date('Y-m-d'));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? null, $tglPromosi);
            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : ('SK-PROM/' . date('Ymd') . '/' . $idPegawai);

            // Asal
            $kdBagianAsal  = !empty($data['KD_BAGIAN_ASAL']) ? (int)$data['KD_BAGIAN_ASAL'] : $snapshot['KD_BAGIAN'];
            $kdJabatanAsal = isset($data['KD_JABATAN_ASAL']) && $data['KD_JABATAN_ASAL'] !== '' ? (int)$data['KD_JABATAN_ASAL'] : $snapshot['KD_JABATAN'];
            $kdEkselonAsal = !empty($data['KD_EKSELON_ASAL']) ? (int)$data['KD_EKSELON_ASAL'] : $snapshot['KD_EKSELON'];
            $kdStatusAsal  = isset($data['KD_STATUS_PEGAWAI_ASAL']) && $data['KD_STATUS_PEGAWAI_ASAL'] !== '' ? (int)$data['KD_STATUS_PEGAWAI_ASAL'] : $snapshot['KD_STATUS_PEGAWAI'];
            $kdGolAsal     = !empty($data['KD_GOLONGAN_ASAL']) ? (int)$data['KD_GOLONGAN_ASAL'] : $snapshot['KD_GOLONGAN'];
            $kdPangkatAsal = !empty($data['KD_PANGKAT_ASAL']) ? (int)$data['KD_PANGKAT_ASAL'] : $snapshot['KD_PANGKAT'];
            $kdJenisAsal   = !empty($data['KD_JENIS_PEGAWAI_ASAL']) ? (int)$data['KD_JENIS_PEGAWAI_ASAL'] : $snapshot['KD_JENIS_PEGAWAI'];
            $kdLokasiAsal  = !empty($data['KD_LOKASI_ASAL']) ? (int)$data['KD_LOKASI_ASAL'] : $snapshot['KD_LOKASI'];

            // Tujuan / Baru
            $kdBagianBaru  = !empty($data['KD_BAGIAN_BARU']) ? (int)$data['KD_BAGIAN_BARU'] : (!empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $kdBagianAsal);
            $kdJabatanBaru = isset($data['KD_JABATAN_BARU']) && $data['KD_JABATAN_BARU'] !== '' ? (int)$data['KD_JABATAN_BARU'] : (isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $kdJabatanAsal);
            $kdEkselonBaru = !empty($data['KD_EKSELON_BARU']) ? (int)$data['KD_EKSELON_BARU'] : (!empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $kdEkselonAsal);
            $kdStatusBaru  = isset($data['KD_STATUS_PEGAWAI_BARU']) && $data['KD_STATUS_PEGAWAI_BARU'] !== '' ? (int)$data['KD_STATUS_PEGAWAI_BARU'] : (isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $kdStatusAsal);
            $kdGolBaru     = !empty($data['KD_GOLONGAN_BARU']) ? (int)$data['KD_GOLONGAN_BARU'] : (!empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $kdGolAsal);
            $kdPangkatBaru = !empty($data['KD_PANGKAT_BARU']) ? (int)$data['KD_PANGKAT_BARU'] : (!empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : (!empty($data['KD_PANGKAT_TUJUAN']) ? (int)$data['KD_PANGKAT_TUJUAN'] : $kdPangkatAsal));
            $kdJenisBaru   = !empty($data['KD_JENIS_PEGAWAI_BARU']) ? (int)$data['KD_JENIS_PEGAWAI_BARU'] : (!empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $kdJenisAsal);
            $kdLokasiBaru  = !empty($data['KD_LOKASI_BARU']) ? (int)$data['KD_LOKASI_BARU'] : (!empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $kdLokasiAsal);

            $nipPimpinan   = !empty($data['NIP_PIMPINAN']) ? $data['NIP_PIMPINAN'] : '-';
            $namaPimpinan  = !empty($data['NAMA_PIMPINAN']) ? $data['NAMA_PIMPINAN'] : '-';
            $keterangan    = !empty($data['KETERANGAN']) ? $data['KETERANGAN'] : '-';

            $dtDari = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $snapshot['NIP'],
                'NAMA'              => $snapshot['NAMA'],
                'KD_BAGIAN'         => $kdBagianAsal,
                'KD_JABATAN'        => $kdJabatanAsal,
                'KD_EKSELON'        => $kdEkselonAsal,
                'KD_STATUS_PEGAWAI' => $kdStatusAsal,
                'KD_GOLONGAN'       => $kdGolAsal,
                'KD_PANGKAT'        => $kdPangkatAsal,
                'KD_JENIS_PEGAWAI'  => $kdJenisAsal,
                'KD_LOKASI'         => $kdLokasiAsal,
            ];

            if (!$isUpdate) {
                $nextDari = $this->getNextId('detail_promosi_dari_pegawai', 'KD_PROMOSI_DARI');
                $dtDari['KD_PROMOSI_DARI'] = $nextDari;
                $this->db->table('detail_promosi_dari_pegawai')->insert($dtDari);
                $kdDari = $nextDari;

                $nextMenjadi = $this->getNextId('detail_promosi_menjadi_pegawai', 'KD_PROMOSI_MENJADI');
                $dtMenjadi = [
                    'KD_PROMOSI_MENJADI' => $nextMenjadi,
                    'KD_PROMOSI_DARI'    => $kdDari,
                    'NIP_PIMPINAN'       => $nipPimpinan,
                    'NAMA_PIMPINAN'      => $namaPimpinan,
                    'TGL_PROMOSI'        => $tglPromosi,
                    'KD_BAGIAN'          => $kdBagianBaru,
                    'KD_JABATAN'         => $kdJabatanBaru,
                    'KD_EKSELON'         => $kdEkselonBaru,
                    'KD_STATUS_PEGAWAI'  => $kdStatusBaru,
                    'KD_GOLONGAN'        => $kdGolBaru,
                    'KD_PANGKAT'         => $kdPangkatBaru,
                    'KD_JENIS_PEGAWAI'   => $kdJenisBaru,
                    'KD_LOKASI'          => $kdLokasiBaru,
                    'KETERANGAN'         => $keterangan,
                    'ID_PEGAWAI'         => $idPegawai,
                ];
                $this->db->table('detail_promosi_menjadi_pegawai')->insert($dtMenjadi);
            } else {
                $this->db->table('detail_promosi_dari_pegawai')->where('KD_PROMOSI_DARI', $kdDari)->update($dtDari);

                $dtMenjadi = [
                    'NIP_PIMPINAN'      => $nipPimpinan,
                    'NAMA_PIMPINAN'     => $namaPimpinan,
                    'TGL_PROMOSI'       => $tglPromosi,
                    'KD_BAGIAN'         => $kdBagianBaru,
                    'KD_JABATAN'        => $kdJabatanBaru,
                    'KD_EKSELON'        => $kdEkselonBaru,
                    'KD_STATUS_PEGAWAI' => $kdStatusBaru,
                    'KD_GOLONGAN'       => $kdGolBaru,
                    'KD_PANGKAT'        => $kdPangkatBaru,
                    'KD_JENIS_PEGAWAI'  => $kdJenisBaru,
                    'KD_LOKASI'         => $kdLokasiBaru,
                    'KETERANGAN'        => $keterangan,
                    'ID_PEGAWAI'        => $idPegawai,
                ];
                $exists = $this->db->table('detail_promosi_menjadi_pegawai')->where('KD_PROMOSI_DARI', $kdDari)->countAllResults();
                if ($exists > 0) {
                    $this->db->table('detail_promosi_menjadi_pegawai')->where('KD_PROMOSI_DARI', $kdDari)->update($dtMenjadi);
                } else {
                    $dtMenjadi['KD_PROMOSI_MENJADI'] = $this->getNextId('detail_promosi_menjadi_pegawai', 'KD_PROMOSI_MENJADI');
                    $dtMenjadi['KD_PROMOSI_DARI'] = $kdDari;
                    $this->db->table('detail_promosi_menjadi_pegawai')->insert($dtMenjadi);
                }
            }

            // Catat ke detail penempatan
            $this->savePenempatan([
                'ID_PEGAWAI'              => $idPegawai,
                'KD_BAGIAN'               => $kdBagianBaru,
                'KD_GOLONGAN'             => $kdGolBaru,
                'KD_JABATAN'              => $kdJabatanBaru,
                'KD_PANGKAT'              => $kdPangkatBaru,
                'KD_EKSELON'              => $kdEkselonBaru,
                'KD_JENIS_PEGAWAI'        => $kdJenisBaru,
                'KD_STATUS_PEGAWAI'       => $kdStatusBaru,
                'KD_LOKASI'               => $kdLokasiBaru,
                'TANGGAL_MULAI'           => $tglPromosi,
                'NO_SK_PENGANGKATAN'      => $noSurat,
                'TANGGAL_SK_PENGANGKATAN' => $tglSurat,
                'KETERANGAN'              => 'Promosi: ' . $keterangan
            ]);

            $this->db->transCommit();
            return ['code' => 200, 'message' => $isUpdate ? 'Promosi berhasil diperbarui' : 'Promosi berhasil disimpan'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deletePromosi($id)
    {
        try {
            $this->db->table('detail_promosi_menjadi_pegawai')->where('KD_PROMOSI_DARI', $id)->delete();
            $this->db->table('detail_promosi_dari_pegawai')->where('KD_PROMOSI_DARI', $id)->delete();
            return ['code' => 200, 'message' => 'Promosi berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 8. KONTRAK KERJA
    // ==========================================
    public function getKontrak($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT k.*,
                       k.TGL_KONTRAK_MULAI AS TGL_MULAI,
                       k.TGL_KONTRAK_SAMPAI AS TGL_SELESAI,
                       COALESCE(p.NAMA, k.NAMA) AS NAMA_PEGAWAI,
                       jk.JENIS_KONTRAK,
                       b.BAGIAN,
                       j.JABATAN,
                       DATEDIFF(k.TGL_KONTRAK_SAMPAI, CURDATE()) AS SISA_HARI,
                       CASE 
                           WHEN k.TGL_KONTRAK_SAMPAI < CURDATE() THEN 'EXPIRED'
                           WHEN DATEDIFF(k.TGL_KONTRAK_SAMPAI, CURDATE()) <= 60 THEN 'WARNING'
                           ELSE 'AKTIF'
                       END AS STATUS_KONTRAK
                FROM detail_kontrak_pegawai k
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = k.ID_PEGAWAI
                LEFT JOIN jenis_kontrak jk ON jk.KD_JENIS_KONTRAK = k.KD_JENIS_KONTRAK
                LEFT JOIN bagian b ON b.KD_BAGIAN = k.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = k.KD_JABATAN
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR k.NIP LIKE ? OR k.NO_SURAT LIKE ? OR b.BAGIAN LIKE ? OR jk.JENIS_KONTRAK LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY k.KD_KONTRAK DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveKontrak($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_KONTRAK'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['NO_KONTRAK']) ? trim($data['NO_KONTRAK']) : ('KTR/' . date('Ymd') . '/' . $idPegawai));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['TGL_MULAI'] ?? null, date('Y-m-d'));
            $tglMulai = $this->normalizeDate($data['TGL_KONTRAK_MULAI'] ?? $data['TGL_MULAI'] ?? null, date('Y-m-d'));
            $tglSampai = $this->normalizeDate($data['TGL_KONTRAK_SAMPAI'] ?? $data['TGL_SELESAI'] ?? null, date('Y-m-d', strtotime('+1 year')));
            $kdJenisKontrak = !empty($data['KD_JENIS_KONTRAK']) ? (int)$data['KD_JENIS_KONTRAK'] : 1;

            $record = [
                'ID_PEGAWAI'         => $idPegawai,
                'NO_SURAT'           => $noSurat,
                'TGL_SURAT'          => $tglSurat,
                'NIP'                => $snapshot['NIP'],
                'NAMA'               => $snapshot['NAMA'],
                'KD_BAGIAN'          => !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $snapshot['KD_BAGIAN'],
                'KD_JABATAN'         => isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $snapshot['KD_JABATAN'],
                'KD_EKSELON'         => !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $snapshot['KD_EKSELON'],
                'KD_STATUS_PEGAWAI'  => isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $snapshot['KD_STATUS_PEGAWAI'],
                'KD_JENIS_KONTRAK'   => $kdJenisKontrak,
                'KD_GOLONGAN'        => !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $snapshot['KD_GOLONGAN'],
                'KD_PANGKAT'         => !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : $snapshot['KD_PANGKAT'],
                'KD_JENIS_PEGAWAI'   => !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $snapshot['KD_JENIS_PEGAWAI'],
                'KD_LOKASI'          => !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $snapshot['KD_LOKASI'],
                'TGL_KONTRAK_MULAI'  => $tglMulai,
                'TGL_KONTRAK_SAMPAI' => $tglSampai,
                'KETERANGAN'         => $data['KETERANGAN'] ?? '-'
            ];

            if (empty($kd)) {
                $record['KD_KONTRAK'] = $this->getNextId('detail_kontrak_pegawai', 'KD_KONTRAK');
                $this->db->table('detail_kontrak_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Kontrak kerja berhasil disimpan'];
            } else {
                $this->db->table('detail_kontrak_pegawai')->where('KD_KONTRAK', $kd)->update($record);
                return ['code' => 200, 'message' => 'Kontrak kerja berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteKontrak($id)
    {
        try {
            $this->db->table('detail_kontrak_pegawai')->where('KD_KONTRAK', $id)->delete();
            return ['code' => 200, 'message' => 'Kontrak berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 9. PEMUTUSAN HUBUNGAN KERJA (PHK)
    // ==========================================
    public function getPHK($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT phk.*,
                       phk.ALASAN AS KETERANGAN,
                       COALESCE(p.NAMA, phk.NAMA) AS NAMA_PEGAWAI,
                       jp.JENIS_PHK,
                       b.BAGIAN,
                       j.JABATAN
                FROM detail_phk_pegawai phk
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = phk.ID_PEGAWAI
                LEFT JOIN jenis_phk jp ON jp.KD_JENIS_PHK = phk.KD_JENIS_PHK
                LEFT JOIN bagian b ON b.KD_BAGIAN = phk.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = phk.KD_JABATAN
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR phk.NIP LIKE ? OR phk.NO_SURAT LIKE ? OR jp.JENIS_PHK LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY phk.KD_PHK DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function savePHK($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_PHK'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-PHK/' . date('Ymd') . '/' . $idPegawai));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['TGL_PHK'] ?? null, date('Y-m-d'));
            $tglPhk = $this->normalizeDate($data['TGL_PHK'] ?? null, date('Y-m-d'));
            $kdJenisPhk = !empty($data['KD_JENIS_PHK']) ? (int)$data['KD_JENIS_PHK'] : 1;
            $alasan = !empty($data['ALASAN']) ? $data['ALASAN'] : (!empty($data['KETERANGAN']) ? $data['KETERANGAN'] : '-');

            $record = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $snapshot['NIP'],
                'NAMA'              => $snapshot['NAMA'],
                'KD_BAGIAN'         => !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $snapshot['KD_BAGIAN'],
                'KD_JABATAN'        => isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $snapshot['KD_JABATAN'],
                'KD_EKSELON'        => !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $snapshot['KD_EKSELON'],
                'KD_STATUS_PEGAWAI' => isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $snapshot['KD_STATUS_PEGAWAI'],
                'KD_JENIS_PHK'      => $kdJenisPhk,
                'KD_GOLONGAN'       => !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $snapshot['KD_GOLONGAN'],
                'KD_PANGKAT'        => !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : $snapshot['KD_PANGKAT'],
                'KD_JENIS_PEGAWAI'  => !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $snapshot['KD_JENIS_PEGAWAI'],
                'KD_LOKASI'         => !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $snapshot['KD_LOKASI'],
                'TGL_PHK'           => $tglPhk,
                'ALASAN'            => $alasan
            ];

            if (empty($kd)) {
                $record['KD_PHK'] = $this->getNextId('detail_phk_pegawai', 'KD_PHK');
                $this->db->table('detail_phk_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Data PHK berhasil disimpan'];
            } else {
                $this->db->table('detail_phk_pegawai')->where('KD_PHK', $kd)->update($record);
                return ['code' => 200, 'message' => 'Data PHK berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deletePHK($id)
    {
        try {
            $this->db->table('detail_phk_pegawai')->where('KD_PHK', $id)->delete();
            return ['code' => 200, 'message' => 'Data PHK berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 10. PENGHARGAAN
    // ==========================================
    public function getPenghargaan($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT ph.*,
                       COALESCE(pg.NAMA, ph.NAMA) AS NAMA_PEGAWAI,
                       jp.JENIS_PENGHARGAAN,
                       b.BAGIAN,
                       j.JABATAN
                FROM detail_penghargaan_pegawai ph
                LEFT JOIN pegawai pg ON pg.ID_PEGAWAI = ph.ID_PEGAWAI
                LEFT JOIN jenis_penghargaan jp ON jp.KD_JENIS_PENGHARGAAN = ph.KD_JENIS_PENGHARGAAN
                LEFT JOIN bagian b ON b.KD_BAGIAN = ph.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = ph.KD_JABATAN
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (pg.NAMA LIKE ? OR ph.NIP LIKE ? OR ph.NO_SURAT LIKE ? OR jp.JENIS_PENGHARGAAN LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY ph.KD_PENGHARGAAN DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function savePenghargaan($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_PENGHARGAAN'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-PGR/' . date('Ymd') . '/' . $idPegawai));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['TGL_PENGHARGAAN'] ?? null, date('Y-m-d'));
            $tglPenghargaan = $this->normalizeDate($data['TGL_PENGHARGAAN'] ?? null, date('Y-m-d'));
            $kdJenis = !empty($data['KD_JENIS_PENGHARGAAN']) ? (int)$data['KD_JENIS_PENGHARGAAN'] : 1;

            $record = [
                'ID_PEGAWAI'           => $idPegawai,
                'NO_SURAT'             => $noSurat,
                'TGL_SURAT'            => $tglSurat,
                'NIP'                  => $snapshot['NIP'],
                'NAMA'                 => $snapshot['NAMA'],
                'KD_BAGIAN'            => !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $snapshot['KD_BAGIAN'],
                'KD_JABATAN'           => isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $snapshot['KD_JABATAN'],
                'KD_EKSELON'           => !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $snapshot['KD_EKSELON'],
                'KD_STATUS_PEGAWAI'    => isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $snapshot['KD_STATUS_PEGAWAI'],
                'KD_JENIS_PENGHARGAAN' => $kdJenis,
                'KD_GOLONGAN'          => !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $snapshot['KD_GOLONGAN'],
                'KD_PANGKAT'           => !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : $snapshot['KD_PANGKAT'],
                'KD_JENIS_PEGAWAI'     => !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $snapshot['KD_JENIS_PEGAWAI'],
                'KD_LOKASI'            => !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $snapshot['KD_LOKASI'],
                'TGL_PENGHARGAAN'      => $tglPenghargaan,
                'KETERANGAN'           => $data['KETERANGAN'] ?? '-'
            ];

            if (empty($kd)) {
                $record['KD_PENGHARGAAN'] = $this->getNextId('detail_penghargaan_pegawai', 'KD_PENGHARGAAN');
                $this->db->table('detail_penghargaan_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Data penghargaan berhasil disimpan'];
            } else {
                $this->db->table('detail_penghargaan_pegawai')->where('KD_PENGHARGAAN', $kd)->update($record);
                return ['code' => 200, 'message' => 'Data penghargaan berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deletePenghargaan($id)
    {
        try {
            $this->db->table('detail_penghargaan_pegawai')->where('KD_PENGHARGAAN', $id)->delete();
            return ['code' => 200, 'message' => 'Penghargaan berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 11. SANKSI & HUKUMAN
    // ==========================================
    public function getSanksi($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT s.*,
                       s.MULAI AS TGL_MULAI,
                       s.SAMPAI AS TGL_SELESAI,
                       COALESCE(pg.NAMA, s.NAMA) AS NAMA_PEGAWAI,
                       js.JENIS_SANKSI,
                       b.BAGIAN,
                       j.JABATAN
                FROM detail_sanksi_pegawai s
                LEFT JOIN pegawai pg ON pg.ID_PEGAWAI = s.ID_PEGAWAI
                LEFT JOIN jenis_sanksi js ON js.KD_JENIS_SANKSI = s.KD_JENIS_SANKSI
                LEFT JOIN bagian b ON b.KD_BAGIAN = s.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = s.KD_JABATAN
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (pg.NAMA LIKE ? OR s.NIP LIKE ? OR s.NO_SURAT LIKE ? OR js.JENIS_SANKSI LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY s.KD_SANKSI DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveSanksi($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_SANKSI'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-SNK/' . date('Ymd') . '/' . $idPegawai));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['TGL_SANKSI'] ?? null, date('Y-m-d'));
            $tglSanksi = $this->normalizeDate($data['TGL_SANKSI'] ?? null, date('Y-m-d'));
            $mulai = $this->normalizeDate($data['MULAI'] ?? $data['TGL_MULAI'] ?? $data['TGL_SANKSI'] ?? null, date('Y-m-d'));
            $sampai = $this->normalizeDate($data['SAMPAI'] ?? $data['TGL_SELESAI'] ?? $data['TGL_SAMPAI'] ?? null, date('Y-m-d', strtotime('+1 month')));
            $kdJenis = !empty($data['KD_JENIS_SANKSI']) ? (int)$data['KD_JENIS_SANKSI'] : 1;

            $record = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $snapshot['NIP'],
                'NAMA'              => $snapshot['NAMA'],
                'KD_BAGIAN'         => !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $snapshot['KD_BAGIAN'],
                'KD_JABATAN'        => isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $snapshot['KD_JABATAN'],
                'KD_EKSELON'        => !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $snapshot['KD_EKSELON'],
                'KD_STATUS_PEGAWAI' => isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $snapshot['KD_STATUS_PEGAWAI'],
                'KD_JENIS_SANKSI'   => $kdJenis,
                'KD_GOLONGAN'       => !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $snapshot['KD_GOLONGAN'],
                'KD_PANGKAT'        => !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : $snapshot['KD_PANGKAT'],
                'KD_JENIS_PEGAWAI'  => !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $snapshot['KD_JENIS_PEGAWAI'],
                'KD_LOKASI'         => !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $snapshot['KD_LOKASI'],
                'TGL_SANKSI'        => $tglSanksi,
                'MULAI'             => $mulai,
                'SAMPAI'            => $sampai,
                'KETERANGAN'        => $data['KETERANGAN'] ?? '-'
            ];

            if (isset($data['FILE']) && $data['FILE'] !== '') {
                $record['FILE'] = $data['FILE'];
            }

            if (empty($kd)) {
                $record['KD_SANKSI'] = $this->getNextId('detail_sanksi_pegawai', 'KD_SANKSI');
                $this->db->table('detail_sanksi_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Data sanksi berhasil disimpan'];
            } else {
                $this->db->table('detail_sanksi_pegawai')->where('KD_SANKSI', $kd)->update($record);
                return ['code' => 200, 'message' => 'Data sanksi berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteSanksi($id)
    {
        try {
            $this->db->table('detail_sanksi_pegawai')->where('KD_SANKSI', $id)->delete();
            return ['code' => 200, 'message' => 'Sanksi berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    // ==========================================
    // 12. CUTI PEGAWAI
    // ==========================================
    public function getCuti($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT c.*,
                       c.NOMOR_SURAT AS NO_SURAT,
                       c.TANGGAL_MULAI AS TGL_CUTI_MULAI,
                       c.TANGGAL_SAMPAI AS TGL_CUTI_SAMPAI,
                       c.KEPERLUAN AS KETERANGAN,
                       COALESCE(p.NAMA, c.NAMA) AS NAMA_PEGAWAI,
                       jc.JENIS_CUTI,
                       b.BAGIAN
                FROM detail_cuti_pegawai c
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = c.ID_PEGAWAI
                LEFT JOIN jenis_cuti jc ON jc.KD_JENIS_CUTI = c.KD_JENIS_CUTI
                LEFT JOIN bagian b ON b.KD_BAGIAN = c.KD_BAGIAN
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR c.NIP LIKE ? OR jc.JENIS_CUTI LIKE ? OR c.NOMOR_SURAT LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY c.KD_CUTI DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveCuti($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_CUTI'] ?? '';

            $noSurat = !empty($data['NOMOR_SURAT']) ? trim($data['NOMOR_SURAT']) : (!empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : ('CUTI/' . date('Ymd') . '/' . $idPegawai));
            $tglMulai = $this->normalizeDate($data['TANGGAL_MULAI'] ?? $data['TGL_CUTI_MULAI'] ?? null, date('Y-m-d'));
            $tglSampai = $this->normalizeDate($data['TANGGAL_SAMPAI'] ?? $data['TGL_CUTI_SAMPAI'] ?? null, date('Y-m-d'));
            $kdJenisCuti = !empty($data['KD_JENIS_CUTI']) ? (int)$data['KD_JENIS_CUTI'] : 1;
            $keperluan = !empty($data['KEPERLUAN']) ? $data['KEPERLUAN'] : (!empty($data['KETERANGAN']) ? $data['KETERANGAN'] : '-');

            $record = [
                'ID_PEGAWAI'        => $idPegawai,
                'NIP'               => $snapshot['NIP'],
                'NAMA'              => $snapshot['NAMA'],
                'KD_BAGIAN'         => !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $snapshot['KD_BAGIAN'],
                'KD_JENIS_CUTI'     => $kdJenisCuti,
                'TANGGAL_MULAI'     => $tglMulai,
                'TANGGAL_SAMPAI'    => $tglSampai,
                'NOMOR_SURAT'       => $noSurat,
                'KEPERLUAN'         => $keperluan,
                'PEGAWAI_PENGGANTI' => $data['PEGAWAI_PENGGANTI'] ?? $data['PENGGANTI'] ?? null,
                'STATUS'            => 1,
                'TANGGAL_INPUT'     => date('Y-m-d'),
            ];

            if (empty($kd)) {
                $record['KD_CUTI'] = $this->getNextId('detail_cuti_pegawai', 'KD_CUTI');
                $this->db->table('detail_cuti_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Pengajuan cuti berhasil disimpan'];
            } else {
                $this->db->table('detail_cuti_pegawai')->where('KD_CUTI', $kd)->update($record);
                return ['code' => 200, 'message' => 'Data cuti berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteCuti($id)
    {
        try {
            $this->db->table('detail_cuti_pegawai')->where('KD_CUTI', $id)->delete();
            return ['code' => 200, 'message' => 'Cuti berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function getDemosi($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT dd.*,
                       dm.KD_DEMOSI_MENJADI,
                       dm.TGL_DEMOSI,
                       dm.KD_BAGIAN AS KD_BAGIAN_BARU,
                       dm.KD_JABATAN AS KD_JABATAN_BARU,
                       dm.KD_PANGKAT AS KD_PANGKAT_BARU,
                       dm.KD_GOLONGAN AS KD_GOLONGAN_BARU,
                       dm.NIP_PIMPINAN,
                       dm.NAMA_PIMPINAN,
                       dm.KETERANGAN,
                       COALESCE(p.NAMA, dd.NAMA) AS NAMA_PEGAWAI,
                       b_asal.BAGIAN AS BAGIAN_ASAL,
                       b_baru.BAGIAN AS BAGIAN_BARU,
                       j_asal.JABATAN AS JABATAN_ASAL,
                       j_baru.JABATAN AS JABATAN_BARU,
                       pkt_asal.PANGKAT AS PANGKAT_ASAL,
                       pkt_baru.PANGKAT AS PANGKAT_BARU
                FROM detail_demosi_dari_pegawai dd
                LEFT JOIN detail_demosi_menjadi_pegawai dm ON dm.KD_DEMOSI_DARI = dd.KD_DEMOSI_DARI
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = dd.ID_PEGAWAI
                LEFT JOIN bagian b_asal ON b_asal.KD_BAGIAN = dd.KD_BAGIAN
                LEFT JOIN bagian b_baru ON b_baru.KD_BAGIAN = dm.KD_BAGIAN
                LEFT JOIN jabatan j_asal ON j_asal.KD_JABATAN = dd.KD_JABATAN
                LEFT JOIN jabatan j_baru ON j_baru.KD_JABATAN = dm.KD_JABATAN
                LEFT JOIN pangkat pkt_asal ON pkt_asal.KD_PANGKAT = dd.KD_PANGKAT
                LEFT JOIN pangkat pkt_baru ON pkt_baru.KD_PANGKAT = dm.KD_PANGKAT
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR dd.NIP LIKE ? OR dd.NO_SURAT LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY dd.KD_DEMOSI_DARI DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveDemosi($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kdDari = $data['KD_DEMOSI_DARI'] ?? '';
            $isUpdate = !empty($kdDari);

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-DMS/' . date('Ymd') . '/' . $idPegawai));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['TGL_DEMOSI'] ?? null, date('Y-m-d'));
            $tglDemosi = $this->normalizeDate($data['TGL_DEMOSI'] ?? null, date('Y-m-d'));

            $kdBagianAsal  = !empty($data['KD_BAGIAN_ASAL']) ? (int)$data['KD_BAGIAN_ASAL'] : $snapshot['KD_BAGIAN'];
            $kdJabatanAsal = isset($data['KD_JABATAN_ASAL']) && $data['KD_JABATAN_ASAL'] !== '' ? (int)$data['KD_JABATAN_ASAL'] : $snapshot['KD_JABATAN'];
            $kdEkselonAsal = !empty($data['KD_EKSELON_ASAL']) ? (int)$data['KD_EKSELON_ASAL'] : $snapshot['KD_EKSELON'];
            $kdStatusAsal  = isset($data['KD_STATUS_PEGAWAI_ASAL']) && $data['KD_STATUS_PEGAWAI_ASAL'] !== '' ? (int)$data['KD_STATUS_PEGAWAI_ASAL'] : $snapshot['KD_STATUS_PEGAWAI'];
            $kdGolAsal     = !empty($data['KD_GOLONGAN_ASAL']) ? (int)$data['KD_GOLONGAN_ASAL'] : $snapshot['KD_GOLONGAN'];
            $kdPangkatAsal = !empty($data['KD_PANGKAT_ASAL']) ? (int)$data['KD_PANGKAT_ASAL'] : $snapshot['KD_PANGKAT'];
            $kdJenisAsal   = !empty($data['KD_JENIS_PEGAWAI_ASAL']) ? (int)$data['KD_JENIS_PEGAWAI_ASAL'] : $snapshot['KD_JENIS_PEGAWAI'];
            $kdLokasiAsal  = !empty($data['KD_LOKASI_ASAL']) ? (int)$data['KD_LOKASI_ASAL'] : $snapshot['KD_LOKASI'];

            $kdBagianBaru  = !empty($data['KD_BAGIAN_BARU']) ? (int)$data['KD_BAGIAN_BARU'] : (!empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $kdBagianAsal);
            $kdJabatanBaru = isset($data['KD_JABATAN_BARU']) && $data['KD_JABATAN_BARU'] !== '' ? (int)$data['KD_JABATAN_BARU'] : (isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $kdJabatanAsal);
            $kdEkselonBaru = !empty($data['KD_EKSELON_BARU']) ? (int)$data['KD_EKSELON_BARU'] : (!empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $kdEkselonAsal);
            $kdStatusBaru  = isset($data['KD_STATUS_PEGAWAI_BARU']) && $data['KD_STATUS_PEGAWAI_BARU'] !== '' ? (int)$data['KD_STATUS_PEGAWAI_BARU'] : (isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $kdStatusAsal);
            $kdGolBaru     = !empty($data['KD_GOLONGAN_BARU']) ? (int)$data['KD_GOLONGAN_BARU'] : (!empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $kdGolAsal);
            $kdPangkatBaru = !empty($data['KD_PANGKAT_BARU']) ? (int)$data['KD_PANGKAT_BARU'] : (!empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : $kdPangkatAsal);
            $kdJenisBaru   = !empty($data['KD_JENIS_PEGAWAI_BARU']) ? (int)$data['KD_JENIS_PEGAWAI_BARU'] : (!empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $kdJenisAsal);
            $kdLokasiBaru  = !empty($data['KD_LOKASI_BARU']) ? (int)$data['KD_LOKASI_BARU'] : (!empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $kdLokasiAsal);

            $nipPimpinan   = $data['NIP_PIMPINAN'] ?? '';
            $namaPimpinan  = $data['NAMA_PIMPINAN'] ?? '';
            $keterangan    = !empty($data['KETERANGAN']) ? $data['KETERANGAN'] : '-';

            $dtDari = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $snapshot['NIP'],
                'NAMA'              => $snapshot['NAMA'],
                'KD_BAGIAN'         => $kdBagianAsal,
                'KD_JABATAN'        => $kdJabatanAsal,
                'KD_EKSELON'        => $kdEkselonAsal,
                'KD_STATUS_PEGAWAI' => $kdStatusAsal,
                'KD_GOLONGAN'       => $kdGolAsal,
                'KD_PANGKAT'        => $kdPangkatAsal,
                'KD_JENIS_PEGAWAI'  => $kdJenisAsal,
                'KD_LOKASI'         => $kdLokasiAsal,
            ];

            if (!$isUpdate) {
                $nextDari = $this->getNextId('detail_demosi_dari_pegawai', 'KD_DEMOSI_DARI');
                $dtDari['KD_DEMOSI_DARI'] = $nextDari;
                $this->db->table('detail_demosi_dari_pegawai')->insert($dtDari);
                $kdDari = $nextDari;

                $nextMenjadi = $this->getNextId('detail_demosi_menjadi_pegawai', 'KD_DEMOSI_MENJADI');
                $dtMenjadi = [
                    'KD_DEMOSI_MENJADI' => $nextMenjadi,
                    'KD_DEMOSI_DARI'    => $kdDari,
                    'NIP_PIMPINAN'       => $nipPimpinan,
                    'NAMA_PIMPINAN'      => $namaPimpinan,
                    'TGL_DEMOSI'        => $tglDemosi,
                    'KD_BAGIAN'          => $kdBagianBaru,
                    'KD_JABATAN'         => $kdJabatanBaru,
                    'KD_EKSELON'         => $kdEkselonBaru,
                    'KD_STATUS_PEGAWAI'  => $kdStatusBaru,
                    'KD_GOLONGAN'        => $kdGolBaru,
                    'KD_PANGKAT'         => $kdPangkatBaru,
                    'KD_JENIS_PEGAWAI'   => $kdJenisBaru,
                    'KD_LOKASI'          => $kdLokasiBaru,
                    'KETERANGAN'         => $keterangan,
                    'ID_PEGAWAI'         => $idPegawai,
                ];
                $this->db->table('detail_demosi_menjadi_pegawai')->insert($dtMenjadi);
            } else {
                $this->db->table('detail_demosi_dari_pegawai')->where('KD_DEMOSI_DARI', $kdDari)->update($dtDari);

                $dtMenjadi = [
                    'NIP_PIMPINAN'      => $nipPimpinan,
                    'NAMA_PIMPINAN'     => $namaPimpinan,
                    'TGL_DEMOSI'        => $tglDemosi,
                    'KD_BAGIAN'         => $kdBagianBaru,
                    'KD_JABATAN'        => $kdJabatanBaru,
                    'KD_EKSELON'        => $kdEkselonBaru,
                    'KD_STATUS_PEGAWAI' => $kdStatusBaru,
                    'KD_GOLONGAN'       => $kdGolBaru,
                    'KD_PANGKAT'        => $kdPangkatBaru,
                    'KD_JENIS_PEGAWAI'  => $kdJenisBaru,
                    'KD_LOKASI'         => $kdLokasiBaru,
                    'KETERANGAN'        => $keterangan,
                    'ID_PEGAWAI'        => $idPegawai,
                ];
                $this->db->table('detail_demosi_menjadi_pegawai')->where('KD_DEMOSI_DARI', $kdDari)->update($dtMenjadi);
            }

            if (!empty($data['SYNC_PEGAWAI'])) {
                $this->db->table('pegawai')->where('ID_PEGAWAI', $idPegawai)->update([
                    'KD_BAGIAN'         => $kdBagianBaru,
                    'KD_JABATAN'        => $kdJabatanBaru,
                    'KD_EKSELON'        => $kdEkselonBaru,
                    'KD_STATUS_PEGAWAI' => $kdStatusBaru,
                    'KD_GOLONGAN'       => $kdGolBaru,
                    'KD_PANGKAT'        => $kdPangkatBaru,
                    'KD_JENIS_PEGAWAI'  => $kdJenisBaru,
                    'KD_LOKASI'         => $kdLokasiBaru,
                ]);
            }

            return ['code' => 200, 'message' => 'Data demosi berhasil disimpan'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteDemosi($id)
    {
        try {
            $this->db->table('detail_demosi_menjadi_pegawai')->where('KD_DEMOSI_DARI', $id)->delete();
            $this->db->table('detail_demosi_dari_pegawai')->where('KD_DEMOSI_DARI', $id)->delete();
            return ['code' => 200, 'message' => 'Demosi berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function getCatatan($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT c.*,
                       COALESCE(p.NAMA, c.NAMA) AS NAMA_PEGAWAI,
                       jc.JENIS_CATATAN,
                       b.BAGIAN,
                       j.JABATAN
                FROM detail_catatan_pegawai c
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = c.ID_PEGAWAI
                LEFT JOIN jenis_catatan jc ON jc.KD_JENIS_CATATAN = c.KD_JENIS_CATATAN
                LEFT JOIN bagian b ON b.KD_BAGIAN = c.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = c.KD_JABATAN
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR c.NIP LIKE ? OR c.NO_SURAT LIKE ? OR jc.JENIS_CATATAN LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY c.KD_CATATAN DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function saveCatatan($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_CATATAN'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-CTT/' . date('Ymd') . '/' . $idPegawai));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['TGL_CATATAN'] ?? null, date('Y-m-d'));
            $tglCatatan = $this->normalizeDate($data['TGL_CATATAN'] ?? null, date('Y-m-d'));
            $kdJenis = !empty($data['KD_JENIS_CATATAN']) ? (int)$data['KD_JENIS_CATATAN'] : 1;

            $record = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $snapshot['NIP'],
                'NAMA'              => $snapshot['NAMA'],
                'KD_BAGIAN'         => !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : $snapshot['KD_BAGIAN'],
                'KD_JABATAN'        => isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : $snapshot['KD_JABATAN'],
                'KD_EKSELON'        => !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : $snapshot['KD_EKSELON'],
                'KD_STATUS_PEGAWAI' => isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : $snapshot['KD_STATUS_PEGAWAI'],
                'KD_JENIS_CATATAN'  => $kdJenis,
                'KD_GOLONGAN'       => !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : $snapshot['KD_GOLONGAN'],
                'KD_PANGKAT'        => !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : $snapshot['KD_PANGKAT'],
                'KD_JENIS_PEGAWAI'  => !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : $snapshot['KD_JENIS_PEGAWAI'],
                'KD_LOKASI'         => !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : $snapshot['KD_LOKASI'],
                'TGL_CATATAN'       => $tglCatatan,
                'KETERANGAN'        => $data['KETERANGAN'] ?? '-',
                'FILE'              => $data['FILE'] ?? ''
            ];

            if (empty($kd)) {
                $record['KD_CATATAN'] = $this->getNextId('detail_catatan_pegawai', 'KD_CATATAN');
                $this->db->table('detail_catatan_pegawai')->insert($record);
                return ['code' => 200, 'message' => 'Data catatan berhasil disimpan'];
            } else {
                $this->db->table('detail_catatan_pegawai')->where('KD_CATATAN', $kd)->update($record);
                return ['code' => 200, 'message' => 'Data catatan berhasil diperbarui'];
            }
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function deleteCatatan($id)
    {
        try {
            $this->db->table('detail_catatan_pegawai')->where('KD_CATATAN', $id)->delete();
            return ['code' => 200, 'message' => 'Catatan berhasil dihapus'];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }
}
