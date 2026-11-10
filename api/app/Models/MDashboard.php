<?php

namespace App\Models;

use CodeIgniter\Model;

class MDashboard extends Model
{
    public function getSummary(): array
    {
        // 1. Total Pegawai Aktif
        $totalPegawai = (int)($this->db->query(
            "SELECT COUNT(*) AS n FROM pegawai WHERE status_aktif = 1"
        )->getRow()->n ?? 0);

        // 2. Pegawai Ulang Tahun (Hari ini & Bulan ini)
        $ultahHariIni = (int)($this->db->query(
            "SELECT COUNT(*) AS n FROM pegawai 
             WHERE status_aktif = 1 
               AND TANGGAL_LAHIR IS NOT NULL 
               AND DATE_FORMAT(TANGGAL_LAHIR, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')"
        )->getRow()->n ?? 0);

        $ultahBulanIni = (int)($this->db->query(
            "SELECT COUNT(*) AS n FROM pegawai 
             WHERE status_aktif = 1 
               AND TANGGAL_LAHIR IS NOT NULL 
               AND MONTH(TANGGAL_LAHIR) = MONTH(CURDATE())"
        )->getRow()->n ?? 0);

        // 3. Pegawai Naik Pangkat / Promosi (3 bulan ke depan)
        $naikPangkatNext = 0;
        if ($this->db->tableExists('detail_promosi_menjadi_pegawai')) {
            $row = $this->db->query(
                "SELECT COUNT(*) AS n FROM pegawai p
                 INNER JOIN detail_promosi_menjadi_pegawai dp ON dp.ID_PEGAWAI = p.ID_PEGAWAI
                 WHERE p.status_aktif = 1 
                   AND dp.TGL_PROMOSI BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)"
            )->getRow();
            $naikPangkatNext = (int)($row->n ?? 0);
        }

        // 4. Pegawai Akan Pensiun (usia >= 57 tahun)
        $akanPensiun = (int)($this->db->query(
            "SELECT COUNT(*) AS n FROM pegawai 
             WHERE status_aktif = 1 
               AND TANGGAL_LAHIR IS NOT NULL 
               AND TIMESTAMPDIFF(YEAR, TANGGAL_LAHIR, CURDATE()) >= 57"
        )->getRow()->n ?? 0);

        // 5. Gender
        $gender = $this->db->query(
            "SELECT 
                SUM(CASE WHEN JENIS_KELAMIN = 1 THEN 1 ELSE 0 END) AS pria,
                SUM(CASE WHEN JENIS_KELAMIN = 2 THEN 1 ELSE 0 END) AS wanita
             FROM pegawai WHERE status_aktif = 1"
        )->getRow();

        // 6. STR & SIP Expiring (dalam 6 bulan ke depan atau sudah lewat)
        $strExpiring = 0;
        if ($this->db->tableExists('detail_str_pegawai')) {
            $r = $this->db->query(
                "SELECT COUNT(*) AS n FROM detail_str_pegawai s
                 INNER JOIN pegawai p ON p.ID_PEGAWAI = s.ID_PEGAWAI AND p.status_aktif = 1
                 WHERE s.SAMPAI IS NOT NULL 
                   AND s.SAMPAI <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)"
            )->getRow();
            $strExpiring = (int)($r->n ?? 0);
        }

