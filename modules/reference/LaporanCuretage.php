<?php

namespace App\Controllers\RawatInap;

use CodeIgniter\Controller;
use App\Models\RawatInap\MLaporanCuretage;
use App\Models\MVerifikasiPIN;


class LaporanCuretage extends Controller
{
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->model = new MLaporanCuretage();
        $this->MVerifikasiPIN = new MVerifikasiPIN();
    }
    public function save(): string
    {
        $request = request();
        $param = array();
        $results = array();
        $rs = $this->model->saveData($request->getPost());
        if (count($rs) > 0) {
            $code = $rs['code'];
            array_push($results, array("code" => $code, "msg" => $rs['msg']));
        }
        return sendResponse($code, $results);
    }
    public function load(): string
    {
        $request = request();
        $param = array();
        $code = 204;
        $results = array();
        $rs = $this->model->loadData($request->getGet());
        if (count($rs) > 0) {
            $code = 200;
            $results = $rs;
        }
        return sendResponse($code, $results);
    }
    public function templateLoad(): string
    {
        $request = request();
        $param = array();
        $code = 204;
        $results = array();
        $rs = $this->model->templateLoad($request->getGet());
        if (count($rs) > 0) {
            $code = 200;
            $results = $rs;
        }
        return sendResponse($code, $results);
    }
    public function templateSave(): string
    {
        $request = request();
        $param = array();
        $results = array();
        $rs = $this->model->templateSave($request->getPost());
        if (count($rs) > 0) {
            $code = $rs['code'];
            array_push($results, array("code" => $code, "msg" => $rs['msg']));
        }
        return sendResponse($code, $results);
    }
    public function templateDelete(): string
    {
        $request = request();
        $param = array();
        $results = array();
        $rs = $this->model->templateDelete($request->getPost());
        if (count($rs) > 0) {
            $code = $rs['code'];
            array_push($results, array("code" => $code, "msg" => $rs['msg']));
        }
        return sendResponse($code, $results);
    }
    public function print(): string
    {
        $zona = intval(getenv('app.zona'));
        $root = getenv('app.root');
        $baseUrl = getenv('app.baseURL');
        $ttdUrl = getenv('app.inapEvoUrl');
        $nama_daerah = env('app.nama_daerah');
        $nama_rs = env('app.nama_rs');
        $alamat_rs = env('app.alamat_rs');
        $kota_rs = env('app.kota_rs');
        $telp_rs = env('app.telp_rs');
        $email_rs = env('app.email_rs');
        $logo_rs = env('app.logo_rs');
        $request = request();
        $single_id = $request->getPost('single_id');
        $no_kunjungan = $request->getPost('no_kunjungan');
        $no_medicalrecord = $request->getPost('no_medicalrecord');
        $nama_pasien = $request->getPost('nama_pasien');
        $tgl_lahir = $request->getPost('tgl_lahir');
        $umur = $request->getPost('umur');
        $jenis_kelamin = '';
        if ($request->getPost('jenis_kelamin') == 'L') {
            $jenis_kelamin = 'L/<s>P</s>';
        } else {
            $jenis_kelamin = '<s>L</s>/P';
        }
        $alamat = $request->getPost('alamat');
        $nik = $request->getPost('nik');
        $dokter_verif = '____________________';
        $img_dokter = '';
        $service = env('app.printing');
        $url_ttd = env('app.url_ttd');
        $img_pasien = '';
        $tgl_cetak = gmdate("Y-m-d H:i:s", time() + 60 * 60 * $zona);
        $bg_lokalis = "{$baseUrl}/assets/status-lokalis.jpeg";
        $wongbaker = "{$baseUrl}/assets/wongbaker.png";
        $nama_dokumen = "laporan_curetage";
        //loadData Curetage
        $rs = $this->model->loadData($request->getPost());

        //verif ttd
        //verfikasi ttd
        $url_qrcode = "{$service}/tools/qrcode";
        $id_module = $this->MVerifikasiPIN->getModules("master.modules", $request->getPost('tag'));
        $params_ttd = array($id_module, $id_module, $no_kunjungan);
        $query_ttd = "SELECT
                        CASE WHEN dkp.kd_kelpenjamin = 2 THEN COALESCE ( K.no_asuransi, P.no_kk ) ELSE K.no_medicalrecord END AS kode_pasien,
                        u.kd_paramedis AS kode_dokter,
                        pm.nama_paramedis AS nama_dokter 
                    FROM
                        kunjungan K 
                        INNER JOIN pasien P ON P.no_medicalrecord = K.no_medicalrecord
                        INNER JOIN detail_kelpenjamin dkp ON dkp.kd_detkelpenjamin = K.kd_detkelpenjamin
                        INNER JOIN rawat_inap.ri_laporan_curetage rm ON rm.no_registrasikunjungan = K.no_registrasikunjungan
                        LEFT JOIN ttd_log tl on tl.no_registrasikunjungan = K.no_registrasikunjungan And tl.id_module = ? AND tl.batal = 0 and tl.waktu = (select waktu from ttd_log where no_registrasikunjungan = K.no_registrasikunjungan And id_module = ? AND batal = 0 ORDER BY waktu desc limit 1)
                        LEFT JOIN users u ON u.kd_user = tl.kd_user
                        LEFT JOIN para_medis pm ON pm.kd_paramedis = u.kd_paramedis
                    WHERE
                    K.no_registrasikunjungan = ?";
        $rows_ttd = $this->db->query($query_ttd, $params_ttd)->getResultArray();
        if (count($rows_ttd) > 0) {
            foreach ($rows_ttd as $v_ttd) {
                if ($v_ttd['kode_dokter'] != '' && $v_ttd['kode_dokter'] != null) {
                    $url_app_ttd_dokter = $url_ttd . '/esignature.php?kd_paramedis=' . $v_ttd['kode_dokter'];
                    $params_qr_dr = array("param" => $url_app_ttd_dokter);
                    $qrcode_dr = http_post($url_qrcode, $params_qr_dr);
                    $img = '';
                    if ($qrcode_dr['code'] == 200) $img_dokter = "<img style=\"height:90px;\" src=" . $qrcode_dr['data'][0]['stream'] . ">";
                    $dokter_verif = $v_ttd['nama_dokter'];
                }
            }
        }

        if (count($rs) > 0) {
            $data = $rs[0];
            $row = json_decode($data['data_post']);
            $nm_dpjp_obgyn = $data['nm_dpjp_obgyn'] != '' ? $data['nm_dpjp_obgyn'] : '_____';
            $nm_dokter_operator = $data['nm_dokter_operator'] != '' ? $data['nm_dokter_operator'] : '_____';
            $nm_asisten_operator = $data['nm_asisten_operator'] != '' ? $data['nm_asisten_operator'] : '_____';
            $nm_dokter_anestesi = $data['nm_dokter_anestesi'] != '' ? $data['nm_dokter_anestesi'] : '_____';
            $nm_asisten_anestesi = $data['nm_asisten_anestesi'] != '' ? $data['nm_asisten_anestesi'] : '_____';
            $diagnosis_pra_ops = $row->diagnosis_pra_ops != '' ? $row->diagnosis_pra_ops : '<div style="margin-bottom: 30px;"></div>';
            $diagnosis_pasca_ops = $row->diagnosis_pasca_ops != '' ? $row->diagnosis_pasca_ops : '<div style="margin-bottom: 30px;"></div>';
            $tgl_ops = $row->tgl_ops != '' ? $row->tgl_ops : '_____';
            $jam_ops = $row->jam_ops != '' ? $row->jam_ops : '_____';
            $lama_ops = $row->lama_ops != '' ? $row->lama_ops : '_____';
            $tindakan_ops = $row->tindakan_ops != '' ? $row->tindakan_ops : '<div style="margin-bottom: 30px;"></div>';
            $arr_jenis_tindakan_ops = array('Emergensi', 'Elektif');
            $jenis_tindakan_ops = map_checkbox($arr_jenis_tindakan_ops, $row->jenis_tindakan_ops, 1, '<div style="margin-bottom: 5px;"><span>', '</span></div>', ',');
            $arr_jenis_ops = array('Bersih', 'Bersih Tercemar', 'Tercemar', 'Kotor');
            $jenis_ops = map_checkbox($arr_jenis_ops, $row->jenis_ops, 1, '<div style="display: inline-block; width: auto; white-space: nowrap; margin-right: 10px;"><span>', '</span></div>', ',');
            $arr_operasi_ke = array('1', 'Re-Do');
            $operasi_ke = map_checkbox($arr_operasi_ke, $row->operasi_ke, 1, '<div style="display: inline-block; width: auto; margin-right: 10px;"><span>', '</span></div>', ',');
            $arr_profilaksis = array('Ya', 'Tidak');
            $profilaksis = map_checkbox($arr_profilaksis, $row->profilaksis, 1, '<div style="display: inline-block; width: auto; margin-right: 10px;"><span>', '</span></div>', ',');
            $antibiotik = $row->antibiotik != '' ? $row->antibiotik : '_____';
            $waktu_antibiotik = $row->waktu_antibiotik != '' ? $row->waktu_antibiotik : '_____';
            $txt_keteterisasi = $row->txt_keteterisasi != '' ? $row->txt_keteterisasi : '_____';
            $arr_periksa = array('Uterus ante', 'Retro fleksi');
            $periksa = map_checkbox($arr_periksa, $row->periksa, 1, '<div style="display: inline-block; width: 100px; white-space: nowrap; margin-right: 10px;"><span>', '</span></div>', ',');
            $txt_periksa = $row->txt_periksa != '' ? $row->txt_periksa : '_____';
            $txt_sondase_awal = $row->txt_sondase_awal != '' ? $row->txt_sondase_awal : '_____';
            $arr_rad_sondase = array('Dilindungi', 'Tidak dengan drip');
            $rad_sondase = map_checkbox($arr_rad_sondase, $row->rad_sondase, 1, '<div style="display: inline-block; width: 100px; white-space: nowrap; margin-right: 10px;"><span>', '</span></div>', ',');
            $txt_sondase_akhir = $row->txt_sondase_akhir != '' ? $row->txt_sondase_akhir : '_____';
            $arr_rad_kuretage = array('Tajam', 'Tumpul&nbsp;nomor');
            $rad_kuretage = map_checkbox($arr_rad_kuretage, $row->rad_kuretage, 1, '<div style="display: inline-block; width: auto; margin-right: 10px;"><span>', '</span></div>', ',');
            $txt_kuretage = $row->txt_kuretage != '' ? $row->txt_kuretage : '_____';
            $txt_keluar_jaringan = $row->txt_keluar_jaringan != '' ? $row->txt_keluar_jaringan : '_____';
            $txt_keluar_jaringan2 = $row->txt_keluar_jaringan2 != '' ? $row->txt_keluar_jaringan2 : '_____';
            $txt_keluar_jaringan3 = $row->txt_keluar_jaringan3 != '' ? $row->txt_keluar_jaringan3 : '_____';
            $txt_jaringan_pendarah = $row->txt_jaringan_pendarah != '' ? $row->txt_jaringan_pendarah : '_____';
            $txt_pasang_tampon = $row->txt_pasang_tampon != '' ? $row->txt_pasang_tampon : '_____';
            $laporan_ops_curetage = $row->laporan_ops_curetage != '' ? $row->laporan_ops_curetage : '<div style="margin-bottom: 100px;"></div>';
            $instruksi_pasca_ops = $row->instruksi_pasca_ops != '' ? $row->instruksi_pasca_ops : '<div style="margin-bottom: 100px;"></div>';
        } else {
            $nm_dpjp_obgyn = '_____';
            $nm_dokter_operator = '_____';
            $nm_asisten_operator = '_____';
            $nm_dokter_anestesi = '_____';
            $nm_asisten_anestesi = '_____';
            $diagnosis_pra_ops = '<div style="margin-bottom: 30px;"></div>';
            $diagnosis_pasca_ops = '<div style="margin-bottom: 30px;"></div>';
            $tgl_ops = '_____';
            $jam_ops = '_____';
            $lama_ops = '_____';
            $tindakan_ops = '<div style="margin-bottom: 30px;"></div>';
            $jenis_tindakan_ops = '
            <div style="margin-bottom: 5px;">&#9744; Emergensi</div>
            <div style="margin-bottom: 5px;">&#9744; Elektif</div>
            ';
            $jenis_ops = '
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Bersih</div>
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Bersih Tercemar</div>
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Tercemar</div>
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Kotor</div>
            ';
            $operasi_ke = '
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; 1</div>
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Re-Do</div>
            ';
            $profilaksis = '
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Ya</div>
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Tidak</div>
            ';
            $antibiotik = '_____';
            $waktu_antibiotik = '_____';
            $txt_keteterisasi = '_____';
            $periksa = '
            <div style="display: inline-block; width: 15%; margin-right: 10px;">&#9744; Uterus ante</div>
            <div style="display: inline-block; width: 15%; margin-right: 10px;">&#9744; Retro fleksi</div>
            ';
            $txt_periksa = '_____';
            $txt_sondase_awal = '_____';
            $rad_sondase = '
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Dilindungi</div>
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Tidak dengan drip</div>
            ';
            $txt_sondase_akhir = '_____';
            $rad_kuretage = '
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Tajam</div>
            <div style="display: inline-block; width: auto; margin-right: 10px;">&#9744; Tumpul&nbsp;nomor</div>
            ';
            $txt_kuretage = '_____';
            $txt_keluar_jaringan = '_____';
            $txt_keluar_jaringan2 = '_____';
            $txt_keluar_jaringan3 = '_____';
            $txt_jaringan_pendarah = '_____';
            $txt_pasang_tampon = '_____';
            $laporan_ops_curetage = '<div style="margin-bottom: 300px;"></div>';
            $instruksi_pasca_ops = '<div style="margin-bottom: 300px;"></div>';
        }
        $rs = $this->model->loadKunjungan($request->getPost());
        if (count($rs) > 0) {
            $row = $rs[0];
            $tgl_masuk = $row['tgl_masuk'] != '' ? $row['tgl_masuk'] : '-';
        } else {
            $tgl_masuk = '-';
        }

        $tags = array("{{no_medicalrecord}}", "{{nama_pasien}}", "{{tgl_lahir}}", "{{umur}}", "{{jenis_kelamin}}", "{{alamat}}", "{{nik}}", "{{nama_daerah}}", "{{nama_rs}}", "{{alamat_rs}}", "{{kota_rs}}", "{{telp_rs}}", "{{email_rs}}", "{{logo_rs}}", "{{tgl_cetak}}", "{{nm_dpjp_obgyn}}", "{{nm_dokter_operator}}", "{{nm_asisten_operator}}", "{{nm_dokter_anestesi}}", "{{nm_asisten_anestesi}}", "{{diagnosis_pra_ops}}", "{{diagnosis_pasca_ops}}", "{{tgl_ops}}", "{{jam_ops}}", "{{lama_ops}}", "{{tindakan_ops}}", "{{jenis_tindakan_ops}}", "{{jenis_ops}}", "{{operasi_ke}}", "{{profilaksis}}", "{{antibiotik}}", "{{waktu_antibiotik}}", "{{txt_keteterisasi}}", "{{periksa}}", "{{txt_periksa}}", "{{txt_sondase_awal}}", "{{rad_sondase}}", "{{txt_sondase_akhir}}", "{{rad_kuretage}}", "{{txt_kuretage}}", "{{txt_keluar_jaringan}}", "{{txt_keluar_jaringan2}}", "{{txt_keluar_jaringan3}}", "{{txt_jaringan_pendarah}}", "{{txt_pasang_tampon}}", "{{laporan_ops_curetage}}", "{{instruksi_pasca_ops}}", "{{tgl_masuk}}", "{{dokter_verif}}", "{{img_dokter}}");
        $dataset = array($no_medicalrecord, $nama_pasien, $tgl_lahir, $umur, $jenis_kelamin, $alamat, $nik, $nama_daerah, $nama_rs, $alamat_rs, $kota_rs, $telp_rs, $email_rs, $logo_rs, $tgl_cetak, $nm_dpjp_obgyn, $nm_dokter_operator, $nm_asisten_operator, $nm_dokter_anestesi, $nm_asisten_anestesi, $diagnosis_pra_ops, $diagnosis_pasca_ops, $tgl_ops, $jam_ops, $lama_ops, $tindakan_ops, $jenis_tindakan_ops, $jenis_ops, $operasi_ke, $profilaksis, $antibiotik, $waktu_antibiotik, $txt_keteterisasi, $periksa, $txt_periksa, $txt_sondase_awal, $rad_sondase, $txt_sondase_akhir, $rad_kuretage, $txt_kuretage, $txt_keluar_jaringan, $txt_keluar_jaringan2, $txt_keluar_jaringan3, $txt_jaringan_pendarah, $txt_pasang_tampon, $laporan_ops_curetage, $instruksi_pasca_ops, $tgl_masuk, $dokter_verif, $img_dokter);
        $outputName = "{$nama_dokumen}_{$no_kunjungan}";
        $printed = printing("inap/{$nama_dokumen}", $tags, $dataset, $outputName);
        $code = $printed['code'];
        $results = $printed['results'];
        return sendResponse($code, $results);
    }
}
