<?php

namespace Config;

$routes = Services::routes(true);

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Common');
$routes->setDefaultMethod('index');
$routes->set404Override(function () {
    helper('response');
    return sendResponse(404, null, 'Endpoint not exist');
});
$routes->get('/', 'Common');
$routes->get('/application/config', 'Common::getConfig');

//login
$routes->post('/login/auth', 'Login::auth');
$routes->get('/login/privileges', 'Login::privileges');
$routes->get('/user/defaultModules', 'Login::defaultModules');
$routes->get('/user/units', 'User::getUserUnit');
$routes->post('/user/unit/save', 'User::saveDefaultUnit');
$routes->get('/tandatangan', 'Paramedis::loadSignature');
$routes->post('/tandatangan/save', 'Paramedis::saveSignature');
$routes->post('/tandatangan/delete', 'Paramedis::deleteSignature');

//dataPasien
$routes->get('/master/loadUsers', 'User::loadUsers');
$routes->get('/search/pasien', 'RawatDarurat\DataPasien::search');
$routes->get('/pasien/new/no_medicalrecord', 'RawatDarurat\DataPasien::createNew');
$routes->get('/pasien/usia', 'Common::getUsia');
$routes->get('/pasien/loadPenjamin', 'RawatDarurat\DataPasien::loadPenjamin');
$routes->get('/pasien/getKelPasien', 'User::getKelPasien');
$routes->get('/pasien/getJKelPasien', 'User::getJKelPasien');
$routes->get('/pasien/getPekerjaan', 'User::getPekerjaan');
$routes->get('/pasien/getPendidikan', 'User::getPendidikan');
$routes->get('/pasien/getDokumen', 'User::getDokumen');
$routes->get('/pasien/getTglLahir', 'Common::getTglLahir');

// Dashboard HRIS
$routes->get('/dashboard/all', 'Dashboard::all');
$routes->get('/dashboard/summary', 'Dashboard::summary');
$routes->get('/dashboard/bagian', 'Dashboard::bagian');
$routes->get('/dashboard/pertumbuhan', 'Dashboard::pertumbuhan');
$routes->get('/dashboard/status_pegawai', 'Dashboard::statusPegawai');
$routes->get('/dashboard/pendidikan', 'Dashboard::pendidikan');
$routes->get('/dashboard/kehadiran', 'Dashboard::kehadiran');
$routes->get('/dashboard/detail/ultah', 'Dashboard::detailUltah');
$routes->get('/dashboard/detail/pensiun', 'Dashboard::detailPensiun');
$routes->get('/dashboard/detail/str_sip', 'Dashboard::detailStrSip');

//Master Tables
$routes->get('/master/(:segment)/(:num)', 'Master::show/$1/$2');
$routes->get('/master/(:segment)', 'Master::index/$1');
$routes->post('/master/(:segment)/save', 'Master::save/$1');
$routes->post('/master/(:segment)/delete', 'Master::delete/$1');

//vital sign - TTV
$routes->post('/vital_sign/save', 'VitalSign::save');
$routes->get('/vital_sign/load', 'VitalSign::load');

//triage
$routes->get('/triage/load', 'RawatDarurat\Triage::load');
$routes->post('/triage/save', 'RawatDarurat\Triage::save');
$routes->get('/triage/loadHistory', 'RawatDarurat\Triage::loadHistory');
$routes->post('/triage/cetak', 'RawatDarurat\Triage::print');

//antrian igd
$routes->get('/antrian/rawat_darurat/belum_terlayani', 'RawatDarurat\Antrian::index');
$routes->get('/antrian/rawat_darurat/terlayani', 'RawatDarurat\Antrian::index');
$routes->get('/antrian/rawat_darurat/terlayani/menunggu/ruang', 'RawatDarurat\Antrian::index');
$routes->get('/antrian/rawat_darurat/riwayat', 'RawatDarurat\Antrian::index');
$routes->post('/pendaftaran', 'Pendaftaran');
$routes->post('/savePemeriksaanAwal/save', 'RawatDarurat\Antrian::savePemeriksaanAwal');

//load-paramedis
$routes->get('/paramedis/load', 'Paramedis::getParamedis');
$routes->get('/paramedis/loadPelaksana', 'Paramedis::getParamedisPelaksana');

//load-perawat
$routes->get('/perawat/load', 'Perawat::loadPegawai');
$routes->get('/pegawai/list', 'Pegawai::getPegawaiUnit');
$routes->get('/pegawai/lookup', 'Pegawai::getLookup');
$routes->get('/pegawai/ref_data', 'Pegawai::getRefData');
$routes->get('/pegawai/provinsi', 'Pegawai::getProvinsi');
$routes->get('/pegawai/kabupaten', 'Pegawai::getKabupaten');
$routes->get('/pegawai/kabupaten/(:segment)', 'Pegawai::getKabupaten/$1');
$routes->get('/pegawai/kecamatan', 'Pegawai::getKecamatan');
$routes->get('/pegawai/kecamatan/(:segment)', 'Pegawai::getKecamatan/$1');
$routes->get('/pegawai/kelurahan', 'Pegawai::getKelurahan');
$routes->get('/pegawai/kelurahan/(:segment)', 'Pegawai::getKelurahan/$1');
$routes->get('/pegawai/agama', 'Pegawai::getAgama');
$routes->post('/pegawai/save', 'Pegawai::postPegawai');
$routes->post('/pegawai/update', 'Pegawai::updatePegawai');
$routes->post('/pegawai/update/(:segment)', 'Pegawai::updatePegawai/$1');
$routes->post('/pegawai/delete', 'Pegawai::deletePegawai');
$routes->post('/pegawai/delete/(:segment)', 'Pegawai::deletePegawai/$1');

// Pegawai Sub-modules
$routes->get('/pegawai/penempatan', 'Pegawai::penempatan');
$routes->post('/pegawai/penempatan/save', 'Pegawai::save_penempatan');
$routes->post('/pegawai/penempatan/delete/(:segment)', 'Pegawai::delete_penempatan/$1');

$routes->get('/pegawai/str', 'Pegawai::str');
$routes->post('/pegawai/str/save', 'Pegawai::save_str');
$routes->post('/pegawai/str/delete/(:segment)', 'Pegawai::delete_str/$1');

$routes->get('/pegawai/sip', 'Pegawai::sip');
$routes->post('/pegawai/sip/save', 'Pegawai::save_sip');
$routes->post('/pegawai/sip/delete/(:segment)', 'Pegawai::delete_sip/$1');

$routes->get('/pegawai/pendidikan', 'Pegawai::pendidikan');
$routes->post('/pegawai/pendidikan/save', 'Pegawai::save_pendidikan');
$routes->post('/pegawai/pendidikan/delete/(:segment)', 'Pegawai::delete_pendidikan/$1');

$routes->get('/pegawai/pelatihan', 'Pegawai::pelatihan');
$routes->post('/pegawai/pelatihan/save', 'Pegawai::save_pelatihan');
$routes->post('/pegawai/pelatihan/delete/(:segment)', 'Pegawai::delete_pelatihan/$1');

$routes->get('/pegawai/mutasi', 'Pegawai::mutasi');
$routes->post('/pegawai/mutasi/save', 'Pegawai::save_mutasi');
$routes->match(['post', 'delete'], '/pegawai/mutasi/delete', 'Pegawai::delete_mutasi');
$routes->match(['post', 'delete'], '/pegawai/mutasi/delete/(:segment)', 'Pegawai::delete_mutasi/$1');

$routes->get('/pegawai/promosi', 'Pegawai::promosi');
$routes->post('/pegawai/promosi/save', 'Pegawai::save_promosi');
$routes->match(['post', 'delete'], '/pegawai/promosi/delete', 'Pegawai::delete_promosi');
$routes->match(['post', 'delete'], '/pegawai/promosi/delete/(:segment)', 'Pegawai::delete_promosi/$1');

$routes->get('/pegawai/kontrak', 'Pegawai::kontrak');
$routes->post('/pegawai/kontrak/save', 'Pegawai::save_kontrak');
$routes->match(['post', 'delete'], '/pegawai/kontrak/delete', 'Pegawai::delete_kontrak');
$routes->match(['post', 'delete'], '/pegawai/kontrak/delete/(:segment)', 'Pegawai::delete_kontrak/$1');

$routes->get('/pegawai/phk', 'Pegawai::phk');
$routes->post('/pegawai/phk/save', 'Pegawai::save_phk');
$routes->match(['post', 'delete'], '/pegawai/phk/delete', 'Pegawai::delete_phk');
$routes->match(['post', 'delete'], '/pegawai/phk/delete/(:segment)', 'Pegawai::delete_phk/$1');

$routes->get('/pegawai/penghargaan', 'Pegawai::penghargaan');
$routes->post('/pegawai/penghargaan/save', 'Pegawai::save_penghargaan');
$routes->match(['post', 'delete'], '/pegawai/penghargaan/delete', 'Pegawai::delete_penghargaan');
$routes->match(['post', 'delete'], '/pegawai/penghargaan/delete/(:segment)', 'Pegawai::delete_penghargaan/$1');

$routes->get('/pegawai/sanksi', 'Pegawai::sanksi');
$routes->post('/pegawai/sanksi/save', 'Pegawai::save_sanksi');
$routes->match(['post', 'delete'], '/pegawai/sanksi/delete', 'Pegawai::delete_sanksi');
$routes->match(['post', 'delete'], '/pegawai/sanksi/delete/(:segment)', 'Pegawai::delete_sanksi/$1');

$routes->get('/pegawai/demosi', 'Pegawai::demosi');
$routes->post('/pegawai/demosi/save', 'Pegawai::save_demosi');
$routes->match(['post', 'delete'], '/pegawai/demosi/delete', 'Pegawai::delete_demosi');
$routes->match(['post', 'delete'], '/pegawai/demosi/delete/(:segment)', 'Pegawai::delete_demosi/$1');

$routes->get('/pegawai/catatan', 'Pegawai::catatan');
$routes->post('/pegawai/catatan/save', 'Pegawai::save_catatan');
$routes->match(['post', 'delete'], '/pegawai/catatan/delete', 'Pegawai::delete_catatan');
$routes->match(['post', 'delete'], '/pegawai/catatan/delete/(:segment)', 'Pegawai::delete_catatan/$1');

$routes->get('/pegawai/cuti', 'Pegawai::cuti');
$routes->post('/pegawai/cuti/save', 'Pegawai::save_cuti');
$routes->post('/pegawai/cuti/delete/(:segment)', 'Pegawai::delete_cuti/$1');

// Modul Kepegawaian Modern (cTambahPegawai & cPegawai)
$routes->get('/cTambahPegawai', 'cTambahPegawai::index');
$routes->get('/cTambahPegawai/index', 'cTambahPegawai::index');
$routes->get('/cTambahPegawai/index/(:segment)', 'cTambahPegawai::index/$1');
$routes->get('/cTambahPegawai/cetak_riwayat', 'cTambahPegawai::cetak_riwayat');
$routes->get('/cTambahPegawai/cetak_riwayat/(:segment)', 'cTambahPegawai::cetak_riwayat/$1');
$routes->get('/cTambahPegawai/get_data_pegawai', 'cTambahPegawai::get_data_pegawai');

$routes->get('/cPegawai', 'cPegawai::master');
$routes->get('/cPegawai/index', 'cPegawai::master');
$routes->get('/cPegawai/index/(:segment)', 'cPegawai::master/$1');
$routes->get('/cPegawai/master', 'cPegawai::master');
$routes->get('/cPegawai/get_kabupaten', 'cPegawai::get_kabupaten');
$routes->get('/cPegawai/get_kabupaten/(:segment)', 'cPegawai::get_kabupaten/$1');
$routes->get('/cPegawai/get_kecamatan', 'cPegawai::get_kecamatan');
$routes->get('/cPegawai/get_kecamatan/(:segment)', 'cPegawai::get_kecamatan/$1');
$routes->get('/cPegawai/get_kelurahan', 'cPegawai::get_kelurahan');
$routes->get('/cPegawai/get_kelurahan/(:segment)', 'cPegawai::get_kelurahan/$1');
$routes->get('/cPegawai/get_jurusan', 'cPegawai::get_jurusan');
$routes->get('/cPegawai/get_jurusan/(:segment)', 'cPegawai::get_jurusan/$1');
$routes->get('/cPegawai/get_sub_bagian', 'cPegawai::get_sub_bagian');
$routes->get('/cPegawai/get_sub_bagian/(:segment)', 'cPegawai::get_sub_bagian/$1');
$routes->match(['get', 'post'], '/cPegawai/cek_pass', 'cPegawai::cek_pass');
$routes->match(['get', 'post'], '/cPegawai/update_pass', 'cPegawai::update_pass');
$routes->post('/cPegawai/save_data', 'cPegawai::save_data');
$routes->match(['get', 'post'], '/cPegawai/simpan_data_pegawai', 'cPegawai::save_data');
$routes->match(['get', 'post'], '/cPegawai/import_excel', 'cPegawai::import_excel');
$routes->match(['get', 'post'], '/cPegawai/download_template', 'cPegawai::download_template');
$routes->get('/cPegawai/edit_detail', 'cPegawai::edit_detail');
$routes->get('/cPegawai/edit_detail/(:segment)', 'cPegawai::edit_detail/$1');
$routes->post('/cPegawai/update_data_pegawai', 'cPegawai::update_data_pegawai');
$routes->post('/cPegawai/update_data_pegawai/(:segment)', 'cPegawai::update_data_pegawai/$1');
$routes->post('/cPegawai/update_data_penempatan', 'cPegawai::update_data_penempatan');
$routes->post('/cPegawai/update_data_penempatan/(:segment)', 'cPegawai::update_data_penempatan/$1');
$routes->post('/cPegawai/update_data_pendidikan', 'cPegawai::update_data_pendidikan');
$routes->post('/cPegawai/update_data_pendidikan/(:segment)', 'cPegawai::update_data_pendidikan/$1');
$routes->post('/cPegawai/update_data_pengalaman', 'cPegawai::update_data_pengalaman');
$routes->post('/cPegawai/update_data_pengalaman/(:segment)', 'cPegawai::update_data_pengalaman/$1');
$routes->post('/cPegawai/update_data_keahlian', 'cPegawai::update_data_keahlian');
$routes->post('/cPegawai/update_data_keahlian/(:segment)', 'cPegawai::update_data_keahlian/$1');
$routes->post('/cPegawai/update_data_keluarga', 'cPegawai::update_data_keluarga');
$routes->post('/cPegawai/update_data_keluarga/(:segment)', 'cPegawai::update_data_keluarga/$1');
$routes->post('/cPegawai/update_data_pelatihan', 'cPegawai::update_data_pelatihan');
$routes->post('/cPegawai/update_data_pelatihan/(:segment)', 'cPegawai::update_data_pelatihan/$1');
$routes->post('/cPegawai/update_data_kemampuan', 'cPegawai::update_data_kemampuan');
$routes->post('/cPegawai/update_data_kemampuan/(:segment)', 'cPegawai::update_data_kemampuan/$1');
$routes->post('/cPegawai/update_data_str', 'cPegawai::update_data_str');
$routes->post('/cPegawai/update_data_str/(:segment)', 'cPegawai::update_data_str/$1');
$routes->post('/cPegawai/update_data_sip', 'cPegawai::update_data_sip');
$routes->post('/cPegawai/update_data_sip/(:segment)', 'cPegawai::update_data_sip/$1');
$routes->post('/cPegawai/update_data_mcu', 'cPegawai::update_data_mcu');
$routes->post('/cPegawai/update_data_mcu/(:segment)', 'cPegawai::update_data_mcu/$1');
$routes->post('/cPegawai/update_data_mutasi', 'cPegawai::update_data_mutasi');
$routes->post('/cPegawai/update_data_mutasi/(:segment)', 'cPegawai::update_data_mutasi/$1');
$routes->post('/cPegawai/update_data_promosi', 'cPegawai::update_data_promosi');
$routes->post('/cPegawai/update_data_promosi/(:segment)', 'cPegawai::update_data_promosi/$1');
$routes->post('/cPegawai/update_data_kontrak', 'cPegawai::update_data_kontrak');
$routes->post('/cPegawai/update_data_kontrak/(:segment)', 'cPegawai::update_data_kontrak/$1');
$routes->post('/cPegawai/update_data_cuti', 'cPegawai::update_data_cuti');
$routes->post('/cPegawai/update_data_cuti/(:segment)', 'cPegawai::update_data_cuti/$1');
$routes->post('/cPegawai/update_data_penghargaan', 'cPegawai::update_data_penghargaan');
$routes->post('/cPegawai/update_data_penghargaan/(:segment)', 'cPegawai::update_data_penghargaan/$1');
$routes->post('/cPegawai/update_data_sanksi', 'cPegawai::update_data_sanksi');
$routes->post('/cPegawai/update_data_sanksi/(:segment)', 'cPegawai::update_data_sanksi/$1');
$routes->post('/cPegawai/update_data_phk', 'cPegawai::update_data_phk');
$routes->post('/cPegawai/update_data_phk/(:segment)', 'cPegawai::update_data_phk/$1');
$routes->post('/cPegawai/update_data_rak', 'cPegawai::update_data_rak');
$routes->post('/cPegawai/update_data_rak/(:segment)', 'cPegawai::update_data_rak/$1');
$routes->post('/cPegawai/update_data_buku', 'cPegawai::update_data_buku');
$routes->post('/cPegawai/update_data_buku/(:segment)', 'cPegawai::update_data_buku/$1');
$routes->post('/cPegawai/upload_foto', 'cPegawai::upload_foto');
$routes->post('/cPegawai/upload_foto/(:segment)', 'cPegawai::upload_foto/$1');
$routes->post('/cPegawai/upload_berkas', 'cPegawai::upload_berkas');
$routes->post('/cPegawai/upload_berkas/(:segment)', 'cPegawai::upload_berkas/$1');
$routes->match(['get', 'post'], '/cPegawai/delete_data', 'cPegawai::delete_data');
$routes->match(['get', 'post'], '/cPegawai/delete_data/(:segment)', 'cPegawai::delete_data/$1');
$routes->get('/cPegawai/get_perubahan_status', 'cPegawai::get_perubahan_status');
$routes->get('/cPegawai/get_perubahan_status/(:segment)', 'cPegawai::get_perubahan_status/$1');

// eAbsensi Modernized Endpoints
$routes->get('/absensi/master', 'Absensi::getMaster');
$routes->get('/absensi/rekap-bulanan', 'Absensi::getRekapBulanan');
$routes->get('/absensi/status', 'Absensi::getStatus');
$routes->get('/absensi/status-summary', 'Absensi::getStatusSummary');
$routes->get('/absensi/jadwal-monitoring', 'Absensi::getJadwalMonitoring');
$routes->get('/absensi/tukar-shift', 'Absensi::getTukarShift');
$routes->get('/absensi/lembur', 'Absensi::getLembur');
$routes->get('/absensi/apel', 'Absensi::getApel');
$routes->get('/absensi/log-mesin', 'Absensi::getLogMesin');
$routes->get('/absensi/log-detail', 'Absensi::getLogDetail');

// eKinerja Modernized Endpoints
$routes->get('/kinerja/master', 'Kinerja::getMaster');
$routes->get('/kinerja/summary', 'Kinerja::summary');
$routes->get('/kinerja/program-kerja', 'Kinerja::programKerja');
$routes->post('/kinerja/program-kerja/save', 'Kinerja::saveProgramKerja');
$routes->post('/kinerja/program-kerja/delete', 'Kinerja::deleteProgramKerja');
$routes->get('/kinerja/target-skp', 'Kinerja::targetSkp');
$routes->post('/kinerja/target-skp/save', 'Kinerja::saveTargetSkp');
$routes->post('/kinerja/target-skp/approve', 'Kinerja::approveTargetSkp');
$routes->get('/kinerja/aktifitas', 'Kinerja::aktifitas');
$routes->post('/kinerja/aktifitas/save', 'Kinerja::saveAktifitas');
$routes->post('/kinerja/aktifitas/approve', 'Kinerja::approveAktifitas');
$routes->get('/kinerja/realisasi-skp', 'Kinerja::realisasiSkp');
$routes->post('/kinerja/realisasi-skp/save', 'Kinerja::saveRealisasiSkp');
$routes->get('/kinerja/penilaian', 'Kinerja::penilaian');
$routes->get('/kinerja/penilaian/perilaku', 'Kinerja::detailPerilaku');
$routes->post('/kinerja/penilaian/perilaku/save', 'Kinerja::saveDetailPerilaku');

// ePayroll Modernized Endpoints
$routes->get('/payroll/master', 'Payroll::getMaster');
$routes->get('/payroll/list', 'Payroll::list');
$routes->get('/payroll/detail/(:num)', 'Payroll::detail/$1');
$routes->post('/payroll/generate', 'Payroll::generate');
$routes->post('/payroll/save', 'Payroll::save');
$routes->post('/payroll/approve', 'Payroll::approve');
$routes->post('/payroll/delete', 'Payroll::delete');
$routes->post('/payroll/delete/(:num)', 'Payroll::delete/$1');
$routes->get('/payroll/rekap', 'Payroll::rekap');
$routes->get('/payroll/potongan-absensi', 'Payroll::potonganAbsensi');
$routes->get('/payroll/master-tarif', 'Payroll::masterTarif');
$routes->post('/payroll/master-tarif/gaji', 'Payroll::saveTarifGaji');
$routes->post('/payroll/master-tarif/umk', 'Payroll::saveTarifUmk');
$routes->post('/payroll/master-tarif/potongan', 'Payroll::saveTarifPotongan');
$routes->post('/payroll/master-tarif/potongan/delete', 'Payroll::deleteTarifPotongan');
$routes->post('/payroll/master-tarif/potongan/delete/(:num)', 'Payroll::deleteTarifPotongan/$1');

// Konfigurasi Modernized Endpoints
$routes->get('/konfigurasi/master', 'Konfigurasi::getMaster');

// Users
$routes->get('/konfigurasi/users/list', 'Konfigurasi::getUsers');
$routes->get('/konfigurasi/users/detail/(:num)', 'Konfigurasi::getUserDetail/$1');
$routes->post('/konfigurasi/users/save', 'Konfigurasi::saveUser');
$routes->post('/konfigurasi/users/status', 'Konfigurasi::updateUserStatus');
$routes->post('/konfigurasi/users/reset-password', 'Konfigurasi::resetPassword');
$routes->post('/konfigurasi/users/delete/(:num)', 'Konfigurasi::deleteUser/$1');
$routes->delete('/konfigurasi/users/delete/(:num)', 'Konfigurasi::deleteUser/$1');

// Roles
$routes->get('/konfigurasi/roles/list', 'Konfigurasi::getRoles');
$routes->post('/konfigurasi/roles/save', 'Konfigurasi::saveRole');
$routes->post('/konfigurasi/roles/delete/(:num)', 'Konfigurasi::deleteRole/$1');
$routes->delete('/konfigurasi/roles/delete/(:num)', 'Konfigurasi::deleteRole/$1');

// Kelompok
$routes->get('/konfigurasi/kelompok/list', 'Konfigurasi::getKelompok');
$routes->post('/konfigurasi/kelompok/save', 'Konfigurasi::saveKelompok');
$routes->post('/konfigurasi/kelompok/delete/(:num)', 'Konfigurasi::deleteKelompok/$1');
$routes->delete('/konfigurasi/kelompok/delete/(:num)', 'Konfigurasi::deleteKelompok/$1');

// Hak Akses
$routes->get('/konfigurasi/hak-akses/matrix/(:num)', 'Konfigurasi::getHakAkses/$1');
$routes->post('/konfigurasi/hak-akses/toggle', 'Konfigurasi::saveHakAkses');
$routes->post('/konfigurasi/hak-akses/save-batch', 'Konfigurasi::saveHakAksesBatch');

// Standarisasi
$routes->get('/konfigurasi/standarisasi/list', 'Konfigurasi::getStandarisasi');
$routes->post('/konfigurasi/standarisasi/save', 'Konfigurasi::saveStandarisasi');
$routes->post('/konfigurasi/standarisasi/delete/(:num)', 'Konfigurasi::deleteStandarisasi/$1');
$routes->delete('/konfigurasi/standarisasi/delete/(:num)', 'Konfigurasi::deleteStandarisasi/$1');

// Jadwal Administrasi
$routes->get('/konfigurasi/jadwal-admin/list', 'Konfigurasi::getJadwalAdministrasi');
$routes->post('/konfigurasi/jadwal-admin/save', 'Konfigurasi::saveJadwalAdministrasi');
$routes->post('/konfigurasi/jadwal-admin/delete/(:num)', 'Konfigurasi::deleteJadwalAdministrasi/$1');
$routes->delete('/konfigurasi/jadwal-admin/delete/(:num)', 'Konfigurasi::deleteJadwalAdministrasi/$1');
$routes->get('/konfigurasi/administrasi/list', 'Konfigurasi::getJadwalAdministrasi');
$routes->post('/konfigurasi/administrasi/save', 'Konfigurasi::saveJadwalAdministrasi');
$routes->post('/konfigurasi/administrasi/delete/(:num)', 'Konfigurasi::deleteJadwalAdministrasi/$1');
$routes->delete('/konfigurasi/administrasi/delete/(:num)', 'Konfigurasi::deleteJadwalAdministrasi/$1');

// Target SKP
$routes->get('/konfigurasi/target/list', 'Konfigurasi::getTarget');
$routes->post('/konfigurasi/target/save', 'Konfigurasi::saveTarget');
$routes->post('/konfigurasi/target/delete/(:num)', 'Konfigurasi::deleteTarget/$1');
$routes->delete('/konfigurasi/target/delete/(:num)', 'Konfigurasi::deleteTarget/$1');

// Setting Pejabat
$routes->get('/konfigurasi/settings/list', 'Konfigurasi::getSettings');
$routes->post('/konfigurasi/settings/save', 'Konfigurasi::saveSetting');

// Approval Routing
$routes->get('/konfigurasi/approval/list', 'Konfigurasi::getApproval');
$routes->post('/konfigurasi/approval/save', 'Konfigurasi::saveApproval');
$routes->post('/konfigurasi/approval/delete/(:num)', 'Konfigurasi::deleteApproval/$1');
$routes->delete('/konfigurasi/approval/delete/(:num)', 'Konfigurasi::deleteApproval/$1');

//histori-pasien
$routes->get('/histori_pasien/load', 'HistoriPasien::load');

//konsul
$routes->post('/perencanaan_konsul/save', 'RawatJalan\PerencanaanKonsul::save');
$routes->get('/perencanaan_konsul/load', 'RawatJalan\PerencanaanKonsul::load');
$routes->get('/perencanaan_konsul/loadhistory', 'RawatJalan\PerencanaanKonsul::loadHistory');
$routes->post('/perencanaan_konsul/batalkonsul', 'RawatJalan\PerencanaanKonsul::batalKonsul');
$routes->post('/perencanaan_konsul/jawabkonsul', 'RawatJalan\PerencanaanKonsul::jawabKonsul');
$routes->get('/perencanaan_konsul/loadparamedis', 'RawatJalan\PerencanaanKonsul::load_paramedis');
$routes->get('/perencanaan_konsul/loadunit', 'RawatJalan\PerencanaanKonsul::load_unit');
$routes->post('/perencanaan_konsul/cetak', 'RawatJalan\PerencanaanKonsul::cetak_konsultasi');
$routes->post('/konsul_igd/save', 'RawatDarurat\KonsulIGD::save');
$routes->get('/konsul_igd/load', 'RawatDarurat\KonsulIGD::load');
$routes->get('/konsul_igd/loadunit', 'RawatDarurat\KonsulIGD::load_unit');
$routes->get('/konsul_igd/loadhistory', 'RawatDarurat\KonsulIGD::loadHistory');
$routes->post('/konsul_igd/batalkonsul', 'RawatDarurat\KonsulIGD::batalKonsul');

$routes->post('/anamnesis/save', 'RawatDarurat\Anamnesis::save');
$routes->get('/anamnesis/load', 'RawatDarurat\Anamnesis::load');

$routes->post('/perencanaan/save', 'RawatDarurat\Perencanaan::save');
$routes->get('/perencanaan/load', 'RawatDarurat\Perencanaan::load');
$routes->post('/nyeri/save', 'RawatDarurat\Nyeri::save');
$routes->get('/nyeri/load', 'RawatDarurat\Nyeri::load');
//icdx
$routes->post('/diagnosa/save', 'DiagnosaDokter::save');
$routes->get('/diagnosa/load', 'DiagnosaDokter::load');
$routes->get('/diagnosa/load_icdx', 'DiagnosaDokter::load_icdx');
$routes->get('/diagnosa/load_kasus', 'DiagnosaDokter::load_kasus');
$routes->get('/diagnosa/load_rme', 'DiagnosaDokter::loadDataRME');
$routes->get('/diagnosa/list_template', 'DiagnosaDokter::list_template');
$routes->post('/diagnosa/savetemplate', 'DiagnosaDokter::savetemplate');

$routes->post('/obstetri/save', 'RawatDarurat\Obstetri::save');
$routes->get('/obstetri/load', 'RawatDarurat\Obstetri::load');
$routes->post('/resiko_jatuh/save', 'RawatDarurat\ResikoJatuh::save');
$routes->get('/resiko_jatuh/load', 'RawatDarurat\ResikoJatuh::load');
$routes->post('/diagnosa_kebidanan/save', 'RawatDarurat\DiagnosaKebidanan::save');
$routes->get('/diagnosa_kebidanan/load', 'RawatDarurat\DiagnosaKebidanan::load');
$routes->post('/ginekologi/save', 'RawatDarurat\Ginekologi::save');
$routes->get('/ginekologi/load', 'RawatDarurat\Ginekologi::load');
$routes->post('/primary_survey/save', 'RawatDarurat\PrimarySurvey::save');
$routes->get('/primary_survey/load', 'RawatDarurat\PrimarySurvey::load');
$routes->get('/primary_survey/load_o2', 'RawatDarurat\PrimarySurvey::load_o2');
$routes->post('/pemeriksaan_fisik/save', 'RawatDarurat\PemeriksaanFisik::save');
$routes->get('/pemeriksaan_fisik/load', 'RawatDarurat\PemeriksaanFisik::load');
$routes->post('/objektif_kebidanan/save', 'RawatDarurat\ObjektifKebidanan::save');
$routes->get('/objektif_kebidanan/load', 'RawatDarurat\ObjektifKebidanan::load');
$routes->post('/objektif_kebidanan/savetindakan', 'RawatDarurat\ObjektifKebidanan::savetindakan');
$routes->get('/objektif_kebidanan/loadtindakan', 'RawatDarurat\ObjektifKebidanan::loadtindakan');
$routes->post('/objektif_kebidanan/edit_kebidanan', 'RawatDarurat\ObjektifKebidanan::edit_kebidanan');
$routes->post('/objektif_kebidanan/hapus_kebidanan', 'RawatDarurat\ObjektifKebidanan::hapus_kebidanan');
$routes->post('/diagnosa_keperawatan/save', 'RawatDarurat\DiagnosaKeperawatan::save');
$routes->get('/diagnosa_keperawatan/load', 'RawatDarurat\DiagnosaKeperawatan::load');
$routes->get('/diagnosa_keperawatan/loadDiagnosa', 'RawatDarurat\DiagnosaKeperawatan::loadDiagnosa');
$routes->post('/diagnosa_neonatus/save', 'RawatDarurat\DiagnosaNeonatus::save');
$routes->get('/diagnosa_neonatus/load', 'RawatDarurat\DiagnosaNeonatus::load');
$routes->post('/subjektif_kebidanan/save', 'RawatDarurat\SubjektifKebidanan::save');
$routes->get('/subjektif_kebidanan/load', 'RawatDarurat\SubjektifKebidanan::load');

$routes->post('/cara_keluar/save', 'RawatDarurat\CaraKeluar::save');
$routes->get('/cara_keluar/load', 'RawatDarurat\CaraKeluar::load');
$routes->post('/cara_keluar/cetak', 'RawatDarurat\CaraKeluar::print');
//kesimpulan akhir
$routes->post('/kesimpulan_akhir/save', 'RawatDarurat\KesimpulanAkhir::save');
$routes->get('/kesimpulan_akhir/load', 'RawatDarurat\KesimpulanAkhir::load');
//intervensi
$routes->post('/intervensi/save', 'RawatDarurat\Intervensi::save');
$routes->get('/intervensi/load', 'RawatDarurat\Intervensi::load');

//resep
$routes->get('/resep/master/dataobat', 'Resep::dataObatMaster');
$routes->get('/resep/master/obatracik', 'Resep::dataObatMaster');
$routes->get('/resep/master/depo', 'Resep::loadDepo');
$routes->get('/resep/master/urutracik', 'Resep::urutRacik');
$routes->get('/resep/master/perintah', 'Resep::perintah');
$routes->get('/resep/master/sediaan', 'Resep::sediaan');
$routes->get('/resep/master/satuan_kecil', 'Resep::satuan_kecil');
$routes->get('/resep/master/waktu_pakai', 'Resep::waktu_pakai');
$routes->get('/resep/master/cara_pakai', 'Resep::cara_pakai');
$routes->get('/resep/racik', 'Resep::satRacik');
$routes->post('/resep/order', 'Resep::orderResep');
$routes->post('/resep/order_racik', 'Resep::orderResepRacik');
$routes->get('/resep/history/order', 'Resep::historyOrder');
$routes->get('/resep/detailhistoryresep', 'Resep::detailHistoryResep');
$routes->get('/resep/order/hapus', 'Resep::hapusOrderResep');
$routes->post('/resep/cetak', 'Resep::print');
$routes->get('/resep/datalistracik', 'Resep::dataListRacik');
$routes->get('/resep/dataobat/templatebydokter', 'Resep::templateByDokter');
$routes->post('/resep/template/save', 'Resep::templateSave');
$routes->get('/resep/hapus/template_resep', 'Resep::hapusTemplateResep');
$routes->get('/resep/restriksi', 'Resep::restriksi');
$routes->get('/resep/getIterasi', 'Resep::getIterasi');

