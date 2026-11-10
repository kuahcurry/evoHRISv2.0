<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_buku extends CI_Model {

	/**
	 * Menampilkan File Buku Tanah
	 *
	 * @return Query
	 **/
	public function file($ID=0, $id_bukutanah = NULL)
	{
		if(!$ID) :
			$query = $this->db->query("SELECT * FROM tb_file_buku WHERE id_bukutanah = '{$id_bukutanah}' ORDER BY mime_type = 'image/jpeg'")->result();
		else :
			$query = $this->db->query("SELECT * FROM tb_file_buku WHERE id = {$ID}")->row();
		endif;
		
		return $query;
	}

	/**
	 * Mengecek Keterangan Buku, pada status peminjaman buku
	 *
	 * @return string true/false
	 **/
	public function keterangan($ID=0)
	{
		$query = $this->db->query("SELECT * FROM tb_pinjam_buku WHERE id_bukutanah = '{$ID}' AND status_pinjam = 'N'");
		if(!$query->num_rows()) :
			$data = false;
		else :
			$data = true;
		endif;
		return $data;
	}

	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	public function get_pinjam($ID=0)
	{
		$query = $this->db->query("SELECT * FROM tb_pinjam_buku WHERE id_bukutanah = '{$ID}' AND status_pinjam = 'N'");
		return $query->row();
	}

	/**
	 * Menampilkan Data Peminjaman Buku Tanah pada Data Paging
	 *
	 * @return Query
	 **/
	public function peminjaman(Array $data, $limit = 20, $offset = 0)
	{
		$this->db->join('tb_buku_tanah', 'tb_pinjam_buku.id_bukutanah = tb_buku_tanah.id_bukutanah');
		$this->db->join('tb_users', 'tb_pinjam_buku.nip = tb_users.nip');
		$this->db->join('tb_hak_milik', 'tb_buku_tanah.id_hak = tb_hak_milik.id_hak');

		if ($data['jenishak'] != '') $this->db->where('tb_buku_tanah.id_hak', $data['jenishak']);
		if ($data['nohak'] != '') $this->db->where('tb_buku_tanah.no_hakbuku', $data['nohak']);
		if ($data['desa'] != '') $this->db->where('tb_buku_tanah.id_desa', $data['desa']);
		if ($data['petugas'] != '') $this->db->where('tb_pinjam_buku.nip', $data['petugas']);
		$this->db->where('tb_pinjam_buku.tgl_peminjaman <=', $data['to']);
		$this->db->where('tb_pinjam_buku.tgl_peminjaman >=', $data['from']);
		$query = $this->db->get('tb_pinjam_buku', $limit, $offset);
		return $query->result();
	}

	/**
	 * menghitung Data Pinjmana Buku Tanah pada Data Paging
	 *
	 * @return Integer
	 **/
	public function count_peminjaman(Array $data)
	{
		$this->db->join('tb_buku_tanah', 'tb_pinjam_buku.id_bukutanah = tb_buku_tanah.id_bukutanah');
		$this->db->join('tb_users', 'tb_pinjam_buku.nip = tb_users.nip');
		$this->db->join('tb_hak_milik', 'tb_buku_tanah.id_hak = tb_hak_milik.id_hak');

		if ($data['jenishak'] != '') $this->db->where('tb_buku_tanah.id_hak', $data['jenishak']);
		if ($data['nohak'] != '') $this->db->where('tb_buku_tanah.no_hakbuku', $data['nohak']);
		if ($data['desa'] != '') $this->db->where('tb_buku_tanah.id_desa', $data['desa']);
		if ($data['petugas'] != '') $this->db->where('tb_pinjam_buku.nip', $data['petugas']);
		$this->db->where('tb_pinjam_buku.tgl_peminjaman <=', $data['from']);
		$this->db->where('tb_pinjam_buku.tgl_peminjaman >=', $data['to']);
		$query = $this->db->get('tb_pinjam_buku');
		return $query->num_rows();
	}

	/**
	 * menghitung Data Pinjmana Buku Tanah pada Data Paging
	 *
	 * @return Integer
	 **/
	public function laporan_pinjam($bln = 0, $thn = 0)
	{
		$query = $this->db->query("SELECT tb_pinjam_buku.*, tb_buku_tanah.*, tb_hak_milik.*, tb_users.* FROM tb_pinjam_buku 
			INNER JOIN tb_buku_tanah ON tb_pinjam_buku.id_bukutanah = tb_buku_tanah.id_bukutanah JOIN tb_hak_milik ON tb_buku_tanah.id_hak = tb_hak_milik.id_hak JOIN tb_users ON tb_pinjam_buku.nip = tb_users.nip WHERE tb_pinjam_buku.bulan = '{$bln}' AND tb_pinjam_buku.tahun = '{$thn}'");
		return $query->result();
	}


	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	
}

/* End of file M_buku.php */
/* Location: ./application/modules/apps/models/M_buku.php */