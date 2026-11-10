<?php

namespace App\Models;

use CodeIgniter\Model;

class MPayroll extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get Bagian list for dropdowns
     */
    public function getBagianList()
    {
        return $this->db->table('bagian')
            ->select('KD_BAGIAN, BAGIAN')
            ->orderBy('BAGIAN', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Get Jabatan list for dropdowns
     */
    public function getJabatanList()
    {
        return $this->db->table('jabatan')
            ->select('KD_JABATAN, JABATAN')
            ->orderBy('JABATAN', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Get Pegawai list
     */
    public function getPegawaiList(?string $kdBagian = null)
    {
        $builder = $this->db->table('pegawai p')
            ->select('p.ID_PEGAWAI, p.NIP, p.NAMA, dp.KD_BAGIAN, dp.KD_JABATAN, b.BAGIAN, j.JABATAN')
            ->join('(SELECT * FROM detail_penempatan_pegawai WHERE KD_PENEMPATAN IN (SELECT MAX(KD_PENEMPATAN) FROM detail_penempatan_pegawai GROUP BY ID_PEGAWAI)) dp', 'p.ID_PEGAWAI = dp.ID_PEGAWAI', 'left')
            ->join('bagian b', 'dp.KD_BAGIAN = b.KD_BAGIAN', 'left')
            ->join('jabatan j', 'dp.KD_JABATAN = j.KD_JABATAN', 'left');

        if (!empty($kdBagian)) {
            $builder->where('dp.KD_BAGIAN', $kdBagian);
        }

        return $builder->orderBy('p.NAMA', 'ASC')->get()->getResultArray();
    }

    /**
     * 1. GET PAYROLL LIST with filters, pagination, and KPI totals
     */
    public function getPayrollList(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('detail_gaji dg')
            ->select('dg.*, p.NAMA as nama_pegawai, p.NIP, b.BAGIAN as nama_bagian, j.JABATAN as nama_jabatan,
                      (COALESCE(dg.gaji_pokok, 0) + COALESCE(dg.tunjangan_bpjs, 0) + COALESCE(dg.tunjangan_jht, 0) + COALESCE(dg.tunjangan_jkk, 0) + COALESCE(dg.tunjangan_jkm, 0) + COALESCE(dg.lembur, 0) + COALESCE(dg.honor, 0)) as total_bruto,
                      (COALESCE(dg.potongan_bpjs, 0) + COALESCE(dg.potongan_jht, 0)) as total_potongan,
                      ((COALESCE(dg.gaji_pokok, 0) + COALESCE(dg.lembur, 0) + COALESCE(dg.honor, 0)) - (COALESCE(dg.potongan_bpjs, 0) + COALESCE(dg.potongan_jht, 0))) as gaji_bersih')
            ->join('pegawai p', 'dg.id_pegawai = p.ID_PEGAWAI', 'left')
            ->join('bagian b', 'dg.kd_bagian = b.KD_BAGIAN', 'left')
            ->join('jabatan j', 'dg.kd_jabatan = j.KD_JABATAN', 'left');

        // Apply filters
        if (!empty($filters['kd_bagian'])) {
            $builder->where('dg.kd_bagian', $filters['kd_bagian']);
        }
        if (!empty($filters['id_pegawai'])) {
            $builder->where('dg.id_pegawai', $filters['id_pegawai']);
        }
        if (isset($filters['status_approve']) && $filters['status_approve'] !== '') {
            $builder->where('dg.status_approve', (int)$filters['status_approve']);
        }
        if (!empty($filters['periode'])) {
            // format YYYY-MM
            $builder->like('dg.tgl_gaji_mulai', $filters['periode'], 'after');
        } elseif (!empty($filters['tgl_mulai']) && !empty($filters['tgl_sampai'])) {
            $builder->where('dg.tgl_gaji_mulai >=', $filters['tgl_mulai']);
            $builder->where('dg.tgl_gaji_mulai <=', $filters['tgl_sampai']);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('p.NAMA', $search)
                ->orLike('p.NIP', $search)
                ->orLike('b.BAGIAN', $search)
                ->orLike('j.JABATAN', $search)
                ->groupEnd();
        }

        // Clone for counting & summary totals
        $totalBuilder = clone $builder;
        $totalCount = $totalBuilder->countAllResults();

        // Calculate aggregates for current filter
        $aggBuilder = clone $builder;
        $aggRow = $aggBuilder->select('
            COUNT(dg.id_gaji) as total_pegawai,
            SUM(COALESCE(dg.gaji_pokok, 0)) as sum_gapok,
            SUM(COALESCE(dg.tunjangan_bpjs, 0) + COALESCE(dg.tunjangan_jht, 0) + COALESCE(dg.tunjangan_jkk, 0) + COALESCE(dg.tunjangan_jkm, 0)) as sum_tunjangan,
            SUM(COALESCE(dg.potongan_bpjs, 0) + COALESCE(dg.potongan_jht, 0)) as sum_potongan,
            SUM((COALESCE(dg.gaji_pokok, 0) + COALESCE(dg.lembur, 0) + COALESCE(dg.honor, 0)) - (COALESCE(dg.potongan_bpjs, 0) + COALESCE(dg.potongan_jht, 0))) as sum_gaji_bersih,
            SUM(CASE WHEN dg.status_approve = 1 THEN 1 ELSE 0 END) as total_approved
        ')->get()->getRowArray();

        $rows = $builder->orderBy('dg.tgl_input', 'DESC')
            ->orderBy('dg.id_gaji', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        return [
            'rows'    => $rows,
            'total'   => $totalCount,
            'summary' => [
                'total_pegawai'   => (int) ($aggRow['total_pegawai'] ?? 0),
                'sum_gapok'       => (float) ($aggRow['sum_gapok'] ?? 0),
                'sum_tunjangan'   => (float) ($aggRow['sum_tunjangan'] ?? 0),
                'sum_potongan'    => (float) ($aggRow['sum_potongan'] ?? 0),
                'sum_gaji_bersih' => (float) ($aggRow['sum_gaji_bersih'] ?? 0),
                'total_approved'  => (int) ($aggRow['total_approved'] ?? 0),
            ]
        ];
    }

    /**
     * 2. GET PAYROLL DETAIL by id_gaji
     */
    public function getPayrollDetail(int $idGaji)
    {
        $row = $this->db->table('detail_gaji dg')
            ->select('dg.*, p.NAMA as nama_pegawai, p.NIP, b.BAGIAN as nama_bagian, j.JABATAN as nama_jabatan,
                      p.EMAIL')
            ->join('pegawai p', 'dg.id_pegawai = p.ID_PEGAWAI', 'left')
            ->join('bagian b', 'dg.kd_bagian = b.KD_BAGIAN', 'left')
            ->join('jabatan j', 'dg.kd_jabatan = j.KD_JABATAN', 'left')
            ->where('dg.id_gaji', $idGaji)
            ->get()->getRowArray();

        if (!$row) {
            return null;
        }

        // Calculate totals
        $gapok = (float) ($row['gaji_pokok'] ?? 0);
        $tunjBpjs = (float) ($row['tunjangan_bpjs'] ?? 0);
        $tunjJht = (float) ($row['tunjangan_jht'] ?? 0);
        $tunjJkk = (float) ($row['tunjangan_jkk'] ?? 0);
        $tunjJkm = (float) ($row['tunjangan_jkm'] ?? 0);
        $lembur = (float) ($row['lembur'] ?? 0);
        $honor = (float) ($row['honor'] ?? 0);

        $potBpjs = (float) ($row['potongan_bpjs'] ?? 0);
        $potJht = (float) ($row['potongan_jht'] ?? 0);

        $totalPenghasilan = $gapok + $tunjBpjs + $tunjJht + $tunjJkk + $tunjJkm + $lembur + $honor;
        $totalPotongan = $potBpjs + $potJht;
        $gajiBersih = ($gapok + $lembur + $honor) - $totalPotongan;

        $row['total_bruto'] = $totalPenghasilan;
        $row['total_potongan'] = $totalPotongan;
        $row['gaji_bersih'] = $gajiBersih;

        return $row;
    }

    /**
     * 3. BATCH GENERATE PAYROLL for a period
     */
    public function generatePayrollBatch(string $periode, ?string $kdBagian = null, float $defaultGapok = 3500000, float $defaultUmk = 3000000, string $nipFilter = 'all')
    {
        $tglGajiMulai = date('Y-m-01', strtotime($periode . '-01'));
        $tglGajiSampai = date('Y-m-t', strtotime($periode . '-01'));
        $tahun = date('Y', strtotime($periode . '-01'));

        // Query active employees with latest placement
        $builder = $this->db->table('pegawai pg')
            ->select('pg.ID_PEGAWAI, pg.NIP, pg.NAMA, dp.KD_JABATAN, dp.KD_BAGIAN,
                      COALESCE(tg.gaji_pokok, ' . $defaultGapok . ') as gaji_pokok,
                      COALESCE(tu.umk, ' . $defaultUmk . ') as umk')
            ->join('(SELECT * FROM detail_penempatan_pegawai WHERE KD_PENEMPATAN IN (SELECT MAX(KD_PENEMPATAN) FROM detail_penempatan_pegawai GROUP BY ID_PEGAWAI)) dp', 'pg.ID_PEGAWAI = dp.ID_PEGAWAI', 'left')
            ->join('tb_gaji tg', 'tg.kd_jabatan = dp.KD_JABATAN', 'left')
            ->join('tb_umk tu', 'tu.kd_jabatan = dp.KD_JABATAN AND tu.tahun_umk = ' . $this->db->escape($tahun), 'left');

        if (!empty($kdBagian)) {
            $builder->where('dp.KD_BAGIAN', $kdBagian);
        }
        if ($nipFilter === 'non_pns') {
            $builder->where("SUBSTR(pg.NIP, 1, 1) != '1'");
        } elseif ($nipFilter === 'pns') {
            $builder->where("SUBSTR(pg.NIP, 1, 1) = '1'");
        }

        $employees = $builder->groupBy('pg.ID_PEGAWAI')->get()->getResultArray();

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($employees as $emp) {
            $idPeg = $emp['ID_PEGAWAI'];
            $gapok = (float) $emp['gaji_pokok'];
            $umk = (float) $emp['umk'];

            // Official statutory formulas (from legacy cgaji.php):
            // Tunjangan BPJS Kesehatan Pemberi Kerja = 3% UMK
            // Tunjangan BPJS Ketenagakerjaan (JHT) = 3.7% UMK
            // Tunjangan JKK = 0.24% UMK
            // Tunjangan JKM = 0.30% UMK
            // Potongan BPJS Kesehatan Karyawan = 2% UMK
            // Potongan BPJS TK (JHT) Karyawan = 2% UMK
            $tunjBpjs = round($umk * 0.03, 2);
            $tunjJht  = round($umk * 0.037, 2);
            $tunjJkk  = round($umk * 0.0024, 2);
            $tunjJkm  = round($umk * 0.0030, 2);
            $potBpjs  = round($umk * 0.02, 2);
            $potJht   = round($umk * 0.02, 2);

            // Check if record already exists for this employee and period
            $existing = $this->db->table('detail_gaji')
                ->where('id_pegawai', $idPeg)
                ->like('tgl_gaji_mulai', $periode, 'after')
                ->get()->getRowArray();

            $payload = [
                'tgl_input'       => date('Y-m-d H:i:s'),
                'id_pegawai'      => $idPeg,
                'gaji_pokok'      => $gapok,
                'tunjangan_bpjs'  => $tunjBpjs,
                'tunjangan_jht'   => $tunjJht,
                'tunjangan_jkk'   => $tunjJkk,
                'tunjangan_jkm'   => $tunjJkm,
                'potongan_bpjs'   => $potBpjs,
                'potongan_jht'    => $potJht,
                'lembur'          => 0,
                'honor'           => 0,
                'tgl_gaji_mulai'  => $tglGajiMulai . ' 00:00:00',
                'tgl_gaji_sampai' => $tglGajiSampai . ' 23:59:59',
                'status_approve'  => 0,
                'kd_bagian'       => $emp['KD_BAGIAN'] ?? null,
                'kd_jabatan'      => $emp['KD_JABATAN'] ?? null
            ];

            if ($existing) {
                // If not yet approved, update values
                if ((int)$existing['status_approve'] === 0) {
                    $this->db->table('detail_gaji')->where('id_gaji', $existing['id_gaji'])->update($payload);
                    $updatedCount++;
                }
            } else {
                $this->db->table('detail_gaji')->insert($payload);
                $insertedCount++;
            }
        }

        return [
            'inserted' => $insertedCount,
            'updated'  => $updatedCount,
            'total'    => count($employees),
            'periode'  => $periode
        ];
    }

    /**
     * 4. SAVE / UPDATE SINGLE PAYROLL RECORD
     */
    public function savePayrollItem(array $data)
    {
        $idGaji = !empty($data['id_gaji']) ? (int)$data['id_gaji'] : null;

        $record = [
            'id_pegawai'      => $data['id_pegawai'],
            'kd_bagian'       => $data['kd_bagian'] ?? null,
            'kd_jabatan'      => $data['kd_jabatan'] ?? null,
            'gaji_pokok'      => (float) ($data['gaji_pokok'] ?? 0),
            'tunjangan_bpjs'  => (float) ($data['tunjangan_bpjs'] ?? 0),
            'tunjangan_jht'   => (float) ($data['tunjangan_jht'] ?? 0),
            'tunjangan_jkk'   => (float) ($data['tunjangan_jkk'] ?? 0),
            'tunjangan_jkm'   => (float) ($data['tunjangan_jkm'] ?? 0),
            'potongan_bpjs'   => (float) ($data['potongan_bpjs'] ?? 0),
            'potongan_jht'    => (float) ($data['potongan_jht'] ?? 0),
            'lembur'          => (float) ($data['lembur'] ?? 0),
            'honor'           => (float) ($data['honor'] ?? 0),
            'tgl_gaji_mulai'  => !empty($data['tgl_gaji_mulai']) ? $data['tgl_gaji_mulai'] : date('Y-m-01 00:00:00'),
            'tgl_gaji_sampai' => !empty($data['tgl_gaji_sampai']) ? $data['tgl_gaji_sampai'] : date('Y-m-t 23:59:59'),
            'status_approve'  => isset($data['status_approve']) ? (int)$data['status_approve'] : 0,
        ];

        if ($idGaji) {
            $this->db->table('detail_gaji')->where('id_gaji', $idGaji)->update($record);
            return ['action' => 'update', 'id_gaji' => $idGaji];
        } else {
            $record['tgl_input'] = date('Y-m-d H:i:s');
            $this->db->table('detail_gaji')->insert($record);
            return ['action' => 'insert', 'id_gaji' => $this->db->insertID()];
        }
    }

    /**
     * 5. DELETE PAYROLL RECORD
     */
    public function deletePayrollItem(int $idGaji)
    {
        return $this->db->table('detail_gaji')->where('id_gaji', $idGaji)->delete();
    }

    /**
     * 6. APPROVE PAYROLL BATCH
     */
    public function approvePayroll(array $ids, int $status = 1)
    {
        if (empty($ids)) return 0;
        return $this->db->table('detail_gaji')
            ->whereIn('id_gaji', $ids)
            ->update(['status_approve' => $status]);
    }

    /**
     * 7. REKAPITULASI GAJI PER BAGIAN / UNIT KERJA
     */
    public function getRekapGaji(string $periode, ?string $kdBagian = null)
    {
        $builder = $this->db->table('detail_gaji dg')
            ->select('dg.kd_bagian, b.BAGIAN as nama_bagian,
                      COUNT(dg.id_gaji) as jml_pegawai,
                      SUM(COALESCE(dg.gaji_pokok, 0)) as total_gapok,
                      SUM(COALESCE(dg.tunjangan_bpjs, 0)) as total_tunj_bpjs,
                      SUM(COALESCE(dg.tunjangan_jht, 0)) as total_tunj_jht,
                      SUM(COALESCE(dg.tunjangan_jkk, 0)) as total_tunj_jkk,
                      SUM(COALESCE(dg.tunjangan_jkm, 0)) as total_tunj_jkm,
                      SUM(COALESCE(dg.potongan_bpjs, 0)) as total_pot_bpjs,
                      SUM(COALESCE(dg.potongan_jht, 0)) as total_pot_jht,
                      SUM(COALESCE(dg.lembur, 0)) as total_lembur,
                      SUM(COALESCE(dg.honor, 0)) as total_honor,
                      SUM((COALESCE(dg.gaji_pokok, 0) + COALESCE(dg.lembur, 0) + COALESCE(dg.honor, 0)) - (COALESCE(dg.potongan_bpjs, 0) + COALESCE(dg.potongan_jht, 0))) as total_thp')
            ->join('bagian b', 'dg.kd_bagian = b.KD_BAGIAN', 'left')
            ->like('dg.tgl_gaji_mulai', $periode, 'after')
            ->groupBy('dg.kd_bagian, b.BAGIAN');

        if (!empty($kdBagian)) {
            $builder->where('dg.kd_bagian', $kdBagian);
        }

        $rekapData = $builder->orderBy('b.BAGIAN', 'ASC')->get()->getResultArray();

        // Calculate grand totals
        $grandTotal = [
            'total_pegawai'   => 0,
            'total_gapok'     => 0,
            'total_tunj_bpjs' => 0,
            'total_tunj_jht'  => 0,
            'total_tunj_jkk'  => 0,
            'total_tunj_jkm'  => 0,
            'total_pot_bpjs'  => 0,
            'total_pot_jht'   => 0,
            'total_lembur'    => 0,
            'total_honor'     => 0,
            'total_thp'       => 0,
        ];

        foreach ($rekapData as &$row) {
            $grandTotal['total_pegawai']   += (int) $row['jml_pegawai'];
            $grandTotal['total_gapok']     += (float) $row['total_gapok'];
            $grandTotal['total_tunj_bpjs'] += (float) $row['total_tunj_bpjs'];
            $grandTotal['total_tunj_jht']  += (float) $row['total_tunj_jht'];
            $grandTotal['total_tunj_jkk']  += (float) $row['total_tunj_jkk'];
            $grandTotal['total_tunj_jkm']  += (float) $row['total_tunj_jkm'];
            $grandTotal['total_pot_bpjs']  += (float) $row['total_pot_bpjs'];
            $grandTotal['total_pot_jht']   += (float) $row['total_pot_jht'];
            $grandTotal['total_lembur']    += (float) $row['total_lembur'];
            $grandTotal['total_honor']     += (float) $row['total_honor'];
            $grandTotal['total_thp']       += (float) $row['total_thp'];
        }

        return [
            'periode'     => $periode,
            'rekap'       => $rekapData,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * 8. REKAP POTONGAN ABSENSI & KETERLAMBATAN
     * Match attendance tardiness (THPJK) and early departure (PSW) with tb_potongan brackets
     */
    public function getPotonganAbsensi(string $startDate, string $endDate, ?string $kdBagian = null)
    {
        // 1. Fetch deduction brackets from tb_potongan
        $rules = $this->db->table('tb_potongan')->orderBy('menit_awal', 'ASC')->get()->getResultArray();
        $thpjkRules = array_filter($rules, function($r) { return (int)$r['Jenis_potongan'] === 1; });
        $pswRules   = array_filter($rules, function($r) { return (int)$r['Jenis_potongan'] === 2; });

        // 2. Fetch attendance logs from t_perencanaan_harikerja
        $builder = $this->db->table('t_perencanaan_harikerja tph')
            ->select('p.ID_PEGAWAI, p.NIP, p.NAMA, b.KD_BAGIAN, b.BAGIAN,
                      tph.tanggal_jadwal, s.JAM_MASUK, s.JAM_PULANG, s.TOLERANSI,
                      tph.jam_datang, tph.jam_keluar')
            ->join('pegawai p', 'tph.id_pegawai = p.ID_PEGAWAI', 'inner')
            ->join('bagian b', 'tph.kd_bagian = b.KD_BAGIAN', 'left')
            ->join('standarisasi s', 'tph.kd_standarisasi = s.KD_STANDARISASI', 'left')
            ->where('tph.tanggal_jadwal >=', $startDate)
            ->where('tph.tanggal_jadwal <=', $endDate)
            ->where('tph.jam_datang IS NOT NULL')
            ->where('tph.jam_datang >', '2000-01-01');

        if (!empty($kdBagian)) {
            $builder->where('tph.kd_bagian', $kdBagian);
        }

        $records = $builder->get()->getResultArray();

        // 3. Aggregate per employee
        $pegawaiDeductions = [];

        foreach ($records as $rec) {
            $idPeg = $rec['ID_PEGAWAI'];
            if (!isset($pegawaiDeductions[$idPeg])) {
                $pegawaiDeductions[$idPeg] = [
                    'id_pegawai'       => $idPeg,
                    'nip'              => $rec['NIP'],
                    'nama'             => $rec['NAMA'],
                    'kd_bagian'        => $rec['KD_BAGIAN'],
                    'bagian'           => $rec['BAGIAN'] ?? '-',
                    'frek_terlambat'   => 0,
                    'menit_terlambat'  => 0,
                    'nominal_thpjk'    => 0,
                    'frek_psw'         => 0,
                    'menit_psw'        => 0,
                    'nominal_psw'      => 0,
                    'total_denda'      => 0,
                ];
            }

            // A. Check Late Arrival (THPJK)
            $scanIn = date('H:i:s', strtotime($rec['jam_datang']));
            $scheduledIn = $rec['JAM_MASUK'];
            $tolerance = $rec['TOLERANSI'] ?? '00:00:00';
            $toleratedTime = date('H:i:s', strtotime($scheduledIn) + strtotime($tolerance) - strtotime('00:00:00'));

            if ($scanIn > $toleratedTime && $scheduledIn != '00:00:00') {
                $diffSec = strtotime($scanIn) - strtotime($scheduledIn);
                $menitLate = max(0, (int) round($diffSec / 60));

                $pegawaiDeductions[$idPeg]['frek_terlambat']++;
                $pegawaiDeductions[$idPeg]['menit_terlambat'] += $menitLate;

                // Match with tb_potongan THPJK rules
                foreach ($thpjkRules as $rule) {
                    if ($menitLate >= (float)$rule['menit_awal'] && $menitLate <= (float)$rule['menit_akhir']) {
                        $pegawaiDeductions[$idPeg]['nominal_thpjk'] += (float)$rule['presentase_pot'];
                        break;
                    }
                }
            }

            // B. Check Early Departure (PSW)
            if (!empty($rec['jam_keluar']) && $rec['jam_keluar'] > '2000-01-01') {
                $scanOut = date('H:i:s', strtotime($rec['jam_keluar']));
                $scheduledOut = $rec['JAM_PULANG'];

                if ($scanOut < $scheduledOut && $scheduledOut != '00:00:00') {
                    $diffSecOut = strtotime($scheduledOut) - strtotime($scanOut);
                    $menitEarly = max(0, (int) round($diffSecOut / 60));

                    $pegawaiDeductions[$idPeg]['frek_psw']++;
                    $pegawaiDeductions[$idPeg]['menit_psw'] += $menitEarly;

                    // Match with tb_potongan PSW rules
                    foreach ($pswRules as $rule) {
                        if ($menitEarly >= (float)$rule['menit_awal'] && $menitEarly <= (float)$rule['menit_akhir']) {
                            $pegawaiDeductions[$idPeg]['nominal_psw'] += (float)$rule['presentase_pot'];
                            break;
                        }
                    }
                }
            }
        }

        // Calculate total denda
        $summary = [
            'total_pegawai_terdenda' => 0,
            'total_denda_thpjk'      => 0,
            'total_denda_psw'        => 0,
            'total_seluruh_denda'    => 0,
            'total_frek_terlambat'   => 0
        ];

        foreach ($pegawaiDeductions as &$p) {
            $p['total_denda'] = $p['nominal_thpjk'] + $p['nominal_psw'];
            if ($p['total_denda'] > 0 || $p['frek_terlambat'] > 0) {
                $summary['total_pegawai_terdenda']++;
            }
            $summary['total_denda_thpjk']   += $p['nominal_thpjk'];
            $summary['total_denda_psw']     += $p['nominal_psw'];
            $summary['total_seluruh_denda'] += $p['total_denda'];
            $summary['total_frek_terlambat'] += $p['frek_terlambat'];
        }

        return [
            'periode' => ['start' => $startDate, 'end' => $endDate],
            'rows'    => array_values($pegawaiDeductions),
            'rules'   => array_values($rules),
            'summary' => $summary
        ];
    }

    /**
     * 9. MASTER TARIF & KOMPONEN
     */
    public function getMasterTarif()
    {
        // Gaji Pokok per Jabatan
        $gajiList = $this->db->table('tb_gaji tg')
            ->select('tg.*, j.JABATAN as nama_jabatan')
            ->join('jabatan j', 'tg.kd_jabatan = j.KD_JABATAN', 'left')
            ->orderBy('j.JABATAN', 'ASC')
            ->get()->getResultArray();

        // UMK per Jabatan
        $umkList = $this->db->table('tb_umk tu')
            ->select('tu.*, j.JABATAN as nama_jabatan')
            ->join('jabatan j', 'tu.kd_jabatan = j.KD_JABATAN', 'left')
            ->orderBy('tu.tahun_umk', 'DESC')
            ->orderBy('j.JABATAN', 'ASC')
            ->get()->getResultArray();

        // Potongan Keterlambatan
        $potonganList = $this->db->table('tb_potongan')
            ->orderBy('Jenis_potongan', 'ASC')
            ->orderBy('menit_awal', 'ASC')
            ->get()->getResultArray();

        return [
            'gaji'     => $gajiList,
            'umk'      => $umkList,
            'potongan' => $potonganList
        ];
    }

    public function saveMasterTarifGaji(array $data)
    {
        $idGaji = !empty($data['id_gaji']) ? (int)$data['id_gaji'] : null;
        $record = [
            'kd_jabatan' => $data['kd_jabatan'],
            'gaji_pokok' => (int)$data['gaji_pokok']
        ];

        if ($idGaji) {
            $this->db->table('tb_gaji')->where('id_gaji', $idGaji)->update($record);
            return ['action' => 'update', 'id_gaji' => $idGaji];
        } else {
            // Check if already exists for this kd_jabatan
            $exist = $this->db->table('tb_gaji')->where('kd_jabatan', $data['kd_jabatan'])->get()->getRowArray();
            if ($exist) {
                $this->db->table('tb_gaji')->where('id_gaji', $exist['id_gaji'])->update($record);
                return ['action' => 'update', 'id_gaji' => $exist['id_gaji']];
            } else {
                $this->db->table('tb_gaji')->insert($record);
                return ['action' => 'insert', 'id_gaji' => $this->db->insertID()];
            }
        }
    }

    public function saveMasterTarifUmk(array $data)
    {
        $idUmk = !empty($data['id_umk']) ? (int)$data['id_umk'] : null;
        $record = [
            'kd_jabatan' => $data['kd_jabatan'],
            'tahun_umk'  => (float)$data['tahun_umk'],
            'umk'        => (float)$data['umk']
        ];

        if ($idUmk) {
            $this->db->table('tb_umk')->where('id_umk', $idUmk)->update($record);
            return ['action' => 'update', 'id_umk' => $idUmk];
        } else {
            $exist = $this->db->table('tb_umk')
                ->where('kd_jabatan', $data['kd_jabatan'])
                ->where('tahun_umk', (float)$data['tahun_umk'])
                ->get()->getRowArray();
            if ($exist) {
                $this->db->table('tb_umk')->where('id_umk', $exist['id_umk'])->update($record);
                return ['action' => 'update', 'id_umk' => $exist['id_umk']];
            } else {
                $this->db->table('tb_umk')->insert($record);
                return ['action' => 'insert', 'id_umk' => $this->db->insertID()];
            }
        }
    }

    public function saveMasterPotongan(array $data)
    {
        $idPotongan = !empty($data['id_potongan']) ? (int)$data['id_potongan'] : null;
        $record = [
            'menit_awal'     => (float)$data['menit_awal'],
            'menit_akhir'    => (float)$data['menit_akhir'],
            'Jenis_potongan' => (int)$data['Jenis_potongan'],
            'keterangan'     => $data['keterangan'] ?? '',
            'presentase_pot' => (float)$data['presentase_pot']
        ];

        if ($idPotongan) {
            $this->db->table('tb_potongan')->where('id_potongan', $idPotongan)->update($record);
            return ['action' => 'update', 'id_potongan' => $idPotongan];
        } else {
            $this->db->table('tb_potongan')->insert($record);
            return ['action' => 'insert', 'id_potongan' => $this->db->insertID()];
        }
    }

    public function deleteMasterPotongan(int $idPotongan)
    {
        return $this->db->table('tb_potongan')->where('id_potongan', $idPotongan)->delete();
    }
}