        $sipExpiring = 0;
        if ($this->db->tableExists('detail_sip_pegawai')) {
            $r = $this->db->query(
                "SELECT COUNT(*) AS n FROM detail_sip_pegawai s
                 INNER JOIN pegawai p ON p.ID_PEGAWAI = s.ID_PEGAWAI AND p.status_aktif = 1
                 WHERE s.SAMPAI IS NOT NULL 
                   AND s.SAMPAI <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)"
            )->getRow();
            $sipExpiring = (int)($r->n ?? 0);
        }

        return [
            'total_pegawai' => $totalPegawai,
            'ultah_hari_ini' => $ultahHariIni,
            'ultah_bulan_ini' => $ultahBulanIni,
            'naik_pangkat_3bln' => $naikPangkatNext,
            'akan_pensiun' => $akanPensiun,
            'pria' => (int)($gender->pria ?? 0),
            'wanita' => (int)($gender->wanita ?? 0),
            'str_expiring' => $strExpiring,
            'sip_expiring' => $sipExpiring,
        ];
    }

    public function getPegawaiByBagian(): array
    {
        return $this->db->query(
            "SELECT 
                COALESCE(b.BAGIAN, 'Lain-lain') AS unit_nama,
                COALESCE(i.INSTALASI, 'Lain-lain') AS instalasi,
                COUNT(DISTINCT dp.ID_PEGAWAI) AS total
             FROM detail_penempatan_pegawai dp
             INNER JOIN pegawai p ON p.ID_PEGAWAI = dp.ID_PEGAWAI AND p.status_aktif = 1
             LEFT JOIN bagian b ON b.KD_BAGIAN = dp.KD_BAGIAN
             LEFT JOIN instalasi i ON i.KD_INSTALASI = b.KD_INSTALASI
             WHERE b.BAGIAN IS NOT NULL
             GROUP BY b.KD_BAGIAN, b.BAGIAN, i.INSTALASI
             ORDER BY total DESC
             LIMIT 10"
        )->getResultArray();
    }

    public function getPertumbuhanPegawai(): array
    {
        $tahun = date('Y');
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $bulananRaw = $this->db->query(
            "SELECT MONTH(TANGGAL_MASUK) AS bln, COUNT(*) AS total
             FROM pegawai 
             WHERE status_aktif = 1 
               AND TANGGAL_MASUK IS NOT NULL 
               AND YEAR(TANGGAL_MASUK) = ?
             GROUP BY MONTH(TANGGAL_MASUK)
             ORDER BY bln ASC",
            [$tahun]
        )->getResultArray();

        $bulanMap = [];
        foreach ($bulananRaw as $row) {
            $bulanMap[(int)$row['bln']] = (int)$row['total'];
        }

        $bulananFormatted = [];
        for ($m = 1; $m <= 12; $m++) {
            $bulananFormatted[] = [
                'bulan_angka' => $m,
                'bulan_nama' => $namaBulan[$m - 1],
                'total' => $bulanMap[$m] ?? 0
            ];
        }

        // Tren penerimaan tahunan (5 tahun terakhir dengan data)
        $tahunan = $this->db->query(
            "SELECT YEAR(TANGGAL_MASUK) AS thn, COUNT(*) AS total
             FROM pegawai 
             WHERE status_aktif = 1 
               AND TANGGAL_MASUK IS NOT NULL 
               AND YEAR(TANGGAL_MASUK) >= 2018
             GROUP BY YEAR(TANGGAL_MASUK)
             ORDER BY thn ASC"
        )->getResultArray();

        return [
            'tahun_aktif' => $tahun,
            'bulanan' => $bulananFormatted,
            'tahunan' => $tahunan
        ];
    }

    public function getStatusPegawaiDistribution(): array
    {
        return $this->db->query(
            "SELECT 
                CASE 
                    WHEN s.STATUS_PEGAWAI IS NOT NULL AND TRIM(s.STATUS_PEGAWAI) != '' AND TRIM(s.STATUS_PEGAWAI) != '-' 
                    THEN TRIM(s.STATUS_PEGAWAI)
                    ELSE 'Umum / Lainnya' 
                END AS status_nama,
                COUNT(*) AS total
             FROM pegawai p
             LEFT JOIN (
                 SELECT ID_PEGAWAI, KD_STATUS_PEGAWAI
                 FROM detail_penempatan_pegawai
                 WHERE KD_PENEMPATAN IN (
                     SELECT MAX(KD_PENEMPATAN) 
                     FROM detail_penempatan_pegawai 
                     GROUP BY ID_PEGAWAI
                 )
             ) dp ON dp.ID_PEGAWAI = p.ID_PEGAWAI
             LEFT JOIN status_pegawai s ON s.KD_STATUS_PEGAWAI = dp.KD_STATUS_PEGAWAI
             WHERE p.status_aktif = 1
             GROUP BY status_nama
             ORDER BY total DESC"
        )->getResultArray();
    }

    public function getPendidikanDistribution(): array
    {
        return $this->db->query(
            "SELECT 
                COALESCE(j.JENJANG_PENDIDIKAN, 'Lainnya') AS jenjang,
                COUNT(DISTINCT dpen.ID_PEGAWAI) AS total
             FROM detail_pendidikan_pegawai dpen
             INNER JOIN pegawai p ON p.ID_PEGAWAI = dpen.ID_PEGAWAI AND p.status_aktif = 1
             LEFT JOIN jenjang_pendidikan j ON j.KD_JENJANG = dpen.KD_JENJANG
             GROUP BY j.KD_JENJANG, j.JENJANG_PENDIDIKAN
             ORDER BY total DESC
             LIMIT 8"
        )->getResultArray();
    }

    public function getKehadiranSummary(): array
    {
        if (!$this->db->tableExists('att_log')) {
            return ['tersedia' => false, 'pesan' => 'Tabel absensi tidak ditemukan'];
        }

        $lastLog = $this->db->query(
            "SELECT DATE_FORMAT(scan_date, '%Y-%m') AS ym, COUNT(*) AS n 
             FROM att_log 
             GROUP BY ym 
             ORDER BY ym DESC 
             LIMIT 1"
        )->getRow();

        if (!$lastLog) {
            return ['tersedia' => false, 'pesan' => 'Belum ada log absensi'];
        }

        $dailyStats = $this->db->query(
            "SELECT DAY(scan_date) AS hari, COUNT(*) AS total
             FROM att_log
             WHERE DATE_FORMAT(scan_date, '%Y-%m') = ?
             GROUP BY DAY(scan_date)
             ORDER BY hari ASC
             LIMIT 15",
            [$lastLog->ym]
        )->getResultArray();

        return [
            'tersedia' => true,
            'periode' => $lastLog->ym,
            'total_scan' => (int)$lastLog->n,
            'harian' => $dailyStats
        ];
    }

    public function getDetailUltah(bool $bulanIni = true): array
    {
        $whereClause = $bulanIni 
            ? "MONTH(p.TANGGAL_LAHIR) = MONTH(CURDATE())"
            : "DATE_FORMAT(p.TANGGAL_LAHIR, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')";

        return $this->db->query(
            "SELECT 
                p.ID_PEGAWAI, p.NIP, p.NAMA, p.JENIS_KELAMIN,
                DATE_FORMAT(p.TANGGAL_LAHIR, '%d %M %Y') AS TGL_LAHIR_FORMAT,
                TIMESTAMPDIFF(YEAR, p.TANGGAL_LAHIR, CURDATE()) AS UMUR,
                COALESCE(b.BAGIAN, '-') AS BAGIAN
             FROM pegawai p
             LEFT JOIN detail_penempatan_pegawai dp ON dp.ID_PEGAWAI = p.ID_PEGAWAI
             LEFT JOIN bagian b ON b.KD_BAGIAN = dp.KD_BAGIAN
             WHERE p.status_aktif = 1 
               AND p.TANGGAL_LAHIR IS NOT NULL
               AND $whereClause
             GROUP BY p.ID_PEGAWAI, p.NIP, p.NAMA, p.JENIS_KELAMIN, p.TANGGAL_LAHIR, b.BAGIAN
             ORDER BY DATE_FORMAT(p.TANGGAL_LAHIR, '%m-%d') ASC"
        )->getResultArray();
    }

    public function getDetailPensiun(): array
    {
        return $this->db->query(
            "SELECT 
                p.ID_PEGAWAI, p.NIP, p.NAMA, p.JENIS_KELAMIN,
                DATE_FORMAT(p.TANGGAL_LAHIR, '%d %M %Y') AS TGL_LAHIR_FORMAT,
                TIMESTAMPDIFF(YEAR, p.TANGGAL_LAHIR, CURDATE()) AS UMUR,
                COALESCE(b.BAGIAN, '-') AS BAGIAN,
                COALESCE(s.STATUS_PEGAWAI, '-') AS STATUS_PEGAWAI
             FROM pegawai p
             LEFT JOIN detail_penempatan_pegawai dp ON dp.ID_PEGAWAI = p.ID_PEGAWAI
             LEFT JOIN bagian b ON b.KD_BAGIAN = dp.KD_BAGIAN
             LEFT JOIN status_pegawai s ON s.KD_STATUS_PEGAWAI = dp.KD_STATUS_PEGAWAI
             WHERE p.status_aktif = 1 
               AND p.TANGGAL_LAHIR IS NOT NULL
               AND TIMESTAMPDIFF(YEAR, p.TANGGAL_LAHIR, CURDATE()) >= 56
             GROUP BY p.ID_PEGAWAI, p.NIP, p.NAMA, p.JENIS_KELAMIN, p.TANGGAL_LAHIR, b.BAGIAN, s.STATUS_PEGAWAI
             ORDER BY UMUR DESC, p.TANGGAL_LAHIR ASC"
        )->getResultArray();
    }

    public function getDetailStrSipExpiring(): array
    {
        $strList = [];
        if ($this->db->tableExists('detail_str_pegawai')) {
            $strList = $this->db->query(
                "SELECT 
                    s.NO_STR, s.NAMA_STR, s.SAMPAI,
                    DATE_FORMAT(s.SAMPAI, '%d-%m-%Y') AS SAMPAI_FORMAT,
                    p.ID_PEGAWAI, p.NIP, p.NAMA,
                    DATEDIFF(s.SAMPAI, CURDATE()) AS SISA_HARI
                 FROM detail_str_pegawai s
                 INNER JOIN pegawai p ON p.ID_PEGAWAI = s.ID_PEGAWAI AND p.status_aktif = 1
                 WHERE s.SAMPAI IS NOT NULL 
                   AND s.SAMPAI <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
                 ORDER BY s.SAMPAI ASC
                 LIMIT 20"
            )->getResultArray();
        }

        $sipList = [];
        if ($this->db->tableExists('detail_sip_pegawai')) {
            $sipList = $this->db->query(
                "SELECT 
                    s.NO_SIP, s.NAMA_SIP, s.SAMPAI,
                    DATE_FORMAT(s.SAMPAI, '%d-%m-%Y') AS SAMPAI_FORMAT,
                    p.ID_PEGAWAI, p.NIP, p.NAMA,
                    DATEDIFF(s.SAMPAI, CURDATE()) AS SISA_HARI
                 FROM detail_sip_pegawai s
                 INNER JOIN pegawai p ON p.ID_PEGAWAI = s.ID_PEGAWAI AND p.status_aktif = 1
                 WHERE s.SAMPAI IS NOT NULL 
                   AND s.SAMPAI <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
                 ORDER BY s.SAMPAI ASC
                 LIMIT 20"
            )->getResultArray();
        }

        return [
            'str' => $strList,
            'sip' => $sipList
        ];
    }
}
