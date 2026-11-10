<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Halaman Index Buku Aplikasi
 *
 * Controller Ini sudah dialihkan pada  ./../config/routes.php
 * @access http://example_root/buku
 * @package Apps - Class App_buku.php
 * @author https://facebook.com/muh.azzain
 **/
class TambahController extends CI_Controller {
	private $name_user;
	private $nip;
	private $level_akses;
	
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
		$this->level_akses = $data_session['level_akses'];
		$this->load->library(array('session','upload','encrypt','Excel/PHPExcel','pdf'));
		$this->load->model(array('m_apps','m_buku','mtrash'));
		$this->load->helper(array('form','url','html'));
	}
	public function index()
	{
		
		$page = ($this->input->get('page')) ? $this->input->get('page') : 0;
		$filter_cari = $this->input->get('filter');
        $config = pagination_list();
        $config['base_url'] = site_url("pegawai/ctambahpegawai?data=datapegawai&filter=".$filter_cari);
		$config['per_page'] = 10;
		$config['uri_segment'] = 4;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'page';
		$filter = "";
		if ($this->level_akses=='admin' || $this->level_akses=='super_admin')
		{
			$filter .= "";
		}
		else if ($this->level_akses=='kepala' )
		{
			$getkd_bagian = $this->db->query("SELECT  peg.*,dpp.KD_BAGIAN,bag.BAGIAN,
									dpp.KD_SUBBAGIAN,sub.sub_bagian,dpp.KD_JABATAN,jab.JABATAN,dpp.KD_PENEMPATAN,i.INSTALASI 
									FROM pegawai peg inner join
									(SELECT ID_PEGAWAI,max(KD_PENEMPATAN) KD_PENEMPATAN  FROM detail_penempatan_pegawai
									GROUP BY ID_PEGAWAI) vdpp on peg.ID_PEGAWAI=vdpp.ID_PEGAWAI 
									INNER JOIN detail_penempatan_pegawai dpp on vdpp.KD_PENEMPATAN=dpp.KD_PENEMPATAN
									INNER JOIN bagian bag on dpp.KD_BAGIAN= bag.KD_BAGIAN
									left JOIN sub_bagian sub on dpp.KD_SUBBAGIAN=sub.kd_sub_bagian
									LEFT JOIN jabatan jab on dpp.KD_JABATAN=jab.KD_JABATAN 
									left  JOIN instalasi i on bag.KD_INSTALASI=i.KD_INSTALASI
									where peg.nip='".$this->nip ."'")->result();
									
			foreach($getkd_bagian as $baris)
			{
				$kd_bagian=$baris->KD_BAGIAN;
			}			
			$filter .=" and dpp.KD_BAGIAN='".$kd_bagian."'";
			}
			else
			{
				$filter .=" and peg.nip='".$this->nip."'";
			}


			if($filter_cari!=''){
				$filter.=" and (peg.nip like '%{$filter_cari}%' or peg.nama like '%{$filter_cari}%' or peg.id_pegawai like '%{$filter_cari}%')";
			}
		
		$config['total_rows'] = $this->db->query("SELECT  peg.*,dpp.KD_BAGIAN,bag.BAGIAN,
									dpp.KD_SUBBAGIAN,sub.sub_bagian,dpp.KD_JABATAN,jab.JABATAN,dpp.KD_PENEMPATAN,i.INSTALASI 
									FROM pegawai peg left join
									(SELECT ID_PEGAWAI,max(KD_PENEMPATAN) KD_PENEMPATAN  FROM detail_penempatan_pegawai
									GROUP BY ID_PEGAWAI) vdpp on peg.ID_PEGAWAI=vdpp.ID_PEGAWAI 
									left JOIN detail_penempatan_pegawai dpp on vdpp.KD_PENEMPATAN=dpp.KD_PENEMPATAN
									left JOIN bagian bag on dpp.KD_BAGIAN= bag.KD_BAGIAN
									left JOIN sub_bagian sub on dpp.KD_SUBBAGIAN=sub.kd_sub_bagian
									LEFT JOIN jabatan jab on dpp.KD_JABATAN=jab.KD_JABATAN 
									left  JOIN instalasi i on bag.KD_INSTALASI=i.KD_INSTALASI
									where 1=1 ".$filter."")->num_rows();
        $this->pagination->initialize($config);

		$data = array(
					'title' => 'Data Pegawai'.DEFAULT_TITLE,
					'data_pegawai' => $this->db->query("SELECT  peg.*,dpp.KD_BAGIAN,bag.BAGIAN,
									dpp.KD_SUBBAGIAN,sub.sub_bagian,dpp.KD_JABATAN,jab.JABATAN,dpp.KD_PENEMPATAN,i.INSTALASI 
									FROM pegawai peg left join
									(SELECT ID_PEGAWAI,max(KD_PENEMPATAN) KD_PENEMPATAN  FROM detail_penempatan_pegawai
									GROUP BY ID_PEGAWAI) vdpp on peg.ID_PEGAWAI=vdpp.ID_PEGAWAI 
									left JOIN detail_penempatan_pegawai dpp on vdpp.KD_PENEMPATAN=dpp.KD_PENEMPATAN
									left JOIN bagian bag on dpp.KD_BAGIAN= bag.KD_BAGIAN
									left JOIN sub_bagian sub on dpp.KD_SUBBAGIAN=sub.kd_sub_bagian
									LEFT JOIN jabatan jab on dpp.KD_JABATAN=jab.KD_JABATAN 
									left  JOIN instalasi i on bag.KD_INSTALASI=i.KD_INSTALASI
									where 1=1 ".$filter." ORDER BY
	peg.nama ASC limit {$config['per_page']} offset ".$page."")->result(),
					'filter_cari'=>$filter_cari

		);
		// var_dump($data);
		$this->template->view('pegawai/v_add_pegawai', $data);

	}

	public function cetak_riwayat(){
		$kd_auto = $this->input->get('no_auto');
		$datapeg = $this->db->query("										SELECT
											peg.id_pegawai,
											peg.nip,
											peg.nama,
											peg.alamat,
											peg.jenis_kelamin,
											peg.KD_AGAMA,
											peg.tempat_lahir,
											peg.tanggal_lahir,
											dpp.KD_BAGIAN,
										
											bag.BAGIAN,
											dpp.KD_SUBBAGIAN,
											sub.sub_bagian,
											dpp.KD_JABATAN,
											jab.JABATAN,
											dpp.KD_PENEMPATAN,
											golongan
										FROM
											pegawai peg
										LEFT JOIN (
											SELECT
												ID_PEGAWAI,
												max(KD_PENEMPATAN) KD_PENEMPATAN
											FROM
												detail_penempatan_pegawai
											GROUP BY
												ID_PEGAWAI
										) vdpp ON peg.ID_PEGAWAI = vdpp.ID_PEGAWAI
										LEFT JOIN detail_penempatan_pegawai dpp ON vdpp.KD_PENEMPATAN = dpp.KD_PENEMPATAN
										LEFT JOIN bagian bag ON dpp.KD_BAGIAN = bag.KD_BAGIAN
										LEFT JOIN sub_bagian sub ON dpp.KD_SUBBAGIAN = sub.kd_sub_bagian
										LEFT JOIN jabatan jab ON dpp.KD_JABATAN = jab.KD_JABATAN
										LEFT JOIN golongan gol ON dpp.kd_golongan = gol.KD_GOLONGAN
												
	where NO_AUTO={$kd_auto}")->row();
		
		

		

		$data['peg']= $datapeg;
		
		
        $html=$this->load->view('app-html/pegawai/v_cetak_riwayat',$data,true);
 
        $filename = "Riwayat_Hidup";
        $this->pdf->create($html, $filename, 'potrait');
	}

	
}

/* End of file App_buku.php */
/* Location: ./application/modules/apps/controllers/App_buku.php */