//billing
$routes->get('/billing/loadtindakan', 'Billing::loadTindakan');
$routes->get('/billing/load', 'Billing::load');
$routes->post('/billing/save', 'Billing::save');
$routes->post('/billing/delete', 'Billing::delete');
$routes->post('/billing/editqty', 'Billing::editQty');
$routes->get('/billing/loaddatapelaksana', 'Billing::loadDataPelaksana');
$routes->get('/billing/loadparamedis', 'Billing::load_paramedis');
$routes->get('/billing/loadkomponentarif', 'Billing::loadDataKomponenTarif');
$routes->post('/billing/addpelaksana', 'Billing::add_pelaksana');
$routes->post('/billing/deletepelaksana', 'Billing::delete_pelaksana');
// transfer
$routes->get('/transfer/cek', 'Billing::cekStatusTransfer');
$routes->post('/transfer/save', 'Billing::saveTransfer');
$routes->post('/transfer/batal', 'Billing::batalTransfer');
$routes->post('/perencanaan_observasi/save', 'RawatDarurat\PerencanaanObservasi::save');
$routes->get('/perencanaan_observasi/load', 'RawatDarurat\PerencanaanObservasi::load');
$routes->get('/perencanaan_observasi/load_table', 'RawatDarurat\PerencanaanObservasi::load_table');
$routes->get('/perencanaan_observasi/tabel', 'RawatDarurat\PerencanaanObservasi::tabel');
$routes->post('/perencanaan_observasi/edit_observasi', 'RawatDarurat\PerencanaanObservasi::edit_observasi');
$routes->post('/perencanaan_observasi/hapus_observasi', 'RawatDarurat\PerencanaanObservasi::hapus_observasi');
$routes->post('/cara_pulang/save', 'RawatDarurat\CaraPulang::save');
$routes->post('/cara_pulang/batal', 'RawatDarurat\CaraPulang::batal');
$routes->get('/cara_pulang/load', 'RawatDarurat\CaraPulang::load');
$routes->get('/cara_pulang/load_ruang', 'RawatDarurat\CaraPulang::load_ruang');
$routes->get('/cara_pulang/load_rs', 'RawatDarurat\CaraPulang::load_rs');
$routes->get('/cara_pulang/load_poli', 'RawatDarurat\CaraPulang::load_poli');
$routes->get('/cara_pulang/load_cara_keluar', 'RawatDarurat\CaraPulang::load_cara_keluar');
$routes->get('/cara_pulang/load_alasan_dirujuk', 'RawatDarurat\CaraPulang::load_alasan_dirujuk');
$routes->get('/cara_pulang/load_icdx', 'RawatDarurat\CaraPulang::load_icdx');
$routes->get('/cara_pulang/load_jenis_diagnosa', 'RawatDarurat\CaraPulang::load_jenis_diagnosa');
//lab
$routes->post('/laboratorium/save', 'Laboratorium::save');
$routes->post('/laboratorium/batalorder', 'Laboratorium::batalOrder');
$routes->get('/laboratorium/load', 'Laboratorium::load');
$routes->post('/laboratorium/saveorder', 'Laboratorium::saveOrder');
$routes->get('/laboratorium/loadhistory', 'Laboratorium::loadDataHistory');
$routes->get('/laboratorium/loadhistory_item', 'Laboratorium::loadDataHistory_item');
$routes->get('/laboratorium/loadpemeriksaan', 'Laboratorium::loadItemPemeriksaan');

//rad
$routes->post('/radiologi/save', 'Radiologi::save');
$routes->post('/radiologi/batalorder', 'Radiologi::batalOrder');
$routes->get('/radiologi/load', 'Radiologi::load');
$routes->get('/radiologi/loadhistory', 'Radiologi::loadDataHistory');
$routes->get('/radiologi/loadhistory_item', 'Radiologi::loadDataHistory_item');
$routes->get('/radiologi/loadpemeriksaan', 'Radiologi::loadItemPemeriksaan');
$routes->post('/radiologi/saveorder', 'Radiologi::saveOrder');
$routes->get('/radiologi/loadBerkasUpload', 'Radiologi::loadBerkasUpload');

//penunjang-template (rad & lab)
$routes->post('/penunjang/save', 'Penunjang::save');
$routes->get('/penunjang/load', 'Penunjang::load');
$routes->post('/penunjang/delete', 'Penunjang::delete');

$routes->post('/assesmen_awal_kebidanan/save', 'RawatDarurat\AssesmenAwalKebidanan::save');
$routes->get('/assesmen_awal_kebidanan/load', 'RawatDarurat\AssesmenAwalKebidanan::load');
$routes->post('/assesmen_awal_kebidanan/saveriwayat', 'RawatDarurat\AssesmenAwalKebidanan::saveriwayat');
$routes->get('/assesmen_awal_kebidanan/loadriwayat', 'RawatDarurat\AssesmenAwalKebidanan::loadriwayat');
$routes->post('/assesmen_awal_kebidanan/edit_riwayat_hamil', 'RawatDarurat\AssesmenAwalKebidanan::edit_riwayat_hamil');
$routes->post('/assesmen_awal_kebidanan/hapus_riwayat_hamil', 'RawatDarurat\AssesmenAwalKebidanan::hapus_riwayat_hamil');
$routes->post('/secondary_survey/save', 'RawatDarurat\SecondarySurvey::save');
$routes->get('/secondary_survey/load', 'RawatDarurat\SecondarySurvey::load');
$routes->post('/secondary_survey/uploadECG', 'RawatDarurat\SecondarySurvey::uploadECG');
$routes->get('/secondary_survey/loadECG', 'RawatDarurat\SecondarySurvey::loadECG');
$routes->post('/secondary_survey/hapus_ecg', 'RawatDarurat\SecondarySurvey::hapus_ecg');
$routes->post('/secondary_survey/uploadDataKlinis', 'RawatDarurat\SecondarySurvey::uploadDataKlinis');
$routes->get('/secondary_survey/loadDataKlinis', 'RawatDarurat\SecondarySurvey::loadDataKlinis');
$routes->post('/secondary_survey/hapus_data_klinis', 'RawatDarurat\SecondarySurvey::hapus_data_klinis');
$routes->post('/tindakan_keperawatan/save', 'RawatDarurat\TindakanKeperawatan::save');
$routes->get('/tindakan_keperawatan/load', 'RawatDarurat\TindakanKeperawatan::load');
$routes->get('/tindakan_keperawatan/load_paramedis', 'RawatDarurat\TindakanKeperawatan::load_paramedis');
$routes->get('/tindakan_keperawatan/load_rute', 'RawatDarurat\TindakanKeperawatan::load_rute');
$routes->get('/tindakan_keperawatan/loadTabelTindakan', 'RawatDarurat\TindakanKeperawatan::loadTabelTindakan');
$routes->post('/tindakan_keperawatan/edit_keperawatan', 'RawatDarurat\TindakanKeperawatan::edit_keperawatan');
$routes->post('/tindakan_keperawatan/hapus_keperawatan', 'RawatDarurat\TindakanKeperawatan::hapus_keperawatan');

//tindakan icd-ix
$routes->post('/icdix/save', 'TindakanICD9::save');
$routes->get('/icdix/load', 'TindakanICD9::load');
$routes->get('/icdix/load_tabel', 'TindakanICD9::loadTabelTindakan');
$routes->get('/icdix/search', 'TindakanICD9::search');
$routes->post('/icdix/hapus_icdix', 'TindakanICD9::hapus_icdix');

$routes->post('/masalah_keperawatan/save', 'RawatDarurat\MasalahKeperawatan::save');
$routes->get('/masalah_keperawatan/load', 'RawatDarurat\MasalahKeperawatan::load');
$routes->post('/pengkajian_keperawatan/save', 'RawatDarurat\PengkajianKeperawatan::save');
$routes->get('/pengkajian_keperawatan/load', 'RawatDarurat\PengkajianKeperawatan::load');
$routes->get('/bridging/icare', 'BridgingIcare::loadData');
$routes->get('/bridging/load/poli', 'BridgingBPJS::loadPoli');
$routes->get('/bridging/load/dpjp', 'BridgingBPJS::loadDPJP');
$routes->get('/bridging/cari_sep', 'BridgingBPJS::cariSEP');
$routes->get('/bridging/cari_kartu', 'BridgingBPJS::cariKartu');
$routes->post('/bridging/rencana_kontrol/simpan', 'BridgingBPJS::simpan_rencana_kontrol');
$routes->post('/bridging/rencana_kontrol/hapus', 'BridgingBPJS::hapus_rencana_kontrol');
$routes->get('/bridging/rencana_kontrol/history', 'BridgingBPJS::history_rencana_kontrol');
$routes->post('/bridging/surat_kontrol/cetak', 'BridgingBPJS::cetak_surat_kontrol');
$routes->get('/bridging/rujukan_surat_kontrol', 'BridgingBPJS::getRujukanSuratKontrol');
$routes->post('/bridging/getdata_peserta_bpjs', 'BridgingBPJS::getdata_peserta_bpjs');
$routes->post('/bridging/tambah_antrean_mjkn', 'BridgingBPJS::tambah_antrean_antrol');

$routes->post('/bridging/sep/update_tglkontrol', 'BridgingBPJS::updateTanggalKontrol');
$routes->get('/bridging/sep/detail', 'RegistrationSEP::detail');
$routes->get('/bridging/sep/status_pulang', 'RegistrationSEP::options');
$routes->post('/bridging/sep/action/(:segment)', 'RegistrationSEP::action/$1');
$routes->get('/bridging/sep/cari_referensi', 'BridgingBPJS::cariReferensiSEP');

$routes->post('/tindakan_kebidanan/save', 'RawatDarurat\TindakanKebidanan::save');
$routes->get('/tindakan_kebidanan/load', 'RawatDarurat\TindakanKebidanan::load');
$routes->post('/neonatal/save', 'RawatDarurat\NeonatalEWS::save');
$routes->get('/neonatal/load', 'RawatDarurat\NeonatalEWS::load');
$routes->get('/neonatal/load_grafik', 'RawatDarurat\NeonatalEWS::load_grafik');
$routes->get('/neonatal/load_tabel', 'RawatDarurat\NeonatalEWS::load_tabel');
$routes->post('/neonatal/hapus_ews', 'RawatDarurat\NeonatalEWS::hapus_ews');

//Routes rawat jalan
$routes->get('/awal_medis_global/get_module', 'RawatJalan\AwalMedisGlobal::get_module');
$routes->get('/antrian/rawat_jalan/belum_terlayani', 'RawatJalan\Antrian::index');
$routes->get('/antrian/rawat_jalan/terlayani', 'RawatJalan\Antrian::index');
$routes->get('/antrian/rawat_jalan/konsul', 'RawatJalan\Antrian::index');
$routes->get('/antrian/rawat_jalan/terlayani/menunggu/ruang', 'RawatJalan\Antrian::index');
$routes->get('/antrian/rawat_jalan/iskontrol', 'RawatJalan\Antrian::iskontrol');
$routes->post('/antrian/rawat_jalan/panggil', 'RawatJalan\Antrian::panggilAntrian');
$routes->get('/antrian/rawat_jalan/riwayat', 'RawatJalan\Antrian::index');
$routes->get('/antrian/rawat_jalan/berkelanjutan', 'RawatJalan\Antrian::index');
$routes->get('/antrian/rawat_jalan/getPlavonBPJS', 'RawatJalan\Antrian::getPlavonBPJS');
$routes->post('/antrian/rawat_jalan/updateterlayani', 'RawatJalan\Antrian::updateterlayani');

$routes->post('/skrining_resiko_jatuh/save', 'RawatJalan\SkriningResikoJatuh::save');
$routes->get('/skrining_resiko_jatuh/load', 'RawatJalan\SkriningResikoJatuh::load');
$routes->post('/pengkajian_nyeri/save', 'RawatJalan\PengkajianNyeri::save');
$routes->get('/pengkajian_nyeri/load', 'RawatJalan\PengkajianNyeri::load');
$routes->post('/pengkajian_medis/save', 'RawatJalan\PengkajianMedis::save');
$routes->get('/pengkajian_medis/load', 'RawatJalan\PengkajianMedis::load');
$routes->post('/resume_rawat_jalan/save', 'ResumeRawatJalan::save');
$routes->get('/resume_rawat_jalan/load', 'ResumeRawatJalan::load');
$routes->get('/resume_rawat_jalan/loadTabel', 'ResumeRawatJalan::loadTabel');

//pengkajian penyakit dalam
$routes->post('/rj_pengkajian_penyakit_dalam/save', 'RawatJalan\PengkajianPenyakitDalam::save');
$routes->get('/rj_pengkajian_penyakit_dalam/load', 'RawatJalan\PengkajianPenyakitDalam::load');
$routes->post('/rj_pengkajian_penyakit_dalam/print', 'RawatJalan\PengkajianPenyakitDalam::print');
//Monitoring HD 
$routes->post('/rj_monitoring_hd/save', 'RawatJalan\MonitoringHD::save');
$routes->get('/rj_monitoring_hd/load', 'RawatJalan\MonitoringHD::load');
$routes->post('/rj_monitoring_hd/print', 'RawatJalan\MonitoringHD::print');
$routes->post('/rj_monitoring_hd/saveTindakan', 'RawatJalan\MonitoringHD::saveTindakan');
$routes->get('/rj_monitoring_hd/loadTindakan', 'RawatJalan\MonitoringHD::loadTindakan');
$routes->post('/rj_monitoring_hd/deleteTindakan', 'RawatJalan\MonitoringHD::deleteTindakan');

//geriatri
$routes->post('/rj_pengkajian_geriatri/save', 'RawatJalan\PengkajianGeriatri::save');
$routes->get('/rj_pengkajian_geriatri/load', 'RawatJalan\PengkajianGeriatri::load');
$routes->post('/rj_geriatri_sindrom/save', 'RawatJalan\PengkajianGeriatri::saveSindrom');
$routes->get('/rj_geriatri_sindrom/load', 'RawatJalan\PengkajianGeriatri::loadSindrom');
$routes->post('/rj_geriatri_sindrom/print', 'RawatJalan\PengkajianGeriatri::print');

//pengkajian anak
$routes->post('/rj_pengkajian_anak/save', 'RawatJalan\PengkajianAnak::save');
$routes->get('/rj_pengkajian_anak/load', 'RawatJalan\PengkajianAnak::load');
$routes->post('/rj_pengkajian_anak/print', 'RawatJalan\PengkajianAnak::print');

$routes->post('/rj_pengkajian_tumbuh_kembang/save', 'RawatJalan\PengkajianTumbuhKembang::save');
$routes->get('/rj_pengkajian_tumbuh_kembang/load', 'RawatJalan\PengkajianTumbuhKembang::load');
$routes->post('/rj_pengkajian_tumbuh_kembang/print', 'RawatJalan\PengkajianTumbuhKembang::print');
$routes->post('/rj_pengkajian_jantung/save', 'RawatJalan\PengkajianJantung::save');
$routes->get('/rj_pengkajian_jantung/load', 'RawatJalan\PengkajianJantung::load');
$routes->post('/rj_pengkajian_jantung/print', 'RawatJalan\PengkajianJantung::print');
$routes->post('/rj_pengkajian_kandungan/save', 'RawatJalan\PengkajianKandungan::save');
$routes->get('/rj_pengkajian_kandungan/load', 'RawatJalan\PengkajianKandungan::load');
$routes->post('/rj_pengkajian_kandungan/print', 'RawatJalan\PengkajianKandungan::print');

//pengkajian ortopedi
$routes->post('/rj_pengkajian_ortopedi/save', 'RawatJalan\PengkajianOrtopedi::save');
$routes->get('/rj_pengkajian_ortopedi/load', 'RawatJalan\PengkajianOrtopedi::load');
$routes->post('/rj_pengkajian_ortopedi/print', 'RawatJalan\PengkajianOrtopedi::print');

$routes->post('/pemeriksaan_penunjang/save', 'RawatJalan\PemeriksaanPenunjang::save');
$routes->get('/pemeriksaan_penunjang/load', 'RawatJalan\PemeriksaanPenunjang::load');
$routes->post('/diagnosis/save', 'RawatJalan\Diagnosis::save');
$routes->get('/diagnosis/load', 'RawatJalan\Diagnosis::load');
$routes->post('/rj_pemeriksaan_fisik/save', 'RawatJalan\PemeriksaanFisik::save');
$routes->get('/rj_pemeriksaan_fisik/load', 'RawatJalan\PemeriksaanFisik::load');
//cara-keluar rajal
$routes->post('/rj_cara_keluar/save', 'RawatJalan\CaraKeluar::save');
$routes->get('/rj_cara_keluar/load', 'RawatJalan\CaraKeluar::load');
$routes->get('/rj_cara_keluar/load_cara_keluar', 'RawatJalan\CaraKeluar::load_cara_keluar');
$routes->post('/rj_cara_keluar/batal_keluar', 'RawatJalan\CaraKeluar::batal_keluar');

$routes->post('/skrining_gizi/save', 'RawatJalan\SkriningGizi::save');
$routes->get('/skrining_gizi/load', 'RawatJalan\SkriningGizi::load');
$routes->post('/rj_anamnesis/save', 'RawatJalan\Anamnesis::save');
$routes->get('/rj_anamnesis/load', 'RawatJalan\Anamnesis::load');
$routes->post('/rj_pemeriksaan_fisik_perawat/save', 'RawatJalan\PemeriksaanFisikPerawat::save');
$routes->get('/rj_pemeriksaan_fisik_perawat/load', 'RawatJalan\PemeriksaanFisikPerawat::load');
$routes->post('/rj_psikososial/save', 'RawatJalan\Psikososial::save');
$routes->get('/rj_psikososial/load', 'RawatJalan\Psikososial::load');
$routes->post('/rj_fungsional/save', 'RawatJalan\Fungsional::save');
$routes->get('/rj_fungsional/load', 'RawatJalan\Fungsional::load');
$routes->post('/rj_edukasi/save', 'RawatJalan\Edukasi::save');
$routes->get('/rj_edukasi/load', 'RawatJalan\Edukasi::load');
$routes->post('/rj_diagnosa_keperawatan/save', 'RawatJalan\DiagnosaKeperawatan::save');
$routes->get('/rj_diagnosa_keperawatan/load', 'RawatJalan\DiagnosaKeperawatan::load');
$routes->post('/rj_perencanaan_perawat/save', 'RawatJalan\PerencanaanPerawat::save');
$routes->get('/rj_perencanaan_perawat/load', 'RawatJalan\PerencanaanPerawat::load');
$routes->post('/rj_perencanaan_lain/save', 'RawatJalan\PerencanaanLain::save');
$routes->get('/rj_perencanaan_lain/load', 'RawatJalan\PerencanaanLain::load');

//pengkajian gigi
$routes->post('/rj_pengkajian_gigi/save', 'RawatJalan\PengkajianGigi::save');
$routes->get('/rj_pengkajian_gigi/load', 'RawatJalan\PengkajianGigi::load');
$routes->post('/rj_pengkajian_gigi/print', 'RawatJalan\PengkajianGigi::print');

//pemeriksaan paru
$routes->post('/rj_pemeriksaan_paru/save', 'RawatJalan\PemeriksaanParu::save');
$routes->get('/rj_pemeriksaan_paru/load', 'RawatJalan\PemeriksaanParu::load');
$routes->post('/rj_pemeriksaan_paru/print', 'RawatJalan\PemeriksaanParu::print');

//pemeriksaan mata
$routes->post('/rj_pemeriksaan_mata/save', 'RawatJalan\PemeriksaanMata::save');
$routes->get('/rj_pemeriksaan_mata/load', 'RawatJalan\PemeriksaanMata::load');
$routes->post('/rj_pemeriksaan_mata/print', 'RawatJalan\PemeriksaanMata::print');

//pemeriksaan rehab
$routes->post('/rj_rehab_pemeriksaan_medis/save', 'RawatJalan\PengkajianRehab::save');
$routes->get('/rj_rehab_pemeriksaan_medis/load', 'RawatJalan\PengkajianRehab::load');
$routes->post('/rj_rehab_pemeriksaan_rehab/save', 'RawatJalan\PengkajianRehab::saveRehab');
$routes->get('/rj_rehab_pemeriksaan_rehab/load', 'RawatJalan\PengkajianRehab::loadRehab');
$routes->post('/rj_rehab_pemeriksaan_rehab/print', 'RawatJalan\PengkajianRehab::printRehab');
$routes->post('/rj_rehab_pemeriksaan_rehab/cetak', 'RawatJalan\PengkajianRehab::printRehabNew');
$routes->get('/rj_rehab_pemeriksaan_rehab/loadParamedis', 'RawatJalan\PengkajianRehab::loadParamedis');
$routes->post('/rj_rehab_terapi/save', 'RawatJalan\PengkajianRehab::saveTerapi');
$routes->get('/rj_rehab_terapi/load', 'RawatJalan\PengkajianRehab::loadTerapi');
$routes->get('/rj_rehab_terapi/loadHistory', 'RawatJalan\PengkajianRehab::loadHistoryTerapi');

//asesmen kontrol
$routes->post('/rj_assesmen_kontrol/save', 'RawatJalan\AssesmenKontrol::save');
$routes->get('/rj_assesmen_kontrol/load', 'RawatJalan\AssesmenKontrol::load');
$routes->get('/rj_assesmen_kontrol/loadSebelumnya', 'RawatJalan\AssesmenKontrol::loadSebelumnya');
$routes->get('/rj_assesmen_kontrol/loadSekarang', 'RawatJalan\AssesmenKontrol::loadSekarang');
$routes->get('/rj_assesmen_kontrol/HistoryKontrol', 'RawatJalan\AssesmenKontrol::HistoryKontrol');

//hemodialisis
$routes->post('/rj_pengkajian_hemodialisis/save', 'RawatJalan\PengkajianHemodialisis::save');
$routes->get('/rj_pengkajian_hemodialisis/load', 'RawatJalan\PengkajianHemodialisis::load');
$routes->post('/rj_instruksi_medis/save', 'RawatJalan\PengkajianHemodialisis::saveInstruksi');
$routes->get('/rj_instruksi_medis/load', 'RawatJalan\PengkajianHemodialisis::loadInstruksi');
$routes->post('/rj_instruksi_medis/saveTindakan', 'RawatJalan\PengkajianHemodialisis::saveTindakan');
$routes->get('/rj_instruksi_medis/loadTindakan', 'RawatJalan\PengkajianHemodialisis::loadTindakan');
$routes->post('/rj_instruksi_medis/deleteTindakan', 'RawatJalan\PengkajianHemodialisis::deleteTindakan');
$routes->post('/rj_pemeriksaan_penunjang_hd/save', 'RawatJalan\PengkajianHemodialisis::savePenunjangHD');
$routes->get('/rj_pemeriksaan_penunjang_hd/load', 'RawatJalan\PengkajianHemodialisis::loadPenunjangHD');
$routes->post('/rj_hemodialisis_harian/save', 'RawatJalan\PengkajianHemodialisis::saveHarian');
$routes->get('/rj_hemodialisis_harian/load', 'RawatJalan\PengkajianHemodialisis::loadHarian');
$routes->post('/rj_hemodialisis/print', 'RawatJalan\PengkajianHemodialisis::print');

//Pemeriksaan Medis HD
$routes->post('/rj_penyakit_dalam/save', 'RawatJalan\PenyakitDalam::save');
$routes->get('/rj_penyakit_dalam/load', 'RawatJalan\PenyakitDalam::load');

//pengkajian kulit
$routes->post('/rj_pengkajian_kulit/save', 'RawatJalan\PengkajianKulit::save');
$routes->get('/rj_pengkajian_kulit/load', 'RawatJalan\PengkajianKulit::load');
$routes->post('/rj_pengkajian_kulit/print', 'RawatJalan\PengkajianKulit::print');

//pengkajian bedah
$routes->post('/rj_pengkajian_bedah/save', 'RawatJalan\PengkajianBedah::save');
$routes->get('/rj_pengkajian_bedah/load', 'RawatJalan\PengkajianBedah::load');
$routes->post('/rj_pengkajian_bedah/print', 'RawatJalan\PengkajianBedah::print');

//psikiatri
$routes->post('/rj_psikiatri_pemeriksaan_medis/save', 'RawatJalan\PemeriksaanPsikiatri::save');
$routes->get('/rj_psikiatri_pemeriksaan_medis/load', 'RawatJalan\PemeriksaanPsikiatri::load');
$routes->post('/rj_status_psikiatri/save', 'RawatJalan\PemeriksaanPsikiatri::saveStatus');
$routes->get('/rj_status_psikiatri/load', 'RawatJalan\PemeriksaanPsikiatri::loadStatus');
$routes->post('/rj_psikiatri_pemeriksaan_medis/print', 'RawatJalan\PemeriksaanPsikiatri::print');

//pengkajian syaraf
$routes->post('/rj_pengkajian_syaraf/save', 'RawatJalan\PengkajianSyaraf::save');
$routes->get('/rj_pengkajian_syaraf/load', 'RawatJalan\PengkajianSyaraf::load');
$routes->post('/rj_pengkajian_syaraf/print', 'RawatJalan\PengkajianSyaraf::print');

//resume-rajal
$routes->get('/summary_resume_rajal/load', 'RawatJalan\ResumeRajal::load');
$routes->post('/summary_resume_rajal/cetak', 'RawatJalan\ResumeRajal::print');
$routes->post('/resume_medis_rajal/cetak', 'RawatJalan\ResumeRajal::print_resume_medis');

//----> ROUTES BARU <----//
$routes->get('/rj_perencanaan_lain/loadKonteks', 'RawatJalan\PerencanaanLain::loadKonteks');

//routes rekam_medis
$routes->get('/daftar/pasien/search', 'RekamMedis\Antrian::searchPasien');
$routes->get('/daftar/pasien/hari_ini', 'RekamMedis\Antrian::index');
$routes->get('/daftar/pasien/kemarin', 'RekamMedis\Antrian::index');
$routes->get('/daftar/pasien/peminjaman', 'RekamMedis\Antrian::index');
$routes->get('/daftar/pasien/cari_pasien', 'RekamMedis\Antrian::index');
$routes->get('/daftar/pasien/histori_pinjam', 'RekamMedis\Antrian::index');
$routes->get('/verifikasi/prepareDokumen', 'RekamMedis\Verifikasi::getEntitasPerKlasifikasi');
$routes->get('/verifikasi/statusDokumen', 'RekamMedis\Verifikasi::verifiedStatus');
$routes->post('/verifikasi/dokumen/save', 'RekamMedis\Verifikasi::save');
$routes->get('/verifikasi/loadDokumen', 'RekamMedis\Verifikasi::loadDokumen');
$routes->post('/verifikasi/verifikasiPasien', 'RekamMedis\Verifikasi::verifikasiPasien');
$routes->post('/verifikasi/batalVerifikasi', 'RekamMedis\Verifikasi::batalVerifikasi');
$routes->get('/verifikasi/loadListCetak', 'RekamMedis\Verifikasi::loadListCetak');
$routes->get('/verifikasi/isverifikasi', 'RekamMedis\Verifikasi::isverifikasi');
//loadUnit
$routes->get('/rekam/loadUnit', 'RekamMedis\Antrian::loadUnit');
$routes->get('/rekam/loadRuang', 'RekamMedis\Antrian::loadRuang');
//peminjaman dokumen
$routes->get('/peminjaman/load', 'RekamMedis\Peminjaman::load');
$routes->post('/peminjaman/save', 'RekamMedis\Peminjaman::save');
$routes->post('/peminjaman/approve', 'RekamMedis\Peminjaman::approve');
$routes->post('/peminjaman/batal_approve', 'RekamMedis\Peminjaman::batal_approve');
$routes->get('/peminjaman/loadHistoryDokumen', 'RekamMedis\Peminjaman::loadHistoryDokumen');
$routes->get('/peminjaman/loadListCetak', 'RekamMedis\Peminjaman::loadListCetak');

//cetak assesmen perawat
$routes->post('/assesmen_perawat/cetak', 'RawatJalan\CetakAssesmenPerawat::print');

//loadsebelumnya
$routes->get('/rj_pengkajian_gigi/sebelumnya', 'RawatJalan\PengkajianGigi::sebelumnya');
$routes->get('/rj_pengkajian_hemodialisis/sebelumnya', 'RawatJalan\PengkajianHemodialisis::sebelumnya');
$routes->get('/rj_hemodialisis_harian/sebelumnya', 'RawatJalan\PengkajianHemodialisis::sebelumnyaHarian');
$routes->get('/rj_hemodialisis_penunjang/sebelumnya', 'RawatJalan\PengkajianHemodialisis::sebelumnyaPenunjangHD');

//diagnosa-keperawatan rajal
$routes->post('/rj_diagnosa_keperawatan/save', 'RawatJalan\DiagnosaKeperawatan::save');
$routes->get('/rj_diagnosa_keperawatan/load', 'RawatJalan\DiagnosaKeperawatan::load');
$routes->get('/rj_diagnosa_keperawatan/loadDiagnosa', 'RawatJalan\DiagnosaKeperawatan::loadDiagnosa');

//ganti dokter
$routes->get('/dokter/load', 'GantiDokter::loadDataDokter');
$routes->post('/dokter/save', 'GantiDokter::saveGantiDokter');

//cetak-dokumen-rajal
$routes->get('/cetak_dokumen/loadDokumen', 'CetakDokumen::loadDokumen');
$routes->post('/cetak_dokumen/cetak', 'CetakDokumen::print');

//cetak transfer-internal
$routes->post('/transfer_internal/print', 'CetakTransferInternal::print');

//poli-asal
$routes->get('/poli_asal/load', 'RawatJalan\PoliAsal::load');

//RAWAT INAP
$routes->get('/antrian/rawat_inap/belum_terlayani', 'RawatInap\Antrian::index');
$routes->get('/antrian/rawat_inap/terlayani', 'RawatInap\Antrian::index');
$routes->get('/antrian/rawat_inap/loadRuang', 'RawatInap\Antrian::index');
$routes->get('/antrian/rawat_inap/konsul', 'RawatInap\Antrian::index');
$routes->get('/antrian/rawat_inap/terlayani/menunggu/ruang', 'RawatInap\Antrian::index');

//serah terima
$routes->post('/ri_serah_terima/save', 'RawatInap\SerahTerima::save');
$routes->get('/ri_serah_terima/load', 'RawatInap\SerahTerima::load');
$routes->post('/ri_serah_terima/saveTindakan', 'RawatInap\SerahTerima::saveTindakan');
$routes->get('/ri_serah_terima/loadTindakan', 'RawatInap\SerahTerima::loadTindakan');
$routes->get('/ri_serah_terima/loadDokter', 'RawatInap\SerahTerima::loadDokter');
$routes->get('/ri_serah_terima/loadRuangan', 'RawatInap\SerahTerima::loadRuangan');
$routes->get('/ri_serah_terima/loadTindakanLab', 'RawatInap\SerahTerima::loadTindakanLab');
$routes->get('/ri_serah_terima/loadObatTerapi', 'RawatInap\SerahTerima::loadObatTerapi');

//pengkajian awal keperawatan
$routes->post('/ri_pengkajian_awal_keperawatan/save', 'RawatInap\PengkajianAwalKeperawatan::save');
$routes->post('/ri_pengkajian_awal_keperawatan/saveTemplate', 'RawatInap\PengkajianAwalKeperawatan::saveTemplate');
$routes->get('/ri_pengkajian_awal_keperawatan/load', 'RawatInap\PengkajianAwalKeperawatan::load');
$routes->post('/ri_pengkajian_awal_keperawatan/cetak', 'RawatInap\PengkajianAwalKeperawatan::cetak');
$routes->get('/ri_pengkajian_awal_keperawatan/loadAgama', 'RawatInap\PengkajianAwalKeperawatan::loadAgama');
$routes->get('/ri_pengkajian_awal_keperawatan/loadPendidikan', 'RawatInap\PengkajianAwalKeperawatan::loadPendidikan');
$routes->get('/ri_pengkajian_awal_keperawatan/loadBahasa', 'RawatInap\PengkajianAwalKeperawatan::loadBahasa');

//pengkajian awal medis
$routes->post('/ri_pengkajian_awal_medis/save', 'RawatInap\PengkajianAwalMedis::save');
$routes->get('/ri_pengkajian_awal_medis/load', 'RawatInap\PengkajianAwalMedis::load');
$routes->post('/ri_pengkajian_awal_medis/print', 'RawatInap\PengkajianAwalMedis::print');

//asuhan gizi
$routes->get("/ri_asuhan_gizi/getdiagnosa_gizi", "RawatInap\AsuhanGizi::getdiagnosa_gizi");
$routes->get("/ri_asuhan_gizi/getDiit", "RawatInap\AsuhanGizi::getDiit");
$routes->get("/ri_asuhan_gizi/getBentukMakan", "RawatInap\AsuhanGizi::getBentukMakan");

//asesmen pra bedah
$routes->post('/ass_pra_bedah/save', 'RawatInap\AssPraBedah::save');
$routes->get('/ass_pra_bedah/load', 'RawatInap\AssPraBedah::load');
$routes->post('/ass_pra_bedah/cetak', 'RawatInap\AssPraBedah::print');
$routes->get('/ass_pra_bedah/verifikasi_pin', 'RawatInap\AssPraBedah::verifikasiPIN');


//identitas bayi
$routes->post('/ri_identitas_bayi/save', 'RawatInap\IdentitasBayi::save');
$routes->get('/ri_identitas_bayi/load', 'RawatInap\IdentitasBayi::load');
$routes->post('/ri_identitas_bayi/simpan_sidik_jari', 'RawatInap\IdentitasBayi::simpan_sidik_jari');
$routes->post('/ri_identitas_bayi/cetak', 'RawatInap\IdentitasBayi::cetak');
$routes->get('/ri_identitas_bayi/verifikasi_pin', 'RawatInap\IdentitasBayi::verifikasiPIN');


//askep bayi baru lahir
$routes->post('/ri_askep_bayi_baru_lahir/save', 'RawatInap\AskepBayiBaruLahir::save');
$routes->get('/ri_askep_bayi_baru_lahir/load', 'RawatInap\AskepBayiBaruLahir::load');
$routes->get('/ri_askep_bayi_baru_lahir/getdtibu', 'RawatInap\AskepBayiBaruLahir::getdtibu');
$routes->post('/ri_askep_bayi_baru_lahir/cetak', 'RawatInap\AskepBayiBaruLahir::cetak');

//jadwal pemberian minum
$routes->post('/ri_peri_jadwal_pemberian_minum/save', 'RawatInap\JadwalPemberianMinum::save');
$routes->get('/ri_peri_jadwal_pemberian_minum/load', 'RawatInap\JadwalPemberianMinum::load');
$routes->post('/ri_peri_jadwal_pemberian_minum/delete', 'RawatInap\JadwalPemberianMinum::deleteData');
$routes->post('/ri_peri_jadwal_pemberian_minum/cetak', 'RawatInap\JadwalPemberianMinum::cetak');

