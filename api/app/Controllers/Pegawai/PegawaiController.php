<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PegawaiController extends CI_Controller {
	private $name_user;
	private $nip;
	public function __construct()
	{
		parent::__construct();
		if ( ! $this->session->userdata('login') ) :
			redirect(site_url('login'));
			return;
		endif;
		$data_session = $this->session->userdata('login');
		$this->name_user = $data_session['nama_lengkap'];
		$this->nip = $data_session['nip'];
		$this->load->library(array('session','Excel/PHPExcel', 'upload'));
		$this->load->model(array('m_apps'));
		$this->load->helper(array('form','url','html'));
	}
	public function index($nip=0)
	{
		$data_user = $this->db->query("SELECT * FROM tb_users WHERE nip = '{$this->nip}'")->row();

		$data_agama = $this->db->query("SELECT * FROM agama")->result();
		$data_identitas= $this->db->query("select * From identitas")->result();
		$data_goldarah = $this->db->query("select * from golongan_darah")->result();
		$data_status=$this->db->query("select * from status")->result();
		$data_propinsi=$this->db->query("select * from provinsi")->result();
		$data_kabupaten=$this->db->query("select * from kabupaten")->result();

		// edited by ridde
		$data_bagian=$this->db->query("SELECT * from bagian")->result();
		$data_subbagian=$this->db->query("select * from sub_bagian")->result();
		$data_jabatan=$this->db->query("select * from jabatan")->result();
		$data_ekselon=$this->db->query("select * from ekselon")->result();
		$data_status_pegawai=$this->db->query("select * from status_pegawai")->result();
		$data_golongan=$this->db->query("select * from golongan")->result();
		$data_pangkat=$this->db->query("select * from pangkat")->result();
		$data_jenis_pegawai=$this->db->query("select * from jenis_pegawai")->result();
		$data_lokasi=$this->db->query("select * from lokasi")->result();

		$data_status_keahlian=$this->db->query("select * from status_keahlian")->result();
		$data_pekerjaan=$this->db->query("select * from pekerjaan")->result();
		$data_hubungan=$this->db->query("select * from hubungan")->result();
		$data_jenjang_pendidikan=$this->db->query("select * from jenjang_pendidikan")->result();
		$data_status_asuransi=$this->db->query("select * from status_asuransi")->result();
		$data_nama_asuransi=$this->db->query("select * from nama_asuransi")->result();
		$data_jenis_pendidikan=$this->db->query("select * from jenis_pendidikan")->result();
		$data_status_lulus=$this->db->query("select * from status_lulus")->result();
		$data_jenis_pengalaman=$this->db->query("select * from jenis_pengalaman")->result();
		
		// edited by ridde

		$page = ($this->input->get('page')) ? $this->input->get('page') : 0;

		$total_rows = $this->db->query("SELECT * FROM tb_history WHERE nip = '{$this->nip}'")->num_rows();

        $config = pagination_list();
        $config['base_url'] = site_url("setting/profile?data=history");
		$config['per_page'] = 10;
		$config['uri_segment'] = 4;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';
        $config['total_rows'] = $total_rows;
        $this->pagination->initialize($config);

		$data_history = $this->db->query("SELECT tb_history.*, tb_buku_tanah.* FROM tb_history JOIN tb_buku_tanah ON tb_history.id_bukutanah = tb_buku_tanah.id_bukutanah WHERE tb_history.nip = '{$this->nip}'  ORDER BY tb_history.time DESC LIMIT {$config['per_page']} OFFSET {$page}")->result();
		$data = array(
			'title' => 'Tambah Data Pegawai'.DEFAULT_TITLE,
			// 'data' => $data_user,
			'data_history' => $data_history,
			'data_agama_pegawai'=>$data_agama,
			'data_identitas'=>$data_identitas,
			'data_goldarah'=>$data_goldarah,
			'data_status'=>$data_status,
			'data_propinsi'=>$data_propinsi,
			'data_kabupaten'=>$data_kabupaten,
			'data_bagian'=>$data_bagian,
			'data_jabatan'=>$data_jabatan,
			'data_ekselon'=>$data_ekselon,
			'data_status_pegawai'=>$data_status_pegawai,
			'data_golongan'=>$data_golongan,
			'data_pangkat'=>$data_pangkat,
			'data_jenis_pegawai'=>$data_jenis_pegawai,
			'data_lokasi'=>$data_lokasi,
			'data_status_keahlian'=>$data_status_keahlian,
			'data_pekerjaan'=>$data_pekerjaan,
			'data_hubungan'=>$data_hubungan,
			'data_jenjang_pendidikan'=>$data_jenjang_pendidikan,
			'data_status_asuransi'=>$data_status_asuransi,
			'data_nama_asuransi'=>$data_nama_asuransi,
			'data_jenis_pendidikan'=>$data_jenis_pendidikan,
			'data_status_lulus'=>$data_status_lulus,
			'data_jenis_pengalaman'=>$data_jenis_pengalaman
		);
		// var_dump($data);
		$this->template->view('pegawai/v_pegawai', $data);
	}

	/**
	 * Mengubah Data profile
	 *
	 * @return string
	 **/
	public function update()
	{
		$data_user = $this->db->query("SELECT * FROM tb_users WHERE nip = '{$this->nip}'")->row();

		$data = array(
			'nama_lengkap' => $this->input->post('nama'),
			'nip' => $this->input->post('nip'),
		);

		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		//$config['remove_spaces'] = FALSE;
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('img')) :
			$data['foto'] = $this->input->post('no_file');
		else :
			$data['foto'] = $this->upload->file_name;
			//if($data_user->foto =! 'null')
				@unlink("./assets/user/{$data_user->foto}");
		endif;

		$this->db->update('tb_users', $data, array('nip'=> $this->nip));
		if($data['nip']==$data->nip) :
			redirect("login/profile?data=update");
		else :
			redirect('login/logout');
		endif;
	}

	/**
	 * Mengecek valid password
	 *
	 * @return string
	 **/
	public function cek_pass()
	{
		$data_user = $this->db->query("SELECT * FROM tb_users WHERE nip = '{$this->nip}'")->row();
		$output['valid'] = (password_verify($this->input->post('lama'), $data_user->pass_login)) ? true : false;
		$this->output->set_content_type('application/json')->set_output(json_encode($output));
	}

	/**
	 * Mengubah Password
	 *
	 * @var string
	 **/
	public function update_pass()
	{
		$data = array(
			'pass_login' => password_hash($this->input->get('password'), PASSWORD_DEFAULT),
		);
		$this->db->update('tb_users', $data, array('nip'=> $this->nip));
		redirect("setting/profile?data=security");
	}

	public function save_data()
	{
		// var_dump($this->input->post());
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		$id_peg = $this->db->query("SELECT COALESCE(max(cast(id_pegawai as UNSIGNED)), 0)+1 as ID FROM pegawai")->row();		
		$pegawai = array(
						"ID_PEGAWAI"	=> $id_peg->ID,
						"NIP" 			=> $this->input->post('txt_nip'),
						"NAMA" 			=> $this->input->post('txt_namapeg'),
						"TEMPAT_LAHIR" 	=> $this->input->post('txt_tmptlahir'),
						"TANGGAL_LAHIR" => $this->input->post('tgllahir'),
						"JENIS_KELAMIN" => $this->input->post('cmb_kelamin_peg'),
						"KD_AGAMA" 		=> $this->input->post('cmb_agama'),
						"KD_GOLDA" 		=> $this->input->post('cmb_goldarah'),
						"ALAMAT" 		=> $this->input->post('txt_alamat'),
						"KD_PROVINSI" 	=> $this->input->post('cmb_provinsi'),
						"KD_KABUPATEN" 	=> $this->input->post('cmb_kabupaten'),
						"KD_KECAMATAN" 	=> $this->input->post('cmb_kecamatan'),
						"KD_KELURAHAN" 	=> $this->input->post('cmb_kelurahan'),
						"KODEPOS" 		=> $this->input->post('txt_kdpos'),
						"TELEPHONE" 	=> $this->input->post('txt_telp'),
						"HANDPHONE" 	=> $this->input->post('txt_hp'),
						"EMAIL" 		=> $this->input->post('txt_email'),
						"KD_IDENTITAS" 	=> $this->input->post('cmb_identitas'),
						"NO_IDENTITAS" 	=> $this->input->post('txt_noidentitas'),
						"NO_NPWP" 		=> $this->input->post('txt_npwp'),
						"TANGGAL_MASUK" => $this->input->post('tgl_masuk'),
					);
		if ($this->upload->do_upload('imgprofile')){
			$pegawai['FOTO'] = $this->upload->file_name;
		}
		$this->db->insert('pegawai', $pegawai);

		if($this->db->affected_rows() > 0){
			$KD_PENEMPATAN = $this->db->query("SELECT COALESCE(MAX(KD_PENEMPATAN), 0)+1 as ID FROM detail_penempatan_pegawai")->row();
			$detail_penempatan_pegawai = array(
						"KD_PENEMPATAN" 	=> $KD_PENEMPATAN->ID,
						"ID_PEGAWAI" 		=> $id_peg->ID,
						"KD_BAGIAN" 		=> $this->input->post('cmb_bagian'),
						"KD_GOLONGAN" 		=> $this->input->post('cmb_golongan'),
						"KD_JABATAN" 		=> $this->input->post('cmb_jabatan'),
						"KD_PANGKAT" 		=> $this->input->post('cmb_pangkat'),
						"KD_EKSELON" 		=> $this->input->post('cmb_ekselon'),
						"KD_JENIS_PEGAWAI"	=> $this->input->post('cmb_jenis_pegawai'),
						"KD_STATUS_PEGAWAI" => $this->input->post('cmb_status_pegawai'),
						"KD_LOKASI" 		=> $this->input->post('cmb_lokasi'),
						"TANGGAL_MULAI" 	=> $this->input->post('cmb_jpenempatan')!=0?null:$this->input->post('indate'),
						"TANGGAL_SAMPAI" 	=> $this->input->post('cmb_jpenempatan')!=0?null:$this->input->post('untildate'),
						"KD_SUBBAGIAN" 		=> $this->input->post('cmb_subbagian'),
						"JENIS_PENEMPATAN" 		=> $this->input->post('cmb_jpenempatan')
					);
			if ($this->upload->do_upload('file_penempatan')){
				$detail_penempatan_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->insert('detail_penempatan_pegawai', $detail_penempatan_pegawai);

			if($this->input->post('txt_lembaga') && $this->input->post('txt_lembaga_alamat')){
				$KD_PENDIDIKAN = $this->db->query("SELECT COALESCE(MAX(KD_PENDIDIKAN), 0)+1 as ID FROM detail_pendidikan_pegawai")->row();
				$detail_pendidikan_pegawai = array(
							"KD_PENDIDIKAN" 		=> $KD_PENDIDIKAN->ID,
							"ID_PEGAWAI" 			=> $id_peg->ID,
							"NAMA_LEMBAGA" 			=> $this->input->post('txt_lembaga'),
							"KD_JENJANG" 			=> $this->input->post('cmb_jenjang'),
							"ALAMAT" 				=> $this->input->post('txt_lembaga_alamat'),
							"PERIODE_MULAI" 		=> $this->input->post('periode_dari'),
							"PERIODE_SAMPAI" 		=> $this->input->post('periode_sampai'),
							"IPK" 					=> $this->input->post('txt_ipk'),
							"KD_JURUSAN" 			=> $this->input->post('cmb_jurusan'),
							"KD_STATUS_LULUS"		=> $this->input->post('cmb_lulus'),
							"KD_JENIS_PENDIDIKAN" 	=> $this->input->post('cmb_j_pendidikan'),
							"NAMA_PEJABAT" 			=> $this->input->post('txt_nmpejabat'),
							"NO_IJAZAH" 			=> $this->input->post('txt_ijazah')
						);
				if ($this->upload->do_upload('file_pendidikan')){
					$detail_pendidikan_pegawai['GAMBAR_IJAZAH'] = $this->upload->file_name;
				}
				$this->db->insert('detail_pendidikan_pegawai', $detail_pendidikan_pegawai);
			}
			
			if($this->input->post('txt_nama_perusahaan') && $this->input->post('txt_alamat_perusahaan')){
				$KD_PENGALAMAN = $this->db->query("SELECT COALESCE(MAX(KD_PENGALAMAN), 0)+1 as ID FROM detail_pengalaman_pegawai")->row();
				$detail_pengalaman_pegawai = array(
							"KD_PENGALAMAN" 		=> $KD_PENGALAMAN->ID,
							"ID_PEGAWAI" 			=> $id_peg->ID,
							"NAMA_PERUSAHAAN"		=> $this->input->post('txt_nama_perusahaan'),
							"KD_JABATAN" 			=> $this->input->post('cmb_jab_pengalaman'),
							"ALAMAT" 				=> $this->input->post('txt_alamat_perusahaan'),
							"KD_JENIS_PENGALAMAN" 	=> $this->input->post('cmb_jenis_pengalaman'),
							"KD_PROVINSI" 			=> $this->input->post('cmb_prov_pengalaman'),
							"KD_KABUPATEN" 			=> $this->input->post('cmb_kab_pengalaman'),
							"KD_KECAMATAN" 			=> $this->input->post('cmb_kec_pengalaman'),
							"KD_KELURAHAN" 			=> $this->input->post('cmb_kel_pengalaman'),
							"TANGGAL_MULAI" 		=> $this->input->post('periode_pengalaman_awal'),
							"TANGGAL_SAMPAI" 		=> $this->input->post('periode_pengalaman_akhir')
						);
				if ($this->upload->do_upload('file_pengalaman')){
					$detail_pengalaman_pegawai['IMAGE_SK'] = $this->upload->file_name;
				}
				$this->db->insert('detail_pengalaman_pegawai', $detail_pengalaman_pegawai);
			}
			
			if($this->input->post('nama_keahlian') && $this->input->post('cmb_status_keahlian') && $this->input->post('ket_keahlian')){
				$nama_keahlian = $this->input->post('nama_keahlian');
				$cmb_status_keahlian = $this->input->post('cmb_status_keahlian');
				$ket_keahlian = $this->input->post('ket_keahlian');
				// $file_keahlian = $this->input->post('file_keahlian');
				foreach ($nama_keahlian as $key => $value) {
					if($value && $cmb_status_keahlian[$key] && $ket_keahlian[$key]){
						$KD_KEAHLIAN = $this->db->query("SELECT COALESCE(MAX(KD_KEAHLIAN), 0)+1 as ID FROM detail_keahlian_pegawai")->row();
						$detail_keahlian_pegawai = array(
								"KD_KEAHLIAN" 			=> $KD_KEAHLIAN->ID,
								"ID_PEGAWAI" 			=> $id_peg->ID,
								"NAMA_KEAHLIAN" 		=> $value,
								"KD_STATUS_KEAHLIAN" 	=> $cmb_status_keahlian[$key],
								"KETERANGAN" 			=> $ket_keahlian[$key]
							);
						if ($this->upload->do_upload('file_keahlian'.$key)){
							$detail_keahlian_pegawai['IMAGE'] = $this->upload->file_name;
						}
						$this->db->insert('detail_keahlian_pegawai', $detail_keahlian_pegawai);
					}
				}
			}
			
			if($this->input->post('txt_nmkel') && $this->input->post('txt_tmptlahirkel')){
				$KD_KELUARGA = $this->db->query("SELECT COALESCE(MAX(KD_KELUARGA), 0)+1 as ID FROM detail_keluarga_pegawai")->row();
				$detail_keluarga_pegawai = array(
							"KD_KELUARGA" 			=> $KD_KELUARGA->ID,
							"ID_PEGAWAI" 			=> $id_peg->ID,
							"NAMA" 					=> $this->input->post('txt_nmkel'),
							"KD_HUBUNGAN" 			=> $this->input->post('cmb_hub_kel'),
							"TEMPAT_LAHIR" 			=> $this->input->post('txt_tmptlahirkel'),
							"TGL_LAHIR" 			=> $this->input->post('tgllahir_kel'),
							"KD_PEKERJAAN" 			=> $this->input->post('cmb_pekerjaan_kel'),
							"KD_JENJANG" 			=> $this->input->post('cmb_pendidikan_kel'),
							"KD_STATUS_ASURANSI" 	=> $this->input->post('cmb_status_asuransi'),
							"KD_ASURANSI" 			=> $this->input->post('cmb_nmasuransi'),
							"JENIS_KELAMIN" 		=> $this->input->post('cmb_kelamin_kel'),
							"KD_GOLDA" 				=> $this->input->post('cmb_goldarah_kel')
						);
				if ($this->upload->do_upload('file_keluarga')){
					$detail_keluarga_pegawai['IMAGE_AKTE_KTP'] = $this->upload->file_name;
				}
				$this->db->insert('detail_keluarga_pegawai', $detail_keluarga_pegawai);
			}			
			redirect("pegawai/ctambahpegawai");
		}else{
			redirect("pegawai/ctambahpegawai");
		}

	}

	public function edit_detail($no_auto=0)
	{
		// Data master
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NO_AUTO={$no_auto}")->row();
		$idpeg=$data_id->ID_PEGAWAI;
		$data_agama = $this->db->query("SELECT * FROM agama")->result();
		$data_identitas= $this->db->query("select * From identitas")->result();
		$data_goldarah = $this->db->query("select * from golongan_darah")->result();
		$data_status=$this->db->query("select * from status")->result();
		$data_propinsi=$this->db->query("select * from provinsi")->result();

		// edited by ridde
		$data_bagian=$this->db->query("select * from bagian")->result();
		$data_subbagian=$this->db->query("select * from sub_bagian")->result();
		$data_jabatan=$this->db->query("select * from jabatan")->result();
		$data_ekselon=$this->db->query("select * from ekselon")->result();
		$data_status_pegawai=$this->db->query("select * from status_pegawai")->result();
		$data_golongan=$this->db->query("select * from golongan")->result();
		$data_pangkat=$this->db->query("select * from pangkat")->result();
		$data_jenis_pegawai=$this->db->query("select * from jenis_pegawai")->result();
		$data_lokasi=$this->db->query("select * from lokasi")->result();

		$data_status_keahlian=$this->db->query("select * from status_keahlian")->result();
		$data_pekerjaan=$this->db->query("select * from pekerjaan")->result();
		$data_hubungan=$this->db->query("select * from hubungan")->result();
		$data_jenjang_pendidikan=$this->db->query("select * from jenjang_pendidikan")->result();
		$data_status_asuransi=$this->db->query("select * from status_asuransi")->result();
		$data_nama_asuransi=$this->db->query("select * from nama_asuransi")->result();
		$data_jenis_pendidikan=$this->db->query("select * from jenis_pendidikan")->result();
		$data_status_lulus=$this->db->query("select * from status_lulus")->result();
		$data_jenis_pengalaman=$this->db->query("select * from jenis_pengalaman")->result();

		$data_level_kemampuan=$this->db->query("select * from level_kemampuan order by KD_LEVEL")->result();
		$data_jenis_sip=$this->db->query("select * from jenis_sip order by KD_JENIS_SIP")->result();

		// Data Pegawai
		$pegawai=$this->db->query("select * from pegawai where ID_PEGAWAI={$idpeg}")->row();
		$data_kabupaten=$this->db->query("select * from kabupaten where KD_PROVINSI={$pegawai->KD_PROVINSI} or KD_PROVINSI=0 order by KABUPATEN asc")->result();
		$data_kecamatan=$this->db->query("select * from kecamatan where KD_KABUPATEN={$pegawai->KD_KABUPATEN} or KD_KABUPATEN=0  order by KECAMATAN asc")->result();
		$data_kelurahan=$this->db->query("select * from kelurahan where KD_KECAMATAN={$pegawai->KD_KECAMATAN} OR KD_KECAMATAN =0 order by KELURAHAN asc")->result();
		$data_status_perkawinan = $this->db->query("SELECT * FROM status_perkawinan order by STATUS_PERKAWINAN")->result();

		$page = ($this->input->get('page')) ? $this->input->get('page') : 0;

        $config = pagination_list();
        $config['base_url'] = site_url("pegawai/cpegawai/edit_detail/".$idpeg."?data=keahlian");
		$config['per_page'] = 10;
		$config['uri_segment'] = 4;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';
		$config['total_rows'] = $this->db->query("SELECT dkp.*, sk.STATUS_KEAHLIAN
						FROM detail_keahlian_pegawai dkp
						INNER JOIN status_keahlian sk on dkp.KD_STATUS_KEAHLIAN=sk.KD_STATUS_KEAHLIAN
						WHERE dkp.ID_PEGAWAI={$idpeg}")->num_rows();
        $this->pagination->initialize($config);
        $pagination_keahlian = $this->pagination->create_links();
		$detail_keahlian_pegawai=$this->db->query("SELECT dkp.*, sk.STATUS_KEAHLIAN
						FROM detail_keahlian_pegawai dkp
						INNER JOIN status_keahlian sk on dkp.KD_STATUS_KEAHLIAN=sk.KD_STATUS_KEAHLIAN
						WHERE dkp.ID_PEGAWAI={$idpeg} AND 1=1  limit {$config['per_page']} offset ".$page."")->result();
		/*$detail_keahlian_pegawai=$this->db->query("SELECT dkp.*, sk.STATUS_KEAHLIAN
						FROM detail_keahlian_pegawai dkp
						INNER JOIN status_keahlian sk on dkp.KD_STATUS_KEAHLIAN=sk.KD_STATUS_KEAHLIAN
						WHERE dkp.ID_PEGAWAI={$idpeg}")->result();*/
		$detail_pendidikan_pegawai=$this->db->query("SELECT dpp.*, jp.JENJANG_PENDIDIKAN, j.JURUSAN, stl.STATUS_LULUS, jpd.JENIS_PENDIDIKAN 
						FROM detail_pendidikan_pegawai dpp
						INNER JOIN jenjang_pendidikan jp on dpp.KD_JENJANG=jp.KD_JENJANG
						left JOIN jurusan j on dpp.KD_JURUSAN=j.KD_JURUSAN
						left JOIN status_lulus stl on dpp.KD_STATUS_LULUS=stl.KD_STATUS_LULUS
						left JOIN jenis_pendidikan jpd on dpp.KD_JENIS_PENDIDIKAN=jpd.KD_JENIS_PENDIDIKAN
						WHERE dpp.ID_PEGAWAI={$idpeg}")->result();
		$detail_pengalaman_pegawai=$this->db->query("SELECT dpp.*, jbt.JABATAN, jp.JENIS_PENGALAMAN FROM detail_pengalaman_pegawai dpp
						INNER JOIN jabatan jbt on dpp.KD_JABATAN=jbt.KD_JABATAN
						INNER JOIN jenis_pengalaman jp on dpp.KD_JENIS_PENGALAMAN=jp.KD_JENIS_PENGALAMAN
						WHERE dpp.ID_PEGAWAI={$idpeg}")->result();
		$detail_penempatan_pegawai=$this->db->query("SELECT dpp.*, b.BAGIAN, sb.sub_bagian, g.GOLONGAN, j.JABATAN, p.PANGKAT, e.EKSELON, jp.JENIS_PEGAWAI, sp.STATUS_PEGAWAI, l.LOKASI
						FROM detail_penempatan_pegawai dpp
						INNER JOIN bagian b on dpp.KD_BAGIAN=b.KD_BAGIAN
						LEFT JOIN sub_bagian sb on dpp.KD_SUBBAGIAN=sb.kd_sub_bagian
						LEFT JOIN golongan g on dpp.KD_GOLONGAN=g.KD_GOLONGAN
						LEFT JOIN jabatan j on dpp.KD_JABATAN=j.KD_JABATAN
						LEFT JOIN pangkat p on dpp.KD_PANGKAT=p.KD_PANGKAT
						LEFT JOIN ekselon e on dpp.KD_EKSELON=e.KD_EKSELON
						LEFT JOIN jenis_pegawai jp on dpp.KD_JENIS_PEGAWAI=jp.KD_JENIS_PEGAWAI
						LEFT JOIN status_pegawai sp on dpp.KD_STATUS_PEGAWAI=sp.KD_STATUS_PEGAWAI
						LEFT JOIN lokasi l on dpp.KD_LOKASI=l.KD_LOKASI
						WHERE dpp.ID_PEGAWAI={$idpeg}")->result();
		$detail_keluarga_pegawai=$this->db->query("SELECT dkp.*, h.HUBUNGAN, p.PEKERJAAN, jp.JENJANG_PENDIDIKAN, gd.GOLONGAN_DARAH
						FROM detail_keluarga_pegawai dkp
						INNER JOIN hubungan h on dkp.KD_HUBUNGAN=h.KD_HUBUNGAN
						INNER JOIN pekerjaan p on dkp.KD_PEKERJAAN=p.KD_PEKERJAAN
						INNER JOIN jenjang_pendidikan jp on dkp.KD_JENJANG=jp.KD_JENJANG
						INNER JOIN golongan_darah gd on dkp.KD_GOLDA=gd.KD_GOLDA
						WHERE dkp.ID_PEGAWAI={$idpeg}")->result();

		$detail_pelatihan_pegawai=$this->db->query("SELECT dpp.*
						FROM detail_pelatihan_pegawai dpp
						WHERE dpp.ID_PEGAWAI={$idpeg}")->result();

		$detail_kemampuan_pegawai=$this->db->query("SELECT dkp.*, lk.LEVEL
						FROM detail_kemampuan_pegawai dkp
						LEFT JOIN level_kemampuan lk on lk.KD_LEVEL = dkp.KD_LEVEL
						WHERE dkp.ID_PEGAWAI={$idpeg}")->result();

		$detail_str_pegawai=$this->db->query("SELECT dsp.*
						FROM detail_str_pegawai dsp
						WHERE dsp.ID_PEGAWAI={$idpeg}")->result();

		$detail_sip_pegawai=$this->db->query("SELECT dsp.*, j.JENIS_SIP
						FROM detail_sip_pegawai dsp
						LEFT JOIN jenis_sip j on j.KD_JENIS_SIP = dsp.KD_JENIS_SIP
						WHERE dsp.ID_PEGAWAI={$idpeg}")->result();

		$detail_mcu_pegawai=$this->db->query("SELECT dmp.*
						FROM detail_mcu_pegawai dmp
						WHERE dmp.ID_PEGAWAI={$idpeg}")->result();

		$data = array(
			'title' => 'Edit Data'.DEFAULT_TITLE,
			'page' => $page,
			'no_auto' => $no_auto,	
			'id_pegawai' => $idpeg,
			'data_agama_pegawai'=>$data_agama,
			'data_identitas'=>$data_identitas,
			'data_goldarah'=>$data_goldarah,
			'data_status'=>$data_status,
			'data_propinsi'=>$data_propinsi,
			'data_bagian'=>$data_bagian,
			'data_jabatan'=>$data_jabatan,
			'data_ekselon'=>$data_ekselon,
			'data_status_pegawai'=>$data_status_pegawai,
			'data_golongan'=>$data_golongan,
			'data_pangkat'=>$data_pangkat,
			'data_jenis_pegawai'=>$data_jenis_pegawai,
			'data_lokasi'=>$data_lokasi,
			'data_status_keahlian'=>$data_status_keahlian,
			'data_pekerjaan'=>$data_pekerjaan,
			'data_hubungan'=>$data_hubungan,
			'data_jenjang_pendidikan'=>$data_jenjang_pendidikan,
			'data_status_asuransi'=>$data_status_asuransi,
			'data_nama_asuransi'=>$data_nama_asuransi,
			'data_jenis_pendidikan'=>$data_jenis_pendidikan,
			'data_status_lulus'=>$data_status_lulus,
			'data_jenis_pengalaman'=>$data_jenis_pengalaman,
			'pegawai'=>$pegawai,
			'data_kabupaten'=>$data_kabupaten,
			'data_kecamatan'=>$data_kecamatan,
			'data_kelurahan'=>$data_kelurahan,
			'detail_keahlian_pegawai'=>$detail_keahlian_pegawai,
			'detail_pendidikan_pegawai'=>$detail_pendidikan_pegawai,
			'detail_pengalaman_pegawai'=>$detail_pengalaman_pegawai,
			'detail_penempatan_pegawai'=>$detail_penempatan_pegawai,
			'detail_keluarga_pegawai'=>$detail_keluarga_pegawai,
			'pagination_keahlian'=>$pagination_keahlian,
			'detail_pelatihan_pegawai'=>$detail_pelatihan_pegawai,
			'data_level_kemampuan'=>$data_level_kemampuan,
			'detail_kemampuan_pegawai'=>$detail_kemampuan_pegawai,
			'detail_str_pegawai'=>$detail_str_pegawai,
			'detail_sip_pegawai'=>$detail_sip_pegawai,
			'data_status_perkawinan'=>$data_status_perkawinan,
			'data_jenis_sip'=>$data_jenis_sip,
			'detail_mcu_pegawai'=>$detail_mcu_pegawai
		);
		// var_dump($data);
		$this->template->view('pegawai/v_detail_pegawai', $data);
	}

	public function get_detail_keahlian($id)
	{
		// var_dump($id);
		$output = array('status' => true, 'results' => '' );
		$query = $this->db->query("SELECT * FROM detail_keahlian_pegawai where KD_KEAHLIAN = {$id}");
		/*foreach($query->result() as $row) :
		$output['results'][] = array(
			'KD_KECAMATAN' => $row->KD_KECAMATAN,
			'KECAMATAN' => $row->KECAMATAN
		);
		endforeach;*/
		$output['results'] = $query->result();
		// var_dump($output);
		$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
	}
	public function get_jurusan($kd_jenjang)
	{
		// var_dump($id);
		$output = array('status' => true, 'results' => '' );
		$query = $this->db->query("SELECT * FROM jurusan where KD_JENJANG = {$kd_jenjang} order by JURUSAN ASC");
		/*foreach($query->result() as $row) :
		$output['results'][] = array(
			'KD_KECAMATAN' => $row->KD_KECAMATAN,
			'KECAMATAN' => $row->KECAMATAN
		);
		endforeach;*/
		$output['results'] = $query->result();
		// var_dump($output);
		$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
	}
	public function get_sub_bagian($kd_bagian)
	{
		// var_dump($id);
		$output = array('status' => true, 'results' => '' );
		$query = $this->db->query("SELECT * FROM sub_bagian where kd_bagian = {$kd_bagian} order by sub_bagian ASC");
		/*foreach($query->result() as $row) :
		$output['results'][] = array(
			'KD_KECAMATAN' => $row->KD_KECAMATAN,
			'KECAMATAN' => $row->KECAMATAN
		);
		endforeach;*/
		$output['results'] = $query->result();
		// var_dump($output);
		$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
	}
	public function update_data_pegawai($id_peg)
	{
		// var_dump($this->input->post());
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		$pegawai = array(
			"ID_PEGAWAI"		=> $this->input->post('txt_id_pegawai'),
			"NIP" 				=> $this->input->post('txt_nip'),
			"NAMA" 				=> $this->input->post('txt_namapeg'),
			"TEMPAT_LAHIR" 		=> $this->input->post('txt_tmptlahir'),
			"TANGGAL_LAHIR" 	=> $this->input->post('tgllahir'),
			"JENIS_KELAMIN" 	=> $this->input->post('cmb_kelamin_peg'),
			"KD_AGAMA" 			=> $this->input->post('cmb_agama'),
			"KD_GOLDA" 			=> $this->input->post('cmb_goldarah'),
			"ALAMAT" 			=> $this->input->post('txt_alamat'),
			"KD_PROVINSI" 		=> $this->input->post('cmb_provinsi'),
			"KD_KABUPATEN" 		=> $this->input->post('cmb_kabupaten'),
			"KD_KECAMATAN" 		=> $this->input->post('cmb_kecamatan'),
			"KD_KELURAHAN" 		=> $this->input->post('cmb_kelurahan'),
			"KODEPOS" 			=> $this->input->post('txt_kdpos'),
			"TELEPHONE" 		=> $this->input->post('txt_telp'),
			"HANDPHONE" 		=> $this->input->post('txt_hp'),
			"EMAIL" 			=> $this->input->post('txt_email'),
			"KD_IDENTITAS" 		=> $this->input->post('cmb_identitas'),
			"NO_IDENTITAS" 		=> $this->input->post('txt_noidentitas'),
			"NO_NPWP" 			=> $this->input->post('txt_npwp'),
			"TANGGAL_MASUK" 	=> $this->input->post('tgl_masuk'),
			"TANGGAL_PENSIUN" 	=> $this->input->post('tgl_pensiun')=="" ? NULL : $this->input->post('tgl_pensiun'),
			"KD_STATUS_PERKAWINAN" 	=> $this->input->post('cmb_stperkawinan'),
			"JUMLAH_ANAK" 			=> $this->input->post('txt_jmlanak'),
			"NOMOR_REKENING" 		=> $this->input->post('txt_norekening'),
			"NAMA_BANK" 			=> $this->input->post('txt_namabank')
		);

		if ($this->upload->do_upload('imgprofile')){
			$pegawai['FOTO'] = $this->upload->file_name;
		}

		$this->db->update('pegawai', $pegawai, array('NO_AUTO'=> $id_peg));

		if($this->db->affected_rows() > 0){
			// redirect("pegawai/cpegawai/edit_detail/".$id_peg);
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
		
	}
	public function update_data_penempatan($no_auto)
	{
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;
		// var_dump($this->input->post());
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_penempatan')){
			// Edit data
			$detail_penempatan_pegawai = array(
						"KD_BAGIAN" 		=> $this->input->post('cmb_bagian'),
						"KD_GOLONGAN" 		=> $this->input->post('cmb_golongan'),
						"KD_JABATAN" 		=> $this->input->post('cmb_jabatan'),
						"KD_PANGKAT" 		=> $this->input->post('cmb_pangkat'),
						"KD_EKSELON" 		=> $this->input->post('cmb_ekselon'),
						"KD_JENIS_PEGAWAI"	=> $this->input->post('cmb_jenis_pegawai'),
						"KD_STATUS_PEGAWAI" => $this->input->post('cmb_status_pegawai'),
						"KD_LOKASI" 		=> $this->input->post('cmb_lokasi'),
						"TANGGAL_MULAI" 	=> $this->input->post('cmb_jepenempatan')!=0?null:$this->input->post('indate'),
						"TANGGAL_SAMPAI" 	=> $this->input->post('cmb_jepenempatan')!=0?null:$this->input->post('untildate'),
						"KD_SUBBAGIAN" 		=> $this->input->post('cmb_subbagian'),
						"JENIS_PENEMPATAN" 		=> $this->input->post('cmb_jepenempatan'),
					);
			if ($this->upload->do_upload('file_penempatan')){
				$detail_penempatan_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->update('detail_penempatan_pegawai', $detail_penempatan_pegawai, array('KD_PENEMPATAN'=> $this->input->post('kd_penempatan')));
		}else{
			// Insert baru
			$KD_PENEMPATAN = $this->db->query("SELECT COALESCE(MAX(KD_PENEMPATAN), 0)+1 as ID FROM detail_penempatan_pegawai")->row();
			$detail_penempatan_pegawai = array(
						"KD_PENEMPATAN" 	=> $KD_PENEMPATAN->ID,
						"ID_PEGAWAI" 		=> $id_peg,
						"KD_BAGIAN" 		=> $this->input->post('cmb_bagian'),
						"KD_GOLONGAN" 		=> $this->input->post('cmb_golongan'),
						"KD_JABATAN" 		=> $this->input->post('cmb_jabatan'),
						"KD_PANGKAT" 		=> $this->input->post('cmb_pangkat'),
						"KD_EKSELON" 		=> $this->input->post('cmb_ekselon'),
						"KD_JENIS_PEGAWAI"	=> $this->input->post('cmb_jenis_pegawai'),
						"KD_STATUS_PEGAWAI" => $this->input->post('cmb_status_pegawai'),
						"KD_LOKASI" 		=> $this->input->post('cmb_lokasi'),
						"TANGGAL_MULAI" 	=> $this->input->post('cmb_jepenempatan')!=0?null:$this->input->post('indate'),
						"TANGGAL_SAMPAI" 	=> $this->input->post('cmb_jepenempatan')!=0?null:$this->input->post('untildate'),
						"KD_SUBBAGIAN" 		=> $this->input->post('cmb_subbagian'),
						"JENIS_PENEMPATAN" 		=> $this->input->post('cmb_jepenempatan')
					);
			if ($this->upload->do_upload('file_penempatan')){
				$detail_penempatan_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->insert('detail_penempatan_pegawai', $detail_penempatan_pegawai);			
		}
		if($this->db->affected_rows() > 0){
			// redirect("pegawai/cpegawai/edit_detail/".$id_peg);
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}
	public function update_data_pendidikan($no_auto)
	{
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;
		// var_dump($this->input->post());
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_pendidikan')){
			// Edit data
			$detail_pendidikan_pegawai = array(
						"NAMA_LEMBAGA" 			=> $this->input->post('txt_lembaga'),
						"KD_JENJANG" 			=> $this->input->post('cmb_jenjang'),
						"ALAMAT" 				=> $this->input->post('txt_lembaga_alamat'),
						"PERIODE_MULAI" 		=> $this->input->post('periode_dari'),
						"PERIODE_SAMPAI" 		=> $this->input->post('periode_sampai'),
						"IPK" 					=> $this->input->post('txt_ipk'),
						"KD_JURUSAN" 			=> $this->input->post('cmb_jurusan'),
						"KD_STATUS_LULUS"		=> $this->input->post('cmb_lulus'),
						"KD_JENIS_PENDIDIKAN" 	=> $this->input->post('cmb_j_pendidikan'),
						"NAMA_PEJABAT" 			=> $this->input->post('txt_nmpejabat'),
						"NO_IJAZAH" 			=> $this->input->post('txt_ijazah')
					);
			if ($this->upload->do_upload('file_pendidikan')){
				$detail_pendidikan_pegawai['GAMBAR_IJAZAH'] = $this->upload->file_name;
			}
			$this->db->update('detail_pendidikan_pegawai', $detail_pendidikan_pegawai, array('KD_PENDIDIKAN'=> $this->input->post('kd_pendidikan')));
		}else{
			// Insert baru
			$KD_PENDIDIKAN = $this->db->query("SELECT COALESCE(MAX(KD_PENDIDIKAN), 0)+1 as ID FROM detail_pendidikan_pegawai")->row();
			$detail_pendidikan_pegawai = array(
						"KD_PENDIDIKAN" 		=> $KD_PENDIDIKAN->ID,
						"ID_PEGAWAI" 			=> $id_peg,
						"NAMA_LEMBAGA" 			=> $this->input->post('txt_lembaga'),
						"KD_JENJANG" 			=> $this->input->post('cmb_jenjang'),
						"ALAMAT" 				=> $this->input->post('txt_lembaga_alamat'),
						"PERIODE_MULAI" 		=> $this->input->post('periode_dari'),
						"PERIODE_SAMPAI" 		=> $this->input->post('periode_sampai'),
						"IPK" 					=> $this->input->post('txt_ipk'),
						"KD_JURUSAN" 			=> $this->input->post('cmb_jurusan'),
						"KD_STATUS_LULUS"		=> $this->input->post('cmb_lulus'),
						"KD_JENIS_PENDIDIKAN" 	=> $this->input->post('cmb_j_pendidikan'),
						"NAMA_PEJABAT" 			=> $this->input->post('txt_nmpejabat'),
						"NO_IJAZAH" 			=> $this->input->post('txt_ijazah')
					);
			if ($this->upload->do_upload('file_pendidikan')){
				$detail_pendidikan_pegawai['GAMBAR_IJAZAH'] = $this->upload->file_name;
			}
			$this->db->insert('detail_pendidikan_pegawai', $detail_pendidikan_pegawai);
			
		}
		if($this->db->affected_rows() > 0){
			// redirect("pegawai/cpegawai/edit_detail/".$id_peg);
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
		
	}
	public function update_data_pengalaman($no_auto)
	{
		
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;
		// var_dump($this->input->post());
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_pengalaman')){
			// Edit data
			$detail_pengalaman_pegawai = array(
						"NAMA_PERUSAHAAN"		=> $this->input->post('txt_nama_perusahaan'),
						"KD_JABATAN" 			=> $this->input->post('cmb_jab_pengalaman'),
						"ALAMAT" 				=> $this->input->post('txt_alamat_perusahaan'),
						"KD_JENIS_PENGALAMAN" 	=> $this->input->post('cmb_jenis_pengalaman'),
						"KD_PROVINSI" 			=> $this->input->post('cmb_prov_pengalaman'),
						"KD_KABUPATEN" 			=> $this->input->post('cmb_kab_pengalaman'),
						"KD_KECAMATAN" 			=> $this->input->post('cmb_kec_pengalaman'),
						"KD_KELURAHAN" 			=> $this->input->post('cmb_kel_pengalaman'),
						"TANGGAL_MULAI" 		=> $this->input->post('periode_pengalaman_awal'),
						"TANGGAL_SAMPAI" 		=> $this->input->post('periode_pengalaman_akhir')
					);
			if ($this->upload->do_upload('file_pengalaman')){
				$detail_pengalaman_pegawai['IMAGE_SK'] = $this->upload->file_name;
			}
			$this->db->update('detail_pengalaman_pegawai', $detail_pengalaman_pegawai, array('KD_PENGALAMAN'=> $this->input->post('kd_pengalaman')));
		}else{
			// Insert baru
			$KD_PENGALAMAN = $this->db->query("SELECT COALESCE(MAX(KD_PENGALAMAN), 0)+1 as ID FROM detail_pengalaman_pegawai")->row();
			$detail_pengalaman_pegawai = array(
						"KD_PENGALAMAN" 		=> $KD_PENGALAMAN->ID,
						"ID_PEGAWAI" 			=> $id_peg,
						"NAMA_PERUSAHAAN"		=> $this->input->post('txt_nama_perusahaan'),
						"KD_JABATAN" 			=> $this->input->post('cmb_jab_pengalaman'),
						"ALAMAT" 				=> $this->input->post('txt_alamat_perusahaan'),
						"KD_JENIS_PENGALAMAN" 	=> $this->input->post('cmb_jenis_pengalaman'),
						"KD_PROVINSI" 			=> $this->input->post('cmb_prov_pengalaman'),
						"KD_KABUPATEN" 			=> $this->input->post('cmb_kab_pengalaman'),
						"KD_KECAMATAN" 			=> $this->input->post('cmb_kec_pengalaman'),
						"KD_KELURAHAN" 			=> $this->input->post('cmb_kel_pengalaman'),
						"TANGGAL_MULAI" 		=> $this->input->post('periode_pengalaman_awal'),
						"TANGGAL_SAMPAI" 		=> $this->input->post('periode_pengalaman_akhir')
					);
			if ($this->upload->do_upload('file_pengalaman')){
				$detail_pengalaman_pegawai['IMAGE_SK'] = $this->upload->file_name;
			}
			$this->db->insert('detail_pengalaman_pegawai', $detail_pengalaman_pegawai);
		}
		if($this->db->affected_rows() > 0){
			// redirect("pegawai/cpegawai/edit_detail/".$id_peg);
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}
	public function update_data_keahlian($no_auto)
	{
		
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;
		// var_dump($this->input->post());
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		$nama_keahlian = $this->input->post('nama_keahlian');
		$cmb_status_keahlian = $this->input->post('cmb_status_keahlian');
		$ket_keahlian = $this->input->post('ket_keahlian');
		$tglmulai = $this->input->post('txt_tglmulai_keahlian');
		$tglsampai = $this->input->post('txt_tglsampai_keahlian');

		if($this->input->post('id_keahlian')){
			// Edit data
			$detail_keahlian_pegawai = array(
				'NAMA_KEAHLIAN' 		=> $nama_keahlian,
				'KD_STATUS_KEAHLIAN' 	=> $cmb_status_keahlian,
				'KETERANGAN' 			=> $ket_keahlian,
				'MULAI' 				=> $tglmulai,
				'SAMPAI' 				=> $tglsampai
			);

			if ($this->upload->do_upload('file_keahlian')){
				$detail_keahlian_pegawai['IMAGE'] = $this->upload->file_name;
			}

			$this->db->update('detail_keahlian_pegawai', $detail_keahlian_pegawai, array('KD_KEAHLIAN'=> $this->input->post('id_keahlian')));
		}else{
			// Insert baru
			$KD_KEAHLIAN = $this->db->query("SELECT COALESCE(MAX(KD_KEAHLIAN), 0)+1 as ID FROM detail_keahlian_pegawai")->row();
			$detail_keahlian_pegawai = array(
				"KD_KEAHLIAN" 			=> $KD_KEAHLIAN->ID,
				"ID_PEGAWAI" 			=> $id_peg,
				"NAMA_KEAHLIAN" 		=> $nama_keahlian,
				"KD_STATUS_KEAHLIAN" 	=> $cmb_status_keahlian,
				"KETERANGAN" 			=> $ket_keahlian,
				'MULAI' 				=> $tglmulai,
				'SAMPAI' 				=> $tglsampai
			);

			if ($this->upload->do_upload('file_keahlian')){
				$detail_keahlian_pegawai['IMAGE'] = $this->upload->file_name;
			}

			$this->db->insert('detail_keahlian_pegawai', $detail_keahlian_pegawai);
		}

		if($this->db->affected_rows() > 0){
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}
	public function update_data_keluarga($no_auto)
	{
		
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;
		// var_dump($this->input->post());
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_keluarga')){
			// Edit data
			$detail_keluarga_pegawai = array(
						"NAMA" 					=> $this->input->post('txt_nmkel'),
						"KD_HUBUNGAN" 			=> $this->input->post('cmb_hub_kel'),
						"TEMPAT_LAHIR" 			=> $this->input->post('txt_tmptlahirkel'),
						"TGL_LAHIR" 			=> $this->input->post('tgllahir_kel'),
						"KD_PEKERJAAN" 			=> $this->input->post('cmb_pekerjaan_kel'),
						"KD_JENJANG" 			=> $this->input->post('cmb_pendidikan_kel'),
						"KD_STATUS_ASURANSI" 	=> $this->input->post('cmb_status_asuransi'),
						"KD_ASURANSI" 			=> $this->input->post('cmb_nmasuransi'),
						"JENIS_KELAMIN" 		=> $this->input->post('cmb_kelamin_kel'),
						"KD_GOLDA" 				=> $this->input->post('cmb_goldarah_kel'),
						"NO_ASURANSI"			=> $this->input->post('txt_noasuransikel'),
						"NIK"					=> $this->input->post('txt_nikkel')
					);
			if ($this->upload->do_upload('file_keluarga')){
				$detail_keluarga_pegawai['IMAGE_AKTE_KTP'] = $this->upload->file_name;
			}
			$this->db->update('detail_keluarga_pegawai', $detail_keluarga_pegawai, array('KD_KELUARGA'=> $this->input->post('kd_keluarga')));
		}else{
			// Insert baru
			$KD_KELUARGA = $this->db->query("SELECT COALESCE(MAX(KD_KELUARGA), 0)+1 as ID FROM detail_keluarga_pegawai")->row();
			$detail_keluarga_pegawai = array(
						"KD_KELUARGA" 			=> $KD_KELUARGA->ID,
						"ID_PEGAWAI" 			=> $id_peg,
						"NAMA" 					=> $this->input->post('txt_nmkel'),
						"KD_HUBUNGAN" 			=> $this->input->post('cmb_hub_kel'),
						"TEMPAT_LAHIR" 			=> $this->input->post('txt_tmptlahirkel'),
						"TGL_LAHIR" 			=> $this->input->post('tgllahir_kel'),
						"KD_PEKERJAAN" 			=> $this->input->post('cmb_pekerjaan_kel'),
						"KD_JENJANG" 			=> $this->input->post('cmb_pendidikan_kel'),
						"KD_STATUS_ASURANSI" 	=> $this->input->post('cmb_status_asuransi'),
						"KD_ASURANSI" 			=> $this->input->post('cmb_nmasuransi'),
						"JENIS_KELAMIN" 		=> $this->input->post('cmb_kelamin_kel'),
						"KD_GOLDA" 				=> $this->input->post('cmb_goldarah_kel'),
						"NO_ASURANSI"			=> $this->input->post('txt_noasuransikel'),
						"NIK"					=> $this->input->post('txt_nikkel')
					);
			if ($this->upload->do_upload('file_keluarga')){
				$detail_keluarga_pegawai['IMAGE_AKTE_KTP'] = $this->upload->file_name;
			}
			$this->db->insert('detail_keluarga_pegawai', $detail_keluarga_pegawai);
		}
		if($this->db->affected_rows() > 0){
			// redirect("pegawai/cpegawai/edit_detail/".$id_peg);
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}
	public function delete_data()
	{
		if($this->input->post('mode')=='pendidikan'){
			$this->db->delete('detail_pendidikan_pegawai', array('KD_PENDIDIKAN'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='pengalaman'){
			$this->db->delete('detail_pengalaman_pegawai', array('KD_PENGALAMAN'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='penempatan'){
			$this->db->delete('detail_penempatan_pegawai', array('KD_PENEMPATAN'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='keahlian'){
			$this->db->delete('detail_keahlian_pegawai', array('KD_KEAHLIAN'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='keluarga'){
			$this->db->delete('detail_keluarga_pegawai', array('KD_KELUARGA'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='pelatihan'){
			$this->db->delete('detail_pelatihan_pegawai', array('KD_PELATIHAN'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='kemampuan'){
			$this->db->delete('detail_kemampuan_pegawai', array('KD_KEMAMPUAN'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='str'){
			$this->db->delete('detail_str_pegawai', array('KD_STR'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='sip'){
			$this->db->delete('detail_sip_pegawai', array('KD_SIP'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='mcu'){
			$this->db->delete('detail_mcu_pegawai', array('KD_MCU'=>$this->input->post('id')));
		}
		else if($this->input->post('mode')=='pegawai'){
			$data_id = $this->db->query("SELECT ID_PEGAWAI FROM pegawai WHERE NO_AUTO={$this->input->post('id')}")->row();
			$id_peg=$data_id->ID_PEGAWAI;

			$this->db->delete('detail_pendidikan_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_pengalaman_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_penempatan_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_keahlian_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_keluarga_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_pelatihan_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_kemampuan_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_str_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_sip_pegawai', array('ID_PEGAWAI'=>$id_peg));
			$this->db->delete('detail_mcu_pegawai', array('ID_PEGAWAI'=>$id_peg));
			
			$this->db->delete('pegawai', array('NO_AUTO'=>$this->input->post('id')));
		}
		
		if($this->db->affected_rows() > 0){
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}

	public function update_data_pelatihan($no_auto)
	{
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM PEGAWAI WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;
		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_pelatihan')){
			// Edit data
			$detail_pelatihan_pegawai = array(
				"ID_PEGAWAI"			=> $id_peg,
				"TGL_PELATIHAN"			=> $this->input->post('txt_tglpelatihan'),
				"NAMA_PELATIHAN"		=> $this->input->post('txt_nmpelatihan'),
				"LOKASI_PELATIHAN"		=> $this->input->post('txt_lokasipelatihan'),
				"NARA_SUMBER"			=> $this->input->post('txt_nrsumberpelatihan'),
				"POINT_SKP"				=> $this->input->post('txt_pointskp')
			);

			if ($this->upload->do_upload('file_pelatihan')){
				$detail_pelatihan_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->update('detail_pelatihan_pegawai', $detail_pelatihan_pegawai, array('KD_PELATIHAN'=> $this->input->post('kd_pelatihan')));
		}else{
			// Insert baru
			$KD_PELATIHAN = $this->db->query("SELECT COALESCE(MAX(KD_PELATIHAN), 0)+1 as ID FROM detail_pelatihan_pegawai")->row();
			$detail_pelatihan_pegawai = array(
				"KD_PELATIHAN"			=> $KD_PELATIHAN->ID,
				"ID_PEGAWAI"			=> $id_peg,
				"TGL_PELATIHAN"			=> $this->input->post('txt_tglpelatihan'),
				"NAMA_PELATIHAN"		=> $this->input->post('txt_nmpelatihan'),
				"LOKASI_PELATIHAN"		=> $this->input->post('txt_lokasipelatihan'),
				"NARA_SUMBER"			=> $this->input->post('txt_nrsumberpelatihan'),
				"POINT_SKP"				=> $this->input->post('txt_pointskp')
			);

			if ($this->upload->do_upload('file_pelatihan')){
				$detail_pelatihan_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->insert('detail_pelatihan_pegawai', $detail_pelatihan_pegawai);
		}

		if($this->db->affected_rows() > 0){
			// redirect("pegawai/cpegawai/edit_detail/".$id_peg);
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}

	public function update_data_kemampuan($no_auto)
	{
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM PEGAWAI WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;

		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size']	= '10240';
		$config['maxawidth']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_kemampuan')){
			// Edit data
			$detail_kemampuan_pegawai = array(
				"ID_PEGAWAI"			=> $id_peg,
				"NAMA_KEMAMPUAN"		=> $this->input->post('txt_nmkemampuan'),
				"KD_LEVEL"				=> $this->input->post('cmb_levelkemampuan')
			);

			if ($this->upload->do_upload('file_kemampuan')){
				$detail_kemampuan_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->update('detail_kemampuan_pegawai', $detail_kemampuan_pegawai, array('KD_KEMAMPUAN'=> $this->input->post('kd_kemampuan')));
		}else{
			// Insert baru
			$KD_KEMAMPUAN = $this->db->query("SELECT COALESCE(MAX(KD_KEMAMPUAN), 0)+1 as ID FROM detail_kemampuan_pegawai")->row();

			$detail_kemampuan_pegawai = array(
				"KD_KEMAMPUAN"			=> $KD_KEMAMPUAN->ID,
				"ID_PEGAWAI"			=> $id_peg,
				"NAMA_KEMAMPUAN"		=> $this->input->post('txt_nmkemampuan'),
				"KD_LEVEL"				=> $this->input->post('cmb_levelkemampuan')
			);

			if ($this->upload->do_upload('file_kemampuan')){
				$detail_kemampuan_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->insert('detail_kemampuan_pegawai', $detail_kemampuan_pegawai);
		}

		if($this->db->affected_rows() > 0){
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}

	public function update_data_str($no_auto)
	{
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM PEGAWAI WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;

		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_str')){
			// Edit data
			$detail_str_pegawai = array(
				"ID_PEGAWAI"			=> $id_peg,
				"NO_STR"				=> $this->input->post('txt_nostr'),
				// "NAMA_STR"				=> $this->input->post('txt_nmstr'),
				"YANG_MENGESAHKAN"		=> $this->input->post('txt_ygmengesahkanstr'),
				"MULAI"					=> $this->input->post('txt_tglmulaistr'),
				"SAMPAI"				=> $this->input->post('txt_tglsampaistr')
			);

			if ($this->upload->do_upload('file_str')){
				$detail_str_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->update('detail_str_pegawai', $detail_str_pegawai, array('KD_STR'=> $this->input->post('kd_str')));
		}else{
			// Insert baru
			$KD_STR = $this->db->query("SELECT COALESCE(MAX(KD_STR), 0)+1 as ID FROM detail_str_pegawai")->row();

			$detail_str_pegawai = array(
				"KD_STR"				=> $KD_STR->ID,
				"ID_PEGAWAI"			=> $id_peg,
				"NO_STR"				=> $this->input->post('txt_nostr'),
				// "NAMA_STR"				=> $this->input->post('txt_nmstr'),
				"YANG_MENGESAHKAN"		=> $this->input->post('txt_ygmengesahkanstr'),
				"MULAI"					=> $this->input->post('txt_tglmulaistr'),
				"SAMPAI"				=> $this->input->post('txt_tglsampaistr')
			);

			if ($this->upload->do_upload('file_str')){
				$detail_str_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->insert('detail_str_pegawai', $detail_str_pegawai);
		}

		if($this->db->affected_rows() > 0){
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}

	public function update_data_sip($no_auto)
	{
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM PEGAWAI WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;

		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_sip')){
			// Edit data
			$detail_sip_pegawai = array(
				"ID_PEGAWAI"			=> $id_peg,
				"NO_SIP"				=> $this->input->post('txt_nosip'),
				"KD_JENIS_SIP"			=> $this->input->post('cmb_jenissip'),
				"NAMA_SIP"				=> $this->input->post('txt_nmsip'),
				// "YANG_MENGESAHKAN"		=> $this->input->post('txt_ygmengesahkansip'),
				"MULAI"					=> $this->input->post('txt_tglmulaisip'),
				"SAMPAI"				=> $this->input->post('txt_tglsampaisip')
			);

			if ($this->upload->do_upload('file_sip')){
				$detail_sip_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->update('detail_sip_pegawai', $detail_sip_pegawai, array('KD_SIP'=> $this->input->post('kd_sip')));
		}else{
			// Insert baru
			$KD_SIP = $this->db->query("SELECT COALESCE(MAX(KD_SIP), 0)+1 as ID FROM detail_sip_pegawai")->row();

			$detail_sip_pegawai = array(
				"KD_SIP"				=> $KD_SIP->ID,
				"ID_PEGAWAI"			=> $id_peg,
				"NO_SIP"				=> $this->input->post('txt_nosip'),
				"KD_JENIS_SIP"			=> $this->input->post('cmb_jenissip'),
				"NAMA_SIP"				=> $this->input->post('txt_nmsip'),
				// "YANG_MENGESAHKAN"		=> $this->input->post('txt_ygmengesahkansip'),
				"MULAI"					=> $this->input->post('txt_tglmulaisip'),
				"SAMPAI"				=> $this->input->post('txt_tglsampaisip')
			);

			if ($this->upload->do_upload('file_sip')){
				$detail_sip_pegawai['IMAGE'] = $this->upload->file_name;
			}
			$this->db->insert('detail_sip_pegawai', $detail_sip_pegawai);
		}

		if($this->db->affected_rows() > 0){
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}

	public function update_data_mcu($no_auto)
	{
		$data_id = $this->db->query("SELECT ID_PEGAWAI FROM PEGAWAI WHERE NO_AUTO={$no_auto}")->row();
		$id_peg=$data_id->ID_PEGAWAI;

		$config['upload_path'] = './assets/user/';
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size']	= '10240';
		$config['max_width']  = '9000';
		$config['max_height']  = '8000';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$this->upload->initialize($config);

		if($this->input->post('kd_mcu')){
			// Edit data
			$detail_mcu_pegawai = array(
				"ID_PEGAWAI"	=> $id_peg,
				"NAMA_RS"		=> $this->input->post('txt_namars'),
				"TANGGAL_MCU"	=> $this->input->post('txt_tglmcu'),
				"HASIL_MCU"		=> $this->input->post('txt_hasilmcu'),
				"DOKTER_MCU"	=> $this->input->post('txt_doktermcu')
			);

			if ($this->upload->do_upload('file_mcu')){
				$detail_mcu_pegawai['IMAGE'] = $this->upload->file_name;
			}

			$this->db->update('detail_mcu_pegawai', $detail_mcu_pegawai, array('KD_MCU'=> $this->input->post('kd_mcu')));
		}else{
			// Insert baru
			$KD_MCU = $this->db->query("SELECT COALESCE(MAX(KD_MCU), 0)+1 as ID FROM detail_mcu_pegawai")->row();

			$detail_mcu_pegawai = array(
				"KD_MCU"		=> $KD_MCU->ID,
				"ID_PEGAWAI"	=> $id_peg,
				"NAMA_RS"		=> $this->input->post('txt_namars'),
				"TANGGAL_MCU"	=> $this->input->post('txt_tglmcu'),
				"HASIL_MCU"		=> $this->input->post('txt_hasilmcu'),
				"DOKTER_MCU"	=> $this->input->post('txt_doktermcu')
			);

			if ($this->upload->do_upload('file_mcu')){
				$detail_mcu_pegawai['IMAGE'] = $this->upload->file_name;
			}

			$this->db->insert('detail_mcu_pegawai', $detail_mcu_pegawai);
		}

		if($this->db->affected_rows() > 0){
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'sukses';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}else{
			$output = array('status' => true, 'results' => '' );
			$output['results'] = 'no update';
			$this->output->set_content_type('application/json')->set_output(json_encode($output, JSON_PRETTY_PRINT));
		}
	}
}

/* End of file Profile.php */
/* Location: ./application/modules/setting/controllers/Profile.php */

