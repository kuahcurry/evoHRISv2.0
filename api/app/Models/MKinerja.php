<?php

namespace App\Models;

use CodeIgniter\Model;

class MKinerja extends Model
{
    /**
     * Get units / departments
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
     * Get master units of output & time
     */
    public function getMasterSatuan()
    {
        $output = $this->db->table('m_satuan_output')
            ->orderBy('id_output', 'ASC')
            ->get()->getResultArray();

        $waktu = $this->db->table('m_satuan_waktu')
            ->orderBy('id_waktu', 'ASC')
            ->get()->getResultArray();

        return [
            'output' => $output,
            'waktu'  => $waktu
        ];
    }

    /**
     * Get master perilaku categories
     */
    public function getMasterPerilaku()
    {
        return $this->db->table('ekinerja_master_perilaku')
            ->orderBy('id_perilaku', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Get master standard evaluation benchmarks
     */
    public function getMasterStandarPenilaian(?int $kdJabatan = null)
    {
        $builder = $this->db->table('master_standar_penilaian')
            ->orderBy('id_standar_penilaian', 'ASC');

        if ($kdJabatan !== null) {
            $builder->where('kd_jabatan', $kdJabatan);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * 1. PROGRAM KERJA / KEGIATAN
     */
    public function getProgramKerja(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('ekinerja_program_kerja x')
            ->select('x.ID_KGT, x.DESC_KGT, x.KD_BAGIAN, x.ID_PEGAWAI, x.IS_DELL,
                      b.BAGIAN as nama_bagian, p.NAMA as nama_pegawai, p.NIP')
            ->join('bagian b', 'x.KD_BAGIAN = b.KD_BAGIAN', 'left')
            ->join('pegawai p', 'x.ID_PEGAWAI = CAST(p.ID_PEGAWAI AS CHAR) OR x.ID_PEGAWAI = p.NIP', 'left')
            ->where('x.IS_DELL', 0);

        if (!empty($filters['kd_bagian'])) {
            $builder->where('x.KD_BAGIAN', $filters['kd_bagian']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('x.DESC_KGT', $s)
                ->orLike('b.BAGIAN', $s)
                ->orLike('p.NAMA', $s)
                ->orLike('p.NIP', $s)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $data = $builder->orderBy('x.ID_KGT', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        return [
            'total' => $total,
            'data'  => $data
        ];
    }

    public function saveProgramKerja(array $data)
    {
        $id = $data['ID_KGT'] ?? null;
        $payload = [
            'DESC_KGT'    => $data['DESC_KGT'] ?? '',
            'KD_BAGIAN'   => !empty($data['KD_BAGIAN']) ? (int) $data['KD_BAGIAN'] : null,
            'ID_PEGAWAI'  => !empty($data['ID_PEGAWAI']) ? (string) $data['ID_PEGAWAI'] : null,
            'IS_DELL'     => 0,
            'ID_INSTANSI' => '0'
        ];

        if (!empty($id)) {
            $this->db->table('ekinerja_program_kerja')->where('ID_KGT', $id)->update($payload);
            return (int) $id;
        } else {
            $this->db->table('ekinerja_program_kerja')->insert($payload);
            return $this->db->insertID();
        }
    }

    public function deleteProgramKerja(int $id)
    {
        return $this->db->table('ekinerja_program_kerja')
            ->where('ID_KGT', $id)
            ->update(['IS_DELL' => 1]);
    }

    /**
     * 2. TARGET SKP
     */
    public function getTargetSkp(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('ekinerja_target_skp ts')
            ->select('ts.id_targetskp, ts.uraian, ts.ak, ts.output,
                      COALESCE(NULLIF(ts.output_satuan, ""), b.nama_output) as output_satuan,
                      ts.mutu, ts.waktu,
                      COALESCE(NULLIF(ts.waktu_satuan, ""), c.nama_waktu) as waktu_satuan,
                      ts.biaya, ts.status, ts.id_pegawai, ts.tgl_entri, ts.tgl_approve, ts.tgl_reject,
                      ts.status_tolak, ts.kd_programkerja, ts.id_output, ts.id_waktu,
                      pk.DESC_KGT as nama_programkerja,
                      COALESCE(p.NAMA, pim.NAMA, pg.nama, ts.id_pegawai) as nama_pegawai,
                      COALESCE(p.NIP, pim.NIP, pg.nip, ts.id_pegawai) as NIP,
                      COALESCE(bg.BAGIAN, pg.bagian, "-") as nama_bagian,
                      COALESCE(pg.jabatan, "-") as nama_jabatan')
            ->join('m_satuan_output b', 'ts.id_output = b.id_output', 'left')
            ->join('m_satuan_waktu c', 'ts.id_waktu = c.id_waktu', 'left')
            ->join('ekinerja_program_kerja pk', 'ts.kd_programkerja = pk.ID_KGT', 'left')
            ->join('pegawai p', 'ts.id_pegawai = p.NIP OR ts.id_pegawai = p.ID_PEGAWAI OR REPLACE(ts.id_pegawai, ".", "") = p.NIP', 'left')
            ->join('pegawai_id_mesin pim', 'ts.id_pegawai = pim.ID_PEGAWAI OR ts.id_pegawai = pim.NIP OR REPLACE(ts.id_pegawai, ".", "") = pim.NIP', 'left')
            ->join('pegawai_grand pg', 'ts.id_pegawai = pg.nip OR ts.id_pegawai = pg.id_pegawai', 'left')
            ->join('detail_penempatan_pegawai dpp', 'dpp.ID_PEGAWAI = p.ID_PEGAWAI', 'left')
            ->join('bagian bg', 'dpp.KD_BAGIAN = bg.KD_BAGIAN', 'left');

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(ts.tgl_entri)', (int) $filters['tahun']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $builder->where('ts.status', (string) $filters['status']);
        }

        if (!empty($filters['id_pegawai'])) {
            $builder->groupStart()
                ->where('ts.id_pegawai', (string) $filters['id_pegawai'])
                ->orWhere('p.ID_PEGAWAI', (string) $filters['id_pegawai'])
                ->orWhere('p.NIP', (string) $filters['id_pegawai'])
                ->groupEnd();
        }

        if (!empty($filters['kd_bagian'])) {
            $builder->where('dpp.KD_BAGIAN', $filters['kd_bagian']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('ts.uraian', $s)
                ->orLike('pk.DESC_KGT', $s)
                ->orLike('p.NAMA', $s)
                ->orLike('p.NIP', $s)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        // Stats summary for target
        $statsBuilder = $this->db->table('ekinerja_target_skp ts')
            ->select('COUNT(*) as total_targets,
                      SUM(CASE WHEN ts.status = "1" THEN 1 ELSE 0 END) as approved,
                      SUM(CASE WHEN ts.status = "0" OR ts.status IS NULL THEN 1 ELSE 0 END) as pending,
                      SUM(CASE WHEN ts.status = "2" THEN 1 ELSE 0 END) as rejected');
        if (!empty($filters['tahun'])) {
            $statsBuilder->where('YEAR(ts.tgl_entri)', (int) $filters['tahun']);
        }
        $summary = $statsBuilder->get()->getRowArray() ?? [
            'total_targets' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0
        ];

        $data = $builder->orderBy('ts.id_targetskp', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        return [
            'total'   => $total,
            'summary' => $summary,
            'data'    => $data
        ];
    }

    public function saveTargetSkp(array $data)
    {
        $id = $data['id_targetskp'] ?? null;
        $payload = [
            'uraian'          => $data['uraian'] ?? '',
            'ak'              => !empty($data['ak']) ? (float) $data['ak'] : 0,
            'output'          => !empty($data['output']) ? (int) $data['output'] : 0,
            'output_satuan'   => $data['output_satuan'] ?? null,
            'mutu'            => !empty($data['mutu']) ? (string) $data['mutu'] : '100',
            'waktu'           => !empty($data['waktu']) ? (int) $data['waktu'] : 1,
            'waktu_satuan'    => $data['waktu_satuan'] ?? 'Bulan',
            'biaya'           => !empty($data['biaya']) ? (float) $data['biaya'] : 0,
            'kd_programkerja' => !empty($data['kd_programkerja']) ? (string) $data['kd_programkerja'] : null,
            'id_output'       => !empty($data['id_output']) ? (int) $data['id_output'] : null,
            'id_waktu'        => !empty($data['id_waktu']) ? (int) $data['id_waktu'] : null,
            'id_pegawai'      => !empty($data['id_pegawai']) ? (string) $data['id_pegawai'] : '1',
            'status'          => $data['status'] ?? '0'
        ];

        if (!empty($id)) {
            $this->db->table('ekinerja_target_skp')->where('id_targetskp', $id)->update($payload);
            return (int) $id;
        } else {
            $payload['tgl_entri'] = date('Y-m-d H:i:s');
            $this->db->table('ekinerja_target_skp')->insert($payload);
            return $this->db->insertID();
        }
    }

    public function approveTargetSkp(int $idTarget, string $status = '1', ?string $catatan = null)
    {
        $update = [
            'status' => $status
        ];

        if ($status === '1') {
            $update['tgl_approve'] = date('Y-m-d H:i:s');
            $update['tgl_reject'] = null;
            $update['status_tolak'] = null;
        } else {
            $update['tgl_reject'] = date('Y-m-d H:i:s');
            $update['status_tolak'] = $catatan ?? 'Ditolak oleh atasan';
        }

        return $this->db->table('ekinerja_target_skp')
            ->where('id_targetskp', $idTarget)
            ->update($update);
    }

    /**
     * 3. AKTIFITAS HARIAN (LOGBOOK)
     */
    public function getAktifitas(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('ekinerja_aktifitas ea')
            ->select('ea.id_aktifitas, ea.aktifitas, ea.aktifitas_output,
                      COALESCE(NULLIF(ea.aktifitas_output_satuan, ""), b.nama_output) as aktifitas_output_satuan,
                      ea.jam_mulai, ea.jam_selesai, ea.id_target, ea.id_pegawai,
                      ea.aktifitas_status, ea.aktifitas_approve, ea.tgl_aktifitas,
                      ea.tgl_aktifitas_approve, ea.catatan, ea.id_output,
                      ts.uraian as nama_target_skp,
                      COALESCE(p.NAMA, pim.NAMA, pg.nama, ea.id_pegawai) as nama_pegawai,
                      COALESCE(p.NIP, pim.NIP, pg.nip, ea.id_pegawai) as NIP,
                      COALESCE(bg.BAGIAN, pg.bagian, "-") as nama_bagian,
                      COALESCE(pg.jabatan, "-") as nama_jabatan')
            ->join('m_satuan_output b', 'ea.id_output = b.id_output', 'left')
            ->join('ekinerja_target_skp ts', 'ea.id_target = ts.id_targetskp', 'left')
            ->join('pegawai p', 'ea.id_pegawai = p.NIP OR ea.id_pegawai = p.ID_PEGAWAI OR REPLACE(ea.id_pegawai, ".", "") = p.NIP', 'left')
            ->join('pegawai_id_mesin pim', 'ea.id_pegawai = pim.ID_PEGAWAI OR ea.id_pegawai = pim.NIP OR REPLACE(ea.id_pegawai, ".", "") = pim.NIP', 'left')
            ->join('pegawai_grand pg', 'ea.id_pegawai = pg.nip OR ea.id_pegawai = pg.id_pegawai', 'left')
            ->join('detail_penempatan_pegawai dpp', 'dpp.ID_PEGAWAI = p.ID_PEGAWAI', 'left')
            ->join('bagian bg', 'dpp.KD_BAGIAN = bg.KD_BAGIAN', 'left');

        if (!empty($filters['bulan'])) {
            $builder->where('DATE_FORMAT(ea.tgl_aktifitas, "%Y-%m")', $filters['bulan']);
        } elseif (!empty($filters['tahun'])) {
            $builder->where('YEAR(ea.tgl_aktifitas)', (int) $filters['tahun']);
        }

        if (!empty($filters['tgl_mulai'])) {
            $builder->where('ea.tgl_aktifitas >=', $filters['tgl_mulai'] . ' 00:00:00');
        }
        if (!empty($filters['tgl_sampai'])) {
            $builder->where('ea.tgl_aktifitas <=', $filters['tgl_sampai'] . ' 23:59:59');
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $builder->where('ea.aktifitas_status', (string) $filters['status']);
        }

        if (!empty($filters['kd_bagian'])) {
            $builder->where('dpp.KD_BAGIAN', $filters['kd_bagian']);
        }

        if (!empty($filters['id_pegawai'])) {
            $builder->groupStart()
                ->where('ea.id_pegawai', (string) $filters['id_pegawai'])
                ->orWhere('p.ID_PEGAWAI', (string) $filters['id_pegawai'])
                ->orWhere('p.NIP', (string) $filters['id_pegawai'])
                ->groupEnd();
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('ea.aktifitas', $s)
                ->orLike('ea.catatan', $s)
                ->orLike('ts.uraian', $s)
                ->orLike('p.NAMA', $s)
                ->orLike('p.NIP', $s)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        // Calculate summary for activities
        $statsBuilder = $this->db->table('ekinerja_aktifitas ea')
            ->select('COUNT(*) as total_aktifitas,
                      SUM(CASE WHEN ea.aktifitas_status = "1" THEN 1 ELSE 0 END) as approved,
                      SUM(CASE WHEN ea.aktifitas_status = "0" OR ea.aktifitas_status IS NULL THEN 1 ELSE 0 END) as pending,
                      SUM(CASE WHEN ea.aktifitas_status = "2" THEN 1 ELSE 0 END) as rejected');
        if (!empty($filters['bulan'])) {
            $statsBuilder->where('DATE_FORMAT(ea.tgl_aktifitas, "%Y-%m")', $filters['bulan']);
        } elseif (!empty($filters['tahun'])) {
            $statsBuilder->where('YEAR(ea.tgl_aktifitas)', (int) $filters['tahun']);
        }
        $summary = $statsBuilder->get()->getRowArray() ?? [
            'total_aktifitas' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0
        ];

        $data = $builder->orderBy('ea.id_aktifitas', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        // Calculate duration for each record
        foreach ($data as &$row) {
            if (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) {
                $t1 = strtotime($row['jam_mulai']);
                $t2 = strtotime($row['jam_selesai']);
                $row['durasi_menit'] = ($t2 > $t1) ? round(($t2 - $t1) / 60) : 0;
            } else {
                $row['durasi_menit'] = 0;
            }
        }

        return [
            'total'   => $total,
            'summary' => $summary,
            'data'    => $data
        ];
    }

    public function saveAktifitas(array $data)
    {
        $id = $data['id_aktifitas'] ?? null;
        $payload = [
            'aktifitas'               => $data['aktifitas'] ?? '',
            'aktifitas_output'        => !empty($data['aktifitas_output']) ? (string) $data['aktifitas_output'] : '1',
            'aktifitas_output_satuan' => $data['aktifitas_output_satuan'] ?? null,
            'jam_mulai'               => $data['jam_mulai'] ?? date('Y-m-d H:i:s'),
            'jam_selesai'             => $data['jam_selesai'] ?? date('Y-m-d H:i:s'),
            'id_target'               => !empty($data['id_target']) ? (int) $data['id_target'] : null,
            'id_pegawai'              => !empty($data['id_pegawai']) ? (string) $data['id_pegawai'] : '1',
            'aktifitas_status'        => $data['aktifitas_status'] ?? '0',
            'tgl_aktifitas'           => $data['tgl_aktifitas'] ?? date('Y-m-d H:i:s'),
            'catatan'                 => $data['catatan'] ?? null,
            'id_output'               => !empty($data['id_output']) ? (int) $data['id_output'] : null
        ];

        if (!empty($id)) {
            $this->db->table('ekinerja_aktifitas')->where('id_aktifitas', $id)->update($payload);
            return (int) $id;
        } else {
            $this->db->table('ekinerja_aktifitas')->insert($payload);
            return $this->db->insertID();
        }
    }

    public function approveAktifitas(int $idAktifitas, string $status = '1', ?string $catatan = null)
    {
        $update = [
            'aktifitas_status'       => $status,
            'aktifitas_approve'      => ($status === '1') ? '1' : '0',
            'tgl_aktifitas_approve'  => date('Y-m-d H:i:s')
        ];

        if ($catatan !== null) {
            $update['catatan'] = $catatan;
        }

        return $this->db->table('ekinerja_aktifitas')
            ->where('id_aktifitas', $idAktifitas)
            ->update($update);
    }

    /**
     * 4. REALISASI SKP & PERHITUNGAN CAPAIAN
     */
    public function getRealisasiSkp(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table('ekinerja_realisasi_skp r')
            ->select('r.id_realisasiskp, r.id_target, r.ak as realisasi_ak, r.output as realisasi_output,
                      COALESCE(NULLIF(r.output_satuan, ""), b.nama_output) as output_satuan,
                      r.mutu as realisasi_mutu, r.waktu as realisasi_waktu,
                      COALESCE(NULLIF(r.waktu_satuan, ""), c.nama_waktu) as waktu_satuan,
                      r.biaya as realisasi_biaya, r.status as realisasi_status, r.tgl_entri,
                      r.tgl_approve, r.status_tolak, r.id_pegawai,
                      ts.uraian as nama_target_skp, ts.output as target_output,
                      ts.mutu as target_mutu, ts.waktu as target_waktu, ts.biaya as target_biaya,
                      ts.ak as target_ak,
                      COALESCE(p.NAMA, pim.NAMA, pg.nama, r.id_pegawai) as nama_pegawai,
                      COALESCE(p.NIP, pim.NIP, pg.nip, r.id_pegawai) as NIP,
                      COALESCE(bg.BAGIAN, pg.bagian, "-") as nama_bagian,
                      COALESCE(pg.jabatan, "-") as nama_jabatan')
            ->join('ekinerja_target_skp ts', 'r.id_target = ts.id_targetskp', 'left')
            ->join('m_satuan_output b', 'r.id_output = b.id_output', 'left')
            ->join('m_satuan_waktu c', 'r.id_waktu = c.id_waktu', 'left')
            ->join('pegawai p', 'r.id_pegawai = p.NIP OR r.id_pegawai = p.ID_PEGAWAI OR REPLACE(r.id_pegawai, ".", "") = p.NIP', 'left')
            ->join('pegawai_id_mesin pim', 'r.id_pegawai = pim.ID_PEGAWAI OR r.id_pegawai = pim.NIP OR REPLACE(r.id_pegawai, ".", "") = pim.NIP', 'left')
            ->join('pegawai_grand pg', 'r.id_pegawai = pg.nip OR r.id_pegawai = pg.id_pegawai', 'left')
            ->join('detail_penempatan_pegawai dpp', 'dpp.ID_PEGAWAI = p.ID_PEGAWAI', 'left')
            ->join('bagian bg', 'dpp.KD_BAGIAN = bg.KD_BAGIAN', 'left');

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(r.tgl_entri)', (int) $filters['tahun']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $builder->where('r.status', (string) $filters['status']);
        }

        if (!empty($filters['kd_bagian'])) {
            $builder->where('dpp.KD_BAGIAN', $filters['kd_bagian']);
        }

        if (!empty($filters['id_pegawai'])) {
            $builder->groupStart()
                ->where('r.id_pegawai', (string) $filters['id_pegawai'])
                ->orWhere('p.ID_PEGAWAI', (string) $filters['id_pegawai'])
                ->orWhere('p.NIP', (string) $filters['id_pegawai'])
                ->groupEnd();
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('ts.uraian', $s)
                ->orLike('p.NAMA', $s)
                ->orLike('p.NIP', $s)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $data = $builder->orderBy('r.id_realisasiskp', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        $sumCapaian = 0;
        $countEvaluated = 0;

        foreach ($data as &$row) {
            $tgtOut = (float) ($row['target_output'] ?? 1);
            if ($tgtOut <= 0) $tgtOut = 1;
            $relOut = (float) ($row['realisasi_output'] ?? 0);
            $hitungOut = round(($relOut / $tgtOut) * 100, 2);

            $tgtMutu = (float) ($row['target_mutu'] ?? 100);
            if ($tgtMutu <= 0) $tgtMutu = 100;
            $relMutu = (float) ($row['realisasi_mutu'] ?? 0);
            $hitungMutu = round(($relMutu / $tgtMutu) * 100, 2);

            $tgtWkt = (float) ($row['target_waktu'] ?? 1);
            if ($tgtWkt <= 0) $tgtWkt = 1;
            $relWkt = (float) ($row['realisasi_waktu'] ?? 1);
            $hitungWkt = round((((1.76 * $tgtWkt) - $relWkt) / $tgtWkt) * 100, 2);

            $penghitungan = round($hitungOut + $hitungMutu + $hitungWkt, 2);
            $capaian = round($penghitungan / 3, 2);

            $row['hitung_output'] = $hitungOut;
            $row['hitung_mutu']   = $hitungMutu;
            $row['hitung_waktu']  = $hitungWkt;
            $row['penghitungan']  = $penghitungan;
            $row['capaian_skp']   = $capaian;

            if ($capaian >= 91) {
                $row['kategori_capaian'] = 'Sangat Baik';
                $row['badge_class'] = 'bg-success';
            } elseif ($capaian >= 76) {
                $row['kategori_capaian'] = 'Baik';
                $row['badge_class'] = 'bg-primary';
            } elseif ($capaian >= 61) {
                $row['kategori_capaian'] = 'Cukup';
                $row['badge_class'] = 'bg-info';
            } elseif ($capaian >= 51) {
                $row['kategori_capaian'] = 'Kurang';
                $row['badge_class'] = 'bg-warning';
            } else {
                $row['kategori_capaian'] = 'Buruk';
                $row['badge_class'] = 'bg-danger';
            }

            $sumCapaian += $capaian;
            $countEvaluated++;
        }

        $avgCapaian = ($countEvaluated > 0) ? round($sumCapaian / $countEvaluated, 2) : 0;

        return [
            'total'   => $total,
            'summary' => [
                'total_realisasi' => $total,
                'avg_capaian'     => $avgCapaian,
                'evaluated_count' => $countEvaluated
            ],
            'data'    => $data
        ];
    }

    public function saveRealisasiSkp(array $data)
    {
        $id = $data['id_realisasiskp'] ?? null;
        $payload = [
            'id_target'     => !empty($data['id_target']) ? (int) $data['id_target'] : null,
            'ak'            => !empty($data['ak']) ? (float) $data['ak'] : null,
            'output'        => !empty($data['output']) ? (int) $data['output'] : 0,
            'output_satuan' => $data['output_satuan'] ?? null,
            'mutu'          => !empty($data['mutu']) ? (string) $data['mutu'] : '100',
            'waktu'         => !empty($data['waktu']) ? (int) $data['waktu'] : 1,
            'waktu_satuan'  => $data['waktu_satuan'] ?? 'Bulan',
            'biaya'         => !empty($data['biaya']) ? (float) $data['biaya'] : 0,
            'id_pegawai'    => !empty($data['id_pegawai']) ? (string) $data['id_pegawai'] : '1',
            'status'        => $data['status'] ?? '1'
        ];

        if (!empty($id)) {
            $this->db->table('ekinerja_realisasi_skp')->where('id_realisasiskp', $id)->update($payload);
            return (int) $id;
        } else {
            $payload['tgl_entri'] = date('Y-m-d H:i:s');
            $this->db->table('ekinerja_realisasi_skp')->insert($payload);
            return $this->db->insertID();
        }
    }

    /**
     * 5. PENILAIAN PERILAKU & PRESTASI KERJA
     */
    public function getPenilaian(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $mode = $filters['mode'] ?? 'rekap';

        if ($mode === 'detail') {
            $builder = $this->db->table('penilaian_ekinerja pe')
                ->select('pe.id_penilaian, pe.id_standar_penilaian, pe.nilai, pe.skor,
                          pe.id_pegawai, pe.periode,
                          COALESCE(p.NAMA, pim.NAMA, pg.nama, pe.id_pegawai) as nama_pegawai,
                          COALESCE(p.NIP, pim.NIP, pg.nip, pe.id_pegawai) as NIP,
                          COALESCE(bg.BAGIAN, pg.bagian, "-") as nama_bagian,
                          sp.standar_penilaian, sp.bobot')
                ->join('master_standar_penilaian sp', 'pe.id_standar_penilaian = sp.id_standar_penilaian', 'left')
                ->join('pegawai p', 'CONVERT(pe.id_pegawai USING latin1) = p.NIP OR CONVERT(pe.id_pegawai USING latin1) = p.ID_PEGAWAI OR REPLACE(CONVERT(pe.id_pegawai USING latin1), ".", "") = p.NIP', 'left')
                ->join('pegawai_id_mesin pim', 'CONVERT(pe.id_pegawai USING latin1) = pim.ID_PEGAWAI OR CONVERT(pe.id_pegawai USING latin1) = pim.NIP OR REPLACE(CONVERT(pe.id_pegawai USING latin1), ".", "") = pim.NIP', 'left')
                ->join('pegawai_grand pg', 'CONVERT(pe.id_pegawai USING latin1) = pg.nip OR CONVERT(pe.id_pegawai USING latin1) = pg.id_pegawai', 'left')
                ->join('detail_penempatan_pegawai dpp', 'dpp.ID_PEGAWAI = p.ID_PEGAWAI', 'left')
                ->join('bagian bg', 'dpp.KD_BAGIAN = bg.KD_BAGIAN', 'left');

            if (!empty($filters['periode'])) {
                $builder->where('pe.periode', $filters['periode']);
            }
            if (!empty($filters['id_pegawai'])) {
                $builder->where('pe.id_pegawai', $filters['id_pegawai']);
            }

            $countBuilder = clone $builder;
            $total = $countBuilder->countAllResults(false);
            $data = $builder->orderBy('pe.id_penilaian', 'DESC')->limit($limit, $offset)->get()->getResultArray();

            return ['total' => $total, 'data' => $data];
        }

        // Mode: Rekap (Aggregated per employee per period)
        $builder = $this->db->table('penilaian_ekinerja pe')
            ->select('pe.id_pegawai, pe.periode,
                      ROUND(AVG(pe.nilai), 2) as rata_nilai,
                      ROUND(SUM(pe.skor), 2) as total_skor,
                      COUNT(pe.id_standar_penilaian) as jml_kriteria,
                      COALESCE(p.NAMA, pim.NAMA, pg.nama, pe.id_pegawai) as nama_pegawai,
                      COALESCE(p.NIP, pim.NIP, pg.nip, pe.id_pegawai) as NIP,
                      COALESCE(bg.BAGIAN, pg.bagian, "-") as nama_bagian,
                      COALESCE(pg.jabatan, "-") as nama_jabatan')
            ->join('pegawai p', 'CONVERT(pe.id_pegawai USING latin1) = p.NIP OR CONVERT(pe.id_pegawai USING latin1) = p.ID_PEGAWAI OR REPLACE(CONVERT(pe.id_pegawai USING latin1), ".", "") = p.NIP', 'left')
            ->join('pegawai_id_mesin pim', 'CONVERT(pe.id_pegawai USING latin1) = pim.ID_PEGAWAI OR CONVERT(pe.id_pegawai USING latin1) = pim.NIP OR REPLACE(CONVERT(pe.id_pegawai USING latin1), ".", "") = pim.NIP', 'left')
            ->join('pegawai_grand pg', 'CONVERT(pe.id_pegawai USING latin1) = pg.nip OR CONVERT(pe.id_pegawai USING latin1) = pg.id_pegawai', 'left')
            ->join('detail_penempatan_pegawai dpp', 'dpp.ID_PEGAWAI = p.ID_PEGAWAI', 'left')
            ->join('bagian bg', 'dpp.KD_BAGIAN = bg.KD_BAGIAN', 'left')
            ->where('pe.periode !=', '');

        if (!empty($filters['periode'])) {
            $builder->where('pe.periode', $filters['periode']);
        }

        if (!empty($filters['kd_bagian'])) {
            $builder->where('dpp.KD_BAGIAN', $filters['kd_bagian']);
        }

        if (!empty($filters['id_pegawai'])) {
            $builder->where('pe.id_pegawai', (string) $filters['id_pegawai']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('p.NAMA', $s)
                ->orLike('pim.NAMA', $s)
                ->orLike('pg.nama', $s)
                ->orLike('pe.id_pegawai', $s)
                ->orLike('pe.periode', $s)
                ->groupEnd();
        }

        $builder->groupBy('pe.id_pegawai, pe.periode');

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $data = $builder->orderBy('pe.periode', 'DESC')
            ->orderBy('total_skor', 'DESC')
            ->limit($limit, $offset)
            ->get()->getResultArray();

        // Map evaluation score label
        foreach ($data as &$row) {
            $score = (float) ($row['total_skor'] ?? 0);
            if ($score >= 91) {
                $row['predikat'] = 'Sangat Baik';
                $row['predikat_class'] = 'bg-success';
            } elseif ($score >= 76) {
                $row['predikat'] = 'Baik';
                $row['predikat_class'] = 'bg-primary';
            } elseif ($score >= 61) {
                $row['predikat'] = 'Cukup';
                $row['predikat_class'] = 'bg-info';
            } elseif ($score >= 51) {
                $row['predikat'] = 'Kurang';
                $row['predikat_class'] = 'bg-warning';
            } else {
                $row['predikat'] = 'Buruk';
                $row['predikat_class'] = 'bg-danger';
            }
        }

        return [
            'total' => $total,
            'data'  => $data
        ];
    }

    public function getDetailPerilaku(string $idPegawai, string $tahun)
    {
        return $this->db->table('ekinerja_detail_perilaku dp')
            ->select('dp.id_detperilaku_peg, dp.id_perilaku, dp.id_pegawai, dp.tahun,
                      dp.nilai, dp.status, mp.perilaku as nama_perilaku')
            ->join('ekinerja_master_perilaku mp', 'dp.id_perilaku = mp.id_perilaku', 'left')
            ->where('dp.id_pegawai', $idPegawai)
            ->where('dp.tahun', $tahun)
            ->orderBy('dp.id_perilaku', 'ASC')
            ->get()->getResultArray();
    }

    public function saveDetailPerilaku(array $data)
    {
        $idPegawai = $data['id_pegawai'];
        $tahun = $data['tahun'] ?? date('Y');
        $scores = $data['perilaku_scores'] ?? [];

        foreach ($scores as $item) {
            $idPerilaku = (int) ($item['id_perilaku'] ?? 0);
            $nilai = (float) ($item['nilai'] ?? 0);

            $existing = $this->db->table('ekinerja_detail_perilaku')
                ->where('id_pegawai', $idPegawai)
                ->where('tahun', $tahun)
                ->where('id_perilaku', $idPerilaku)
                ->get()->getRowArray();

            if ($existing) {
                $this->db->table('ekinerja_detail_perilaku')
                    ->where('id_detperilaku_peg', $existing['id_detperilaku_peg'])
                    ->update([
                        'nilai'     => $nilai,
                        'tgl_entri' => date('Y-m-d H:i:s')
                    ]);
            } else {
                $this->db->table('ekinerja_detail_perilaku')->insert([
                    'id_perilaku' => $idPerilaku,
                    'id_pegawai'  => $idPegawai,
                    'tahun'       => $tahun,
                    'tgl_entri'   => date('Y-m-d H:i:s'),
                    'nilai'       => $nilai
                ]);
            }
        }

        return true;
    }

    /**
     * 6. KINERJA OVERVIEW DASHBOARD SUMMARY
     */
    public function getKinerjaSummary()
    {
        $totalProgram = $this->db->table('ekinerja_program_kerja')->where('IS_DELL', 0)->countAllResults();
        $totalTarget  = $this->db->table('ekinerja_target_skp')->countAllResults();
        $targetApproved = $this->db->table('ekinerja_target_skp')->where('status', '1')->countAllResults();
        $targetPending  = $this->db->table('ekinerja_target_skp')->groupStart()->where('status', '0')->orWhere('status IS NULL')->groupEnd()->countAllResults();
        
        $totalAktifitas = $this->db->table('ekinerja_aktifitas')->countAllResults();
        $totalRealisasi = $this->db->table('ekinerja_realisasi_skp')->countAllResults();
        $totalPenilaian = $this->db->table('penilaian_ekinerja')->countAllResults();

        return [
            'total_program'    => $totalProgram,
            'total_target'     => $totalTarget,
            'target_approved'  => $targetApproved,
            'target_pending'   => $targetPending,
            'total_aktifitas'  => $totalAktifitas,
            'total_realisasi'  => $totalRealisasi,
            'total_penilaian'  => $totalPenilaian
        ];
    }
}
