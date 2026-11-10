<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\mArsip;
use App\Models\mBuku;

class cTambahPegawai extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['response', 'url']);
    }

    /**
     * Listing Data Pegawai (Server-side Datatables / CSR Fetch Support)
     */
    public function index($id = null)
    {
        if ($id !== null && is_numeric($id)) {
            return $this->detail($id);
        }

        $page   = (int) ($this->request->getGet('page') ?? $this->request->getPost('page') ?? 1);
        $limit  = (int) ($this->request->getGet('limit') ?? $this->request->getPost('limit') ?? 10);
        $search = trim($this->request->getGet('search') ?? $this->request->getPost('search') ?? '');

        $kdBagian        = $this->request->getGet('kd_bagian') ?? $this->request->getPost('kd_bagian');
        $kdJabatan       = $this->request->getGet('kd_jabatan') ?? $this->request->getPost('kd_jabatan');
        $kdStatusPegawai = $this->request->getGet('kd_status_pegawai') ?? $this->request->getPost('kd_status_pegawai');
        $statusAktif     = $this->request->getGet('status_aktif') ?? $this->request->getPost('status_aktif');

        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;
        $offset = ($page - 1) * $limit;

        $builder = $this->db->table('pegawai p');
        $builder->select('
            p.NO_AUTO,
            p.ID_PEGAWAI,
            p.NIP,
            p.NAMA,
            p.TEMPAT_LAHIR,
            p.TANGGAL_LAHIR,
            p.JENIS_KELAMIN,
            p.HANDPHONE,
            p.EMAIL,
            p.FOTO,
            p.status_aktif,
            b.BAGIAN,
            j.JABATAN,
            sp.STATUS_PEGAWAI,
            pk.PANGKAT,
            g.GOLONGAN
        ');

        // Join to penempatan (get latest penempatan per pegawai)
        $builder->join('(
            SELECT dp1.*
            FROM detail_penempatan_pegawai dp1
            INNER JOIN (
                SELECT ID_PEGAWAI, MAX(KD_PENEMPATAN) as max_penempatan
                FROM detail_penempatan_pegawai
                GROUP BY ID_PEGAWAI
            ) dp2 ON dp1.ID_PEGAWAI = dp2.ID_PEGAWAI AND dp1.KD_PENEMPATAN = dp2.max_penempatan
        ) d', 'p.ID_PEGAWAI = d.ID_PEGAWAI', 'left');

        $builder->join('bagian b', 'd.KD_BAGIAN = b.KD_BAGIAN', 'left');
        $builder->join('jabatan j', 'd.KD_JABATAN = j.KD_JABATAN', 'left');
        $builder->join('status_pegawai sp', 'd.KD_STATUS_PEGAWAI = sp.KD_STATUS_PEGAWAI', 'left');
        $builder->join('pangkat pk', 'd.KD_PANGKAT = pk.KD_PANGKAT', 'left');
        $builder->join('golongan g', 'd.KD_GOLONGAN = g.KD_GOLONGAN', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('p.NIP', $search)
                ->orLike('p.NAMA', $search)
                ->orLike('b.BAGIAN', $search)
                ->orLike('j.JABATAN', $search)
                ->groupEnd();
        }

        if (!empty($kdBagian)) {
            $builder->where('d.KD_BAGIAN', $kdBagian);
        }

        if (!empty($kdJabatan)) {
            $builder->where('d.KD_JABATAN', $kdJabatan);
        }

        if (!empty($kdStatusPegawai)) {
            $builder->where('d.KD_STATUS_PEGAWAI', $kdStatusPegawai);
        }

        if ($statusAktif !== null && $statusAktif !== '') {
            $builder->where('p.status_aktif', $statusAktif);
        }

        $totalBuilder = clone $builder;
        $totalRows = $totalBuilder->countAllResults(false);

        $builder->orderBy('p.NO_AUTO', 'DESC');
        $builder->limit($limit, $offset);

        $rows = $builder->get()->getResultArray();

        $totalPages = ($totalRows > 0) ? (int) ceil($totalRows / $limit) : 1;

        $response = [
            'total'        => $totalRows,
            'total_pages'  => $totalPages,
            'current_page' => $page,
            'limit'        => $limit,
            'data'         => $rows,
            'rows'         => $rows
        ];

        return sendResponse(200, $response, 'Data pegawai berhasil dimuat');
    }

    public function get_data_pegawai()
    {
        return $this->index();
    }

    /**
     * Get single detail for quick lookup
     */
    public function detail($no_auto)
    {
        $pegawai = $this->db->table('pegawai p')
            ->select('p.*, b.BAGIAN, j.JABATAN, sp.STATUS_PEGAWAI')
            ->join('(
                SELECT dp1.*
                FROM detail_penempatan_pegawai dp1
                INNER JOIN (
                    SELECT ID_PEGAWAI, MAX(KD_PENEMPATAN) as max_penempatan
                    FROM detail_penempatan_pegawai
                    GROUP BY ID_PEGAWAI
                ) dp2 ON dp1.ID_PEGAWAI = dp2.ID_PEGAWAI AND dp1.KD_PENEMPATAN = dp2.max_penempatan
            ) d', 'p.ID_PEGAWAI = d.ID_PEGAWAI', 'left')
            ->join('bagian b', 'd.KD_BAGIAN = b.KD_BAGIAN', 'left')
            ->join('jabatan j', 'd.KD_JABATAN = j.KD_JABATAN', 'left')
            ->join('status_pegawai sp', 'd.KD_STATUS_PEGAWAI = sp.KD_STATUS_PEGAWAI', 'left')
            ->where('p.NO_AUTO', $no_auto)
            ->get()->getRowArray();

        if (!$pegawai) {
            return sendResponse(404, null, 'Pegawai tidak ditemukan');
        }

        return sendResponse(200, $pegawai);
    }

    /**
     * Cetak Riwayat Hidup Komprehensif (18 Data Tab Riwayat)
     */
    public function cetak_riwayat($no_auto = 0)
    {
        $id = $no_auto ?: $this->request->getGet('id') ?: $this->request->getGet('no_auto');
        if (empty($id)) {
            return sendResponse(400, null, 'ID atau No Auto Pegawai tidak ditentukan');
        }

        // 1. Data Pokok Pegawai
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

        if (is_numeric($id)) {
            $pegawaiBuilder->where('p.NO_AUTO', $id);
        } else {
            $pegawaiBuilder->where('p.ID_PEGAWAI', $id);
        }

        $pegawai = $pegawaiBuilder->get()->getRowArray();
        if (empty($pegawai)) {
            return sendResponse(404, null, 'Data pegawai tidak ditemukan');
        }

        $idPeg = $pegawai['ID_PEGAWAI'];

        // 2. Riwayat Penempatan
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

        // 3. Riwayat Pendidikan
        $pendidikan = $this->db->table('detail_pendidikan_pegawai d')
            ->select('d.*, j.JENJANG_PENDIDIKAN, jr.JURUSAN, sl.STATUS_LULUS, jp.JENIS_PENDIDIKAN')
            ->join('jenjang_pendidikan j', 'd.KD_JENJANG = j.KD_JENJANG', 'left')
            ->join('jurusan jr', 'd.KD_JURUSAN = jr.KD_JURUSAN', 'left')
            ->join('status_lulus sl', 'd.KD_STATUS_LULUS = sl.KD_STATUS_LULUS', 'left')
            ->join('jenis_pendidikan jp', 'd.KD_JENIS_PENDIDIKAN = jp.KD_JENIS_PENDIDIKAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_PENDIDIKAN', 'DESC')->get()->getResultArray();

        // 4. Riwayat Pengalaman Kerja
        $pengalaman = $this->db->table('detail_pengalaman_pegawai d')
            ->select('d.*, j.JABATAN, jp.JENIS_PENGALAMAN, pr.PROVINSI, kb.KABUPATEN')
            ->join('jabatan j', 'd.KD_JABATAN = j.KD_JABATAN', 'left')
            ->join('jenis_pengalaman jp', 'd.KD_JENIS_PENGALAMAN = jp.KD_JENIS_PENGALAMAN', 'left')
            ->join('provinsi pr', 'd.KD_PROVINSI = pr.KD_PROVINSI', 'left')
            ->join('kabupaten kb', 'd.KD_KABUPATEN = kb.KD_KABUPATEN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_PENGALAMAN', 'DESC')->get()->getResultArray();

        // 5. Riwayat Keahlian
        $keahlian = $this->db->table('detail_keahlian_pegawai d')
            ->select('d.*, sk.STATUS_KEAHLIAN')
            ->join('status_keahlian sk', 'd.KD_STATUS_KEAHLIAN = sk.KD_STATUS_KEAHLIAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_KEAHLIAN', 'DESC')->get()->getResultArray();

        // 6. Riwayat Keluarga
        $keluarga = $this->db->table('detail_keluarga_pegawai d')
            ->select('d.*, h.HUBUNGAN, pk.PEKERJAAN, j.JENJANG_PENDIDIKAN, gd.GOLONGAN_DARAH, a.NAMA_ASURANSI')
            ->join('hubungan h', 'd.KD_HUBUNGAN = h.KD_HUBUNGAN', 'left')
            ->join('pekerjaan pk', 'd.KD_PEKERJAAN = pk.KD_PEKERJAAN', 'left')
            ->join('jenjang_pendidikan j', 'd.KD_JENJANG = j.KD_JENJANG', 'left')
            ->join('golongan_darah gd', 'd.KD_GOLDA = gd.KD_GOLDA', 'left')
            ->join('nama_asuransi a', 'd.KD_ASURANSI = a.KD_ASURANSI', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)
            ->orderBy('d.KD_KELUARGA', 'ASC')->get()->getResultArray();

        // 7. Riwayat Pelatihan
        $pelatihan = $this->db->table('detail_pelatihan_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_PELATIHAN', 'DESC')->get()->getResultArray();

        // 8. Riwayat Kemampuan Bahasa / Lainnya
        $kemampuan = $this->db->table('detail_kemampuan_pegawai d')
            ->select('d.*, l.LEVEL')
            ->join('level_kemampuan l', 'd.KD_LEVEL = l.KD_LEVEL', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('KD_KEMAMPUAN', 'DESC')->get()->getResultArray();

        // 9. Riwayat STR
        $str = $this->db->table('detail_str_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_STR', 'DESC')->get()->getResultArray();

        // 10. Riwayat SIP
        $sip = $this->db->table('detail_sip_pegawai d')
            ->select('d.*, js.JENIS_SIP')
            ->join('jenis_sip js', 'd.KD_JENIS_SIP = js.KD_JENIS_SIP', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('KD_SIP', 'DESC')->get()->getResultArray();

        // 11. Riwayat MCU
        $mcu = $this->db->table('detail_mcu_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_MCU', 'DESC')->get()->getResultArray();

        // 12. Riwayat Mutasi
        $mutasi = $this->db->table('detail_mutasi_dari_pegawai d')
            ->select('d.*, b1.BAGIAN as BAGIAN_DARI, b2.BAGIAN as BAGIAN_MENJADI, j1.JABATAN as JABATAN_DARI, j2.JABATAN as JABATAN_MENJADI, dm.TGL_MUTASI, dm.KETERANGAN as KET_MUTASI')
            ->join('detail_mutasi_menjadi_pegawai dm', 'd.KD_MUTASI_DARI = dm.KD_MUTASI_DARI', 'left')
            ->join('bagian b1', 'd.KD_BAGIAN = b1.KD_BAGIAN', 'left')
            ->join('bagian b2', 'dm.KD_BAGIAN = b2.KD_BAGIAN', 'left')
            ->join('jabatan j1', 'd.KD_JABATAN = j1.KD_JABATAN', 'left')
            ->join('jabatan j2', 'dm.KD_JABATAN = j2.KD_JABATAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('d.KD_MUTASI_DARI', 'DESC')->get()->getResultArray();

        // 13. Riwayat Promosi
        $promosi = $this->db->table('detail_promosi_dari_pegawai d')
            ->select('d.*, j1.JABATAN as JABATAN_DARI, j2.JABATAN as JABATAN_MENJADI, dp.TGL_PROMOSI, dp.KETERANGAN as KET_PROMOSI')
            ->join('detail_promosi_menjadi_pegawai dp', 'd.KD_PROMOSI_DARI = dp.KD_PROMOSI_DARI', 'left')
            ->join('jabatan j1', 'd.KD_JABATAN = j1.KD_JABATAN', 'left')
            ->join('jabatan j2', 'dp.KD_JABATAN = j2.KD_JABATAN', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('d.KD_PROMOSI_DARI', 'DESC')->get()->getResultArray();

        // 14. Riwayat Kontrak Kerja
        $kontrak = $this->db->table('detail_kontrak_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_KONTRAK', 'DESC')->get()->getResultArray();

        // 15. Riwayat Cuti
        $cuti = $this->db->table('detail_cuti_pegawai d')
            ->select('d.*, jc.JENIS_CUTI')
            ->join('jenis_cuti jc', 'd.KD_JENIS_CUTI = jc.KD_JENIS_CUTI', 'left')
            ->where('d.ID_PEGAWAI', $idPeg)->orderBy('d.KD_CUTI', 'DESC')->get()->getResultArray();

        // 16. Riwayat Penghargaan
        $penghargaan = $this->db->table('detail_penghargaan_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_PENGHARGAAN', 'DESC')->get()->getResultArray();

        // 17. Riwayat Sanksi
        $sanksi = $this->db->table('detail_sanksi_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_SANKSI', 'DESC')->get()->getResultArray();

        // 18. Riwayat PHK
        $phk = $this->db->table('detail_phk_pegawai')
            ->where('ID_PEGAWAI', $idPeg)->orderBy('KD_PHK', 'DESC')->get()->getResultArray();

        // Arsip Rak & Buku Tanah Data Reference
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

        return sendResponse(200, $payload, 'Data riwayat lengkap pegawai berhasil dimuat');
    }
}