//fenton preterm growth chart
$routes->post('/ri_fenton_preterm_growth_chart/save', 'RawatInap\FentonPretermGrowthChart::save');
$routes->get('/ri_fenton_preterm_growth_chart/load', 'RawatInap\FentonPretermGrowthChart::load');
$routes->post('/ri_fenton_preterm_growth_chart/cetak', 'RawatInap\FentonPretermGrowthChart::cetak');

//tindakan anastesi
$routes->get('/tindakan_anastesi/loadJenisInformasi', 'RawatInap\TindakanAnastesi::loadJenisInformasi');
$routes->post('/tindakan_anastesi/save', 'RawatInap\TindakanAnastesi::save');
$routes->get('/tindakan_anastesi/load', 'RawatInap\TindakanAnastesi::load');
$routes->post('/tindakan_anastesi/print', 'RawatInap\TindakanAnastesi::print');
$routes->get('/tindakan_anastesi/verifikasi_pin', 'RawatInap\TindakanAnastesi::verifikasiPIN');
$routes->get('/tindakan_anastesi/verifikasi_pin_setuju', 'RawatInap\TindakanAnastesi::verifikasiPIN_setuju');

//discharge planning
$routes->post('/ri_discharge_planning/save', 'RawatInap\DischargePlanning::save');
$routes->get('/ri_discharge_planning/load', 'RawatInap\DischargePlanning::load');
$routes->post('/ri_discharge_planning/print', 'RawatInap\DischargePlanning::print');
$routes->get('/ri_discharge_planning/verifikasi_pin', 'RawatInap\DischargePlanning::verifikasiPIN');


//asesmen anastesi sedasi
$routes->post('/ass_anestesi_sedasi/save', 'RawatInap\AssAnastesiSedasi::save');
$routes->get('/ass_anestesi_sedasi/load', 'RawatInap\AssAnastesiSedasi::load');
$routes->post('/ass_anestesi_sedasi/print', 'RawatInap\AssAnastesiSedasi::print');
$routes->get('/ass_anestesi_sedasi/verifikasi_pin', 'RawatInap\AssAnastesiSedasi::verifikasiPIN');


//laporan pembedahan
$routes->post('/laporan_pembedahan/save', 'RawatInap\LaporanPembedahan::save');
$routes->get('/laporan_pembedahan/load', 'RawatInap\LaporanPembedahan::load');
$routes->post('/laporan_pembedahan/print', 'RawatInap\LaporanPembedahan::print');
$routes->get('/laporan_pembedahan/verifikasi_pin', 'RawatInap\LaporanPembedahan::verifikasiPIN');
$routes->post('/laporan_pembedahan/delete', 'RawatInap\LaporanPembedahan::delete');

//resume medis
$routes->get('/resume_medis/loadObatRawat', 'RawatInap\ResumeMedis::loadObatRawat');
$routes->get('/resume_medis/loadTindakanOperasi', 'RawatInap\ResumeMedis::loadTindakanOperasi');
$routes->post('/resume_medis/save', 'RawatInap\ResumeMedis::save');
$routes->get('/resume_medis/load', 'RawatInap\ResumeMedis::load');
$routes->post('/resume_medis/cetak', 'RawatInap\ResumeMedis::cetak');
$routes->get('/resume_medis/loadDataDiagnosa', 'RawatInap\ResumeMedis::loadDataDiagnosa');
$routes->get('/resume_medis/loadDataTindakan', 'RawatInap\ResumeMedis::loadDataTindakan');
$routes->get('/resume_medis/verifikasi_pin', 'RawatInap\ResumeMedis::verifikasiPIN');

//edukasi terintegrasi
$routes->get('/ri_edukasi_terintegrasi/loadMaster', 'RawatInap\EdukasiTerintegrasi::loadMaster');
$routes->post('/ri_edukasi_terintegrasi/cetak', 'RawatInap\EdukasiTerintegrasi::cetak');
$routes->post('/ri_edukasi_terintegrasi/perawat_ri/save', 'RawatInap\EdukasiTerintegrasi::savePerawatRI');
$routes->get('/ri_edukasi_terintegrasi/perawat_ri/load', 'RawatInap\EdukasiTerintegrasi::loadPerawatRI');
$routes->post('/ri_edukasi_terintegrasi/perawat_pemulangan/save', 'RawatInap\EdukasiTerintegrasi::savePerawatPemulangan');
$routes->get('/ri_edukasi_terintegrasi/perawat_pemulangan/load', 'RawatInap\EdukasiTerintegrasi::loadPerawatPemulangan');
$routes->post('/ri_edukasi_terintegrasi/perawat_kolaboratif/save', 'RawatInap\EdukasiTerintegrasi::savePerawatKolaboratif');
$routes->get('/ri_edukasi_terintegrasi/perawat_kolaboratif/load', 'RawatInap\EdukasiTerintegrasi::loadPerawatKolaboratif');
$routes->post('/ri_edukasi_terintegrasi/ahli_gizi/save', 'RawatInap\EdukasiTerintegrasi::saveAhliGizi');
$routes->get('/ri_edukasi_terintegrasi/ahli_gizi/load', 'RawatInap\EdukasiTerintegrasi::loadAhliGizi');
$routes->post('/ri_edukasi_terintegrasi/apoteker/save', 'RawatInap\EdukasiTerintegrasi::saveApoteker');
$routes->get('/ri_edukasi_terintegrasi/apoteker/load', 'RawatInap\EdukasiTerintegrasi::loadApoteker');
$routes->post('/ri_edukasi_terintegrasi/dokter/save', 'RawatInap\EdukasiTerintegrasi::saveDokterRI');
$routes->get('/ri_edukasi_terintegrasi/dokter/load', 'RawatInap\EdukasiTerintegrasi::loadDokterRI');
$routes->post('/ri_edukasi_terintegrasi/terapis/save', 'RawatInap\EdukasiTerintegrasi::saveTerapis');
$routes->get('/ri_edukasi_terintegrasi/terapis/load', 'RawatInap\EdukasiTerintegrasi::loadTerapis');
$routes->get('/ri_edukasi_terintegrasi/verifikasi_Gizi', 'RawatInap\EdukasiTerintegrasi::verifikasiGizi');
$routes->get('/ri_edukasi_terintegrasi/verifikasiTerapis', 'RawatInap\EdukasiTerintegrasi::verifikasiTerapis');
$routes->get('/ri_edukasi_terintegrasi/verifikasiDokter', 'RawatInap\EdukasiTerintegrasi::verifikasiDokter');

//LEMBAR OBSERVASI
//tab => lembar observasi
$routes->get('/lembar_observasi/load', 'RawatInap\LembarObservasi::load');
$routes->post('/lembar_observasi/save', 'RawatInap\LembarObservasi::save');
$routes->get('/lembar_observasi/loadKesadaran', 'RawatInap\LembarObservasi::loadKesadaran');
$routes->get("/lembar_observasi/loadSkor", "RawatInap\LembarObservasi::loadSkor");
//tab => grafik
$routes->get("/lembar_observasi/loadGrafik", "RawatInap\LembarObservasi::loadGrafik");
//tab => histori & histori askeb
$routes->get("/lembar_observasi/loadHistory", "RawatInap\LembarObservasi::loadHistory");
$routes->post("/lembar_observasi/deleteHistory", "RawatInap\LembarObservasi::deleteHistory");
//tab => histori lab
$routes->get("/lembar_observasi/loadHistoryLab", "RawatInap\LembarObservasi::loadHistoryLab");
//tab => hasil lab
$routes->get("/lembar_observasi/loadHasilLab", "RawatInap\LembarObservasi::loadHasilLab");
//data terapi
$routes->get('/lembar_observasi/getdata_obat_terapi', 'RawatInap\LembarObservasi::getdata_obat_terapi');
$routes->post("/lembar_observasi/saveTerapi", "RawatInap\LembarObservasi::saveTerapi");
$routes->get("/lembar_observasi/loadTerapi", "RawatInap\LembarObservasi::loadTerapi");
$routes->post("/lembar_observasi/deleteTerapi", "RawatInap\LembarObservasi::deleteTerapi");
$routes->post("/lembar_observasi/simpan_ttd_terapi_obat", "RawatInap\LembarObservasi::simpan_ttd_terapi_obat");
$routes->get('/lembar_observasi/loadCaraPemberian', 'RawatInap\LembarObservasi::loadCaraPemberian');
//cetakan observasi
$routes->post("/lembar_observasi/cetak_ewss", "RawatInap\LembarObservasi::cetak_ewss");
$routes->post("/lembar_observasi/cetak_lembar_observasi", "RawatInap\LembarObservasi::cetak_lembar_observasi");
$routes->post("/lembar_observasi/cetak_asuhan_kebidanan", "RawatInap\LembarObservasi::cetak_asuhan_kebidanan");
$routes->post("/lembar_observasi/cetak_lembar_observasi_kritis", "RawatInap\LembarObservasi::cetak_lembar_observasi_kritis");
$routes->post("/lembar_observasi/cetak_lembar_observasi_kritis_icu", "RawatInap\LembarObservasi::cetak_lembar_observasi_kritis_icu");
$routes->post("/lembar_observasi/cetak_lembar_observasi_perawatan_intensif", "RawatInap\LembarObservasi::cetak_lembar_observasi_perawatan_intensif");
$routes->post("/lembar_observasi/cetak_ewss_anak", "RawatInap\LembarObservasi::cetak_ewss_anak");

//konsul inap
$routes->post('/perencanaan_konsul_inap/save', 'RawatInap\PerencanaanKonsulInap::save');
$routes->get('/perencanaan_konsul_inap/load', 'RawatInap\PerencanaanKonsulInap::load');
$routes->get('/perencanaan_konsul_inap/loadhistory', 'RawatInap\PerencanaanKonsulInap::loadHistory');
$routes->post('/perencanaan_konsul_inap/batalkonsul', 'RawatInap\PerencanaanKonsulInap::batalKonsul');
$routes->post('/perencanaan_konsul_inap/jawabkonsul', 'RawatInap\PerencanaanKonsulInap::jawabKonsul');
$routes->get('/perencanaan_konsul_inap/loadparamedis', 'RawatInap\PerencanaanKonsulInap::load_paramedis');
$routes->get('/perencanaan_konsul_inap/loadunit', 'RawatInap\PerencanaanKonsulInap::load_unit');
$routes->post('/perencanaan_konsul_inap/cetak', 'RawatInap\PerencanaanKonsulInap::cetak_konsultasi');
$routes->get('/perencanaan_konsul_inap/loadspesialisasi', 'RawatInap\PerencanaanKonsulInap::load_spesialisasi');
$routes->get('/perencanaan_konsul_inap/loadparamedis_byspesialisasi', 'RawatInap\PerencanaanKonsulInap::load_paramedis_by_spesialisasi');
$routes->post('/perencanaan_konsul_inap/jawabkonsul_inap', 'RawatInap\PerencanaanKonsulInap::jawabKonsulInap');
$routes->post('/perencanaan_konsul_inap/save_inap', 'RawatInap\PerencanaanKonsulInap::saveInap');
$routes->get('/perencanaan_konsul_inap/load_inap', 'RawatInap\PerencanaanKonsulInap::loadInap');
$routes->get('/perencanaan_konsul_inap/loadhistory_inap', 'RawatInap\PerencanaanKonsulInap::loadHistoryInap');
$routes->post('/perencanaan_konsul_inap/hapuskonsul_inap', 'RawatInap\PerencanaanKonsulInap::hapusKonsulInap');
$routes->post('/perencanaan_konsul_inap/cetak_konsul_inap', 'RawatInap\PerencanaanKonsulInap::cetakKonsulInap');
$routes->get('/perencanaan_konsul_inap/loadDataKonsul', 'RawatInap\PerencanaanKonsulInap::loadDataKonsul');


//informed consent karawang
$routes->post('/informed_consent_karawang/save', 'RawatInap\InformedConsentKarawang::save');
$routes->get('/informed_consent_karawang/load', 'RawatInap\InformedConsentKarawang::load');
$routes->post('/informed_consent_karawang/print', 'RawatInap\InformedConsentKarawang::print');

//medical safety checklist
$routes->post('/medical_safety_checklist/save', 'RawatInap\MedicalSafetyChecklist::save');
$routes->get('/medical_safety_checklist/load', 'RawatInap\MedicalSafetyChecklist::load');
$routes->post('/medical_safety_checklist/print', 'RawatInap\MedicalSafetyChecklist::print');


$routes->post('/catatan_sedasi/save', 'RawatInap\CatatanSedasi::save');
$routes->get('/catatan_sedasi/load', 'RawatInap\CatatanSedasi::load');
$routes->post('/catatan_sedasi/cetak', 'RawatInap\CatatanSedasi::print');

//skrining dini ibu resiko tinggi
$routes->get('/skrining_dini_ibu_resiko_tinggi/loadAgama', 'RawatInap\SkriningDiniIbuResikoTinggi::loadAgama');
$routes->get('/skrining_dini_ibu_resiko_tinggi/loadPendidikan', 'RawatInap\SkriningDiniIbuResikoTinggi::loadPendidikan');
$routes->get('/skrining_dini_ibu_resiko_tinggi/loadPekerjaan', 'RawatInap\SkriningDiniIbuResikoTinggi::loadPekerjaan');
$routes->get('/skrining_dini_ibu_resiko_tinggi/loadMaster', 'RawatInap\SkriningDiniIbuResikoTinggi::loadMaster');
$routes->post('/skrining_dini_ibu_resiko_tinggi/save', 'RawatInap\SkriningDiniIbuResikoTinggi::save');
$routes->get('/skrining_dini_ibu_resiko_tinggi/load', 'RawatInap\SkriningDiniIbuResikoTinggi::load');

//MASTER
//master ttd
$routes->get('/master_ttd/loadDataMaster', 'Admin\MasterTTD::loadDataMaster');

//profile
$routes->post('/profile/profile_upload', 'Profile::profileUpload');
$routes->get('/profile/get_profile', 'Profile::getProfile');
$routes->get('/profile/get_ktp', 'Profile::getKTP');
$routes->get('/profile/get_sip', 'Profile::getSIP');

//cara-keluar ranap
$routes->post('/ri_cara_keluar/save', 'RawatInap\CaraKeluar::save');
$routes->get('/ri_cara_keluar/load', 'RawatInap\CaraKeluar::load');
$routes->get('/ri_cara_keluar/load_cara_keluar', 'RawatInap\CaraKeluar::load_cara_keluar');
$routes->post('/ri_cara_keluar/batal_keluar', 'RawatInap\CaraKeluar::batal_keluar');

//---ROUTES VERIFIKASI PIN---
$routes->get('/verifikasi_pin', 'VerifikasiPIN::verifikasiPIN');
$routes->get('/cppt/verifikasi_pin', 'VerifikasiPIN::CpptVerifikasiPIN');
$routes->get('/cppt/verifikasi_pin/verifall', 'VerifikasiPIN::CpptVerifikasiPINAll');

// RINGKASAN PASIEN
$routes->get('/ringkasan_pasien/loadDiagnosa', 'RingkasanPasien::loadDiagnosa');
$routes->get('/ringkasan_pasien/loadResep', 'RingkasanPasien::loadResep');
$routes->get('/ringkasan_pasien/loadResepDetail', 'RingkasanPasien::loadResepDetail');
$routes->get('/ringkasan_pasien/loadLab', 'RingkasanPasien::loadLab');
$routes->get('/ringkasan_pasien/loadLabDetail', 'RingkasanPasien::loadLabDetail');
$routes->get('/ringkasan_pasien/loadRad', 'RingkasanPasien::loadRad');
$routes->get('/ringkasan_pasien/loadRadDetail', 'RingkasanPasien::loadRadDetail');
$routes->get('/ringkasan_pasien/loadPA', 'RingkasanPasien::loadPA');
$routes->get('/ringkasan_pasien/loadPADetail', 'RingkasanPasien::loadPADetail');

//pengkajian awal medis - IGD
$routes->post('/igd_pengkajian_awal_medis/save', 'RawatDarurat\PengkajianAwalMedis::save');
$routes->get('/igd_pengkajian_awal_medis/load', 'RawatDarurat\PengkajianAwalMedis::load');
$routes->post('/igd_pengkajian_awal_medis/saveRiwayatHamil', 'RawatDarurat\PengkajianAwalMedis::saveRiwayatHamil');
$routes->get('/igd_pengkajian_awal_medis/loadRiwayatHamil', 'RawatDarurat\PengkajianAwalMedis::loadRiwayatHamil');
$routes->post('/igd_pengkajian_awal_medis/deleteRiwayatHamil', 'RawatDarurat\PengkajianAwalMedis::deleteRiwayatHamil');
$routes->get('/igd_pengkajian_awal_medis/loadDokter', 'RawatDarurat\PengkajianAwalMedis::loadDokter');
$routes->post('/igd_pengkajian_awal_medis/print', 'RawatDarurat\PengkajianAwalMedis::print');

//pengkajian awal keperawatan - IGD
$routes->post('/igd_pengkajian_awal_keperawatan/save', 'RawatDarurat\PengkajianAwalKeperawatan::save');
$routes->get('/igd_pengkajian_awal_keperawatan/load', 'RawatDarurat\PengkajianAwalKeperawatan::load');
$routes->get('/igd_pengkajian_awal_keperawatan/loadKeperawatan', 'RawatDarurat\PengkajianAwalKeperawatan::loadKeperawatan');
$routes->post('/igd_pengkajian_awal_keperawatan/print', 'RawatDarurat\PengkajianAwalKeperawatan::print');

//pengkajian awal medis - RWJ
$routes->post('/rj_pengkajian_awal_medis/save', 'RawatJalan\PengkajianAwalMedis::save');
$routes->get('/rj_pengkajian_awal_medis/load', 'RawatJalan\PengkajianAwalMedis::load');
$routes->post('/rj_pengkajian_awal_medis/print', 'RawatJalan\PengkajianAwalMedis::print');

//pengkajian awal keperawatan - RWJ
$routes->post('/rj_pengkajian_awal_keperawatan/save', 'RawatJalan\PengkajianAwalKeperawatan::save');
$routes->get('/rj_pengkajian_awal_keperawatan/load', 'RawatJalan\PengkajianAwalKeperawatan::load');
$routes->post('/rj_pengkajian_awal_keperawatan/print', 'RawatJalan\PengkajianAwalKeperawatan::print');

//rekonsiliasi obat
$routes->get('/rekonsiliasi_obat/load', 'RekonsiliasiObat::load');
$routes->post('/rekonsiliasi_obat/save', 'RekonsiliasiObat::save');
$routes->post('/rekonsiliasi_obat/delete', 'RekonsiliasiObat::delete');
$routes->get('/rekonsiliasi_obat/loadEdukasi', 'RekonsiliasiObat::loadEdukasi');
$routes->post('/rekonsiliasi_obat/saveEdukasi', 'RekonsiliasiObat::saveEdukasi');

//UPLOAD BERKAS MANUAL
$routes->get('/upload_berkas/get_klasifikasi', 'UploadBerkas::loadKlasifikasi');
$routes->post('/upload_berkas/upload_berkas_rajal', 'UploadBerkas::uploadBerkasRajal');
$routes->get('/upload_berkas/load_dokumen_upload', 'UploadBerkas::loadDokumenUpload');
$routes->post('/upload_berkas/hapus_berkas_rajal', 'UploadBerkas::hapusBerkasRajal');
$routes->post('/upload_berkas/simpan_template_expertise', 'UploadBerkas::simpanTemplateExpertise');
$routes->get('/upload_berkas/load_data_template', 'UploadBerkas::loadDataTemplate');

//INTEGRASI INFO PASIEN
$routes->get('/info_pasien/loadTtv', 'InfoPasien::loadTtv');
$routes->get('/info_pasien/loadAlergi', 'InfoPasien::loadAlergi');
$routes->get('/info_pasien/loadNyeri', 'InfoPasien::loadNyeri');
$routes->get('/info_pasien/loadEWS', 'InfoPasien::loadEws');
$routes->get('/info_pasien/loadJatuh', 'InfoPasien::loadJatuh');
$routes->get('/info_pasien/loadGizi', 'InfoPasien::loadGizi');
//TEMPLATE PEMERIKSAAN FISIK
$routes->get('/info_pasien/loadFisikTemplate', 'InfoPasien::loadFisikTemplate');
//EWS
$routes->get('/ews_kategori/master', 'RawatDarurat\EwsObstetric::master');
$routes->post('/ews_obstetric/save', 'RawatDarurat\EwsObstetric::save');
$routes->get('/ews_obstetric/load', 'RawatDarurat\EwsObstetric::load');
$routes->get('/ews_obstetric/loadGrafik', 'RawatDarurat\EwsObstetric::loadGrafik');
$routes->get('/ews_obstetric/load_ews', 'RawatDarurat\EwsObstetric::load_ews');
$routes->get('/ews_obstetric/load_tabel', 'RawatDarurat\EwsObstetric::load_tabel');
$routes->post('/ews_obstetric/hapus_ews', 'RawatDarurat\EwsObstetric::hapus_ews');
$routes->post('/ews_dewasa/save', 'RawatDarurat\EwsDewasa::save');
$routes->get('/ews_dewasa/load', 'RawatDarurat\EwsDewasa::load');
$routes->get('/ews_dewasa/loadGrafik', 'RawatDarurat\EwsDewasa::loadGrafik');
$routes->get('/ews_dewasa/load_ews', 'RawatDarurat\EwsDewasa::load_ews');
$routes->get('/ews_dewasa/load_tabel', 'RawatDarurat\EwsDewasa::load_tabel');
$routes->post('/ews_dewasa/hapus_ews', 'RawatDarurat\EwsDewasa::hapus_ews');
$routes->post('/ews_anak/save', 'RawatDarurat\EwsAnak::save');
$routes->get('/ews_anak/load', 'RawatDarurat\EwsAnak::load');
$routes->get('/ews_anak/loadGrafik', 'RawatDarurat\EwsAnak::loadGrafik');
$routes->get('/ews_anak/load_ews', 'RawatDarurat\EwsAnak::load_ews');
$routes->get('/ews_anak/load_tabel', 'RawatDarurat\EwsAnak::load_tabel');
$routes->post('/ews_anak/hapus_ews', 'RawatDarurat\EwsAnak::hapus_ews');

//PEMANTAUAN TRANSFUSI
$routes->post('/rj_pemantauan_transfusi/save', 'RawatJalan\PemantauanTransfusi::save');
$routes->get('/rj_pemantauan_transfusi/load', 'RawatJalan\PemantauanTransfusi::load');
$routes->post('/rj_pemantauan_transfusi/delete', 'RawatJalan\PemantauanTransfusi::delete');
$routes->get('/rj_pemantauan_transfusi/loadlabu', 'RawatJalan\PemantauanTransfusi::loadlabu');

//SKRINING TB - RWJ
$routes->post('/rj_skrining_tb/save', 'RawatJalan\SkriningTb::save');
$routes->get('/rj_skrining_tb/load', 'RawatJalan\SkriningTb::load');

//SKRINING RAJAL
$routes->post('/rj_skrining_rajal/save', 'RawatJalan\SkriningRajal::save');
$routes->get('/rj_skrining_rajal/load', 'RawatJalan\SkriningRajal::load');

//cpo
$routes->get('/cpo/load', 'RawatInap\Cpo::load');

//calculator
$routes->get('/calculator/load', 'RawatInap\Calculator::load');

//tht
$routes->get('/rj_pemeriksaan_tht/load', 'RawatJalan\Tht::load');
$routes->post('/rj_pemeriksaan_tht/save', 'RawatJalan\Tht::save');
$routes->post('/rj_pemeriksaan_tht/print', 'RawatJalan\Tht::print');
//umum
$routes->get('/rj_pemeriksaan_umum/load', 'RawatJalan\Umum::load');
$routes->post('/rj_pemeriksaan_umum/save', 'RawatJalan\Umum::save');
$routes->post('/rj_pemeriksaan_umum/print', 'RawatJalan\Umum::print');

//checklist pre ops
$routes->get('/checklist_persiapan_operasi/load', 'RawatInap\ChecklistPreOps::load');
$routes->post('/checklist_persiapan_operasi/save', 'RawatInap\ChecklistPreOps::save');
//perawat intra ops
$routes->get('/catatan_perawat_intra_ops/load', 'RawatInap\CatatanPerawatIntraOps::load');
$routes->post('/catatan_perawat_intra_ops/save', 'RawatInap\CatatanPerawatIntraOps::save');
//perawat post ops
$routes->get('/catatan_perawat_post_ops/load', 'RawatInap\CatatanPerawatPostOps::load');
$routes->post('/catatan_perawat_post_ops/save', 'RawatInap\CatatanPerawatPostOps::save');

//reservasi ok - NEW
$routes->get('/reservasi_ok/load', 'ReservasiOk::load');
$routes->post('/reservasi_ok/save', 'ReservasiOk::save');
$routes->get('/reservasi_ok/loadKelasPerawatanOk', 'ReservasiOk::loadKelasPerawatanOk');
$routes->get('/reservasi_ok/loadStatusOk', 'ReservasiOk::loadStatusOk');
$routes->get('/reservasi_ok/loadJenisOk', 'ReservasiOk::loadJenisOk');
$routes->get('/reservasi_ok/loadKamarOk', 'ReservasiOk::loadKamarOk');
$routes->get('/reservasi_ok/loadJadwal', 'ReservasiOk::loadJadwal');
$routes->get('/reservasi_ok/loadDokterOk', 'ReservasiOk::loadDokterOk');
$routes->get('/reservasi_ok/loadJenisAnestesiOk', 'ReservasiOk::loadJenisAnestesiOk');
$routes->get('/reservasi_ok/loadDetailAnestesiOk', 'ReservasiOk::loadDetailAnestesiOk');
$routes->get('/reservasi_ok/loadAsaOk', 'ReservasiOk::loadAsaOk');
$routes->get('/reservasi_ok/loadTindakanOk', 'ReservasiOk::loadTindakanOk');
$routes->get('/reservasi_ok/cekOkKirim', 'ReservasiOk::cekOkKirim');
$routes->post('/reservasi_ok/addItem', 'ReservasiOk::addItem');
$routes->get('/reservasi_ok/loadHistoryOk', 'ReservasiOk::loadHistoryOk');
$routes->get('/reservasi_ok/loadTabelTindakanOk', 'ReservasiOk::loadTabelTindakanOk');
$routes->post('/reservasi_ok/hapusOrderOk', 'ReservasiOk::hapusOrderOk');
$routes->get('/reservasi_ok/cekOkTerlayani', 'ReservasiOk::cekOkTerlayani');
$routes->post('/reservasi_ok/batalOrderOk', 'ReservasiOk::batalOrderOk');
$routes->post('/reservasi_ok/deleteItem', 'ReservasiOk::deleteItem');

//reservasi OK - MAGETAN - GA DIPAKEK
$routes->post('/ok/addItem', 'Ok::addItem');
$routes->post('/ok/deleteItem', 'Ok::deleteItem');
$routes->post('/ok/batalOrder', 'Ok::batalOrder');
$routes->post('/ok/kirimOrder', 'Ok::kirimOrder');
$routes->get('/ok/loadhistory', 'Ok::loadDataHistory');
$routes->get('/ok/loadhistory_item', 'Ok::loadDataHistory_item');
$routes->get('/ok/master/statusOperasi', 'Ok::statusOperasi');
$routes->get('/ok/master/detailAnastesi', 'Ok::detailAnastesi');
$routes->get('/ok/master/asa', 'Ok::asa');
$routes->get('/ok/master/dokter', 'Ok::dokter');
$routes->get('/ok/master/icdx', 'Ok::icdx');
$routes->post('/ok/master/tindakan', 'Ok::tindakan');

//cetak resume igd
$routes->post('/resume_igd/cetak', 'RawatDarurat\CetakResumeIgd::print');

//PA - PATOLOGI ANATOMI
$routes->post('/pa/save', 'Pa::save');
$routes->post('/pa/batalorder', 'Pa::batalOrder');
$routes->get('/pa/load', 'Pa::load');
$routes->post('/pa/saveorder', 'Pa::saveOrder');
$routes->get('/pa/loadhistory', 'Pa::loadDataHistory');
$routes->get('/pa/loadhistory_item', 'Pa::loadDataHistory_item');
$routes->get('/pa/loadpemeriksaan', 'Pa::loadItemPemeriksaan');

//cekSpMata
$routes->get('/user/cekSpMata', 'User::cekSpMata');

//anastesi
$routes->get('/rj_pemeriksaan_anastesi/load', 'RawatJalan\Anastesi::load');
$routes->post('/rj_pemeriksaan_anastesi/save', 'RawatJalan\Anastesi::save');
$routes->post('/rj_pemeriksaan_anastesi/print', 'RawatJalan\Anastesi::print');

//apache II
$routes->post('/apache_ii/save', 'RawatInap\Apacheii::save');
$routes->get('/apache_ii/load', 'RawatInap\Apacheii::load');
$routes->get('/apache_ii/loadGrafik', 'RawatInap\Apacheii::loadGrafik');
$routes->get('/apache_ii/loadMaster', 'RawatInap\Apacheii::loadMaster');
$routes->get('/apache_ii/loadTabel', 'RawatInap\Apacheii::loadTabel');
$routes->post('/apache_ii/hapusApache', 'RawatInap\Apacheii::hapusApache');

//CPIS
$routes->post('/cpis/save', 'RawatInap\Cpis::save');
$routes->get('/cpis/load', 'RawatInap\Cpis::load');
$routes->get('/cpis/loadGrafik', 'RawatInap\Cpis::loadGrafik');
$routes->get('/cpis/loadMaster', 'RawatInap\Cpis::loadMaster');
$routes->get('/cpis/loadTabel', 'RawatInap\Cpis::loadTabel');
$routes->post('/cpis/hapusCpis', 'RawatInap\Cpis::hapusCpis');

//PEMANTAUAN TRANSFUSI
$routes->post('/ri_pemakaian_alat_bantu_napas/save', 'RawatInap\PemakaianAlatBantuNapas::save');
$routes->get('/ri_pemakaian_alat_bantu_napas/load', 'RawatInap\PemakaianAlatBantuNapas::load');
$routes->post('/ri_pemakaian_alat_bantu_napas/delete', 'RawatInap\PemakaianAlatBantuNapas::delete');
$routes->post('/ri_pemakaian_alat_bantu_napas/print', 'RawatInap\PemakaianAlatBantuNapas::print');

//LAPORAN PCI
$routes->post('/ri_laporan_pci/save', 'RawatInap\LaporanPCI::save');
$routes->get('/ri_laporan_pci/load', 'RawatInap\LaporanPCI::load');
$routes->post('/ri_laporan_pci/print', 'RawatInap\LaporanPCI::print');

//LAPORAN CAG
$routes->post('/ri_laporan_cag/save', 'RawatInap\LaporanCAG::save');
$routes->get('/ri_laporan_cag/load', 'RawatInap\LaporanCAG::load');
$routes->post('/ri_laporan_cag/print', 'RawatInap\LaporanCAG::print');

//baru - rehab
$routes->post('/rj_rehab_pemeriksaan_rehab/cetak', 'RawatJalan\PengkajianRehab::printRehabNew');
$routes->post('/rj_pengkajian_fisioterapi/save', 'RawatJalan\PengkajianRehab::saveFisio');
$routes->get('/rj_pengkajian_fisioterapi/load', 'RawatJalan\PengkajianRehab::loadFisio');
$routes->post('/rj_pengkajian_fisioterapi/print', 'RawatJalan\PengkajianRehab::printFisio');
$routes->post('/rj_pengkajian_psikologi/save', 'RawatJalan\PengkajianRehab::savePsikologi');
$routes->get('/rj_pengkajian_psikologi/load', 'RawatJalan\PengkajianRehab::loadPsikologi');
$routes->post('/rj_pengkajian_psikologi/print', 'RawatJalan\PengkajianRehab::printPsikologi');

//LOAD DATA PASIEN
$routes->get('/info_pasien/loadDataPasien', 'InfoPasien::loadDataPasien');

//rehab - rencana terapi
$routes->post('/rj_rehab_rencana_terapi/save', 'RawatJalan\PengkajianRehab::saveRencanaTerapi');
$routes->get('/rj_rehab_rencana_terapi/load', 'RawatJalan\PengkajianRehab::loadRencanaTerapi');
$routes->post('/rj_rehab_rencana_terapi/print', 'RawatJalan\PengkajianRehab::printRencanaTerapi');

//rehab - terapi
$routes->post('/rj_rehab_terapi/print', 'RawatJalan\PengkajianRehab::printRehabTerapi');

//riwayat kunjungan - inap
$routes->get('/antrian/rawat_inap/riwayat', 'RawatInap\Antrian::index');

//data kontrol inap
$routes->get('/ri_cara_keluar/loadDataKontrol', 'RawatInap\CaraKeluar::loadDataKontrol');



//tindakan_ecce
$routes->post('/ri_tindakan_ecce/save', 'RawatInap\TindakanEcce::save');
$routes->get('/ri_tindakan_ecce/load', 'RawatInap\TindakanEcce::load');
$routes->post('/ri_tindakan_ecce/print', 'RawatInap\TindakanEcce::print');

//tindakan_ecce
$routes->post('/ri_catatan_kamar_pemulihan/save', 'RawatInap\CatatanKamarPemulihan::save');
$routes->get('/ri_catatan_kamar_pemulihan/load', 'RawatInap\CatatanKamarPemulihan::load');
$routes->post('/ri_catatan_kamar_pemulihan/print', 'RawatInap\CatatanKamarPemulihan::print');
$routes->get('/ri_catatan_kamar_pemulihan/loadTableTtv', 'RawatInap\CatatanKamarPemulihan::loadTableTtv');
$routes->post('/ri_catatan_kamar_pemulihan/saveGrafik', 'RawatInap\CatatanKamarPemulihan::saveGrafik');

//surgical safety - ceklist keselamatan pasien ok
$routes->post('/ri_surgical_safety/save', 'RawatInap\SurgicalSafety::save');
$routes->get('/ri_surgical_safety/load', 'RawatInap\SurgicalSafety::load');
$routes->post('/ri_surgical_safety/print', 'RawatInap\SurgicalSafety::print');

//catatan anestesi
$routes->post('/ri_catatan_anestesi/save', 'RawatInap\CatatanAnestesi::save');
$routes->get('/ri_catatan_anestesi/load', 'RawatInap\CatatanAnestesi::load');
$routes->post('/ri_catatan_anestesi/print', 'RawatInap\CatatanAnestesi::print');

