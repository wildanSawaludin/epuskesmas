<?php
class inv_ruangan_model extends CI_Model {

    var $tabel       = 'mst_inv_ruangan';
    var $t_puskesmas = 'cl_phc';
	var $lang	     = '';

    function __construct() {
        parent::__construct();
		$this->lang	  = $this->config->item('language');
    }
    
	function get_pilihan_kondisi(){
		$this->db->select('code as id, value as val');
		$this->db->where('tipe','keadaan_barang');
		$q = $this->db->get('mst_inv_pilihan');
		return $q;
	}
	function get_data_detail($start=0, $limit=9999999, $options=array()){
		$txt = "
					SELECT
				 *
			FROM
				(
					SELECT
						*
					FROM
						(
							SELECT
								*
							FROM
								(
									SELECT
										*
									FROM
										inv_inventaris_distribusi
									ORDER BY
										tgl_distribusi DESC
								) AS a
							WHERE
								a.tgl_distribusi <= ?
							GROUP BY
								a.id_inventaris_barang
						) AS b
					WHERE
						id_ruangan = ?
				) z
			INNER JOIN (
				SELECT
					id_inventaris_barang,
					id_mst_inv_barang,
					nama_barang,
					tanggal_pengadaan,
					year(tanggal) as tahun,
					harga,
					IFNULL(
						b.pilihan_keadaan_barang,
						a.pilihan_keadaan_barang
					) AS kondisi
				FROM
					inv_inventaris_barang a
				LEFT JOIN (
					SELECT
						*
					FROM
						inv_keadaan_barang
					WHERE
						tanggal <= ?
					ORDER BY
						tanggal DESC
				) b USING (id_inventaris_barang)	
				GROUP BY
					id_inventaris_barang
				ORDER BY
					tanggal DESC
			) x USING (id_inventaris_barang)
			WHERE
				
			 tgl_distribusi <= ?
			AND id_cl_phc = ?
			AND id_ruangan = ?

		";
		$tgl = $this->session->userdata('filter_tanggal');
		$id_cl_phc = $this->session->userdata('filter_code_cl_phc');
		$id_ruang = $this->session->userdata('filter_id_ruang');
		$query = $this->db->query($txt, array($tgl, $id_ruang, $tgl, $tgl, $id_cl_phc,$id_ruang));
		return $query->result();
	}
	function get_data_detailxxx($start=0,$limit=999999,$options=array()){
		//filter puskes
		if(!empty($this->session->userdata('filter_code_cl_phc')) and $this->session->userdata('filter_code_cl_phc') != 'none'){		
			$this->db->where('inv_inventaris_distribusi.id_cl_phc',$this->session->userdata('filter_code_cl_phc'));
			
			//filter ruang
			if(!empty($this->session->userdata('filter_id_ruang')) and $this->session->userdata('filter_id_ruang') != '0'){
				if($this->session->userdata('filter_id_ruang') == 'none'){
					$this->db->where('inv_inventaris_distribusi.id_ruangan','0');
				}else if($this->session->userdata('filter_id_ruang') == 'all'){
					
				}else{
					$this->db->where('inv_inventaris_distribusi.id_ruangan',$this->session->userdata('filter_id_ruang'));
				}
					
			}					
			
		}else{
			$this->db->where('inv_inventaris_distribusi.id_ruangan');
		}
		//filter date
		if(!empty($this->session->userdata('filter_tanggal')) and $this->session->userdata('filter_tanggal') != '0'){
			$this->db->where('inv_inventaris_distribusi.tgl_distribusi',$this->session->userdata('filter_tanggal'));
		}else{
			$this->db->where('inv_inventaris_distribusi.status','1');
		}
		
		$this->db->select('inv_inventaris_barang.id_inventaris_barang, id_mst_inv_barang, nama_barang, register, year(tanggal_pembelian) as tahun, pilihan_keadaan_barang, harga, barang_kembar_inv ');
		$this->db->join('inv_inventaris_distribusi','inv_inventaris_distribusi.id_inventaris_barang = inv_inventaris_barang.id_inventaris_barang ');
		$this->db->order_by('barang_kembar_inv');
		$q = $this->db->get('inv_inventaris_barang', $limit, $start);
		return $q->result();
	}
	function get_jumlah_kondisi($kondisi, $kembar){
		//filter puskes
		$tgl = $this->session->userdata('filter_tanggal');
			
			$txt = "select * from (SELECT * FROM 
					(
					SELECT * FROM
					(SELECT * FROM inv_inventaris_distribusi ORDER BY tgl_distribusi DESC) AS a WHERE a.tgl_distribusi <= ? 
					GROUP BY a.id_inventaris_barang
					) 
					AS b WHERE id_ruangan=?) z 
					inner JOIN (SELECT
						id_inventaris_barang, IFNULL(b.pilihan_keadaan_barang,a.pilihan_keadaan_barang) as kondisi
					FROM
						inv_inventaris_barang a
					LEFT JOIN (
						SELECT
							*
						FROM
							inv_keadaan_barang
						WHERE
							tanggal <= ?
						ORDER BY
							tanggal DESC
					) b USING (id_inventaris_barang)
					where a.barang_kembar_proc = ?
					GROUP BY
						id_inventaris_barang
					ORDER BY tanggal desc) x using(id_inventaris_barang)
					where barang_kembar_inv = ? and tgl_distribusi <= ?
					and id_cl_phc = ? and id_ruangan = ? and kondisi = ?";
			$code_cl_phc = $this->session->userdata('filter_code_cl_phc');
			$id_ruangan = $this->session->userdata('filter_id_ruang');
			$query = $this->db->query($txt,array($tgl, $id_ruangan, $tgl, $kembar, $kembar, $tgl, $code_cl_phc, $id_ruangan, $kondisi))->result_array();
			return count($query);		
	}
	function get_data_detail_group($start=0,$limit=9999999, $options=array()){
		$txt = "
					SELECT
				 *
			FROM
				(
					SELECT
						*
					FROM
						(
							SELECT
								*
							FROM
								(
									SELECT
										*
									FROM
										inv_inventaris_distribusi
									ORDER BY
										tgl_distribusi DESC
								) AS a
							WHERE
								a.tgl_distribusi <= ?
							GROUP BY
								a.id_inventaris_barang
						) AS b
					WHERE
						id_ruangan = ?
				) z
			INNER JOIN (
				SELECT
					id_inventaris_barang,
					id_mst_inv_barang,
					nama_barang,
					tanggal_pengadaan,
					year(tanggal) as tahun,
					barang_kembar_proc,
					harga,
					IFNULL(
						b.pilihan_keadaan_barang,
						a.pilihan_keadaan_barang
					) AS kondisi
				FROM
					inv_inventaris_barang a
				LEFT JOIN (
					SELECT
						*
					FROM
						inv_keadaan_barang
					WHERE
						tanggal <= ?
					ORDER BY
						tanggal DESC
				) b USING (id_inventaris_barang)	
				GROUP BY
					id_inventaris_barang
				ORDER BY
					tanggal DESC
			) x USING (id_inventaris_barang)
			WHERE
				
			 tgl_distribusi <= ?
			AND id_cl_phc = ?
			AND id_ruangan = ?
			group by barang_kembar_proc

		";
		$tgl = $this->session->userdata('filter_tanggal');
		$id_cl_phc = $this->session->userdata('filter_code_cl_phc');
		$id_ruang = $this->session->userdata('filter_id_ruang');
		$query = $this->db->query($txt, array($tgl, $id_ruang, $tgl, $tgl, $id_cl_phc,$id_ruang));
		return $query->result_array();
	}
	function get_data_detail_groupxxx($start=0,$limit=999999,$options=array()){
		//filter puskes
		if(!empty($this->session->userdata('filter_code_cl_phc')) and $this->session->userdata('filter_code_cl_phc') != 'none'){		
			$this->db->where('inv_inventaris_distribusi.id_cl_phc',$this->session->userdata('filter_code_cl_phc'));
			
			//filter ruang
			if(!empty($this->session->userdata('filter_id_ruang')) and $this->session->userdata('filter_id_ruang') != '0'){
				if($this->session->userdata('filter_id_ruang') == 'none'){
					$this->db->where('inv_inventaris_distribusi.id_ruangan','0');
				}else if($this->session->userdata('filter_id_ruang') == 'all'){
					
				}else{
					$this->db->where('inv_inventaris_distribusi.id_ruangan',$this->session->userdata('filter_id_ruang'));
				}
					
			}					
			
		}else{
			$this->db->where('inv_inventaris_distribusi.id_ruangan');
		}
		//filter date
		if(!empty($this->session->userdata('filter_tanggal')) and $this->session->userdata('filter_tanggal') != '0'){
			$this->db->where('inv_inventaris_distribusi.tgl_distribusi',$this->session->userdata('filter_tanggal'));
		}else{
			$this->db->where('inv_inventaris_distribusi.status','1');
		}
		
		$this->db->select('inv_inventaris_barang.id_inventaris_barang, id_mst_inv_barang, nama_barang, register, year(tanggal_pembelian) as tahun,  harga, barang_kembar_inv ');
		$this->db->join('inv_inventaris_distribusi','inv_inventaris_distribusi.id_inventaris_barang = inv_inventaris_barang.id_inventaris_barang ');
		$this->db->order_by('barang_kembar_inv');
		$this->db->group_by('barang_kembar_proc');
		$q = $this->db->get('inv_inventaris_barang', $limit, $start);
		return $q->result_array();
	}
    function get_data($start=0,$limit=999999,$options=array())
    {
    	$this->db->select('*');
	    $this->db->join('cl_phc', 'mst_inv_ruangan.code_cl_phc = cl_phc.code', 'inner'); 
	    $query = $this->db->get('mst_inv_ruangan',$limit,$start);
    	return $query->result();
	
    }

    function get_data_puskesmas($start=0,$limit=999999,$options=array())
    {
    	$this->db->order_by('value','asc');
    	// $this->db->where(code)
        $query = $this->db->get($this->t_puskesmas,$limit,$start);
        return $query->result();
    }

    function get_ruangan_id($puskesmas="")
    {
    	$this->db->select('max(id_mst_inv_ruangan) as id');
    	$this->db->where('code_cl_phc',$puskesmas);
    	$jum = $this->db->get('mst_inv_ruangan')->row();
    	
    	if (empty($jum)){
    		return 1;
    	}else {
			return $jum->id+1;
    	}

	}
	
	function get_data_deskripsi($kode, $id){
		$this->db->select("IFNULL(value,'-') as value, IFNULL(nama_ruangan,'-') as nama_ruangan, IFNULL(keterangan,'-') as keterangan",false);
		$this->db->where("code_cl_phc",$kode);
		$this->db->where("id_mst_inv_ruangan",$id);
		$this->db->join("mst_inv_ruangan", 'mst_inv_ruangan.code_cl_phc=cl_phc.code','left');
		$query = $this->db->get("cl_phc");
		return $query->result();
	}
	
 	function get_data_row($kode,$id){
		$data = array();
		$this->db->where("code_cl_phc",$kode);
		$this->db->where("id_mst_inv_ruangan",$id);
		$query = $this->db->get_where($this->tabel);
		if ($query->num_rows() > 0){
			$data = $query->row_array();
		}

		$query->free_result();    
		return $data;
	}

	public function getSelectedData($tabel,$data)
    {
        return $this->db->get_where($tabel, array('code_cl_phc'=>$data));
    }

    function insert_entry()
    {
    	$data['id_mst_inv_ruangan']  = $this->get_ruangan_id($this->input->post('codepus'));
		$data['nama_ruangan']		 = $this->input->post('nama_ruangan');
		$data['keterangan']			 = $this->input->post('keterangan');
		$data['code_cl_phc']	 	 = $this->input->post('codepus');

		if($this->getSelectedData($this->tabel,$data['id_mst_inv_ruangan'])->num_rows() > 0) {
			return 0;
		}else{
			if($this->db->insert($this->tabel, $data)){
			 return 1;

			}else{
				return mysql_error();
			}
		}
    }
	function get_detail_kondisi($id_barang, $tgl){
		$this->db->select('id_inventaris_barang, pilihan_keadaan_barang');
		$this->db->where('tanggal <=', $tgl);
		$this->db->where('id_inventaris_barang', $id_barang);
		$this->db->order_by('tanggal','desc');
		$q = $this->db->get('inv_keadaan_barang',1);
		$result = '0';
		foreach($q->result() as $r){
			$result = $r->pilihan_keadaan_barang;
		}
		return $result;
		
		
	}
    function update_entry($kode,$id)
    {
		// $data['id_mst_inv_ruangan'] = $this->input->post($this->input->post('code_cl_phc'));
		$data['nama_ruangan']		= $this->input->post('nama_ruangan');
		$data['keterangan']		= $this->input->post('keterangan');
		// $data['code_cl_phc']		= $this->input->post('codepus');

		$this->db->where('code_cl_phc',$kode);
		$this->db->where('id_mst_inv_ruangan',$id);

		if($this->db->update($this->tabel, $data)){
			return true;
		}else{
			return mysql_error();
		}
    }

	function delete_entry($kode,$id)
	{
		$this->db->where('code_cl_phc',$kode);
		$this->db->where('id_mst_inv_ruangan',$id);

		return $this->db->delete($this->tabel);
	}
}