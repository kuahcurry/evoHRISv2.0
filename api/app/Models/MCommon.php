<?php

namespace App\Models;

use CodeIgniter\Model;

class MCommon extends Model
{
        private const DEFAULT_PENDAFTARAN = ['CMB_JKEL', 'CMB_GOLDAR', 'CMB_PROPINSI', 'CMB_KABUPATEN', 'CMB_KECAMATAN', 'CMB_KELURAHAN', 'CMB_STATUS', 'CMB_IDENTITAS', 'CMB_PENDIDIKAN', 'CMB_WARGANEGARA', 'CMB_PEKERJAAN', 'CMB_AGAMA', 'CMB_SPESIALISASI', 'CMB_PENJAMIN', 'CMB_NAMAPENJAMIN', 'CMB_DARI', 'CMB_PENANGGUNGJWB', 'CMB_INSTALASI', 'CMB_INSTALASI_DEFAULT', 'CMB_UNIT_DEFAULT', 'CMB_PARAMEDIS_DEFAULT', 'CMB_PARAMEDIS_DEFALUT', 'CMB_SUKU', 'CMB_BAHASA'];
        private const DEFAULT_KASIR_GLOBAL = ['KSR_DEFAULT_PENJAMIN_BAYAR', 'KSR_DEFAULT_CARA_BAYAR', 'KSR_DEFAULT_SHIFT'];
        private const DEFAULT_KASIR_USER = ['KSR_DEFAULT_FILTER_UNIT', 'KSR_DEFAULT_FILTER_STATUS', 'COUNTER'];
        private const DEFAULT_MUTASI_GLOBAL = ['KD_INSTALASI_FARMASI', 'UNIT_GUDANG_FARMASI', 'KD_APOTEK_JENIS_TRANS_PENGELUARAN', 'KD_APOTEK_JENIS_TRANS_PEMBATALAN_PENGELUARAN'];
        private const DEFAULT_MUTASI_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_PENYESUAIAN_STOK_GLOBAL = ['UNIT_GUDANG_FARMASI', 'KD_INSTALASI_FARMASI'];
        private const DEFAULT_INFO_HARGA_OBAT_GLOBAL = ['HEADER1', 'GAMBARHEADER', 'KET_LAPORAN'];
        private const DEFAULT_PENYESUAIAN_STOK_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_PELAYANAN_RESEP_GLOBAL = ['KD_INSTALASI_FARMASI', 'KD_KELPENJAMIN_UMUM', 'KD_KATEGORI_PASIEN_APOTEK', 'KD_APOTEK_JENIS_TRANS_PENJUALAN', 'KD_APOTEK_JENIS_TRANS_PEMBATALAN_PENJUALAN', 'TUSLAH', 'RACIKAN', 'KONSELING', 'ADMINISTRASI', 'KD_JENIS_KEUNTUNGAN_TUSLAH'];
        private const DEFAULT_PELAYANAN_RESEP_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_PENGEMBALIAN_RESEP_GLOBAL = ['KD_INSTALASI_FARMASI', 'KD_APOTEK_JENIS_TRANS_RETUR'];
        private const DEFAULT_PENGEMBALIAN_RESEP_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_FARMASI_KLINIS_GLOBAL = ['UNIT_KEMO', 'KD_INSTALASI_RJ', 'KD_INSTALASI_RD', 'KD_INSTALASI_RAD', 'KD_KLASIFIKASI_FARMASIKLINIS', 'KD_POLATARIF_JASA_PELAYANAN'];
        private const DEFAULT_FARMASI_KLINIS_USER = ['UNIT_APOTEK', 'CMB_JKEL', 'CMB_NAMAPENJAMIN_LAB', 'CMB_DARI'];
        private const DEFAULT_PENERIMAAN_GLOBAL = ['UNIT_GUDANG_FARMASI'];
        private const DEFAULT_PENERIMAAN_USER = ['SUMBER_ANGGARAN'];
        private const DEFAULT_PERMINTAAN_UNIT_GLOBAL = ['UNIT_GUDANG_FARMASI', 'KD_INSTALASI_FARMASI'];
        private const DEFAULT_PERMINTAAN_UNIT_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_PERMINTAAN_KEBUTUHAN_GLOBAL = ['UNIT_GUDANG_FARMASI', 'KD_INSTALASI_FARMASI'];
        private const DEFAULT_PERMINTAAN_KEBUTUHAN_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_RENCANA_KEBUTUHAN_GLOBAL = ['UNIT_GUDANG_FARMASI', 'KD_INSTALASI_FARMASI'];
        private const DEFAULT_RENCANA_KEBUTUHAN_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_RENCANA_PENGADAAN_GLOBAL = ['UNIT_GUDANG_FARMASI', 'KD_INSTALASI_FARMASI', 'HEADER1', 'GAMBARHEADER', 'GAMBARHEADER2', 'KABAG_KEUANGAN', 'LOGOBARU1', 'LOGOBARU2', 'HEADERBARU'];
        private const DEFAULT_RENCANA_PENGADAAN_USER = ['SUMBER_ANGGARAN'];
        private const DEFAULT_PEMANGGIL_ANTRIAN_GLOBAL = ['KD_INSTALASI_RJ'];
        private const DEFAULT_PEMANGGIL_ANTRIAN_USER = ['UNIT_APOTEK', 'KD_LOKET'];
        private const DEFAULT_PENGHAPUSAN_GLOBAL = ['KD_APOTEK_JENIS_TRANS_PENGHAPUSAN', 'KD_APOTEK_JENIS_TRANS_PEMBATALAN_PENGHAPUSAN'];
        private const DEFAULT_PENGHAPUSAN_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN'];
        private const DEFAULT_PRODUKSI_GLOBAL = ['UNIT_GUDANG_FARMASI', 'UNIT_GUDANG_ED', 'KD_INSTALASI_FARMASI', 'KD_APOTEK_JENIS_TRANS_PRODUKSI', 'KD_APOTEK_JENIS_TRANS_PEMBATALAN_PRODUKSI'];
        private const DEFAULT_PRODUKSI_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN', 'KD_PENJAMIN_OBAT_APOTEK'];
        private const DEFAULT_PENGEMBALIAN_PBF_GLOBAL = ['UNIT_GUDANG_FARMASI', 'KD_APOTEK_JENIS_TRANS_RETURPBF', 'KD_APOTEK_JENIS_TRANS_PEMBATALAN_RETURPBF'];
        private const DEFAULT_PENGEMBALIAN_PBF_USER = ['UNIT_APOTEK', 'SUMBER_ANGGARAN'];
    public function getKdParamedisBpjs($dataset = array())
    {
        $result = '';
        $params = array($dataset['kd_paramedis']);
        $query = "select kd_dokter_bpjs from para_medis where kd_paramedis = ? limit 1";
        $res = $this->db->query($query, $params);
        if (count($res->getResultArray()) > 0) {
            $result = intval($res->getResultArray()[0]['kd_dokter_bpjs']);
        }
        return $result;
    }

    public static function currentDateTime(): \DateTimeImmutable
    {
        $offset = max(-14, min(14, (int) getenv('app.zona')));
        $timezone = new \DateTimeZone(sprintf('%+03d:00', $offset));

        return (new \DateTimeImmutable('@'.time()))
            ->setTimezone($timezone);
    }

    public static function timezoneName(): string
    {
        $offset = max(-14, min(14, (int) getenv('app.zona')));
        if ($offset === 0) return 'UTC';
        return 'Etc/GMT'.($offset > 0 ? '-' : '+').abs($offset);
    }