//billing - delegasi
$routes->post('/billing/delegasi', 'Billing::delegasi');

//cetak skrining tb
$routes->post('/rj_skrining_tb/print', 'RawatJalan\SkriningTb::print');

//cetak serah terima pasien
$routes->post('/ri_serah_terima/print', 'RawatInap\SerahTerima::print');

//cetak pemantauan transfusi
$routes->post('/rj_pemantauan_transfusi/print', 'RawatJalan\PemantauanTransfusi::print');

//BHP
$routes->get('/bhp/loadUnitOrder', 'Bhp::loadUnitOrder');
$routes->get('/bhp/loadMaster', 'Bhp::loadMaster');
$routes->post('/bhp/save', 'Bhp::save');
$routes->get('/bhp/load', 'Bhp::load');
$routes->post('/bhp/update', 'Bhp::update');
$routes->post('/bhp/delete', 'Bhp::delete');

//GIZI - PENUNJANG
$routes->get('/gizi/loadJenisMakan', 'Gizi::loadJenisMakan');
$routes->get('/gizi/loadJadwalMakan', 'Gizi::loadJadwalMakan');
$routes->post('/gizi/save', 'Gizi::save');
$routes->get('/gizi/load', 'Gizi::load');
$routes->post('/gizi/delete', 'Gizi::delete');
$routes->get('/gizi/loadMasterDiit', 'Gizi::loadMasterDiit');
$routes->post('/gizi/saveDiit', 'Gizi::saveDiit');
$routes->get('/gizi/loadDiit', 'Gizi::loadDiit');
$routes->post('/gizi/deleteDiit', 'Gizi::deleteDiit');
$routes->post('/gizi/hapusOrderGizi', 'Gizi::hapusOrderGizi');

//PENGKAJIAN AWAL PERAWAT INAP - INPUT PERI
$routes->get('/ri_pengkajian_awal_keperawatan/loadPeri', 'RawatInap\PengkajianAwalKeperawatan::loadPeri');
$routes->post('/ri_pengkajian_awal_keperawatan/savePeri', 'RawatInap\PengkajianAwalKeperawatan::savePeri');
$routes->get('/ri_pengkajian_awal_keperawatan/loadTempatLahirPeri', 'RawatInap\PengkajianAwalKeperawatan::loadTempatLahirPeri');
$routes->get('/ri_pengkajian_awal_keperawatan/loadKeadaanLahirPeri', 'RawatInap\PengkajianAwalKeperawatan::loadKeadaanLahirPeri');
$routes->get('/ri_pengkajian_awal_keperawatan/loadSebabMatiPeri', 'RawatInap\PengkajianAwalKeperawatan::loadSebabMatiPeri');
$routes->get('/ri_pengkajian_awal_keperawatan/loadLahirKembarPeri', 'RawatInap\PengkajianAwalKeperawatan::loadLahirKembarPeri');

//ringkasan masuk & pulang pasien
$routes->post('/ri_ringkasan_masuk_pulang/save', 'RawatInap\RingkasanMasukKeluar::save');
$routes->get('/ri_ringkasan_masuk_pulang/load', 'RawatInap\RingkasanMasukKeluar::load');

//get change unit
$routes->get('/user/getChangeUnit', 'User::getChangeUnit');

//BANK DARAH
$routes->post('/bank_darah/save', 'BankDarah::save');
$routes->get('/bank_darah/load', 'BankDarah::load');
$routes->get('/bank_darah/loadHistory', 'BankDarah::loadHistory');
$routes->post('/bank_darah/kirimOrder', 'BankDarah::kirimOrder');
$routes->post('/bank_darah/batalOrder', 'BankDarah::batalOrder');
$routes->get('/bank_darah/loadKomponenDarah', 'BankDarah::loadKomponenDarah');
$routes->post('/bank_darah/delete', 'BankDarah::delete');
$routes->post('/bank_darah/hapusOrder', 'BankDarah::hapusOrder');
$routes->get('/bank_darah/loadDiagnosaPrimer', 'BankDarah::loadDiagnosaPrimer');

//PENGKAJIAN AWAL PERAWAT INAP - INPUT DATA PERSALINAN
$routes->get('/ri_pengkajian_awal_keperawatan/loadPers', 'RawatInap\PengkajianAwalKeperawatan::loadPers');
$routes->post('/ri_pengkajian_awal_keperawatan/savePers', 'RawatInap\PengkajianAwalKeperawatan::savePers');
$routes->get('/ri_pengkajian_awal_keperawatan/loadCaraLahir', 'RawatInap\PengkajianAwalKeperawatan::loadCaraLahir');
$routes->get('/ri_pengkajian_awal_keperawatan/loadBantuLahir', 'RawatInap\PengkajianAwalKeperawatan::loadBantuLahir');

//BILLING TAMBAHAN
$routes->get('/billing/cekDokterKosong', 'Billing::cekDokterKosong');

//Prosedur Kemoterapi
$routes->get('/rj_prosedur_kemoterapi/loadRegimen', 'RawatJalan\ProsedurKemoterapi::loadRegimen');
$routes->get('/rj_prosedur_kemoterapi/loadObatRegimen', 'RawatJalan\ProsedurKemoterapi::loadObatRegimen');
$routes->post('/rj_prosedur_kemoterapi/save', 'RawatJalan\ProsedurKemoterapi::save');
$routes->get('/rj_prosedur_kemoterapi/load', 'RawatJalan\ProsedurKemoterapi::load');
$routes->post('/rj_prosedur_kemoterapi/delete', 'RawatJalan\ProsedurKemoterapi::deleteData');
$routes->post('/rj_prosedur_kemoterapi/delete_obat', 'RawatJalan\ProsedurKemoterapi::deleteObat');
$routes->post('/rj_prosedur_kemoterapi/save_obat', 'RawatJalan\ProsedurKemoterapi::saveObat');
$routes->get('/rj_prosedur_kemoterapi/load_obat', 'RawatJalan\ProsedurKemoterapi::loadObat');
$routes->post('/rj_prosedur_kemoterapi/edit_obat', 'RawatJalan\ProsedurKemoterapi::editObat');
$routes->get('/rj_prosedur_kemoterapi/load_history', 'RawatJalan\ProsedurKemoterapi::loadHistory');
$routes->post('/rj_prosedur_kemoterapi/edit_history', 'RawatJalan\ProsedurKemoterapi::editHistory');
$routes->post('/rj_prosedur_kemoterapi/save_obatsitostatika', 'RawatJalan\ProsedurKemoterapi::saveObatSitostatika');
$routes->get('/rj_prosedur_kemoterapi/load_obatsitostatika', 'RawatJalan\ProsedurKemoterapi::loadObatSitostatika');
$routes->post('/rj_prosedur_kemoterapi/edit_obatsitostatika', 'RawatJalan\ProsedurKemoterapi::editObatSitostatika');
$routes->post('/rj_prosedur_kemoterapi/delete_obatsitostatika', 'RawatJalan\ProsedurKemoterapi::deleteObatSitostatika');
$routes->post('/rj_prosedur_kemoterapi/print', 'RawatJalan\ProsedurKemoterapi::print');

//FORENSIK / JENAZAH
$routes->post('/forensik/save', 'Forensik::save');
$routes->get('/forensik/load', 'Forensik::load');
$routes->get('/forensik/loadHistory', 'Forensik::loadHistory');
$routes->post('/forensik/kirimOrder', 'Forensik::kirimOrder');
$routes->post('/forensik/batalOrder', 'Forensik::batalOrder');
$routes->get('/forensik/loadTindakan', 'Forensik::loadTindakan');
$routes->post('/forensik/delete', 'Forensik::delete');
$routes->post('/forensik/hapusOrder', 'Forensik::hapusOrder');

//reservasi ok - NEW -> UNTUK RAJAL
$routes->post('/reservasi_ok/saveRsv', 'ReservasiOk::saveRsv');
$routes->get('/reservasi_ok/loadTindakanRsv', 'ReservasiOk::loadTindakanRsv');
$routes->get('/reservasi_ok/loadHistoryRsv', 'ReservasiOk::loadHistoryRsv');
$routes->post('/reservasi_ok/deleteRsv', 'ReservasiOk::deleteRsv');
$routes->post('/reservasi_ok/tutupRsv', 'ReservasiOk::tutupRsv');
$routes->post('/reservasi_ok/bukaRsv', 'ReservasiOk::bukaRsv');
$routes->post('/reservasi_ok/hapusRsv', 'ReservasiOk::hapusRsv');

//CMPP
$routes->post('/ri_cmpp/save', 'RawatInap\Cmpp::save');
$routes->get('/ri_cmpp/load', 'RawatInap\Cmpp::load');
$routes->get('/ri_cmpp/loadDirujuk', 'RawatInap\Cmpp::loadDirujuk');
$routes->get('/ri_cmpp/loadIdentifikasi', 'RawatInap\Cmpp::loadIdentifikasi');
$routes->get('/ri_cmpp/loadAssesmen', 'RawatInap\Cmpp::loadAssesmen');
$routes->get('/ri_cmpp/loadMasalah', 'RawatInap\Cmpp::loadMasalah');
$routes->get('/ri_cmpp/loadHasil', 'RawatInap\Cmpp::loadHasil');
$routes->post('/ri_cmpp/savePerencanaan', 'RawatInap\Cmpp::savePerencanaan');
$routes->get('/ri_cmpp/loadPerencanaan', 'RawatInap\Cmpp::loadPerencanaan');
$routes->post('/ri_cmpp/deletePerencanaan', 'RawatInap\Cmpp::deletePerencanaan');
$routes->post('/ri_cmpp/saveImplementasi', 'RawatInap\Cmpp::saveImplementasi');
$routes->get('/ri_cmpp/loadImplementasi', 'RawatInap\Cmpp::loadImplementasi');
$routes->post('/ri_cmpp/deleteImplementasi', 'RawatInap\Cmpp::deleteImplementasi');
$routes->post('/ri_cmpp/saveMonitoring', 'RawatInap\Cmpp::saveMonitoring');
$routes->get('/ri_cmpp/loadMonitoring', 'RawatInap\Cmpp::loadMonitoring');
$routes->post('/ri_cmpp/deleteMonitoring', 'RawatInap\Cmpp::deleteMonitoring');
$routes->post('/ri_cmpp/print', 'RawatInap\Cmpp::print');

//PINDAH RUANG / MUTASI
$routes->post('/pindah_ruang/save', 'PindahRuang::save');
$routes->get('/pindah_ruang/load', 'PindahRuang::load');
$routes->get('/pindah_ruang/loadKunjungan', 'PindahRuang::loadKunjungan');
$routes->get('/pindah_ruang/loadUnit', 'PindahRuang::loadUnit');
$routes->get('/pindah_ruang/loadSpesialisasi', 'PindahRuang::loadSpesialisasi');
$routes->get('/pindah_ruang/loadKelas', 'PindahRuang::loadKelas');
$routes->get('/pindah_ruang/loadRuang', 'PindahRuang::loadRuang');
$routes->get('/pindah_ruang/loadKamar', 'PindahRuang::loadKamar');
$routes->get('/pindah_ruang/loadPenjamin', 'PindahRuang::loadPenjamin');
$routes->get('/pindah_ruang/cekRuangTerakhir', 'PindahRuang::cekRuangTerakhir');

//antrian penunjang
$routes->get('/antrian/penunjang/belum_terlayani', 'Penunjang\Antrian::index');
$routes->get('/antrian/penunjang/terlayani', 'Penunjang\Antrian::index');
$routes->get('/antrian/penunjang/riwayat', 'Penunjang\Antrian::index');

//cek hasil kirim
$routes->get('/laboratorium/cek_hasil_kirim', 'Laboratorium::cekHasilKirim');

//history laporan bedah
$routes->get('/laporan_pembedahan/loadHistory', 'RawatInap\LaporanPembedahan::loadHistory');
$routes->get('/laporan_pembedahan/loadImplan', 'RawatInap\LaporanPembedahan::loadImplan');

//---ROUTES RESUME MEDIS KLAIM---
$routes->post('/resume_klaim/print', 'CetakResumeKlaim::print');

//template laporan bedah
$routes->get('/laporan_pembedahan/loadTemplate', 'RawatInap\LaporanPembedahan::loadTemplate');
$routes->post('/laporan_pembedahan/saveTemplate', 'RawatInap\LaporanPembedahan::saveTemplate');
$routes->post('/laporan_pembedahan/deleteTemplate', 'RawatInap\LaporanPembedahan::deleteTemplate');

//history surgical safety
$routes->get('/ri_surgical_safety/loadHistory', 'RawatInap\SurgicalSafety::loadHistory');

//jumlah pasien
$routes->get('/antrian/rawat_inap/loadJumlahPasien', 'RawatInap\Antrian::loadJumlahPasien');

//history ass pra bedah
$routes->get('/ass_pra_bedah/loadHistory', 'RawatInap\AssPraBedah::loadHistory');

//history ass anestesi sedasi
$routes->get('/ass_anestesi_sedasi/loadHistory', 'RawatInap\AssAnastesiSedasi::loadHistory');

//history checklist pre ops
$routes->get('/checklist_persiapan_operasi/loadHistory', 'RawatInap\ChecklistPreOps::loadHistory');

//history catatan perawat intra ops
$routes->get('/catatan_perawat_intra_ops/loadHistory', 'RawatInap\CatatanPerawatIntraOps::loadHistory');

//history catatan perawat post ops
$routes->get('/catatan_perawat_post_ops/loadHistory', 'RawatInap\CatatanPerawatPostOps::loadHistory');

//history tindakan anestesi
$routes->get('/tindakan_anastesi/loadHistory', 'RawatInap\TindakanAnastesi::loadHistory');

//history tindakan anestesi
$routes->get('/ri_catatan_anestesi/loadHistory', 'RawatInap\CatatanAnestesi::loadHistory');

//history catatan sedasi
$routes->get('/catatan_sedasi/loadHistory', 'RawatInap\CatatanSedasi::loadHistory');

//history tindakan ecce
$routes->get('/ri_tindakan_ecce/loadHistory', 'RawatInap\TindakanEcce::loadHistory');

//history catatan kamar pemulihan
$routes->get('/ri_catatan_kamar_pemulihan/loadHistory', 'RawatInap\CatatanKamarPemulihan::loadHistory');

//history cetak suket kematian
$routes->post('/cara_pulang/cetak_surat_kematian', 'CetakSuratKematian::print');

//hasil forensik klinik
$routes->post('/ri_hasil_forensik_klinik/save', 'RawatInap\HasilForensikKlinik::save');
$routes->get('/ri_hasil_forensik_klinik/load', 'RawatInap\HasilForensikKlinik::load');
$routes->post('/ri_hasil_forensik_klinik/print', 'RawatInap\HasilForensikKlinik::print');
$routes->get('/ri_hasil_forensik_klinik/loadKunjungan', 'RawatInap\HasilForensikKlinik::loadKunjungan');

//delegasi bawah
$routes->get('/antrian/rawat_inap/delegasi', 'RawatInap\Antrian::index');
$routes->get('/perencanaan_konsul_inap/loadDelegasiBawah', 'RawatInap\PerencanaanKonsulInap::loadDelegasiBawah');

//cek tgl keluar akhir
$routes->get('/ri_cara_keluar/cekTglKeluarAkhir', 'RawatInap\CaraKeluar::cekTglKeluarAkhir');
$routes->post('/ri_cara_keluar/batalKeluar', 'RawatInap\CaraKeluar::batalKeluar');

//surat kematian inap
$routes->get('/ri_cara_keluar/loadKeteranganKematian', 'RawatInap\CaraKeluar::loadKeteranganKematian');
$routes->post('/ri_cara_keluar/saveKeteranganKematian', 'RawatInap\CaraKeluar::saveKeteranganKematian');
$routes->post('/ri_cara_keluar/printKeteranganKematian', 'RawatInap\CaraKeluar::printKeteranganKematian');

//cetak kontrol umum
$routes->post('/rj_cara_keluar/printKontrolUmum', 'RawatJalan\CaraKeluar::printKontrolUmum');

//cetak konsul inap riwayat
$routes->post('/perencanaan_konsul_inap/cetakKonsulInapRiwayat', 'CetakLembarKonsultasi::cetakKonsulInapRiwayat');

//cetak konsul rajal riwayat
$routes->post('/rj_perencanaan_konsul/cetakKonsulRjRiwayat', 'CetakLembarKonsultasi::cetakKonsulRjRiwayat');

//cetak serah terima load awal
$routes->get('/ri_serah_terima/loadAwal', 'RawatInap\SerahTerima::loadAwal');

//ganti password user
$routes->post('/user/changePassword', 'User::changePassword');

//cek ct scan
$routes->get('/radiologi/cekCtScan', 'Radiologi::cekCtScan');

//resep view obat
$routes->get('/resep/perbandinganhistoryresepdokter', 'Resep::perbandinganHistoryResepDokter');
$routes->get('/resep/perbandinganhistoryresepterlayani', 'Resep::perbandinganHistoryResepTerlayani');

//get instalasi
$routes->get('/user/getInstalasi', 'User::getInstalasi');

//ANTRIAN REHAB MEDIK
$routes->get('/antrian/rehab/belum_terlayani', 'RawatJalan\AntrianRehab::index');
$routes->get('/antrian/rehab/terlayani', 'RawatJalan\AntrianRehab::index');
$routes->get('/antrian/rehab/konsul', 'RawatJalan\AntrianRehab::index');
$routes->get('/antrian/rehab/terlayani/menunggu/ruang', 'RawatJalan\AntrianRehab::index');
$routes->get('/antrian/rehab/iskontrol', 'RawatJalan\AntrianRehab::iskontrol');
$routes->post('/antrian/rehab/panggil', 'RawatJalan\AntrianRehab::panggilAntrian');
$routes->get('/antrian/rehab/riwayat', 'RawatJalan\AntrianRehab::index');
$routes->get('/antrian/rehab/berkelanjutan', 'RawatJalan\AntrianRehab::index');
$routes->get('/antrian/rehab/getPlavonBPJS', 'RawatJalan\AntrianRehab::getPlavonBPJS');

//rehab medik - order
$routes->post('/rehab/save', 'Rehab::save');
$routes->post('/rehab/batalorder', 'Rehab::batalOrder');
$routes->get('/rehab/load', 'Rehab::load');
$routes->post('/rehab/saveorder', 'Rehab::saveOrder');
$routes->get('/rehab/loadhistory', 'Rehab::loadDataHistory');
$routes->get('/rehab/loadhistory_item', 'Rehab::loadDataHistory_item');
$routes->get('/rehab/loadpemeriksaan', 'Rehab::loadItemPemeriksaan');
$routes->get('/rehab/loadDokterRehab', 'Rehab::loadDokterRehab');
$routes->post('/rehab/orderDilayani', 'Rehab::orderDilayani');

//vk
$routes->post('/vk/save', 'Vk::save');
$routes->post('/vk/batalorder', 'Vk::batalOrder');
$routes->get('/vk/load', 'Vk::load');
$routes->post('/vk/saveorder', 'Vk::saveOrder');
$routes->get('/vk/loadhistory', 'Vk::loadDataHistory');
$routes->get('/vk/loadhistory_item', 'Vk::loadDataHistory_item');
$routes->get('/vk/loadpemeriksaan', 'Vk::loadItemPemeriksaan');
$routes->get('/vk/loadDokterVk', 'Vk::loadDokterVk');
$routes->post('/vk/orderDilayani', 'Vk::orderDilayani');

//rehab jadwal terapi
$routes->get('/rj_rehab_rencana_terapi/loadJadwalTerapi', 'RawatJalan\PengkajianRehab::loadJadwalTerapi');
$routes->post('/rj_rehab_rencana_terapi/deleteJadwalTerapi', 'RawatJalan\PengkajianRehab::deleteJadwalTerapi');
//baru - rehab
$routes->get('/rj_pengkajian_okupasi_terapi/load', 'RawatJalan\PengkajianRehab::loadOkupasiTerapi');
$routes->post('/rj_pengkajian_okupasi_terapi/print', 'RawatJalan\PengkajianRehab::printOkupasiTerapi');
$routes->get('/rj_pengkajian_terapi_wicara/load', 'RawatJalan\PengkajianRehab::loadTerapiWicara');
$routes->post('/rj_pengkajian_terapi_wicara/print', 'RawatJalan\PengkajianRehab::printTerapiWicara');

//antrian delegasi spesialis
$routes->get('/antrian/rawat_inap/delegasi_spesialis', 'RawatInap\Antrian::index');

//EWS CETAKAN
$routes->post('/ews_dewasa/print', 'RawatDarurat\EwsDewasa::print');
$routes->post('/ews_anak/print', 'RawatDarurat\EwsAnak::print');
$routes->post('/ews_obstetric/print', 'RawatDarurat\EwsObstetric::print');

//ews neonatal
$routes->post('/ews_neonatal/save', 'RawatDarurat\EwsNeonatal::save');
$routes->get('/ews_neonatal/load', 'RawatDarurat\EwsNeonatal::load');
$routes->post('/ews_neonatal/print', 'RawatDarurat\EwsNeonatal::print');
$routes->get('/ews_neonatal/loadGrafik', 'RawatDarurat\EwsNeonatal::loadGrafik');
$routes->get('/ews_neonatal/load_ews', 'RawatDarurat\EwsNeonatal::load_ews');
$routes->get('/ews_neonatal/load_tabel', 'RawatDarurat\EwsNeonatal::load_tabel');
$routes->post('/ews_neonatal/hapus_ews', 'RawatDarurat\EwsNeonatal::hapus_ews');

//tab konsul igd
$routes->get('/antrian/rawat_darurat/konsul', 'RawatDarurat\Antrian::index');

//konsul igd
$routes->post('/igd_perencanaan_konsul/save', 'RawatDarurat\PerencanaanKonsul::save');
$routes->get('/igd_perencanaan_konsul/load', 'RawatDarurat\PerencanaanKonsul::load');
$routes->get('/igd_perencanaan_konsul/loadhistory', 'RawatDarurat\PerencanaanKonsul::loadHistory');
$routes->post('/igd_perencanaan_konsul/batalkonsul', 'RawatDarurat\PerencanaanKonsul::batalKonsul');
$routes->post('/igd_perencanaan_konsul/jawabkonsul', 'RawatDarurat\PerencanaanKonsul::jawabKonsul');
$routes->get('/igd_perencanaan_konsul/loadparamedis', 'RawatDarurat\PerencanaanKonsul::load_paramedis');
$routes->get('/igd_perencanaan_konsul/loadunit', 'RawatDarurat\KonsulIGD::load_unit');
$routes->post('/igd_perencanaan_konsul/cetak', 'RawatDarurat\PerencanaanKonsul::cetak_konsultasi');

//routes rekam medis baru
$routes->get('/daftar/pasien/riwayat', 'RekamMedis\Antrian::index');
$routes->get('/daftar/pasien/rekap_hasil_verif', 'RekamMedis\Antrian::index');
$routes->post('/verifikasi/cetak/laporan_verifikasi', 'RekamMedis\Verifikasi::cetakLaporanVerifikasi');
$routes->post('/verifikasi/cetak/laporan_kelengkapan', 'RekamMedis\Verifikasi::cetakLaporanKelengkapan');
$routes->post('/verifikasi/cetak/laporan_analis', 'RekamMedis\Verifikasi::cetakLaporanAnalis');
$routes->get('/verifikasi/cetak/laporan_analis/loadRuang', 'RekamMedis\Verifikasi::loadRuang');
$routes->post('/laporan_analisa_indikator_mutu/cetak', 'RekamMedis\LaporanAnalisaIndikatorMutu::cetak');
$routes->get('/laporan_analisa_indikator_mutu/loadRuang', 'RekamMedis\LaporanAnalisaIndikatorMutu::loadRuang');
$routes->post('/laporan_indikator_mutu_prioritas/cetak', 'RekamMedis\LaporanIndikatorMutuPrioritas::cetak');
$routes->get('/laporan_indikator_mutu_prioritas/loadRuang', 'RekamMedis\LaporanIndikatorMutuPrioritas::loadRuang');

//lab hasil kultur
$routes->get('/laboratorium/loadHasilKultur', 'Laboratorium::loadHasilKultur');

//PPI IADP
$routes->post('/ppi_iadp/save', 'NonRs\PpiIadp::save');
$routes->get('/ppi_iadp/load', 'NonRs\PpiIadp::load');
$routes->post('/ppi_iadp/print', 'NonRs\PpiIadp::print');
$routes->get('/ppi_iadp/loadHistory', 'NonRs\PpiIadp::loadHistory');
$routes->get('/ppi_iadp/loadGejala', 'NonRs\PpiIadp::loadGejala');
$routes->get('/ppi_iadp/loadItemPencegahan', 'NonRs\PpiIadp::loadItemPencegahan');
$routes->get('/ppi_iadp/loadJenisCVC', 'NonRs\PpiIadp::loadJenisCVC');
$routes->get('/ppi_iadp/loadLokasi', 'NonRs\PpiIadp::loadLokasi');
$routes->get('/ppi_iadp/loadNomorCVC', 'NonRs\PpiIadp::loadNomorCVC');
$routes->get('/ppi_iadp/loadModelCVC', 'NonRs\PpiIadp::loadModelCVC');
$routes->get('/ppi_iadp/loadObatAntibiotik', 'NonRs\PpiIadp::loadObatAntibiotik');
$routes->post('/ppi_iadp/delete', 'NonRs\PpiIadp::delete');
$routes->get('/ppi_iadp/loadBulan', 'NonRs\PpiIadp::loadBulan');
$routes->get('/ppi_iadp/loadTahun', 'NonRs\PpiIadp::loadTahun');

//PPI ISK
$routes->post('/ppi_isk/save', 'NonRs\PpiIsk::save');
$routes->get('/ppi_isk/load', 'NonRs\PpiIsk::load');
$routes->post('/ppi_isk/print', 'NonRs\PpiIsk::print');
$routes->get('/ppi_isk/loadHistory', 'NonRs\PpiIsk::loadHistory');
$routes->get('/ppi_isk/loadGejala', 'NonRs\PpiIsk::loadGejala');
$routes->get('/ppi_isk/loadItemPencegahan', 'NonRs\PpiIsk::loadItemPencegahan');
$routes->get('/ppi_isk/loadJenisCath', 'NonRs\PpiIsk::loadJenisCath');
$routes->get('/ppi_isk/loadNomorCath', 'NonRs\PpiIsk::loadNomorCath');
$routes->get('/ppi_isk/loadObatAntibiotik', 'NonRs\PpiIsk::loadObatAntibiotik');
$routes->post('/ppi_isk/delete', 'NonRs\PpiIsk::delete');
$routes->get('/ppi_isk/loadBulan', 'NonRs\PpiIsk::loadBulan');
$routes->get('/ppi_isk/loadTahun', 'NonRs\PpiIsk::loadTahun');
$routes->get('/ppi_isk/loadBahanCath', 'NonRs\PpiIsk::loadBahanCath');

//PPI Plebitis
$routes->post('/ppi_plebitis/save', 'NonRs\PpiPlebitis::save');
$routes->get('/ppi_plebitis/load', 'NonRs\PpiPlebitis::load');
$routes->post('/ppi_plebitis/print', 'NonRs\PpiPlebitis::print');
$routes->get('/ppi_plebitis/loadHistory', 'NonRs\PpiPlebitis::loadHistory');
$routes->get('/ppi_plebitis/loadGejala', 'NonRs\PpiPlebitis::loadGejala');
$routes->get('/ppi_plebitis/loadItemPencegahan', 'NonRs\PpiPlebitis::loadItemPencegahan');
$routes->get('/ppi_plebitis/loadLokasi', 'NonRs\PpiPlebitis::loadLokasi');
$routes->get('/ppi_plebitis/loadNomorIVCath', 'NonRs\PpiPlebitis::loadNomorIVCath');
$routes->get('/ppi_plebitis/loadMetodeViksasi', 'NonRs\PpiPlebitis::loadMetodeViksasi');
$routes->get('/ppi_plebitis/loadObatAntibiotik', 'NonRs\PpiPlebitis::loadObatAntibiotik');
$routes->post('/ppi_plebitis/delete', 'NonRs\PpiPlebitis::delete');
$routes->get('/ppi_plebitis/loadBulan', 'NonRs\PpiPlebitis::loadBulan');
$routes->get('/ppi_plebitis/loadTahun', 'NonRs\PpiPlebitis::loadTahun');

//PPI VAP
$routes->get('/ppi_vap/loadSputum', 'NonRs\PpiVap::loadSputum');
$routes->get('/ppi_vap/loadHasilSputum', 'NonRs\PpiVap::loadHasilSputum');
$routes->get('/ppi_vap/loadTemperatur', 'NonRs\PpiVap::loadTemperatur');
$routes->get('/ppi_vap/loadLeukosit', 'NonRs\PpiVap::loadLeukosit');
$routes->get('/ppi_vap/loadItemPencegahan', 'NonRs\PpiVap::loadItemPencegahan');
$routes->get('/ppi_vap/loadGejala', 'NonRs\PpiVap::loadGejala');
$routes->get('/ppi_vap/loadItemVAC', 'NonRs\PpiVap::loadItemVAC');
$routes->get('/ppi_vap/loadLokasi', 'NonRs\PpiVap::loadLokasi');
$routes->get('/ppi_vap/loadJenisETT', 'NonRs\PpiVap::loadJenisETT');
$routes->get('/ppi_vap/loadModelETT', 'NonRs\PpiVap::loadModelETT');
$routes->get('/ppi_vap/loadNomorETT', 'NonRs\PpiVap::loadNomorETT');
$routes->get('/ppi_vap/loadKulturSputum', 'NonRs\PpiVap::loadKulturSputum');
$routes->get('/ppi_vap/loadParamedis', 'NonRs\PpiVap::loadParamedis');
$routes->post('/ppi_vap/save', 'NonRs\PpiVap::save');
$routes->get('/ppi_vap/load', 'NonRs\PpiVap::load');
$routes->post('/ppi_vap/print', 'NonRs\PpiVap::print');
$routes->get('/ppi_vap/loadHistory', 'NonRs\PpiVap::loadHistory');
$routes->post('/ppi_vap/delete', 'NonRs\PpiVap::delete');

//PPI HAP
$routes->get('/ppi_hap/loadSputum', 'NonRs\PpiHap::loadSputum');
$routes->get('/ppi_hap/loadHasilSputum', 'NonRs\PpiHap::loadHasilSputum');
$routes->get('/ppi_hap/loadTemperatur', 'NonRs\PpiHap::loadTemperatur');
$routes->get('/ppi_hap/loadLeukosit', 'NonRs\PpiHap::loadLeukosit');
$routes->get('/ppi_hap/loadItemPencegahan', 'NonRs\PpiHap::loadItemPencegahan');
$routes->get('/ppi_hap/loadGejala', 'NonRs\PpiHap::loadGejala');
$routes->get('/ppi_hap/loadItemVAC', 'NonRs\PpiHap::loadItemVAC');
$routes->post('/ppi_hap/save', 'NonRs\PpiHap::save');
$routes->get('/ppi_hap/load', 'NonRs\PpiHap::load');
$routes->post('/ppi_hap/print', 'NonRs\PpiHap::print');
$routes->get('/ppi_hap/loadHistory', 'NonRs\PpiHap::loadHistory');
$routes->post('/ppi_hap/delete', 'NonRs\PpiHap::delete');

//PPI IDO
$routes->get('/ppi_ido/loadStatusOperasi', 'NonRs\PpiIdo::loadStatusOperasi');
$routes->get('/ppi_ido/loadKualfikasiDokter', 'NonRs\PpiIdo::loadKualfikasiDokter');
$routes->get('/ppi_ido/loadProsedur', 'NonRs\PpiIdo::loadProsedur');
$routes->get('/ppi_ido/loadKamarOperasi', 'NonRs\PpiIdo::loadKamarOperasi');
$routes->get('/ppi_ido/loadASA', 'NonRs\PpiIdo::loadASA');
$routes->get('/ppi_ido/loadDokterOperator', 'NonRs\PpiIdo::loadDokterOperator');
$routes->get('/ppi_ido/loadKlasifikasiLuka', 'NonRs\PpiIdo::loadKlasifikasiLuka');
$routes->get('/ppi_ido/loadPenyakit', 'NonRs\PpiIdo::loadPenyakit');
$routes->get('/ppi_ido/loadPencukuran', 'NonRs\PpiIdo::loadPencukuran');
$routes->get('/ppi_ido/loadAntiseptik', 'NonRs\PpiIdo::loadAntiseptik');
$routes->get('/ppi_ido/loadInfeksi', 'NonRs\PpiIdo::loadInfeksi');
$routes->get('/ppi_ido/loadObat', 'NonRs\PpiIdo::loadObat');
$routes->get('/ppi_ido/loadAntibiotik', 'NonRs\PpiIdo::loadAntibiotik');
$routes->get('/ppi_ido/loadLokasiInfeksi', 'NonRs\PpiIdo::loadLokasiInfeksi');
$routes->get('/ppi_ido/loadSpesifikInfeksi', 'NonRs\PpiIdo::loadSpesifikInfeksi');
$routes->get('/ppi_ido/loadGejala', 'NonRs\PpiIdo::loadGejala');
$routes->get('/ppi_ido/loadDisinfeksiKulit', 'NonRs\PpiIdo::loadDisinfeksiKulit');
$routes->get('/ppi_ido/loadIndikatorInstrumen', 'NonRs\PpiIdo::loadIndikatorInstrumen');
$routes->get('/ppi_ido/loadItemTindakan', 'NonRs\PpiIdo::loadItemTindakan');
$routes->get('/ppi_ido/loadIdentifikasiILO', 'NonRs\PpiIdo::loadIdentifikasiILO');
$routes->post('/ppi_ido/save', 'NonRs\PpiIdo::save');
$routes->get('/ppi_ido/load', 'NonRs\PpiIdo::load');
$routes->post('/ppi_ido/print', 'NonRs\PpiIdo::print');
$routes->get('/ppi_ido/loadHistory', 'NonRs\PpiIdo::loadHistory');
$routes->post('/ppi_ido/delete', 'NonRs\PpiIdo::delete');
$routes->get('/ppi_ido/loadDokterAnastesi', 'NonRs\PpiIdo::loadDokterAnastesi');
$routes->get('/ppi_ido/loadPerawat', 'NonRs\PpiIdo::loadPerawat');

