<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MasterTables extends BaseConfig
{
    public array $tables = [
        // 1. Agama
        'agama' => [
            'table'       => 'agama',
            'primary_key' => 'KD_AGAMA',
            'display'     => 'AGAMA',
            'fields'      => ['AGAMA'],
            'title'       => 'Agama',
        ],

        // 2. Golongan Darah
        'golongan_darah' => [
            'table'       => 'golongan_darah',
            'primary_key' => 'KD_GOLDA',
            'display'     => 'GOLONGAN_DARAH',
            'fields'      => ['GOLONGAN_DARAH'],
            'title'       => 'Golongan Darah',
        ],
        'golongandarah' => [
            'table'       => 'golongan_darah',
            'primary_key' => 'KD_GOLDA',
            'display'     => 'GOLONGAN_DARAH',
            'fields'      => ['GOLONGAN_DARAH'],
            'title'       => 'Golongan Darah',
        ],

        // 3. Status Pegawai
        'status_pegawai' => [
            'table'       => 'status_pegawai',
            'primary_key' => 'KD_STATUS_PEGAWAI',
            'display'     => 'STATUS_PEGAWAI',
            'fields'      => ['STATUS_PEGAWAI'],
            'title'       => 'Status Pegawai',
        ],
        'statuspegawai' => [
            'table'       => 'status_pegawai',
            'primary_key' => 'KD_STATUS_PEGAWAI',
            'display'     => 'STATUS_PEGAWAI',
            'fields'      => ['STATUS_PEGAWAI'],
            'title'       => 'Status Pegawai',
        ],

        // 4. Jenis Pegawai
        'jenis_pegawai' => [
            'table'       => 'jenis_pegawai',
            'primary_key' => 'KD_JENIS_PEGAWAI',
            'display'     => 'JENIS_PEGAWAI',
            'fields'      => ['JENIS_PEGAWAI'],
            'title'       => 'Jenis Pegawai',
        ],
        'jenispegawai' => [
            'table'       => 'jenis_pegawai',
            'primary_key' => 'KD_JENIS_PEGAWAI',
            'display'     => 'JENIS_PEGAWAI',
            'fields'      => ['JENIS_PEGAWAI'],
            'title'       => 'Jenis Pegawai',
        ],

        // 5. Pekerjaan
        'pekerjaan' => [
            'table'       => 'pekerjaan',
            'primary_key' => 'KD_PEKERJAAN',
            'display'     => 'PEKERJAAN',
            'fields'      => ['PEKERJAAN'],
            'title'       => 'Pekerjaan',
        ],

        // 6. Hubungan (Keluarga)
        'hubungan' => [
            'table'       => 'hubungan',
            'primary_key' => 'KD_HUBUNGAN',
            'display'     => 'HUBUNGAN',
            'fields'      => ['HUBUNGAN'],
            'title'       => 'Hubungan Keluarga',
        ],

        // 7. Pangkat
        'pangkat' => [
            'table'       => 'pangkat',
            'primary_key' => 'KD_PANGKAT',
            'display'     => 'PANGKAT',
            'fields'      => ['PANGKAT'],
            'title'       => 'Pangkat',
        ],

        // 8. Golongan
        'golongan' => [
            'table'       => 'golongan',
            'primary_key' => 'KD_GOLONGAN',
            'display'     => 'GOLONGAN',
            'fields'      => ['GOLONGAN'],
            'title'       => 'Golongan',
        ],

        // 9. Ekselon
        'ekselon' => [
            'table'       => 'ekselon',
            'primary_key' => 'KD_EKSELON',
            'display'     => 'EKSELON',
            'fields'      => ['EKSELON'],
            'title'       => 'Ekselon',
        ],

        // 10. Status Keahlian
        'status_keahlian' => [
            'table'       => 'status_keahlian',
            'primary_key' => 'KD_STATUS_KEAHLIAN',
            'display'     => 'STATUS_KEAHLIAN',
            'fields'      => ['STATUS_KEAHLIAN'],
            'title'       => 'Status Keahlian',
        ],
        'statuskeahlian' => [
            'table'       => 'status_keahlian',
            'primary_key' => 'KD_STATUS_KEAHLIAN',
            'display'     => 'STATUS_KEAHLIAN',
            'fields'      => ['STATUS_KEAHLIAN'],
            'title'       => 'Status Keahlian',
        ],

        // 11. Status Lulus
        'status_lulus' => [
            'table'       => 'status_lulus',
            'primary_key' => 'KD_STATUS_LULUS',
            'display'     => 'STATUS_LULUS',
            'fields'      => ['STATUS_LULUS'],
            'title'       => 'Status Kelulusan',
        ],
        'statuslulus' => [
            'table'       => 'status_lulus',
            'primary_key' => 'KD_STATUS_LULUS',
            'display'     => 'STATUS_LULUS',
            'fields'      => ['STATUS_LULUS'],
            'title'       => 'Status Kelulusan',
        ],

        // 12. Jenis Kontrak
        'jenis_kontrak' => [
            'table'       => 'jenis_kontrak',
            'primary_key' => 'KD_JENIS_KONTRAK',
            'display'     => 'JENIS_KONTRAK',
            'fields'      => ['JENIS_KONTRAK'],
            'title'       => 'Jenis Kontrak',
        ],
        'jeniskontrak' => [
            'table'       => 'jenis_kontrak',
            'primary_key' => 'KD_JENIS_KONTRAK',
            'display'     => 'JENIS_KONTRAK',
            'fields'      => ['JENIS_KONTRAK'],
            'title'       => 'Jenis Kontrak',
        ],

        // 13. Jenis Pendidikan
        'jenis_pendidikan' => [
            'table'       => 'jenis_pendidikan',
            'primary_key' => 'KD_JENIS_PENDIDIKAN',
            'display'     => 'JENIS_PENDIDIKAN',
            'fields'      => ['JENIS_PENDIDIKAN'],
            'title'       => 'Jenis Pendidikan',
        ],
        'jenispendidikan' => [
            'table'       => 'jenis_pendidikan',
            'primary_key' => 'KD_JENIS_PENDIDIKAN',
            'display'     => 'JENIS_PENDIDIKAN',
            'fields'      => ['JENIS_PENDIDIKAN'],
            'title'       => 'Jenis Pendidikan',
        ],

        // 14. Jenjang Pendidikan
        'jenjang_pendidikan' => [
            'table'       => 'jenjang_pendidikan',
            'primary_key' => 'KD_JENJANG',
            'display'     => 'JENJANG_PENDIDIKAN',
            'fields'      => ['JENJANG_PENDIDIKAN'],
            'title'       => 'Jenjang Pendidikan',
        ],
        'jenjangpendidikan' => [
            'table'       => 'jenjang_pendidikan',
            'primary_key' => 'KD_JENJANG',
            'display'     => 'JENJANG_PENDIDIKAN',
            'fields'      => ['JENJANG_PENDIDIKAN'],
            'title'       => 'Jenjang Pendidikan',
        ],

        // 15. Jenis Pengalaman
        'jenis_pengalaman' => [
            'table'       => 'jenis_pengalaman',
            'primary_key' => 'KD_JENIS_PENGALAMAN',
            'display'     => 'JENIS_PENGALAMAN',
            'fields'      => ['JENIS_PENGALAMAN'],
            'title'       => 'Jenis Pengalaman',
        ],
        'jenispengalaman' => [
            'table'       => 'jenis_pengalaman',
            'primary_key' => 'KD_JENIS_PENGALAMAN',
            'display'     => 'JENIS_PENGALAMAN',
            'fields'      => ['JENIS_PENGALAMAN'],
            'title'       => 'Jenis Pengalaman',
        ],

        // 16. Jenis Sanksi
        'jenis_sanksi' => [
            'table'       => 'jenis_sanksi',
            'primary_key' => 'KD_JENIS_SANKSI',
            'display'     => 'JENIS_SANKSI',
            'fields'      => ['JENIS_SANKSI'],
            'title'       => 'Jenis Sanksi',
        ],
        'jenissanksi' => [
            'table'       => 'jenis_sanksi',
            'primary_key' => 'KD_JENIS_SANKSI',
            'display'     => 'JENIS_SANKSI',
            'fields'      => ['JENIS_SANKSI'],
            'title'       => 'Jenis Sanksi',
        ],

        // 17. Lokasi
        'lokasi' => [
            'table'       => 'lokasi',
            'primary_key' => 'KD_LOKASI',
            'display'     => 'LOKASI',
            'fields'      => ['LOKASI'],
            'title'       => 'Lokasi',
        ],

        // 18. Provinsi
        'provinsi' => [
            'table'       => 'provinsi',
            'primary_key' => 'KD_PROVINSI',
            'display'     => 'PROVINSI',
            'fields'      => ['PROVINSI'],
            'title'       => 'Provinsi',
        ],

        // 19. Jenis Tahap
        'jenis_tahap' => [
            'table'       => 'jenis_tahap',
            'primary_key' => 'KD_JENIS_TAHAP',
            'display'     => 'JENIS_TAHAP',
            'fields'      => ['JENIS_TAHAP'],
            'title'       => 'Jenis Tahap',
        ],
        'jenistahap' => [
            'table'       => 'jenis_tahap',
            'primary_key' => 'KD_JENIS_TAHAP',
            'display'     => 'JENIS_TAHAP',
            'fields'      => ['JENIS_TAHAP'],
            'title'       => 'Jenis Tahap',
        ],

        // 20. Jenis Penghargaan
        'jenis_penghargaan' => [
            'table'       => 'jenis_penghargaan',
            'primary_key' => 'KD_JENIS_PENGHARGAAN',
            'display'     => 'JENIS_PENGHARGAAN',
            'fields'      => ['JENIS_PENGHARGAAN'],
            'title'       => 'Jenis Penghargaan',
        ],
        'jenispenghargaan' => [
            'table'       => 'jenis_penghargaan',
            'primary_key' => 'KD_JENIS_PENGHARGAAN',
            'display'     => 'JENIS_PENGHARGAAN',
            'fields'      => ['JENIS_PENGHARGAAN'],
            'title'       => 'Jenis Penghargaan',
        ],

        // 21. Jenis Qualifikasi
        'jenis_qualifikasi' => [
            'table'       => 'jenis_qualifikasi',
            'primary_key' => 'KD_JENIS_QUALIFIKASI',
            'display'     => 'JENIS_QUALIFIKASI',
            'fields'      => ['JENIS_QUALIFIKASI'],
            'title'       => 'Jenis Kualifikasi',
        ],
        'jenisqualifikasi' => [
            'table'       => 'jenis_qualifikasi',
            'primary_key' => 'KD_JENIS_QUALIFIKASI',
            'display'     => 'JENIS_QUALIFIKASI',
            'fields'      => ['JENIS_QUALIFIKASI'],
            'title'       => 'Jenis Kualifikasi',
        ],

        // 22. Status Asuransi
        'status_asuransi' => [
            'table'       => 'status_asuransi',
            'primary_key' => 'KD_STATUS_ASURANSI',
            'display'     => 'STATUS_ASURANSI',
            'fields'      => ['STATUS_ASURANSI'],
            'title'       => 'Status Asuransi',
        ],
        'statusasuransi' => [
            'table'       => 'status_asuransi',
            'primary_key' => 'KD_STATUS_ASURANSI',
            'display'     => 'STATUS_ASURANSI',
            'fields'      => ['STATUS_ASURANSI'],
            'title'       => 'Status Asuransi',
        ],

        // 23. Nama Asuransi
        'nama_asuransi' => [
            'table'       => 'nama_asuransi',
            'primary_key' => 'KD_ASURANSI',
            'display'     => 'NAMA_ASURANSI',
            'fields'      => ['NAMA_ASURANSI'],
            'title'       => 'Nama Asuransi',
        ],
        'namaasuransi' => [
            'table'       => 'nama_asuransi',
            'primary_key' => 'KD_ASURANSI',
            'display'     => 'NAMA_ASURANSI',
            'fields'      => ['NAMA_ASURANSI'],
            'title'       => 'Nama Asuransi',
        ],

        // 24. Jenis Syarat
        'jenis_syarat' => [
            'table'       => 'jenis_syarat',
            'primary_key' => 'KD_JENIS_SYARAT',
            'display'     => 'JENIS_SYARAT',
            'fields'      => ['JENIS_SYARAT'],
            'title'       => 'Jenis Persyaratan',
        ],
        'jenissyarat' => [
            'table'       => 'jenis_syarat',
            'primary_key' => 'KD_JENIS_SYARAT',
            'display'     => 'JENIS_SYARAT',
            'fields'      => ['JENIS_SYARAT'],
            'title'       => 'Jenis Persyaratan',
        ],

        // 25. Jenis Catatan
        'jenis_catatan' => [
            'table'       => 'jenis_catatan',
            'primary_key' => 'KD_JENIS_CATATAN',
            'display'     => 'JENIS_CATATAN',
            'fields'      => ['JENIS_CATATAN'],
            'title'       => 'Jenis Catatan Pegawai',
        ],
        'jeniscatatan' => [
            'table'       => 'jenis_catatan',
            'primary_key' => 'KD_JENIS_CATATAN',
            'display'     => 'JENIS_CATATAN',
            'fields'      => ['JENIS_CATATAN'],
            'title'       => 'Jenis Catatan Pegawai',
        ],

        // 26. Jenis PHK
        'jenis_phk' => [
            'table'       => 'jenis_phk',
            'primary_key' => 'KD_JENIS_PHK',
            'display'     => 'JENIS_PHK',
            'fields'      => ['JENIS_PHK'],
            'title'       => 'Jenis PHK',
        ],
        'jenisphk' => [
            'table'       => 'jenis_phk',
            'primary_key' => 'KD_JENIS_PHK',
            'display'     => 'JENIS_PHK',
            'fields'      => ['JENIS_PHK'],
            'title'       => 'Jenis PHK',
        ],

        // 27. Identitas
        'identitas' => [
            'table'       => 'identitas',
            'primary_key' => 'KD_IDENTITAS',
            'display'     => 'IDENTITAS',
            'fields'      => ['IDENTITAS'],
            'title'       => 'Jenis Identitas',
        ],
        'jenis_identitas' => [
            'table'       => 'identitas',
            'primary_key' => 'KD_IDENTITAS',
            'display'     => 'IDENTITAS',
            'fields'      => ['IDENTITAS'],
            'title'       => 'Jenis Identitas',
        ],
        'jenisidentitas' => [
            'table'       => 'identitas',
            'primary_key' => 'KD_IDENTITAS',
            'display'     => 'IDENTITAS',
            'fields'      => ['IDENTITAS'],
            'title'       => 'Jenis Identitas',
        ],

        // 28. Jenis SIP
        'jenis_sip' => [
            'table'       => 'jenis_sip',
            'primary_key' => 'KD_JENIS_SIP',
            'display'     => 'JENIS_SIP',
            'fields'      => ['JENIS_SIP'],
            'title'       => 'Jenis Surat Izin Praktik (SIP)',
        ],
    ];
}