    public function getCurrentDate(): string
    {
        return self::currentDateTime()->format('Y-m-d');
    }
    public function getSetting($komponen)
    {
        $setting = '';

        $params = array($komponen);
        $strquery = "SELECT setting FROM setting WHERE komponen = ? AND global = true LIMIT 1";
        $res = $this->db->query($strquery, $params);
        if($res->getNumRows() > 0) {
            foreach($res->getResult() as $p) {
                $setting = $p->setting;
            }
        }

        return $setting;
    }
    public function getKdMasterPentarifan($dataset = array())
    {
        $result = '';
        $params = array($dataset['kd_unit'], $dataset['kd_penjamin']);
        $strquery = "select kd_masterpentarifan from mapping_tarif where kd_unit = ? AND kd_detkelpenjamin = ?";
        $res = $this->db->query($strquery, $params);
        if (count($res->getResultArray()) > 0) {
            $result = $res->getResultArray()[0]['kd_masterpentarifan'];
        }
        return $result;
    }
    public function getDataCounter($tabel, $single_id, $no_registrasikunjungan)
    {
        $result = '';
        $params = array($single_id, $no_registrasikunjungan);
        $strquery = "select max(counter) counternya from {$tabel} where single_id = ? and no_registrasikunjungan = ?";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['counternya'] + 1;
        } else {
            $result = 1;
        }
        return $result;
    }

    public function getDataCounter_bymedrec($tabel, $no_medicalrecord)
    {
        $result = '';
        $zona = intval(getenv('app.zona'));
        $tgl = gmdate("Y-m-d", time() + 60 * 60 * $zona);
        $params = array($no_medicalrecord, $tgl);
        $strquery = "select max(counter) counternya from {$tabel} where no_medicalrecord = ? and tgl_entry::date=?";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['counternya'] + 1;
        } else {
            $result = 1;
        }
        return $result;
    }
    public function getTriageNumber($tabel, $single_id, $no_registrasikunjungan)
    {
        $zona = intval(getenv('app.zona'));
        $result = 1;
        $no_triage = gmdate("dmYH", time() + 60 * 60 * $zona);
        $params = array($no_triage);
        $strquery = "select substr(no_triage, 11) from rawat_darurat_rme.pra_asesmen order by counter desc limit 1";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['counternya'] + 1;
        } else {
            $result = $no_triage . '-1';
        }
        return $result;
    }
    public function getShift()
    {
        $time = self::currentDateTime()->format('H:i:s');

        $sql = "SELECT shift_id FROM master.shift
                WHERE (
                    jam_mulai <= jam_selesai
                    AND ?::time >= jam_mulai
                    AND ?::time < jam_selesai
                )
                OR (
                    jam_mulai > jam_selesai
                    AND (
                        ?::time >= jam_mulai
                        OR ?::time < jam_selesai
                    )
                )
                ORDER BY jam_mulai DESC
                LIMIT 1";

        $rows = $this->db
            ->query($sql, [$time, $time, $time, $time])
            ->getResultArray();

        return (string) ($rows[0]['shift_id'] ?? '1');
    }
    public function getKaruPpi($kd_ruangperawatan)
    {
        $result = 0;
        $params = array($kd_ruangperawatan);
        $strquery = "select karu.kd_paramedis, pm.nama_paramedis
            from master.mapping_karu_ppi karu 
            left join para_medis pm on pm.kd_paramedis = karu.kd_paramedis
            where karu.kd_ruangperawatan = ? and karu.status = 1";
        $res = $this->db->query($strquery, $params);
        return $res->getResultArray();
    }
    public function getUrutMasuk($no_medicalrecord)
    {
        $result = 0;
        $params = array($no_medicalrecord);
        $strquery = "select urut_masuk from kunjungan where no_medicalrecord = ? and tgl_keluar is null order by tgl_masuk desc limit 1";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['urut_masuk'];
            $no_urut = ((int) $result) + 1;
        } else {
            $no_urut = 1;
        }
        return $no_urut;
    }
    public function getCounter($kd_unit)
    {
        $result = '';
        $params = array($kd_unit);
        $strquery = "select kd_counter from counter where kd_unit = ?";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['kd_counter'];
        }
        return $result;
    }
    public function getKdUnitBytag($tag)
    {
        $result = 0;
        $params = array("%{$tag}%");
        $strquery = "select kd_unit from unit where nama_unit ilike ?";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['kd_unit'];
        }
        return $result;
    }
    public function getKdParamedis($kd_user)
    {
        $result = 0;
        $params = array($kd_user);
        $strquery = "select kd_paramedis from para_medis where kd_user = ?";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['kd_paramedis'];
        }
        return $result;
    }
    public function createNo_order($dataset)
    {
        $result = '';
        $jenis = isset($dataset['jenis']) ? $dataset['jenis'] : 'resep';
        if ($jenis == 'resep') {
            $prefix = 'APT';
            $tabel = 'detail_transaksi_order_obat_penatajasa';
            $dmy = gmdate("dmy", time() + 60 * 60 * 7);
            $params = array($dataset['kd_unit_asal'], $dmy);
        } else if ($jenis == 'laboratorium') {
            $prefix = 'LAB';
            $tabel = 'detail_transaksi_order_penatajasa';
        } else if ($jenis == 'radiologi') {
            $prefix = 'RAD';
            $tabel = 'detail_transaksi_order_penatajasa';
        }
        $strquery = "select coalesce(max(substr(no_order, 14)::numeric), 0) + 1 no_order from {$tabel} 
               where substr(no_order, 1, 5)::numeric = ? and substr(no_order, 7, 6) = ?";
        $res = $this->db->query($strquery, $params);
        $lastNumber = $res->getResultArray()[0]['no_order'];
        $result = $prefix . '-' . $dmy . '-' . $lastNumber;
        return $result;
    }
    public function createNo_medicalrecord()
    {
        $params = array();
        $strquery = "select no_medicalrecord from
                (select replace(NO_MEDICALRECORD,'-','') as NO_MEDICALRECORD from PASIEN WHERE substr(NO_MEDICALRECORD,1,3) != 'APT' and substr(NO_MEDICALRECORD,1,3) != 'RAD' and substr(NO_MEDICALRECORD,1,3) != 'LAB' and substr(no_medicalrecord,1,2)='0-' order by NO_MEDICALRECORD desc)x
                where no_medicalrecord not in (
                '0999124','0999123','0999122','0999121','0999120','0999119','0999118','0999117','0999116','0999115','0999114','0999113','0999112','0999111','0999110','0999109','0999108','0999107','0999106','0999105','0999104','0900114','0900113','0900112','0900111','0900110','0900109','0900108','0900107','0900106','0900105','0900104','0900103','0900102','0900101','0900100','0900099','0900098','0900097','0900096','0900095','0900094','0900093','0900092','0900091','0900090','0900089','0900088','0900087','0900086','0900085','0900084','0900083','0900082','0900081','0900080','0900079','0900078','0900077','0900076','0900075','0900074','0900073','0900072','0900071','0900070','0900069','0900068')--,'0778813')
                limit 1";
        $res = $this->db->query($strquery, $params);
        if (count($res->getResultArray()) > 0) {
            $rows = $res->getResultArray();
            foreach ($rows as $row) {
                $r = ((int)$row['no_medicalrecord']) + 1;
                if (strlen($r) == 1) {
                    $r1 = "0-00-00-0" . $r;
                } else if (strlen($r) == 2) {
                    $r1 = "0-00-00-" . substr($r, -2);
                } else if (strlen($r) == 3) {
                    $r1 = "0-00-0" . substr($r, -3, 1) . "-" . substr($r, -2);
                } else if (strlen($r) == 4) {
                    $r1 = "0-00-" . substr($r, -4, 2) . "-" . substr($r, -2);
                } else if (strlen($r) == 5) {
                    $r1 = "0-0" . substr($r, -5, 1) . "-" . substr($r, -4, 2) . "-" . substr($r, -2);
                } else if (strlen($r) == 6) {
                    $r1 = "0-" . substr($r, -6, 2) . "-" . substr($r, -4, 2) . "-" . substr($r, -2);
                } else if (strlen($r) == 7) {
                    $r1 = substr($r, -7, 1) . "-" . substr($r, -6, 2) . "-" . substr($r, -4, 2) . "-" . substr($r, -2);
                }
            }
            $no_medicalrecord = $r1;
        } else {
            $no_medicalrecord = "0-00-00-01";
        }
        return $no_medicalrecord;
    }
    public function createNo_kunjungan()
    {
        $zona = intval(getenv('app.zona'));
        $tgl = gmdate("dmY", time() + 60 * 60 * $zona);
        $params = array($tgl);
        $strquery = "select max(cast(r.no as integer)) as no_registrasikunjungan
               from (
                select substring(no_registrasikunjungan,10,10) as no
                from kunjungan where substr(no_registrasikunjungan,0,9) = ?
                group by no_registrasikunjungan, tgl_masuk
               ) r where r.no != ''";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['no_registrasikunjungan'];
            $no_kunjungan = ((int) $result) + 1;
        } else {
            $no_kunjungan = 1;
        }
        return $tgl . '-' . $no_kunjungan;
    }
    public function createSingle_id()
    {
        $zona = intval(getenv('app.zona'));
        $tgl_masuk = gmdate("Y-m-d", time() + 60 * 60 * $zona);
        $tgl = gmdate("Ymd", time() + 60 * 60 * $zona);
        $params = array($tgl_masuk);
        $strquery = "select COALESCE(MAX(substr( single_id :: TEXT, 9)::INTEGER), 0) + 1 single_id FROM kunjungan_header WHERE tgl_masuk::DATE = ?";
        $res = $this->db->query($strquery, $params);
        $result = $res->getResultArray()[0]['single_id'];
        return $tgl . sprintf("%04d", $result);
    }
    /** Allocate inside a transaction; insert the number before committing. */
    public function createNo_transaksi($kd_unit, ?\DateTimeInterface $tanggal = null): string
    {
        $unit = trim((string) $kd_unit);
        if ($unit === '' || !ctype_digit($unit)) {
            throw new \InvalidArgumentException('Kode unit transaksi tidak valid.');
        }
        $date = $tanggal ? $tanggal->format('dmy') : gmdate('dmy', time() + 3600 * (int) getenv('app.zona'));
        if (!$this->db->query('LOCK TABLE transaksi IN SHARE ROW EXCLUSIVE MODE')) {
            throw new \RuntimeException('Gagal mengunci nomor transaksi.');
        }
        $row = $this->db->query(
            "SELECT COALESCE(MAX(CAST(split_part(no_transaksi,'-',3) AS bigint)),0)+1 AS n FROM transaksi WHERE split_part(no_transaksi,'-',1)=? AND split_part(no_transaksi,'-',2)=? AND no_transaksi ~ '^[0-9]+-[0-9]{6}-[0-9]+$'",
            [$unit, $date],
        )->getRowArray();
        return $unit . '-' . $date . '-' . $row['n'];
    }
    public function createNo_antrian($kd_unit)
    {
        $zona = intval(getenv('app.zona'));
        $tgl = gmdate("Y-m-d", time() + 60 * 60 * $zona);
        $params = array($kd_unit, $tgl);
        $strquery = "select max(no_antrian) as no_antrian from kunjungan where status = 0 and kd_unit = ? and tgl_masuk::date = ?";
        $res = $this->db->query($strquery, $params);
        if ($res->getNumRows() > 0) {
            $result = $res->getResultArray()[0]['no_antrian'];
            $no_antrian = ((int) $result) + 1;
        } else {
            $no_antrian = 1;
        }
        return $no_antrian;
    }
    public function createTriage_id()
    {
        $zona = intval(getenv('app.zona'));
        $tgl_masuk = gmdate("Y-m-d", time() + 60 * 60 * $zona);
        $tgl = gmdate("Ymd", time() + 60 * 60 * $zona);
        $params = array($tgl_masuk);
        $strquery = "select COALESCE(MAX(substr( id_triage :: TEXT, 9)::INTEGER), 0) + 1 id_triage FROM rawat_darurat_rme.data_pasien WHERE tgl_entry::DATE = ?";
        $res = $this->db->query($strquery, $params);
        $result = $res->getResultArray()[0]['id_triage'];
        return $tgl . sprintf("%04d", $result);
    }
    public function getTglLahir($dataset)
    {
        date_default_timezone_set('Asia/Bangkok');

        $data = array();
        $thn = $dataset['umur_thn'];
        $bln = $dataset['umur_bln'];
        $har  = $dataset['umur_hr'];

        //Result
        $res_tgl_lahir = '';
        $res_thn = '';
        $res_bln = '';
        $res_hr = '';

        if (($thn != "" || $thn != "0") && ($bln == "" || $bln == "0") && ($har == "" || $har == "0")) {
            $date = strtotime(date('Y-m-d ') . ' -' . $thn . ' year ');
            $tgl_lahir = date('Y-m-d', $date);

            $res_tgl_lahir = substr($tgl_lahir, -10, 4) . "-" . substr($tgl_lahir, -5, 2) . "-" . substr($tgl_lahir, -2, 2);
            $res_thn = $thn;
            $res_bln = "0";
            $res_hr = "0";
        } else if (($bln != "" || $bln != "0") && ($thn == "" || $thn == "0") && ($har == "" || $har == "0")) {
            $date = strtotime(date('Y-m-d ') . ' -' . $bln . ' month');
            $tgl_lahir = date('Y-m-d', $date);

            $res_tgl_lahir = substr($tgl_lahir, -10, 4) . "-" . substr($tgl_lahir, -5, 2) . "-" . substr($tgl_lahir, -2, 2);
            $res_thn = "0";
            $res_bln = $bln;
            $res_hr = "0";
        } else if (($har != "" || $har != "0") && ($bln == "" || $bln == "0") && ($thn == "" || $thn == "0")) {
            $date = strtotime(date('Y-m-d ') . ' -' . $har . ' days');
            $tgl_lahir = date('Y-m-d', $date);

            $res_tgl_lahir = substr($tgl_lahir, -10, 4) . "-" . substr($tgl_lahir, -5, 2) . "-" . substr($tgl_lahir, -2, 2);
            $res_thn = "0";
            $res_bln = "0";
            $res_hr = $har;
        } else if (($thn != "" || $thn != "0") && ($bln != "" || $bln != "0") && ($har == "" || $har == "0")) {
            $date = strtotime(date('Y-m-d ') . ' -' . $thn . ' year ' . ' -' . $bln . ' month');
            $tgl_lahir = date('Y-m-d', $date);

            $res_tgl_lahir = substr($tgl_lahir, -10, 4) . "-" . substr($tgl_lahir, -5, 2) . "-" . substr($tgl_lahir, -2, 2);
            $res_thn = $thn;
            $res_bln = $bln;
            $res_hr = "0";
        } else if (($bln != "" || $bln != "0") && ($har != "" || $har != "0") && ($thn == "" || $thn == "0")) {
            $date = strtotime(date('Y-m-d ') . ' -' . $bln . ' month' . ' -' . $har . ' days');
            $tgl_lahir = date('Y-m-d', $date);

            $res_tgl_lahir = substr($tgl_lahir, -10, 4) . "-" . substr($tgl_lahir, -5, 2) . "-" . substr($tgl_lahir, -2, 2);
            $res_thn = "0";
            $res_bln = $bln;
            $res_hr = $har;
        } else if (($thn != "" || $thn != "0") && ($bln != "" || $bln != "0") && ($har != "" || $har != "0")) {
            $date = strtotime(date('Y-m-d ') . ' -' . $thn . ' year ' . ' -' . $bln . ' month' . ' -' . $har . ' days');
            $tgl_lahir = date('Y-m-d', $date);

            $res_tgl_lahir = substr($tgl_lahir, -10, 4) . "-" . substr($tgl_lahir, -5, 2) . "-" . substr($tgl_lahir, -2, 2);
            $res_thn = $thn;
            $res_bln = $bln;
            $res_hr = $har;
        }

        array_push($data, array(
            "tgl_lahir" => $res_tgl_lahir,
            "umur_thn" => $res_thn,
            "umur_bln" => $res_bln,
            "umur_hr" => $res_hr
        ));
        return $data;
    }

    public function mgetcmbjeniskelamin($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(jk.kd_jenis AS VARCHAR) ILIKE ? OR jk.jenis ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT jk.kd_jenis, jk.jenis FROM jenis_kelamin jk{$whereSql} ORDER BY jk.kd_jenis ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbgolongandarah($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(gd.kd_golongan AS VARCHAR) ILIKE ? OR gd.nama_golongan ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT gd.kd_golongan, gd.nama_golongan FROM golongan_darah gd{$whereSql} ORDER BY gd.kd_golongan ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbbahasa($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(b.kd_bahasa AS VARCHAR) ILIKE ? OR b.nama_bahasa ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT b.kd_bahasa, b.nama_bahasa FROM bahasa b{$whereSql} ORDER BY b.kd_bahasa ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbagama($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(a.kd_agama AS VARCHAR) ILIKE ? OR a.nama_agama ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT a.kd_agama, a.nama_agama FROM agama a{$whereSql} ORDER BY a.kd_agama ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbpendidikan($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(p.kd_pendidikan AS VARCHAR) ILIKE ? OR p.nama_pendidikan ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT p.kd_pendidikan, p.nama_pendidikan FROM pendidikan p{$whereSql} ORDER BY p.kd_pendidikan ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbpekerjaan($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(p.kd_pekerjaan AS VARCHAR) ILIKE ? OR p.nama_pekerjaan ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT p.kd_pekerjaan, p.nama_pekerjaan FROM pekerjaan p{$whereSql} ORDER BY p.kd_pekerjaan ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbkebangsaan($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(w.kd_warganegara AS VARCHAR) ILIKE ? OR w.nama_warganegara ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT w.kd_warganegara, w.nama_warganegara FROM warga_negara w{$whereSql} ORDER BY w.kd_warganegara ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbstatuspasien($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(s.kd_status AS VARCHAR) ILIKE ? OR s.nama_status ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT s.kd_status, s.nama_status FROM status_perkawinan s{$whereSql} ORDER BY s.kd_status ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbsuku($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(s.kd_suku AS VARCHAR) ILIKE ? OR s.nama_suku ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT s.kd_suku, s.nama_suku FROM suku_bangsa s{$whereSql} ORDER BY s.kd_suku ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbpropinsi($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(p.kd_propinsi AS VARCHAR) ILIKE ? OR p.nama_propinsi ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT p.kd_propinsi, p.nama_propinsi FROM propinsi p{$whereSql} ORDER BY p.kd_propinsi ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbkabupaten($keyword = '', $kdPropinsi = '')
    {
        $where = [];
        $params = [];

        if ($kdPropinsi !== '') {
            $where[] = 'CAST(k.kd_propinsi AS VARCHAR) = ?';
            $params[] = (string)$kdPropinsi;
        }else{
            $where[] = 'CAST(k.kd_propinsi AS VARCHAR) = 0';
        }

        if ($keyword !== '') {
            $where[] = '(CAST(k.kd_kabupaten AS VARCHAR) ILIKE ? OR k.nama_kabupaten ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT k.kd_kabupaten, k.kd_propinsi, k.nama_kabupaten FROM kabupaten k{$whereSql} ORDER BY k.kd_kabupaten ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbkecamatan($keyword = '', $kdKabupaten = '')
    {
        $where = [];
        $params = [];

        if ($kdKabupaten !== '') {
            $where[] = 'CAST(k.kd_kabupaten AS VARCHAR) = ?';
            $params[] = (string)$kdKabupaten;
        }else{
            $where[] = 'CAST(k.kd_kabupaten AS VARCHAR) = ?';
            $params[] = (string)0;
        }

        if ($keyword !== '') {
            $where[] = '(CAST(k.kd_kecamatan AS VARCHAR) ILIKE ? OR k.nama_kecamatan ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT k.kd_kecamatan, k.kd_kabupaten, k.nama_kecamatan FROM kecamatan k{$whereSql} ORDER BY k.kd_kecamatan ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbkelurahan($keyword = '', $kdKecamatan = '')
    {
        $where = [];
        $params = [];

        if ($kdKecamatan !== '') {
            $where[] = 'CAST(k.kd_kecamatan AS VARCHAR) = ?';
            $params[] = (string)$kdKecamatan;
        }else{
            $where[] = 'CAST(k.kd_kecamatan AS VARCHAR) = ?';
            $params[] = (string)0;
        }

        if ($keyword !== '') {
            $where[] = '(CAST(k.kd_kelurahan AS VARCHAR) ILIKE ? OR k.nama_kelurahan ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT k.kd_kelurahan, k.kd_kecamatan, k.nama_kelurahan FROM kelurahan k{$whereSql} ORDER BY k.kd_kelurahan ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbidentitas($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(i.kd_identitas AS VARCHAR) ILIKE ? OR i.nama_identitas ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT i.kd_identitas, i.nama_identitas FROM identitas i{$whereSql} ORDER BY i.kd_identitas ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbdokter($keyword = '')
    {
        $where = ["pm.kd_jabatanparamedis = '1'", "pm.status_aktif = '1'"];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(pm.kd_paramedis AS VARCHAR) ILIKE ? OR pm.nama_paramedis ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $sql = "SELECT pm.kd_paramedis, pm.nama_paramedis FROM para_medis pm WHERE " . implode(' AND ', $where) . " ORDER BY pm.nama_paramedis ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbdokterkirim($keyword = '')
    {
        $where = ["pm.kd_jabatanparamedis = '1'", "pm.status_aktif = '1'", "pm.kd_paramedis NOT IN ('601', '573')"];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(pm.kd_paramedis AS VARCHAR) ILIKE ? OR pm.nama_paramedis ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $sql = "SELECT pm.kd_paramedis, pm.nama_paramedis FROM para_medis pm WHERE " . implode(' AND ', $where) . " ORDER BY pm.nama_paramedis ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbinstalasi($keyword = '', $in = '', $visibleOnly = false)
    {
        $where = [];
        $params = [];

        if ($visibleOnly) {
            $where[] = 'i."view" IS TRUE';
        }

        if ($keyword !== '') {
            $where[] = '(CAST(i.kd_instalasi AS VARCHAR) ILIKE ? OR i.nama_instalasi ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($in !== '') {
             $where[] = 'i.kd_instalasi IN ('.$in.')';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT i.kd_instalasi, i.nama_instalasi FROM instalasi i{$whereSql} ORDER BY i.kd_instalasi ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbunit($keyword = '', $kdInstalasi = '')
    {
        $where = [
            'u.status_aktif = 1'
        ];

        $params = [];

        if ($kdInstalasi !== '') {
            $where[] = 'CAST(u.kd_instalasi AS VARCHAR) = ?';
            $params[] = (string) $kdInstalasi;
        }

        if (trim($keyword) !== '') {
            $where[] = '(
                CAST(u.kd_unit AS VARCHAR) ILIKE ?
                OR u.nama_unit ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = ' WHERE ' . implode(' AND ', $where);

        $sql = "
            SELECT
                u.kd_unit,
                u.kd_instalasi,
                u.nama_unit
            FROM unit u
            {$whereSql}
            ORDER BY u.nama_unit ASC
        ";

        $rows = $this->db
            ->query($sql, $params)
            ->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbuser($dataset = [])
    {
        $keyword = trim((string)($dataset['keyword'] ?? ''));
        $in      = trim((string)($dataset['in'] ?? ''));
        $jabatan = trim((string)($dataset['jabatan'] ?? ''));

        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(u.kd_user AS VARCHAR) ILIKE ? OR u.nama_user ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($in !== '') {
            $where[] = 'u.kd_user IN (' . $in . ')';
        }
        if ($jabatan !== '') {
            $where[] = 'u.jabatan ILIKE ?';
            $params[] = '%' . $jabatan . '%';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT u.kd_user, u.nama_user FROM users u{$whereSql} ORDER BY u.nama_user ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbkelpenjamin($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(k.kd_kelpenjamin AS VARCHAR) ILIKE ? OR k.nama_kelpenjamin ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT k.kd_kelpenjamin, k.nama_kelpenjamin FROM kelompok_penjamin k{$whereSql} ORDER BY k.kd_kelpenjamin ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbpenjamin($keyword = '', $kd_kelpenjamin = '')
    {
        $where = [];
        $params = [];

        if ($kd_kelpenjamin !== '') {
            $where[] = 'd.kd_kelpenjamin = ?';
            $params[] = $kd_kelpenjamin;
        }

        if ($keyword !== '') {
            $where[] = '(
                CAST(d.kd_detkelpenjamin AS VARCHAR) ILIKE ?
                OR d.nama_penjamin ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where)
            ? ''
            : (' WHERE ' . implode(' AND ', $where));

        $sql = "
            SELECT
                d.kd_detkelpenjamin,
                TRIM(d.nama_penjamin) AS nama_penjamin
            FROM detail_kelpenjamin d
            {$whereSql}
            ORDER BY d.kd_detkelpenjamin ASC
        ";

        $rows = $this->db
            ->query($sql, $params)
            ->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbperusahaan($keyword = '', $kdKelpenjamin = '')
    {
        $where = ["d.status_aktif = '1'"];
        $params = [];

        if ($kdKelpenjamin !== '') {
            $where[] = 'CAST(d.kd_kelpenjamin AS VARCHAR) = ?';
            $params[] = (string)$kdKelpenjamin;
        }

        if ($keyword !== '') {
            $where[] = '(CAST(d.kd_detkelpenjamin AS VARCHAR) ILIKE ? OR d.nama_penjamin ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $sql = "SELECT d.kd_detkelpenjamin, d.kd_kelpenjamin, d.nama_penjamin FROM detail_kelpenjamin d WHERE " . implode(' AND ', $where) . " ORDER BY d.nama_penjamin ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbpenanggungjwb($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(p.kd_jabatanpenanggungjwb AS VARCHAR) ILIKE ? OR p.jabatan_penanggungjwb ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT p.kd_jabatanpenanggungjwb, p.jabatan_penanggungjwb FROM jabatan_penanggungjwb p{$whereSql} ORDER BY p.kd_jabatanpenanggungjwb ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbrujukanasal($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(r.kd_rujukanasal AS VARCHAR) ILIKE ? OR r.nama_rujukanasal ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT r.kd_rujukanasal, r.nama_rujukanasal FROM rujukan_asal r{$whereSql} ORDER BY r.kd_rujukanasal ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbrujukandetail($keyword = '', $kdRujukanAsal = '')
    {
        $where = [];
        $params = [];

        if ($kdRujukanAsal !== '') {
            $where[] = 'CAST(r.kd_rujukanasal AS VARCHAR) = ?';
            $params[] = (string)$kdRujukanAsal;
        }

        if ($keyword !== '') {
            $where[] = '(CAST(r.kd_rujukan AS VARCHAR) ILIKE ? OR r.nama_rujukan ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT r.kd_rujukan, r.kd_rujukanasal, r.nama_rujukan, r.alamat, kab.nama_kabupaten FROM rujukan_detail r LEFT JOIN kabupaten kab ON kab.kd_kabupaten = r.kd_kabupaten{$whereSql} ORDER BY r.nama_rujukan ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbtujuankunjungan($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(t.kd_tujuan_kunjungan AS VARCHAR) ILIKE ? OR t.tujuan_kunjungan ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT t.kd_tujuan_kunjungan, t.tujuan_kunjungan FROM bpjs_tujuan_kunjungan t{$whereSql} ORDER BY t.kd_tujuan_kunjungan ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }
    
    public function getDataPasien($dataset)
    {
        $tabel = isset($dataset['tabel']) ? $dataset['tabel'] : 'rawat_jalan_rme.status_pasien';
        $params = array($dataset['no_kunjungan']);
        $query = "SELECT p.nama_pasien, p.tanggal_lahir tgl_lahir, wn.nama_warganegara kebangsaan, p.alamat, p.no_bpjs, p.telpon telepon, p.nomer_identitas nik, pk.nama_pekerjaan, k.tgl_masuk, k.tgl_kontrol, k.no_medicalrecord, pm.nama_paramedis, u.nama_unit, u.kd_unit, u.kd_instalasi, concat ( date_part( 'year', age( p.tanggal_lahir ) ), ' thn ', date_part( 'month', age( p.tanggal_lahir ) ), ' bln ', date_part( 'day', age( p.tanggal_lahir ) ), ' hr' ) umur, case when p.kd_jenis = 0 then 'P' else 'L' end gender, kab.nama_kabupaten kabupaten, sk.nama_statuskeluar, sp.tgl_keluar, k.tgl_keluar_rm, k.kd_detkelpenjamin, dkp.nama_penjamin, rp.nama_ruangperawatan ruangan
            from kunjungan k
            inner join pasien p on p.no_medicalrecord = k.no_medicalrecord
            inner join para_medis pm on pm.kd_paramedis = k.kd_paramedis
            inner join unit u on u.kd_unit = k.kd_unit
            inner join warga_negara wn on wn.kd_warganegara = p.kd_warganegara 
            inner join pekerjaan pk on pk.kd_pekerjaan = p.kd_pekerjaan 
            inner join kabupaten kab on kab.kd_kabupaten = p.kd_kabupaten 
            inner join detail_kelpenjamin dkp on dkp.kd_detkelpenjamin = k.kd_detkelpenjamin
            left join {$tabel} sp on sp.single_id = k.single_id and sp.no_registrasikunjungan = k.no_registrasikunjungan
            left join status_keluar sk on sk.kd_statuskeluar = sp.kd_statuskeluar
            left join history_gantikamar hg on hg.no_registrasikunjungan = k.no_registrasikunjungan 
            left join kamar_perawatan kp on kp.kd_kamarperawatan = hg.kd_kamarperawatan 
            left join unit uk on uk.kd_unit = kp.kd_unit
            left join ruang_perawatan rp on rp.kd_ruangperawatan = uk.kd_ruangperawatan
            where k.no_registrasikunjungan = ?
        ";
        // $db->log($query,$params);
        $result = $this->db->query($query, $params)->getResultArray();
        return $result;
    }

    public function mgetcmbshift($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = ' s.shift = ? ';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = implode(' AND ', $where);
        }

        $sql = "SELECT s.shift FROM shift s  WHERE status_aktif = 1 {$whereSql} ORDER BY s.shift ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetCaraKeluar($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(k.kd_statuskeluar AS VARCHAR) ILIKE ? OR k.nama_statuskeluar ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));
        $sql = "SELECT k.kd_statuskeluar, k.nama_statuskeluar FROM status_keluar k{$whereSql} ORDER BY k.kd_statuskeluar ASC";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbdiagnosa($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(
                CAST(i.kd_icdx AS VARCHAR) ILIKE ?
                OR i.penyakit ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where)
            ? ''
            : (' WHERE ' . implode(' AND ', $where));

        $sql = "
            SELECT
                i.kd_icdx,
                i.penyakit
            FROM icdx i
            {$whereSql}
            ORDER BY i.kd_icdx ASC
            LIMIT 10
        ";

        $rows = $this->db
            ->query($sql, $params)
            ->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetJenisAkses($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(
                CAST(j.kd_jenis_akses AS VARCHAR) ILIKE ?
                OR j.nama_jenis_akses ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where)
            ? ''
            : (' WHERE ' . implode(' AND ', $where));

        $sql = "
            SELECT
                j.kd_jenis_akses,
                j.nama_jenis_akses
            FROM hd_jenis_akses j
            {$whereSql}
            ORDER BY j.kd_jenis_akses ASC
        ";

        $rows = $this->db
            ->query($sql, $params)
            ->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }


    public function mgetRujukanAsal($keyword = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(
                CAST(r.kd_rujukanasal AS VARCHAR) ILIKE ?
                OR r.nama_rujukanasal ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where)
            ? ''
            : (' WHERE ' . implode(' AND ', $where));

        $sql = "
            SELECT
                r.kd_rujukanasal,
                r.nama_rujukanasal
            FROM rujukan_asal r
            {$whereSql}
            ORDER BY r.kd_rujukanasal ASC
        ";

        $rows = $this->db
            ->query($sql, $params)
            ->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }


    public function mgetRujukanDetail($kd_rujukanasal = '', $keyword = '')
    {
        $where = [];
        $params = [];

        if ($kd_rujukanasal !== '') {
            $where[] = 'a.kd_rujukanasal = ?';
            $params[] = $kd_rujukanasal;
        }

        if ($keyword !== '') {
            $where[] = '(
                CAST(a.kd_rujukan AS VARCHAR) ILIKE ?
                OR a.nama_rujukan ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = empty($where)
            ? ''
            : (' WHERE ' . implode(' AND ', $where));

        $sql = "
            SELECT
                a.kd_rujukan,
                a.nama_rujukan,
                a.alamat,
                b.nama_kabupaten
            FROM rujukan_detail a
            JOIN kabupaten b
                ON a.kd_kabupaten = b.kd_kabupaten
            {$whereSql}
            ORDER BY a.nama_rujukan ASC
        ";

        $rows = $this->db
            ->query($sql, $params)
            ->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetRuangPerawatan($keyword = '')
    {
        $where = [
            'rp.status_aktif = 1'
        ];

        $params = [];

        if ($keyword !== '') {
            $where[] = '(
                CAST(rp.kd_ruangperawatan AS VARCHAR) ILIKE ?
                OR TRIM(rp.nama_ruangperawatan) ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql = ' WHERE ' . implode(' AND ', $where);

        $sql = "
            SELECT
                rp.kd_ruangperawatan,
                TRIM(rp.nama_ruangperawatan) AS nama_ruangperawatan
            FROM ruang_perawatan rp
            {$whereSql}
            ORDER BY
                TRIM(rp.nama_ruangperawatan) ASC
        ";

        $rows = $this->db
            ->query($sql, $params)
            ->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetCaraPemesanan($keyword = '')
    {
        $where = [
            'cp.kd_carapemesanan IN (6,7)'
        ];

        $params = [];

        if ($keyword !== '') {
            $where[] = '(
                CAST(cp.kd_carapemesanan AS VARCHAR) ILIKE ?
                OR cp.nama_carapemesanan ILIKE ?
            )';

            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $whereSql =
            ' WHERE ' . implode(
                ' AND ',
                $where
            );

        $sql = "
            SELECT
                cp.kd_carapemesanan,
                cp.nama_carapemesanan
            FROM cara_pemesanan cp
            {$whereSql}
            ORDER BY
                cp.kd_carapemesanan ASC
        ";

        $rows =
            $this->db
                ->query(
                    $sql,
                    $params
                )
                ->getResultArray();

        return [
            'code' =>
                count($rows) > 0
                    ? 200
                    : 204,

            'data' =>
                $rows
        ];
    }

    public function mgetcmbbayar($keyword = '', $in = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(kd_jenispembayaran AS VARCHAR) ILIKE ? OR nama_jenispembayaran ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($in !== '') {
            $where[] = 'kd_jenispembayaran IN (' . $in . ')';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));

        $sql = "SELECT * FROM (
                    SELECT
                        string_agg(kd_jenispembayaran::text, ',') kd_jenispembayaran,
                        'TUNAI' nama_jenispembayaran,
                        kd_kelompok_pembayaran,
                        0 kd_detkelpenjamin
                    FROM jenis_pembayaran
                    WHERE kd_jenispembayaran != 0 AND kd_kelompok_pembayaran = 1
                    GROUP BY kd_kelompok_pembayaran

                    UNION ALL

                    SELECT
                        kd_jenispembayaran::text kd_jenispembayaran,
                        nama_jenispembayaran,
                        kd_kelompok_pembayaran,
                        kd_detkelpenjamin
                    FROM jenis_pembayaran
                    WHERE kd_jenispembayaran != 0 AND kd_kelompok_pembayaran != 1
                ) a{$whereSql}
                ORDER BY kd_kelompok_pembayaran, nama_jenispembayaran";
        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbcounter($keyword = '', $in = '')
    {
        $where = [];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(CAST(a.kd_counter AS VARCHAR) ILIKE ? OR b.nama_unit ILIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($in !== '') {
            $where[] = 'a.kd_counter IN (' . $in . ')';
        }

        $whereSql = empty($where) ? '' : (' WHERE ' . implode(' AND ', $where));

        $sql = "SELECT 
                    a.kd_counter, 
                    TRIM(b.nama_unit) AS unit,
                    TRIM(b.nama_unit) AS nama_counter
                FROM counter a
                INNER JOIN unit b ON b.kd_unit = a.kd_unit
                {$whereSql}
                ORDER BY a.kd_counter ASC";

        $rows = $this->db->query($sql, $params)->getResultArray();

        return [
            'code' => count($rows) > 0 ? 200 : 204,
            'data' => $rows,
        ];
    }

    public function mgetcmbkelurahanGrid($keyword = '')
    {
        $search = "l.nama_kelurahan || ' ' || c.nama_kecamatan || ' ' || b.nama_kabupaten || ' ' || p.nama_propinsi";
        $params = ['%' . $keyword . '%'];
        $where = "($search) ILIKE ?";
        $rows = $this->db->query("SELECT $search AS searchtext,
            l.kd_kelurahan, l.nama_kelurahan, c.kd_kecamatan, c.nama_kecamatan,
            b.kd_kabupaten, b.nama_kabupaten, p.kd_propinsi, p.nama_propinsi
            FROM kelurahan l
            JOIN kecamatan c ON c.kd_kecamatan = l.kd_kecamatan
            JOIN kabupaten b ON b.kd_kabupaten = c.kd_kabupaten
            JOIN propinsi p ON p.kd_propinsi = b.kd_propinsi
            WHERE $where ORDER BY l.nama_kelurahan, l.kd_kelurahan LIMIT 25", $params)->getResultArray();
        return ['code' => count($rows) > 0 ? 200 : 204, 'data' => $rows];
    }
    public function getReferensiBarangFarmasi()
    {
        // Table and column names are fixed here, never supplied by the client.
        $sources = [
            'golongan' => ['apotek_golongan', 'kd_golongan', 'golongan'],
            'jenis' => ['apotek_jenis', 'kd_jenis', 'jenis'],
            'subjenis' => ['apotek_sub_jenis', 'kd_sub_jenis', 'sub_jenis'],
            'subjenis_2' => ['apotek_sub_jenis_2', 'kd_sub_jenis_2', 'sub_jenis_2'],
            'jenis_morphine' => ['apotek_morphine', 'kd_morphine', 'nama_morphine'],
            'pabrik' => ['apotek_pabrik', 'kd_pabrik', 'nama'],
            'status_barang' => ['apotek_status_obat', 'kd_status_obat', 'status_obat'],
            'kelas_terapi' => ['apotek_jenis_terapi', 'kd_jenis_terapi', 'jenis_terapi'],
            'sub_kelas_terapi' => ['apotek_subkelas_terapi', 'kd_subkelas_terapi', 'subkelas_terapi'],
            'komposisi' => ['apotek_komposisi', 'kd_komposisi', 'komposisi'],
            'group' => ['apotek_group_obat', 'kd_group', 'nama'],
            'obat_dasar' => ['apotek_obat_dasar', 'kd_master_dasar', 'master_dasar'],
            'satuan_kecil' => ['apotek_satuan_kecil', 'kd_satuan_kecil', 'satuan_kecil'],
            'satuan_besar' => ['apotek_satuan_besar', 'kd_satuan_besar', 'satuan_besar'],
            'satuan_terkecil' => ['apotek_satuan_terkecil', 'kd_satuan_terkecil', 'satuan_terkecil'],
            'formularium' => ['apotek_formularium', 'kd_formularium', 'formularium'],
            'status_fornas' => ['apotek_status_fornas', 'kd_status_fornas', 'status_fornas'],
            'status_obat_kronis' => ['apotek_status_obat_kronis', 'kd_status_obat_kronis', 'status_obat'],
            'status_pdn' => ['apotek_pdn', 'kd_pdn', 'nama_pdn'],
            'sumber_tkdn' => ['apotek_sumber_tkdn', 'kd_sumber_tkdn', 'sumber_tkdn'],
        ];
        $result = [];
        foreach ($sources as $field => [$table, $key, $label]) {
            $result[$field] = $this->db->query("select {$key} as value, {$label} as label from {$table} order by {$label}")->getResultArray();
        }
        return $result;
    }

    public function getBarangFarmasi($params = [])
    {
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = min(100, max(1, (int)($params['limit'] ?? 10)));
        $where = ["status_hapus = '0'"];
        $binds = [];
        foreach (['kd_obat', 'kd_komposisi', 'group_obat', 'status_aktif'] as $field) {
            if (isset($params[$field]) && (string)$params[$field] !== '') {
                $where[] = "{$field} = ?";
                $binds[] = $params[$field];
            }
        }
        $keyword = trim((string)($params['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = '(kd_obat ilike ? or nama_obat ilike ?)';
            $binds[] = '%' . $keyword . '%';
            $binds[] = '%' . $keyword . '%';
        }
        $filter = implode(' and ', $where);
        $total = (int)$this->db->query("select count(*) as total from apotek_obat where {$filter}", $binds)->getRow()->total;
        $page = min($page, max(1, (int)ceil($total / $limit)));
        $offset = ($page - 1) * $limit;
        $rows = $this->db->query("select * from apotek_obat where {$filter} order by nama_obat, kd_obat limit {$limit} offset {$offset}", $binds)->getResultArray();
        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit];
    }
    public function getAkunTransaksi($keyword = '')
{
    $sql = 'SELECT kd_akun, nama_akun
            FROM akuntansi_akun
            WHERE status_aktif = 1 AND status_akun_transaksi = 1';
    $binds = [];

    if ($keyword !== '') {
        $sql .= ' AND (CAST(kd_akun AS VARCHAR) ILIKE ? OR nama_akun ILIKE ?)';
        $binds = ['%' . $keyword . '%', '%' . $keyword . '%'];
    }

    return $this->db->query(
        $sql . ' ORDER BY kd_akun',
        $binds
    )->getResultArray();
}

public function getVendorFarmasi($params = [])
{
    $page = max(1, (int)($params['page'] ?? 1));
    $limit = min(100, max(1, (int)($params['limit'] ?? 10)));
    $where = ['1 = 1'];
    $binds = [];

    if (trim((string)($params['kode'] ?? '')) !== '') {
        $where[] = 'CAST(a.kd_pbf AS VARCHAR) = ?';
        $binds[] = trim((string)$params['kode']);
    }

    if (trim((string)($params['keyword'] ?? '')) !== '') {
        $where[] = 'a.nama ILIKE ?';
        $binds[] = '%' . trim((string)$params['keyword']) . '%';
    }

    $filter = implode(' AND ', $where);
    $total = (int)$this->db->query(
        "SELECT COUNT(*) AS total FROM apotek_pbf a WHERE {$filter}",
        $binds
    )->getRow()->total;

    $page = min($page, max(1, (int)ceil($total / $limit)));
    $offset = ($page - 1) * $limit;

    $sql = "SELECT a.*, pro.nama_propinsi, kab.nama_kabupaten,
                   kec.nama_kecamatan,
                   debit.nama_akun AS akun_debit,
                   kredit.nama_akun AS akun_kredit
            FROM apotek_pbf a
            LEFT JOIN propinsi pro ON pro.kd_propinsi = a.kd_propinsi
            LEFT JOIN kabupaten kab ON kab.kd_kabupaten = a.kd_kabupaten
            LEFT JOIN kecamatan kec ON kec.kd_kecamatan = a.kd_kecamatan
            LEFT JOIN akuntansi_akun debit ON debit.kd_akun = a.kd_akun
            LEFT JOIN akuntansi_akun kredit ON kredit.kd_akun = a.kd_akun_kredit
            WHERE {$filter}
            ORDER BY a.kd_pbf
            LIMIT {$limit} OFFSET {$offset}";

    return [
        'rows' => $this->db->query($sql, $binds)->getResultArray(),
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
    ];
}


public function saveDefaultSetting($menu, $kdUser, $values): array
    {
        $scope = $this->defaultSettingScope($menu, $kdUser);
        if (!is_array($values) || !$values) throw new \DomainException('Daftar setting tidak valid.');
        if ((int) $kdUser !== 0 && !$this->db->query('SELECT kd_user FROM users WHERE kd_user=?', [$kdUser])->getRowArray()) {
            throw new \DomainException('Pengguna tidak ditemukan.');
        }
        foreach ($values as $key => $value) {
            if (!in_array($key, $scope['components'], true) || !is_string($value) || strlen($value) > 255) {
                throw new \DomainException('Komponen atau nilai setting tidak valid.');
            }
            $kind = $scope['scopes'][$key] ?? 'global';
            if (strpos($menu, 'farmasi_') === 0 && $kind === 'global') {
                // Farmasi global disimpan berdasarkan flag global=true, bukan kd_user=0.
            } elseif ($kind === 'global' && (int) $kdUser !== 0) {
                throw new \DomainException('Komponen global hanya dapat disimpan pada kd_user = 0.');
            }
            if ($kind === 'user' && (int) $kdUser === 0) {
                throw new \DomainException('Komponen per pengguna harus disimpan pada kd_user login.');
            }
            if ($menu === 'kasir' && $key === 'COUNTER' && trim($value) !== '') {
                $counterInduk = $this->db->query(
                    "SELECT setting FROM setting WHERE komponen='KD_COUNTER_INDUK_KASIR' AND global=true LIMIT 1"
                )->getRowArray();
                $counterIndukValue = trim((string) ($counterInduk['setting'] ?? ''));
                if ($counterIndukValue === '') throw new \RuntimeException('Setting global KD_COUNTER_INDUK_KASIR belum dikonfigurasi.');
                $exists = $this->db->query(
                    "SELECT kd_counter FROM counter WHERE CAST(kd_counter AS text)=? AND CAST(kd_counter_induk AS text)=? AND status_aktif=1",
                    [trim($value), $counterIndukValue],
                )->getRowArray();
                if (!$exists) throw new \DomainException('Counter kasir tidak valid atau tidak aktif.');
            }
        }
        if (!$this->db->transBegin()) throw new \RuntimeException('Transaksi gagal dimulai.');
        try {
            if (!$this->db->query('LOCK TABLE setting IN SHARE ROW EXCLUSIVE MODE')) throw new \RuntimeException('Setting gagal dikunci.');
            foreach ($values as $key => $value) {
                $kind = $scope['scopes'][$key] ?? 'global';
                $where = strpos($menu, 'farmasi_') === 0 && $kind === 'global' ? ['komponen' => $key, 'global' => true] : ['kd_user' => $kdUser, 'komponen' => $key];
                $exists = $this->db->table('setting')->where($where)->countAllResults() > 0;
                $ok = $exists
                    ? $this->db->table('setting')->where($where)->update(['setting' => trim($value)])
                    : $this->db->table('setting')->insert($where + ['setting' => trim($value)]);
                if (!$ok) throw new \RuntimeException('Setting gagal disimpan.');
            }
            if (!$this->db->transStatus() || !$this->db->transCommit()) throw new \RuntimeException('Setting gagal disimpan.');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
        return ['message' => 'Default setting berhasil disimpan.'];
    }

    public function getDefaultSetting($menu, $kdUser): array
    {
        if (!is_scalar($kdUser) || !ctype_digit((string) $kdUser) || (int) $kdUser < 0) {
            throw new \DomainException('Kode pengguna tidak valid.');
        }
        if (!is_string($menu)) throw new \DomainException('Menu default setting tidak didukung.');
        if ($menu === 'pendaftaran') return $this->getDefaultPendaftaran((string) $kdUser);
        if ($menu === 'kasir') return $this->getDefaultKasir((string) $kdUser);
        if ($menu === 'farmasi_mutasi') return $this->getDefaultMutasi((string) $kdUser);
        if ($menu === 'farmasi_penyesuaian_stok') return $this->getDefaultPenyesuaianStok((string) $kdUser);
        if ($menu === 'farmasi_info_stok_harga') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_INFO_HARGA_OBAT_GLOBAL, []);
        if ($menu === 'farmasi_pelayanan_resep') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PELAYANAN_RESEP_GLOBAL, self::DEFAULT_PELAYANAN_RESEP_USER);
        if ($menu === 'farmasi_pengembalian_resep') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PENGEMBALIAN_RESEP_GLOBAL, self::DEFAULT_PENGEMBALIAN_RESEP_USER);
        if ($menu === 'farmasi_farmasi_klinis') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_FARMASI_KLINIS_GLOBAL, self::DEFAULT_FARMASI_KLINIS_USER);
        if ($menu === 'farmasi_penerimaan') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PENERIMAAN_GLOBAL, self::DEFAULT_PENERIMAAN_USER);
        if ($menu === 'farmasi_permintaan_unit') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PERMINTAAN_UNIT_GLOBAL, self::DEFAULT_PERMINTAAN_UNIT_USER);
        if ($menu === 'farmasi_permintaan_kebutuhan') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PERMINTAAN_KEBUTUHAN_GLOBAL, self::DEFAULT_PERMINTAAN_KEBUTUHAN_USER);
        if ($menu === 'farmasi_rencana_kebutuhan') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_RENCANA_KEBUTUHAN_GLOBAL, self::DEFAULT_RENCANA_KEBUTUHAN_USER);
        if ($menu === 'farmasi_rencana_pengadaan') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_RENCANA_PENGADAAN_GLOBAL, self::DEFAULT_RENCANA_PENGADAAN_USER);
        if ($menu === 'farmasi_pemanggil_antrian') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PEMANGGIL_ANTRIAN_GLOBAL, self::DEFAULT_PEMANGGIL_ANTRIAN_USER);
        if ($menu === 'farmasi_penghapusan') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PENGHAPUSAN_GLOBAL, self::DEFAULT_PENGHAPUSAN_USER);
        if ($menu === 'farmasi_produksi') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PRODUKSI_GLOBAL, self::DEFAULT_PRODUKSI_USER);
        if ($menu === 'farmasi_pengembalian') return $this->getDefaultFarmasi((string) $kdUser, self::DEFAULT_PENGEMBALIAN_PBF_GLOBAL, self::DEFAULT_PENGEMBALIAN_PBF_USER);
        throw new \DomainException('Menu default setting tidak didukung.');
    }
    private function defaultSettingScope($menu, $kdUser): array
    {
        if (!is_string($menu)) throw new \DomainException('Menu default setting tidak didukung.');
        if ($menu === 'pendaftaran') {
            return [
                'components' => self::DEFAULT_PENDAFTARAN,
                'scopes' => array_fill_keys(self::DEFAULT_PENDAFTARAN, 'global'),
            ];
        }
        if ($menu === 'kasir') {
            $components = array_values(array_unique(array_merge(self::DEFAULT_KASIR_GLOBAL, self::DEFAULT_KASIR_USER)));
            $scopes = array_fill_keys(self::DEFAULT_KASIR_GLOBAL, 'global') + array_fill_keys(self::DEFAULT_KASIR_USER, 'user');
            return ['components' => $components, 'scopes' => $scopes];
        }
        if ($menu === 'farmasi_mutasi') {
            $components = array_values(array_unique(array_merge(self::DEFAULT_MUTASI_GLOBAL, self::DEFAULT_MUTASI_USER)));
            $scopes = array_fill_keys(self::DEFAULT_MUTASI_GLOBAL, 'global') + array_fill_keys(self::DEFAULT_MUTASI_USER, 'user');
            return ['components' => $components, 'scopes' => $scopes];
        }
        if ($menu === 'farmasi_penyesuaian_stok') {
            $components = array_values(array_unique(array_merge(self::DEFAULT_PENYESUAIAN_STOK_GLOBAL, self::DEFAULT_PENYESUAIAN_STOK_USER)));
            $scopes = array_fill_keys(self::DEFAULT_PENYESUAIAN_STOK_GLOBAL, 'global') + array_fill_keys(self::DEFAULT_PENYESUAIAN_STOK_USER, 'user');
            return ['components' => $components, 'scopes' => $scopes];
        }
        if ($menu === 'farmasi_info_stok_harga') return $this->farmasiDefaultScope(self::DEFAULT_INFO_HARGA_OBAT_GLOBAL, []);
        if ($menu === 'farmasi_pelayanan_resep') {
            return $this->farmasiDefaultScope(self::DEFAULT_PELAYANAN_RESEP_GLOBAL, self::DEFAULT_PELAYANAN_RESEP_USER);
        }
        if ($menu === 'farmasi_pengembalian_resep') {
            return $this->farmasiDefaultScope(self::DEFAULT_PENGEMBALIAN_RESEP_GLOBAL, self::DEFAULT_PENGEMBALIAN_RESEP_USER);
        }
        if ($menu === 'farmasi_farmasi_klinis') {
            return $this->farmasiDefaultScope(self::DEFAULT_FARMASI_KLINIS_GLOBAL, self::DEFAULT_FARMASI_KLINIS_USER);
        }
        if ($menu === 'farmasi_penerimaan') {
            return $this->farmasiDefaultScope(self::DEFAULT_PENERIMAAN_GLOBAL, self::DEFAULT_PENERIMAAN_USER);
        }
        if ($menu === 'farmasi_permintaan_unit') {
            return $this->farmasiDefaultScope(self::DEFAULT_PERMINTAAN_UNIT_GLOBAL, self::DEFAULT_PERMINTAAN_UNIT_USER);
        }
        if ($menu === 'farmasi_permintaan_kebutuhan') {
            return $this->farmasiDefaultScope(self::DEFAULT_PERMINTAAN_KEBUTUHAN_GLOBAL, self::DEFAULT_PERMINTAAN_KEBUTUHAN_USER);
        }
        if ($menu === 'farmasi_rencana_kebutuhan') {
            return $this->farmasiDefaultScope(self::DEFAULT_RENCANA_KEBUTUHAN_GLOBAL, self::DEFAULT_RENCANA_KEBUTUHAN_USER);
        }
        if ($menu === 'farmasi_rencana_pengadaan') {
            return $this->farmasiDefaultScope(self::DEFAULT_RENCANA_PENGADAAN_GLOBAL, self::DEFAULT_RENCANA_PENGADAAN_USER);
        }
        if ($menu === 'farmasi_pemanggil_antrian') {
            return $this->farmasiDefaultScope(self::DEFAULT_PEMANGGIL_ANTRIAN_GLOBAL, self::DEFAULT_PEMANGGIL_ANTRIAN_USER);
        }
        if ($menu === 'farmasi_penghapusan') {
            return $this->farmasiDefaultScope(self::DEFAULT_PENGHAPUSAN_GLOBAL, self::DEFAULT_PENGHAPUSAN_USER);
        }
        if ($menu === 'farmasi_produksi') return $this->farmasiDefaultScope(self::DEFAULT_PRODUKSI_GLOBAL, self::DEFAULT_PRODUKSI_USER);
        if ($menu === 'farmasi_pengembalian') return $this->farmasiDefaultScope(self::DEFAULT_PENGEMBALIAN_PBF_GLOBAL, self::DEFAULT_PENGEMBALIAN_PBF_USER);
        throw new \DomainException('Menu default setting tidak didukung.');
    }
    private function farmasiDefaultScope(array $global, array $user): array
    {
        $components = array_values(array_unique(array_merge($global, $user)));
        $scopes = array_fill_keys($global, 'global') + array_fill_keys($user, 'user');
        return ['components' => $components, 'scopes' => $scopes];
    }

    private function getDefaultFarmasi(string $kdUser, array $global, array $user): array
    {
        $globalRows = $global
            ? $this->db->table('setting')->select('komponen,setting')->where('global', true)->whereIn('komponen', $global)->get()->getResultArray()
            : [];
        $userRows = $user
            ? $this->db->table('setting')->select('komponen,setting')->where('kd_user', $kdUser)->whereIn('komponen', $user)->get()->getResultArray()
            : [];
        $globalDefaults = [];
        foreach ($globalRows as $row) $globalDefaults[$row['komponen']] = trim((string) $row['setting']);
        $userDefaults = [];
        foreach ($userRows as $row) $userDefaults[$row['komponen']] = trim((string) $row['setting']);
        $defaults = $globalDefaults + $userDefaults;
        $scope = $this->farmasiDefaultScope($global, $user);
        return [
            'defaults' => $defaults,
            'stored_defaults' => (object) $defaults,
            'stored_global' => (object) $globalDefaults,
            'stored_user' => (object) $userDefaults,
            'components' => $scope['components'],
            'scopes' => (object) $scope['scopes'],
        ];
    }

    private function getDefaultPendaftaran(string $kdUser): array
    {
        $settings = $this->db->table('setting')
            ->select('komponen,setting')
            ->where('kd_user', $kdUser)
            ->whereIn('komponen', self::DEFAULT_PENDAFTARAN)
            ->get()->getResultArray();
        $defaults = [];
        foreach ($settings as $row) $defaults[$row['komponen']] = trim((string) $row['setting']);
        $storedDefaults = $defaults;
        $installations = preg_split('/[^0-9]+/', $defaults['CMB_INSTALASI'] ?? '', -1, PREG_SPLIT_NO_EMPTY);
        $installation = $defaults['CMB_INSTALASI_DEFAULT'] ?? ($installations[0] ?? '');
        if (ctype_digit($installation)) {
            $defaults['CMB_INSTALASI_DEFAULT'] = $installation;
            if (empty($defaults['CMB_UNIT_DEFAULT'])) {
                $unit = $this->db->query('SELECT kd_unit FROM unit WHERE kd_instalasi=? ORDER BY kd_unit LIMIT 1', [$installation])->getRowArray();
                $defaults['CMB_UNIT_DEFAULT'] = (string) ($unit['kd_unit'] ?? '');
            }
            if (empty($defaults['CMB_PARAMEDIS_DEFAULT']) && !empty($defaults['CMB_UNIT_DEFAULT'])) {
                $doctor = $this->db->query('SELECT kd_paramedis FROM paramedis_bagian WHERE kd_instalasi=? AND kd_unit=? AND tpp=1 ORDER BY kd_paramedis LIMIT 1', [$installation, $defaults['CMB_UNIT_DEFAULT']])->getRowArray();
                $defaults['CMB_PARAMEDIS_DEFAULT'] = (string) ($doctor['kd_paramedis'] ?? '');
            }
        }
        $payer = empty($defaults['CMB_NAMAPENJAMIN']) ? null : $this->db->query('SELECT kd_detkelpenjamin,kd_kelpenjamin,nama_penjamin FROM detail_kelpenjamin WHERE CAST(kd_detkelpenjamin AS text)=?', [$defaults['CMB_NAMAPENJAMIN']])->getRowArray();
        return ['defaults' => $defaults, 'penjamin_default' => $payer, 'stored_defaults' => (object) $storedDefaults, 'components' => self::DEFAULT_PENDAFTARAN];
    }
    private function getDefaultMutasi(string $kdUser): array
    {
        $globalRows = $this->db->table('setting')->select('komponen,setting')->where('global', true)->whereIn('komponen', self::DEFAULT_MUTASI_GLOBAL)->get()->getResultArray();
        $userRows = $this->db->table('setting')->select('komponen,setting')->where('kd_user', $kdUser)->whereIn('komponen', self::DEFAULT_MUTASI_USER)->get()->getResultArray();
        $globalDefaults = []; foreach ($globalRows as $row) $globalDefaults[$row['komponen']] = trim((string) $row['setting']);
        $userDefaults = []; foreach ($userRows as $row) $userDefaults[$row['komponen']] = trim((string) $row['setting']);
        $defaults = $globalDefaults + $userDefaults;
        $components = array_values(array_unique(array_merge(self::DEFAULT_MUTASI_GLOBAL, self::DEFAULT_MUTASI_USER)));
        $scopes = array_fill_keys(self::DEFAULT_MUTASI_GLOBAL, 'global') + array_fill_keys(self::DEFAULT_MUTASI_USER, 'user');
        return ['defaults' => $defaults, 'stored_defaults' => (object) $defaults, 'stored_global' => (object) $globalDefaults, 'stored_user' => (object) $userDefaults, 'components' => $components, 'scopes' => (object) $scopes];
    }

    private function getDefaultPenyesuaianStok(string $kdUser): array
    {
        $globalRows = $this->db->table('setting')->select('komponen,setting')->where('global', true)->whereIn('komponen', self::DEFAULT_PENYESUAIAN_STOK_GLOBAL)->get()->getResultArray();
        $userRows = $this->db->table('setting')->select('komponen,setting')->where('kd_user', $kdUser)->whereIn('komponen', self::DEFAULT_PENYESUAIAN_STOK_USER)->get()->getResultArray();
        $globalDefaults = []; foreach ($globalRows as $row) $globalDefaults[$row['komponen']] = trim((string) $row['setting']);
        $userDefaults = []; foreach ($userRows as $row) $userDefaults[$row['komponen']] = trim((string) $row['setting']);
        $defaults = $globalDefaults + $userDefaults;
        $components = array_values(array_unique(array_merge(self::DEFAULT_PENYESUAIAN_STOK_GLOBAL, self::DEFAULT_PENYESUAIAN_STOK_USER)));
        $scopes = array_fill_keys(self::DEFAULT_PENYESUAIAN_STOK_GLOBAL, 'global') + array_fill_keys(self::DEFAULT_PENYESUAIAN_STOK_USER, 'user');
        return ['defaults' => $defaults, 'stored_defaults' => (object) $defaults, 'stored_global' => (object) $globalDefaults, 'stored_user' => (object) $userDefaults, 'components' => $components, 'scopes' => (object) $scopes];
    }

    private function getDefaultKasir(string $kdUser): array
    {
        $globalRows = $this->db->table('setting')
            ->select('komponen,setting')
            ->where('kd_user', '0')
            ->whereIn('komponen', self::DEFAULT_KASIR_GLOBAL)
            ->get()->getResultArray();
        $userRows = $this->db->table('setting')
            ->select('komponen,setting')
            ->where('kd_user', $kdUser)
            ->whereIn('komponen', self::DEFAULT_KASIR_USER)
            ->get()->getResultArray();
        $globalDefaults = [];
        foreach ($globalRows as $row) $globalDefaults[$row['komponen']] = trim((string) $row['setting']);
        $userDefaults = [];
        foreach ($userRows as $row) $userDefaults[$row['komponen']] = trim((string) $row['setting']);
        $defaults = $globalDefaults + $userDefaults;
        $scopes = array_fill_keys(self::DEFAULT_KASIR_GLOBAL, 'global') + array_fill_keys(self::DEFAULT_KASIR_USER, 'user');
        $components = array_values(array_unique(array_merge(self::DEFAULT_KASIR_GLOBAL, self::DEFAULT_KASIR_USER)));
        return [
            'defaults' => $defaults,
            'stored_defaults' => (object) $defaults,
            'stored_global' => (object) $globalDefaults,
            'stored_user' => (object) $userDefaults,
            'components' => $components,
            'scopes' => (object) $scopes,
        ];
    }

    // Registration numbering: caller owns the transaction and required table locks.
    public function generateNoRegistrasi(\DateTimeInterface $numberDate)
    {
        $prefix = $numberDate->format('dmY');
        $seq = $this->db
            ->query(
                "SELECT COALESCE(MAX(CAST(split_part(no_registrasikunjungan,'-',2) AS bigint)),0)+1 AS n FROM kunjungan WHERE no_registrasikunjungan LIKE ? AND split_part(no_registrasikunjungan,'-',2) ~ '^[0-9]+$'",
                [$prefix . '-%'],
            )
            ->getRowArray()['n'];
        $reg = $prefix . '-' . $seq;

        return $reg;
    }

    public function generateSingleId(string $date)
    {
        $n = $this->db
            ->query(
                'SELECT COALESCE(MAX(CAST(SUBSTRING(CAST(single_id AS text),9) AS bigint)),0)+1 AS n FROM kunjungan_header WHERE CAST(tgl_masuk AS date)=?',
                [$date],
            )
            ->getRowArray()['n'];
        $single = str_replace('-', '', $date) . str_pad((string) $n, 4, '0', STR_PAD_LEFT);

        return $single;
    }

    public function generateUrutMasuk(string $rm)
    {
        $urut = (int) $this->db
            ->query(
                'SELECT COALESCE(MAX(urut_masuk),0)+1 AS n FROM kunjungan WHERE no_medicalrecord=?',
                [$rm],
            )
            ->getRowArray()['n'];

        return $urut;
    }

    public function generateNoAntrian($kdUnit, $shift, $doctor, string $date)
    {
        $queue = (int) $this->db
            ->query(
                'SELECT COALESCE(MAX(no_antrian),0)+1 AS n FROM (SELECT no_antrian FROM reservasi WHERE status=0 AND kd_unit=? AND shift=? AND kd_paramedis=? AND CAST(tgl_pesan AS date)=? UNION ALL SELECT no_antrian FROM kunjungan WHERE status=0 AND kd_unit=? AND shiftterakhir=? AND kd_paramedis=? AND CAST(tgl_masuk AS date)=?) q',
                [$kdUnit, $shift, $doctor, $date, $kdUnit, $shift, $doctor, $date],
            )
            ->getRowArray()['n'];

        return $queue;
    }

    public function generateIdBerkas(string $date, string $rm)
    {
        $this->db->query('LOCK TABLE tracer_urut IN SHARE ROW EXCLUSIVE MODE');
        $n = $this->db
            ->query('SELECT COALESCE(MAX(id_urut),0)+1 AS n FROM tracer_urut WHERE tgl=?', [
                $date,
            ])
            ->getRowArray()['n'];
        $this->db->query('INSERT INTO tracer_urut VALUES (?,?,?)', [$date, $n, $rm]);
        $idBerkas =
            (new \DateTimeImmutable($date))->format('ymd') .
            str_pad((string) $n, 4, '0', STR_PAD_LEFT);

        return $idBerkas;
    }

    public function generateNoAntrianPindah($unitCode, $doctor, array $visit, string $date, string $reg)
    {
        $queue = $this->db->query(
            'SELECT COALESCE(MAX(no_antrian),0)+1 AS n FROM (SELECT no_antrian FROM kunjungan WHERE kd_unit=? AND kd_paramedis=? AND shiftterakhir=? AND CAST(tgl_masuk AS date)=? AND status=0 AND no_registrasikunjungan<>? UNION ALL SELECT no_antrian FROM reservasi WHERE kd_unit=? AND kd_paramedis=? AND shift=? AND CAST(tgl_pesan AS date)=? AND status=0) q',
            [$unitCode, $doctor, $visit['shiftterakhir'], $date, $reg, $unitCode, $doctor, $visit['shiftterakhir'], $date],
        )->getRowArray()['n'];

        return $queue;
    }

    public function generateUrutGantiDokter(string $reg)
    {
        $seq = $this->db->query('SELECT COALESCE(MAX(urut_masuk),0)+1 AS n FROM history_gantidokter WHERE no_registrasikunjungan=?', [$reg])->getRowArray()['n'];

        return $seq;
    }

    public function generateUrutTindakan(string $noTransaksi)
    {
        $urut = $this->db->query(
            'SELECT COALESCE(MAX(urut_tindakan),0)+1 AS n FROM detail_transaksi WHERE no_transaksi=?',
            [$noTransaksi],
        )->getRowArray()['n'];

        return $urut;
    }

    public function msimpangetmedrecbaru()
    {
        // Preserve the v1.0 numbering series and exclusions.
        $row = $this->db
            ->query(
                "SELECT MAX(REPLACE(no_medicalrecord, '-', '')::NUMERIC) AS nomor
            FROM pasien WHERE no_medicalrecord ~ '^[0-9]-[0-9]{2}-[0-9]{2}-[0-9]{2}$'
            AND no_medicalrecord NOT IN ('0-02-28-6767')
            AND substr(no_medicalrecord, 1, 2) != '2-' AND status_rm_lompat != 1",
            )
            ->getRowArray();
        $nomor = (int) ($row['nomor'] ?? 0) + 1;
        do {
            if ($nomor > 9999999) {
                throw new \RuntimeException('Nomor rekam medis habis');
            }
            $digits = str_pad((string) $nomor++, 7, '0', STR_PAD_LEFT);
            $rm =
                substr($digits, 0, 1) .
                '-' .
                substr($digits, 1, 2) .
                '-' .
                substr($digits, 3, 2) .
                '-' .
                substr($digits, 5, 2);
        } while ($this->db->table('pasien')->where('no_medicalrecord', $rm)->countAllResults() > 0);
        return $rm;
    }

    public function medrecKategori(array $kategori): string
    {
        $prefix = trim((string) ($kategori['prefix_medrec'] ?? ''));
        if ($prefix === '') {
            return $this->msimpangetmedrecbaru();
        }
        $length = (int) $kategori['panjang_nomor'];
        if (!preg_match('/^[A-Za-z0-9]+$/D', $prefix) || $length < 1 || $length > 12) {
            throw new \DomainException('Format nomor kategori pasien tidak valid.');
        }
        $pattern = '^' . $prefix . '-[0-9]+$';
        $row = $this->db
            ->query(
                "SELECT MAX(split_part(no_medicalrecord, '-', 2)::NUMERIC) AS nomor FROM pasien WHERE no_medicalrecord ~ ?",
                [$pattern],
            )
            ->getRowArray();
        $next = (int) ($row['nomor'] ?? 0) + 1;
        if (strlen((string) $next) > $length) {
            throw new \DomainException('Nomor rekam medis kategori ini sudah habis.');
        }
        return $prefix . '-' . str_pad((string) $next, $length, '0', STR_PAD_LEFT);
    }

    public function generateKodeRujukan()
    {
        $this->db->query('LOCK TABLE rujukan_detail IN SHARE ROW EXCLUSIVE MODE');
        $row = $this->db
            ->query(
                'SELECT COALESCE(MAX(CAST(kd_rujukan AS BIGINT)), 0) + 1 AS kode FROM rujukan_detail',
            )
            ->getRowArray();
        $kodeRujukan = (string) $row['kode'];

        return $kodeRujukan;
    }

    public function getMikrodetik() {
        $zona = intval(getenv('app.zona'));

        $microtime = microtime(true) + ($zona * 3600);
        $detik = floor($microtime);
        $mikro = sprintf("%06d", ($microtime - $detik) * 1000000);
        
        return gmdate("Y-m-d H:i:s", $detik) . "." . $mikro;
    }
}