//PPI DEKUBITUS
$routes->post('/ppi_dekubitus/save', 'NonRs\PpiDekubitus::save');
$routes->get('/ppi_dekubitus/load', 'NonRs\PpiDekubitus::load');
$routes->post('/ppi_dekubitus/print', 'NonRs\PpiDekubitus::print');
$routes->get('/ppi_dekubitus/loadHistory', 'NonRs\PpiDekubitus::loadHistory');
$routes->get('/ppi_dekubitus/loadGejala', 'NonRs\PpiDekubitus::loadGejala');
$routes->get('/ppi_dekubitus/loadItemPencegahan', 'NonRs\PpiDekubitus::loadItemPencegahan');
$routes->get('/ppi_dekubitus/loadObatAntibiotik', 'NonRs\PpiDekubitus::loadObatAntibiotik');
$routes->post('/ppi_dekubitus/delete', 'NonRs\PpiDekubitus::delete');
$routes->get('/ppi_dekubitus/loadBulan', 'NonRs\PpiDekubitus::loadBulan');
$routes->get('/ppi_dekubitus/loadTahun', 'NonRs\PpiDekubitus::loadTahun');

//RESEP ENDPOINT BARU
$routes->get('/resep/master/datasigna', 'Resep::dataSignaMaster');
$routes->get('/resep/loadTipeSigna', 'Resep::loadTipeSigna');
$routes->get('/resep/loadTipeObat', 'Resep::loadTipeObat');
$routes->get('/resep/cekdata/obat_antimikroba', 'Resep::cekDataObatAntimikroba');
$routes->get('/resep/loadTipePpra', 'Resep::loadTipePpra');
$routes->get('/resep/loadTipeCatatanResep', 'Resep::loadTipeCatatanResep');
$routes->get('/resep/loadTipeDepo', 'Resep::loadTipeDepo');
$routes->get('/resep/loadTipeSatuanRacik', 'Resep::loadTipeSatuanRacik');
$routes->get('/resep/loadTipeKonsulMpp', 'Resep::loadTipeKonsulMpp');
$routes->post('/resep/order/approve', 'Resep::approveResep');
$routes->get('/resep/cekdata/obatMpp', 'Resep::ObatMpp');
$routes->get('/resep/loadTipePrb', 'Resep::loadTipePrb');

//cetak bank darah
$routes->post('/bank_darah/print', 'BankDarah::print');

//konsul mpp
$routes->post('/konsul_mpp/save', 'NonRs\KonsulMpp::save');
$routes->get('/konsul_mpp/load', 'NonRs\KonsulMpp::load');
$routes->get('/konsul_mpp/loadJenisKonsul', 'NonRs\KonsulMpp::loadJenisKonsul');
$routes->post('/konsul_mpp/jawab', 'NonRs\KonsulMpp::jawab');
$routes->get('/konsul_mpp/loadHistory', 'NonRs\KonsulMpp::loadHistory');
$routes->post('/konsul_mpp/delete', 'NonRs\KonsulMpp::delete');
$routes->post('/konsul_mpp/approve', 'NonRs\KonsulMpp::approve');
$routes->post('/konsul_mpp/notApprove', 'NonRs\KonsulMpp::notApprove');

//antrian mpp
$routes->get('/antrian/non_rs/mpp/belum_terlayani', 'NonRs\AntrianMpp::index');
$routes->get('/antrian/non_rs/mpp/terlayani', 'NonRs\AntrianMpp::index');
$routes->get('/antrian/non_rs/mpp/riwayat', 'NonRs\AntrianMpp::index');

//radiologi - approve mpp
$routes->post('/radiologi/approveMpp', 'Radiologi::approveMpp');
$routes->get('/radiologi/radMpp', 'Radiologi::radMpp');

//GET DATA LAMA RINGKASAN PASIEN
$routes->get('/ringkasan_pasien/loadTglMasuk', 'RingkasanPasien::loadTglMasuk');

//implan mpp
$routes->post('/implan_mpp/save', 'NonRs\ImplanMpp::save');
$routes->get('/implan_mpp/load', 'NonRs\ImplanMpp::load');
$routes->get('/implan_mpp/loadImplan', 'NonRs\ImplanMpp::loadImplan');
$routes->post('/implan_mpp/delete', 'NonRs\ImplanMpp::delete');

//laporan mpp
$routes->post('/mpp/laporan/cetak', 'NonRs\LaporanMpp::cetak');

//cetak prb
$routes->post('/prb/print', 'CetakPrb::print');

//kb
$routes->post('/kb/save', 'Kb::save');
$routes->get('/kb/load', 'Kb::load');
$routes->get('/kb/loadJenisKb', 'Kb::loadJenisKb');
$routes->get('/kb/loadKasus', 'Kb::loadKasus');
$routes->post('/kb/delete', 'Kb::delete');
$routes->get('/kb/loadPelayanankb', 'Kb::loadPelayanankb');
$routes->get('/kb/loadKomplikasi', 'Kb::loadKomplikasi');

//imunisasi
$routes->post('/imunisasi/save', 'Imunisasi::save');
$routes->get('/imunisasi/load', 'Imunisasi::load');
$routes->get('/imunisasi/loadJenisImunisasi', 'Imunisasi::loadJenisImunisasi');
$routes->get('/imunisasi/loadKelasImunisasi', 'Imunisasi::loadKelasImunisasi');
$routes->get('/imunisasi/loadUrutImunisasi', 'Imunisasi::loadUrutImunisasi');
$routes->post('/imunisasi/delete', 'Imunisasi::delete');

//laporan ppm
$routes->post('/laporan_ppm/save', 'RawatInap\LaporanPPM::save');
$routes->get('/laporan_ppm/load', 'RawatInap\LaporanPPM::load');
$routes->post('/laporan_ppm/cetak', 'RawatInap\LaporanPPM::cetak');

//laporan tpm
$routes->post('/laporan_tpm/save', 'RawatInap\LaporanTPM::save');
$routes->get('/laporan_tpm/load', 'RawatInap\LaporanTPM::load');
$routes->post('/laporan_tpm/cetak', 'RawatInap\LaporanTPM::cetak');

//pengantar inap
$routes->post('/pengantar_inap/save', 'RawatDarurat\PengantarInap::save');
$routes->get('/pengantar_inap/load', 'RawatDarurat\PengantarInap::load');
$routes->post('/pengantar_inap/print', 'RawatDarurat\PengantarInap::print');
$routes->get('/pengantar_inap/loadDataInap', 'RawatDarurat\PengantarInap::loadDataInap');

//PPRA
//antrian ppra
$routes->get('/antrian/non_rs/ppra/belum_terlayani', 'NonRs\AntrianPpra::index');
$routes->get('/antrian/non_rs/ppra/terlayani', 'NonRs\AntrianPpra::index');
$routes->get('/antrian/non_rs/ppra/riwayat', 'NonRs\AntrianPpra::index');

//master mapping antibiotik
$routes->get('/non_rs/master_mapping_antibiotik/load_data_master', 'NonRs\MasterMappingAntibiotik::load');
$routes->post('/non_rs/master_mapping_antibiotik/save', 'NonRs\MasterMappingAntibiotik::save');
$routes->get('/non_rs/master_mapping_antibiotik/load_obat', 'NonRs\MasterMappingAntibiotik::loadObat');
$routes->get('/non_rs/master_mapping_antibiotik/load_kelompok', 'NonRs\MasterMappingAntibiotik::loadKelompok');
$routes->post('/non_rs/master_mapping_antibiotik/delete', 'NonRs\MasterMappingAntibiotik::delete');

//Antimikroba
$routes->get('/non_rs/form_penggunaan_antimikroba/antimikroba', 'NonRs\FormPenggunaanAntimikroba::loadAntimikroba');
$routes->get('/non_rs/form_penggunaan_antimikroba/indikasi', 'NonRs\FormPenggunaanAntimikroba::loadIndikasi');
$routes->post('/non_rs/form_penggunaan_antimikroba/save', 'NonRs\FormPenggunaanAntimikroba::save');
$routes->get('/non_rs/form_penggunaan_antimikroba/load', 'NonRs\FormPenggunaanAntimikroba::load');
$routes->post('/non_rs/form_penggunaan_antimikroba/approve', 'NonRs\FormPenggunaanAntimikroba::approve');
$routes->get('/non_rs/form_penggunaan_antimikroba/load_history', 'NonRs\FormPenggunaanAntimikroba::loadHistory');
$routes->get('/non_rs/form_penggunaan_antimikroba/load_history_persetujuan', 'NonRs\FormPenggunaanAntimikroba::loadHistoryPersetujuan');
$routes->get('/non_rs/form_penggunaan_antimikroba/loadEmpirik', 'NonRs\FormPenggunaanAntimikroba::loadEmpirik');

//RESEP ENDPOINT BARU -> PPRA
$routes->get('/resep/cekdata/cekProfilaksis', 'Resep::cekProfilaksis');
$routes->get('/resep/cekdata/cekEmpirik', 'Resep::cekEmpirik');
$routes->get('/resep/cekdata/cekDefinitif', 'Resep::cekDefinitif');
$routes->get('/resep/cekdata/cekObatInjek', 'Resep::cekObatInjek');
$routes->post('/resep/order/approvePpra', 'Resep::approvePpra');

//konsul ppra
$routes->post('/konsul_ppra/save', 'NonRs\KonsulPpra::save');
$routes->get('/konsul_ppra/load', 'NonRs\KonsulPpra::load');
$routes->get('/konsul_ppra/loadJenisKonsul', 'NonRs\KonsulPpra::loadJenisKonsul');
$routes->post('/konsul_ppra/jawab', 'NonRs\KonsulPpra::jawab');
$routes->get('/konsul_ppra/loadHistory', 'NonRs\KonsulPpra::loadHistory');
$routes->post('/konsul_ppra/delete', 'NonRs\KonsulPpra::delete');
$routes->post('/konsul_ppra/approve', 'NonRs\KonsulPpra::approve');
$routes->post('/konsul_ppra/notApprove', 'NonRs\KonsulPpra::notApprove');
$routes->get('/konsul_ppra/getModulesKhusus', 'NonRs\KonsulPpra::getModulesKhusus');
$routes->get('/konsul_ppra/lab_mikrobiologi/load', 'NonRs\KonsulPpra::loadLabMikro');
$routes->get('/konsul_ppra/hasil_rad/load', 'NonRs\KonsulPpra::loadHasilRad');

//laporan gambar cag
$routes->post('/laporan_gambar_cag/save', 'RawatInap\LaporanGambarCag::save');
$routes->get('/laporan_gambar_cag/load', 'RawatInap\LaporanGambarCag::load');
$routes->post('/laporan_gambar_cag/print', 'RawatInap\LaporanGambarCag::print');

//laporan gambar pci
$routes->post('/laporan_gambar_pci/save', 'RawatInap\LaporanGambarPci::save');
$routes->get('/laporan_gambar_pci/load', 'RawatInap\LaporanGambarPci::load');
$routes->post('/laporan_gambar_pci/print', 'RawatInap\LaporanGambarPci::print');

//BANK DARAH - cek konsul mpp
$routes->get('/bank_darah/cekKonsulMpp', 'BankDarah::cekKonsulMpp');
$routes->post('/bank_darah/approveMpp', 'BankDarah::approveMpp');

//cathlab
$routes->post('/cathlab/save', 'Cathlab::save');
$routes->post('/cathlab/batalorder', 'Cathlab::batalOrder');
$routes->get('/cathlab/load', 'Cathlab::load');
$routes->get('/cathlab/loadhistory', 'Cathlab::loadDataHistory');
$routes->get('/cathlab/loadhistory_item', 'Cathlab::loadDataHistory_item');
$routes->get('/cathlab/loadpemeriksaan', 'Cathlab::loadItemPemeriksaan');
$routes->post('/cathlab/saveorder', 'Cathlab::saveOrder');
$routes->post('/cathlab/approveMpp', 'Cathlab::approveMpp');
$routes->get('/cathlab/cathlabMpp', 'Cathlab::cathlabMpp');

//mcu
$routes->post('/rj_tes_fisik_mcu/save', 'RawatJalan\TesFisikMCU::save');
$routes->get('/rj_tes_fisik_mcu/load', 'RawatJalan\TesFisikMCU::load');
$routes->post('/rj_tes_fisik_mcu/print', 'RawatJalan\TesFisikMCU::print');
$routes->post('/rj_tes_kebugaran_mcu/save', 'RawatJalan\TesKebugaranMCU::save');
$routes->get('/rj_tes_kebugaran_mcu/load', 'RawatJalan\TesKebugaranMCU::load');
$routes->post('/rj_tes_kebugaran_mcu/print', 'RawatJalan\TesKebugaranMCU::print');

//lab hasil checklist
$routes->get('/laboratorium/loadLabChecklist', 'Laboratorium::loadLabChecklist');

//AMBULANCE
$routes->post('/ambulance/save', 'Ambulance::save');
$routes->get('/ambulance/load', 'Ambulance::load');
$routes->get('/ambulance/loadHistory', 'Ambulance::loadHistory');
$routes->post('/ambulance/kirimOrder', 'Ambulance::kirimOrder');
$routes->post('/ambulance/batalOrder', 'Ambulance::batalOrder');
$routes->get('/ambulance/loadTindakanRanmor', 'Ambulance::loadTindakanRanmor');
$routes->post('/ambulance/delete', 'Ambulance::delete');
$routes->post('/ambulance/hapusOrder', 'Ambulance::hapusOrder');
$routes->get('/ambulance/cekTindakanOrder', 'Ambulance::cekTindakanOrder');
$routes->get('/file/base64', 'FileController::getBase64');

$routes->post('/prmrj/cetak', 'RawatJalan\PRMRJ::print');

//cetak surat keterangan mdr
$routes->post('/mdr/print', 'CetakMdr::print');

$routes->post('/cetak/surat_keterangan/indo', 'RawatJalan\CetakMCU::surat_keterangan');
$routes->post('/cetak/surat_keterangan/inggris', 'RawatJalan\CetakMCU::surat_keterangan');
$routes->post('/cetak/surat_keterangan_sehat/indo', 'RawatJalan\CetakMCU::surat_keterangan_sehat');
$routes->post('/cetak/surat_keterangan_sehat/inggris', 'RawatJalan\CetakMCU::surat_keterangan_sehat');
$routes->post('/cetak/surat_keterangan_narkoba/indo', 'RawatJalan\CetakMCU::surat_keterangan_narkoba');
$routes->post('/cetak/surat_keterangan_narkoba/inggris', 'RawatJalan\CetakMCU::surat_keterangan_narkoba');
$routes->post('/cetak/hasil_pengujian_kesehatan/indo', 'RawatJalan\CetakMCU::hasil_pengujian_kesehatan');
$routes->post('/cetak/hasil_pengujian_kesehatan/inggris', 'RawatJalan\CetakMCU::hasil_pengujian_kesehatan');
$routes->post('/cetak/surat_keterangan_disabilitas/indo', 'RawatJalan\CetakMCU::surat_keterangan_disabilitas');
$routes->post('/cetak/surat_keterangan_disabilitas/inggris', 'RawatJalan\CetakMCU::surat_keterangan_disabilitas');
$routes->post('/cetak/lembar_pemeriksaan_pribadi/indo_jepang', 'RawatJalan\CetakMCU::lembar_pemeriksaan_pribadi');
$routes->post('/cetak/ttv/indo', 'RawatJalan\CetakMCU::ttv');
$routes->post('/cetak/ttv/inggris', 'RawatJalan\CetakMCU::ttv');

//GET CETAKAN EVOMEDIS
$routes->get('/histori_pasien/loadCetakanEvomedis', 'HistoriPasien::loadCetakanEvomedis');

//rekon obat bawaan new
$routes->get('/rekonsiliasi_obat/loadMembawaObat', 'RekonsiliasiObat::loadMembawaObat');
$routes->post('/rekonsiliasi_obat/saveMembawaObat', 'RekonsiliasiObat::saveMembawaObat');

//surat_keterangan_gfr
$routes->post('/surat_keterangan_gfr/save', 'RawatJalan\SuratKeteranganGfr::save');
$routes->get('/surat_keterangan_gfr/load', 'RawatJalan\SuratKeteranganGfr::load');
$routes->post('/surat_keterangan_gfr/print', 'RawatJalan\SuratKeteranganGfr::print');

//menjelang akhir kehidupan
$routes->post('/pengkajian_menjelang_akhir_kehidupan/save', 'RawatInap\PengkajianMenjelangAkhirKehidupan::save');
$routes->get('/pengkajian_menjelang_akhir_kehidupan/load', 'RawatInap\PengkajianMenjelangAkhirKehidupan::load');
$routes->post('/pengkajian_menjelang_akhir_kehidupan/print', 'RawatInap\PengkajianMenjelangAkhirKehidupan::print');

$routes->post('/pengkajian_ulang_menjelang_akhir_kehidupan/save', 'RawatInap\PengkajianUlangMenjelangAkhirKehidupan::save');
$routes->get('/pengkajian_ulang_menjelang_akhir_kehidupan/load', 'RawatInap\PengkajianUlangMenjelangAkhirKehidupan::load');
$routes->post('/pengkajian_ulang_menjelang_akhir_kehidupan/print', 'RawatInap\PengkajianUlangMenjelangAkhirKehidupan::print');

//cetak surat keterangan disabilitas
$routes->post('/surat_disabilitas/print', 'CetakSuratDisabilitas::print');

//update aplicares
$routes->post('/aplicares/update/bed', 'BridgingAplicares::updateBed');

//master user
$routes->post('/master_user/save', 'Admin\MasterUser::save');
$routes->get('/master_user/load', 'Admin\MasterUser::load');
$routes->get('/master_user/loadUser', 'Admin\MasterUser::loadUser');
$routes->get('/master_user/loadJabatan', 'Admin\MasterUser::loadJabatan');
$routes->get('/master_user/loadUnit', 'Admin\MasterUser::loadUnit');
$routes->get('/master_user/loadRuang', 'Admin\MasterUser::loadRuang');
$routes->post('/master_user/delete', 'Admin\MasterUser::delete');

//riwayat pembedahan
$routes->get('/pembedahan/riwayat', 'Pembedahan::load');
$routes->get('/pembedahan/cetak', 'Pembedahan::cetak');

//vk
$routes->post('/vk/save', 'Vk::save');
$routes->post('/vk/batalorder', 'Vk::batalOrder');
$routes->get('/vk/load', 'Vk::load');
$routes->post('/vk/saveorder', 'Vk::saveOrder');
$routes->get('/vk/loadhistory', 'Vk::loadDataHistory');
$routes->get('/vk/loadhistory_item', 'Vk::loadDataHistory_item');
$routes->get('/vk/loadpemeriksaan', 'Vk::loadItemPemeriksaan');
$routes->get('/vk/loadDokterVk', 'Vk::loadDokterVk');
$routes->post('/vk/orderDilayani', 'Vk::orderDilayani');


//pendaftaran pasien
$routes->get('/pendaftaran/get_datapasien_pendaftaran', 'Pendaftaran\Pendaftaran::get_datapasien_pendaftaran');
$routes->post('/pendaftaran/getdata_peserta_bpjs_nik', 'Pendaftaran\Pendaftaran::getdata_peserta_bpjs_nik');
$routes->post('/pendaftaran/tambah_pasien', 'Pendaftaran\Pendaftaran::tambah_pasien');
$routes->get('/pendaftaran/getcmbparamedis', 'Pendaftaran\Pendaftaran::getcmbparamedis');
$routes->get('/pendaftaran/get_datatriage', 'Pendaftaran\Pendaftaran::get_datatriage');
$routes->post('/pendaftaran/getno_antrian', 'Pendaftaran\Pendaftaran::getno_antrian');
$routes->get('/pendaftaran/getKelasKamar', 'Pendaftaran\Pendaftaran::getKelasKamar');
$routes->get('/pendaftaran/getRuangKamar', 'Pendaftaran\Pendaftaran::getRuangKamar');
$routes->get('/pendaftaran/getcekkamar', 'Pendaftaran\Pendaftaran::getcekkamar');
$routes->get('/pendaftaran/getPenjamin', 'Pendaftaran\Pendaftaran::getPenjamin');
$routes->post('/pendaftaran/cetak-identitas', 'Pendaftaran\Pendaftaran::cetakIdentitas');
$routes->post('/pendaftaran/cetak-dokumen', 'Pendaftaran\Pendaftaran::cetakDokumen');


$routes->post('/pendaftaran/update_penanggungjwb', 'Pendaftaran\Pendaftaran::update_penanggungjwb');
$routes->get('/pendaftaran/gethistorypenanggungjwb', 'Pendaftaran\Pendaftaran::gethistorypenanggungjwb');
$routes->post('/pendaftaran/update_caramasuk', 'Pendaftaran\Pendaftaran::update_caramasuk');
$routes->post('/pendaftaran/simpan_caramasuk', 'Pendaftaran\Pendaftaran::simpan_caramasuk');
$routes->post('/pendaftaran/reservasi/get_datareservasi', 'Pendaftaran\Pendaftaran::get_datareservasi');
$routes->get('/pendaftaran/reservasi/filters', 'Pendaftaran\Pendaftaran::get_reservasi_filters');
$routes->get('/pendaftaran/reservasi/operasi', 'Pendaftaran\Pendaftaran::get_reservasi_operasi');
$routes->post('/pendaftaran/simpan', 'Pendaftaran\Pendaftaran::simpan');
$routes->get('/pendaftaran/getshift_combo', 'Pendaftaran\Pendaftaran::getshift_combo');
$routes->post('/pendaftaran/cariSEPinap', 'Pendaftaran\Pendaftaran::cariSEPinap');

$routes->get('/pendaftaran/kunjungan', 'Pendaftaran\Pendaftaran::dataKunjungan');
$routes->get('/pendaftaran/kunjungan/filters', 'Pendaftaran\Pendaftaran::filterKunjungan');
$routes->get('/pendaftaran/kunjungan/detail', 'Pendaftaran\Pendaftaran::detailKunjungan');
$routes->get('/pendaftaran/history/(:segment)', 'Pendaftaran\Pendaftaran::history/$1');
$routes->get('/pendaftaran/history-action/options', 'Pendaftaran\Pendaftaran::historyActionOptions');
$routes->post('/pendaftaran/history-action/(:segment)', 'Pendaftaran\Pendaftaran::historyAction/$1');
$routes->get('/pendaftaran/get_datariwayatsep', 'RegistrationSEP::history');

$routes->get('/pendaftaran/kategori_pasien', 'Pendaftaran\Pendaftaran::kategori_pasien');
$routes->get('/pendaftaran/mode_sep', 'Pendaftaran\Pendaftaran::mode_sep');
$routes->post('/pendaftaran/mode_sep', 'Pendaftaran\Pendaftaran::update_mode_sep');
$routes->get('/pendaftaran/paket_mcu', 'Pendaftaran\Pendaftaran::paket_mcu');
$routes->get('/pendaftaran/update_kunjungan_context', 'Pendaftaran\Pendaftaran::update_kunjungan_context');
$routes->post('/pendaftaran/update_unit', 'Pendaftaran\Pendaftaran::update_kunjungan/unit');
$routes->post('/pendaftaran/update_dokter', 'Pendaftaran\Pendaftaran::update_kunjungan/dokter');
$routes->post('/pendaftaran/update_penjamin', 'Pendaftaran\Pendaftaran::update_kunjungan/penjamin');
$routes->post('/pendaftaran/update_penjamin2', 'Pendaftaran\Pendaftaran::update_kunjungan/penjamin2');
$routes->get('/pendaftaran/antrian_loket/referensi', 'Pendaftaran\Pendaftaran::antrian_loket/referensi');
$routes->get('/pendaftaran/antrian_loket/history', 'Pendaftaran\Pendaftaran::antrian_loket/history');
$routes->post('/pendaftaran/antrian_loket/panggil', 'Pendaftaran\Pendaftaran::antrian_loket/panggil');
$routes->get('/pendaftaran/getcmbkelasasal', 'Pendaftaran\Pendaftaran::getcmbkelasasal');
$routes->get('/pendaftaran/konfigurasi', 'Pendaftaran\Pendaftaran::konfigurasi');


$routes->get('/bpjs-service/sep/referensi', 'BPJSService::getReferensiSEP');
$routes->get('/bpjs-service/load/poli', 'BPJSService::loadPoli');
$routes->get('/bpjs-service/load/dpjp', 'BPJSService::loadDPJP');
$routes->get('/bpjs-service/cari_sep', 'BPJSService::cariSEP');
$routes->get('/bpjs-service/cari_kartu', 'BPJSService::cariKartu');
$routes->post('/bpjs-service/getdata_peserta_bpjs', 'BPJSService::getdata_peserta_bpjs');
$routes->get('/bpjs-service/rujukan_surat_kontrol', 'BPJSService::getRujukanSuratKontrol');
$routes->get('/bpjs-service/sep/referensi', 'BPJSService::getReferensiSEP');
$routes->get('/bpjs-service/sep/cari_referensi', 'BPJSService::cariReferensiSEP');
$routes->post('/bpjs-service/sep/update_tglkontrol', 'BPJSService::updateTanggalKontrol');
$routes->post('/bpjs-service/rencana_kontrol/simpan', 'BPJSService::simpan_rencana_kontrol');
$routes->post('/bpjs-service/rencana_kontrol/hapus', 'BPJSService::hapus_rencana_kontrol');
$routes->get('/bpjs-service/rencana_kontrol/history', 'BPJSService::history_rencana_kontrol');
$routes->post('/bpjs-service/surat_kontrol/cetak', 'BPJSService::cetak_surat_kontrol');
//tambah antrean mjkn
$routes->post('/bpjs-service/tambah_antrean_mjkn', 'BPJSService::tambah_antrean_antrol');
$routes->post('/bpjs-service/sep/action/(:segment)','RegistrationSEP::action/$1');
$routes->get('/bpjs-service/sep/detail', 'RegistrationSEP::detail');
$routes->get('/bpjs-service/sep/status_pulang', 'RegistrationSEP::options');
$routes->post('/bpjs-service/sep/cetak','Kasir\KasirSEP::preview');

// Reservasi pendaftaran
$routes->get('/reservasi/referensi', 'Pendaftaran\Reservasi::referensi');
$routes->get('/reservasi/dokter', 'Pendaftaran\Reservasi::dokter');
$routes->get('/reservasi/penjamin', 'Pendaftaran\Reservasi::penjamin');
$routes->get('/reservasi/pasien', 'Pendaftaran\Reservasi::pasien');
$routes->get('/reservasi/cari-pasien', 'Pendaftaran\Reservasi::cariPasien');
$routes->get('/reservasi/daftar', 'Pendaftaran\Reservasi::daftar');
$routes->get('/reservasi/detail', 'Pendaftaran\Reservasi::detail');
$routes->get('/reservasi/antrian', 'Pendaftaran\Reservasi::antrian');

$routes->post('/reservasi/simpan', 'Pendaftaran\Reservasi::simpan');
$routes->post('/reservasi/hapus', 'Pendaftaran\Reservasi::hapus');

$routes->get('/reservasi/template', 'Pendaftaran\Reservasi::downloadTemplate');
$routes->post('/reservasi/upload', 'Pendaftaran\Reservasi::uploadExcel');
$routes->get('/contact-person/daftar', 'Pendaftaran\ContactPerson::daftar');
$routes->post('/contact-person/simpan', 'Pendaftaran\ContactPerson::simpan');
$routes->post('/contact-person/hapus', 'Pendaftaran\ContactPerson::hapus');
$routes->get('/contact-person/kode-urut', 'Pendaftaran\ContactPerson::kodeUrut');
$routes->get('/info-dokter/daftar', 'Pendaftaran\InfoDokter::daftar');
$routes->get('/verifikasi-mobile/referensi', 'Pendaftaran\VerifikasiMobile::referensi');
$routes->get('/verifikasi-mobile/daftar', 'Pendaftaran\VerifikasiMobile::daftar');
$routes->get('/verifikasi-mobile/pasien', 'Pendaftaran\VerifikasiMobile::pasien');

$routes->post('/verifikasi-mobile/pilih', 'Pendaftaran\VerifikasiMobile::pilih');
$routes->post('/verifikasi-mobile/verifikasi', 'Pendaftaran\VerifikasiMobile::verifikasi');
$routes->post('/verifikasi-mobile/sync', 'Pendaftaran\VerifikasiMobile::sync');

//kasir
$routes->get('/kasir/referensi', 'Kasir\Kasir::referensi');
$routes->get('/kasir/kunjungan', 'Kasir\Kasir::kunjungan');
$routes->get('/kasir/detail', 'Kasir\Kasir::detail');
$routes->get('/kasir/cekaksesbackdate', 'Kasir\Kasir::cekaksesbackdate');
$routes->post('/kasir/simpan', 'Kasir\Kasir::simpan');
$routes->post('/kasir/batal', 'Kasir\Kasir::batal');
$routes->post('/kasir/diskon-info', 'Kasir\Kasir::diskonInfo');
$routes->post('/kasir/diskon-simpan', 'Kasir\Kasir::diskonSimpan');
$routes->post('/kasir/cetak', 'Kasir\Kasir::cetak');
$routes->post('/kasir/counter-info', 'Kasir\Kasir::counterInfo');
$routes->post('/kasir/counter-simpan', 'Kasir\Kasir::counterSimpan');
$routes->post('/kasir/rekap-info', 'Kasir\Kasir::rekapInfo');
$routes->post('/kasir/toolbar-cetak', 'Kasir\Kasir::toolbarCetak');
$routes->post('/kasir/preview-sep', 'Kasir\KasirSEP::preview');
$routes->post('/kasir/posting-pendapatan', 'Kasir\Kasir::postingPendapatan');


$routes->get('/kasir-mjkn/referensi', 'Kasir\KasirMJKN::referensi');
$routes->get('/kasir-mjkn/pendapatan', 'Kasir\KasirMJKN::pendapatan');
$routes->get('/kasir-mjkn/cek-counter', 'Kasir\KasirMJKN::cekCounter');
$routes->post('/kasir-mjkn/bayar', 'Kasir\KasirMJKN::bayar');
$routes->post('/kasir-mjkn/batal', 'Kasir\KasirMJKN::batal');

$routes->get('/penerimaan-uang-muka/referensi', 'Kasir\PenerimaanUangMuka::referensi');
$routes->get('/penerimaan-uang-muka/daftar', 'Kasir\PenerimaanUangMuka::daftar');
$routes->get('/penerimaan-uang-muka/detail', 'Kasir\PenerimaanUangMuka::detail');
$routes->get('/penerimaan-uang-muka/pasien', 'Kasir\PenerimaanUangMuka::pasien');
$routes->post('/penerimaan-uang-muka/simpan', 'Kasir\PenerimaanUangMuka::simpan');
$routes->post('/penerimaan-uang-muka/hapus', 'Kasir\PenerimaanUangMuka::hapus');
$routes->post('/penerimaan-uang-muka/cetak', 'Kasir\PenerimaanUangMuka::cetak');

$routes->get('/pengembalian-uang-muka/referensi', 'Kasir\PengembalianUangMuka::referensi');
$routes->get('/pengembalian-uang-muka/daftar', 'Kasir\PengembalianUangMuka::daftar');
$routes->get('/pengembalian-uang-muka/detail', 'Kasir\PengembalianUangMuka::detail');
$routes->get('/pengembalian-uang-muka/penerimaan', 'Kasir\PengembalianUangMuka::penerimaan');
$routes->post('/pengembalian-uang-muka/simpan', 'Kasir\PengembalianUangMuka::simpan');
$routes->post('/pengembalian-uang-muka/hapus', 'Kasir\PengembalianUangMuka::hapus');
$routes->post('/pengembalian-uang-muka/cetak', 'Kasir\PengembalianUangMuka::cetak');

$routes->get('/otorisasi-pendapatan/referensi', 'Kasir\OtorisasiPendapatan::referensi');
$routes->get('/otorisasi-pendapatan/daftar', 'Kasir\OtorisasiPendapatan::daftar');
$routes->get('/otorisasi-pendapatan/detail', 'Kasir\OtorisasiPendapatan::detail');

$routes->post('/otorisasi-pendapatan/otorisasi', 'Kasir\OtorisasiPendapatan::otorisasi');
$routes->post('/otorisasi-pendapatan/rollback', 'Kasir\OtorisasiPendapatan::rollback');
$routes->post('/otorisasi-pendapatan/cetak', 'Kasir\OtorisasiPendapatan::cetak');

//Common
$routes->get('/common/getJenisKelamin', 'Common::getJenisKelamin');
$routes->get('/common/getGolonganDarah', 'Common::getGolonganDarah');
$routes->get('/common/getBahasa', 'Common::getBahasa');
$routes->get('/common/getAgama', 'Common::getAgama');
$routes->get('/common/getPendidikan', 'Common::getPendidikan');
$routes->get('/common/getPekerjaan', 'Common::getPekerjaan');
$routes->get('/common/getKebangsaan', 'Common::getKebangsaan');
$routes->get('/common/getStatusPasien', 'Common::getStatusPasien');
$routes->get('/common/getSuku', 'Common::getSuku');
$routes->get('/common/getShift', 'Common::getShift');
$routes->get('/common/getPropinsi', 'Common::getPropinsi');
$routes->get('/common/getKabupaten', 'Common::getKabupaten');
$routes->get('/common/getKecamatan', 'Common::getKecamatan');
$routes->get('/common/getKelurahan', 'Common::getKelurahan');
$routes->get('/common/getIdentitas', 'Common::getIdentitas');
$routes->get('/common/getDokter', 'Common::getDokter');
$routes->get('/common/getDokterPengirim', 'Common::getDokterPengirim');
$routes->get('/common/getInstalasi', 'Common::getInstalasi');
$routes->get('/common/getUnit', 'Common::getUnit');
$routes->get('/common/getUser', 'Common::getUser');
$routes->get('/common/getRuangPerawatan', 'Common::getRuangPerawatan');
$routes->get('/common/getKelPenjamin', 'Common::getKelPenjamin');
$routes->get('/common/getPenjamin', 'Common::getPenjamin');
$routes->get('/common/getPerusahaan', 'Common::getPerusahaan');
$routes->get('/common/getPenanggungJawab', 'Common::getPenanggungJawab');
$routes->get('/common/getCaraMasuk', 'Common::getCaraMasuk');
$routes->get('/common/getCaraKeluar', 'Common::getCaraKeluar');
$routes->get('/common/getRujukan', 'Common::getRujukan');
$routes->get('/common/getNama', 'Common::getNama');
$routes->get('/common/getTujuanKunjungan', 'Common::getTujuanKunjungan');
$routes->get('/common/getDiagnosa', 'Common::getDiagnosa');
$routes->get('/common/getJenisAkses', 'Common::getJenisAkses');
$routes->get('/common/getRujukanAsal', 'Common::getRujukanAsal');
$routes->get('/common/getRujukanDetail', 'Common::getRujukanDetail');
$routes->get('/common/getUsia', 'Common::getUsia');
$routes->get('/common/getCaraPemesanan', 'Common::getCaraPemesanan');
$routes->get('/common/getCaraBayar', 'Common::getCaraBayar');
$routes->get('/common/getCounter', 'Common::getCounter');
$routes->get('/common/getKelurahanGrid', 'Common::getKelurahanGrid');
$routes->get('/common/getDefaultSetting', 'Common::getDefaultSetting');
$routes->post('/common/saveDefaultSetting', 'Common::saveDefaultSetting');
$routes->get('/common/getCurrentShift','Common::getCurrentShift');

