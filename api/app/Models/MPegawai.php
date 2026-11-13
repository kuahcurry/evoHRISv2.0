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

        if (!$pegawai && !empty($idPegawai)) {
            $pegawai = $this->db->table('pegawai')
                ->select('ID_PEGAWAI, NAMA, NIP')
                ->where('NIP', (string)$idPegawai)
                ->get()->getRowArray();
            if ($pegawai) {
                $idPegawai = $pegawai['ID_PEGAWAI'];
            }
        }

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
            'ekselon' => $this->db->query("SELECT KD_EKSELON, EKSELON FROM ekselon ORDER BY KD_EKSELON ASC")->getResultArray(),
            'status_pegawai' => $this->db->query("SELECT KD_STATUS_PEGAWAI, STATUS_PEGAWAI FROM status_pegawai ORDER BY STATUS_PEGAWAI ASC")->getResultArray(),
            'jenis_pegawai' => $this->db->query("SELECT KD_JENIS_PEGAWAI, JENIS_PEGAWAI FROM jenis_pegawai ORDER BY JENIS_PEGAWAI ASC")->getResultArray(),
            'lokasi' => $this->db->query("SELECT KD_LOKASI, LOKASI FROM lokasi ORDER BY LOKASI ASC")->getResultArray(),
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
                       mm.KD_PANGKAT AS KD_PANGKAT_BARU,
                       mm.KD_GOLONGAN AS KD_GOLONGAN_BARU,
                       mm.KD_EKSELON AS KD_EKSELON_BARU,
                       mm.KD_STATUS_PEGAWAI AS KD_STATUS_PEGAWAI_BARU,
                       mm.KD_JENIS_PEGAWAI AS KD_JENIS_PEGAWAI_BARU,
                       mm.KD_LOKASI AS KD_LOKASI_BARU,
                       mm.NIP_PIMPINAN,
                       mm.NAMA_PIMPINAN,
                       mm.KETERANGAN,
                       p.NAMA AS NAMA_PEGAWAI,
                       b_asal.BAGIAN AS BAGIAN_ASAL,
                       b_baru.BAGIAN AS BAGIAN_BARU,
                       j_asal.JABATAN AS JABATAN_ASAL,
                       j_baru.JABATAN AS JABATAN_BARU,
                       pkt_asal.PANGKAT AS PANGKAT_ASAL,
                       pkt_baru.PANGKAT AS PANGKAT_BARU,
                       g_asal.GOLONGAN AS GOLONGAN_ASAL,
                       g_baru.GOLONGAN AS GOLONGAN_BARU,
                       e_asal.EKSELON AS EKSELON_ASAL,
                       e_baru.EKSELON AS EKSELON_BARU,
                       jp_asal.JENIS_PEGAWAI AS JENIS_PEGAWAI_ASAL,
                       jp_baru.JENIS_PEGAWAI AS JENIS_PEGAWAI_BARU,
                       sp_asal.STATUS_PEGAWAI AS STATUS_PEGAWAI_ASAL,
                       sp_baru.STATUS_PEGAWAI AS STATUS_PEGAWAI_BARU,
                       l_asal.LOKASI AS LOKASI_ASAL,
                       l_baru.LOKASI AS LOKASI_BARU
                FROM detail_mutasi_dari_pegawai md
                LEFT JOIN detail_mutasi_menjadi_pegawai mm ON mm.KD_MUTASI_DARI = md.KD_MUTASI_DARI
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = md.ID_PEGAWAI
                LEFT JOIN bagian b_asal ON b_asal.KD_BAGIAN = md.KD_BAGIAN
                LEFT JOIN bagian b_baru ON b_baru.KD_BAGIAN = mm.KD_BAGIAN
                LEFT JOIN jabatan j_asal ON j_asal.KD_JABATAN = md.KD_JABATAN
                LEFT JOIN jabatan j_baru ON j_baru.KD_JABATAN = mm.KD_JABATAN
                LEFT JOIN pangkat pkt_asal ON pkt_asal.KD_PANGKAT = md.KD_PANGKAT
                LEFT JOIN pangkat pkt_baru ON pkt_baru.KD_PANGKAT = mm.KD_PANGKAT
                LEFT JOIN golongan g_asal ON g_asal.KD_GOLONGAN = md.KD_GOLONGAN
                LEFT JOIN golongan g_baru ON g_baru.KD_GOLONGAN = mm.KD_GOLONGAN
                LEFT JOIN ekselon e_asal ON e_asal.KD_EKSELON = md.KD_EKSELON
                LEFT JOIN ekselon e_baru ON e_baru.KD_EKSELON = mm.KD_EKSELON
                LEFT JOIN jenis_pegawai jp_asal ON jp_asal.KD_JENIS_PEGAWAI = md.KD_JENIS_PEGAWAI
                LEFT JOIN jenis_pegawai jp_baru ON jp_baru.KD_JENIS_PEGAWAI = mm.KD_JENIS_PEGAWAI
                LEFT JOIN status_pegawai sp_asal ON sp_asal.KD_STATUS_PEGAWAI = md.KD_STATUS_PEGAWAI
                LEFT JOIN status_pegawai sp_baru ON sp_baru.KD_STATUS_PEGAWAI = mm.KD_STATUS_PEGAWAI
                LEFT JOIN lokasi l_asal ON l_asal.KD_LOKASI = md.KD_LOKASI
                LEFT JOIN lokasi l_baru ON l_baru.KD_LOKASI = mm.KD_LOKASI
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
            $aliases = [
                'ID_PEGAWAI'             => ['replacement_id_pegawai', 'id_pegawai'],
                'NIP'                    => ['replacement_NIP', 'nip'],
                'NAMA'                   => ['replacement_nama', 'nama'],
                'KD_MUTASI_DARI'         => ['replacement_kd_mutasi_dari', 'kd_mutasi_dari'],
                'NO_SURAT'               => ['replacement_no_surat', 'no_surat'],
                'TGL_SURAT'              => ['replacement_tanggal_surat', 'tgl_surat'],
                'KD_BAGIAN_ASAL'         => ['replacement_bagian', 'kd_bagian_asal', 'kd_bagian'],
                'KD_JABATAN_ASAL'        => ['replacement_jabatan', 'kd_jabatan_asal', 'kd_jabatan'],
                'KD_EKSELON_ASAL'        => ['replacement_ekselon', 'kd_ekselon_asal', 'kd_ekselon'],
                'KD_STATUS_PEGAWAI_ASAL' => ['replacement_status_pegawai', 'kd_status_pegawai_asal', 'kd_status_pegawai'],
                'KD_GOLONGAN_ASAL'       => ['replacement_golongan', 'kd_golongan_asal', 'kd_golongan'],
                'KD_PANGKAT_ASAL'        => ['replacement_pangkat', 'kd_pangkat_asal', 'kd_pangkat'],
                'KD_JENIS_PEGAWAI_ASAL'  => ['replacement_jenis_pegawai', 'kd_jenis_pegawai_asal', 'kd_jenis_pegawai'],
                'KD_LOKASI_ASAL'         => ['replacement_lokasi', 'kd_lokasi_asal', 'kd_lokasi'],
                'NIP_PIMPINAN'           => ['replacement_NIP_pimpinan', 'nip_pimpinan'],
                'NAMA_PIMPINAN'          => ['replacement_nama_pimpinan', 'nama_pimpinan'],
                'TGL_MUTASI'             => ['replacement_tanggal_mutasi', 'tgl_mutasi'],
                'KD_BAGIAN_BARU'         => ['replacement_bagian_menjadi', 'kd_bagian_baru', 'kd_bagian_tujuan'],
                'KD_JABATAN_BARU'        => ['replacement_jabatan_menjadi', 'kd_jabatan_baru', 'kd_jabatan_tujuan'],
                'KD_EKSELON_BARU'        => ['replacement_ekselon_menjadi', 'kd_ekselon_baru'],
                'KD_STATUS_PEGAWAI_BARU' => ['replacement_status_pegawai_menjadi', 'kd_status_pegawai_baru'],
                'KD_GOLONGAN_BARU'       => ['replacement_golongan_menjadi', 'kd_golongan_baru'],
                'KD_PANGKAT_BARU'        => ['replacement_pangkat_menjadi', 'kd_pangkat_baru'],
                'KD_JENIS_PEGAWAI_BARU'  => ['replacement_jenis_pegawai_menjadi', 'kd_jenis_pegawai_baru'],
                'KD_LOKASI_BARU'         => ['replacement_lokasi_menjadi', 'kd_lokasi_baru'],
                'KETERANGAN'             => ['replacement_keterangan', 'keterangan'],
            ];
            foreach ($aliases as $target => $sources) {
                if (!isset($data[$target]) || $data[$target] === '') {
                    foreach ($sources as $s) {
                        if (isset($data[$s]) && $data[$s] !== '') {
                            $data[$target] = $data[$s];
                            break;
                        }
                    }
                }
            }

            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai) && !empty($data['NIP'])) {
                $pRow = $this->db->table('pegawai')->where('NIP', trim($data['NIP']))->get()->getRowArray();
                if ($pRow) {
                    $idPegawai = $pRow['ID_PEGAWAI'];
                }
            }
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
                       pm.KD_EKSELON AS KD_EKSELON_BARU,
                       pm.KD_STATUS_PEGAWAI AS KD_STATUS_PEGAWAI_BARU,
                       pm.KD_JENIS_PEGAWAI AS KD_JENIS_PEGAWAI_BARU,
                       pm.KD_LOKASI AS KD_LOKASI_BARU,
                       pm.NIP_PIMPINAN,
                       pm.NAMA_PIMPINAN,
                       pm.KETERANGAN,
                       p.NAMA AS NAMA_PEGAWAI,
                       b_asal.BAGIAN AS BAGIAN_ASAL,
                       b_baru.BAGIAN AS BAGIAN_BARU,
                       j_asal.JABATAN AS JABATAN_ASAL,
                       j_baru.JABATAN AS JABATAN_BARU,
                       pkt_asal.PANGKAT AS PANGKAT_ASAL,
                       pkt_baru.PANGKAT AS PANGKAT_BARU,
                       g_asal.GOLONGAN AS GOLONGAN_ASAL,
                       g_baru.GOLONGAN AS GOLONGAN_BARU,
                       e_asal.EKSELON AS EKSELON_ASAL,
                       e_baru.EKSELON AS EKSELON_BARU,
                       jp_asal.JENIS_PEGAWAI AS JENIS_PEGAWAI_ASAL,
                       jp_baru.JENIS_PEGAWAI AS JENIS_PEGAWAI_BARU,
                       sp_asal.STATUS_PEGAWAI AS STATUS_PEGAWAI_ASAL,
                       sp_baru.STATUS_PEGAWAI AS STATUS_PEGAWAI_BARU,
                       l_asal.LOKASI AS LOKASI_ASAL,
                       l_baru.LOKASI AS LOKASI_BARU
                FROM detail_promosi_dari_pegawai pd
                LEFT JOIN detail_promosi_menjadi_pegawai pm ON pm.KD_PROMOSI_DARI = pd.KD_PROMOSI_DARI
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = pd.ID_PEGAWAI
                LEFT JOIN bagian b_asal ON b_asal.KD_BAGIAN = pd.KD_BAGIAN
                LEFT JOIN bagian b_baru ON b_baru.KD_BAGIAN = pm.KD_BAGIAN
                LEFT JOIN jabatan j_asal ON j_asal.KD_JABATAN = pd.KD_JABATAN
                LEFT JOIN jabatan j_baru ON j_baru.KD_JABATAN = pm.KD_JABATAN
                LEFT JOIN pangkat pkt_asal ON pkt_asal.KD_PANGKAT = pd.KD_PANGKAT
                LEFT JOIN pangkat pkt_baru ON pkt_baru.KD_PANGKAT = pm.KD_PANGKAT
                LEFT JOIN golongan g_asal ON g_asal.KD_GOLONGAN = pd.KD_GOLONGAN
                LEFT JOIN golongan g_baru ON g_baru.KD_GOLONGAN = pm.KD_GOLONGAN
                LEFT JOIN ekselon e_asal ON e_asal.KD_EKSELON = pd.KD_EKSELON
                LEFT JOIN ekselon e_baru ON e_baru.KD_EKSELON = pm.KD_EKSELON
                LEFT JOIN jenis_pegawai jp_asal ON jp_asal.KD_JENIS_PEGAWAI = pd.KD_JENIS_PEGAWAI
                LEFT JOIN jenis_pegawai jp_baru ON jp_baru.KD_JENIS_PEGAWAI = pm.KD_JENIS_PEGAWAI
                LEFT JOIN status_pegawai sp_asal ON sp_asal.KD_STATUS_PEGAWAI = pd.KD_STATUS_PEGAWAI
                LEFT JOIN status_pegawai sp_baru ON sp_baru.KD_STATUS_PEGAWAI = pm.KD_STATUS_PEGAWAI
                LEFT JOIN lokasi l_asal ON l_asal.KD_LOKASI = pd.KD_LOKASI
                LEFT JOIN lokasi l_baru ON l_baru.KD_LOKASI = pm.KD_LOKASI
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
            $aliases = [
                'ID_PEGAWAI' => ['replacement_id_pegawai', 'id_pegawai'],
                'NIP' => ['replacement_NIP', 'nip'],
                'NAMA' => ['replacement_nama', 'nama'],
                'KD_PROMOSI_DARI' => ['replacement_kd_promosi_dari', 'kd_promosi_dari'],
                'NO_SURAT' => ['replacement_no_surat', 'no_surat'],
                'TGL_SURAT' => ['replacement_tanggal_surat', 'tgl_surat'],
                'KD_BAGIAN_ASAL' => ['replacement_bagian', 'kd_bagian_asal'],
                'KD_JABATAN_ASAL' => ['replacement_jabatan', 'kd_jabatan_asal'],
                'KD_EKSELON_ASAL' => ['replacement_ekselon', 'kd_ekselon_asal'],
                'KD_STATUS_PEGAWAI_ASAL' => ['replacement_status_pegawai', 'kd_status_pegawai_asal'],
                'KD_GOLONGAN_ASAL' => ['replacement_golongan', 'kd_golongan_asal'],
                'KD_PANGKAT_ASAL' => ['replacement_pangkat', 'kd_pangkat_asal'],
                'KD_JENIS_PEGAWAI_ASAL' => ['replacement_jenis_pegawai', 'kd_jenis_pegawai_asal'],
                'KD_LOKASI_ASAL' => ['replacement_lokasi', 'kd_lokasi_asal'],
                'NIP_PIMPINAN' => ['replacement_NIP_pimpinan', 'nip_pimpinan'],
                'NAMA_PIMPINAN' => ['replacement_nama_pimpinan', 'nama_pimpinan'],
                'TGL_PROMOSI' => ['replacement_tanggal_promosi', 'tgl_promosi'],
                'KD_BAGIAN_BARU' => ['replacement_bagian_menjadi', 'kd_bagian_baru', 'kd_bagian'],
                'KD_JABATAN_BARU' => ['replacement_jabatan_menjadi', 'kd_jabatan_baru', 'kd_jabatan'],
                'KD_EKSELON_BARU' => ['replacement_ekselon_menjadi', 'kd_ekselon_baru', 'kd_ekselon'],
                'KD_STATUS_PEGAWAI_BARU' => ['replacement_status_pegawai_menjadi', 'kd_status_pegawai_baru', 'kd_status_pegawai'],
                'KD_GOLONGAN_BARU' => ['replacement_golongan_menjadi', 'kd_golongan_baru', 'kd_golongan'],
                'KD_PANGKAT_BARU' => ['replacement_pangkat_menjadi', 'kd_pangkat_baru', 'kd_pangkat'],
                'KD_JENIS_PEGAWAI_BARU' => ['replacement_jenis_pegawai_menjadi', 'kd_jenis_pegawai_baru', 'kd_jenis_pegawai'],
                'KD_LOKASI_BARU' => ['replacement_lokasi_menjadi', 'kd_lokasi_baru', 'kd_lokasi'],
                'KETERANGAN' => ['replacement_keterangan', 'keterangan'],
            ];
            foreach ($aliases as $target => $sources) {
                if (!isset($data[$target]) || $data[$target] === '') {
                    foreach ($sources as $s) {
                        if (isset($data[$s]) && $data[$s] !== '') {
                            $data[$target] = $data[$s];
                            break;
                        }
                    }
                }
            }

            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai) && !empty($data['NIP'])) {
                $pRow = $this->db->table('pegawai')->where('NIP', trim($data['NIP']))->get()->getRowArray();
                if ($pRow) {
                    $idPegawai = $pRow['ID_PEGAWAI'];
                }
            }
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
                'NIP'               => !empty($data['NIP']) ? trim($data['NIP']) : ($snapshot['NIP'] ?? ''),
                'NAMA'              => !empty($data['NAMA']) ? trim($data['NAMA']) : ($snapshot['NAMA'] ?? ''),
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
                       pkt.PANGKAT,
                       g.GOLONGAN,
                       e.EKSELON,
                       sp.STATUS_PEGAWAI,
                       jp.JENIS_PEGAWAI,
                       l.LOKASI,
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
                LEFT JOIN pangkat pkt ON pkt.KD_PANGKAT = k.KD_PANGKAT
                LEFT JOIN golongan g ON g.KD_GOLONGAN = k.KD_GOLONGAN
                LEFT JOIN ekselon e ON e.KD_EKSELON = k.KD_EKSELON
                LEFT JOIN status_pegawai sp ON sp.KD_STATUS_PEGAWAI = k.KD_STATUS_PEGAWAI
                LEFT JOIN jenis_pegawai jp ON jp.KD_JENIS_PEGAWAI = k.KD_JENIS_PEGAWAI
                LEFT JOIN lokasi l ON l.KD_LOKASI = k.KD_LOKASI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR k.NAMA LIKE ? OR k.NIP LIKE ? OR k.NO_SURAT LIKE ? OR b.BAGIAN LIKE ? OR j.JABATAN LIKE ? OR pkt.PANGKAT LIKE ? OR jk.JENIS_KONTRAK LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
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
            $idPegawai = $data['ID_PEGAWAI'] ?? $data['id_pegawai'] ?? $data['replacement_id_pegawai'] ?? null;
            $nip = $data['NIP'] ?? $data['nip'] ?? $data['replacement_NIP'] ?? null;
            if (empty($idPegawai) && !empty($nip)) {
                $rowPeg = $this->db->table('pegawai')->where('NIP', $nip)->get()->getRowArray();
                if ($rowPeg) {
                    $idPegawai = $rowPeg['ID_PEGAWAI'];
                }
            }
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_KONTRAK'] ?? $data['kd_kontrak'] ?? $data['replacement_kd_kontrak'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['no_surat']) ? trim($data['no_surat']) : (!empty($data['replacement_no_surat']) ? trim($data['replacement_no_surat']) : ('KTR/' . date('Ymd') . '/' . $idPegawai)));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['tgl_surat'] ?? $data['replacement_tanggal_surat'] ?? null, date('Y-m-d'));
            $tglMulai = $this->normalizeDate($data['TGL_KONTRAK_MULAI'] ?? $data['tgl_mulai'] ?? $data['replacement_tgl_kontrak_mulai'] ?? null, date('Y-m-d'));
            $tglSampai = $this->normalizeDate($data['TGL_KONTRAK_SAMPAI'] ?? $data['tgl_selesai'] ?? $data['replacement_tgl_kontrak_sampai'] ?? null, date('Y-m-d', strtotime('+1 year')));
            $kdJenisKontrak = !empty($data['KD_JENIS_KONTRAK']) && $data['KD_JENIS_KONTRAK'] !== '-' ? (int)$data['KD_JENIS_KONTRAK'] : (!empty($data['kd_jenis_kontrak']) && $data['kd_jenis_kontrak'] !== '-' ? (int)$data['kd_jenis_kontrak'] : (!empty($data['id_jenis_kontrak']) && $data['id_jenis_kontrak'] !== '-' ? (int)$data['id_jenis_kontrak'] : (!empty($data['replacement_jenis_kontrak']) && $data['replacement_jenis_kontrak'] !== '-' ? (int)$data['replacement_jenis_kontrak'] : 1)));

            $nama = !empty($data['NAMA']) ? $data['NAMA'] : (!empty($data['nama']) ? $data['nama'] : (!empty($data['replacement_nama']) ? $data['replacement_nama'] : ($snapshot['NAMA'] ?? '')));

            $valBagian = $data['KD_BAGIAN'] ?? $data['kd_bagian'] ?? $data['id_bagian'] ?? $data['replacement_bagian'] ?? null;
            $valJabatan = $data['KD_JABATAN'] ?? $data['kd_jabatan'] ?? $data['id_jabatan'] ?? $data['replacement_jabatan'] ?? null;
            $valEkselon = $data['KD_EKSELON'] ?? $data['kd_eselon'] ?? $data['kd_ekselon'] ?? $data['id_eselon'] ?? $data['id_ekselon'] ?? $data['replacement_ekselon'] ?? null;
            $valStatusPegawai = $data['KD_STATUS_PEGAWAI'] ?? $data['kd_status_pegawai'] ?? $data['id_status_pegawai'] ?? $data['replacement_status_pegawai'] ?? null;
            $valGolongan = $data['KD_GOLONGAN'] ?? $data['kd_golongan'] ?? $data['id_golongan'] ?? $data['replacement_golongan'] ?? null;
            $valPangkat = $data['KD_PANGKAT'] ?? $data['kd_pangkat'] ?? $data['id_pangkat'] ?? $data['replacement_pangkat'] ?? null;
            $valJenisPegawai = $data['KD_JENIS_PEGAWAI'] ?? $data['kd_jenis_pegawai'] ?? $data['id_jenis_pegawai'] ?? $data['replacement_jenis_pegawai'] ?? null;
            $valLokasi = $data['KD_LOKASI'] ?? $data['kd_lokasi'] ?? $data['id_lokasi'] ?? $data['replacement_lokasi'] ?? null;

            $record = [
                'ID_PEGAWAI'         => $idPegawai,
                'NO_SURAT'           => $noSurat,
                'TGL_SURAT'          => $tglSurat,
                'NIP'                => $snapshot['NIP'] ?? $nip,
                'NAMA'               => $nama,
                'KD_BAGIAN'          => (!empty($valBagian) && $valBagian !== '-') ? (int)$valBagian : ($snapshot['KD_BAGIAN'] ?? null),
                'KD_JABATAN'         => (isset($valJabatan) && $valJabatan !== '' && $valJabatan !== '-') ? (int)$valJabatan : ($snapshot['KD_JABATAN'] ?? null),
                'KD_EKSELON'         => (!empty($valEkselon) && $valEkselon !== '-') ? (int)$valEkselon : ($snapshot['KD_EKSELON'] ?? null),
                'KD_STATUS_PEGAWAI'  => (isset($valStatusPegawai) && $valStatusPegawai !== '' && $valStatusPegawai !== '-') ? (int)$valStatusPegawai : ($snapshot['KD_STATUS_PEGAWAI'] ?? null),
                'KD_JENIS_KONTRAK'   => $kdJenisKontrak,
                'KD_GOLONGAN'        => (!empty($valGolongan) && $valGolongan !== '-') ? (int)$valGolongan : ($snapshot['KD_GOLONGAN'] ?? null),
                'KD_PANGKAT'         => (!empty($valPangkat) && $valPangkat !== '-') ? (int)$valPangkat : ($snapshot['KD_PANGKAT'] ?? null),
                'KD_JENIS_PEGAWAI'   => (!empty($valJenisPegawai) && $valJenisPegawai !== '-') ? (int)$valJenisPegawai : ($snapshot['KD_JENIS_PEGAWAI'] ?? null),
                'KD_LOKASI'          => (!empty($valLokasi) && $valLokasi !== '-') ? (int)$valLokasi : ($snapshot['KD_LOKASI'] ?? null),
                'TGL_KONTRAK_MULAI'  => $tglMulai,
                'TGL_KONTRAK_SAMPAI' => $tglSampai,
                'KETERANGAN'         => $data['KETERANGAN'] ?? $data['keterangan'] ?? $data['replacement_keterangan'] ?? '-'
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
                       j.JABATAN,
                       pg.PANGKAT,
                       gol.GOLONGAN,
                       es.EKSELON,
                       sp.STATUS_PEGAWAI,
                       jpe.JENIS_PEGAWAI,
                       lok.LOKASI
                FROM detail_phk_pegawai phk
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = phk.ID_PEGAWAI
                LEFT JOIN jenis_phk jp ON jp.KD_JENIS_PHK = phk.KD_JENIS_PHK
                LEFT JOIN bagian b ON b.KD_BAGIAN = phk.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = phk.KD_JABATAN
                LEFT JOIN pangkat pg ON pg.KD_PANGKAT = phk.KD_PANGKAT
                LEFT JOIN golongan gol ON gol.KD_GOLONGAN = phk.KD_GOLONGAN
                LEFT JOIN ekselon es ON es.KD_EKSELON = phk.KD_EKSELON
                LEFT JOIN status_pegawai sp ON sp.KD_STATUS_PEGAWAI = phk.KD_STATUS_PEGAWAI
                LEFT JOIN jenis_pegawai jpe ON jpe.KD_JENIS_PEGAWAI = phk.KD_JENIS_PEGAWAI
                LEFT JOIN lokasi lok ON lok.KD_LOKASI = phk.KD_LOKASI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR phk.NAMA LIKE ? OR phk.NIP LIKE ? OR phk.NO_SURAT LIKE ? OR jp.JENIS_PHK LIKE ? OR b.BAGIAN LIKE ? OR j.JABATAN LIKE ? OR pg.PANGKAT LIKE ? OR phk.ALASAN LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
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
            $idPegawai = $data['ID_PEGAWAI'] ?? $data['id_pegawai'] ?? $data['replacement_id_pegawai'] ?? null;
            $nip = $data['NIP'] ?? $data['nip'] ?? $data['replacement_NIP'] ?? '';

            if (empty($idPegawai) && !empty($nip)) {
                $peg = $this->db->table('pegawai')->where('NIP', $nip)->get()->getRowArray();
                if ($peg) {
                    $idPegawai = $peg['ID_PEGAWAI'];
                }
            }

            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_PHK'] ?? $data['kd_phk'] ?? $data['id'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['no_surat']) ? trim($data['no_surat']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-PHK/' . date('Ymd') . '/' . $idPegawai)));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['tgl_surat'] ?? $data['TGL_PHK'] ?? $data['tgl_phk'] ?? null, date('Y-m-d'));
            $tglPhk = $this->normalizeDate($data['TGL_PHK'] ?? $data['tgl_phk'] ?? null, date('Y-m-d'));
            $kdJenisPhk = !empty($data['KD_JENIS_PHK']) && $data['KD_JENIS_PHK'] !== '-' ? (int)$data['KD_JENIS_PHK'] : (!empty($data['kd_jenis_phk']) && $data['kd_jenis_phk'] !== '-' ? (int)$data['kd_jenis_phk'] : (!empty($data['id_jenis_phk']) && $data['id_jenis_phk'] !== '-' ? (int)$data['id_jenis_phk'] : (!empty($data['replacement_jenis_phk']) && $data['replacement_jenis_phk'] !== '-' ? (int)$data['replacement_jenis_phk'] : 3)));
            $alasan = !empty($data['ALASAN']) ? $data['ALASAN'] : (!empty($data['alasan']) ? $data['alasan'] : (!empty($data['KETERANGAN']) ? $data['KETERANGAN'] : (!empty($data['keterangan']) ? $data['keterangan'] : '-')));

            $nama = !empty($data['NAMA']) ? $data['NAMA'] : (!empty($data['nama']) ? $data['nama'] : (!empty($data['replacement_nama']) ? $data['replacement_nama'] : ($snapshot['NAMA'] ?? '')));

            $valBagian = $data['KD_BAGIAN'] ?? $data['kd_bagian'] ?? $data['id_bagian'] ?? $data['replacement_bagian'] ?? null;
            $valJabatan = $data['KD_JABATAN'] ?? $data['kd_jabatan'] ?? $data['id_jabatan'] ?? $data['replacement_jabatan'] ?? null;
            $valEkselon = $data['KD_EKSELON'] ?? $data['kd_eselon'] ?? $data['kd_ekselon'] ?? $data['id_eselon'] ?? $data['id_ekselon'] ?? $data['replacement_ekselon'] ?? null;
            $valStatusPegawai = $data['KD_STATUS_PEGAWAI'] ?? $data['kd_status_pegawai'] ?? $data['id_status_pegawai'] ?? $data['replacement_status_pegawai'] ?? null;
            $valGolongan = $data['KD_GOLONGAN'] ?? $data['kd_golongan'] ?? $data['id_golongan'] ?? $data['replacement_golongan'] ?? null;
            $valPangkat = $data['KD_PANGKAT'] ?? $data['kd_pangkat'] ?? $data['id_pangkat'] ?? $data['replacement_pangkat'] ?? null;
            $valJenisPegawai = $data['KD_JENIS_PEGAWAI'] ?? $data['kd_jenis_pegawai'] ?? $data['id_jenis_pegawai'] ?? $data['replacement_jenis_pegawai'] ?? null;
            $valLokasi = $data['KD_LOKASI'] ?? $data['kd_lokasi'] ?? $data['id_lokasi'] ?? $data['replacement_lokasi'] ?? null;

            $record = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $snapshot['NIP'] ?? $nip,
                'NAMA'              => $nama,
                'KD_BAGIAN'         => (!empty($valBagian) && $valBagian !== '-') ? (int)$valBagian : ($snapshot['KD_BAGIAN'] ?? null),
                'KD_JABATAN'        => (isset($valJabatan) && $valJabatan !== '' && $valJabatan !== '-') ? (int)$valJabatan : ($snapshot['KD_JABATAN'] ?? null),
                'KD_EKSELON'        => (!empty($valEkselon) && $valEkselon !== '-') ? (int)$valEkselon : ($snapshot['KD_EKSELON'] ?? null),
                'KD_STATUS_PEGAWAI' => (isset($valStatusPegawai) && $valStatusPegawai !== '' && $valStatusPegawai !== '-') ? (int)$valStatusPegawai : ($snapshot['KD_STATUS_PEGAWAI'] ?? null),
                'KD_JENIS_PHK'      => $kdJenisPhk,
                'KD_GOLONGAN'       => (!empty($valGolongan) && $valGolongan !== '-') ? (int)$valGolongan : ($snapshot['KD_GOLONGAN'] ?? null),
                'KD_PANGKAT'        => (!empty($valPangkat) && $valPangkat !== '-') ? (int)$valPangkat : ($snapshot['KD_PANGKAT'] ?? null),
                'KD_JENIS_PEGAWAI'  => (!empty($valJenisPegawai) && $valJenisPegawai !== '-') ? (int)$valJenisPegawai : ($snapshot['KD_JENIS_PEGAWAI'] ?? null),
                'KD_LOKASI'         => (!empty($valLokasi) && $valLokasi !== '-') ? (int)$valLokasi : ($snapshot['KD_LOKASI'] ?? null),
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
                       COALESCE(pg.NIP, ph.NIP) AS NIP,
                       jp.JENIS_PENGHARGAAN,
                       b.BAGIAN,
                       j.JABATAN,
                       pkt.PANGKAT,
                       g.GOLONGAN,
                       e.EKSELON,
                       sp.STATUS_PEGAWAI,
                       l.LOKASI
                FROM detail_penghargaan_pegawai ph
                LEFT JOIN pegawai pg ON pg.ID_PEGAWAI = ph.ID_PEGAWAI
                LEFT JOIN jenis_penghargaan jp ON jp.KD_JENIS_PENGHARGAAN = ph.KD_JENIS_PENGHARGAAN
                LEFT JOIN bagian b ON b.KD_BAGIAN = ph.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = ph.KD_JABATAN
                LEFT JOIN pangkat pkt ON pkt.KD_PANGKAT = ph.KD_PANGKAT
                LEFT JOIN golongan g ON g.KD_GOLONGAN = ph.KD_GOLONGAN
                LEFT JOIN ekselon e ON e.KD_EKSELON = ph.KD_EKSELON
                LEFT JOIN status_pegawai sp ON sp.KD_STATUS_PEGAWAI = ph.KD_STATUS_PEGAWAI
                LEFT JOIN lokasi l ON l.KD_LOKASI = ph.KD_LOKASI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (pg.NAMA LIKE ? OR ph.NIP LIKE ? OR ph.NO_SURAT LIKE ? OR jp.JENIS_PENGHARGAAN LIKE ? OR b.BAGIAN LIKE ? OR j.JABATAN LIKE ? OR pkt.PANGKAT LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
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
            // Support parameter alias
            $idPegawai = $data['ID_PEGAWAI'] ?? $data['id_pegawai'] ?? null;
            $nip = trim($data['NIP'] ?? $data['nip'] ?? '');
            if (empty($idPegawai) && !empty($nip)) {
                $peg = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NIP = ? LIMIT 1", [$nip])->getRowArray();
                if ($peg) {
                    $idPegawai = $peg['ID_PEGAWAI'];
                }
            }
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih atau NIP tidak valid'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_PENGHARGAAN'] ?? $data['kd_penghargaan'] ?? $data['id_penghargaan'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['no_surat']) ? trim($data['no_surat']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-PGR/' . date('Ymd') . '/' . $idPegawai)));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['tgl_surat'] ?? $data['TGL_PENGHARGAAN'] ?? $data['tgl_penghargaan'] ?? null, date('Y-m-d'));
            $tglPenghargaan = $this->normalizeDate($data['TGL_PENGHARGAAN'] ?? $data['tgl_penghargaan'] ?? null, date('Y-m-d'));
            
            $kdJenisRaw = $data['KD_JENIS_PENGHARGAAN'] ?? $data['kd_jenis_penghargaan'] ?? $data['id_jenis_penghargaan'] ?? null;
            $kdJenis = (!empty($kdJenisRaw) && $kdJenisRaw !== '-') ? (int)$kdJenisRaw : 1;

            $kdBagian = !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : (!empty($data['id_bagian']) ? (int)$data['id_bagian'] : (!empty($data['kd_bagian']) ? (int)$data['kd_bagian'] : $snapshot['KD_BAGIAN']));
            $kdJabatan = isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' && $data['KD_JABATAN'] !== '-' ? (int)$data['KD_JABATAN'] : (isset($data['id_jabatan']) && $data['id_jabatan'] !== '' && $data['id_jabatan'] !== '-' ? (int)$data['id_jabatan'] : (isset($data['kd_jabatan']) && $data['kd_jabatan'] !== '' && $data['kd_jabatan'] !== '-' ? (int)$data['kd_jabatan'] : $snapshot['KD_JABATAN']));
            $kdEkselon = !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : (!empty($data['id_eselon']) ? (int)$data['id_eselon'] : (!empty($data['id_ekselon']) ? (int)$data['id_ekselon'] : (!empty($data['kd_ekselon']) ? (int)$data['kd_ekselon'] : $snapshot['KD_EKSELON'])));
            $kdStatus = isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' && $data['KD_STATUS_PEGAWAI'] !== '-' ? (int)$data['KD_STATUS_PEGAWAI'] : (isset($data['id_status_pegawai']) && $data['id_status_pegawai'] !== '' && $data['id_status_pegawai'] !== '-' ? (int)$data['id_status_pegawai'] : (isset($data['kd_status_pegawai']) && $data['kd_status_pegawai'] !== '' && $data['kd_status_pegawai'] !== '-' ? (int)$data['kd_status_pegawai'] : $snapshot['KD_STATUS_PEGAWAI']));
            $kdGolongan = !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : (!empty($data['id_golongan']) ? (int)$data['id_golongan'] : (!empty($data['kd_golongan']) ? (int)$data['kd_golongan'] : $snapshot['KD_GOLONGAN']));
            $kdPangkat = !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : (!empty($data['id_pangkat']) ? (int)$data['id_pangkat'] : (!empty($data['kd_pangkat']) ? (int)$data['kd_pangkat'] : $snapshot['KD_PANGKAT']));
            $kdJenisPegawai = !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : (!empty($data['id_jenis_pegawai']) ? (int)$data['id_jenis_pegawai'] : (!empty($data['kd_jenis_pegawai']) ? (int)$data['kd_jenis_pegawai'] : $snapshot['KD_JENIS_PEGAWAI']));
            $kdLokasi = !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : (!empty($data['id_lokasi']) ? (int)$data['id_lokasi'] : (!empty($data['kd_lokasi']) ? (int)$data['kd_lokasi'] : $snapshot['KD_LOKASI']));
            $keterangan = $data['KETERANGAN'] ?? $data['keterangan'] ?? '-';
            $nama = !empty($data['NAMA']) ? trim($data['NAMA']) : (!empty($data['nama']) ? trim($data['nama']) : $snapshot['NAMA']);

            $record = [
                'ID_PEGAWAI'           => $idPegawai,
                'NO_SURAT'             => $noSurat,
                'TGL_SURAT'            => $tglSurat,
                'NIP'                  => $snapshot['NIP'],
                'NAMA'                 => $nama,
                'KD_BAGIAN'            => $kdBagian,
                'KD_JABATAN'           => $kdJabatan,
                'KD_EKSELON'           => $kdEkselon,
                'KD_STATUS_PEGAWAI'    => $kdStatus,
                'KD_JENIS_PENGHARGAAN' => $kdJenis,
                'KD_GOLONGAN'          => $kdGolongan,
                'KD_PANGKAT'           => $kdPangkat,
                'KD_JENIS_PEGAWAI'     => $kdJenisPegawai,
                'KD_LOKASI'            => $kdLokasi,
                'TGL_PENGHARGAAN'      => $tglPenghargaan,
                'KETERANGAN'           => $keterangan
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
                       COALESCE(pg.NIP, s.NIP) AS NIP,
                       js.JENIS_SANKSI,
                       b.BAGIAN,
                       j.JABATAN,
                       pkt.PANGKAT,
                       g.GOLONGAN,
                       sp.STATUS_PEGAWAI,
                       l.LOKASI
                FROM detail_sanksi_pegawai s
                LEFT JOIN pegawai pg ON pg.ID_PEGAWAI = s.ID_PEGAWAI
                LEFT JOIN jenis_sanksi js ON js.KD_JENIS_SANKSI = s.KD_JENIS_SANKSI
                LEFT JOIN bagian b ON b.KD_BAGIAN = s.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = s.KD_JABATAN
                LEFT JOIN pangkat pkt ON pkt.KD_PANGKAT = s.KD_PANGKAT
                LEFT JOIN golongan g ON g.KD_GOLONGAN = s.KD_GOLONGAN
                LEFT JOIN status_pegawai sp ON sp.KD_STATUS_PEGAWAI = s.KD_STATUS_PEGAWAI
                LEFT JOIN lokasi l ON l.KD_LOKASI = s.KD_LOKASI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (pg.NAMA LIKE ? OR s.NAMA LIKE ? OR s.NIP LIKE ? OR pg.NIP LIKE ? OR s.NO_SURAT LIKE ? OR js.JENIS_SANKSI LIKE ? OR b.BAGIAN LIKE ? OR j.JABATAN LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
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
            $idPegawai = $data['ID_PEGAWAI'] ?? $data['id_pegawai'] ?? $data['replacement_id_pegawai'] ?? null;
            if (empty($idPegawai)) {
                $nip = $data['NIP'] ?? $data['nip'] ?? $data['replacement_NIP'] ?? null;
                if (!empty($nip)) {
                    $p = $this->db->table('pegawai')->select('ID_PEGAWAI')->where('NIP', $nip)->get()->getRowArray();
                    if ($p) $idPegawai = $p['ID_PEGAWAI'];
                }
            }
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_SANKSI'] ?? $data['kd_sanksi'] ?? $data['replacement_kd_sanksi'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['replacement_no_surat']) ? trim($data['replacement_no_surat']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-SNK/' . date('Ymd') . '/' . $idPegawai)));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['replacement_tanggal_surat'] ?? $data['TGL_SANKSI'] ?? null, date('Y-m-d'));
            $tglSanksi = $this->normalizeDate($data['TGL_SANKSI'] ?? $data['replacement_tanggal_sanksi'] ?? null, date('Y-m-d'));
            $mulai = $this->normalizeDate($data['MULAI'] ?? $data['TGL_MULAI'] ?? $data['berlaku_mulai'] ?? $data['replacement_berlaku_mulai'] ?? null, date('Y-m-d'));
            $sampai = $this->normalizeDate($data['SAMPAI'] ?? $data['TGL_SELESAI'] ?? $data['berlaku_selesai'] ?? $data['replacement_berlaku_selesai'] ?? null, date('Y-m-d', strtotime('+1 month')));
            
            $kdJenis = !empty($data['KD_JENIS_SANKSI']) ? (int)$data['KD_JENIS_SANKSI'] : (!empty($data['replacement_jenis_sanksi']) ? (int)$data['replacement_jenis_sanksi'] : 1);
            $kdBagian = !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : (!empty($data['replacement_bagian']) ? (int)$data['replacement_bagian'] : $snapshot['KD_BAGIAN']);
            $kdGolongan = !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : (!empty($data['replacement_golongan']) ? (int)$data['replacement_golongan'] : $snapshot['KD_GOLONGAN']);
            $kdJabatan = isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : (isset($data['replacement_jabatan']) && $data['replacement_jabatan'] !== '' ? (int)$data['replacement_jabatan'] : $snapshot['KD_JABATAN']);
            $kdPangkat = !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : (!empty($data['replacement_pangkat']) ? (int)$data['replacement_pangkat'] : $snapshot['KD_PANGKAT']);
            $kdStatusPegawai = isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : (isset($data['replacement_status_pegawai']) && $data['replacement_status_pegawai'] !== '' ? (int)$data['replacement_status_pegawai'] : $snapshot['KD_STATUS_PEGAWAI']);
            $kdLokasi = !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : (!empty($data['replacement_lokasi']) ? (int)$data['replacement_lokasi'] : $snapshot['KD_LOKASI']);
            $keterangan = $data['KETERANGAN'] ?? $data['replacement_keterangan'] ?? '-';

            $record = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => $data['NIP'] ?? $data['replacement_NIP'] ?? $snapshot['NIP'],
                'NAMA'              => $data['NAMA'] ?? $data['replacement_nama'] ?? $snapshot['NAMA'],
                'KD_BAGIAN'         => $kdBagian,
                'KD_JABATAN'        => $kdJabatan,
                'KD_EKSELON'        => !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : (!empty($data['replacement_ekselon']) ? (int)$data['replacement_ekselon'] : $snapshot['KD_EKSELON']),
                'KD_STATUS_PEGAWAI' => $kdStatusPegawai,
                'KD_JENIS_SANKSI'   => $kdJenis,
                'KD_GOLONGAN'       => $kdGolongan,
                'KD_PANGKAT'        => $kdPangkat,
                'KD_JENIS_PEGAWAI'  => !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : (!empty($data['replacement_jenis_pegawai']) ? (int)$data['replacement_jenis_pegawai'] : $snapshot['KD_JENIS_PEGAWAI']),
                'KD_LOKASI'         => $kdLokasi,
                'TGL_SANKSI'        => $tglSanksi,
                'MULAI'             => $mulai,
                'SAMPAI'            => $sampai,
                'KETERANGAN'        => $keterangan
            ];

            if (isset($data['FILE']) && $data['FILE'] !== '') {
                $record['FILE'] = $data['FILE'];
            } elseif (isset($data['file']) && $data['file'] !== '') {
                $record['FILE'] = $data['file'];
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
    // 11. CUTI
    // ==========================================
    public function getCuti($params = [])
    {
        $keyword = trim($params['search'] ?? $params['keyword'] ?? '');

        $sql = "SELECT c.*,
                       c.NOMOR_SURAT AS NO_SURAT,
                       c.TANGGAL_MULAI AS TGL_CUTI_MULAI,
                       c.TANGGAL_SAMPAI AS TGL_CUTI_SAMPAI,
                       c.KEPERLUAN AS KETERANGAN,
                       CASE 
                           WHEN c.TANGGAL_SAMPAI IS NOT NULL AND c.TANGGAL_SAMPAI != '0000-00-00' 
                                AND c.TANGGAL_MULAI IS NOT NULL AND c.TANGGAL_MULAI != '0000-00-00'
                           THEN DATEDIFF(c.TANGGAL_SAMPAI, c.TANGGAL_MULAI) + 1
                           ELSE 1
                       END AS LAMA,
                       COALESCE(p.NAMA, c.NAMA) AS NAMA_PEGAWAI,
                       jc.JENIS_CUTI,
                       b.BAGIAN,
                       pp.NAMA AS NAMA_PENGGANTI
                FROM detail_cuti_pegawai c
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = c.ID_PEGAWAI
                LEFT JOIN jenis_cuti jc ON jc.KD_JENIS_CUTI = c.KD_JENIS_CUTI
                LEFT JOIN bagian b ON b.KD_BAGIAN = c.KD_BAGIAN
                LEFT JOIN pegawai pp ON pp.ID_PEGAWAI = c.PEGAWAI_PENGGANTI
                WHERE 1=1";
        $binds = [];

        if ($keyword !== '') {
            $sql .= " AND (p.NAMA LIKE ? OR c.NAMA LIKE ? OR c.NIP LIKE ? OR p.NIP LIKE ? OR jc.JENIS_CUTI LIKE ? OR c.NOMOR_SURAT LIKE ? OR b.BAGIAN LIKE ?)";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
            $binds[] = "%$keyword%";
        }

        $sql .= " ORDER BY c.KD_CUTI DESC LIMIT 200";
        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function getSisaCuti($idPegawai = null, $nip = null)
    {
        $year = date('Y');
        $row = null;
        if ($idPegawai !== null && $idPegawai !== '') {
            $row = $this->db->table('pegawai')->where('ID_PEGAWAI', $idPegawai)->get()->getRowArray();
        }
        if (!$row && !empty($nip)) {
            $row = $this->db->table('pegawai')->where('NIP', $nip)->get()->getRowArray();
        }

        $id = $row['ID_PEGAWAI'] ?? ($idPegawai !== null && $idPegawai !== '' ? $idPegawai : 0);
        $nipVal = $row['NIP'] ?? $nip ?? '';
        $baseCuti = !empty($row['jml_cuti']) ? (int)$row['jml_cuti'] : 12;

        if (!empty($id)) {
            $mc = $this->db->table('master_cutitahunan')
                ->where('id_pegawai', $id)
                ->where('tahun', $year)
                ->get()->getRowArray();
            if ($mc && isset($mc['jumlah'])) {
                $baseCuti = (int)$mc['jumlah'];
            }
        }

        $used = 0;
        if ($id !== null || !empty($nipVal)) {
            $q = "SELECT COALESCE(SUM(
                    CASE 
                        WHEN TANGGAL_SAMPAI IS NOT NULL AND TANGGAL_SAMPAI != '0000-00-00'
                        THEN DATEDIFF(TANGGAL_SAMPAI, TANGGAL_MULAI) + 1
                        ELSE 1
                    END
                  ), 0) AS total_hari
                  FROM detail_cuti_pegawai
                  WHERE (ID_PEGAWAI = ? OR (NIP = ? AND NIP != ''))
                    AND YEAR(TANGGAL_MULAI) = ?
                    AND (STATUS_BATAL = 0 OR STATUS_BATAL IS NULL)
                    AND (STATUS = 1 OR STATUS IS NULL)";
            $usedRow = $this->db->query($q, [$id, $nipVal, $year])->getRowArray();
            if ($usedRow && isset($usedRow['total_hari'])) {
                $used = (int)$usedRow['total_hari'];
            }
        }

        $sisa = max(0, $baseCuti - $used);
        $kontak = $row['HANDPHONE'] ?? $row['TELEPHONE'] ?? '';
        if (empty($kontak)) {
            $kontak = '4456';
        }

        return [
            'id_pegawai' => $id,
            'nip'        => $nipVal,
            'nama'       => $row['NAMA'] ?? '',
            'total'      => $baseCuti,
            'terpakai'   => $used,
            'sisa'       => $sisa,
            'kontak'     => $kontak,
            'bagian'     => $row['BAGIAN'] ?? ''
        ];
    }

    public function saveCuti($data)
    {
        try {
            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if ($idPegawai === null || $idPegawai === '') {
                $idPegawai = $data['NIP'] ?? null;
            }
            if ($idPegawai === null || $idPegawai === '') {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_CUTI'] ?? '';

            $noSurat = !empty($data['NOMOR_SURAT']) ? trim($data['NOMOR_SURAT']) : (!empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : ('CUTI/' . date('Ymd') . '/' . ($snapshot['ID_PEGAWAI'] ?? $idPegawai)));
            $tglMulai = $this->normalizeDate($data['TANGGAL_MULAI'] ?? $data['TGL_CUTI_MULAI'] ?? null, date('Y-m-d'));
            $tglSampai = $this->normalizeDate($data['TANGGAL_SAMPAI'] ?? $data['TGL_CUTI_SAMPAI'] ?? null, date('Y-m-d'));
            $kdJenisCuti = !empty($data['KD_JENIS_CUTI']) ? (int)$data['KD_JENIS_CUTI'] : 1;
            $keperluan = !empty($data['KEPERLUAN']) ? $data['KEPERLUAN'] : (!empty($data['KETERANGAN']) ? $data['KETERANGAN'] : '-');
            $nip = !empty($data['NIP']) ? $data['NIP'] : (!empty($snapshot['NIP']) ? $snapshot['NIP'] : '');
            $nama = !empty($data['NAMA']) ? $data['NAMA'] : (!empty($snapshot['NAMA']) ? $snapshot['NAMA'] : '');
            $kdBagian = !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : ($snapshot['KD_BAGIAN'] ?? 1);
            $idPegVal = isset($snapshot['ID_PEGAWAI']) ? $snapshot['ID_PEGAWAI'] : $idPegawai;

            $record = [
                'ID_PEGAWAI'        => $idPegVal,
                'NIP'               => $nip,
                'NAMA'              => $nama,
                'KD_BAGIAN'         => $kdBagian,
                'KD_JENIS_CUTI'     => $kdJenisCuti,
                'TANGGAL_MULAI'     => $tglMulai,
                'TANGGAL_SAMPAI'    => $tglSampai,
                'NOMOR_SURAT'       => $noSurat,
                'KEPERLUAN'         => $keperluan,
                'PEGAWAI_PENGGANTI' => $data['PEGAWAI_PENGGANTI'] ?? $data['PENGGANTI'] ?? null,
                'STATUS'            => isset($data['STATUS']) ? (int)$data['STATUS'] : 0,
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

    public function updateApprovalHRD($kdCuti, $statusApproval, $catatanHRD = '')
    {
        try {
            if (empty($kdCuti)) {
                return ['code' => 400, 'message' => 'KD_CUTI diperlukan'];
            }

            $statusApproval = (int)$statusApproval; // 1 = Approve, 2 = Reject
            $updateData = [
                'APPROVAL_HRD' => $statusApproval,
                'CATATAN_HRD'  => $catatanHRD,
            ];

            // Selaraskan juga STATUS umum
            if ($statusApproval === 1) {
                $updateData['STATUS'] = 1; // Disetujui
            } elseif ($statusApproval === 2) {
                $updateData['STATUS'] = 2; // Ditolak
            }

            $this->db->table('detail_cuti_pegawai')
                ->where('KD_CUTI', $kdCuti)
                ->update($updateData);

            $msg = 'Status approval cuti berhasil direset';
            if ($statusApproval === 1) {
                $msg = 'Pengajuan cuti berhasil disetujui HRD';
            } elseif ($statusApproval === 2) {
                $msg = 'Pengajuan cuti telah ditolak HRD';
            }

            return [
                'code' => 200,
                'message' => $msg,
                'kd_cuti' => $kdCuti,
                'approval_hrd' => $statusApproval
            ];
        } catch (\Exception $e) {
            return ['code' => 500, 'message' => $e->getMessage()];
        }
    }

    public function getMonitoringCuti($params = [])
    {
        $year = $params['tahun'] ?? $params['year'] ?? '';
        $month = $params['bulan'] ?? $params['month'] ?? '';
        $keyword = trim($params['search'] ?? $params['keyword'] ?? $params['unit'] ?? '');

        // 15 jenis cuti baku sesuai spesifikasi
        $jenisList = [
            ['kd' => 3,  'key' => 'CUTI_MELAHIRKAN',                             'nama' => 'CUTI MELAHIRKAN'],
            ['kd' => 6,  'key' => 'CUTI_TAHUNAN',                                'nama' => 'CUTI TAHUNAN'],
            ['kd' => 9,  'key' => 'CUTI_DI_LUAR_TANGGUNGAN',                    'nama' => 'CUTI DI LUAR TANGGUNGAN'],
            ['kd' => 13, 'key' => 'IJIN_PULANG_AWAL',                           'nama' => 'IJIN PULANG AWAL'],
            ['kd' => 14, 'key' => 'IJIN_KELUAR_TUGAS_IKT',                     'nama' => 'IJIN KELUAR TUGAS (IKT)'],
            ['kd' => 15, 'key' => 'IJIN_KELUAR_PRIBADI_IKP',                   'nama' => 'IJIN KELUAR PRIBADI (IKP)'],
            ['kd' => 17, 'key' => 'CUTI_KHUSUS_PERNIKAHAN_ANAK',               'nama' => 'CUTI KHUSUS (PERNIKAHAN ANAK)'],
            ['kd' => 18, 'key' => 'CUTI_KHUSUS_IBADAH_HAJI',                   'nama' => 'CUTI KHUSUS (IBADAH HAJI)'],
            ['kd' => 19, 'key' => 'CUTI_KHUSUS_IBADAH_UMROH',                  'nama' => 'CUTI KHUSUS (IBADAH UMROH)'],
            ['kd' => 20, 'key' => 'CUTI_KHUSUS_ISTRI_MELAHIRKAN_KEGUGURAN',     'nama' => 'CUTI KHUSUS (ISTRI MELAHIRKAN/ KEGUGURAN)'],
            ['kd' => 21, 'key' => 'CUTI_KHUSUS_MENIKAH',                       'nama' => 'CUTI KHUSUS (MENIKAH)'],
            ['kd' => 22, 'key' => 'CUTI_KHUSUS_MENGKHITANKAN_PEMBAPTISAN_ANAK', 'nama' => 'CUTI KHUSUS (MENGKHITANKAN/ PEMBAPTISAN ANAK)'],
            ['kd' => 23, 'key' => 'CUTI_KHUSUS_KELUARGA_INTI_MENINGGAL_DUNIA',  'nama' => 'CUTI KHUSUS (KELUARGA INTI MENINGGAL DUNIA)'],
            ['kd' => 26, 'key' => 'CUTI_SAKIT',                                'nama' => 'CUTI SAKIT'],
            ['kd' => 27, 'key' => 'IJIN_KHUSUS',                               'nama' => 'IJIN KHUSUS'],
        ];

        // 48 unit baku rumah sakit sesuai template spesifikasi
        $standardUnits = [
            ['no' => 1,  'nama' => 'Kepegawaian', 'aliases' => ['kepegawaian', 'admin dan kepegawaian', 'sdm']],
            ['no' => 2,  'nama' => 'case manager', 'aliases' => ['case manager', 'casemanager']],
            ['no' => 3,  'nama' => 'ICU', 'aliases' => ['icu', 'hcu', 'iccu', 'intensif']],
            ['no' => 4,  'nama' => 'UGD umum', 'aliases' => ['ugd umum', 'igd umum', 'ugd', 'igd']],
            ['no' => 5,  'nama' => 'UGD PONEK', 'aliases' => ['ugd ponek', 'igd ponek', 'ponek']],
            ['no' => 6,  'nama' => 'POLIKLINIK BEDAH', 'aliases' => ['poliklinik bedah', 'poli bedah', 'bedah']],
            ['no' => 7,  'nama' => 'POLIKLINIK PENYAKIT DALAM', 'aliases' => ['poliklinik penyakit dalam', 'poli dalam', 'penyakit dalam']],
            ['no' => 8,  'nama' => 'POLIKLINIK ANAK', 'aliases' => ['poliklinik anak', 'poli anak']],
            ['no' => 9,  'nama' => 'POLIKLINIK OBGYN', 'aliases' => ['poliklinik obgyn', 'poli obgyn', 'obgyn', 'kandungan']],
            ['no' => 10, 'nama' => 'POLIKLINIK GIGI', 'aliases' => ['poliklinik gigi', 'poli gigi', 'gigi dasar', 'poli gigi dasar']],
            ['no' => 11, 'nama' => 'POLIKLINIK THT', 'aliases' => ['poliklinik tht', 'poli tht', 'tht']],
            ['no' => 12, 'nama' => 'POLIKLINIK JANTUNG DAN PEMBULUH DARAH', 'aliases' => ['poliklinik jantung', 'poli jantung', 'jantung']],
            ['no' => 13, 'nama' => 'LABORATORIUM', 'aliases' => ['laboratorium', 'lab', 'instalasi laboratorium']],
            ['no' => 14, 'nama' => 'GIZI', 'aliases' => ['gizi', 'konsultasi gizi', 'instalasi gizi']],
            ['no' => 15, 'nama' => 'PERENCANAAN dan PENGEMBANGAN', 'aliases' => ['perencanaan dan pengembangan', 'perencanaan']],
            ['no' => 16, 'nama' => 'KEUANGAN dan UMUM', 'aliases' => ['keuangan dan umum', 'keuangan']],
            ['no' => 17, 'nama' => 'OK', 'aliases' => ['ok', 'kamar operasi', 'bedah sentral', 'ibs']],
            ['no' => 18, 'nama' => 'POLIKLINIK SYARAF', 'aliases' => ['poliklinik syaraf', 'poli syaraf', 'syaraf', 'saraf']],
            ['no' => 19, 'nama' => 'RUANGAN BERSALIN', 'aliases' => ['ruangan bersalin', 'kebidanan', 'bersalin', 'vk']],
            ['no' => 20, 'nama' => 'RUANG NIFAS', 'aliases' => ['ruang nifas', 'nifas']],
            ['no' => 21, 'nama' => 'RUANG NICU', 'aliases' => ['ruang nicu', 'perinatologi', 'nicu', 'picu']],
            ['no' => 22, 'nama' => 'POLIKLINIK MATA', 'aliases' => ['poliklinik mata', 'poli mata', 'mata']],
            ['no' => 23, 'nama' => 'RUANG ANAK', 'aliases' => ['ruang anak', 'anak', 'bangsal anak']],
            ['no' => 24, 'nama' => 'REKAM MEDIS', 'aliases' => ['rekam medis', 'ruang rekam medis', 'rekam medik']],
            ['no' => 25, 'nama' => 'BPJS', 'aliases' => ['bpjs', 'casemix']],
            ['no' => 26, 'nama' => 'RADIOLOGI', 'aliases' => ['radiologi', 'instalasi radiologi', 'roentgen']],
            ['no' => 27, 'nama' => 'POLIKLINIK TB DOTS RO', 'aliases' => ['poliklinik tb dots', 'poli tb dots', 'tb dots', 'tb dots ro']],
            ['no' => 28, 'nama' => 'LONDRY', 'aliases' => ['londry', 'laundry', 'loundry']],
            ['no' => 29, 'nama' => 'CSSD', 'aliases' => ['cssd', 'sterilisasi']],
            ['no' => 30, 'nama' => 'KESEHATAN LINGKUNGAN', 'aliases' => ['kesehatan lingkungan', 'kesling', 'sanitasi']],
            ['no' => 31, 'nama' => 'PKRS_promosi kesehatan RS', 'aliases' => ['pkrs_promosi kesehatan rs', 'pkrs', 'promosi kesehatan']],
            ['no' => 32, 'nama' => 'PENGELOLA BARANG / ASET', 'aliases' => ['pengelola barang / aset', 'pengelola barang', 'aset']],
            ['no' => 33, 'nama' => 'LOGISTIK DAN UMUM', 'aliases' => ['logistik dan umum', 'logistik', 'rumah tangga']],
            ['no' => 34, 'nama' => 'IPSRS', 'aliases' => ['ipsrs', 'ips rs', 'pemeliharaan sarana']],
            ['no' => 35, 'nama' => 'BDRS', 'aliases' => ['bdrs', 'unit transfusi darah', 'bank darah', 'utd']],
            ['no' => 36, 'nama' => 'BIDANG KEPERAWATAN', 'aliases' => ['bidang keperawatan', 'keperawatan', 'komite keperawatan']],
            ['no' => 37, 'nama' => 'GUDANG FARMASI', 'aliases' => ['gudang farmasi']],
            ['no' => 38, 'nama' => 'RUANG APOTIK', 'aliases' => ['ruang apotik', 'apotik', 'apotek']],
            ['no' => 39, 'nama' => 'INSTALASI FARMASI', 'aliases' => ['instalasi farmasi', 'farmasi']],
            ['no' => 40, 'nama' => 'KEAMANAN', 'aliases' => ['keamanan', 'satpam', 'security']],
            ['no' => 41, 'nama' => 'CLEANING SERVICE', 'aliases' => ['cleaning service', 'cs', 'kebersihan']],
            ['no' => 42, 'nama' => 'PPI', 'aliases' => ['ppi', 'pencegahan infeksi']],
            ['no' => 43, 'nama' => 'DIREKTUR', 'aliases' => ['direktur', 'direksi', 'pimpinan']],
            ['no' => 44, 'nama' => 'KASUBAG TATA USAHA', 'aliases' => ['kasubag tata usaha', 'tata usaha', 'tu']],
            ['no' => 45, 'nama' => 'DRIVER AMBULANCE', 'aliases' => ['driver ambulance', 'driver', 'sopir', 'pengemudi']],
            ['no' => 46, 'nama' => 'RUANG INTERNA DEWASA', 'aliases' => ['ruang interna dewasa', 'kelas 1 dan 2', 'umum dan bangsal', 'interna', 'rawat inap dewasa']],
            ['no' => 47, 'nama' => 'POLIKLINIK', 'aliases' => ['poliklinik', 'poli general check up', 'poli fisioterapi', 'rawat jalan']],
            ['no' => 48, 'nama' => 'IPCN', 'aliases' => ['ipcn']]
        ];

        // Inisialisasi data unit
        $unitsData = [];
        foreach ($standardUnits as $u) {
            $cutiCounts = [];
            foreach ($jenisList as $j) {
                $cutiCounts[$j['kd']] = 0;
            }
            $unitsData[] = [
                'no'         => $u['no'],
                'nama'       => $u['nama'],
                'aliases'    => $u['aliases'],
                'cuti'       => $cutiCounts,
                'sudah'      => 0,
                'belum'      => 0,
                'ditolak'    => 0,
                'total'      => 0,
                'keterangan' => 'Tidak ada data',
                'detail'     => []
            ];
        }

        // Ambil data dari detail_cuti_pegawai dengan filter tanggal
        $sql = "SELECT c.KD_CUTI, c.ID_PEGAWAI, c.NIP, c.NAMA, c.KD_BAGIAN, b.BAGIAN, c.KD_JENIS_CUTI, jc.JENIS_CUTI,
                       c.TANGGAL_MULAI, c.TANGGAL_SAMPAI, c.STATUS, c.APPROVAL_HRD, c.CATATAN_HRD, c.CATATAN_ATASAN
                FROM detail_cuti_pegawai c
                LEFT JOIN bagian b ON b.KD_BAGIAN = c.KD_BAGIAN
                LEFT JOIN jenis_cuti jc ON jc.KD_JENIS_CUTI = c.KD_JENIS_CUTI
                WHERE (c.STATUS_BATAL = 0 OR c.STATUS_BATAL IS NULL)";
        
        $binds = [];
        if (!empty($year)) {
            $sql .= " AND YEAR(c.TANGGAL_MULAI) = ?";
            $binds[] = $year;
        }
        if (!empty($month)) {
            $sql .= " AND MONTH(c.TANGGAL_MULAI) = ?";
            $binds[] = $month;
        }

        $records = $this->db->query($sql, $binds)->getResultArray();

        // Agregasi setiap permohonan ke dalam unit yang sesuai
        foreach ($records as $row) {
            $bagianName = strtolower(trim($row['BAGIAN'] ?? ''));
            $kdJenisCuti = (int)($row['KD_JENIS_CUTI'] ?? 0);
            $apprHrd = (int)($row['APPROVAL_HRD'] ?? 0);

            $matchedIdx = -1;
            // Cocokkan ke unit yang bersangkutan
            foreach ($unitsData as $idx => $u) {
                if ($bagianName !== '' && (strtolower($u['nama']) === $bagianName || in_array($bagianName, $u['aliases']))) {
                    $matchedIdx = $idx;
                    break;
                }
            }

            // Jika tidak cocok langsung, cari substring
            if ($matchedIdx === -1 && $bagianName !== '') {
                foreach ($unitsData as $idx => $u) {
                    foreach ($u['aliases'] as $alias) {
                        if (strpos($bagianName, $alias) !== false || strpos($alias, $bagianName) !== false) {
                            $matchedIdx = $idx;
                            break 2;
                        }
                    }
                }
            }

            // Fallback cerdas
            if ($matchedIdx === -1) {
                if (strpos($bagianName, 'poli') !== false) {
                    $matchedIdx = 46; // No 47 POLIKLINIK
                } elseif (strpos($bagianName, 'kepeg') !== false || strpos($bagianName, 'admin') !== false) {
                    $matchedIdx = 0;  // No 1 Kepegawaian
                }
            }

            if ($matchedIdx !== -1) {
                if (isset($unitsData[$matchedIdx]['cuti'][$kdJenisCuti])) {
                    $unitsData[$matchedIdx]['cuti'][$kdJenisCuti]++;
                }

                // Hitung status approval
                if ($apprHrd === 1) {
                    $unitsData[$matchedIdx]['sudah']++;
                } elseif ($apprHrd === 2) {
                    $unitsData[$matchedIdx]['ditolak']++;
                } else {
                    $unitsData[$matchedIdx]['belum']++;
                }

                $unitsData[$matchedIdx]['total']++;
                $unitsData[$matchedIdx]['detail'][] = [
                    'kd_cuti'      => $row['KD_CUTI'],
                    'nip'          => $row['NIP'],
                    'nama'         => $row['NAMA'],
                    'jenis_cuti'   => $row['JENIS_CUTI'],
                    'tgl_mulai'    => $row['TANGGAL_MULAI'],
                    'approval_hrd' => $apprHrd
                ];
            }
        }

        // Tentukan keterangan akhir untuk setiap unit
        foreach ($unitsData as &$u) {
            if ($u['total'] === 0) {
                $u['keterangan'] = 'Tidak ada data';
            } elseif ($u['belum'] > 0) {
                $u['keterangan'] = 'Ada data yang belum di Approv';
            } else {
                $u['keterangan'] = 'Sudah di Approv semua';
            }
        }
        unset($u);

        // Filter keyword unit jika diminta
        if ($keyword !== '') {
            $filtered = [];
            foreach ($unitsData as $u) {
                if (stripos($u['nama'], $keyword) !== false) {
                    $filtered[] = $u;
                }
            }
            $unitsData = $filtered;
        }

        return [
            'jenis_cuti' => $jenisList,
            'units'      => $unitsData,
            'summary'    => [
                'total_permohonan' => count($records),
                'total_unit'       => count($unitsData)
            ]
        ];
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
                       dm.KD_EKSELON AS KD_EKSELON_BARU,
                       dm.KD_STATUS_PEGAWAI AS KD_STATUS_PEGAWAI_BARU,
                       dm.KD_JENIS_PEGAWAI AS KD_JENIS_PEGAWAI_BARU,
                       dm.KD_LOKASI AS KD_LOKASI_BARU,
                       dm.NIP_PIMPINAN,
                       dm.NAMA_PIMPINAN,
                       dm.KETERANGAN,
                       COALESCE(p.NAMA, dd.NAMA) AS NAMA_PEGAWAI,
                       b_asal.BAGIAN AS BAGIAN_ASAL,
                       b_baru.BAGIAN AS BAGIAN_BARU,
                       j_asal.JABATAN AS JABATAN_ASAL,
                       j_baru.JABATAN AS JABATAN_BARU,
                       pkt_asal.PANGKAT AS PANGKAT_ASAL,
                       pkt_baru.PANGKAT AS PANGKAT_BARU,
                       g_asal.GOLONGAN AS GOLONGAN_ASAL,
                       g_baru.GOLONGAN AS GOLONGAN_BARU,
                       e_asal.EKSELON AS EKSELON_ASAL,
                       e_baru.EKSELON AS EKSELON_BARU,
                       jp_asal.JENIS_PEGAWAI AS JENIS_PEGAWAI_ASAL,
                       jp_baru.JENIS_PEGAWAI AS JENIS_PEGAWAI_BARU,
                       sp_asal.STATUS_PEGAWAI AS STATUS_PEGAWAI_ASAL,
                       sp_baru.STATUS_PEGAWAI AS STATUS_PEGAWAI_BARU,
                       l_asal.LOKASI AS LOKASI_ASAL,
                       l_baru.LOKASI AS LOKASI_BARU
                FROM detail_demosi_dari_pegawai dd
                LEFT JOIN detail_demosi_menjadi_pegawai dm ON dm.KD_DEMOSI_DARI = dd.KD_DEMOSI_DARI
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = dd.ID_PEGAWAI
                LEFT JOIN bagian b_asal ON b_asal.KD_BAGIAN = dd.KD_BAGIAN
                LEFT JOIN bagian b_baru ON b_baru.KD_BAGIAN = dm.KD_BAGIAN
                LEFT JOIN jabatan j_asal ON j_asal.KD_JABATAN = dd.KD_JABATAN
                LEFT JOIN jabatan j_baru ON j_baru.KD_JABATAN = dm.KD_JABATAN
                LEFT JOIN pangkat pkt_asal ON pkt_asal.KD_PANGKAT = dd.KD_PANGKAT
                LEFT JOIN pangkat pkt_baru ON pkt_baru.KD_PANGKAT = dm.KD_PANGKAT
                LEFT JOIN golongan g_asal ON g_asal.KD_GOLONGAN = dd.KD_GOLONGAN
                LEFT JOIN golongan g_baru ON g_baru.KD_GOLONGAN = dm.KD_GOLONGAN
                LEFT JOIN ekselon e_asal ON e_asal.KD_EKSELON = dd.KD_EKSELON
                LEFT JOIN ekselon e_baru ON e_baru.KD_EKSELON = dm.KD_EKSELON
                LEFT JOIN jenis_pegawai jp_asal ON jp_asal.KD_JENIS_PEGAWAI = dd.KD_JENIS_PEGAWAI
                LEFT JOIN jenis_pegawai jp_baru ON jp_baru.KD_JENIS_PEGAWAI = dm.KD_JENIS_PEGAWAI
                LEFT JOIN status_pegawai sp_asal ON sp_asal.KD_STATUS_PEGAWAI = dd.KD_STATUS_PEGAWAI
                LEFT JOIN status_pegawai sp_baru ON sp_baru.KD_STATUS_PEGAWAI = dm.KD_STATUS_PEGAWAI
                LEFT JOIN lokasi l_asal ON l_asal.KD_LOKASI = dd.KD_LOKASI
                LEFT JOIN lokasi l_baru ON l_baru.KD_LOKASI = dm.KD_LOKASI
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
        $this->db->transBegin();
        try {
            $aliases = [
                'ID_PEGAWAI' => ['replacement_id_pegawai', 'id_pegawai'],
                'NIP' => ['replacement_NIP', 'nip'],
                'NAMA' => ['replacement_nama', 'nama'],
                'KD_DEMOSI_DARI' => ['replacement_kd_demosi_dari', 'kd_demosi_dari'],
                'NO_SURAT' => ['replacement_no_surat', 'no_surat', 'replacement_no_sk', 'no_sk'],
                'TGL_SURAT' => ['replacement_tanggal_surat', 'tgl_surat'],
                'KD_BAGIAN_ASAL' => ['replacement_bagian', 'kd_bagian_asal'],
                'KD_JABATAN_ASAL' => ['replacement_jabatan', 'kd_jabatan_asal'],
                'KD_EKSELON_ASAL' => ['replacement_ekselon', 'kd_ekselon_asal'],
                'KD_STATUS_PEGAWAI_ASAL' => ['replacement_status_pegawai', 'kd_status_pegawai_asal'],
                'KD_GOLONGAN_ASAL' => ['replacement_golongan', 'kd_golongan_asal'],
                'KD_PANGKAT_ASAL' => ['replacement_pangkat', 'kd_pangkat_asal'],
                'KD_JENIS_PEGAWAI_ASAL' => ['replacement_jenis_pegawai', 'kd_jenis_pegawai_asal'],
                'KD_LOKASI_ASAL' => ['replacement_lokasi', 'kd_lokasi_asal'],
                'NIP_PIMPINAN' => ['replacement_NIP_pimpinan', 'nip_pimpinan'],
                'NAMA_PIMPINAN' => ['replacement_nama_pimpinan', 'nama_pimpinan'],
                'TGL_DEMOSI' => ['replacement_tanggal_demosi', 'tgl_demosi'],
                'KD_BAGIAN_BARU' => ['replacement_bagian_menjadi', 'kd_bagian_baru', 'kd_bagian'],
                'KD_JABATAN_BARU' => ['replacement_jabatan_menjadi', 'kd_jabatan_baru', 'kd_jabatan'],
                'KD_EKSELON_BARU' => ['replacement_ekselon_menjadi', 'kd_ekselon_baru', 'kd_ekselon'],
                'KD_STATUS_PEGAWAI_BARU' => ['replacement_status_pegawai_menjadi', 'kd_status_pegawai_baru', 'kd_status_pegawai'],
                'KD_GOLONGAN_BARU' => ['replacement_golongan_menjadi', 'kd_golongan_baru', 'kd_golongan'],
                'KD_PANGKAT_BARU' => ['replacement_pangkat_menjadi', 'kd_pangkat_baru', 'kd_pangkat'],
                'KD_JENIS_PEGAWAI_BARU' => ['replacement_jenis_pegawai_menjadi', 'kd_jenis_pegawai_baru', 'kd_jenis_pegawai'],
                'KD_LOKASI_BARU' => ['replacement_lokasi_menjadi', 'kd_lokasi_baru', 'kd_lokasi'],
                'KETERANGAN' => ['replacement_keterangan', 'keterangan'],
            ];
            foreach ($aliases as $target => $sources) {
                if (!isset($data[$target]) || $data[$target] === '') {
                    foreach ($sources as $s) {
                        if (isset($data[$s]) && $data[$s] !== '') {
                            $data[$target] = $data[$s];
                            break;
                        }
                    }
                }
            }

            $idPegawai = $data['ID_PEGAWAI'] ?? null;
            if (empty($idPegawai) && !empty($data['NIP'])) {
                $pRow = $this->db->table('pegawai')->where('NIP', trim($data['NIP']))->get()->getRowArray();
                if ($pRow) {
                    $idPegawai = $pRow['ID_PEGAWAI'];
                }
            }
            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kdDari = $data['KD_DEMOSI_DARI'] ?? '';
            $isUpdate = !empty($kdDari);

            $tglDemosi = $this->normalizeDate($data['TGL_DEMOSI'] ?? null, date('Y-m-d'));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? null, $tglDemosi);
            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : ('SK-DMS/' . date('Ymd') . '/' . $idPegawai);

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
                'NIP'               => !empty($data['NIP']) ? trim($data['NIP']) : $snapshot['NIP'],
                'NAMA'              => !empty($data['NAMA']) ? trim($data['NAMA']) : $snapshot['NAMA'],
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

            $this->db->transCommit();
            return ['code' => 200, 'message' => 'Data demosi berhasil disimpan'];
        } catch (\Exception $e) {
            $this->db->transRollback();
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
                       j.JABATAN,
                       pkt.PANGKAT,
                       g.GOLONGAN,
                       e.EKSELON,
                       jp.JENIS_PEGAWAI,
                       sp.STATUS_PEGAWAI,
                       l.LOKASI
                FROM detail_catatan_pegawai c
                LEFT JOIN pegawai p ON p.ID_PEGAWAI = c.ID_PEGAWAI
                LEFT JOIN jenis_catatan jc ON jc.KD_JENIS_CATATAN = c.KD_JENIS_CATATAN
                LEFT JOIN bagian b ON b.KD_BAGIAN = c.KD_BAGIAN
                LEFT JOIN jabatan j ON j.KD_JABATAN = c.KD_JABATAN
                LEFT JOIN pangkat pkt ON pkt.KD_PANGKAT = c.KD_PANGKAT
                LEFT JOIN golongan g ON g.KD_GOLONGAN = c.KD_GOLONGAN
                LEFT JOIN ekselon e ON e.KD_EKSELON = c.KD_EKSELON
                LEFT JOIN jenis_pegawai jp ON jp.KD_JENIS_PEGAWAI = c.KD_JENIS_PEGAWAI
                LEFT JOIN status_pegawai sp ON sp.KD_STATUS_PEGAWAI = c.KD_STATUS_PEGAWAI
                LEFT JOIN lokasi l ON l.KD_LOKASI = c.KD_LOKASI
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
            $idPegawai = $data['ID_PEGAWAI'] ?? $data['id_pegawai'] ?? null;
            $nip = $data['NIP'] ?? $data['nip'] ?? null;
            $nama = $data['NAMA'] ?? $data['nama'] ?? '';

            if (empty($idPegawai) && !empty($nip)) {
                $peg = $this->db->table('pegawai')->select('ID_PEGAWAI, NAMA')->where('NIP', $nip)->get()->getRowArray();
                if ($peg) {
                    $idPegawai = $peg['ID_PEGAWAI'];
                    if (empty($nama)) $nama = $peg['NAMA'];
                }
            }

            if (empty($idPegawai)) {
                return ['code' => 422, 'message' => 'Pegawai belum dipilih atau NIP tidak valid'];
            }

            $snapshot = $this->getPegawaiSnapshot($idPegawai);
            $kd = $data['KD_CATATAN'] ?? $data['kd_catatan'] ?? $data['id_catatan'] ?? $data['ID_CATATAN'] ?? $data['id'] ?? '';

            $noSurat = !empty($data['NO_SURAT']) ? trim($data['NO_SURAT']) : (!empty($data['no_surat']) ? trim($data['no_surat']) : (!empty($data['NO_SK']) ? trim($data['NO_SK']) : ('SK-CTT/' . date('Ymd') . '/' . $idPegawai)));
            $tglSurat = $this->normalizeDate($data['TGL_SURAT'] ?? $data['tgl_surat'] ?? $data['TGL_CATATAN'] ?? $data['tgl_catatan'] ?? null, date('Y-m-d'));
            $tglCatatan = $this->normalizeDate($data['TGL_CATATAN'] ?? $data['tgl_catatan'] ?? null, date('Y-m-d'));
            $kdJenis = !empty($data['KD_JENIS_CATATAN']) ? (int)$data['KD_JENIS_CATATAN'] : (!empty($data['id_jenis_catatan']) ? (int)$data['id_jenis_catatan'] : (!empty($data['kd_jenis_catatan']) ? (int)$data['kd_jenis_catatan'] : 1));

            $kdBagian = !empty($data['KD_BAGIAN']) ? (int)$data['KD_BAGIAN'] : (!empty($data['id_bagian']) ? (int)$data['id_bagian'] : (!empty($data['kd_bagian']) ? (int)$data['kd_bagian'] : $snapshot['KD_BAGIAN']));
            $kdJabatan = isset($data['KD_JABATAN']) && $data['KD_JABATAN'] !== '' ? (int)$data['KD_JABATAN'] : (isset($data['id_jabatan']) && $data['id_jabatan'] !== '' ? (int)$data['id_jabatan'] : (isset($data['kd_jabatan']) && $data['kd_jabatan'] !== '' ? (int)$data['kd_jabatan'] : $snapshot['KD_JABATAN']));
            $kdGolongan = !empty($data['KD_GOLONGAN']) ? (int)$data['KD_GOLONGAN'] : (!empty($data['id_golongan']) ? (int)$data['id_golongan'] : (!empty($data['kd_golongan']) ? (int)$data['kd_golongan'] : $snapshot['KD_GOLONGAN']));
            $kdPangkat = !empty($data['KD_PANGKAT']) ? (int)$data['KD_PANGKAT'] : (!empty($data['id_pangkat']) ? (int)$data['id_pangkat'] : (!empty($data['kd_pangkat']) ? (int)$data['kd_pangkat'] : $snapshot['KD_PANGKAT']));
            $kdEkselon = !empty($data['KD_EKSELON']) ? (int)$data['KD_EKSELON'] : (!empty($data['id_eselon']) ? (int)$data['id_eselon'] : (!empty($data['kd_ekselon']) ? (int)$data['kd_ekselon'] : $snapshot['KD_EKSELON']));
            $kdJenisPegawai = !empty($data['KD_JENIS_PEGAWAI']) ? (int)$data['KD_JENIS_PEGAWAI'] : (!empty($data['id_jenis_pegawai']) ? (int)$data['id_jenis_pegawai'] : (!empty($data['kd_jenis_pegawai']) ? (int)$data['kd_jenis_pegawai'] : $snapshot['KD_JENIS_PEGAWAI']));
            $kdStatusPegawai = isset($data['KD_STATUS_PEGAWAI']) && $data['KD_STATUS_PEGAWAI'] !== '' ? (int)$data['KD_STATUS_PEGAWAI'] : (isset($data['id_status_pegawai']) && $data['id_status_pegawai'] !== '' ? (int)$data['id_status_pegawai'] : (isset($data['kd_status_pegawai']) && $data['kd_status_pegawai'] !== '' ? (int)$data['kd_status_pegawai'] : $snapshot['KD_STATUS_PEGAWAI']));
            $kdLokasi = !empty($data['KD_LOKASI']) ? (int)$data['KD_LOKASI'] : (!empty($data['id_lokasi']) ? (int)$data['id_lokasi'] : (!empty($data['kd_lokasi']) ? (int)$data['kd_lokasi'] : $snapshot['KD_LOKASI']));

            $record = [
                'ID_PEGAWAI'        => $idPegawai,
                'NO_SURAT'          => $noSurat,
                'TGL_SURAT'         => $tglSurat,
                'NIP'               => !empty($data['NIP']) ? $data['NIP'] : (!empty($data['nip']) ? $data['nip'] : $snapshot['NIP']),
                'NAMA'              => !empty($nama) ? $nama : $snapshot['NAMA'],
                'KD_BAGIAN'         => $kdBagian,
                'KD_JABATAN'        => $kdJabatan,
                'KD_EKSELON'        => $kdEkselon,
                'KD_STATUS_PEGAWAI' => $kdStatusPegawai,
                'KD_JENIS_CATATAN'  => $kdJenis,
                'KD_GOLONGAN'       => $kdGolongan,
                'KD_PANGKAT'        => $kdPangkat,
                'KD_JENIS_PEGAWAI'  => $kdJenisPegawai,
                'KD_LOKASI'         => $kdLokasi,
                'TGL_CATATAN'       => $tglCatatan,
                'KETERANGAN'        => $data['KETERANGAN'] ?? $data['keterangan'] ?? '-',
                'FILE'              => $data['FILE'] ?? $data['file'] ?? ''
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
