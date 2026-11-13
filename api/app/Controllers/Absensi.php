<?php

namespace App\Controllers;

use App\Models\MAbsensi;

class Absensi extends BaseController
{
    protected $mAbsensi;

    public function __construct()
    {
        $this->mAbsensi = new MAbsensi();
    }

    /**
     * Master data for Absensi filters (shifts, bagian)
     */
    public function getMaster()
    {
        $shifts = $this->mAbsensi->getMasterShift();
        $bagian = $this->mAbsensi->getBagianList();

        return sendResponse(200, [
            'shifts' => $shifts,
            'bagian' => $bagian
        ]);
    }

    /**
     * Monthly attendance recap matrix
     * GET /api/absensi/rekap-bulanan?bulan=YYYY-MM&kd_bagian=...&id_pegawai=...
     */
    public function getRekapBulanan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $kdBagian = $this->request->getGet('kd_bagian');
        $idPegawai = $this->request->getGet('id_pegawai');

        $result = $this->mAbsensi->getRekapBulanan($bulan, $kdBagian, $idPegawai);
        return sendResponse(200, $result);
    }

    /**
     * Attendance status daily/range list
     * GET /api/absensi/status?tgl_mulai=...&tgl_sampai=...&kd_bagian=...&status=...&search=...&limit=50&offset=0
     */
    public function getStatus()
    {
        $tglMulai = $this->request->getGet('tgl_mulai') ?? date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?? date('Y-m-d');
        $kdBagian = $this->request->getGet('kd_bagian');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search') ?? $this->request->getGet('keyword');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getStatusRekap($tglMulai, $tglSampai, $kdBagian, $status, $search, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Attendance status counter summary
     * GET /api/absensi/status-summary?tgl_mulai=...&tgl_sampai=...&kd_bagian=...
     */
    public function getStatusSummary()
    {
        $tglMulai = $this->request->getGet('tgl_mulai') ?? date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?? date('Y-m-d');
        $kdBagian = $this->request->getGet('kd_bagian');

        $result = $this->mAbsensi->getStatusSummary($tglMulai, $tglSampai, $kdBagian);
        return sendResponse(200, $result);
    }

    /**
     * Scheduling progress by department
     * GET /api/absensi/jadwal-monitoring?bulan=YYYY-MM
     */
    public function getJadwalMonitoring()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $result = $this->mAbsensi->getJadwalMonitoring($bulan);
        return sendResponse(200, $result);
    }

    /**
     * Shift swap applications
     * GET /api/absensi/tukar-shift?kd_bagian=...&status=...&limit=50&offset=0
     */
    public function getTukarShift()
    {
        $kdBagian = $this->request->getGet('kd_bagian');
        $status = $this->request->getGet('status');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getTukarShift($kdBagian, $status, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Overtime requests
     * GET /api/absensi/lembur?bulan=YYYY-MM&kd_bagian=...&status=...&limit=50&offset=0
     */
    public function getLembur()
    {
        $bulan = $this->request->getGet('bulan');
        $kdBagian = $this->request->getGet('kd_bagian');
        $status = $this->request->getGet('status');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getLembur($bulan, $kdBagian, $status, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Morning assembly attendance events
     * GET /api/absensi/apel?tgl_mulai=...&tgl_sampai=...
     */
    public function getApel()
    {
        $tglMulai = $this->request->getGet('tgl_mulai');
        $tglSampai = $this->request->getGet('tgl_sampai');

        $result = $this->mAbsensi->getApel($tglMulai, $tglSampai);
        return sendResponse(200, $result);
    }

    /**
     * Raw biometric punch logs from machines
     * GET /api/absensi/log-mesin?tanggal=YYYY-MM-DD&pin=...&sn=...&limit=50&offset=0
     */
    public function getLogMesin()
    {
        $tanggal = $this->request->getGet('tanggal');
        $pin = $this->request->getGet('pin');
        $sn = $this->request->getGet('sn');
        $limit = (int) ($this->request->getGet('limit') ?? 50);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $result = $this->mAbsensi->getLogMesin($tanggal, $pin, $sn, $limit, $offset);
        return sendResponse(200, $result);
    }

    /**
     * Detailed punch scans for an employee on a specific date
     * GET /api/absensi/log-detail?pin=...&tanggal=YYYY-MM-DD
     */
    public function getLogDetail()
    {
        $pin = $this->request->getGet('pin');
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        if (empty($pin)) {
            return sendResponse(400, null, 'Parameter pin wajib disertakan');
        }

        $result = $this->mAbsensi->getLogDetail($pin, $tanggal);
        return sendResponse(200, $result);
    }

    /**
     * Preview tabular data for Rekap Laporan
     * GET /api/absensi/rekap-data?type=...&tahun=...&bulan=...&kd_bagian=...
     */
    public function getRekapData()
    {
        $type = $this->request->getGet('type') ?? 'cuti_tahun';
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        $kdBagian = $this->request->getGet('kd_bagian');

        $data = [];
        switch ($type) {
            case 'cuti_tahun':
                $data = $this->mAbsensi->getRekapCuti($tahun, null, $kdBagian);
                break;
            case 'cuti_bulan':
                $data = $this->mAbsensi->getRekapCuti($tahun, $bulan, $kdBagian);
                break;
            case 'ijin_tahun':
                $data = $this->mAbsensi->getRekapIjin($tahun, null, $kdBagian);
                break;
            case 'ijin_bulan':
                $data = $this->mAbsensi->getRekapIjin($tahun, $bulan, $kdBagian);
                break;
            case 'lembur':
                $data = $this->mAbsensi->getRekapLembur($tahun, $bulan, $kdBagian);
                break;
            default:
                return sendResponse(400, null, 'Tipe rekap tidak valid');
        }

        return sendResponse(200, $data);
    }

    /**
     * Handler Utama Cetak Laporan Rekapitulasi HTML A4
     */
    public function cetakRekap($overrideType = null)
    {
        helper(['printing', 'common']);

        $type = $overrideType ?? $this->request->getVar('type') ?? 'cuti_tahun';
        $tahun = $this->request->getVar('tahun') ?? date('Y');
        $bulan = $this->request->getVar('bulan');
        $kdBagian = $this->request->getVar('kd_bagian');

        $namaBulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Format nama Bagian
        $namaBagian = 'Semua Bagian / Unit Kerja';
        if (!empty($kdBagian) && $kdBagian !== 'all' && $kdBagian !== '-') {
            $db = \Config\Database::connect();
            $bg = $db->table('bagian')->select('BAGIAN')->where('KD_BAGIAN', $kdBagian)->get()->getRowArray();
            if (!empty($bg['BAGIAN'])) {
                $namaBagian = $bg['BAGIAN'];
            }
        }

        $judulLaporan = '';
        $periodeLabel = '';
        $rows = [];
        $isLembur = false;

        switch ($type) {
            case 'cuti_tahun':
                $judulLaporan = 'LAPORAN REKAPITULASI CUTI TAHUNAN PEGAWAI';
                $periodeLabel = "Tahun {$tahun}";
                $rows = $this->mAbsensi->getRekapCuti($tahun, null, $kdBagian);
                break;

            case 'cuti_bulan':
                $judulLaporan = 'LAPORAN REKAPITULASI CUTI BULANAN PEGAWAI';
                $bulanName = $namaBulanIndo[(int)$bulan] ?? "Bulan {$bulan}";
                $periodeLabel = "{$bulanName} {$tahun}";
                $rows = $this->mAbsensi->getRekapCuti($tahun, $bulan, $kdBagian);
                break;

            case 'ijin_tahun':
                $judulLaporan = 'LAPORAN REKAPITULASI IJIN TAHUNAN PEGAWAI';
                $periodeLabel = "Tahun {$tahun}";
                $rows = $this->mAbsensi->getRekapIjin($tahun, null, $kdBagian);
                break;

            case 'ijin_bulan':
                $judulLaporan = 'LAPORAN REKAPITULASI IJIN BULANAN PEGAWAI';
                $bulanName = $namaBulanIndo[(int)$bulan] ?? "Bulan {$bulan}";
                $periodeLabel = "{$bulanName} {$tahun}";
                $rows = $this->mAbsensi->getRekapIjin($tahun, $bulan, $kdBagian);
                break;

            case 'lembur':
                $isLembur = true;
                $judulLaporan = 'LAPORAN REKAPITULASI LEMBUR PEGAWAI';
                if (!empty($bulan) && $bulan !== 'all' && $bulan !== '-') {
                    $bulanName = $namaBulanIndo[(int)$bulan] ?? "Bulan {$bulan}";
                    $periodeLabel = "{$bulanName} {$tahun}";
                } else {
                    $periodeLabel = "Tahun {$tahun}";
                }
                $rows = $this->mAbsensi->getRekapLembur($tahun, $bulan, $kdBagian);
                break;

            default:
                return sendResponse(400, null, 'Jenis laporan cetak tidak valid');
        }

        // Render Table Headers & Rows
        $tableHeaders = '';
        $tableRows = '';
        $tableFooter = '';

        if ($isLembur) {
            $tableHeaders = '<tr>
                <th style="width: 35px;">No</th>
                <th style="width: 140px;">NIP</th>
                <th>Nama Pegawai</th>
                <th>Bagian / Unit Kerja</th>
                <th style="width: 95px;">Tgl Lembur</th>
                <th style="width: 110px;">Jam</th>
                <th style="width: 75px;">Durasi</th>
                <th style="width: 85px;">Kategori Hari</th>
                <th>Uraian Tugas / Keterangan</th>
                <th style="width: 85px;">Status</th>
            </tr>';

            if (empty($rows)) {
                $tableRows = '<tr><td colspan="10" class="text-center" style="padding: 25px; font-style: italic; color: #555;">Tidak ada data lembur pada periode dan filter yang dipilih.</td></tr>';
            } else {
                $totalLemburJam = 0;
                $no = 1;
                foreach ($rows as $r) {
                    $totalLemburJam += ($r['DURASI_JAM'] ?? 0);
                    $tglLemburStr = !empty($r['tanggal_lembur']) && $r['tanggal_lembur'] !== '0000-00-00' ? tgl_indo($r['tanggal_lembur']) : '-';
                    $jamStr = (!empty($r['jam_mulai']) ? substr($r['jam_mulai'], 0, 5) : '00:00') . ' - ' . (!empty($r['jam_selesai']) ? substr($r['jam_selesai'], 0, 5) : '00:00');
                    $statusColor = ($r['status'] == '1') ? 'color: #15803d;' : (($r['status'] == '2') ? 'color: #b91c1c;' : 'color: #b45309;');

                    $tableRows .= '<tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td class="text-center nowrap">' . htmlspecialchars($r['NIP'] ?? '-') . '</td>
                        <td class="font-bold">' . htmlspecialchars($r['NAMA'] ?? '-') . '</td>
                        <td>' . htmlspecialchars($r['BAGIAN'] ?? '-') . '</td>
                        <td class="text-center nowrap">' . $tglLemburStr . '</td>
                        <td class="text-center nowrap">' . $jamStr . '</td>
                        <td class="text-center font-bold">' . htmlspecialchars($r['DURASI'] ?? '-') . '</td>
                        <td class="text-center">' . htmlspecialchars($r['JENIS_HARI_LABEL'] ?? '-') . '</td>
                        <td>' . htmlspecialchars($r['keterangan'] ?? '-') . '</td>
                        <td class="text-center font-bold" style="' . $statusColor . '">' . htmlspecialchars($r['STATUS_LABEL'] ?? '-') . '</td>
                    </tr>';
                }
                $tableFooter = '<tr>
                    <td colspan="6" class="font-bold text-right" style="padding: 6px 10px;">TOTAL DATA: ' . count($rows) . ' Record &bull; TOTAL DURASI LEMBUR:</td>
                    <td class="font-bold text-center" style="background-color: #f8fafc;">' . round($totalLemburJam, 2) . ' Jam</td>
                    <td colspan="3"></td>
                </tr>';
            }
        } else {
            $isCuti = (strpos($type, 'cuti') !== false);
            $colJenis = $isCuti ? 'Jenis Cuti' : 'Jenis Ijin';
            $colKet = $isCuti ? 'Keperluan' : 'Alasan / Keperluan';

            $tableHeaders = '<tr>
                <th style="width: 35px;">No</th>
                <th style="width: 140px;">NIP</th>
                <th>Nama Pegawai</th>
                <th>Bagian / Unit Kerja</th>
                <th style="width: 140px;">' . $colJenis . '</th>
                <th style="width: 170px;">Periode Tanggal</th>
                <th style="width: 65px;">Jml Hari</th>
                <th>' . $colKet . '</th>
                <th style="width: 85px;">Status</th>
            </tr>';

            if (empty($rows)) {
                $tableRows = '<tr><td colspan="9" class="text-center" style="padding: 25px; font-style: italic; color: #555;">Tidak ada data pada periode dan filter yang dipilih.</td></tr>';
            } else {
                $totalHari = 0;
                $no = 1;
                foreach ($rows as $r) {
                    $totalHari += ($r['JUMLAH_HARI'] ?? 0);
                    $tglMulaiStr = !empty($r['TANGGAL_MULAI']) && $r['TANGGAL_MULAI'] !== '0000-00-00' ? tgl_indo($r['TANGGAL_MULAI']) : '-';
                    $tglSampaiStr = !empty($r['TANGGAL_SAMPAI']) && $r['TANGGAL_SAMPAI'] !== '0000-00-00' ? tgl_indo($r['TANGGAL_SAMPAI']) : $tglMulaiStr;
                    $periodeTanggal = ($tglMulaiStr === $tglSampaiStr) ? $tglMulaiStr : "{$tglMulaiStr} s/d {$tglSampaiStr}";
                    $statusColor = ($r['STATUS'] == '1') ? 'color: #15803d;' : (($r['STATUS'] == '2') ? 'color: #b91c1c;' : 'color: #b45309;');

                    $tableRows .= '<tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td class="text-center nowrap">' . htmlspecialchars($r['NIP'] ?? '-') . '</td>
                        <td class="font-bold">' . htmlspecialchars($r['NAMA'] ?? '-') . '</td>
                        <td>' . htmlspecialchars($r['BAGIAN'] ?? '-') . '</td>
                        <td>' . htmlspecialchars($r['JENIS_CUTI'] ?? '-') . '</td>
                        <td class="text-center nowrap">' . $periodeTanggal . '</td>
                        <td class="text-center font-bold">' . ($r['JUMLAH_HARI'] ?? 1) . ' Hari</td>
                        <td>' . htmlspecialchars($r['KEPERLUAN'] ?? '-') . '</td>
                        <td class="text-center font-bold" style="' . $statusColor . '">' . htmlspecialchars($r['STATUS_LABEL'] ?? '-') . '</td>
                    </tr>';
                }
                $tableFooter = '<tr>
                    <td colspan="6" class="font-bold text-right" style="padding: 6px 10px;">TOTAL DATA: ' . count($rows) . ' Record &bull; TOTAL HARI:</td>
                    <td class="font-bold text-center" style="background-color: #f8fafc;">' . $totalHari . ' Hari</td>
                    <td colspan="2"></td>
                </tr>';
            }
        }

        // Kop & Profil RS
        $namaRsPemerintah = env('app.nama_rs_pemerintah') ?: 'PEMERINTAH KABUPATEN PULAU TALIABU';
        $namaRs = env('app.nama_rs') ?: 'UPTD RUMAH SAKIT UMUM DAERAH BOBONG';
        $alamatRs = env('app.alamat_rs') ?: 'Jl. Trans Taliabu, Desa Bobong, Kec. Taliabu Barat, Kab. Pulau Taliabu, Maluku Utara 97794';
        $telpRs = env('app.telp_rs') ?: '(0929) 21100 / 085211917591';
        $emailRs = env('app.email_rs') ?: 'rsudbobong@gmail.com';

        // Logo Base64
        $logoPath = ROOTPATH . 'assets/logo_bobong.png';
        if (file_exists($logoPath)) {
            $logoRs = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            $logoRs = env('app.logo_rs') ?: 'http://127.0.0.1:8099/evoHRIS_v2.1/api/assets/logo_bobong.png';
        }

        $tanggalCetak = tgl_indo(date('Y-m-d'));

        // Pejabat Tanda Tangan
        $jabatanKiri = 'Kasubag Kepegawaian & Tata Usaha';
        $pejabatKiri = 'Hasmi Gani, SKM';
        $nipKiri = '19850612 201101 1 002';

        $jabatanKanan = 'Direktur UPTD RSUD Bobong';
        $pejabatKanan = 'dr. Cecilia, Sp.A';
        $nipKanan = '19790422 200804 2 001';

        $outputName = "rekap_{$type}_" . date('Ymd_His');

        $tags = [
            '{judul_laporan}', '{periode_label}', '{nama_bagian}',
            '{nama_rs_pemerintah}', '{nama_rs}', '{alamat_rs}', '{telp_rs}', '{email_rs}', '{logo_rs}',
            '{tanggal_cetak}', '{table_headers}', '{table_rows}', '{table_footer}',
            '{jabatan_kiri}', '{pejabat_kiri}', '{nip_kiri}',
            '{jabatan_kanan}', '{pejabat_kanan}', '{nip_kanan}'
        ];

        $inputData = [
            $judulLaporan, $periodeLabel, $namaBagian,
            $namaRsPemerintah, $namaRs, $alamatRs, $telpRs, $emailRs, $logoRs,
            $tanggalCetak, $tableHeaders, $tableRows, $tableFooter,
            $jabatanKiri, $pejabatKiri, $nipKiri,
            $jabatanKanan, $pejabatKanan, $nipKanan
        ];

        $printed = printing('eabsensi/cetak_rekap_laporan', $tags, $inputData, $outputName, 'pdf', 'landscape', 'html', 'A4');

        $results = $printed['results'] ?? [];
        $code = $printed['code'] ?? 200;

        $isJson = $this->request->isAJAX() || strpos($this->request->getHeaderLine('Accept'), 'application/json') !== false || $this->request->getMethod() === 'post';
        if (!$isJson && isset($results[0]['file'])) {
            return redirect()->to($results[0]['file']);
        }

        return sendResponse($code, $results);
    }

    public function cetakCutiTahun()
    {
        return $this->cetakRekap('cuti_tahun');
    }

    public function cetakCutiBulan()
    {
        return $this->cetakRekap('cuti_bulan');
    }

    public function cetakIjinTahun()
    {
        return $this->cetakRekap('ijin_tahun');
    }

    public function cetakIjinBulan()
    {
        return $this->cetakRekap('ijin_bulan');
    }

    public function cetakLembur()
    {
        return $this->cetakRekap('lembur');
    }
}