//LAPORAN PENDAFTARAN
$routes->post('/laporan_register_kunjungan/print', 'Pendaftaran\LaporanRegisterKunjungan::print');
$routes->post('/laporan_register_rj/print', 'Pendaftaran\LaporanRegisterRJ::print');
$routes->post('/laporan_register_ri/print', 'Pendaftaran\LaporanRegisterRI::print');
$routes->post('/laporan_pasien_rwi/print', 'Pendaftaran\LaporanPasienRWI::print');
$routes->post('/laporan_respon_time/print', 'Pendaftaran\LaporanResponTime::print');
$routes->post('/laporan_rekap_rj/print', 'Pendaftaran\LaporanRekapRJ::print');
$routes->post('/laporan_kunjungan_peroperator/print', 'Pendaftaran\LaporanKunjunganPerOperator::print');
$routes->post('/laporan_kunjungan/print', 'Pendaftaran\LaporanKunjungan::print');
$routes->post('/laporan_rekap_ri/print', 'Pendaftaran\LaporanRekapRI::print');
$routes->post('/laporan_reservasi/print', 'Pendaftaran\LaporanReservasi::print');
$routes->post('/laporan_rujukan_bpjs/print', 'Pendaftaran\LaporanRujukanBPJS::print');

//Laporan Kasir
$routes->post('/laporan_penerimaan_uang_muka/print', 'Laporan\Kasir\LaporanPenerimaanUangMuka::print');
$routes->post('/laporan_pengembalian_uang_muka/print', 'Laporan\Kasir\LaporanPengembalianUangMuka::print');
$routes->post('/laporan_penerimaan_pengembalian_uang_muka/print', 'Laporan\Kasir\LaporanPenerimaanPengembalianUangMuka::print');
$routes->post('/laporan_setoran_kasir/print', 'Laporan\Kasir\LaporanSetoranKasir::print');
$routes->post('/laporan_setoran_kasir/print_rekap', 'Laporan\Kasir\LaporanSetoranKasir::print_rekap');
$routes->post('/laporan_setoran_kasir/print_farmasi', 'Laporan\Kasir\LaporanSetoranKasir::print_farmasi');
$routes->post('/laporan_setoran_kasir/print_pno', 'Laporan\Kasir\LaporanSetoranKasir::print_pno');
// $routes->post('/laporan_komponen_unit/print', 'Laporan\Kasir\LaporanKasirMapping::print');
// $routes->post('/laporan_komponen_unit/print_prosentase', 'Laporan\Kasir\LaporanKasirMapping::print_prosentase');
// $routes->post('/laporan_komponen_unit/print_instalasi', 'Laporan\Kasir\LaporanKasirMapping::print_instalasi');
$routes->post('/laporan_batal_payment/print', 'Laporan\Kasir\LaporanBatalPayment::print');
$routes->post('/laporan_diskon/print', 'Laporan\Kasir\LaporanDiskon::print');
$routes->post('/rekap_pemanfaatan_rs_percarabayar/print', 'Laporan\Kasir\RekapPemanfaatanRsPercarabayar::print');
$routes->post('/laporan_deposit/print', 'Laporan\Kasir\LaporanDeposit::print');
$routes->post('/laporan_belum_bayar/print', 'Laporan\Kasir\LaporanBelumBayar::print');
$routes->post('/laporan_respontime_kasir/print', 'Laporan\Kasir\LaporanResponTimeKasir::print');


//laboratorium
$routes->get('/laporan_buku_register_lab/getTindakanLab', 'Laporan\Laboratorium\LaporanBukuRegisterLab::getTindakanLab');
$routes->post('/laporan_buku_register_lab/print', 'Laporan\Laboratorium\LaporanBukuRegisterLab::print');
$routes->post('/laporan_buku_register_lab/print_summary', 'Laporan\Laboratorium\LaporanBukuRegisterLab::print_summary');
$routes->post('/laporan_informasi_transfer_lab/print', 'Laporan\Laboratorium\LaporanInformasiTransfer::print');
$routes->post('/laporan_transaksi_cito_lab/print', 'Laporan\Laboratorium\LaporanTransaksiCito::print');
$routes->post('/laporan_pemeriksaan_rujukan/print', 'Laporan\Laboratorium\LaporanPemeriksaanRujukan::print');
$routes->post('/laporan_pemeriksaan_lab/print', 'Laporan\Laboratorium\LaporanPemeriksaanLab::print');
$routes->get('/laporan_pemeriksaan_lab/getKlasifikasi', 'Laporan\Laboratorium\LaporanPemeriksaanLab::getKlasifikasi');
$routes->post('/laporan_penerimaan_summary_lab/print', 'Laporan\Laboratorium\LaporanPenerimaanSummary::print');
$routes->get('/laporan_penerimaan_summary_lab/getTindakan', 'Laporan\Laboratorium\LaporanPenerimaanSummary::getTindakan');
$routes->get('/laporan_penerimaan_summary_lab/getPenjamin', 'Laporan\Laboratorium\LaporanPenerimaanSummary::getPenjamin');
$routes->post('/laporan_pendapatan_pmi/print', 'Laporan\Laboratorium\LaporanPendapatanPmi::print');
$routes->post('/laporan_dokter_kirim_lab/print', 'Laporan\Laboratorium\LaporanDokterKirimLab::print');
$routes->post('/laporan_dokter_kirim_lab/print_detail', 'Laporan\Laboratorium\LaporanDokterKirimLab::print_detail');
$routes->post('/laporan_penerimaan_barang/print', 'Laporan\Laboratorium\LaporanPenerimaanBarang::print');
$routes->get('/laporan_penerimaan_barang/getPbf', 'Laporan\Laboratorium\LaporanPenerimaanBarang::getPbf');
$routes->post('/laporan_pemeriksaan_lab_rirj/print', 'Laporan\Laboratorium\LaporanPemeriksaanLabRiRj::print');
$routes->post('/laporan_detail_pemeriksaan_perunit/print', 'Laporan\Laboratorium\LaporanDetailPemeriksaanPerunit::print');
$routes->post('/laporan_ubt/print', 'Laporan\Laboratorium\LaporanUbt::print');
$routes->post('/laporan_bulanan_lab/print', 'Laporan\Laboratorium\LaporanBulananLab::print');
$routes->post('/laporan_penerimaan_lab/print', 'Laporan\Laboratorium\LaporanPenerimaanLab::print');
$routes->post('/laporan_penerimaan_lab/print_prev', 'Laporan\Laboratorium\LaporanPenerimaanLab::print_prev');
$routes->post('/laporan_penerimaan_lab/print_internal', 'Laporan\Laboratorium\LaporanPenerimaanLab::print_internal');
$routes->post('/laporan_penerimaan_lab/print_bulanan', 'Laporan\Laboratorium\LaporanPenerimaanLab::print_bulanan');
$routes->post('/laporan_nilai_kritis/print', 'Laporan\Laboratorium\LaporanNilaiKritis::print');
$routes->post('/laporan_order_lab/print', 'Laporan\Laboratorium\LaporanOrderLab::print');
$routes->post('/laporan_durasi_pemeriksaan/print', 'Laporan\Laboratorium\LaporanDurasiPemeriksaan::print');
$routes->get('/laporan_durasi_pemeriksaan/getTindakan', 'Laporan\Laboratorium\LaporanDurasiPemeriksaan::getTindakan');
$routes->get('/laporan_durasi_pemeriksaan/getKlasifikasi', 'Laporan\Laboratorium\LaporanDurasiPemeriksaan::getKlasifikasi');
$routes->get('/laporan_durasi_pemeriksaan/getRuang', 'Laporan\Laboratorium\LaporanDurasiPemeriksaan::getRuang');
$routes->post('/laporan_jumlah_pemeriksaan/print', 'Laporan\Laboratorium\LaporanJumlahPemeriksaan::print');
$routes->post('/laporan_jumlah_pemeriksaan/print_pertanggal', 'Laporan\Laboratorium\LaporanJumlahPemeriksaan::print_pertanggal');
$routes->post('/laporan_jumlah_pasien/print', 'Laporan\Laboratorium\LaporanJumlahPasien::print');
$routes->post('/laporan_jumlah_pasien_v2/print_pasien', 'Laporan\Laboratorium\LaporanJumlahPasienV2::print_pasien');
$routes->post('/laporan_jumlah_pasien_v2/print_pemeriksaan', 'Laporan\Laboratorium\LaporanJumlahPasienV2::print_pemeriksaan');
$routes->get('/laporan_jumlah_pasien_v2/getRuang', 'Laporan\Laboratorium\LaporanJumlahPasienV2::getRuang');
$routes->post('laporan_validasi_pasien/print', 'Laporan\Laboratorium\LaporanValidasiPasien::print');
$routes->post('laporan_pemeriksaan_cito/print', 'Laporan\Laboratorium\LaporanPemeriksaanCito::print');
$routes->get('laporan_pemeriksaan_cito/getTindakan', 'Laporan\Laboratorium\LaporanPemeriksaanCito::getTindakan');
$routes->post('laporan_pemeriksaan_lab_perkelompok/print', 'Laporan\Laboratorium\LaporanPemeriksaanLabPerkelompok::print');
$routes->post('laporan_hasil_kultur/print', 'Laporan\Laboratorium\LaporanHasilKultur::print');
$routes->post('laporan_informasi_transfer_radiologi/print', 'Laporan\Radiologi\LaporanInformasiTransferRadiologi::print');

// radiologi
$routes->post('laporan_buku_register_radiologi/print', 'Laporan\Radiologi\LaporanBukuRegisterRadiologi::print');
$routes->get('laporan_buku_register_radiologi/getGrup', 'Laporan\Radiologi\LaporanBukuRegisterRadiologi::getGrup');

$routes->get('laporan_dokter_baca_radiologi/getDokterBaca', 'Laporan\Radiologi\LaporanDokterBacaRadiologi::getDokterBaca');
$routes->get('laporan_dokter_baca_radiologi/getKlasifikasi', 'Laporan\Radiologi\LaporanDokterBacaRadiologi::getKlasifikasi');
$routes->get('laporan_dokter_baca_radiologi/getPenjamin', 'Laporan\Radiologi\LaporanDokterBacaRadiologi::getPenjamin');
$routes->post('laporan_dokter_baca_radiologi/print', 'Laporan\Radiologi\LaporanDokterBacaRadiologi::print');

$routes->post('/radiologi/laporan_penerimaan_summary/print', 'Laporan\Radiologi\LaporanPenerimaanSummary::print');
$routes->get('/radiologi/laporan_penerimaan_summary/getPenjamin', 'Laporan\Radiologi\LaporanPenerimaanSummary::getPenjamin');

$routes->post('laporan_pemakaian_film/print', 'Laporan\Radiologi\LaporanPemakaianFilm::print');

$routes->post('laporan_kegiatan_expertise/print', 'Laporan\Radiologi\LaporanKegiatanExpertise::print');
$routes->post('laporan_kegiatan_expertise/printDetail', 'Laporan\Radiologi\LaporanKegiatanExpertise::printDetail');
$routes->get('laporan_kegiatan_expertise/getDokter', 'Laporan\Radiologi\LaporanKegiatanExpertise::getDokter');

$routes->get('/common/getReferensiBarangFarmasi', 'Common::getReferensiBarangFarmasi');
$routes->get('/common/getBarangFarmasi', 'Common::getBarangFarmasi');
$routes->post('/farmasi/master-barang/simpan', 'Farmasi\MasterBarangFarmasi::simpan');
$routes->post('/farmasi/master-barang/hapus', 'Farmasi\MasterBarangFarmasi::hapus');


$routes->get('/common/getAkunTransaksi', 'Common::getAkunTransaksi');
$routes->get('/common/getVendorFarmasi', 'Common::getVendorFarmasi');
$routes->post('/farmasi/master-vendor/simpan', 'Farmasi\MasterVendor::simpan');
$routes->post('/farmasi/master-vendor/hapus', 'Farmasi\MasterVendor::hapus');

$routes->get('/farmasi/master_satuan_besar/load', 'Farmasi\MasterSatuanBesar::load');
$routes->post('/farmasi/master_satuan_besar/save', 'Farmasi\MasterSatuanBesar::save');
$routes->post('/farmasi/master_satuan_besar/delete', 'Farmasi\MasterSatuanBesar::delete');

$routes->get('/farmasi/master_pabrik/load', 'Farmasi\MasterPabrik::load');
$routes->get('/farmasi/master_pabrik/propinsi', 'Farmasi\MasterPabrik::propinsi');
$routes->get('/farmasi/master_pabrik/kabupaten', 'Farmasi\MasterPabrik::kabupaten');
$routes->get('/farmasi/master_pabrik/kecamatan', 'Farmasi\MasterPabrik::kecamatan');
$routes->post('/farmasi/master_pabrik/save', 'Farmasi\MasterPabrik::save');
$routes->post('/farmasi/master_pabrik/delete', 'Farmasi\MasterPabrik::delete');

$routes->get('/farmasi/master_jenis/load', 'Farmasi\MasterJenis::load');
$routes->post('/farmasi/master_jenis/save', 'Farmasi\MasterJenis::save');
$routes->post('/farmasi/master_jenis/delete', 'Farmasi\MasterJenis::delete');
$routes->get('/farmasi/master_jenis/klasifikasi', 'Farmasi\MasterJenis::klasifikasi');

$routes->get('/farmasi/master_sub_jenis/load', 'Farmasi\MasterSubJenis::load');
$routes->post('/farmasi/master_sub_jenis/save', 'Farmasi\MasterSubJenis::save');
$routes->post('/farmasi/master_sub_jenis/delete', 'Farmasi\MasterSubJenis::delete');
$routes->get('/farmasi/master_sub_jenis/jenis', 'Farmasi\MasterSubJenis::jenis');

$routes->get('/farmasi/master_golongan/load', 'Farmasi\MasterGolongan::load');
$routes->post('/farmasi/master_golongan/save', 'Farmasi\MasterGolongan::save');
$routes->post('/farmasi/master_golongan/delete', 'Farmasi\MasterGolongan::delete');

$routes->get('/farmasi/master_jenis_terapi/load', 'Farmasi\MasterJenisTerapi::load');
$routes->post('/farmasi/master_jenis_terapi/save', 'Farmasi\MasterJenisTerapi::save');
$routes->post('/farmasi/master_jenis_terapi/delete', 'Farmasi\MasterJenisTerapi::delete');

$routes->get('/farmasi/master_satuan_kecil/load', 'Farmasi\MasterSatuanKecil::load');
$routes->post('/farmasi/master_satuan_kecil/save', 'Farmasi\MasterSatuanKecil::save');
$routes->post('/farmasi/master_satuan_kecil/delete', 'Farmasi\MasterSatuanKecil::delete');

$routes->get('/farmasi/master_formularium/load', 'Farmasi\MasterFormularium::load');
$routes->post('/farmasi/master_formularium/save', 'Farmasi\MasterFormularium::save');
$routes->post('/farmasi/master_formularium/delete', 'Farmasi\MasterFormularium::delete');

$routes->get('/farmasi/master_reg_pom/load', 'Farmasi\MasterRegPom::load');
$routes->post('/farmasi/master_reg_pom/save', 'Farmasi\MasterRegPom::save');
$routes->post('/farmasi/master_reg_pom/delete', 'Farmasi\MasterRegPom::delete');

$routes->get('/farmasi/master_komposisi/load', 'Farmasi\MasterKomposisi::load');
$routes->post('/farmasi/master_komposisi/save', 'Farmasi\MasterKomposisi::save');
$routes->post('/farmasi/master_komposisi/delete', 'Farmasi\MasterKomposisi::delete');

$routes->get('/farmasi/master_jenis_keuntungan/load', 'Farmasi\MasterJenisKeuntungan::load');
$routes->post('/farmasi/master_jenis_keuntungan/save', 'Farmasi\MasterJenisKeuntungan::save');
$routes->post('/farmasi/master_jenis_keuntungan/delete', 'Farmasi\MasterJenisKeuntungan::delete');

$routes->get('/farmasi/master_satuan_keuntungan/load', 'Farmasi\MasterSatuanKeuntungan::load');
$routes->post('/farmasi/master_satuan_keuntungan/save', 'Farmasi\MasterSatuanKeuntungan::save');
$routes->post('/farmasi/master_satuan_keuntungan/delete', 'Farmasi\MasterSatuanKeuntungan::delete');

$routes->get('/farmasi/master_keuntungan_detail/load', 'Farmasi\MasterKeuntunganDetail::load');
$routes->get('/farmasi/master_keuntungan_detail/jenis', 'Farmasi\MasterKeuntunganDetail::jenis');
$routes->get('/farmasi/master_keuntungan_detail/satuan', 'Farmasi\MasterKeuntunganDetail::satuan');
$routes->get('/farmasi/master_keuntungan_detail/sumber_anggaran', 'Farmasi\MasterKeuntunganDetail::sumberAnggaran');
$routes->get('/farmasi/master_keuntungan_detail/unit', 'Farmasi\MasterKeuntunganDetail::unit');
$routes->post('/farmasi/master_keuntungan_detail/save', 'Farmasi\MasterKeuntunganDetail::save');
$routes->post('/farmasi/master_keuntungan_detail/delete', 'Farmasi\MasterKeuntunganDetail::delete');

$routes->get('/farmasi/master_template/load', 'Farmasi\MasterTemplate::load');
$routes->post('/farmasi/master_template/save', 'Farmasi\MasterTemplate::save');
$routes->post('/farmasi/master_template/delete', 'Farmasi\MasterTemplate::delete');

$routes->get('/farmasi/master_template_detail/load', 'Farmasi\MasterTemplateDetail::load');
$routes->get('/farmasi/master_template_detail/templates', 'Farmasi\MasterTemplateDetail::templates');
$routes->get('/farmasi/master_template_detail/obat', 'Farmasi\MasterTemplateDetail::obat');
$routes->get('/farmasi/master_template_detail/cara_pakai', 'Farmasi\MasterTemplateDetail::caraPakai');
$routes->get('/farmasi/master_template_detail/waktu_pakai', 'Farmasi\MasterTemplateDetail::waktuPakai');
$routes->post('/farmasi/master_template_detail/save', 'Farmasi\MasterTemplateDetail::save');
$routes->post('/farmasi/master_template_detail/delete', 'Farmasi\MasterTemplateDetail::delete');

$routes->get('/farmasi/master_aturan_tambahan/load', 'Farmasi\MasterAturanTambahan::load');
$routes->post('/farmasi/master_aturan_tambahan/save', 'Farmasi\MasterAturanTambahan::save');
$routes->post('/farmasi/master_aturan_tambahan/delete', 'Farmasi\MasterAturanTambahan::delete');

$routes->get('/farmasi/master_sub_jenis_2/load', 'Farmasi\MasterSubJenis2::load');
$routes->get('/farmasi/master_sub_jenis_2/sub_jenis', 'Farmasi\MasterSubJenis2::subJenis');
$routes->post('/farmasi/master_sub_jenis_2/save', 'Farmasi\MasterSubJenis2::save');
$routes->post('/farmasi/master_sub_jenis_2/delete', 'Farmasi\MasterSubJenis2::delete');

$routes->get('/farmasi/master_interaksi_obat/load', 'Farmasi\MasterInteraksiObat::load');
$routes->get('/farmasi/master_interaksi_obat/obat', 'Farmasi\MasterInteraksiObat::obat');
$routes->post('/farmasi/master_interaksi_obat/save', 'Farmasi\MasterInteraksiObat::save');
$routes->post('/farmasi/master_interaksi_obat/delete', 'Farmasi\MasterInteraksiObat::delete');

$routes->get('/farmasi/master_signa/load', 'Farmasi\MasterSigna::load');
$routes->post('/farmasi/master_signa/save', 'Farmasi\MasterSigna::save');
$routes->post('/farmasi/master_signa/delete', 'Farmasi\MasterSigna::delete');

$routes->get('/farmasi/master_pj_pengadaan/load', 'Farmasi\MasterPjPengadaan::load');
$routes->get('/farmasi/master_pj_pengadaan/unit_kebutuhan', 'Farmasi\MasterPjPengadaan::unitKebutuhan');
$routes->post('/farmasi/master_pj_pengadaan/save', 'Farmasi\MasterPjPengadaan::save');
$routes->post('/farmasi/master_pj_pengadaan/delete', 'Farmasi\MasterPjPengadaan::delete');

$routes->get('/farmasi/master_dasar/load', 'Farmasi\MasterDasar::load');
$routes->post('/farmasi/master_dasar/save', 'Farmasi\MasterDasar::save');
$routes->post('/farmasi/master_dasar/delete', 'Farmasi\MasterDasar::delete');

// Penerimaan gudang farmasi
$routes->get('/farmasi/penerimaan/referensi', 'Farmasi\Penerimaan::referensi');
$routes->get('/farmasi/penerimaan/daftar', 'Farmasi\Penerimaan::daftar');
$routes->get('/farmasi/penerimaan/detail', 'Farmasi\Penerimaan::detail');
$routes->get('/farmasi/penerimaan/obat', 'Farmasi\Penerimaan::obat');
$routes->get('/farmasi/penerimaan/order', 'Farmasi\Penerimaan::order');

$routes->post('/farmasi/penerimaan/simpan', 'Farmasi\Penerimaan::simpan');
$routes->post('/farmasi/penerimaan/item', 'Farmasi\Penerimaan::item');
$routes->post('/farmasi/penerimaan/hapus-item', 'Farmasi\Penerimaan::hapusItem');
$routes->post('/farmasi/penerimaan/hapus', 'Farmasi\Penerimaan::hapus');
$routes->post('/farmasi/penerimaan/tutup', 'Farmasi\Penerimaan::tutup');
$routes->post('/farmasi/penerimaan/buka', 'Farmasi\Penerimaan::buka');
$routes->post('/farmasi/penerimaan/checker', 'Farmasi\Penerimaan::checker');
$routes->post('/farmasi/penerimaan/cetak', 'Farmasi\Penerimaan::cetak');
$routes->get('/farmasi/penerimaan/pesanan', 'Farmasi\Penerimaan::pesanan');


// Rencana kebutuhan farmasi
$routes->get('/farmasi/rencana-kebutuhan/referensi', 'Farmasi\RencanaKebutuhan::referensi');
$routes->get('/farmasi/rencana-kebutuhan/daftar', 'Farmasi\RencanaKebutuhan::daftar');
$routes->get('/farmasi/rencana-kebutuhan/permintaan', 'Farmasi\RencanaKebutuhan::permintaan');
$routes->get('/farmasi/rencana-kebutuhan/detail', 'Farmasi\RencanaKebutuhan::detail');
$routes->get('/farmasi/rencana-kebutuhan/obat', 'Farmasi\RencanaKebutuhan::obat');
$routes->get('/farmasi/rencana-kebutuhan/estimasi', 'Farmasi\RencanaKebutuhan::estimasi');

$routes->post('/farmasi/rencana-kebutuhan/item', 'Farmasi\RencanaKebutuhan::item');
$routes->post('/farmasi/rencana-kebutuhan/impor', 'Farmasi\RencanaKebutuhan::impor');
$routes->post('/farmasi/rencana-kebutuhan/hapus-item', 'Farmasi\RencanaKebutuhan::hapusItem');
$routes->post('/farmasi/rencana-kebutuhan/hapus', 'Farmasi\RencanaKebutuhan::hapus');
$routes->post('/farmasi/rencana-kebutuhan/kirim', 'Farmasi\RencanaKebutuhan::kirim');
$routes->post('/farmasi/rencana-kebutuhan/batal-kirim', 'Farmasi\RencanaKebutuhan::batalKirim');
$routes->post('/farmasi/rencana-kebutuhan/cetak', 'Farmasi\RencanaKebutuhan::cetak');
$routes->post('/farmasi/rencana-kebutuhan/excel', 'Farmasi\RencanaKebutuhan::excel');

$routes->get('/farmasi/permintaan-kebutuhan/referensi', 'Farmasi\PermintaanKebutuhan::referensi');
$routes->get('/farmasi/permintaan-kebutuhan/daftar', 'Farmasi\PermintaanKebutuhan::daftar');
$routes->get('/farmasi/permintaan-kebutuhan/detail', 'Farmasi\PermintaanKebutuhan::detail');
$routes->get('/farmasi/permintaan-kebutuhan/obat', 'Farmasi\PermintaanKebutuhan::obat');
$routes->post('/farmasi/permintaan-kebutuhan/item', 'Farmasi\PermintaanKebutuhan::item');
$routes->post('/farmasi/permintaan-kebutuhan/hapus-item', 'Farmasi\PermintaanKebutuhan::hapusItem');
$routes->post('/farmasi/permintaan-kebutuhan/hapus', 'Farmasi\PermintaanKebutuhan::hapus');
$routes->post('/farmasi/permintaan-kebutuhan/kirim', 'Farmasi\PermintaanKebutuhan::kirim');
$routes->post('/farmasi/permintaan-kebutuhan/batal-kirim', 'Farmasi\PermintaanKebutuhan::batalKirim');
$routes->post('/farmasi/permintaan-kebutuhan/cetak', 'Farmasi\PermintaanKebutuhan::cetak');

// Permintaan unit (orderunit)
$routes->get('/farmasi/permintaan-unit/referensi', 'Farmasi\PermintaanUnit::referensi');
$routes->get('/farmasi/permintaan-unit/daftar', 'Farmasi\PermintaanUnit::daftar');
$routes->get('/farmasi/permintaan-unit/detail', 'Farmasi\PermintaanUnit::detail');
$routes->get('/farmasi/permintaan-unit/obat', 'Farmasi\PermintaanUnit::obat');
$routes->get('/farmasi/permintaan-unit/penjualan', 'Farmasi\PermintaanUnit::penjualan');
$routes->post('/farmasi/permintaan-unit/item', 'Farmasi\PermintaanUnit::item');
$routes->post('/farmasi/permintaan-unit/hapus-item', 'Farmasi\PermintaanUnit::hapusItem');
$routes->post('/farmasi/permintaan-unit/hapus', 'Farmasi\PermintaanUnit::hapus');
$routes->post('/farmasi/permintaan-unit/kirim', 'Farmasi\PermintaanUnit::kirim');
$routes->post('/farmasi/permintaan-unit/batal-kirim', 'Farmasi\PermintaanUnit::batalKirim');
$routes->post('/farmasi/permintaan-unit/simpan-penjualan', 'Farmasi\PermintaanUnit::simpanPenjualan');
$routes->post('/farmasi/permintaan-unit/cetak', 'Farmasi\PermintaanUnit::cetak');

// Penyesuaian stok farmasi
$routes->get('/farmasi/penyesuaian-stok/referensi', 'Farmasi\PenyesuaianStok::referensi');
$routes->get('/farmasi/penyesuaian-stok/daftar', 'Farmasi\PenyesuaianStok::daftar');
$routes->get('/farmasi/penyesuaian-stok/obat', 'Farmasi\PenyesuaianStok::obat');
$routes->post('/farmasi/penyesuaian-stok/simpan', 'Farmasi\PenyesuaianStok::simpan');
$routes->post('/farmasi/penyesuaian-stok/tambah', 'Farmasi\PenyesuaianStok::tambah');
$routes->post('/farmasi/penyesuaian-stok/ed', 'Farmasi\PenyesuaianStok::ed');
$routes->post('/farmasi/penyesuaian-stok/sumber', 'Farmasi\PenyesuaianStok::sumber');
$routes->post('/farmasi/penyesuaian-stok/setting', 'Farmasi\PenyesuaianStok::setting');
$routes->post('/farmasi/penyesuaian-stok/cetak', 'Farmasi\PenyesuaianStok::cetak');
$routes->post('/farmasi/penyesuaian-stok/authorize', 'Farmasi\PenyesuaianStok::authorize');

// Penerimaan gabung TT
$routes->get('/farmasi/penerimaan-tt/referensi', 'Farmasi\PenerimaanTT::referensi');
$routes->get('/farmasi/penerimaan-tt/daftar', 'Farmasi\PenerimaanTT::daftar');
$routes->get('/farmasi/penerimaan-tt/detail', 'Farmasi\PenerimaanTT::detail');
$routes->get('/farmasi/penerimaan-tt/penerimaan', 'Farmasi\PenerimaanTT::penerimaan');
$routes->get('/farmasi/penerimaan-tt/rincian', 'Farmasi\PenerimaanTT::rincian');

$routes->post('/farmasi/penerimaan-tt/tampung', 'Farmasi\PenerimaanTT::tampung');
$routes->post('/farmasi/penerimaan-tt/hapus-item', 'Farmasi\PenerimaanTT::hapusItem');
$routes->post('/farmasi/penerimaan-tt/hapus', 'Farmasi\PenerimaanTT::hapus');
$routes->post('/farmasi/penerimaan-tt/tutup', 'Farmasi\PenerimaanTT::tutup');
$routes->post('/farmasi/penerimaan-tt/buka', 'Farmasi\PenerimaanTT::buka');
$routes->post('/farmasi/penerimaan-tt/cetak', 'Farmasi\PenerimaanTT::cetak');

// Farmasi - Mutasi
$routes->get('/farmasi/mutasi/referensi', 'Farmasi\Mutasi::referensi');
$routes->get('/farmasi/mutasi/daftar', 'Farmasi\Mutasi::daftar');
$routes->get('/farmasi/mutasi/detail', 'Farmasi\Mutasi::detail');
$routes->get('/farmasi/mutasi/obat', 'Farmasi\Mutasi::obat');
$routes->get('/farmasi/mutasi/order', 'Farmasi\Mutasi::order');
$routes->get('/farmasi/mutasi/order-detail', 'Farmasi\Mutasi::orderDetail');
$routes->get('/farmasi/mutasi/stok', 'Farmasi\Mutasi::stok');

$routes->post('/farmasi/mutasi/simpan', 'Farmasi\Mutasi::simpan');
$routes->post('/farmasi/mutasi/item', 'Farmasi\Mutasi::item');
$routes->post('/farmasi/mutasi/hapus-item', 'Farmasi\Mutasi::hapusItem');
$routes->post('/farmasi/mutasi/hapus', 'Farmasi\Mutasi::hapus');
$routes->post('/farmasi/mutasi/batal-approve', 'Farmasi\Mutasi::batalApprove');
$routes->post('/farmasi/mutasi/tutup', 'Farmasi\Mutasi::tutup');
$routes->post('/farmasi/mutasi/buka', 'Farmasi\Mutasi::buka');
$routes->post('/farmasi/mutasi/kirim', 'Farmasi\Mutasi::kirim');
$routes->post('/farmasi/mutasi/batal-kirim', 'Farmasi\Mutasi::batalKirim');
$routes->post('/farmasi/mutasi/ambil-order', 'Farmasi\Mutasi::ambilOrder');
$routes->post('/farmasi/mutasi/cetak', 'Farmasi\Mutasi::cetak');

$routes->post('farmasi/laporan_penerimaan/print', 'Laporan\Farmasi\LaporanPenerimaan::print');
$routes->post('farmasi/laporan_penerimaan/printExcel', 'Laporan\Farmasi\LaporanPenerimaan::printExcel');
$routes->get('farmasi/laporan_penerimaan/getPbf', 'Laporan\Farmasi\LaporanPenerimaan::getPbf');
$routes->get('farmasi/laporan_penerimaan/getType', 'Laporan\Farmasi\LaporanPenerimaan::getType');
$routes->get('farmasi/laporan_penerimaan/getUnit', 'Laporan\Farmasi\LaporanPenerimaan::getUnit');
$routes->get('farmasi/laporan_penerimaan/getGolongan', 'Laporan\Farmasi\LaporanPenerimaan::getGolongan');
$routes->get('farmasi/laporan_penerimaan/getObat', 'Laporan\Farmasi\LaporanPenerimaan::getObat');
$routes->get('farmasi/laporan_penerimaan/getSumberAnggaran', 'Laporan\Farmasi\LaporanPenerimaan::getSumberAnggaran');

$routes->post('farmasi/laporan_penjualan_obat/print','Laporan\Farmasi\LaporanPenjualanObat::print');
$routes->get('farmasi/laporan_penjualan_obat/getUnit', 'Laporan\Farmasi\LaporanPenjualanObat::getUnit');
$routes->get('farmasi/laporan_penjualan_obat/getShift', 'Laporan\Farmasi\LaporanPenjualanObat::getShift');
$routes->get('farmasi/laporan_penjualan_obat/getPenjamin', 'Laporan\Farmasi\LaporanPenjualanObat::getPenjamin');
$routes->get('farmasi/laporan_penjualan_obat/getCaraBayar', 'Laporan\Farmasi\LaporanPenjualanObat::getCaraBayar');
$routes->get('farmasi/laporan_penjualan_obat/getInstalasiAsal', 'Laporan\Farmasi\LaporanPenjualanObat::getInstalasiAsal');
$routes->get('farmasi/laporan_penjualan_obat/getUnitAsal', 'Laporan\Farmasi\LaporanPenjualanObat::getUnitAsal');

$routes->post('farmasi/laporan_pengeluaran_obat/print','Laporan\Farmasi\LaporanPengeluaranObat::print');
$routes->get('farmasi/laporan_pengeluaran_obat/getUnit', 'Laporan\Farmasi\LaporanPengeluaranObat::getUnit');
$routes->get('farmasi/laporan_pengeluaran_obat/getSumberAnggaran', 'Laporan\Farmasi\LaporanPengeluaranObat::getSumberAnggaran');
$routes->get('farmasi/laporan_pengeluaran_obat/getStatusObat', 'Laporan\Farmasi\LaporanPengeluaranObat::getStatusObat');
$routes->get('farmasi/laporan_pengeluaran_obat/getObat', 'Laporan\Farmasi\LaporanPengeluaranObat::getObat');

