<?php

namespace App\Models;

use CodeIgniter\Model;

class MAbsensi extends Model
{
    /**
     * Get shift configurations from standarisasi
     */
    public function getMasterShift()
    {
        return $this->db->table('standarisasi')
            ->select('KD_STANDARISASI, STANDARISASI as nama_shift, PENDEK as kode_shift, JAM_MASUK, JAM_PULANG, TOLERANSI, STATUS_LIBUR')
            ->orderBy('KD_STANDARISASI', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Get list of active departments (bagian)
     */
    public function getBagianList()
    {
        return $this->db->table('bagian')
            ->select('KD_BAGIAN, BAGIAN')
            ->where('STATUS_AKTIF', '1')
            ->orderBy('BAGIAN', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Monthly recapitulation matrix (1..31 days)
     */
    public function getRekapBulanan(string $bulan, ?string $kdBagian = null, ?string $idPegawai = null)
    {
        $daysInMonth = (int) date('t', strtotime($bulan . '-01'));
        $startDate = $bulan . '-01';
        $endDate = $bulan . '-' . str_pad((string) $daysInMonth, 2, '0', STR_PAD_LEFT);

        $builder = $this->db->table('t_perencanaan_harikerja tph')
            ->select('p.ID_PEGAWAI, p.NIP, p.NAMA, b.KD_BAGIAN, b.BAGIAN,
                     tph.tanggal_jadwal, tph.kd_standarisasi, s.PENDEK as kode_shift, s.STANDARISASI as nama_shift,
                     s.JAM_MASUK, s.JAM_PULANG, s.TOLERANSI, s.STATUS_LIBUR,
                     tph.jam_datang, tph.jam_keluar')
            ->join('pegawai p', 'tph.id_pegawai = p.ID_PEGAWAI', 'inner')
            ->join('bagian b', 'tph.kd_bagian = b.KD_BAGIAN', 'left')
            ->join('standarisasi s', 'tph.kd_standarisasi = s.KD_STANDARISASI', 'left')
            ->where('tph.tanggal_jadwal >=', $startDate)
            ->where('tph.tanggal_jadwal <=', $endDate);

        if (!empty($kdBagian)) {
            $builder->where('tph.kd_bagian', $kdBagian);
        }
        if (!empty($idPegawai)) {
            $builder->where('tph.id_pegawai', $idPegawai);
        }

        $records = $builder->orderBy('b.BAGIAN, p.NAMA, tph.tanggal_jadwal', 'ASC')->get()->getResultArray();

        $pegawaiMap = [];
        $totalHadirBulan = 0;
        $totalTerlambatBulan = 0;

        foreach ($records as $row) {
            $idPeg = $row['ID_PEGAWAI'];
            if (!isset($pegawaiMap[$idPeg])) {
                $pegawaiMap[$idPeg] = [
                    'id_pegawai' => $idPeg,
                    'nip' => $row['NIP'],
                    'nama' => $row['NAMA'],
                    'kd_bagian' => $row['KD_BAGIAN'],
                    'bagian' => $row['BAGIAN'] ?? '-',
                    'total_hadir' => 0,
                    'total_terlambat' => 0,
                    'total_libur' => 0,
                    'total_izin_cuti' => 0,
                    'total_alpa' => 0,
                    'total_menit_terlambat' => 0,
                    'hari' => []
                ];
            }

            $dayNum = (int) date('j', strtotime($row['tanggal_jadwal']));
            $isLibur = ((int) $row['STATUS_LIBUR'] === 1 || $row['kd_standarisasi'] == '20');
            $hasDatang = (!empty($row['jam_datang']) && $row['jam_datang'] > '2000-01-01');

            $status = 'Alpa';
            $badgeColor = 'danger';
            $menitTerlambat = 0;

            if ($isLibur) {
                $status = 'Libur';
                $badgeColor = 'secondary';
                $pegawaiMap[$idPeg]['total_libur']++;
            } elseif ($hasDatang) {
                $status = 'Hadir';
                $badgeColor = 'success';
                $pegawaiMap[$idPeg]['total_hadir']++;
                $totalHadirBulan++;

                // Calculate punctuality
                $scanTime = date('H:i:s', strtotime($row['jam_datang']));
                $scheduledIn = $row['JAM_MASUK'];
                $tolerance = $row['TOLERANSI'] ?? '00:00:00';
                $toleratedTime = date('H:i:s', strtotime($scheduledIn) + strtotime($tolerance) - strtotime('00:00:00'));

                if ($scanTime > $toleratedTime && $scheduledIn != '00:00:00') {
                    $diffSec = strtotime($scanTime) - strtotime($scheduledIn);
                    $menitTerlambat = max(0, (int) round($diffSec / 60));
                    $pegawaiMap[$idPeg]['total_terlambat']++;
                    $pegawaiMap[$idPeg]['total_menit_terlambat'] += $menitTerlambat;
                    $totalTerlambatBulan++;
                    $badgeColor = ($menitTerlambat > 15) ? 'danger' : 'warning';
                    $status = 'Terlambat (' . $menitTerlambat . 'm)';
                }
            } elseif (in_array($row['kd_standarisasi'], ['7', '8', '9', '10', '11', '12'])) {
                $status = $row['nama_shift'] ?? 'Izin/Cuti';
                $badgeColor = 'info';
                $pegawaiMap[$idPeg]['total_izin_cuti']++;
            } else {
                $status = 'Alpa';
                $badgeColor = 'danger';
                $pegawaiMap[$idPeg]['total_alpa']++;
            }

            $pegawaiMap[$idPeg]['hari'][$dayNum] = [
                'tanggal' => $row['tanggal_jadwal'],
                'kd_standarisasi' => $row['kd_standarisasi'],
                'kode_shift' => $row['kode_shift'] ?? $row['nama_shift'],
                'nama_shift' => $row['nama_shift'],
                'jam_masuk' => $row['JAM_MASUK'],
                'jam_pulang' => $row['JAM_PULANG'],
                'jam_datang' => $hasDatang ? date('H:i', strtotime($row['jam_datang'])) : '-',
                'jam_keluar' => (!empty($row['jam_keluar']) && $row['jam_keluar'] > '2000-01-01') ? date('H:i', strtotime($row['jam_keluar'])) : '-',
                'status' => $status,
                'badge_color' => $badgeColor,
                'menit_terlambat' => $menitTerlambat
            ];
        }

        return [
            'bulan' => $bulan,
            'days_in_month' => $daysInMonth,
            'total_pegawai' => count($pegawaiMap),
            'total_hadir' => $totalHadirBulan,
            'total_terlambat' => $totalTerlambatBulan,
            'data' => array_values($pegawaiMap)
        ];
    }

    /**
     * Daily / range attendance status detail
     */
    public function getStatusRekap(string $tglMulai, string $tglSampai, ?string $kdBagian = null, ?string $status = null, ?string $search = null, int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('t_perencanaan_harikerja tph')
            ->select('tph.kd_jadwal, tph.tanggal_jadwal, p.ID_PEGAWAI, p.NIP, p.NAMA, b.BAGIAN,
                     s.KD_STANDARISASI, s.STANDARISASI as nama_shift, s.PENDEK as kode_shift,
                     s.JAM_MASUK, s.JAM_PULANG, s.TOLERANSI, s.STATUS_LIBUR,
                     tph.jam_datang, tph.jam_keluar')
            ->join('pegawai p', 'tph.id_pegawai = p.ID_PEGAWAI', 'inner')
            ->join('bagian b', 'tph.kd_bagian = b.KD_BAGIAN', 'left')
            ->join('standarisasi s', 'tph.kd_standarisasi = s.KD_STANDARISASI', 'left')
            ->where('tph.tanggal_jadwal >=', $tglMulai)
            ->where('tph.tanggal_jadwal <=', $tglSampai);

        if (!empty($kdBagian)) {
            $builder->where('tph.kd_bagian', $kdBagian);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('p.NAMA', $search)
                ->orLike('p.NIP', $search)
                ->orLike('b.BAGIAN', $search)
                ->groupEnd();
        }

        // Clone builder for total count
        $countBuilder = clone $builder;
        $totalRows = $countBuilder->countAllResults();

        $rows = $builder->orderBy('tph.tanggal_jadwal DESC, p.NAMA ASC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $r) {
            $isLibur = ((int) $r['STATUS_LIBUR'] === 1 || $r['KD_STANDARISASI'] == '20');
            $hasDatang = (!empty($r['jam_datang']) && $r['jam_datang'] > '2000-01-01');
            $hasKeluar = (!empty($r['jam_keluar']) && $r['jam_keluar'] > '2000-01-01');

            $statusText = 'Alpa';
            $badgeColor = 'danger';
            $menitTerlambat = 0;
            $durasiKerja = '-';

            if ($isLibur) {
                $statusText = 'Libur';
                $badgeColor = 'secondary';
            } elseif ($hasDatang) {
                $statusText = 'Hadir';
                $badgeColor = 'success';

                $scanTime = date('H:i:s', strtotime($r['jam_datang']));
                $schedIn = $r['JAM_MASUK'];
                $tolerance = $r['TOLERANSI'] ?? '00:00:00';
                $toleratedTime = date('H:i:s', strtotime($schedIn) + strtotime($tolerance) - strtotime('00:00:00'));

                if ($scanTime > $toleratedTime && $schedIn != '00:00:00') {
                    $diffSec = strtotime($scanTime) - strtotime($schedIn);
                    $menitTerlambat = max(0, (int) round($diffSec / 60));
                    $statusText = 'Terlambat';
                    $badgeColor = ($menitTerlambat > 15) ? 'danger' : 'warning';
                }

                if ($hasKeluar) {
                    $secDiff = strtotime($r['jam_keluar']) - strtotime($r['jam_datang']);
                    if ($secDiff > 0) {
                        $hours = floor($secDiff / 3600);
                        $mins = floor(($secDiff % 3600) / 60);
                        $durasiKerja = "{$hours}j {$mins}m";
                    }
                }
            } elseif ($r['KD_STANDARISASI'] == '7') {
                $statusText = 'Cuti';
                $badgeColor = 'info';
            } elseif ($r['KD_STANDARISASI'] == '8') {
                $statusText = 'Izin';
                $badgeColor = 'info';
            } elseif ($r['KD_STANDARISASI'] == '9') {
                $statusText = 'Sakit';
                $badgeColor = 'warning';
            } elseif ($r['KD_STANDARISASI'] == '10') {
                $statusText = 'Dinas Luar';
                $badgeColor = 'primary';
            }

            // Status filter check if passed
            if (!empty($status) && strcasecmp($statusText, $status) !== 0) {
                continue;
            }

            $result[] = [
                'kd_jadwal' => $r['kd_jadwal'],
                'tanggal' => $r['tanggal_jadwal'],
                'id_pegawai' => $r['ID_PEGAWAI'],
                'nip' => $r['NIP'],
                'nama' => $r['NAMA'],
                'bagian' => $r['BAGIAN'] ?? '-',
                'nama_shift' => $r['nama_shift'] ?? '-',
                'kode_shift' => $r['kode_shift'] ?? '-',
                'jadwal_masuk' => $r['JAM_MASUK'],
                'jadwal_pulang' => $r['JAM_PULANG'],
                'jam_datang' => $hasDatang ? date('H:i:s', strtotime($r['jam_datang'])) : '-',
                'jam_keluar' => $hasKeluar ? date('H:i:s', strtotime($r['jam_keluar'])) : '-',
                'durasi_kerja' => $durasiKerja,
                'status' => $statusText,
                'badge_color' => $badgeColor,
                'menit_terlambat' => $menitTerlambat
            ];
        }

        return [
            'total' => $totalRows,
            'limit' => $limit,
            'offset' => $offset,
            'data' => $result
        ];
    }

    /**
     * Aggregate status summary counts
     */
    public function getStatusSummary(string $tglMulai, string $tglSampai, ?string $kdBagian = null)
    {
        $builder = $this->db->table('t_perencanaan_harikerja tph')
            ->select('COUNT(tph.kd_jadwal) as total_jadwal,
                     SUM(CASE WHEN tph.jam_datang IS NOT NULL AND tph.jam_datang > "2000-01-01" THEN 1 ELSE 0 END) as total_hadir,
                     SUM(CASE WHEN s.STATUS_LIBUR = 1 OR tph.kd_standarisasi = "20" THEN 1 ELSE 0 END) as total_libur,
                     SUM(CASE WHEN tph.kd_standarisasi = "7" THEN 1 ELSE 0 END) as total_cuti,
                     SUM(CASE WHEN tph.kd_standarisasi = "8" THEN 1 ELSE 0 END) as total_izin,
                     SUM(CASE WHEN tph.kd_standarisasi = "9" THEN 1 ELSE 0 END) as total_sakit,
                     SUM(CASE WHEN tph.kd_standarisasi = "10" THEN 1 ELSE 0 END) as total_dinas_luar')
            ->join('standarisasi s', 'tph.kd_standarisasi = s.KD_STANDARISASI', 'left')
            ->where('tph.tanggal_jadwal >=', $tglMulai)
            ->where('tph.tanggal_jadwal <=', $tglSampai);

        if (!empty($kdBagian)) {
            $builder->where('tph.kd_bagian', $kdBagian);
        }

        $row = $builder->get()->getRowArray();
        $totalHadir = (int) ($row['total_hadir'] ?? 0);
        $totalLibur = (int) ($row['total_libur'] ?? 0);
        $totalCuti = (int) ($row['total_cuti'] ?? 0);
        $totalIzin = (int) ($row['total_izin'] ?? 0);
        $totalSakit = (int) ($row['total_sakit'] ?? 0);
        $totalDinasLuar = (int) ($row['total_dinas_luar'] ?? 0);
        $totalJadwal = (int) ($row['total_jadwal'] ?? 0);
        $totalAlpa = max(0, $totalJadwal - ($totalHadir + $totalLibur + $totalCuti + $totalIzin + $totalSakit + $totalDinasLuar));

        // Calculate late punches
        $lateBuilder = $this->db->table('t_perencanaan_harikerja tph')
            ->join('standarisasi s', 'tph.kd_standarisasi = s.KD_STANDARISASI', 'inner')
            ->where('tph.tanggal_jadwal >=', $tglMulai)
            ->where('tph.tanggal_jadwal <=', $tglSampai)
            ->where('tph.jam_datang IS NOT NULL')
            ->where('tph.jam_datang >', '2000-01-01')
            ->where('TIME(tph.jam_datang) > ADDTIME(s.JAM_MASUK, COALESCE(s.TOLERANSI, "00:00:00"))');

        if (!empty($kdBagian)) {
            $lateBuilder->where('tph.kd_bagian', $kdBagian);
        }
        $totalTerlambat = $lateBuilder->countAllResults();

        return [
            'total_jadwal' => $totalJadwal,
            'total_hadir' => $totalHadir,
            'total_terlambat' => $totalTerlambat,
            'total_libur' => $totalLibur,
            'total_cuti' => $totalCuti,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_dinas_luar' => $totalDinasLuar,
            'total_alpa' => $totalAlpa
        ];
    }

    /**
     * Scheduling monitor progress by department
     */
    public function getJadwalMonitoring(string $bulan)
    {
        $sql = "SELECT b.KD_BAGIAN, b.BAGIAN,
                       COUNT(DISTINCT peg_unit.ID_PEGAWAI) as total_pegawai,
                       COUNT(DISTINCT tph.id_pegawai) as pegawai_terjadwal,
                       COUNT(tph.kd_jadwal) as total_jadwal,
                       CASE
                           WHEN COUNT(DISTINCT tph.id_pegawai) = 0 THEN 'BELUM TERJADWAL'
                           WHEN COUNT(DISTINCT tph.id_pegawai) < COUNT(DISTINCT peg_unit.ID_PEGAWAI) THEN 'SEBAGIAN TERJADWAL'
                           ELSE 'TERJADWAL LENGKAP'
                       END as status_keterangan,
                       CASE
                           WHEN COUNT(DISTINCT tph.id_pegawai) = 0 THEN 'danger'
                           WHEN COUNT(DISTINCT tph.id_pegawai) < COUNT(DISTINCT peg_unit.ID_PEGAWAI) THEN 'warning'
                           ELSE 'success'
                       END as status_color
                FROM bagian b
                LEFT JOIN (
                    SELECT dpp.KD_BAGIAN, dpp.ID_PEGAWAI
                    FROM detail_penempatan_pegawai dpp
                    INNER JOIN (
                        SELECT ID_PEGAWAI, MAX(KD_PENEMPATAN) as max_penempatan
                        FROM detail_penempatan_pegawai
                        GROUP BY ID_PEGAWAI
                    ) max_dpp ON dpp.KD_PENEMPATAN = max_dpp.max_penempatan
                ) peg_unit ON b.KD_BAGIAN = peg_unit.KD_BAGIAN
                LEFT JOIN t_perencanaan_harikerja tph ON peg_unit.ID_PEGAWAI = tph.id_pegawai AND tph.tanggal_jadwal LIKE ?
                GROUP BY b.KD_BAGIAN, b.BAGIAN
                ORDER BY b.BAGIAN ASC";

        return $this->db->query($sql, [$bulan . '%'])->getResultArray();
    }

    /**
     * Shift swap applications & approval status
     */
    public function getTukarShift(?string $kdBagian = null, ?string $status = null, int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('approval_tukar_shift ats')
            ->select('ats.*, b.BAGIAN as nama_bagian, bp.BAGIAN as nama_bagian_pengganti')
            ->join('bagian b', 'ats.KD_BAGIAN = b.KD_BAGIAN', 'left')
            ->join('bagian bp', 'ats.KD_BAGIAN_PENGGANTI = bp.KD_BAGIAN', 'left');

        if (!empty($kdBagian)) {
            $builder->groupStart()
                ->where('ats.KD_BAGIAN', $kdBagian)
                ->orWhere('ats.KD_BAGIAN_PENGGANTI', $kdBagian)
                ->groupEnd();
        }
        if ($status !== null && $status !== '') {
            $builder->where('ats.STATUS', $status);
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $rows = $builder->orderBy('ats.ID_APPROVAL', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        return [
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'data' => $rows
        ];
    }

    /**
     * Overtime requests & status
     */
    public function getLembur(?string $bulan = null, ?string $kdBagian = null, ?string $status = null, int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('t_lembur tl')
            ->select('tl.id_lembur, tl.id_pegawai, COALESCE(NULLIF(tl.nama, ""), p.NAMA) as nama, p.NIP,
                     tl.tanggal_lembur, tl.jam_mulai, tl.jam_selesai, tl.keterangan, tl.status,
                     tl.progress_approval, tl.max_approval, tl.file_berkas, tl.jenis_hari,
                     COALESCE(b.BAGIAN, b_sub.BAGIAN) as nama_bagian')
            ->join('pegawai p', 'tl.id_pegawai = p.ID_PEGAWAI', 'left')
            ->join('bagian b', 'tl.kd_bagian = b.KD_BAGIAN', 'left')
            ->join('bagian b_sub', 'tl.kd_subbagian = b_sub.KD_BAGIAN', 'left');

        if (!empty($bulan)) {
            $builder->like('tl.tanggal_lembur', $bulan, 'after');
        }
        if (!empty($kdBagian)) {
            $builder->groupStart()
                ->where('tl.kd_bagian', $kdBagian)
                ->orWhere('tl.kd_subbagian', $kdBagian)
                ->groupEnd();
        }
        if ($status !== null && $status !== '') {
            $builder->where('tl.status', $status);
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $rows = $builder->orderBy('tl.id_lembur', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        return [
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'data' => $rows
        ];
    }

    /**
     * Morning assembly attendance events
     */
    public function getApel(?string $tglMulai = null, ?string $tglSampai = null)
    {
        $builder = $this->db->table('tb_jenis_apel tja')
            ->select('tja.*');

        if (!empty($tglMulai)) {
            $builder->where('tja.tgl_apel >=', $tglMulai);
        }
        if (!empty($tglSampai)) {
            $builder->where('tja.tgl_apel <=', $tglSampai . ' 23:59:59');
        }

        return $builder->orderBy('tja.tgl_apel', 'DESC')->get()->getResultArray();
    }

    /**
     * Raw biometric audit logs from att_log
     */
    public function getLogMesin(?string $tanggal = null, ?string $pin = null, ?string $sn = null, int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('att_log att')
            ->select('att.att_id, att.sn, att.scan_date, att.pin, att.verify_mode, att.io_mode,
                     p.NIP, p.NAMA')
            ->join('pegawai p', 'att.pin = p.ID_PEGAWAI', 'left');

        if (!empty($tanggal)) {
            $builder->like('att.scan_date', $tanggal, 'after');
        }
        if (!empty($pin)) {
            $builder->where('att.pin', $pin);
        }
        if (!empty($sn)) {
            $builder->where('att.sn', $sn);
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $rows = $builder->orderBy('att.scan_date', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        return [
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'data' => $rows
        ];
    }

    /**
     * All biometric scans for one employee on a single day
     */
    public function getLogDetail(string $pin, string $tanggal)
    {
        return $this->db->table('att_log att')
            ->select('att.att_id, att.sn, att.scan_date, att.pin, att.verify_mode, att.io_mode')
            ->where('att.pin', $pin)
            ->like('att.scan_date', $tanggal, 'after')
            ->orderBy('att.scan_date', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Rekapitulasi Data Cuti Pegawai (Tahunan / Bulanan)
     */
    public function getRekapCuti($tahun, $bulan = null, $kdBagian = null)
    {
        $builder = $this->db->table('detail_cuti_pegawai d')
            ->select('d.KD_CUTI, d.NIP, d.NAMA, d.KD_BAGIAN, COALESCE(b.BAGIAN, "-") AS BAGIAN, jc.JENIS_CUTI, d.TANGGAL_MULAI, d.TANGGAL_SAMPAI, d.KEPERLUAN, d.STATUS')
            ->join('jenis_cuti jc', 'jc.KD_JENIS_CUTI = d.KD_JENIS_CUTI', 'left')
            ->join('bagian b', 'b.KD_BAGIAN = d.KD_BAGIAN', 'left')
            ->where('jc.KELOMPOK_IJIN', 0);

        if (!empty($tahun)) {
            $builder->where('YEAR(d.TANGGAL_MULAI)', (int)$tahun);
        }
        if (!empty($bulan) && $bulan !== 'all' && $bulan !== '-') {
            $builder->where('MONTH(d.TANGGAL_MULAI)', (int)$bulan);
        }
        if (!empty($kdBagian) && $kdBagian !== 'all' && $kdBagian !== '-') {
            $builder->where('d.KD_BAGIAN', $kdBagian);
        }

        $rows = $builder->orderBy('d.TANGGAL_MULAI', 'ASC')->get()->getResultArray();

        foreach ($rows as &$r) {
            $tglMulai = $r['TANGGAL_MULAI'];
            $tglSampai = $r['TANGGAL_SAMPAI'];
            if (empty($tglSampai) || $tglSampai === '0000-00-00' || $tglSampai === $tglMulai) {
                $r['JUMLAH_HARI'] = 1;
            } else {
                try {
                    $d1 = new \DateTime($tglMulai);
                    $d2 = new \DateTime($tglSampai);
                    $diff = $d1->diff($d2);
                    $r['JUMLAH_HARI'] = ($diff->days + 1);
                } catch (\Exception $e) {
                    $r['JUMLAH_HARI'] = 1;
                }
            }
            if ($r['STATUS'] == '1') {
                $r['STATUS_LABEL'] = 'Disetujui';
            } elseif ($r['STATUS'] == '2') {
                $r['STATUS_LABEL'] = 'Ditolak';
            } else {
                $r['STATUS_LABEL'] = 'Menunggu';
            }
        }
        unset($r);

        return $rows;
    }

    /**
     * Rekapitulasi Data Ijin Pegawai (Tahunan / Bulanan)
     */
    public function getRekapIjin($tahun, $bulan = null, $kdBagian = null)
    {
        $builder = $this->db->table('detail_cuti_pegawai d')
            ->select('d.KD_CUTI, d.NIP, d.NAMA, d.KD_BAGIAN, COALESCE(b.BAGIAN, "-") AS BAGIAN, jc.JENIS_CUTI, d.TANGGAL_MULAI, d.TANGGAL_SAMPAI, d.KEPERLUAN, d.STATUS')
            ->join('jenis_cuti jc', 'jc.KD_JENIS_CUTI = d.KD_JENIS_CUTI', 'left')
            ->join('bagian b', 'b.KD_BAGIAN = d.KD_BAGIAN', 'left')
            ->where('jc.KELOMPOK_IJIN', 1);

        if (!empty($tahun)) {
            $builder->where('YEAR(d.TANGGAL_MULAI)', (int)$tahun);
        }
        if (!empty($bulan) && $bulan !== 'all' && $bulan !== '-') {
            $builder->where('MONTH(d.TANGGAL_MULAI)', (int)$bulan);
        }
        if (!empty($kdBagian) && $kdBagian !== 'all' && $kdBagian !== '-') {
            $builder->where('d.KD_BAGIAN', $kdBagian);
        }

        $rows = $builder->orderBy('d.TANGGAL_MULAI', 'ASC')->get()->getResultArray();

        foreach ($rows as &$r) {
            $tglMulai = $r['TANGGAL_MULAI'];
            $tglSampai = $r['TANGGAL_SAMPAI'];
            if (empty($tglSampai) || $tglSampai === '0000-00-00' || $tglSampai === $tglMulai) {
                $r['JUMLAH_HARI'] = 1;
            } else {
                try {
                    $d1 = new \DateTime($tglMulai);
                    $d2 = new \DateTime($tglSampai);
                    $diff = $d1->diff($d2);
                    $r['JUMLAH_HARI'] = ($diff->days + 1);
                } catch (\Exception $e) {
                    $r['JUMLAH_HARI'] = 1;
                }
            }
            if ($r['STATUS'] == '1') {
                $r['STATUS_LABEL'] = 'Disetujui';
            } elseif ($r['STATUS'] == '2') {
                $r['STATUS_LABEL'] = 'Ditolak';
            } else {
                $r['STATUS_LABEL'] = 'Menunggu';
            }
        }
        unset($r);

        return $rows;
    }

    /**
     * Rekapitulasi Data Lembur Pegawai
     */
    public function getRekapLembur($tahun, $bulan = null, $kdBagian = null)
    {
        $builder = $this->db->table('t_lembur tl')
            ->select('tl.id_lembur, tl.id_pegawai, COALESCE(NULLIF(tl.nama, ""), p.NAMA, "-") AS NAMA, COALESCE(p.NIP, "-") AS NIP, tl.kd_bagian, COALESCE(b.BAGIAN, b_sub.BAGIAN, "-") AS BAGIAN, tl.tanggal_lembur, tl.jam_mulai, tl.jam_selesai, tl.keterangan, tl.status, tl.jenis_hari')
            ->join('pegawai p', 'tl.id_pegawai = p.ID_PEGAWAI', 'left')
            ->join('bagian b', 'tl.kd_bagian = b.KD_BAGIAN', 'left')
            ->join('bagian b_sub', 'tl.kd_subbagian = b_sub.KD_BAGIAN', 'left');

        if (!empty($tahun)) {
            $builder->where('YEAR(tl.tanggal_lembur)', (int)$tahun);
        }
        if (!empty($bulan) && $bulan !== 'all' && $bulan !== '-') {
            $builder->where('MONTH(tl.tanggal_lembur)', (int)$bulan);
        }
        if (!empty($kdBagian) && $kdBagian !== 'all' && $kdBagian !== '-') {
            $builder->where('tl.kd_bagian', $kdBagian);
        }

        $rows = $builder->orderBy('tl.tanggal_lembur', 'ASC')->get()->getResultArray();

        foreach ($rows as &$r) {
            $mulai = $r['jam_mulai'] ?? '';
            $selesai = $r['jam_selesai'] ?? '';
            $durasiStr = '-';
            $durasiJam = 0;
            if (!empty($mulai) && !empty($selesai)) {
                $t1 = strtotime($mulai);
                $t2 = strtotime($selesai);
                if ($t1 && $t2) {
                    $diff = $t2 - $t1;
                    if ($diff < 0) $diff += 86400;
                    $h = floor($diff / 3600);
                    $m = floor(($diff % 3600) / 60);
                    $durasiStr = "{$h} Jam" . ($m > 0 ? " {$m} Mnt" : "");
                    $durasiJam = round($diff / 3600, 2);
                }
            }
            $r['DURASI'] = $durasiStr;
            $r['DURASI_JAM'] = $durasiJam;

            if ($r['jenis_hari'] == '1') {
                $r['JENIS_HARI_LABEL'] = 'Hari Kerja';
            } elseif ($r['jenis_hari'] == '2') {
                $r['JENIS_HARI_LABEL'] = 'Hari Libur';
            } else {
                $r['JENIS_HARI_LABEL'] = '-';
            }

            if ($r['status'] == '1') {
                $r['STATUS_LABEL'] = 'Disetujui';
            } elseif ($r['status'] == '2') {
                $r['STATUS_LABEL'] = 'Ditolak';
            } else {
                $r['STATUS_LABEL'] = 'Menunggu';
            }
        }
        unset($r);

        return $rows;
    }
}
