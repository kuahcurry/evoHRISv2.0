<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\mArsip;
use App\Models\mBuku;

class cPegawai extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['response', 'url', 'filesystem']);
    }

    /**
     * Master Dropdowns / References
     */
    public function master()
    {
        $data = [
            'agama'             => $this->db->table('agama')->select('KD_AGAMA, AGAMA')->get()->getResultArray(),
            'identitas'         => $this->db->table('identitas')->select('KD_IDENTITAS, IDENTITAS, IDENTITAS as NAMA_IDENTITAS')->get()->getResultArray(),
            'golda'             => $this->db->table('golongan_darah')->select('KD_GOLDA, GOLONGAN_DARAH, GOLONGAN_DARAH as NAMA_GOLDA')->get()->getResultArray(),
            'status_perkawinan' => $this->db->table('status_perkawinan')->select('KD_STATUS_PERKAWINAN, STATUS_PERKAWINAN, STATUS_PERKAWINAN as NAMA_STATUS_PERKAWINAN')->get()->getResultArray(),
            'provinsi'          => $this->db->table('provinsi')->select('KD_PROVINSI, PROVINSI, PROVINSI as NAMA_PROVINSI')->get()->getResultArray(),
            'bagian'            => $this->db->table('bagian')->select('KD_BAGIAN, BAGIAN')->get()->getResultArray(),
            'sub_bagian'        => $this->db->table('sub_bagian')->select('kd_sub_bagian as KD_SUBBAGIAN, sub_bagian as SUBBAGIAN, kd_bagian as KD_BAGIAN')->get()->getResultArray(),
            'jabatan'           => $this->db->table('jabatan')->select('KD_JABATAN, JABATAN')->get()->getResultArray(),
            'ekselon'           => $this->db->table('ekselon')->select('KD_EKSELON, EKSELON, EKSELON as NAMA_EKSELON')->get()->getResultArray(),
            'status_pegawai'    => $this->db->table('status_pegawai')->select('KD_STATUS_PEGAWAI, STATUS_PEGAWAI, STATUS_PEGAWAI as NAMA_STATUS_PEGAWAI')->get()->getResultArray(),
            'golongan'          => $this->db->table('golongan')->select('KD_GOLONGAN, GOLONGAN, GOLONGAN as NAMA_GOLONGAN')->get()->getResultArray(),
            'pangkat'           => $this->db->table('pangkat')->select('KD_PANGKAT, PANGKAT, PANGKAT as NAMA_PANGKAT')->get()->getResultArray(),
            'jenis_pegawai'     => $this->db->table('jenis_pegawai')->select('KD_JENIS_PEGAWAI, JENIS_PEGAWAI, JENIS_PEGAWAI as NAMA_JENIS_PEGAWAI')->get()->getResultArray(),
            'lokasi'            => $this->db->table('lokasi')->select('KD_LOKASI, LOKASI, LOKASI as NAMA_LOKASI')->get()->getResultArray(),
            'status_keahlian'   => $this->db->table('status_keahlian')->select('KD_STATUS_KEAHLIAN, STATUS_KEAHLIAN, STATUS_KEAHLIAN as NAMA_STATUS_KEAHLIAN')->get()->getResultArray(),
            'pekerjaan'         => $this->db->table('pekerjaan')->select('KD_PEKERJAAN, PEKERJAAN, PEKERJAAN as NAMA_PEKERJAAN')->get()->getResultArray(),
            'hubungan'          => $this->db->table('hubungan')->select('KD_HUBUNGAN, HUBUNGAN, HUBUNGAN as NAMA_HUBUNGAN')->get()->getResultArray(),
            'jenjang_pendidikan'=> $this->db->table('jenjang_pendidikan')->select('KD_JENJANG, JENJANG_PENDIDIKAN, JENJANG_PENDIDIKAN as NAMA_JENJANG')->get()->getResultArray(),
            'status_asuransi'   => $this->db->table('status_asuransi')->select('KD_STATUS_ASURANSI, STATUS_ASURANSI')->get()->getResultArray(),
            'nama_asuransi'     => $this->db->table('nama_asuransi')->select('KD_ASURANSI, NAMA_ASURANSI')->get()->getResultArray(),
            'jenis_pendidikan'  => $this->db->table('jenis_pendidikan')->select('KD_JENIS_PENDIDIKAN, JENIS_PENDIDIKAN, JENIS_PENDIDIKAN as NAMA_JENIS_PENDIDIKAN')->get()->getResultArray(),
            'status_lulus'      => $this->db->table('status_lulus')->select('KD_STATUS_LULUS, STATUS_LULUS')->get()->getResultArray(),
            'jenis_pengalaman'  => $this->db->table('jenis_pengalaman')->select('KD_JENIS_PENGALAMAN, JENIS_PENGALAMAN, JENIS_PENGALAMAN as NAMA_JENIS_PENGALAMAN')->get()->getResultArray(),
            'level_kemampuan'   => $this->db->table('level_kemampuan')->select('KD_LEVEL, LEVEL, LEVEL as NAMA_LEVEL')->get()->getResultArray(),
            'jenis_sip'         => $this->db->table('jenis_sip')->select('KD_JENIS_SIP, JENIS_SIP, JENIS_SIP as NAMA_JENIS_SIP')->get()->getResultArray(),
            'jenis_cuti'        => $this->db->table('jenis_cuti')->select('KD_JENIS_CUTI, JENIS_CUTI, JENIS_CUTI as NAMA_JENIS_CUTI')->get()->getResultArray()
        ];

        return sendResponse(200, $data, 'Data master referensi berhasil dimuat');
    }

    public function get_kabupaten($kd_provinsi = null)
    {
        $kd = $kd_provinsi ?? $this->request->getGet('kd_provinsi') ?? $this->request->getPost('kd_provinsi');
        $builder = $this->db->table('kabupaten')->select('KD_KABUPATEN, KABUPATEN, KABUPATEN as NAMA_KABUPATEN, KD_PROVINSI');
        if (!empty($kd)) {
            $builder->where('KD_PROVINSI', $kd);
        }
        return sendResponse(200, $builder->get()->getResultArray());
    }

    public function get_kecamatan($kd_kabupaten = null)
    {
        $kd = $kd_kabupaten ?? $this->request->getGet('kd_kabupaten') ?? $this->request->getPost('kd_kabupaten');
        $builder = $this->db->table('kecamatan')->select('KD_KECAMATAN, KECAMATAN, KECAMATAN as NAMA_KECAMATAN, KD_KABUPATEN');
        if (!empty($kd)) {
            $builder->where('KD_KABUPATEN', $kd);
        }
        return sendResponse(200, $builder->get()->getResultArray());
    }

    public function get_kelurahan($kd_kecamatan = null)
    {
        $kd = $kd_kecamatan ?? $this->request->getGet('kd_kecamatan') ?? $this->request->getPost('kd_kecamatan');
        $builder = $this->db->table('kelurahan')->select('KD_KELURAHAN, KELURAHAN, KELURAHAN as NAMA_KELURAHAN, KD_KECAMATAN');
        if (!empty($kd)) {
            $builder->where('KD_KECAMATAN', $kd);
        }
        return sendResponse(200, $builder->get()->getResultArray());
    }

    public function get_jurusan($kd_jenjang = null)
    {
        $kd = $kd_jenjang ?? $this->request->getGet('kd_jenjang') ?? $this->request->getPost('kd_jenjang');
        $builder = $this->db->table('jurusan')->select('KD_JURUSAN, JURUSAN, JURUSAN as NAMA_JURUSAN, KD_JENJANG');
        if (!empty($kd)) {
            $builder->where('KD_JENJANG', $kd);
        }
        return sendResponse(200, $builder->get()->getResultArray());
    }

    public function get_sub_bagian($kd_bagian = null)
    {
        $kd = $kd_bagian ?? $this->request->getGet('kd_bagian') ?? $this->request->getPost('kd_bagian');
        $builder = $this->db->table('sub_bagian')->select('kd_sub_bagian as KD_SUBBAGIAN, sub_bagian as SUBBAGIAN, kd_bagian as KD_BAGIAN');
        if (!empty($kd)) {
            $builder->where('kd_bagian', $kd);
        }
        return sendResponse(200, $builder->get()->getResultArray());
    }

    public function get_arsip_rak()
    {
        $mArsip = new mArsip();
        return sendResponse(200, $mArsip->get_data_rak());
    }

    public function get_buku_tanah()
    {
        $mBuku = new mBuku();
        return sendResponse(200, $mBuku->get_data_buku());
    }

    /**
     * Cek Password User (tb_users)
     */
    public function cek_pass()
    {
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $idPegawai = $req['id_pegawai'] ?? $req['ID_PEGAWAI'] ?? null;
        $password  = $req['password'] ?? $req['pass'] ?? '';

        if (empty($idPegawai)) {
            return sendResponse(400, null, 'ID Pegawai tidak boleh kosong');
        }

        $user = $this->db->table('tb_users')
            ->where('id_pegawai', $idPegawai)
            ->orWhere('username', $idPegawai)
            ->get()->getRowArray();

        if (!$user) {
            return sendResponse(404, ['valid' => false], 'Akun user belum terdaftar untuk pegawai ini');
        }

        $isValid = false;
        if (password_verify($password, $user['password']) || md5($password) === $user['password'] || $password === $user['password']) {
            $isValid = true;
        }

        return sendResponse(200, [
            'valid'    => $isValid,
            'username' => $user['username'] ?? '',
            'id_user'  => $user['id_user'] ?? null
        ], $isValid ? 'Password cocok' : 'Password tidak cocok');
    }

    /**
     * Update Password User (tb_users)
     */
    public function update_pass()
    {
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $idPegawai   = $req['id_pegawai'] ?? $req['ID_PEGAWAI'] ?? null;
        $oldPassword = $req['old_password'] ?? $req['old_pass'] ?? '';
        $newPassword = $req['new_password'] ?? $req['new_pass'] ?? '';

        if (empty($idPegawai) || empty($newPassword)) {
            return sendResponse(400, null, 'ID Pegawai dan Password Baru harus diisi');
        }

        $user = $this->db->table('tb_users')
            ->where('id_pegawai', $idPegawai)
            ->orWhere('username', $idPegawai)
            ->get()->getRowArray();

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($user) {
            if (!empty($oldPassword)) {
                $matched = password_verify($oldPassword, $user['password']) || md5($oldPassword) === $user['password'] || $oldPassword === $user['password'];
                if (!$matched) {
                    return sendResponse(400, null, 'Password lama tidak sesuai');
                }
            }

            $this->db->table('tb_users')
                ->where('id_user', $user['id_user'])
                ->update([
                    'password'   => $hashed,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return sendResponse(200, ['status' => 'updated'], 'Password user berhasil diubah');
        } else {
            $peg = $this->db->table('pegawai')->where('ID_PEGAWAI', $idPegawai)->get()->getRowArray();
            $username = $peg['NIP'] ?? $idPegawai;

            $this->db->table('tb_users')->insert([
                'id_pegawai' => $idPegawai,
                'username'   => $username,
                'password'   => $hashed,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return sendResponse(201, ['status' => 'created'], 'Akun user baru berhasil dibuat');
        }
    }

    /**
     * Simpan Pegawai Baru Lengkap (Multipart FormData & JSON)
     */
    public function save_data()
    {
        // Support both JSON payload and multipart FormData
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        if (empty($req)) {
            $req = $_POST;
        }

        // Helper to get string / int / null safely
        $val = function($keys, $default = null) use ($req) {
            if (!is_array($keys)) $keys = [$keys];
            foreach ($keys as $k) {
                if (isset($req[$k]) && $req[$k] !== '') {
                    return trim((string)$req[$k]);
                }
            }
            return $default;
        };

        $valInt = function($keys, $default = null) use ($req) {
            if (!is_array($keys)) $keys = [$keys];
            foreach ($keys as $k) {
                if (isset($req[$k]) && $req[$k] !== '' && is_numeric($req[$k])) {
                    return (int)$req[$k];
                }
            }
            return $default;
        };

        $nip  = $val(['nip', 'NIP', 'txt_nip', 'add-nip']);
        $nama = $val(['nama', 'NAMA', 'txt_nama', 'add-nama']);

        if (empty($nip) || empty($nama)) {
            return sendResponse(400, null, 'NIP dan Nama Pegawai wajib diisi');
        }

        // Cek duplikasi NIP
        $cek = $this->db->table('pegawai')->where('NIP', $nip)->get()->getRowArray();
        if ($cek) {
            return sendResponse(400, null, 'NIP sudah terdaftar dalam sistem');
        }

        // Upload Helper
        $uploadDir = ROOTPATH . '../assets/user/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $uploadedFiles = [];

        $handleUpload = function($fileKey) use ($uploadDir, &$uploadedFiles) {
            try {
                $file = $this->request->getFile($fileKey);
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $ext = $file->getClientExtension();
                    $newName = time() . '_' . bin2hex(random_bytes(6)) . ($ext ? '.' . $ext : '');
                    $file->move($uploadDir, $newName);
                    $uploadedFiles[] = $uploadDir . $newName;
                    return $newName;
                }
            } catch (\Exception $e) {
                log_message('error', 'File upload error: ' . $e->getMessage());
            }
            return '';
        };

        // Handle uploaded files
        $fotoProfile    = $handleUpload('foto') ?: $handleUpload('imgprofile') ?: $handleUpload('input-file-foto') ?: $val(['FOTO', 'foto', 'txt_foto'], '');
        $filePenempatan = $handleUpload('file_penempatan') ?: $handleUpload('dokumen_penempatan') ?: $handleUpload('add-upload-dokumen');
        $filePendidikan = $handleUpload('file_pendidikan') ?: $handleUpload('upload_ijazah') ?: $handleUpload('add-upload-ijazah');
        $filePengalaman = $handleUpload('file_pengalaman') ?: $handleUpload('upload_sk') ?: $handleUpload('add-pengalaman-sk');
        $fileKeahlian   = $handleUpload('file_keahlian') ?: $handleUpload('upload_keahlian') ?: $handleUpload('add-keahlian-upload');
        $fileKeluarga   = $handleUpload('file_keluarga') ?: $handleUpload('upload_keluarga') ?: $handleUpload('add-keluarga-upload');

        // Hitung ID_PEGAWAI baru
        $resId = $this->db->query("SELECT COALESCE(MAX(CAST(ID_PEGAWAI AS UNSIGNED)), 0) + 1 AS new_id FROM pegawai")->getRowArray();
        $idPegawai = (string)($resId['new_id'] ?? 1);

        // Normalize Jenis Kelamin: 1 (Laki-laki), 2 (Perempuan)
        $jkRaw = $val(['jenis_kelamin', 'JENIS_KELAMIN', 'cmb_jk', 'add-jk'], '1');
        $jk = (strtoupper((string)$jkRaw) === 'P' || $jkRaw === '2' || stripos((string)$jkRaw, 'perempuan') !== false) ? 2 : 1;

        $tglLahir = $val(['tgl_lahir', 'tanggal_lahir', 'TANGGAL_LAHIR', 'txt_tgl_lahir', 'add-tgl-lahir']);
        $tglMasuk = $val(['tgl_masuk', 'tanggal_masuk', 'TANGGAL_MASUK', 'txt_tgl_masuk', 'add-tgl-masuk']) ?: date('Y-m-d');

        $dataPegawai = [
            'ID_PEGAWAI'           => $idPegawai,
            'NIP'                  => $nip,
            'NAMA'                 => $nama,
            'NAMA_ALIAS'           => $val(['nama_alias', 'NAMA_ALIAS', 'txt_alias'], ''),
            'TEMPAT_LAHIR'         => $val(['tempat_lahir', 'TEMPAT_LAHIR', 'txt_tempat_lahir', 'add-tempat-lahir'], ''),
            'TANGGAL_LAHIR'        => !empty($tglLahir) ? $tglLahir : null,
            'JENIS_KELAMIN'        => $jk,
            'KD_AGAMA'             => $valInt(['kd_agama', 'KD_AGAMA', 'cmb_agama', 'add-agama']),
            'KD_GOLDA'             => $valInt(['kd_golda', 'KD_GOLDA', 'cmb_golda', 'add-goldar']),
            'KD_STATUS_PERKAWINAN' => $valInt(['kd_status_perkawinan', 'KD_STATUS_PERKAWINAN', 'cmb_status_nikah', 'add-status-nikah']),
            'ALAMAT'               => $val(['alamat', 'ALAMAT', 'txt_alamat', 'add-alamat'], ''),
            'ALAMAT_DOMISILI'      => $val(['alamat_domisili', 'ALAMAT_DOMISILI'], ''),
            'KD_PROVINSI'          => $valInt(['kd_provinsi', 'KD_PROVINSI', 'cmb_provinsi', 'add-provinsi']),
            'KD_KABUPATEN'         => $valInt(['kd_kabupaten', 'KD_KABUPATEN', 'cmb_kabupaten', 'add-kabupaten']),
            'KD_KECAMATAN'         => $valInt(['kd_kecamatan', 'KD_KECAMATAN', 'cmb_kecamatan', 'add-kecamatan']),
            'KD_KELURAHAN'         => $valInt(['kd_kelurahan', 'KD_KELURAHAN', 'cmb_kelurahan', 'add-kelurahan']),
            'KODEPOS'              => $val(['kodepos', 'KODEPOS', 'kode_pos', 'KODE_POS', 'txt_kodepos', 'add-kodepos'], ''),
            'TELEPHONE'            => $val(['telephone', 'TELEPHONE', 'telepon', 'TELEPON', 'txt_telepon'], ''),
            'HANDPHONE'            => $val(['handphone', 'HANDPHONE', 'hp', 'txt_hp', 'add-hp'], ''),
            'EMAIL'                => $val(['email', 'EMAIL', 'txt_email', 'add-email'], ''),
            'KD_IDENTITAS'         => $valInt(['kd_identitas', 'KD_IDENTITAS', 'cmb_identitas', 'add-identitas']),
            'NO_IDENTITAS'         => $val(['no_identitas', 'NO_IDENTITAS', 'nik', 'txt_nik', 'add-nik'], ''),
            'NO_NPWP'              => $val(['no_npwp', 'NO_NPWP', 'npwp', 'txt_npwp', 'add-npwp'], ''),
            'FOTO'                 => $fotoProfile ?: '',
            'TANGGAL_MASUK'        => !empty($tglMasuk) ? $tglMasuk : null,
            'status_aktif'         => 1,
            'jml_cuti'             => 12
        ];

        $this->db->transStart();

        // 1. Insert Pegawai
        $this->db->table('pegawai')->insert($dataPegawai);

        // 2. Insert Penempatan Pegawai (Wajib)
        $resPen = $this->db->query("SELECT COALESCE(MAX(KD_PENEMPATAN), 0) + 1 AS new_id FROM detail_penempatan_pegawai")->getRowArray();
        $kdPenempatan = (int)($resPen['new_id'] ?? 1);

        $kdBagian        = $valInt(['kd_bagian', 'KD_BAGIAN', 'cmb_bagian', 'add-bagian'], 1);
        $kdSubBagian     = $valInt(['kd_subbagian', 'KD_SUBBAGIAN', 'kd_sub_bagian', 'add-sub-bagian']);
        $kdJabatan       = $valInt(['kd_jabatan', 'KD_JABATAN', 'cmb_jabatan', 'add-jabatan'], 1);
        $kdGolongan      = $valInt(['kd_golongan', 'KD_GOLONGAN', 'cmb_golongan', 'add-golongan'], 1);
        $kdPangkat       = $valInt(['kd_pangkat', 'KD_PANGKAT', 'cmb_pangkat', 'add-pangkat']);
        $kdEkselon       = $valInt(['kd_eselon', 'kd_ekselon', 'KD_EKSELON', 'cmb_eselon', 'add-eselon']);
        $kdJenisPegawai  = $valInt(['kd_jenis_pegawai', 'KD_JENIS_PEGAWAI', 'cmb_jenis_pegawai', 'add-jenis-pegawai'], 1);
        $kdStatusPegawai = $valInt(['kd_status_pegawai', 'KD_STATUS_PEGAWAI', 'cmb_status_pegawai', 'add-status-pegawai'], 1);
        $kdLokasi        = $valInt(['kd_lokasi', 'KD_LOKASI', 'cmb_lokasi', 'add-lokasi'], 1);
        $jenisPenempatan = $valInt(['jenis_penempatan', 'JENIS_PENEMPATAN', 'add-jenis-penempatan'], 1);

        $dataPenempatan = [
            'KD_PENEMPATAN'           => $kdPenempatan,
            'ID_PEGAWAI'              => $idPegawai,
            'KD_BAGIAN'               => $kdBagian ?: 1,
            'KD_SUBBAGIAN'            => $kdSubBagian,
            'KD_JABATAN'              => $kdJabatan ?: 1,
            'KD_GOLONGAN'             => $kdGolongan ?: 1,
            'KD_PANGKAT'              => $kdPangkat,
            'KD_EKSELON'              => $kdEkselon,
            'KD_JENIS_PEGAWAI'        => $kdJenisPegawai ?: 1,
            'KD_STATUS_PEGAWAI'       => $kdStatusPegawai ?: 1,
            'KD_LOKASI'               => $kdLokasi ?: 1,
            'JENIS_PENEMPATAN'        => $jenisPenempatan,
            'TANGGAL_MULAI'           => !empty($tglMasuk) ? $tglMasuk : date('Y-m-d'),
            'TANGGAL_SK_PENGANGKATAN' => !empty($tglMasuk) ? $tglMasuk : date('Y-m-d'),
            'IMAGE'                   => $filePenempatan ?: ''
        ];
        $this->db->table('detail_penempatan_pegawai')->insert($dataPenempatan);

        // 3. Insert Pendidikan Pegawai (Opsional jika ada input)
        $namaLembaga = $val(['nama_lembaga', 'NAMA_LEMBAGA', 'txt_lembaga', 'add-lembaga', 'pendidikan', 'add-pendidikan']);
        $kdJenjang = $valInt(['kd_jenjang', 'KD_JENJANG', 'cmb_jenjang', 'add-jenjang-pendidikan']);
        if (!empty($namaLembaga) || !empty($kdJenjang) || !empty($filePendidikan)) {
            $resPdk = $this->db->query("SELECT COALESCE(MAX(KD_PENDIDIKAN), 0) + 1 AS new_id FROM detail_pendidikan_pegawai")->getRowArray();
            $kdPendidikan = (int)($resPdk['new_id'] ?? 1);

            $dataPendidikan = [
                'KD_PENDIDIKAN'       => $kdPendidikan,
                'ID_PEGAWAI'          => $idPegawai,
                'NAMA_LEMBAGA'        => $namaLembaga ?: '-',
                'KD_JENJANG'          => $kdJenjang ?: 1,
                'ALAMAT'              => $val(['alamat_lembaga', 'ALAMAT_LEMBAGA', 'add-alamat-lembaga'], '-'),
                'PERIODE_MULAI'       => $val(['periode_mulai', 'PERIODE_MULAI', 'add-tgl-periode'], '2000-01-01'),
                'PERIODE_SAMPAI'      => $val(['periode_sampai', 'PERIODE_SAMPAI', 'add-sampai-dengan'], '2004-01-01'),
                'IPK'                 => $val(['ipk', 'IPK', 'add-ipk'], '0.00'),
                'KD_JURUSAN'          => $valInt(['kd_jurusan', 'KD_JURUSAN', 'cmb_jurusan', 'add-jurusan']),
                'KD_STATUS_LULUS'     => $valInt(['kd_status_lulus', 'KD_STATUS_LULUS', 'add-status-lulus'], 1),
                'KD_JENIS_PENDIDIKAN' => $valInt(['kd_jenis_pendidikan', 'KD_JENIS_PENDIDIKAN', 'add-jenis-pendidikan'], 1),
                'NAMA_PEJABAT'        => $val(['nama_pejabat', 'NAMA_PEJABAT', 'add-nama-pejabat'], '-'),
                'NO_IJAZAH'           => $val(['no_ijazah', 'NO_IJAZAH', 'add-no-ijazah'], '-'),
                'GAMBAR_IJAZAH'       => $filePendidikan ?: '',
                'JURUSAN'             => $val(['jurusan', 'JURUSAN', 'txt_jurusan'], '-')
            ];
            $this->db->table('detail_pendidikan_pegawai')->insert($dataPendidikan);
        }

        // 4. Insert Pengalaman Pegawai (Opsional jika ada input)
        $namaPerusahaan = $val(['nama_perusahaan', 'NAMA_PERUSAHAAN', 'txt_perusahaan', 'pengalaman_perusahaan', 'add-pengalaman-perusahaan']);
        if (!empty($namaPerusahaan) || !empty($filePengalaman)) {
            $resPeng = $this->db->query("SELECT COALESCE(MAX(KD_PENGALAMAN), 0) + 1 AS new_id FROM detail_pengalaman_pegawai")->getRowArray();
            $kdPengalaman = (int)($resPeng['new_id'] ?? 1);

            $dataPengalaman = [
                'KD_PENGALAMAN'       => $kdPengalaman,
                'ID_PEGAWAI'          => $idPegawai,
                'NAMA_PERUSAHAAN'     => $namaPerusahaan ?: '-',
                'KD_JABATAN'          => $valInt(['pengalaman_jabatan', 'add-pengalaman-jabatan'], 1),
                'JABATAN'             => $val(['pengalaman_nama_jabatan', 'add-pengalaman-nama-jabatan'], '-'),
                'ALAMAT'              => $val(['pengalaman_alamat', 'add-pengalaman-alamat'], '-'),
                'KD_JENIS_PENGALAMAN' => $valInt(['pengalaman_jenis', 'add-pengalaman-jenis'], 1),
                'KD_PROVINSI'         => $valInt(['pengalaman_provinsi', 'add-pengalaman-provinsi'], 0),
                'KD_KABUPATEN'        => $valInt(['pengalaman_kabupaten', 'add-pengalaman-kabupaten'], 0),
                'KD_KECAMATAN'        => $valInt(['pengalaman_kecamatan', 'add-pengalaman-kecamatan'], 0),
                'KD_KELURAHAN'        => $valInt(['pengalaman_kelurahan', 'add-pengalaman-kelurahan'], 0),
                'TANGGAL_MULAI'       => $val(['pengalaman_periode_mulai', 'add-pengalaman-periode-mulai'], '2020-01-01'),
                'TANGGAL_SAMPAI'      => $val(['pengalaman_periode_selesai', 'add-pengalaman-periode-selesai'], '2022-01-01'),
                'IMAGE_SK'            => $filePengalaman ?: ''
            ];
            $this->db->table('detail_pengalaman_pegawai')->insert($dataPengalaman);
        }

        // 5. Insert Keahlian Pegawai (Opsional jika ada input)
        $namaKeahlian = $val(['nama_keahlian', 'NAMA_KEAHLIAN', 'keahlian_nama', 'add-keahlian-nama']);
        if (!empty($namaKeahlian) || !empty($fileKeahlian)) {
            $resKhl = $this->db->query("SELECT COALESCE(MAX(KD_KEAHLIAN), 0) + 1 AS new_id FROM detail_keahlian_pegawai")->getRowArray();
            $kdKeahlian = (int)($resKhl['new_id'] ?? 1);

            $dataKeahlian = [
                'KD_KEAHLIAN'         => $kdKeahlian,
                'ID_PEGAWAI'          => $idPegawai,
                'NAMA_KEAHLIAN'       => $namaKeahlian ?: '-',
                'KD_STATUS_KEAHLIAN'  => $valInt(['keahlian_status', 'add-keahlian-status'], 1),
                'KETERANGAN'          => $val(['keahlian_keterangan', 'add-keahlian-keterangan'], '-'),
                'MULAI'               => date('Y-m-d'),
                'SAMPAI'              => date('Y-m-d', strtotime('+5 years')),
                'IMAGE'               => $fileKeahlian ?: ''
            ];
            $this->db->table('detail_keahlian_pegawai')->insert($dataKeahlian);
        }

        // 6. Insert Keluarga Pegawai (Opsional jika ada input)
        $namaKeluarga = $val(['nama_keluarga', 'NAMA_KELUARGA', 'keluarga_nama', 'add-keluarga-nama']);
        if (!empty($namaKeluarga) || !empty($fileKeluarga)) {
            $resKel = $this->db->query("SELECT COALESCE(MAX(KD_KELUARGA), 0) + 1 AS new_id FROM detail_keluarga_pegawai")->getRowArray();
            $kdKeluarga = (int)($resKel['new_id'] ?? 1);

            $jkKelRaw = $val(['keluarga_jk', 'add-keluarga-jk'], '1');
            $jkKel = (strtoupper((string)$jkKelRaw) === 'P' || $jkKelRaw === '2' || stripos((string)$jkKelRaw, 'perempuan') !== false) ? 2 : 1;

            $dataKeluarga = [
                'KD_KELUARGA'        => $kdKeluarga,
                'ID_PEGAWAI'         => $idPegawai,
                'NAMA'               => $namaKeluarga ?: '-',
                'KD_HUBUNGAN'        => $valInt(['keluarga_hubungan', 'add-keluarga-hubungan'], 1),
                'TEMPAT_LAHIR'       => $val(['keluarga_tempat_lahir', 'add-keluarga-tempat-lahir'], '-'),
                'TGL_LAHIR'          => $val(['keluarga_tgl_lahir', 'add-keluarga-tgl-lahir'], '2000-01-01'),
                'KD_PEKERJAAN'       => $valInt(['keluarga_pekerjaan', 'add-keluarga-pekerjaan'], 1),
                'KD_JENJANG'         => $valInt(['keluarga_pendidikan', 'add-keluarga-pendidikan'], 1),
                'KD_STATUS_ASURANSI' => $valInt(['keluarga_status_asuransi', 'add-keluarga-status-asuransi'], 1),
                'KD_ASURANSI'        => $valInt(['keluarga_nama_asuransi', 'add-keluarga-nama-asuransi'], 1),
                'JENIS_KELAMIN'      => $jkKel,
                'KD_GOLDA'           => $valInt(['keluarga_goldar', 'add-keluarga-goldar'], 1),
                'IMAGE_AKTE_KTP'     => $fileKeluarga ?: ''
            ];
            $this->db->table('detail_keluarga_pegawai')->insert($dataKeluarga);
        }

        // 7. Insert Akun Pengguna tb_users (Opsional / Checkbox)
        $buatUser = $val(['buat_user', 'add-buat-user', 'is_create_user']);
        $username = $val(['username', 'add-username']);
        $password = $val(['password', 'add-password']);

        if ($buatUser === '1' || $buatUser === 'true' || $buatUser === 'on' || !empty($username)) {
            $userLogin = !empty($username) ? $username : $nip;
            $passReal  = !empty($password) ? $password : (!empty($tglLahir) ? str_replace('-', '', $tglLahir) : '123456');

            // Cek apakah username sudah ada
            $cekUser = $this->db->table('tb_users')->where('username', $userLogin)->get()->getRowArray();
            if (!$cekUser) {
                $dataUser = [
                    'nip'          => $nip,
                    'nama_lengkap' => $nama,
                    'pass_login'   => md5($passReal),
                    'pass_real'    => $passReal,
                    'foto'         => $fotoProfile ?: '',
                    'level_akses'  => 'operator',
                    'kd_userlevel' => 10,
                    'status_user'  => 'valid',
                    'status'       => 'offline',
                    'id_pegawai'   => $idPegawai,
                    'username'     => $userLogin,
                    'email'        => $val(['email', 'EMAIL', 'add-email']),
                    'allow'        => 1
                ];
                $this->db->table('tb_users')->insert($dataUser);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            // Hapus file yang terlanjur diunggah jika transaksi gagal
            foreach ($uploadedFiles as $f) {
                if (file_exists($f)) {
                    @unlink($f);
                }
            }
            return sendResponse(500, null, 'Gagal menyimpan seluruh data pegawai ke database');
        }

        return sendResponse(200, [
            'id_pegawai' => $idPegawai,
            'nip'        => $nip,
            'nama'       => $nama,
            'foto'       => $fotoProfile
        ], 'Data pegawai baru berhasil ditambahkan');
    }

    public function simpan_data_pegawai()
    {
        return $this->save_data();
    }

    /**
     * Get Pegawai Detail along with all 18 History Tabs
     */
    public function edit_detail($no_auto = 0)
    {
        $id = $no_auto ?: $this->request->getGet('id') ?: $this->request->getGet('no_auto');
        if (empty($id)) {
            return sendResponse(400, null, 'ID atau No Auto Pegawai tidak ditentukan');
        }

        $pegawaiBuilder = $this->db->table('pegawai p');
        $pegawaiBuilder->select('p.*, 
                                 a.AGAMA, 
                                 gd.GOLONGAN_DARAH, 
                                 sk.STATUS_PERKAWINAN, 
                                 pr.PROVINSI, 
                                 kb.KABUPATEN, 
                                 kc.KECAMATAN, 
                                 kl.KELURAHAN,
                                 idnt.IDENTITAS');
        $pegawaiBuilder->join('agama a', 'p.KD_AGAMA = a.KD_AGAMA', 'left');
        $pegawaiBuilder->join('golongan_darah gd', 'p.KD_GOLDA = gd.KD_GOLDA', 'left');
        $pegawaiBuilder->join('status_perkawinan sk', 'p.KD_STATUS_PERKAWINAN = sk.KD_STATUS_PERKAWINAN', 'left');
        $pegawaiBuilder->join('provinsi pr', 'p.KD_PROVINSI = pr.KD_PROVINSI', 'left');
        $pegawaiBuilder->join('kabupaten kb', 'p.KD_KABUPATEN = kb.KD_KABUPATEN', 'left');
        $pegawaiBuilder->join('kecamatan kc', 'p.KD_KECAMATAN = kc.KD_KECAMATAN', 'left');
        $pegawaiBuilder->join('kelurahan kl', 'p.KD_KELURAHAN = kl.KD_KELURAHAN', 'left');
        $pegawaiBuilder->join('identitas idnt', 'p.KD_IDENTITAS = idnt.KD_IDENTITAS', 'left');

        $pegawaiBuilder->groupStart()
            ->where('p.NO_AUTO', is_numeric($id) ? $id : 0)
            ->orWhere('p.ID_PEGAWAI', (string)$id)
            ->orWhere('p.NIP', (string)$id)
            ->groupEnd();

        $pegawai = $pegawaiBuilder->get()->getRowArray();
        if (empty($pegawai)) {
            return sendResponse(404, null, 'Data pegawai tidak ditemukan');
        }

        $idPeg = $pegawai['ID_PEGAWAI'];

        // Riwayat 1-18
        $penempatan = $this->db->table('detail_penempatan_pegawai d')
            ->select('d.*, b.BAGIAN, sb.sub_bagian as SUBBAGIAN, j.JABATAN, g.GOLONGAN, p.PANGKAT, sp.STATUS_PEGAWAI, jp.JENIS_PEGAWAI, l.LOKASI, ek.EKSELON')
            ->join('bagian b', 'd.KD_BAGIAN = b.KD_BAGIAN', 'left')
            ->join('sub_bagian sb', 'd.KD_SUBBAGIAN = sb.kd_sub_bagian', 'left')
            ->join('jabatan j', 'd.KD_JABATAN = j.KD_JABATAN', 'left')
            ->join('golongan g', 'd.KD_GOLONGAN = g.KD_GOLONGAN', 'left')
            ->join('pangkat p', 'd.KD_PANGKAT = p.KD_PANGKAT', 'left')
            ->join('status_pegawai sp', 'd.KD_STATUS_PEGAWAI = sp.KD_STATUS_PEGAWAI', 'left')
            ->join('jenis_pegawai jp', 'd.KD_JENIS_PEGAWAI = jp.KD_JENIS_PEGAWAI', 'left')
            ->join('lokasi l', 'd.KD_LOKASI = l.KD_LOKASI', 'left')
            ->join('ekselon ek', 'd.KD_EKSELON = ek.KD_EKSELON', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_PENEMPATAN', 'DESC')->get()->getResultArray();

        $pendidikan = $this->db->table('detail_pendidikan_pegawai d')
            ->select('d.*, j.JENJANG_PENDIDIKAN, jr.JURUSAN, sl.STATUS_LULUS, jp.JENIS_PENDIDIKAN')
            ->join('jenjang_pendidikan j', 'd.KD_JENJANG = j.KD_JENJANG', 'left')
            ->join('jurusan jr', 'd.KD_JURUSAN = jr.KD_JURUSAN', 'left')
            ->join('status_lulus sl', 'd.KD_STATUS_LULUS = sl.KD_STATUS_LULUS', 'left')
            ->join('jenis_pendidikan jp', 'd.KD_JENIS_PENDIDIKAN = jp.KD_JENIS_PENDIDIKAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_PENDIDIKAN', 'DESC')->get()->getResultArray();

        $pengalaman = $this->db->table('detail_pengalaman_pegawai d')
            ->select('d.*, j.JABATAN, jp.JENIS_PENGALAMAN, pr.PROVINSI, kb.KABUPATEN')
            ->join('jabatan j', 'd.KD_JABATAN = j.KD_JABATAN', 'left')
            ->join('jenis_pengalaman jp', 'd.KD_JENIS_PENGALAMAN = jp.KD_JENIS_PENGALAMAN', 'left')
            ->join('provinsi pr', 'd.KD_PROVINSI = pr.KD_PROVINSI', 'left')
            ->join('kabupaten kb', 'd.KD_KABUPATEN = kb.KD_KABUPATEN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_PENGALAMAN', 'DESC')->get()->getResultArray();

        $keahlian = $this->db->table('detail_keahlian_pegawai d')
            ->select('d.*, sk.STATUS_KEAHLIAN')
            ->join('status_keahlian sk', 'd.KD_STATUS_KEAHLIAN = sk.KD_STATUS_KEAHLIAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_KEAHLIAN', 'DESC')->get()->getResultArray();

        $keluarga = $this->db->table('detail_keluarga_pegawai d')
            ->select('d.*, h.HUBUNGAN, pk.PEKERJAAN, j.JENJANG_PENDIDIKAN, gd.GOLONGAN_DARAH, a.NAMA_ASURANSI')
            ->join('hubungan h', 'd.KD_HUBUNGAN = h.KD_HUBUNGAN', 'left')
            ->join('pekerjaan pk', 'd.KD_PEKERJAAN = pk.KD_PEKERJAAN', 'left')
            ->join('jenjang_pendidikan j', 'd.KD_JENJANG = j.KD_JENJANG', 'left')
            ->join('golongan_darah gd', 'd.KD_GOLDA = gd.KD_GOLDA', 'left')
            ->join('nama_asuransi a', 'd.KD_ASURANSI = a.KD_ASURANSI', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_KELUARGA', 'ASC')->get()->getResultArray();

        $pelatihan = $this->db->table('detail_pelatihan_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_PELATIHAN', 'DESC')->get()->getResultArray();

        $kemampuan = $this->db->table('detail_kemampuan_pegawai d')
            ->select('d.*, l.LEVEL')
            ->join('level_kemampuan l', 'd.KD_LEVEL = l.KD_LEVEL', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('KD_KEMAMPUAN', 'DESC')->get()->getResultArray();

        $str = $this->db->table('detail_str_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_STR', 'DESC')->get()->getResultArray();

        $sip = $this->db->table('detail_sip_pegawai d')
            ->select('d.*, js.JENIS_SIP')
            ->join('jenis_sip js', 'd.KD_JENIS_SIP = js.KD_JENIS_SIP', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('KD_SIP', 'DESC')->get()->getResultArray();

        $mcu = $this->db->table('detail_mcu_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_MCU', 'DESC')->get()->getResultArray();

        $mutasi = $this->db->table('detail_mutasi_dari_pegawai d')
            ->select('d.*, b1.BAGIAN as BAGIAN_DARI, b2.BAGIAN as BAGIAN_MENJADI, j1.JABATAN as JABATAN_DARI, j2.JABATAN as JABATAN_MENJADI, dm.TGL_MUTASI, dm.KETERANGAN as KET_MUTASI')
            ->join('detail_mutasi_menjadi_pegawai dm', 'd.KD_MUTASI_DARI = dm.KD_MUTASI_DARI', 'left')
            ->join('bagian b1', 'd.KD_BAGIAN = b1.KD_BAGIAN', 'left')
            ->join('bagian b2', 'dm.KD_BAGIAN = b2.KD_BAGIAN', 'left')
            ->join('jabatan j1', 'd.KD_JABATAN = j1.KD_JABATAN', 'left')
            ->join('jabatan j2', 'dm.KD_JABATAN = j2.KD_JABATAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('d.KD_MUTASI_DARI', 'DESC')->get()->getResultArray();

        $promosi = $this->db->table('detail_promosi_dari_pegawai d')
            ->select('d.*, j1.JABATAN as JABATAN_DARI, j2.JABATAN as JABATAN_MENJADI, dp.TGL_PROMOSI, dp.KETERANGAN as KET_PROMOSI')
            ->join('detail_promosi_menjadi_pegawai dp', 'd.KD_PROMOSI_DARI = dp.KD_PROMOSI_DARI', 'left')
            ->join('jabatan j1', 'd.KD_JABATAN = j1.KD_JABATAN', 'left')
            ->join('jabatan j2', 'dp.KD_JABATAN = j2.KD_JABATAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('d.KD_PROMOSI_DARI', 'DESC')->get()->getResultArray();

        $kontrak = $this->db->table('detail_kontrak_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_KONTRAK', 'DESC')->get()->getResultArray();

        $cuti = $this->db->table('detail_cuti_pegawai d')
            ->select('d.*, jc.JENIS_CUTI')
            ->join('jenis_cuti jc', 'd.KD_JENIS_CUTI = jc.KD_JENIS_CUTI', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('KD_CUTI', 'DESC')->get()->getResultArray();

        $penghargaan = $this->db->table('detail_penghargaan_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_PENGHARGAAN', 'DESC')->get()->getResultArray();

        $sanksi = $this->db->table('detail_sanksi_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_SANKSI', 'DESC')->get()->getResultArray();

        $phk = $this->db->table('detail_phk_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_PHK', 'DESC')->get()->getResultArray();

        $mArsip = new mArsip();
        $mBuku = new mBuku();
        $arsipRak = $mArsip->get_data_rak(10);
        $bukuTanah = $mBuku->get_data_buku(10);

        $payload = [
            'pegawai'     => $pegawai,
            'penempatan'  => $penempatan,
            'pendidikan'  => $pendidikan,
            'pengalaman'  => $pengalaman,
            'keahlian'    => $keahlian,
            'keluarga'    => $keluarga,
            'pelatihan'   => $pelatihan,
            'kemampuan'   => $kemampuan,
            'str'         => $str,
            'sip'         => $sip,
            'mcu'         => $mcu,
            'mutasi'      => $mutasi,
            'promosi'     => $promosi,
            'kontrak'     => $kontrak,
            'cuti'        => $cuti,
            'penghargaan' => $penghargaan,
            'sanksi'      => $sanksi,
            'phk'         => $phk,
            'arsip_rak'   => $arsipRak,
            'buku_tanah'  => $bukuTanah
        ];

        return sendResponse(200, $payload, 'Detail data pegawai berhasil dimuat');
    }

    /**
     * Update Biodata Pegawai
     */
    public function update_data_pegawai($id_peg = null)
    {
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $id = $id_peg ?? $req['id_pegawai'] ?? $req['ID_PEGAWAI'] ?? null;

        if (empty($id)) {
            return sendResponse(400, null, 'ID Pegawai tidak boleh kosong');
        }

        $peg = $this->db->table('pegawai')
            ->where('ID_PEGAWAI', $id)
            ->orWhere('NO_AUTO', is_numeric($id) ? $id : 0)
            ->get()->getRowArray();

        if (!$peg) {
            return sendResponse(404, null, 'Data pegawai tidak ditemukan');
        }

        $updateData = [];
        $allowed = [
            'NIP', 'NAMA', 'GELAR_DEPAN', 'GELAR_BELAKANG', 'KD_AGAMA', 'KD_IDENTITAS', 'NO_IDENTITAS',
            'KD_GOLDA', 'KD_STATUS_PERKAWINAN', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR', 'JENIS_KELAMIN',
            'ALAMAT', 'KD_PROVINSI', 'KD_KABUPATEN', 'KD_KECAMATAN', 'KD_KELURAHAN', 'KODE_POS',
            'TELEPON', 'HANDPHONE', 'EMAIL', 'FOTO', 'status_aktif'
        ];

        foreach ($allowed as $f) {
            $keyLower = strtolower($f);
            if (isset($req[$keyLower])) {
                $updateData[$f] = $req[$keyLower];
            } elseif (isset($req[$f])) {
                $updateData[$f] = $req[$f];
            }
        }

        if (!empty($updateData)) {
            $this->db->table('pegawai')->where('ID_PEGAWAI', $peg['ID_PEGAWAI'])->update($updateData);
        }

        return sendResponse(200, ['id_pegawai' => $peg['ID_PEGAWAI']], 'Biodata pegawai berhasil diperbarui');
    }

    /**
     * Upload Foto Profil Pegawai
     */
    public function upload_foto($id_peg = null)
    {
        $id = $id_peg ?? $this->request->getPost('id_pegawai') ?? $this->request->getPost('ID_PEGAWAI');
        $file = $this->request->getFile('foto') ?? $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return sendResponse(400, null, 'File foto tidak valid atau belum diunggah');
        }

        $idPeg = $this->resolveIdPegawai($id);
        $newName = 'peg_' . $idPeg . '_' . time() . '.' . $file->guessExtension();

        $uploadPath = ROOTPATH . '../assets/user';
        if (!is_dir($uploadPath)) {
            @mkdir($uploadPath, 0777, true);
        }

        $file->move($uploadPath, $newName);

        $this->db->table('pegawai')->where('ID_PEGAWAI', $idPeg)->update(['FOTO' => $newName]);

        return sendResponse(200, ['foto' => $newName, 'url' => 'assets/user/' . $newName], 'Foto berhasil diperbarui');
    }

    /**
     * Upload Berkas Pendukung Riwayat (uploads/)
     */
    public function upload_berkas()
    {
        $file = $this->request->getFile('berkas') ?? $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return sendResponse(400, null, 'File berkas tidak valid atau belum diunggah');
        }

        $newName = 'doc_' . time() . '_' . $file->getRandomName();
        $uploadPath = ROOTPATH . '../uploads';
        if (!is_dir($uploadPath)) {
            @mkdir($uploadPath, 0777, true);
        }

        $file->move($uploadPath, $newName);

        return sendResponse(200, [
            'file_name' => $newName,
            'url'       => 'uploads/' . $newName
        ], 'Berkas berhasil diunggah');
    }

    /**
     * Helper resolve ID_PEGAWAI from no_auto or ID_PEGAWAI
     */
    private function resolveIdPegawai($val)
    {
        if (empty($val)) return 0;
        $peg = $this->db->table('pegawai')
            ->select('ID_PEGAWAI')
            ->where('NO_AUTO', is_numeric($val) ? $val : 0)
            ->orWhere('ID_PEGAWAI', $val)
            ->get()->getRowArray();
        return $peg['ID_PEGAWAI'] ?? $val;
    }

    // ==========================================
    // CRUD RIWAYAT 1-18
    // ==========================================

    public function update_data_penempatan($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdPenempatan = $req['kd_penempatan'] ?? $req['KD_PENEMPATAN'] ?? null;

        $data = [
            'ID_PEGAWAI'        => $idPeg,
            'KD_BAGIAN'         => $req['kd_bagian'] ?? null,
            'KD_SUBBAGIAN'      => $req['kd_subbagian'] ?? null,
            'KD_JABATAN'        => $req['kd_jabatan'] ?? null,
            'KD_GOLONGAN'       => $req['kd_golongan'] ?? null,
            'KD_PANGKAT'        => $req['kd_pangkat'] ?? null,
            'KD_STATUS_PEGAWAI' => $req['kd_status_pegawai'] ?? null,
            'KD_JENIS_PEGAWAI'  => $req['kd_jenis_pegawai'] ?? null,
            'KD_LOKASI'         => $req['kd_lokasi'] ?? null,
            'KD_EKSELON'        => $req['kd_ekselon'] ?? null,
            'TGL_MASUK'         => $req['tgl_masuk'] ?? null,
            'TGL_SK'            => $req['tgl_sk'] ?? null,
            'NO_SK'             => $req['no_sk'] ?? ''
        ];

        if (!empty($kdPenempatan)) {
            $this->db->table('detail_penempatan_pegawai')->where('KD_PENEMPATAN', $kdPenempatan)->update($data);
        } else {
            $max = $this->db->table('detail_penempatan_pegawai')->selectMax('KD_PENEMPATAN', 'max_id')->get()->getRowArray();
            $data['KD_PENEMPATAN'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_penempatan_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data penempatan berhasil disimpan');
    }

    public function update_data_pendidikan($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdPendidikan = $req['kd_pendidikan'] ?? $req['KD_PENDIDIKAN'] ?? null;

        $data = [
            'ID_PEGAWAI'          => $idPeg,
            'KD_JENJANG'          => $req['kd_jenjang'] ?? null,
            'KD_JURUSAN'          => $req['kd_jurusan'] ?? null,
            'KD_STATUS_LULUS'     => $req['kd_status_lulus'] ?? null,
            'KD_JENIS_PENDIDIKAN' => $req['kd_jenis_pendidikan'] ?? null,
            'NAMA_SEKOLAH'        => $req['nama_sekolah'] ?? '',
            'NO_IJAZAH'           => $req['no_ijazah'] ?? '',
            'TGL_IJAZAH'          => $req['tgl_ijazah'] ?? null,
            'IPK'                 => $req['ipk'] ?? null
        ];

        if (!empty($kdPendidikan)) {
            $this->db->table('detail_pendidikan_pegawai')->where('KD_PENDIDIKAN', $kdPendidikan)->update($data);
        } else {
            $max = $this->db->table('detail_pendidikan_pegawai')->selectMax('KD_PENDIDIKAN', 'max_id')->get()->getRowArray();
            $data['KD_PENDIDIKAN'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_pendidikan_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data pendidikan berhasil disimpan');
    }

    public function update_data_pengalaman($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdPengalaman = $req['kd_pengalaman'] ?? $req['KD_PENGALAMAN'] ?? null;

        $data = [
            'ID_PEGAWAI'         => $idPeg,
            'NAMA_PERUSAHAAN'    => $req['nama_perusahaan'] ?? '',
            'KD_JABATAN'         => $req['kd_jabatan'] ?? null,
            'KD_JENIS_PENGALAMAN'=> $req['kd_jenis_pengalaman'] ?? null,
            'KD_PROVINSI'        => $req['kd_provinsi'] ?? null,
            'KD_KABUPATEN'       => $req['kd_kabupaten'] ?? null,
            'TGL_MASUK'          => $req['tgl_masuk'] ?? null,
            'TGL_KELUAR'         => $req['tgl_keluar'] ?? null,
            'KETERANGAN'         => $req['keterangan'] ?? ''
        ];

        if (!empty($kdPengalaman)) {
            $this->db->table('detail_pengalaman_pegawai')->where('KD_PENGALAMAN', $kdPengalaman)->update($data);
        } else {
            $max = $this->db->table('detail_pengalaman_pegawai')->selectMax('KD_PENGALAMAN', 'max_id')->get()->getRowArray();
            $data['KD_PENGALAMAN'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_pengalaman_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data pengalaman kerja berhasil disimpan');
    }

    public function update_data_keahlian($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdKeahlian = $req['kd_keahlian'] ?? $req['KD_KEAHLIAN'] ?? null;

        $data = [
            'ID_PEGAWAI'        => $idPeg,
            'KEAHLIAN'          => $req['keahlian'] ?? '',
            'KD_STATUS_KEAHLIAN'=> $req['kd_status_keahlian'] ?? null,
            'KETERANGAN'        => $req['keterangan'] ?? ''
        ];

        if (!empty($kdKeahlian)) {
            $this->db->table('detail_keahlian_pegawai')->where('KD_KEAHLIAN', $kdKeahlian)->update($data);
        } else {
            $max = $this->db->table('detail_keahlian_pegawai')->selectMax('KD_KEAHLIAN', 'max_id')->get()->getRowArray();
            $data['KD_KEAHLIAN'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_keahlian_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data keahlian berhasil disimpan');
    }

    public function update_data_keluarga($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdKeluarga = $req['kd_keluarga'] ?? $req['KD_KELUARGA'] ?? null;

        $data = [
            'ID_PEGAWAI'     => $idPeg,
            'NAMA_KELUARGA'  => $req['nama_keluarga'] ?? '',
            'KD_HUBUNGAN'    => $req['kd_hub'] ?? $req['kd_hubungan'] ?? null,
            'KD_JENJANG'     => $req['kd_jenjang'] ?? null,
            'KD_PEKERJAAN'   => $req['kd_pekerjaan'] ?? null,
            'KD_GOLDA'       => $req['kd_golda'] ?? null,
            'KD_ASURANSI'    => $req['kd_asuransi'] ?? null,
            'NO_ASURANSI'    => $req['no_asuransi'] ?? '',
            'TEMPAT_LAHIR'   => $req['tempat_lahir'] ?? '',
            'TANGGAL_LAHIR'  => $req['tanggal_lahir'] ?? null,
            'JENIS_KELAMIN'  => $req['jenis_kelamin'] ?? 'L'
        ];

        if (!empty($kdKeluarga)) {
            $this->db->table('detail_keluarga_pegawai')->where('KD_KELUARGA', $kdKeluarga)->update($data);
        } else {
            $max = $this->db->table('detail_keluarga_pegawai')->selectMax('KD_KELUARGA', 'max_id')->get()->getRowArray();
            $data['KD_KELUARGA'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_keluarga_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data keluarga berhasil disimpan');
    }

    public function update_data_pelatihan($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdPelatihan = $req['kd_pelatihan'] ?? $req['KD_PELATIHAN'] ?? null;

        $data = [
            'ID_PEGAWAI'       => $idPeg,
            'NAMA_PELATIHAN'   => $req['nama_pelatihan'] ?? '',
            'PENYELENGGARA'    => $req['penyelenggara'] ?? '',
            'TEMPAT_PELATIHAN' => $req['tempat_pelatihan'] ?? '',
            'TGL_MULAI'        => $req['tgl_mulai'] ?? null,
            'TGL_SELESAI'      => $req['tgl_selesai'] ?? null,
            'NO_SERTIFIKAT'    => $req['no_sertifikat'] ?? ''
        ];

        if (!empty($kdPelatihan)) {
            $this->db->table('detail_pelatihan_pegawai')->where('KD_PELATIHAN', $kdPelatihan)->update($data);
        } else {
            $max = $this->db->table('detail_pelatihan_pegawai')->selectMax('KD_PELATIHAN', 'max_id')->get()->getRowArray();
            $data['KD_PELATIHAN'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_pelatihan_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data pelatihan berhasil disimpan');
    }

    public function update_data_kemampuan($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdKemampuan = $req['kd_kemampuan'] ?? $req['KD_KEMAMPUAN'] ?? null;

        $data = [
            'ID_PEGAWAI'     => $idPeg,
            'NAMA_KEMAMPUAN' => $req['nama_kemampuan'] ?? '',
            'KD_LEVEL'       => $req['kd_level'] ?? null,
            'KETERANGAN'     => $req['keterangan'] ?? ''
        ];

        if (!empty($kdKemampuan)) {
            $this->db->table('detail_kemampuan_pegawai')->where('KD_KEMAMPUAN', $kdKemampuan)->update($data);
        } else {
            $max = $this->db->table('detail_kemampuan_pegawai')->selectMax('KD_KEMAMPUAN', 'max_id')->get()->getRowArray();
            $data['KD_KEMAMPUAN'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_kemampuan_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data kemampuan berhasil disimpan');
    }

    public function update_data_str($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdStr = $req['kd_str'] ?? $req['KD_STR'] ?? null;

        $data = [
            'ID_PEGAWAI' => $idPeg,
            'NO_STR'     => $req['no_str'] ?? '',
            'TGL_TERBIT' => $req['tgl_terbit'] ?? null,
            'TGL_KADALUARSA' => $req['tgl_kadaluarsa'] ?? null
        ];

        if (!empty($kdStr)) {
            $this->db->table('detail_str_pegawai')->where('KD_STR', $kdStr)->update($data);
        } else {
            $max = $this->db->table('detail_str_pegawai')->selectMax('KD_STR', 'max_id')->get()->getRowArray();
            $data['KD_STR'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_str_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data STR berhasil disimpan');
    }

    public function update_data_sip($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdSip = $req['kd_sip'] ?? $req['KD_SIP'] ?? null;

        $data = [
            'ID_PEGAWAI'     => $idPeg,
            'KD_JENIS_SIP'   => $req['kd_jenis_sip'] ?? null,
            'NO_SIP'         => $req['no_sip'] ?? '',
            'TGL_TERBIT'     => $req['tgl_terbit'] ?? null,
            'TGL_KADALUARSA' => $req['tgl_kadaluarsa'] ?? null
        ];

        if (!empty($kdSip)) {
            $this->db->table('detail_sip_pegawai')->where('KD_SIP', $kdSip)->update($data);
        } else {
            $max = $this->db->table('detail_sip_pegawai')->selectMax('KD_SIP', 'max_id')->get()->getRowArray();
            $data['KD_SIP'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_sip_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data SIP berhasil disimpan');
    }

    public function update_data_mcu($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdMcu = $req['kd_mcu'] ?? $req['KD_MCU'] ?? null;

        $data = [
            'ID_PEGAWAI' => $idPeg,
            'TGL_MCU'    => $req['tgl_mcu'] ?? date('Y-m-d'),
            'HASIL_MCU'  => $req['hasil_mcu'] ?? '',
            'CATATAN'    => $req['catatan'] ?? ''
        ];

        if (!empty($kdMcu)) {
            $this->db->table('detail_mcu_pegawai')->where('KD_MCU', $kdMcu)->update($data);
        } else {
            $max = $this->db->table('detail_mcu_pegawai')->selectMax('KD_MCU', 'max_id')->get()->getRowArray();
            $data['KD_MCU'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_mcu_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data MCU berhasil disimpan');
    }

    public function update_data_mutasi($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdMutasiDari = $req['kd_mutasi_dari'] ?? $req['KD_MUTASI_DARI'] ?? null;

        $peg = $this->db->table('pegawai')->where('ID_PEGAWAI', $idPeg)->get()->getRowArray();
        $nip = $peg['NIP'] ?? '';
        $nama = $peg['NAMA'] ?? '';

        $dataDari = [
            'ID_PEGAWAI' => $idPeg,
            'NIP'        => $nip,
            'NAMA'       => $nama,
            'KD_BAGIAN'  => $req['kd_bagian_dari'] ?? null,
            'KD_JABATAN' => $req['kd_jabatan_dari'] ?? null,
            'NO_SURAT'   => $req['no_surat'] ?? '',
            'TGL_SURAT'  => $req['tgl_surat'] ?? date('Y-m-d')
        ];

        $dataMenjadi = [
            'ID_PEGAWAI'    => $idPeg,
            'KD_BAGIAN'     => $req['kd_bagian_menjadi'] ?? null,
            'KD_JABATAN'    => $req['kd_jabatan_menjadi'] ?? null,
            'TGL_MUTASI'    => $req['tgl_mutasi'] ?? date('Y-m-d'),
            'KETERANGAN'    => $req['keterangan'] ?? '',
            'NAMA_PIMPINAN' => $req['nama_pimpinan'] ?? ''
        ];

        if (!empty($kdMutasiDari)) {
            $this->db->table('detail_mutasi_dari_pegawai')->where('KD_MUTASI_DARI', $kdMutasiDari)->update($dataDari);
            $this->db->table('detail_mutasi_menjadi_pegawai')->where('KD_MUTASI_DARI', $kdMutasiDari)->update($dataMenjadi);
        } else {
            $max = $this->db->table('detail_mutasi_dari_pegawai')->selectMax('KD_MUTASI_DARI', 'max_id')->get()->getRowArray();
            $newKd = ($max['max_id'] ?? 0) + 1;
            $dataDari['KD_MUTASI_DARI'] = $newKd;
            $dataMenjadi['KD_MUTASI_DARI'] = $newKd;

            $maxMenjadi = $this->db->table('detail_mutasi_menjadi_pegawai')->selectMax('KD_MUTASI_MENJADI', 'max_m')->get()->getRowArray();
            $dataMenjadi['KD_MUTASI_MENJADI'] = ($maxMenjadi['max_m'] ?? 0) + 1;

            $this->db->table('detail_mutasi_dari_pegawai')->insert($dataDari);
            $this->db->table('detail_mutasi_menjadi_pegawai')->insert($dataMenjadi);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data mutasi berhasil disimpan');
    }

    public function update_data_promosi($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdPromosiDari = $req['kd_promosi_dari'] ?? $req['KD_PROMOSI_DARI'] ?? null;

        $peg = $this->db->table('pegawai')->where('ID_PEGAWAI', $idPeg)->get()->getRowArray();
        $nip = $peg['NIP'] ?? '';
        $nama = $peg['NAMA'] ?? '';

        $dataDari = [
            'ID_PEGAWAI' => $idPeg,
            'NIP'        => $nip,
            'NAMA'       => $nama,
            'KD_BAGIAN'  => $req['kd_bagian_dari'] ?? null,
            'KD_JABATAN' => $req['kd_jabatan_dari'] ?? null,
            'NO_SURAT'   => $req['no_surat'] ?? '',
            'TGL_SURAT'  => $req['tgl_surat'] ?? date('Y-m-d')
        ];

        $dataMenjadi = [
            'ID_PEGAWAI'    => $idPeg,
            'KD_BAGIAN'     => $req['kd_bagian_menjadi'] ?? null,
            'KD_JABATAN'    => $req['kd_jabatan_menjadi'] ?? null,
            'TGL_PROMOSI'   => $req['tgl_promosi'] ?? date('Y-m-d'),
            'KETERANGAN'    => $req['keterangan'] ?? '',
            'NAMA_PIMPINAN' => $req['nama_pimpinan'] ?? ''
        ];

        if (!empty($kdPromosiDari)) {
            $this->db->table('detail_promosi_dari_pegawai')->where('KD_PROMOSI_DARI', $kdPromosiDari)->update($dataDari);
            $this->db->table('detail_promosi_menjadi_pegawai')->where('KD_PROMOSI_DARI', $kdPromosiDari)->update($dataMenjadi);
        } else {
            $max = $this->db->table('detail_promosi_dari_pegawai')->selectMax('KD_PROMOSI_DARI', 'max_id')->get()->getRowArray();
            $newKd = ($max['max_id'] ?? 0) + 1;
            $dataDari['KD_PROMOSI_DARI'] = $newKd;
            $dataMenjadi['KD_PROMOSI_DARI'] = $newKd;

            $maxMenjadi = $this->db->table('detail_promosi_menjadi_pegawai')->selectMax('KD_PROMOSI_MENJADI', 'max_m')->get()->getRowArray();
            $dataMenjadi['KD_PROMOSI_MENJADI'] = ($maxMenjadi['max_m'] ?? 0) + 1;

            $this->db->table('detail_promosi_dari_pegawai')->insert($dataDari);
            $this->db->table('detail_promosi_menjadi_pegawai')->insert($dataMenjadi);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data promosi berhasil disimpan');
    }

    public function update_data_kontrak($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdKontrak = $req['kd_kontrak'] ?? $req['KD_KONTRAK'] ?? null;

        $data = [
            'ID_PEGAWAI' => $idPeg,
            'NO_KONTRAK' => $req['no_kontrak'] ?? '',
            'TGL_MULAI'  => $req['tgl_mulai'] ?? null,
            'TGL_AKHIR'  => $req['tgl_akhir'] ?? null,
            'KETERANGAN' => $req['keterangan'] ?? ''
        ];

        if (!empty($kdKontrak)) {
            $this->db->table('detail_kontrak_pegawai')->where('KD_KONTRAK', $kdKontrak)->update($data);
        } else {
            $max = $this->db->table('detail_kontrak_pegawai')->selectMax('KD_KONTRAK', 'max_id')->get()->getRowArray();
            $data['KD_KONTRAK'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_kontrak_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data kontrak kerja berhasil disimpan');
    }

    public function update_data_cuti($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdCuti = $req['kd_cuti'] ?? $req['KD_CUTI'] ?? null;

        $data = [
            'ID_PEGAWAI'   => $idPeg,
            'KD_JENIS_CUTI'=> $req['kd_jenis_cuti'] ?? null,
            'TGL_MULAI'    => $req['tgl_mulai'] ?? null,
            'TGL_SELESAI'  => $req['tgl_selesai'] ?? null,
            'JML_HARI'     => $req['jml_hari'] ?? 1,
            'ALASAN'       => $req['alasan'] ?? ''
        ];

        if (!empty($kdCuti)) {
            $this->db->table('detail_cuti_pegawai')->where('KD_CUTI', $kdCuti)->update($data);
        } else {
            $max = $this->db->table('detail_cuti_pegawai')->selectMax('KD_CUTI', 'max_id')->get()->getRowArray();
            $data['KD_CUTI'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_cuti_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data cuti berhasil disimpan');
    }

    public function update_data_penghargaan($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdPenghargaan = $req['kd_penghargaan'] ?? $req['KD_PENGHARGAAN'] ?? null;

        $data = [
            'ID_PEGAWAI'       => $idPeg,
            'NAMA_PENGHARGAAN' => $req['nama_penghargaan'] ?? '',
            'PEMBERI'          => $req['pemberi'] ?? '',
            'TGL_PENGHARGAAN'  => $req['tgl_penghargaan'] ?? null,
            'KETERANGAN'       => $req['keterangan'] ?? ''
        ];

        if (!empty($kdPenghargaan)) {
            $this->db->table('detail_penghargaan_pegawai')->where('KD_PENGHARGAAN', $kdPenghargaan)->update($data);
        } else {
            $max = $this->db->table('detail_penghargaan_pegawai')->selectMax('KD_PENGHARGAAN', 'max_id')->get()->getRowArray();
            $data['KD_PENGHARGAAN'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_penghargaan_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data penghargaan berhasil disimpan');
    }

    public function update_data_sanksi($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdSanksi = $req['kd_sanksi'] ?? $req['KD_SANKSI'] ?? null;

        $data = [
            'ID_PEGAWAI'  => $idPeg,
            'NAMA_SANKSI' => $req['nama_sanksi'] ?? '',
            'PEMBERI'     => $req['pemberi'] ?? '',
            'TGL_SANKSI'  => $req['tgl_sanksi'] ?? null,
            'KETERANGAN'  => $req['keterangan'] ?? ''
        ];

        if (!empty($kdSanksi)) {
            $this->db->table('detail_sanksi_pegawai')->where('KD_SANKSI', $kdSanksi)->update($data);
        } else {
            $max = $this->db->table('detail_sanksi_pegawai')->selectMax('KD_SANKSI', 'max_id')->get()->getRowArray();
            $data['KD_SANKSI'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_sanksi_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data sanksi berhasil disimpan');
    }

    public function update_data_phk($no_auto = null)
    {
        $idPeg = $this->resolveIdPegawai($no_auto);
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $kdPhk = $req['kd_phk'] ?? $req['KD_PHK'] ?? null;

        $data = [
            'ID_PEGAWAI' => $idPeg,
            'NO_SK_PHK'  => $req['no_sk_phk'] ?? '',
            'TGL_PHK'    => $req['tgl_phk'] ?? null,
            'ALASAN_PHK' => $req['alasan_phk'] ?? '',
            'KETERANGAN' => $req['keterangan'] ?? ''
        ];

        if (!empty($kdPhk)) {
            $this->db->table('detail_phk_pegawai')->where('KD_PHK', $kdPhk)->update($data);
        } else {
            $max = $this->db->table('detail_phk_pegawai')->selectMax('KD_PHK', 'max_id')->get()->getRowArray();
            $data['KD_PHK'] = ($max['max_id'] ?? 0) + 1;
            $this->db->table('detail_phk_pegawai')->insert($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data PHK berhasil disimpan');
    }

    public function update_data_arsip_rak()
    {
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $noRak = $req['no_rak'] ?? null;
        $mArsip = new mArsip();

        $data = [
            'no_lemari' => $req['no_lemari'] ?? '',
            'nama_rak'  => $req['nama_rak'] ?? ''
        ];

        if (!empty($noRak)) {
            $mArsip->update_rak($noRak, $data);
        } else {
            $mArsip->tambah_rak($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data rak arsip berhasil disimpan');
    }

    public function update_data_buku_tanah()
    {
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $idBuku = $req['id_buku'] ?? null;
        $mBuku = new mBuku();

        $data = [
            'kd_kec'      => $req['kd_kec'] ?? null,
            'kd_des'      => $req['kd_des'] ?? null,
            'no_hak'      => $req['no_hak'] ?? '',
            'jenis_hak'   => $req['jenis_hak'] ?? null,
            'edisi'       => $req['edisi'] ?? '',
            'tahun'       => $req['tahun'] ?? date('Y'),
            'kd_lemari'   => $req['kd_lemari'] ?? null,
            'kd_rak'      => $req['kd_rak'] ?? null,
            'kd_album'    => $req['kd_album'] ?? null,
            'kd_baris'    => $req['kd_baris'] ?? null,
            'pinjam'      => $req['pinjam'] ?? 0,
            'status_buku' => $req['status_buku'] ?? 1
        ];

        if (!empty($idBuku)) {
            $mBuku->update_buku($idBuku, $data);
        } else {
            $mBuku->tambah_buku($data);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data buku tanah berhasil disimpan');
    }

    public function update_data_rak()
    {
        return $this->update_data_arsip_rak();
    }

    public function update_data_buku()
    {
        return $this->update_data_buku_tanah();
    }

    /**
     * Delete Data Universal
     */
    public function delete_data()
    {
        $req = $this->request->getJSON(true) ?: $this->request->getPost();
        $mode = $req['mode'] ?? $this->request->getGet('mode');
        $id   = $req['id'] ?? $req['kode'] ?? $this->request->getGet('id');

        if (empty($mode) || empty($id)) {
            return sendResponse(400, null, 'Parameter mode dan id/kode wajib disertakan');
        }

        switch (strtolower($mode)) {
            case 'pegawai':
                $peg = $this->db->table('pegawai')->where('NO_AUTO', $id)->orWhere('ID_PEGAWAI', $id)->get()->getRowArray();
                if ($peg) {
                    $idPeg = $peg['ID_PEGAWAI'];
                    $this->db->table('pegawai')->where('ID_PEGAWAI', $idPeg)->update(['status_aktif' => 0]);
                }
                break;

            case 'penempatan':
                $this->db->table('detail_penempatan_pegawai')->where('KD_PENEMPATAN', $id)->delete();
                break;

            case 'pendidikan':
                $this->db->table('detail_pendidikan_pegawai')->where('KD_PENDIDIKAN', $id)->delete();
                break;

            case 'pengalaman':
                $this->db->table('detail_pengalaman_pegawai')->where('KD_PENGALAMAN', $id)->delete();
                break;

            case 'keahlian':
                $this->db->table('detail_keahlian_pegawai')->where('KD_KEAHLIAN', $id)->delete();
                break;

            case 'keluarga':
                $this->db->table('detail_keluarga_pegawai')->where('KD_KELUARGA', $id)->delete();
                break;

            case 'pelatihan':
                $this->db->table('detail_pelatihan_pegawai')->where('KD_PELATIHAN', $id)->delete();
                break;

            case 'kemampuan':
                $this->db->table('detail_kemampuan_pegawai')->where('KD_KEMAMPUAN', $id)->delete();
                break;

            case 'str':
                $this->db->table('detail_str_pegawai')->where('KD_STR', $id)->delete();
                break;

            case 'sip':
                $this->db->table('detail_sip_pegawai')->where('KD_SIP', $id)->delete();
                break;

            case 'mcu':
                $this->db->table('detail_mcu_pegawai')->where('KD_MCU', $id)->delete();
                break;

            case 'mutasi':
                $this->db->table('detail_mutasi_dari_pegawai')->where('KD_MUTASI_DARI', $id)->delete();
                $this->db->table('detail_mutasi_menjadi_pegawai')->where('KD_MUTASI_DARI', $id)->delete();
                break;

            case 'promosi':
                $this->db->table('detail_promosi_dari_pegawai')->where('KD_PROMOSI_DARI', $id)->delete();
                $this->db->table('detail_promosi_menjadi_pegawai')->where('KD_PROMOSI_DARI', $id)->delete();
                break;

            case 'kontrak':
                $this->db->table('detail_kontrak_pegawai')->where('KD_KONTRAK', $id)->delete();
                break;

            case 'cuti':
                $this->db->table('detail_cuti_pegawai')->where('KD_CUTI', $id)->delete();
                break;

            case 'penghargaan':
                $this->db->table('detail_penghargaan_pegawai')->where('KD_PENGHARGAAN', $id)->delete();
                break;

            case 'sanksi':
                $this->db->table('detail_sanksi_pegawai')->where('KD_SANKSI', $id)->delete();
                break;

            case 'phk':
                $this->db->table('detail_phk_pegawai')->where('KD_PHK', $id)->delete();
                break;

            case 'rak':
            case 'arsip_rak':
                $mArsip = new mArsip();
                $mArsip->hapus_rak($id);
                break;

            case 'buku':
            case 'buku_tanah':
                $mBuku = new mBuku();
                $mBuku->hapus_buku($id);
                break;

            default:
                return sendResponse(400, null, 'Mode penghapusan tidak dikenali: ' . $mode);
        }

        return sendResponse(200, ['results' => 'sukses'], 'Data berhasil dihapus');
    }

    /**
     * Endpoint Perubahan Status Karyawan (Mutasi, Promosi, Kontrak, Cuti, PHK)
     */
    public function get_perubahan_status()
    {
        $jenis = strtolower($this->request->getGet('jenis') ?? 'semua');
        $search = trim($this->request->getGet('search') ?? '');
        $limit = (int)($this->request->getGet('limit') ?? 20);
        $page = (int)($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $limit;

        $results = [];

        // 1. Mutasi
        if ($jenis === 'semua' || $jenis === 'mutasi') {
            $mutasi = $this->db->table('detail_mutasi_menjadi_pegawai m')
                ->select('m.KD_MUTASI_MENJADI, m.KD_MUTASI_DARI, m.ID_PEGAWAI, m.TGL_MUTASI as TANGGAL, m.KETERANGAN, 
                          p.NAMA, p.NIP, 
                          bd.BAGIAN as BAGIAN_DARI, bm.BAGIAN as BAGIAN_MENJADI,
                          jd.JABATAN as JABATAN_DARI, jm.JABATAN as JABATAN_MENJADI,
                          d.NO_SURAT, d.TGL_SURAT')
                ->join('pegawai p', 'p.ID_PEGAWAI = m.ID_PEGAWAI', 'left')
                ->join('detail_mutasi_dari_pegawai d', 'd.KD_MUTASI_DARI = m.KD_MUTASI_DARI', 'left')
                ->join('bagian bd', 'bd.KD_BAGIAN = d.KD_BAGIAN', 'left')
                ->join('bagian bm', 'bm.KD_BAGIAN = m.KD_BAGIAN', 'left')
                ->join('jabatan jd', 'jd.KD_JABATAN = d.KD_JABATAN', 'left')
                ->join('jabatan jm', 'jm.KD_JABATAN = m.KD_JABATAN', 'left');

            if ($search) {
                $mutasi->groupStart()
                    ->like('p.NAMA', $search)
                    ->orLike('p.NIP', $search)
                    ->orLike('d.NO_SURAT', $search)
                    ->groupEnd();
            }
            foreach ($mutasi->orderBy('m.TGL_MUTASI', 'DESC')->limit($limit)->get()->getResultArray() as $row) {
                $results[] = [
                    'id'            => $row['KD_MUTASI_MENJADI'],
                    'id_pegawai'    => $row['ID_PEGAWAI'],
                    'nama'          => $row['NAMA'] ?? 'Pegawai',
                    'nip'           => $row['NIP'] ?? '-',
                    'jenis'         => 'Mutasi',
                    'tanggal'       => $row['TANGGAL'] ?: ($row['TGL_SURAT'] ?? '-'),
                    'no_surat'      => $row['NO_SURAT'] ?? '-',
                    'sebelum'       => ($row['BAGIAN_DARI'] ?? '-') . ' - ' . ($row['JABATAN_DARI'] ?? '-'),
                    'menjadi'       => ($row['BAGIAN_MENJADI'] ?? '-') . ' - ' . ($row['JABATAN_MENJADI'] ?? '-'),
                    'keterangan'    => $row['KETERANGAN'] ?? '-'
                ];
            }
        }

        // 2. Promosi
        if ($jenis === 'semua' || $jenis === 'promosi') {
            $promosi = $this->db->table('detail_promosi_menjadi_pegawai m')
                ->select('m.KD_PROMOSI_MENJADI, m.KD_PROMOSI_DARI, m.ID_PEGAWAI, m.TGL_PROMOSI as TANGGAL, m.KETERANGAN, 
                          p.NAMA, p.NIP, 
                          bd.BAGIAN as BAGIAN_DARI, bm.BAGIAN as BAGIAN_MENJADI,
                          jd.JABATAN as JABATAN_DARI, jm.JABATAN as JABATAN_MENJADI,
                          d.NO_SURAT, d.TGL_SURAT')
                ->join('pegawai p', 'p.ID_PEGAWAI = m.ID_PEGAWAI', 'left')
                ->join('detail_promosi_dari_pegawai d', 'd.KD_PROMOSI_DARI = m.KD_PROMOSI_DARI', 'left')
                ->join('bagian bd', 'bd.KD_BAGIAN = d.KD_BAGIAN', 'left')
                ->join('bagian bm', 'bm.KD_BAGIAN = m.KD_BAGIAN', 'left')
                ->join('jabatan jd', 'jd.KD_JABATAN = d.KD_JABATAN', 'left')
                ->join('jabatan jm', 'jm.KD_JABATAN = m.KD_JABATAN', 'left');

            if ($search) {
                $promosi->groupStart()
                    ->like('p.NAMA', $search)
                    ->orLike('p.NIP', $search)
                    ->orLike('d.NO_SURAT', $search)
                    ->groupEnd();
            }
            foreach ($promosi->orderBy('m.TGL_PROMOSI', 'DESC')->limit($limit)->get()->getResultArray() as $row) {
                $results[] = [
                    'id'            => $row['KD_PROMOSI_MENJADI'],
                    'id_pegawai'    => $row['ID_PEGAWAI'],
                    'nama'          => $row['NAMA'] ?? 'Pegawai',
                    'nip'           => $row['NIP'] ?? '-',
                    'jenis'         => 'Promosi',
                    'tanggal'       => $row['TANGGAL'] ?: ($row['TGL_SURAT'] ?? '-'),
                    'no_surat'      => $row['NO_SURAT'] ?? '-',
                    'sebelum'       => ($row['BAGIAN_DARI'] ?? '-') . ' - ' . ($row['JABATAN_DARI'] ?? '-'),
                    'menjadi'       => ($row['BAGIAN_MENJADI'] ?? '-') . ' - ' . ($row['JABATAN_MENJADI'] ?? '-'),
                    'keterangan'    => $row['KETERANGAN'] ?? '-'
                ];
            }
        }

        // 3. Kontrak
        if ($jenis === 'semua' || $jenis === 'kontrak') {
            $kontrak = $this->db->table('detail_kontrak_pegawai k')
                ->select('k.*, p.NAMA, p.NIP')
                ->join('pegawai p', 'p.ID_PEGAWAI = k.ID_PEGAWAI', 'left');

            if ($search) {
                $kontrak->groupStart()
                    ->like('p.NAMA', $search)
                    ->orLike('p.NIP', $search)
                    ->groupEnd();
            }
            foreach ($kontrak->orderBy('k.KD_KONTRAK', 'DESC')->limit($limit)->get()->getResultArray() as $row) {
                $results[] = [
                    'id'            => $row['KD_KONTRAK'] ?? '',
                    'id_pegawai'    => $row['ID_PEGAWAI'] ?? '',
                    'nama'          => $row['NAMA'] ?? 'Pegawai',
                    'nip'           => $row['NIP'] ?? '-',
                    'jenis'         => 'Kontrak PKWT',
                    'tanggal'       => ($row['TGL_MULAI'] ?? '-') . ' s/d ' . ($row['TGL_AKHIR'] ?? ($row['TANGGAL_AKHIR'] ?? '-')),
                    'no_surat'      => $row['NO_KONTRAK'] ?? ($row['NOMOR_KONTRAK'] ?? ($row['NO_SURAT'] ?? '-')),
                    'sebelum'       => 'Pegawai Kontrak',
                    'menjadi'       => 'Masa Berlaku Kontrak',
                    'keterangan'    => $row['KETERANGAN'] ?? '-'
                ];
            }
        }

        // 4. Cuti
        if ($jenis === 'semua' || $jenis === 'cuti') {
            $cuti = $this->db->table('detail_cuti_pegawai c')
                ->select('c.*, p.NAMA, p.NIP, jc.JENIS_CUTI')
                ->join('pegawai p', 'p.ID_PEGAWAI = c.ID_PEGAWAI', 'left')
                ->join('jenis_cuti jc', 'c.KD_JENIS_CUTI = jc.KD_JENIS_CUTI', 'left');

            if ($search) {
                $cuti->groupStart()
                    ->like('p.NAMA', $search)
                    ->orLike('p.NIP', $search)
                    ->groupEnd();
            }
            foreach ($cuti->orderBy('c.KD_CUTI', 'DESC')->limit($limit)->get()->getResultArray() as $row) {
                $results[] = [
                    'id'            => $row['KD_CUTI'] ?? '',
                    'id_pegawai'    => $row['ID_PEGAWAI'] ?? '',
                    'nama'          => $row['NAMA'] ?? 'Pegawai',
                    'nip'           => $row['NIP'] ?? '-',
                    'jenis'         => 'Cuti (' . ($row['JENIS_CUTI'] ?? 'Tahunan') . ')',
                    'tanggal'       => ($row['TGL_MULAI'] ?? '-') . ' s/d ' . ($row['TGL_AKHIR'] ?? '-'),
                    'no_surat'      => ($row['LAMA'] ?? '0') . ' Hari',
                    'sebelum'       => 'Cuti Kerja',
                    'menjadi'       => 'Izin Cuti Kerja',
                    'keterangan'    => $row['KETERANGAN'] ?? '-'
                ];
            }
        }

        // 5. PHK
        if ($jenis === 'semua' || $jenis === 'phk') {
            $phk = $this->db->table('detail_phk_pegawai ph')
                ->select('ph.*, p.NAMA, p.NIP, jp.JENIS_PHK')
                ->join('pegawai p', 'p.ID_PEGAWAI = ph.ID_PEGAWAI', 'left')
                ->join('jenis_phk jp', 'jp.KD_JENIS_PHK = ph.KD_JENIS_PHK', 'left');

            if ($search) {
                $phk->groupStart()
                    ->like('p.NAMA', $search)
                    ->orLike('p.NIP', $search)
                    ->groupEnd();
            }
            foreach ($phk->orderBy('ph.KD_PHK', 'DESC')->limit($limit)->get()->getResultArray() as $row) {
                $results[] = [
                    'id'            => $row['KD_PHK'] ?? '',
                    'id_pegawai'    => $row['ID_PEGAWAI'] ?? '',
                    'nama'          => $row['NAMA'] ?? 'Pegawai',
                    'nip'           => $row['NIP'] ?? '-',
                    'jenis'         => 'PHK / Berhenti (' . ($row['JENIS_PHK'] ?? 'Terminasi') . ')',
                    'tanggal'       => $row['TGL_BERHENTI'] ?? ($row['TANGGAL'] ?? '-'),
                    'no_surat'      => $row['NO_SURAT'] ?? '-',
                    'sebelum'       => 'Pegawai Aktif',
                    'menjadi'       => 'Status Nonaktif',
                    'keterangan'    => $row['KETERANGAN'] ?? '-'
                ];
            }
        }

        // Urutkan tanggal descending
        usort($results, function($a, $b) {
            return strcmp($b['tanggal'], $a['tanggal']);
        });

        $total = count($results);
        $paginated = array_slice($results, $offset, $limit);

        return sendResponse(200, [
            'data'  => $paginated,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit
        ], 'Data perubahan status berhasil diambil');
    }
}