$routes->post('farmasi/laporan_stok_obat/print','Laporan\Farmasi\LaporanStokObat::print');
$routes->get('farmasi/laporan_stok_obat/getUnit', 'Laporan\Farmasi\LaporanStokObat::getUnit');
$routes->get('farmasi/laporan_stok_obat/getKepemilikan', 'Laporan\Farmasi\LaporanStokObat::getKepemilikan');
$routes->get('farmasi/laporan_stok_obat/getJenis', 'Laporan\Farmasi\LaporanStokObat::getJenis');
$routes->get('farmasi/laporan_stok_obat/getStatus', 'Laporan\Farmasi\LaporanStokObat::getStatus');
$routes->get('farmasi/laporan_stok_obat/getGolongan', 'Laporan\Farmasi\LaporanStokObat::getGolongan');
$routes->get('farmasi/laporan_stok_obat/getFormularium', 'Laporan\Farmasi\LaporanStokObat::getFormularium');
$routes->get('farmasi/laporan_stok_obat/getPabrik', 'Laporan\Farmasi\LaporanStokObat::getPabrik');
$routes->get('farmasi/laporan_stok_obat/getObat', 'Laporan\Farmasi\LaporanStokObat::getObat');

$routes->post('farmasi/laporan_detail_kartu_stok/print','Laporan\Farmasi\LaporanDetailKartuStok::print');
$routes->post('farmasi/laporan_detail_kartu_stok/print','Laporan\Farmasi\LaporanDetailKartuStok::print');
$routes->get('farmasi/laporan_detail_kartu_stok/getUnit','Laporan\Farmasi\LaporanDetailKartuStok::getUnit');
$routes->get('farmasi/laporan_detail_kartu_stok/getKepemilikan','Laporan\Farmasi\LaporanDetailKartuStok::getKepemilikan');
$routes->get('farmasi/laporan_detail_kartu_stok/getObat','Laporan\Farmasi\LaporanDetailKartuStok::getObat');

$routes->get('/farmasi/laporan_penyesuaian_obat/getUnit','Laporan\Farmasi\LaporanPenyesuaianObat::getUnit');
$routes->get('/farmasi/laporan_penyesuaian_obat/getSumberAnggaran','Laporan\Farmasi\LaporanPenyesuaianObat::getSumberAnggaran');
$routes->get('/farmasi/laporan_penyesuaian_obat/getPetugas','Laporan\Farmasi\LaporanPenyesuaianObat::getPetugas');
$routes->get('/farmasi/laporan_penyesuaian_obat/getRak','Laporan\Farmasi\LaporanPenyesuaianObat::getRak');
$routes->get('/farmasi/laporan_penyesuaian_obat/getAlasan','Laporan\Farmasi\LaporanPenyesuaianObat::getAlasan');
$routes->post('/farmasi/laporan_penyesuaian_obat/print','Laporan\Farmasi\LaporanPenyesuaianObat::print');

$routes->get('/farmasi/laporan_morphine/getObat','Laporan\Farmasi\LaporanMorphine::getObat');
$routes->get('/farmasi/laporan_morphine/getUnit','Laporan\Farmasi\LaporanMorphine::getUnit');
$routes->post('/farmasi/laporan_morphine/print','Laporan\Farmasi\LaporanMorphine::print');

$routes->get('/farmasi/laporan_narkotik_resume/getGolongan','Laporan\Farmasi\LaporanNarkotikResume::getGolongan');
$routes->get('/farmasi/laporan_narkotik_resume/getObat','Laporan\Farmasi\LaporanNarkotikResume::getObat');
$routes->get('/farmasi/laporan_narkotik_resume/getUnit','Laporan\Farmasi\LaporanNarkotikResume::getUnit');
$routes->post('/farmasi/laporan_narkotik_resume/print','Laporan\Farmasi\LaporanNarkotikResume::print');

$routes->get('/farmasi/laporan_obat_formularium/getUnit','Laporan\Farmasi\LaporanObatFormularium::getUnit');
$routes->get('/farmasi/laporan_obat_formularium/getFormula','Laporan\Farmasi\LaporanObatFormularium::getFormula');
$routes->get('/farmasi/laporan_obat_formularium/getMasterKategori','Laporan\Farmasi\LaporanObatFormularium::getMasterKategori');
$routes->post('/farmasi/laporan_obat_formularium/print','Laporan\Farmasi\LaporanObatFormularium::print');

$routes->get('/farmasi/laporan_obat_perpasien/getUnit','Laporan\Farmasi\LaporanObatPerPasien::getUnit');
$routes->get('/farmasi/laporan_obat_perpasien/getSumberAnggaran','Laporan\Farmasi\LaporanObatPerPasien::getSumberAnggaran');
$routes->get('/farmasi/laporan_obat_perpasien/getObat','Laporan\Farmasi\LaporanObatPerPasien::getObat');
$routes->post('/farmasi/laporan_obat_perpasien/print','Laporan\Farmasi\LaporanObatPerPasien::print');

$routes->post('/farmasi/laporan_penghapusan/print','Laporan\Farmasi\LaporanPenghapusan::print');

// Bedah sentral 
  // Master Status Operasi
$routes->post('bedah-sentral/master-status-operasi/simpan','BedahSentral\MasterStatusOperasi::simpan');
$routes->post('bedah-sentral/master-status-operasi/hapus','BedahSentral\MasterStatusOperasi::hapus');
$routes->get('bedah-sentral/master-status-operasi/kodeUrutStatusOperasi','BedahSentral\MasterStatusOperasi::kodeUrutStatusOperasi');
$routes->get('bedah-sentral/master-status-operasi/load','BedahSentral\MasterStatusOperasi::load');
  // Master Jenis Operasi
$routes->get('bedah-sentral/master-jenis-operasi/load','BedahSentral\MasterJenisOperasi::load');
$routes->post('bedah-sentral/master-jenis-operasi/simpan','BedahSentral\MasterJenisOperasi::simpan');
$routes->post('bedah-sentral/master-jenis-operasi/hapus','BedahSentral\MasterJenisOperasi::hapus');
$routes->get('bedah-sentral/master-jenis-operasi/kodeUrutJenisOperasi','BedahSentral\MasterJenisOperasi::kodeUrutJenisOperasi');
  // Master Sub Jenis Operasi
$routes->get('bedah-sentral/master-subjenis-operasi/load','BedahSentral\MasterSubJenisOperasi::load');
$routes->post('bedah-sentral/master-subjenis-operasi/simpan','BedahSentral\MasterSubJenisOperasi::simpan');
$routes->post('bedah-sentral/master-subjenis-operasi/hapus','BedahSentral\MasterSubJenisOperasi::hapus');
$routes->get('bedah-sentral/master-subjenis-operasi/kodeUrutSubJenisOperasi','BedahSentral\MasterSubJenisOperasi::kodeUrutSubJenisOperasi');
  // Master Kamar Operasi
$routes->post('bedah-sentral/master-kamar-operasi/simpan','BedahSentral\MasterKamarOperasi::simpan');
$routes->post('bedah-sentral/master-kamar-operasi/hapus','BedahSentral\MasterKamarOperasi::hapus');
$routes->get('bedah-sentral/master-kamar-operasi/kodeUrutKamarOperasi','BedahSentral\MasterKamarOperasi::kodeUrutKamarOperasi');
$routes->get('bedah-sentral/master-kamar-operasi/load','BedahSentral\MasterKamarOperasi::load');
  // Master Meja Operasi
$routes->get('bedah-sentral/master-meja-operasi/load','BedahSentral\MasterMejaOperasi::load');
$routes->post('bedah-sentral/master-meja-operasi/simpan','BedahSentral\MasterMejaOperasi::simpan');
$routes->post('bedah-sentral/master-meja-operasi/hapus','BedahSentral\MasterMejaOperasi::hapus');
$routes->get('bedah-sentral/master-meja-operasi/kodeUrutMejaOperasi','BedahSentral\MasterMejaOperasi::kodeUrutMejaOperasi');
  // Master Tindakan Item
$routes->get('bedah-sentral/master-tindakan-item/load','BedahSentral\MasterTindakanItem::load');
$routes->post('bedah-sentral/master-tindakan-item/simpan','BedahSentral\MasterTindakanItem::simpan');
$routes->post('bedah-sentral/master-tindakan-item/hapus','BedahSentral\MasterTindakanItem::hapus');
$routes->get('bedah-sentral/master-tindakan-item/kode-urut','BedahSentral\MasterTindakanItem::kodeUrut');

// Master lab
  // Master barang
$routes->get('laboratorium/master-barang/load','Lab\MasterBarang::load');
$routes->post('laboratorium/master-barang/save','Lab\MasterBarang::save');
$routes->post('laboratorium/master-barang/delete','Lab\MasterBarang::delete');
$routes->get('laboratorium/master-barang/kode-urut','Lab\MasterBarang::kodeUrut');
$routes->get('laboratorium/master-barang/golongan','Lab\MasterBarang::golongan');
$routes->get('laboratorium/master-barang/satuan','Lab\MasterBarang::satuan');
  // Lab Item Pemeriksaan
$routes->get('laboratorium/lab-item-pemeriksaan/load','Lab\LabItemPemeriksaan::load');
$routes->get('laboratorium/lab-item-pemeriksaan/get-id','Lab\LabItemPemeriksaan::id');
$routes->get('laboratorium/lab-item-pemeriksaan/get-metode','Lab\LabItemPemeriksaan::metode');
$routes->get('laboratorium/lab-item-pemeriksaan/get-kelamin','Lab\LabItemPemeriksaan::kelamin');
$routes->get('laboratorium/lab-item-pemeriksaan/item-pemeriksaan','Lab\LabItemPemeriksaan::itemPemeriksaan');
$routes->post('laboratorium/lab-item-pemeriksaan/save','Lab\LabItemPemeriksaan::save');
$routes->post('laboratorium/lab-item-pemeriksaan/delete','Lab\LabItemPemeriksaan::delete');
$routes->post('laboratorium/lab-item-pemeriksaan/check-delete','Lab\LabItemPemeriksaan::checkDelete');
$routes->get('laboratorium/lab-item-pemeriksaan/check-user-access','Lab\LabItemPemeriksaan::checkUserAccess');
$routes->post('laboratorium/lab-item-pemeriksaan/check-duplicate-range','Lab\LabItemPemeriksaan::checkDuplicateRange');
$routes->post('laboratorium/lab-item-pemeriksaan/approve','Lab\LabItemPemeriksaan::approve');
$routes->post('laboratorium/lab-item-pemeriksaan/cancel-approve','Lab\LabItemPemeriksaan::cancelApprove');

// Lab Item Pemeriksaan detail

$routes->get('/laboratorium/lab_item_pemeriksaan_detail/load', 'Lab\LabItemPemeriksaanDetail::load');
$routes->get('/laboratorium/lab_item_pemeriksaan_detail/tindakan', 'Lab\LabItemPemeriksaanDetail::tindakan');
$routes->get('/laboratorium/lab_item_pemeriksaan_detail/items', 'Lab\LabItemPemeriksaanDetail::items');
$routes->get('/laboratorium/lab_item_pemeriksaan_detail/subgroups', 'Lab\LabItemPemeriksaanDetail::subgroups');
$routes->get('/laboratorium/lab_item_pemeriksaan_detail/bahan', 'Lab\LabItemPemeriksaanDetail::bahan');

$routes->post('/laboratorium/lab_item_pemeriksaan_detail/save', 'Lab\LabItemPemeriksaanDetail::save');
$routes->post('/laboratorium/lab_item_pemeriksaan_detail/delete', 'Lab\LabItemPemeriksaanDetail::delete');

$routes->get('/laboratorium/hasil_pemeriksaan/samples', 'Lab\HasilPemeriksaanLaboratorium::samples');
$routes->get('/laboratorium/hasil_pemeriksaan/transaksi', 'Lab\HasilPemeriksaanLaboratorium::transaksi');
$routes->get('/laboratorium/hasil_pemeriksaan/hasil', 'Lab\HasilPemeriksaanLaboratorium::hasil');
$routes->get('/laboratorium/hasil_pemeriksaan/paramedis', 'Lab\HasilPemeriksaanLaboratorium::paramedis');

$routes->post('/laboratorium/hasil_pemeriksaan/save', 'Lab\HasilPemeriksaanLaboratorium::save');
$routes->post('/laboratorium/hasil_pemeriksaan/approval', 'Lab\HasilPemeriksaanLaboratorium::approval');
$routes->post('/laboratorium/hasil_pemeriksaan/status_cetak', 'Lab\HasilPemeriksaanLaboratorium::statusCetak');
$routes->post('/laboratorium/hasil_pemeriksaan/kirim', 'Lab\HasilPemeriksaanLaboratorium::kirim');
$routes->post('/laboratorium/hasil_pemeriksaan/batal_kirim', 'Lab\HasilPemeriksaanLaboratorium::batalKirim');
$routes->post('/laboratorium/hasil_pemeriksaan/hasil_alat', 'Lab\HasilPemeriksaanLaboratorium::hasilAlat');

$routes->post('/laboratorium/hasil_pemeriksaan/print', 'Lab\HasilPemeriksaanLaboratorium::print');

$routes->match(['get', 'post'], '/laboratorium/hasil_pemeriksaan/kultur', 'Lab\HasilPemeriksaanLaboratorium::kultur');
$routes->post('/laboratorium/hasil_pemeriksaan/print_bebas_narkoba', 'Lab\HasilPemeriksaanLaboratorium::printBebasNarkoba');
$routes->post('/laboratorium/hasil_pemeriksaan/kirim_wa', 'Lab\HasilPemeriksaanLaboratorium::kirimWa');

$routes->get('/laboratorium/history_pemeriksaan/load','Lab\HistoryPemeriksaan::load');
$routes->get('/laboratorium/history_pemeriksaan/pasien','Lab\HistoryPemeriksaan::pasien');
$routes->post('/laboratorium/history_pemeriksaan/print','Lab\HistoryPemeriksaan::print');

$routes->get('/radiologi/history_pemeriksaan/load','Radiologi\HistoryPemeriksaan::load');
$routes->get('/radiologi/history_pemeriksaan/pasien','Radiologi\HistoryPemeriksaan::pasien');
$routes->post('/radiologi/history_pemeriksaan/print','Radiologi\HistoryPemeriksaan::print');

// Master Vendor
$routes->get('/laboratorium/master-vendor/load', 'Lab\MasterVendor::load');
$routes->post('/laboratorium/master-vendor/save', 'Lab\MasterVendor::save');
$routes->post('/laboratorium/master-vendor/delete', 'Lab\MasterVendor::delete');
$routes->get('/laboratorium/master-vendor/getPropinsi', 'Lab\MasterVendor::getPropinsi');
$routes->get('/laboratorium/master-vendor/getKabupaten', 'Lab\MasterVendor::getKabupaten');
$routes->get('/laboratorium/master-vendor/getKecamatan', 'Lab\MasterVendor::getKecamatan');

// Master Kuman
$routes->get('/laboratorium/master-kuman/load', 'Lab\MasterKuman::load');
$routes->post('/laboratorium/master-kuman/save', 'Lab\MasterKuman::save');

// Master Antibioteik
$routes->get('/laboratorium/master-antibiotik/load', 'Lab\MasterAntibiotik::load');
$routes->post('/laboratorium/master-antibiotik/save', 'Lab\MasterAntibiotik::save');

//pindah ruang
$routes->get('pindah_ruang/loadInstalasi','PindahRuang::loadInstalasi');

// Rencana pengadaan
$routes->get('/farmasi/rencana-pengadaan/referensi', 'Farmasi\RencanaPengadaan::referensi');
$routes->get('/farmasi/rencana-pengadaan/daftar', 'Farmasi\RencanaPengadaan::daftar');
$routes->get('/farmasi/rencana-pengadaan/detail', 'Farmasi\RencanaPengadaan::detail');
$routes->get('/farmasi/rencana-pengadaan/obat', 'Farmasi\RencanaPengadaan::obat');
$routes->get('/farmasi/rencana-pengadaan/estimasi', 'Farmasi\RencanaPengadaan::estimasi');
$routes->get('/farmasi/rencana-pengadaan/kebutuhan', 'Farmasi\RencanaPengadaan::kebutuhan');
$routes->post('/farmasi/rencana-pengadaan/item', 'Farmasi\RencanaPengadaan::item');
$routes->post('/farmasi/rencana-pengadaan/edit-item', 'Farmasi\RencanaPengadaan::editItem');
$routes->post('/farmasi/rencana-pengadaan/hapus-item', 'Farmasi\RencanaPengadaan::hapusItem');
$routes->post('/farmasi/rencana-pengadaan/update', 'Farmasi\RencanaPengadaan::update');
$routes->post('/farmasi/rencana-pengadaan/hapus', 'Farmasi\RencanaPengadaan::hapus');
$routes->post('/farmasi/rencana-pengadaan/kirim', 'Farmasi\RencanaPengadaan::kirim');
$routes->post('/farmasi/rencana-pengadaan/batal-kirim', 'Farmasi\RencanaPengadaan::batalKirim');
$routes->post('/farmasi/rencana-pengadaan/impor-kebutuhan', 'Farmasi\RencanaPengadaan::imporKebutuhan');
$routes->get('/farmasi/rencana-pengadaan/kekurangan-sp','Farmasi\RencanaPengadaan::kekuranganSp');
$routes->post('/farmasi/rencana-pengadaan/impor-kekurangan-sp','Farmasi\RencanaPengadaan::imporKekuranganSp');
// Cetak Rencana Pengadaan
$routes->post('/farmasi/rencana-pengadaan/cetak','Farmasi\RencanaPengadaan::cetak');

// ============================================================
// Farmasi - Pelayanan Resep
// ============================================================

// Referensi / master
$routes->get('/farmasi/pelayanan-resep/referensi', 'Farmasi\PelayananResep::referensi');
$routes->get('/farmasi/pelayanan-resep/obat', 'Farmasi\PelayananResep::obat');

// Detail pelayanan resep
$routes->get('/farmasi/pelayanan-resep/detail', 'Farmasi\PelayananResep::detail');

// Pencarian resep / pasien / order
$routes->get('/farmasi/pelayanan-resep/cari-resep', 'Farmasi\PelayananResep::cariResep');
$routes->get('/farmasi/pelayanan-resep/cari-pasien', 'Farmasi\PelayananResep::cariPasien');
$routes->get('/farmasi/pelayanan-resep/cari-order', 'Farmasi\PelayananResep::cariOrder');

// Data pasien yang dipilih
$routes->get('/farmasi/pelayanan-resep/pasien', 'Farmasi\PelayananResep::pasien');

// Order obat
$routes->get('/farmasi/pelayanan-resep/order-detail', 'Farmasi\PelayananResep::orderDetail');
$routes->get('/farmasi/pelayanan-resep/order-matching', 'Farmasi\PelayananResep::orderMatching');
$routes->post('/farmasi/pelayanan-resep/import-order', 'Farmasi\PelayananResep::importOrder');

// Item resep
$routes->post('/farmasi/pelayanan-resep/item', 'Farmasi\PelayananResep::item');
$routes->post('/farmasi/pelayanan-resep/hapus-item', 'Farmasi\PelayananResep::hapusItem');

// Racikan
$routes->get('/farmasi/pelayanan-resep/racikan', 'Farmasi\PelayananResep::racikan');
$routes->post('/farmasi/pelayanan-resep/simpan-racikan', 'Farmasi\PelayananResep::simpanRacikan');
$routes->post('/farmasi/pelayanan-resep/hapus-racikan', 'Farmasi\PelayananResep::hapusRacikan');

// Telaah resep
$routes->get('/farmasi/pelayanan-resep/telaah', 'Farmasi\PelayananResep::telaah');
$routes->post('/farmasi/pelayanan-resep/simpan-telaah', 'Farmasi\PelayananResep::simpanTelaah');

// Tutup / buka transaksi dan stok
$routes->post('/farmasi/pelayanan-resep/tutup', 'Farmasi\PelayananResep::tutup');
$routes->post('/farmasi/pelayanan-resep/buka', 'Farmasi\PelayananResep::buka');

// Shift
$routes->post('/farmasi/pelayanan-resep/tutup-shift', 'Farmasi\PelayananResep::tutupShift');

// Hapus transaksi
$routes->post('/farmasi/pelayanan-resep/hapus-transaksi', 'Farmasi\PelayananResep::hapusTransaksi');

// Master aturan & waktu pakai
$routes->post('/farmasi/pelayanan-resep/tambah-aturan', 'Farmasi\PelayananResep::tambahAturan');
$routes->post('/farmasi/pelayanan-resep/tambah-waktu', 'Farmasi\PelayananResep::tambahWaktu');

// Cetak
$routes->post('/farmasi/pelayanan-resep/cetak', 'Farmasi\PelayananResep::cetak');

$routes->get('/farmasi/pelayanan-resep/template', 'Farmasi\PelayananResep::template');
$routes->get('/farmasi/pelayanan-resep/template-obat', 'Farmasi\PelayananResep::templateObat');
$routes->get('/farmasi/pelayanan-resep/template', 'Farmasi\PelayananResep::template');
$routes->get('/farmasi/pelayanan-resep/template-obat', 'Farmasi\PelayananResep::templateObat');
$routes->get('/farmasi/pelayanan-resep/nomor-antrian', 'Farmasi\PelayananResep::nomorAntrian');
$routes->get('/farmasi/pelayanan-resep/history-obat', 'Farmasi\PelayananResep::historyObat');
$routes->get('/farmasi/pelayanan-resep/history-lab', 'Farmasi\PelayananResep::historyLab');

$routes->get('/farmasi/pelayanan-resep/history-cpo', 'Farmasi\PelayananResep::historyCpo');
$routes->post('/farmasi/pelayanan-resep/ubah-item', 'Farmasi\PelayananResep::ubahItem');
$routes->post('/farmasi/pelayanan-resep/pindah-order', 'Farmasi\PelayananResep::pindahOrder');
$routes->post('/farmasi/pelayanan-resep/cetak-order', 'Farmasi\PelayananResep::cetakOrder');
$routes->get('/farmasi/pelayanan-resep/total-order', 'Farmasi\PelayananResep::totalOrder');

// Farmasi - Pengembalian Resep
$routes->get('/farmasi/pengembalian-resep/referensi', 'Farmasi\PengembalianResep::referensi');
$routes->get('/farmasi/pengembalian-resep/kunjungan', 'Farmasi\PengembalianResep::kunjungan');
$routes->get('/farmasi/pengembalian-resep/daftar', 'Farmasi\PengembalianResep::daftar');
$routes->get('/farmasi/pengembalian-resep/detail', 'Farmasi\PengembalianResep::detail');
$routes->get('/farmasi/pengembalian-resep/obat', 'Farmasi\PengembalianResep::obat');

$routes->post('/farmasi/pengembalian-resep/simpan', 'Farmasi\PengembalianResep::simpan');
$routes->post('/farmasi/pengembalian-resep/item', 'Farmasi\PengembalianResep::item');
$routes->post('/farmasi/pengembalian-resep/ubah-item', 'Farmasi\PengembalianResep::ubahItem');
$routes->post('/farmasi/pengembalian-resep/hapus-item', 'Farmasi\PengembalianResep::hapusItem');
$routes->post('/farmasi/pengembalian-resep/hapus', 'Farmasi\PengembalianResep::hapus');
$routes->post('/farmasi/pengembalian-resep/tutup', 'Farmasi\PengembalianResep::tutup');
$routes->post('/farmasi/pengembalian-resep/buka', 'Farmasi\PengembalianResep::buka');
$routes->post('/farmasi/pengembalian-resep/cetak', 'Farmasi\PengembalianResep::cetak');

$routes->get('/farmasi/farmasi-klinis/referensi', 'Farmasi\FarmasiKlinis::referensi');
$routes->get('/farmasi/farmasi-klinis/kunjungan', 'Farmasi\FarmasiKlinis::kunjungan');
$routes->get('/farmasi/farmasi-klinis/detail-kunjungan', 'Farmasi\FarmasiKlinis::detailKunjungan');
$routes->get('/farmasi/farmasi-klinis/tindakan', 'Farmasi\FarmasiKlinis::tindakan');
$routes->get('/farmasi/farmasi-klinis/detail', 'Farmasi\FarmasiKlinis::detail');
$routes->get('/farmasi/farmasi-klinis/komponen', 'Farmasi\FarmasiKlinis::komponen');
$routes->get('/farmasi/farmasi-klinis/pelaksana', 'Farmasi\FarmasiKlinis::pelaksana');
$routes->get('/farmasi/farmasi-klinis/paramedis', 'Farmasi\FarmasiKlinis::paramedis');
$routes->get('/farmasi/farmasi-klinis/history', 'Farmasi\FarmasiKlinis::history');

$routes->post('/farmasi/farmasi-klinis/simpan', 'Farmasi\FarmasiKlinis::simpan');
$routes->post('/farmasi/farmasi-klinis/tambah-tindakan', 'Farmasi\FarmasiKlinis::tambahTindakan');
$routes->post('/farmasi/farmasi-klinis/ubah-tindakan', 'Farmasi\FarmasiKlinis::ubahTindakan');
$routes->post('/farmasi/farmasi-klinis/hapus-tindakan', 'Farmasi\FarmasiKlinis::hapusTindakan');
$routes->post('/farmasi/farmasi-klinis/simpan-pelaksana', 'Farmasi\FarmasiKlinis::simpanPelaksana');
$routes->post('/farmasi/farmasi-klinis/hapus', 'Farmasi\FarmasiKlinis::hapus');
$routes->post('/farmasi/farmasi-klinis/cetak', 'Farmasi\FarmasiKlinis::cetak');

// Farmasi - Pemanggil Antrian
$routes->get('/farmasi/pemanggil-antrian/referensi', 'Farmasi\PemanggilAntrian::referensi');
$routes->get('/farmasi/pemanggil-antrian/daftar', 'Farmasi\PemanggilAntrian::daftar');
$routes->get('/farmasi/pemanggil-antrian/kelompok', 'Farmasi\PemanggilAntrian::kelompok');
$routes->get('/farmasi/pemanggil-antrian/nomor-antrian', 'Farmasi\PemanggilAntrian::nomorAntrian');

$routes->post('/farmasi/pemanggil-antrian/update-antrian', 'Farmasi\PemanggilAntrian::updateAntrian');
$routes->post('/farmasi/pemanggil-antrian/panggil', 'Farmasi\PemanggilAntrian::panggil');
$routes->post('/farmasi/pemanggil-antrian/penyerahan', 'Farmasi\PemanggilAntrian::penyerahan');
$routes->post('/farmasi/pemanggil-antrian/batal-penyerahan', 'Farmasi\PemanggilAntrian::batalPenyerahan');
$routes->post('/farmasi/pemanggil-antrian/panggil-penyerahan', 'Farmasi\PemanggilAntrian::panggilPenyerahan');

$routes->get('/farmasi/penghapusan/referensi', 'Farmasi\Penghapusan::referensi');
$routes->get('/farmasi/penghapusan/obat', 'Farmasi\Penghapusan::obat');
$routes->get('/farmasi/penghapusan/daftar-item', 'Farmasi\Penghapusan::daftarItem');
$routes->get('/farmasi/penghapusan/cari', 'Farmasi\Penghapusan::cari');
$routes->get('/farmasi/penghapusan/detail', 'Farmasi\Penghapusan::detail');
$routes->post('/farmasi/penghapusan/tampung', 'Farmasi\Penghapusan::tampung');
$routes->post('/farmasi/penghapusan/hapus-item', 'Farmasi\Penghapusan::hapusItem');
$routes->post('/farmasi/penghapusan/hapus', 'Farmasi\Penghapusan::hapus');
$routes->post('/farmasi/penghapusan/tutup', 'Farmasi\Penghapusan::tutup');
$routes->post('/farmasi/penghapusan/buka', 'Farmasi\Penghapusan::buka');

$routes->get('/farmasi/info-stok-harga/referensi', 'Farmasi\InfoStokHarga::referensi');
$routes->get('/farmasi/info-stok-harga/daftar', 'Farmasi\InfoStokHarga::daftar');
$routes->get('/farmasi/info-stok-harga/obat', 'Farmasi\InfoStokHarga::obat');
$routes->post('/farmasi/info-stok-harga/simpan', 'Farmasi\InfoStokHarga::simpan');
$routes->post('/farmasi/info-stok-harga/tambah', 'Farmasi\InfoStokHarga::tambah');
$routes->post('/farmasi/info-stok-harga/ed', 'Farmasi\InfoStokHarga::ed');
$routes->post('/farmasi/info-stok-harga/sumber', 'Farmasi\InfoStokHarga::sumber');
$routes->post('/farmasi/info-stok-harga/setting', 'Farmasi\InfoStokHarga::setting');
$routes->post('/farmasi/info-stok-harga/authorize', 'Farmasi\InfoStokHarga::authorize');



// Registrasi Pemeriksaan Laboratorium
// Init & lookup
$routes->get('/lab/registrasi/init', 'Lab\RegistrasiPemeriksaan::init');
$routes->get('/lab/registrasi/penjamin', 'Lab\RegistrasiPemeriksaan::penjamin');
$routes->get('/lab/registrasi/dokter', 'Lab\RegistrasiPemeriksaan::dokter');
$routes->get('/lab/registrasi/dokter-pj', 'Lab\RegistrasiPemeriksaan::dokterPJ');
$routes->get('/lab/registrasi/cara-masuk', 'Lab\RegistrasiPemeriksaan::caraMasuk');
$routes->get('/lab/registrasi/jenis-kelamin', 'Lab\RegistrasiPemeriksaan::jenisKelamin');
$routes->get('/lab/registrasi/unit', 'Lab\RegistrasiPemeriksaan::unit');
$routes->get('/lab/registrasi/kunjungan', 'Lab\RegistrasiPemeriksaan::kunjungan');
$routes->get('/lab/registrasi/kunjungan/detail', 'Lab\RegistrasiPemeriksaan::detailKunjungan');
$routes->get('/lab/registrasi/order', 'Lab\RegistrasiPemeriksaan::order');
$routes->get('/lab/registrasi/order/detail', 'Lab\RegistrasiPemeriksaan::detailOrder');
$routes->get('/lab/registrasi/tindakan', 'Lab\RegistrasiPemeriksaan::tindakan');
$routes->get('/lab/registrasi/tindakan-transaksi', 'Lab\RegistrasiPemeriksaan::tindakanTransaksi');
$routes->post('/lab/registrasi/tindakan/simpan', 'Lab\RegistrasiPemeriksaan::simpanTindakan');
$routes->post('/lab/registrasi/tindakan/hapus', 'Lab\RegistrasiPemeriksaan::hapusTindakan');
$routes->post('/lab/registrasi/order/layani', 'Lab\RegistrasiPemeriksaan::layaniOrder');
$routes->get('/lab/registrasi/alasan-batal','Lab\RegistrasiPemeriksaan::alasanBatal');
$routes->post('/lab/registrasi/simpan', 'Lab\RegistrasiPemeriksaan::simpan');
$routes->post('/lab/registrasi/hapus', 'Lab\RegistrasiPemeriksaan::hapus');

$routes->get('/lab/registrasi/spesimen', 'Lab\RegistrasiPemeriksaan::spesimen');
$routes->post('/lab/registrasi/spesimen/simpan', 'Lab\RegistrasiPemeriksaan::simpanSpesimen');
$routes->post('/lab/registrasi/spesimen/hapus', 'Lab\RegistrasiPemeriksaan::hapusSpesimen');
$routes->get('/lab/registrasi/history', 'Lab\RegistrasiPemeriksaan::history');
$routes->get('/lab/registrasi/cara-masuk-nama', 'Lab\RegistrasiPemeriksaan::caraMasukNama');
$routes->get('/lab/registrasi/cara-masuk-kunjungan', 'Lab\RegistrasiPemeriksaan::caraMasukKunjungan');
// LI
$routes->post('/lab/registrasi/lis/order', 'Lab\RegistrasiPemeriksaan::orderLIS');
$routes->post('/lab/registrasi/lis/batal', 'Lab\RegistrasiPemeriksaan::batalOrderLIS');
$routes->post('/lab/registrasi/default-setting', 'Lab\RegistrasiPemeriksaan::saveDefaultSetting');
$routes->get('/lab/registrasi/tindakan/komponen','Lab\RegistrasiPemeriksaan::detailKomponenTindakan');
$routes->get('/lab/registrasi/tindakan/pelaksana', 'Lab\RegistrasiPemeriksaan::pelaksanaKomponen');
$routes->get('/lab/registrasi/tindakan/pilihan-pelaksana', 'Lab\RegistrasiPemeriksaan::pilihanPelaksana');
$routes->post('/lab/registrasi/tindakan/pelaksana/simpan', 'Lab\RegistrasiPemeriksaan::simpanPelaksanaKomponen');
$routes->post('/lab/registrasi/tindakan/pelaksana/hapus','Lab\RegistrasiPemeriksaan::hapusPelaksanaKomponen');

 
// Cetak
$routes->post('/lab/registrasi/cetak', 'Lab\RegistrasiPemeriksaan::cetak');
$routes->get('/lab/registrasi/history', 'Lab\RegistrasiPemeriksaan::history');
$routes->get('/lab/registrasi/order/count', 'Lab\RegistrasiPemeriksaan::countOrder');
$routes->get('/lab/registrasi/transaksi/cari', 'Lab\RegistrasiPemeriksaan::cariTransaksi');

// lis
$routes->get('/lab/lis-lab/kunjungan',       'Lab\LisLab::kunjungan');
$routes->get('/lab/lis-lab/pengambilan',     'Lab\LisLab::pengambilan');
$routes->get('/lab/lis-lab/penerimaan',      'Lab\LisLab::penerimaan');
$routes->get('/lab/lis-lab/sampel',          'Lab\LisLab::sampel');
$routes->get('/lab/lis-lab/pemeriksaan',     'Lab\LisLab::pemeriksaan');
$routes->get('/lab/lis-lab/bhp',             'Lab\LisLab::bhp');
$routes->get('/lab/lis-lab/hasil',           'Lab\LisLab::hasil');
$routes->get('/lab/lis-lab/default-setting', 'Lab\LisLab::defaultSetting');
$routes->post('/lab/lis-lab/ambil',           'Lab\LisLab::ambil');
$routes->post('/lab/lis-lab/batal-ambil',     'Lab\LisLab::batalAmbil');
$routes->post('/lab/lis-lab/terima',          'Lab\LisLab::terima');
$routes->post('/lab/lis-lab/batal-terima',    'Lab\LisLab::batalTerima');
$routes->post('/lab/lis-lab/proses',          'Lab\LisLab::proses');
$routes->post('/lab/lis-lab/batal-proses',    'Lab\LisLab::batalProses');
$routes->post('/lab/lis-lab/edit-sample',     'Lab\LisLab::editSample');
$routes->post('/lab/lis-lab/delete-sample',   'Lab\LisLab::deleteSample');
$routes->post('/lab/lis-lab/default-setting', 'Lab\LisLab::saveDefaultSetting');


$routes->get('/radiologi/registrasi/default-setting', 'Radiologi\RegistrasiPemeriksaan::defaultSetting');
$routes->post('/radiologi/registrasi/default-setting', 'Radiologi\RegistrasiPemeriksaan::saveDefaultSetting');
$routes->get('/radiologi/registrasi/hasil', 'Radiologi\RegistrasiPemeriksaan::hasil');
$routes->get('/radiologi/registrasi/total', 'Radiologi\RegistrasiPemeriksaan::total');
$routes->get('/radiologi/registrasi/riwayat-transaksi', 'Radiologi\RegistrasiPemeriksaan::riwayatTransaksi');

// Registrasi radiologi CI4 (rad_registrasi_pemeriksaan_daricodex.html).
$routes->get('/radiologi/registrasi/init', 'Radiologi\RegistrasiPemeriksaan::init');
$routes->get('/radiologi/registrasi/lookup', 'Radiologi\RegistrasiPemeriksaan::lookup');
$routes->get('/radiologi/registrasi/kunjungan', 'Radiologi\RegistrasiPemeriksaan::kunjungan');
$routes->get('/radiologi/registrasi/kunjungan/detail', 'Radiologi\RegistrasiPemeriksaan::detailKunjungan');
$routes->get('/radiologi/registrasi/order', 'Radiologi\RegistrasiPemeriksaan::order');
$routes->get('/radiologi/registrasi/order/detail', 'Radiologi\RegistrasiPemeriksaan::detailOrder');
$routes->post('/radiologi/registrasi/order/layani', 'Radiologi\RegistrasiPemeriksaan::layaniOrder');
$routes->get('/radiologi/registrasi/transaksi', 'Radiologi\RegistrasiPemeriksaan::transaksi');
$routes->post('/radiologi/registrasi/simpan', 'Radiologi\RegistrasiPemeriksaan::simpan');
$routes->post('/radiologi/registrasi/hapus', 'Radiologi\RegistrasiPemeriksaan::hapus');
$routes->get('/radiologi/registrasi/tindakan', 'Radiologi\RegistrasiPemeriksaan::tindakan');
$routes->get('/radiologi/registrasi/tindakan-transaksi', 'Radiologi\RegistrasiPemeriksaan::tindakanTransaksi');
$routes->post('/radiologi/registrasi/tindakan/simpan', 'Radiologi\RegistrasiPemeriksaan::simpanTindakan');
$routes->post('/radiologi/registrasi/tindakan/hapus', 'Radiologi\RegistrasiPemeriksaan::hapusTindakan');
$routes->get('/radiologi/registrasi/tindakan/komponen', 'Radiologi\RegistrasiPemeriksaan::komponen');
$routes->get('/radiologi/registrasi/film', 'Radiologi\RegistrasiPemeriksaan::film');
$routes->post('/radiologi/registrasi/film/simpan', 'Radiologi\RegistrasiPemeriksaan::simpanFilm');
$routes->post('/radiologi/registrasi/cetak', 'Radiologi\RegistrasiPemeriksaan::cetak');
$routes->get('/radiologi/registrasi/history', 'Radiologi\RegistrasiPemeriksaan::history');
$routes->get('/radiologi/registrasi/pasien', 'Radiologi\RegistrasiPemeriksaan::pasien');
$routes->get('/radiologi/registrasi/details', 'Radiologi\RegistrasiPemeriksaan::details');
$routes->post('/radiologi/registrasi/details/simpan', 'Radiologi\RegistrasiPemeriksaan::simpanDetails');
$routes->get('/radiologi/registrasi/dokter/history', 'Radiologi\RegistrasiPemeriksaan::historyDokter');
$routes->post('/radiologi/registrasi/dokter/update', 'Radiologi\RegistrasiPemeriksaan::updateDokter');
$routes->get('/radiologi/registrasi/rujukan', 'Radiologi\RegistrasiPemeriksaan::rujukan');
$routes->post('/radiologi/registrasi/rujukan/simpan', 'Radiologi\RegistrasiPemeriksaan::simpanRujukan');
$routes->post('/radiologi/registrasi/cara-masuk/update', 'Radiologi\RegistrasiPemeriksaan::updateCaraMasuk');
$routes->get('/radiologi/registrasi/pelaksana', 'Radiologi\RegistrasiPemeriksaan::pelaksana');
$routes->post('/radiologi/registrasi/pelaksana/simpan', 'Radiologi\RegistrasiPemeriksaan::simpanPelaksana');
$routes->post('/radiologi/registrasi/pelaksana/hapus', 'Radiologi\RegistrasiPemeriksaan::hapusPelaksana');
$routes->get('/radiologi/registrasi/cito', 'Radiologi\RegistrasiPemeriksaan::cito');
$routes->post('/radiologi/registrasi/cito/simpan', 'Radiologi\RegistrasiPemeriksaan::simpanCito');
$routes->post('/radiologi/registrasi/cito/batch', 'Radiologi\RegistrasiPemeriksaan::simpanCitoBatch');
$routes->post('/radiologi/registrasi/tindakan/edit', 'Radiologi\RegistrasiPemeriksaan::editTindakan');
$routes->get('/radiologi/registrasi/status', 'Radiologi\RegistrasiPemeriksaan::status');
$routes->get('/radiologi/registrasi/film/stok', 'Radiologi\RegistrasiPemeriksaan::stokFilm');
$routes->post('/radiologi/registrasi/film/hapus', 'Radiologi\RegistrasiPemeriksaan::hapusFilm');
$routes->get('/radiologi/registrasi/bhp/mapping', 'Radiologi\RegistrasiPemeriksaan::mappingBhp');
$routes->post('/radiologi/registrasi/transaksi/tutup', 'Radiologi\RegistrasiPemeriksaan::tutup');
$routes->post('/radiologi/registrasi/transaksi/buka', 'Radiologi\RegistrasiPemeriksaan::buka');
$routes->post('/radiologi/registrasi/transfer', 'Radiologi\RegistrasiPemeriksaan::transfer');
$routes->post('/radiologi/registrasi/transfer/batal', 'Radiologi\RegistrasiPemeriksaan::batalTransfer');
$routes->get('/radiologi/registrasi/order/count', 'Radiologi\RegistrasiPemeriksaan::countOrder');
$routes->post('/radiologi/registrasi/order/balasan', 'Radiologi\RegistrasiPemeriksaan::balasan');
$routes->get('/radiologi/registrasi/antrian', 'Radiologi\RegistrasiPemeriksaan::antrian');
$routes->post('/radiologi/registrasi/antrian/panggil', 'Radiologi\RegistrasiPemeriksaan::panggil');
$routes->post('/radiologi/registrasi/acc/generate', 'Radiologi\RegistrasiPemeriksaan::generateAcc');
$routes->post('/radiologi/registrasi/tarif/copy', 'Radiologi\RegistrasiPemeriksaan::copyTarif');
$routes->post('/radiologi/registrasi/ris/send', 'Radiologi\RegistrasiPemeriksaan::sendRis');
$routes->post('/radiologi/registrasi/ris/batal', 'Radiologi\RegistrasiPemeriksaan::batalRis');
// URL compatibility for the original view; its existing JavaScript still needs completion.
$routes->get('/radiologi/rad_registrasi_pemeriksaan/viewOrder', 'Radiologi\RegistrasiPemeriksaan::viewOrder');


$routes->group('radiologi/hasil-pemeriksaan', static function ($routes) {
    $routes->get('init', 'Radiologi\HasilPemeriksaan::init');
    $routes->get('kunjungan', 'Radiologi\HasilPemeriksaan::kunjungan');
    $routes->get('transaksi', 'Radiologi\HasilPemeriksaan::transaksi');
    $routes->get('hasil', 'Radiologi\HasilPemeriksaan::hasil');

    $routes->post('simpan-hasil', 'Radiologi\HasilPemeriksaan::simpanHasil');
    $routes->post('approve-gambar', 'Radiologi\HasilPemeriksaan::approveGambar');
    $routes->post('dokter-baca', 'Radiologi\HasilPemeriksaan::dokterBaca');

    $routes->get('cetak', 'Radiologi\HasilPemeriksaan::cetak');
    $routes->post('update-no-telp', 'Radiologi\HasilPemeriksaan::updateNoTelp');
});

$routes->get('/radiologi/rad_item_pemeriksaan/load', 'Radiologi\RadItemPemeriksaan::load');
$routes->get('/radiologi/rad_item_pemeriksaan/metode', 'Radiologi\RadItemPemeriksaan::metode');
$routes->post('/radiologi/rad_item_pemeriksaan/save', 'Radiologi\RadItemPemeriksaan::save');
$routes->post('/radiologi/rad_item_pemeriksaan/delete', 'Radiologi\RadItemPemeriksaan::delete');

$routes->get('/radiologi/rad_item_pemeriksaan_detail/load', 'Radiologi\RadItemPemeriksaanDetail::load');
$routes->get('/radiologi/rad_item_pemeriksaan_detail/tindakan', 'Radiologi\RadItemPemeriksaanDetail::tindakan');
$routes->get('/radiologi/rad_item_pemeriksaan_detail/items', 'Radiologi\RadItemPemeriksaanDetail::items');
$routes->get('/radiologi/rad_item_pemeriksaan_detail/groups', 'Radiologi\RadItemPemeriksaanDetail::groups');
$routes->post('/radiologi/rad_item_pemeriksaan_detail/save', 'Radiologi\RadItemPemeriksaanDetail::save');
$routes->post('/radiologi/rad_item_pemeriksaan_detail/delete', 'Radiologi\RadItemPemeriksaanDetail::delete');

$routes->get('/radiologi/rad_expertise_template/load', 'Radiologi\RadExpertiseTemplate::load');
$routes->get('/radiologi/rad_expertise_template/metode', 'Radiologi\RadExpertiseTemplate::metode');
$routes->get('/radiologi/rad_expertise_template/dokter', 'Radiologi\RadExpertiseTemplate::dokter');
$routes->get('/radiologi/rad_expertise_template/tindakan', 'Radiologi\RadExpertiseTemplate::tindakan');
$routes->get('/radiologi/rad_expertise_template/kode', 'Radiologi\RadExpertiseTemplate::kode');
$routes->post('/radiologi/rad_expertise_template/save', 'Radiologi\RadExpertiseTemplate::save');
$routes->post('/radiologi/rad_expertise_template/delete', 'Radiologi\RadExpertiseTemplate::delete');

$routes->get('/radiologi/laporan_tat_radiologi/getDokterBaca','Laporan\Radiologi\LaporanTatRadiologi::getDokterBaca');
$routes->get('/radiologi/laporan_tat_radiologi/getKlasifikasi','Laporan\Radiologi\LaporanTatRadiologi::getKlasifikasi');
$routes->post('/radiologi/laporan_tat_radiologi/print','Laporan\Radiologi\LaporanTatRadiologi::print');

 // Route Farmasi tambahan dari Local (hanya yang belum ada di Live)
$routes->get('/farmasi/pelayanan-resep/cek-tutup', 'Farmasi\PelayananResep::cekTutup');
$routes->post('/farmasi/pelayanan-resep/racikan', 'Farmasi\PelayananResep::simpanRacikan');
$routes->get('/farmasi/pelayanan-resep/antrian-ringkasan', 'Farmasi\PelayananResep::antrianRingkasan');
$routes->post('/farmasi/pelayanan-resep/antrian-panggil', 'Farmasi\PelayananResep::panggilAntrian');
$routes->post('/farmasi/pelayanan-resep/antrian-ulangi', 'Farmasi\PelayananResep::ulangiAntrian');

$routes->get('/farmasi/info-stok-harga/referensi', 'Farmasi\InfoStokHarga::referensi');
$routes->get('/farmasi/info-stok-harga/obat', 'Farmasi\InfoStokHarga::obat');
$routes->get('/farmasi/info-stok-harga/daftar', 'Farmasi\InfoStokHarga::daftar');
$routes->post('/farmasi/info-stok-harga/cetak', 'Farmasi\InfoStokHarga::cetak');


// Pemanggil antrian farmasi
$routes->get('/farmasi/pemanggil-antrian/referensi', 'Farmasi\PemanggilAntrian::referensi');
$routes->get('/farmasi/pemanggil-antrian/daftar', 'Farmasi\PemanggilAntrian::daftar');
$routes->get('/farmasi/pemanggil-antrian/kelompok', 'Farmasi\PemanggilAntrian::kelompok');
$routes->get('/farmasi/pemanggil-antrian/nomor-antrian', 'Farmasi\PemanggilAntrian::nomorAntrian');

$routes->post('/farmasi/pemanggil-antrian/update-antrian', 'Farmasi\PemanggilAntrian::updateAntrian');
$routes->post('/farmasi/pemanggil-antrian/panggil', 'Farmasi\PemanggilAntrian::panggil');
$routes->post('/farmasi/pemanggil-antrian/penyerahan', 'Farmasi\PemanggilAntrian::penyerahan');
$routes->post('/farmasi/pemanggil-antrian/batal-penyerahan', 'Farmasi\PemanggilAntrian::batalPenyerahan');
$routes->post('/farmasi/pemanggil-antrian/panggil-penyerahan', 'Farmasi\PemanggilAntrian::panggilPenyerahan');

$routes->get('/farmasi/produksi/referensi', 'Farmasi\Produksi::referensi'); 
$routes->get('/farmasi/produksi/obat', 'Farmasi\Produksi::obat'); 
$routes->get('/farmasi/produksi/daftar', 'Farmasi\Produksi::daftar'); 
$routes->get('/farmasi/produksi/detail', 'Farmasi\Produksi::detail'); 
$routes->get('/farmasi/produksi/stok', 'Farmasi\Produksi::stok'); 
$routes->post('/farmasi/produksi/simpan', 'Farmasi\Produksi::simpan'); 
$routes->post('/farmasi/produksi/item', 'Farmasi\Produksi::item'); 
$routes->post('/farmasi/produksi/hapus-item', 'Farmasi\Produksi::hapusItem'); 
$routes->post('/farmasi/produksi/hapus', 'Farmasi\Produksi::hapus'); 
$routes->post('/farmasi/produksi/tutup', 'Farmasi\Produksi::tutup'); 
$routes->post('/farmasi/produksi/buka', 'Farmasi\Produksi::buka'); 
$routes->post('/farmasi/produksi/cetak', 'Farmasi\Produksi::cetak');

//dokter ok
$routes->get('/paramedis/loadDokterBedah', 'Paramedis::loadDokterBedah');
$routes->get('/paramedis/loadDokterAnestesi', 'Paramedis::loadDokterAnestesi');

$routes->get('/farmasi/pengembalian/referensi', 'Farmasi\Pengembalian::referensi'); 
$routes->get('/farmasi/pengembalian/status-detail', 'Farmasi\Pengembalian::statusDetail'); 
$routes->get('/farmasi/pengembalian/penerimaan', 'Farmasi\Pengembalian::penerimaan'); 
$routes->get('/farmasi/pengembalian/daftar', 'Farmasi\Pengembalian::daftar'); 
$routes->get('/farmasi/pengembalian/detail', 'Farmasi\Pengembalian::detail'); 
$routes->post('/farmasi/pengembalian/buat', 'Farmasi\Pengembalian::buat'); 
$routes->post('/farmasi/pengembalian/ubah-item', 'Farmasi\Pengembalian::ubahItem'); 
$routes->post('/farmasi/pengembalian/hapus-item', 'Farmasi\Pengembalian::hapusItem'); 
$routes->post('/farmasi/pengembalian/hapus', 'Farmasi\Pengembalian::hapus'); 
$routes->post('/farmasi/pengembalian/tutup', 'Farmasi\Pengembalian::tutup'); 
$routes->post('/farmasi/pengembalian/buka', 'Farmasi\Pengembalian::buka'); 
$routes->post('/farmasi/pengembalian/cetak', 'Farmasi\Pengembalian::cetak');

$routes->get('/laporan_komponen_unit/referensi', 'Kasir\LaporanKomponenUnit::referensi');
$routes->post('/laporan_komponen_unit/print', 'Kasir\LaporanKomponenUnit::print');

$routes->get('/laporan_setoran_kasir_per_pasien/referensi', 'Kasir\LaporanSetoranKasirPerPasien::referensi');
$routes->post('/laporan_setoran_kasir_per_pasien/print', 'Kasir\LaporanSetoranKasirPerPasien::print');

$routes->get('/rekap_setoran_kasir_per_pasien/referensi', 'Kasir\RekapSetoranKasirPerPasien::referensi');
$routes->post('/rekap_setoran_kasir_per_pasien/print', 'Kasir\RekapSetoranKasirPerPasien::print');

$routes->get('/laporan_setoran_kasir_farmasi/referensi', 'Kasir\LaporanSetoranKasirFarmasi::referensi');
$routes->post('/laporan_setoran_kasir_farmasi/print', 'Kasir\LaporanSetoranKasirFarmasi::print');

$routes->get('/bedah_sentral/jadwal_operasi/referensi', 'BedahSentral\JadwalOperasi::referensi');

$routes->get('/bedah_sentral/jadwal_operasi/settings', 'BedahSentral\JadwalOperasi::settings');
$routes->post('/bedah_sentral/jadwal_operasi/settings', 'BedahSentral\JadwalOperasi::saveSettings');

$routes->get('/bedah_sentral/jadwal_operasi/diagnosis', 'BedahSentral\JadwalOperasi::diagnosis');
$routes->get('/bedah_sentral/jadwal_operasi/kunjungan', 'BedahSentral\JadwalOperasi::kunjungan');
$routes->get('/bedah_sentral/jadwal_operasi/orders', 'BedahSentral\JadwalOperasi::orders');
$routes->get('/bedah_sentral/jadwal_operasi/reservasi', 'BedahSentral\JadwalOperasi::reservasi');
$routes->get('/bedah_sentral/jadwal_operasi/counts', 'BedahSentral\JadwalOperasi::counts');
$routes->get('/bedah_sentral/jadwal_operasi/daftar', 'BedahSentral\JadwalOperasi::daftar');
$routes->get('/bedah_sentral/jadwal_operasi/detail', 'BedahSentral\JadwalOperasi::detail');

$routes->post('/bedah_sentral/jadwal_operasi/cek-simpan', 'BedahSentral\JadwalOperasi::cekSimpan');
$routes->post('/bedah_sentral/jadwal_operasi/save', 'BedahSentral\JadwalOperasi::save');
$routes->post('/bedah_sentral/jadwal_operasi/delete', 'BedahSentral\JadwalOperasi::deleteSchedule');

$routes->get('/bedah_sentral/jadwal_operasi/persiapan', 'BedahSentral\JadwalOperasi::preparation');

$routes->get('/bedah_sentral/jadwal_operasi/berkas', 'BedahSentral\JadwalOperasi::berkas');
$routes->post('/bedah_sentral/jadwal_operasi/berkas', 'BedahSentral\JadwalOperasi::updateBerkas');

$routes->post('/bedah_sentral/jadwal_operasi/print', 'BedahSentral\JadwalOperasi::print');

// Master User
$routes->get('/master/master-user/get-paramedis', 'Master\MasterUser::paramedis');
$routes->get('/master/master-user/get-unit', 'Master\MasterUser::unit');
$routes->get('/master/master-user/get-landing', 'Master\MasterUser::landing');
$routes->get('/master/master-user/load', 'Master\MasterUser::load');
$routes->post('/master/master-user/save', 'Master\MasterUser::save');
$routes->post('/master/master-user/delete', 'Master\MasterUser::delete');

// master paramedis
$routes->get('/master/master-paramedis/get-jabatan', 'Master\MasterParamedis::jabatan');
$routes->get('/master/master-paramedis/get-spesialisasi', 'Master\MasterParamedis::spesialisasi');
$routes->get('/master/master-paramedis/get-kode-bpjs', 'Master\MasterParamedis::kodeBpjs');
$routes->get('/master/master-paramedis/load', 'Master\MasterParamedis::load');
$routes->post('/master/master-paramedis/save', 'Master\MasterParamedis::save');
$routes->post('/master/master-paramedis/delete', 'Master\MasterParamedis::delete');

// master paramedis bagian
$routes->post('/master/master-paramedis-bagian/delete', 'Master\MasterParamedisBagian::delete');
$routes->post('/master/master-paramedis-bagian/save', 'Master\MasterParamedisBagian::save');
$routes->get('/master/master-paramedis-bagian/get-load', 'Master\MasterParamedisBagian::load');
$routes->get('/master/master-paramedis-bagian/get-unit', 'Master\MasterParamedisBagian::unit');
$routes->get('/master/master-paramedis-bagian/get-instalasi', 'Master\MasterParamedisBagian::instalasi');
$routes->get('/master/master-paramedis-bagian/get-paramedis', 'Master\MasterParamedisBagian::paramedis');

// master unit
$routes->get('/master/master-unit/load', 'Master\MasterUnit::load');
$routes->post('/master/master-unit/save', 'Master\MasterUnit::save');
$routes->post('/master/master-unit/delete', 'Master\MasterUnit::delete'); 
$routes->get('/master/master-unit/get-instalasi', 'Master\MasterUnit::getInstalasi');
$routes->get('/master/master-unit/get-spesialisasi', 'Master\MasterUnit::getSpesialisasi');
$routes->get('/master/master-unit/get-spesialisasirl', 'Master\MasterUnit::getSpesialisasiRl');
$routes->get('/master/master-unit/get-ruang-perawatan', 'Master\MasterUnit::getRuangPerawatan');
$routes->get('/master/master-unit/get-kelas-perawatan', 'Master\MasterUnit::getKelasPerawatan');
$routes->get('/master/master-unit/get-siranap-tt', 'Master\MasterUnit::getSiranapTT');

// kelompok penjamin
$routes->get('/master/kelompok-penjamin/get-load', 'Master\KelompokPenjamin::load');
$routes->post('/master/kelompok-penjamin/save', 'Master\KelompokPenjamin::save');
$routes->post('/master/kelompok-penjamin/hapus', 'Master\KelompokPenjamin::hapus');

// master instalasi
$routes->post('/master/master-instalasi/save', 'Master\MasterInstalasi::save');
$routes->post('/master/master-instalasi/hapus', 'Master\MasterInstalasi::hapus');
$routes->get('/master/master-instalasi/load', 'Master\MasterInstalasi::load');
$routes->get('/master/master-instalasi/kelompok-instalasi', 'Master\MasterInstalasi::loadKelompokInstalasi');


// Bedah sentral - Persiapan operasi
$routes->get('/bedah_sentral/persiapan_operasi/referensi', 'BedahSentral\PersiapanOperasi::referensi');
$routes->get('/bedah_sentral/persiapan_operasi/settings', 'BedahSentral\PersiapanOperasi::settings');
$routes->get('/bedah_sentral/persiapan_operasi/jadwal', 'BedahSentral\PersiapanOperasi::jadwal');
$routes->get('/bedah_sentral/persiapan_operasi/detail', 'BedahSentral\PersiapanOperasi::detail');
$routes->get('/bedah_sentral/persiapan_operasi/tindakan', 'BedahSentral\PersiapanOperasi::tindakan');
$routes->get('/bedah_sentral/persiapan_operasi/items', 'BedahSentral\PersiapanOperasi::items');
$routes->get('/bedah_sentral/persiapan_operasi/pelaksana', 'BedahSentral\PersiapanOperasi::pelaksana');
$routes->get('/bedah_sentral/persiapan_operasi/status', 'BedahSentral\PersiapanOperasi::status');
$routes->get('/bedah_sentral/persiapan_operasi/template-items', 'BedahSentral\PersiapanOperasi::templateItems');

$routes->post('/bedah_sentral/persiapan_operasi/settings', 'BedahSentral\PersiapanOperasi::saveSettings');
$routes->post('/bedah_sentral/persiapan_operasi/save-item', 'BedahSentral\PersiapanOperasi::saveItem');
$routes->post('/bedah_sentral/persiapan_operasi/save-template', 'BedahSentral\PersiapanOperasi::saveTemplate');
$routes->post('/bedah_sentral/persiapan_operasi/tampung-order', 'BedahSentral\PersiapanOperasi::tampungOrder');
$routes->post('/bedah_sentral/persiapan_operasi/delete-item', 'BedahSentral\PersiapanOperasi::deleteItem');
$routes->post('/bedah_sentral/persiapan_operasi/cancel', 'BedahSentral\PersiapanOperasi::cancel');
$routes->post('/bedah_sentral/persiapan_operasi/save-pelaksana', 'BedahSentral\PersiapanOperasi::savePelaksana');
$routes->post('/bedah_sentral/persiapan_operasi/delete-pelaksana', 'BedahSentral\PersiapanOperasi::deletePelaksana');

//master auto_tindakan
$routes->post('/master/auto_tindakan/save', 'Master\AutoTindakan::save');
$routes->get('/master/auto_tindakan/load', 'Master\AutoTindakan::load');
$routes->post('/master/auto_tindakan/delete', 'Master\AutoTindakan::delete');
$routes->get('/master/auto_tindakan/loadUnit', 'Master\AutoTindakan::loadUnit');
$routes->get('/master/auto_tindakan/loadTindakan', 'Master\AutoTindakan::loadTindakan');

// master -> fitur
$routes->get('master/fitur/load', 'Master\Fitur::load');
$routes->post('master/fitur/save', 'Master\Fitur::save');
$routes->post('master/fitur/delete', 'Master\Fitur::delete');
$routes->get('master/fitur/loadModulesGroup', 'Master\Fitur::loadModulesGroup');
$routes->get('master/fitur/loadInstalasi', 'Master\Fitur::loadInstalasi');
$routes->get('master/fitur/loadUnit', 'Master\Fitur::loadUnit');
$routes->get('master/fitur/loadJabatan', 'Master\Fitur::loadJabatan');
$routes->get('master/fitur/loadUser', 'Master\Fitur::loadUser');




$routes->get('/bedah_sentral/hasil_operasi/referensi', 'BedahSentral\HasilOperasi::referensi');
$routes->get('/bedah_sentral/hasil_operasi/settings', 'BedahSentral\HasilOperasi::settings');
$routes->get('/bedah_sentral/hasil_operasi/pasien', 'BedahSentral\HasilOperasi::pasien');
$routes->get('/bedah_sentral/hasil_operasi/tindakan', 'BedahSentral\HasilOperasi::tindakan');
$routes->get('/bedah_sentral/hasil_operasi/diagnosis', 'BedahSentral\HasilOperasi::diagnosis');
$routes->get('/bedah_sentral/hasil_operasi/hasil', 'BedahSentral\HasilOperasi::hasil');
$routes->post('/bedah_sentral/hasil_operasi/settings', 'BedahSentral\HasilOperasi::saveSettings');
$routes->post('/bedah_sentral/hasil_operasi/simpan', 'BedahSentral\HasilOperasi::simpan');
$routes->post('/bedah_sentral/hasil_operasi/hapus', 'BedahSentral\HasilOperasi::hapus');
$routes->post('/bedah_sentral/hasil_operasi/upload', 'BedahSentral\HasilOperasi::upload');
$routes->post('/bedah_sentral/hasil_operasi/print', 'BedahSentral\HasilOperasi::print');


$routes->get('/master/setting_tarif/referensi', 'Master\SettingTarif::referensi');
$routes->get('/master/setting_tarif/settings', 'Master\SettingTarif::settings');
$routes->post('/master/setting_tarif/settings', 'Master\SettingTarif::saveSettings');
$routes->get('/master/setting_tarif/masters', 'Master\SettingTarif::masters');
$routes->post('/master/setting_tarif/save-master', 'Master\SettingTarif::saveMaster');
$routes->post('/master/setting_tarif/delete-master', 'Master\SettingTarif::deleteMaster');
$routes->get('/master/setting_tarif/mappings', 'Master\SettingTarif::mappings');
$routes->post('/master/setting_tarif/save-mapping', 'Master\SettingTarif::saveMapping');
$routes->post('/master/setting_tarif/delete-mapping', 'Master\SettingTarif::deleteMapping');
$routes->get('/master/setting_tarif/tariffs', 'Master\SettingTarif::tariffs');
$routes->get('/master/setting_tarif/procedures', 'Master\SettingTarif::procedures');
$routes->post('/master/setting_tarif/add-tariffs', 'Master\SettingTarif::addTariffs');
$routes->get('/master/setting_tarif/detail', 'Master\SettingTarif::detail');
$routes->post('/master/setting_tarif/save-tariff', 'Master\SettingTarif::saveTariff');
$routes->post('/master/setting_tarif/delete-tariff', 'Master\SettingTarif::deleteTariff');
$routes->post('/master/setting_tarif/copy-tariffs', 'Master\SettingTarif::copyTariffs');
$routes->post('/master/setting_tarif/print', 'Master\SettingTarif::print');

//master -> bridging
$routes->get('master/bridging/load', 'Master\Bridging::load');
$routes->post('master/bridging/save', 'Master\Bridging::save');
$routes->post('master/bridging/delete', 'Master\Bridging::delete');

$routes->get('/user/loadUserRole', 'User::loadUserRole');
$routes->get('/user/changeRole', 'User::changeRole');

//master -> jabatan_paramedis
$routes->get('master/jabatan_paramedis/load', 'Master\JabatanParamedis::load');
$routes->post('master/jabatan_paramedis/save', 'Master\JabatanParamedis::save');
$routes->post('master/jabatan_paramedis/delete', 'Master\JabatanParamedis::delete');

$routes->get('/kamar_operasi/lihat_jadwal_operasi/referensi', 'BedahSentral\LihatJadwalOperasi::referensi');
$routes->get('/kamar_operasi/lihat_jadwal_operasi/settings', 'BedahSentral\LihatJadwalOperasi::settings');
$routes->post('/kamar_operasi/lihat_jadwal_operasi/settings', 'BedahSentral\LihatJadwalOperasi::saveSettings');
$routes->post('/kamar_operasi/lihat_jadwal_operasi/cekstatus_jadwal', 'BedahSentral\LihatJadwalOperasi::cekstatus_jadwal');
$routes->post('/kamar_operasi/lihat_jadwal_operasi/get_datajadwal', 'BedahSentral\LihatJadwalOperasi::get_datajadwal');
$routes->post('/kamar_operasi/lihat_jadwal_operasi/get_listpelaksana', 'BedahSentral\LihatJadwalOperasi::get_listpelaksana');

$routes->get('/bedah_sentral/registrasi_pemeriksaan/referensi', 'BedahSentral\RegistrasiPemeriksaan::referensi');
$routes->get('/bedah_sentral/registrasi_pemeriksaan/settings', 'BedahSentral\RegistrasiPemeriksaan::settings');
$routes->post('/bedah_sentral/registrasi_pemeriksaan/settings', 'BedahSentral\RegistrasiPemeriksaan::saveSettings');
$routes->get('/bedah_sentral/registrasi_pemeriksaan/kunjungan', 'BedahSentral\RegistrasiPemeriksaan::kunjungan');
$routes->get('/bedah_sentral/registrasi_pemeriksaan/detail-kunjungan', 'BedahSentral\RegistrasiPemeriksaan::detailKunjungan');
$routes->get('/bedah_sentral/registrasi_pemeriksaan/jadwal', 'BedahSentral\RegistrasiPemeriksaan::jadwal');
$routes->get('/bedah_sentral/registrasi_pemeriksaan/history', 'BedahSentral\RegistrasiPemeriksaan::history');
$routes->get('/bedah_sentral/registrasi_pemeriksaan/tindakan', 'BedahSentral\RegistrasiPemeriksaan::tindakan');

//master -> setting
$routes->get('master/setting/load', 'Master\Setting::load');
$routes->post('master/setting/save', 'Master\Setting::save');
$routes->post('master/setting/delete', 'Master\Setting::delete');

$routes->get('/master/tindakan/getKlasifikasi','Master\Tindakan::getKlasifikasi');
$routes->get('/master/tindakan/getKomponenTarif','Master\Tindakan::getKomponenTarif');
$routes->get('/master/tindakan/getTindakan','Master\Tindakan::getTindakan');
$routes->get('/master/tindakan/loadData','Master\Tindakan::loadData');
$routes->get('/master/tindakan/getKodeUrut','Master\Tindakan::getKodeUrut');
$routes->post('/master/tindakan/save','Master\Tindakan::save');
$routes->post('/master/tindakan/delete','Master\Tindakan::delete');

$routes->get('/master/klasifikasi_tindakan/getInduk','Master\KlasifikasiTindakan::getInduk');
$routes->get('/master/klasifikasi_tindakan/getKlasifikasi','Master\KlasifikasiTindakan::getKlasifikasi');
$routes->get('/master/klasifikasi_tindakan/loadData','Master\KlasifikasiTindakan::loadData');
$routes->post('/master/klasifikasi_tindakan/save','Master\KlasifikasiTindakan::save');
$routes->post('/master/klasifikasi_tindakan/delete','Master\KlasifikasiTindakan::delete');

$routes->get('/master/template_tindakan/getTemplate','Master\TemplateTindakan::getTemplate');
$routes->get('/master/template_tindakan/loadData','Master\TemplateTindakan::loadData');
$routes->post('/master/template_tindakan/save','Master\TemplateTindakan::save');
$routes->post('/master/template_tindakan/delete','Master\TemplateTindakan::delete');

$routes->get('/master/jadwal_dokter/getInstalasi','Master\JadwalDokter::getInstalasi');
$routes->get('/master/jadwal_dokter/getUnit','Master\JadwalDokter::getUnit');
$routes->get('/master/jadwal_dokter/getParamedis','Master\JadwalDokter::getParamedis');
$routes->get('/master/jadwal_dokter/getHari','Master\JadwalDokter::getHari');
$routes->get('/master/jadwal_dokter/loadData','Master\JadwalDokter::loadData');
$routes->post('/master/jadwal_dokter/save','Master\JadwalDokter::save');

//dashboard -> e_kamar
$routes->get('dashboard/e_kamar/load', 'Dashboard\Ekamar::load');
$routes->post('dashboard/e_kamar/update', 'Dashboard\Ekamar::update');