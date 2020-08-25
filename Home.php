<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	     public function __construct()
        {
                parent::__construct();
                // Your own constructor code
				$this->load->helper('url');
				$this->load->database();
				$this->load->library('session');
				$this->load->model('Database_Model');
		
				
        }


	
	
	public function index()
	{


     	$query=$this->db->query("select * from ayarlar limit 1");
		$data["veri"]=$query->result();


		$query=$this->db->query("select * from urunler limit 5"); 
		$data["slider"]=$query->result();
		
		$query=$this->db->query("select * from urunler"); 
		$data["urunler"]=$query->result();

		$query=$this->db->query("select * from urunler limit 6"); 
		$data["coksatanlar"]=$query->result();

		$query=$this->db->query("select * from urunler limit 10"); 
		$data["yeniurunler"]=$query->result();


		$this->load->view('_topmenu',$data);
		$this->load->view('_header');
		$this->load->view('_sidebar');
		//$this->load->view('_slider');
		$this->load->view('_content');
		$this->load->view('_footer');
	}

	public function uyelik()
	{
		$query=$this->db->query("select * from ayarlar limit 1");
		$data["veri"]=$query->result();


		$this->load->view('_topmenu',$data);
		$this->load->view('_header');
		//$this->load->view('_sidebar');
		$this->load->view('_menubar');
		$this->load->view('_uyelik',$data);
		$this->load->view('_footer');
	}


		public function uyepanel()
	{
		if(!$this->session->userdata('uye_session'))
				{
					$this->session->set_flashdata("mesaj","Önce Giriş Yapmalısınız!");
					redirect(base_url().'home/uyelik');
				}


		$query=$this->db->query("select * from ayarlar limit 1");
		$data["veri"]=$query->result();

		$id=$this->session->uye_session["id"];
		$query=$this->db->query("select * from musteriler where Id=$id");

		$data["veri2"]=$query->result();


		$this->load->view('_topmenu',$data);
		$this->load->view('_header');
		$this->load->view('_menubar');
		$this->load->view('_uye_sidebar');
		$this->load->view('_uye_paneli',$data);
		$this->load->view('_footer');
	}


	public function uyeguncelle($id)
	{
		$data=array (
			'Kullaniciad' => $this->input->post('kullaniciad'),
			'ad' => $this->input->post('ad'),
			'soyad' => $this->input->post('soyad'),
			'parola' => $this->input->post('parola'),
			
		
			);
			
			$this->Database_Model->update_data("musteriler",$data,$id);
	
			$this->session->set_flashdata("sonuc","Güncelleme İşlemi Başarı İle Gerçekleştirildi");
			redirect (base_url()."home/uyepanel");
	
	}
	

	public function cikis_yap()
	{
		$this->session->unset_userdata("uye_session");
		
		redirect(base_url()."home/uyelik");
		
		
	}

	public function uyegiris_yap()
	{
		$email=$this->input->post('email');
		$sifre=$this->input->post('sifre');
		
		$email=$this->security->xss_clean($email);
		$sifre=$this->security->xss_clean($sifre);

		
		if( $email and $sifre)
		{
			
			$result=$this->Database_Model->login("musteriler",$email,$sifre);
			if($result)
			{
							
					$sess_array=array(
					'id' => $result[0]->Id,
					'yetki' => $result[0]->yetki,
					'email' => $result[0]->resim,
					'ad' => $result[0]->ad,
					'soyad' => $result[0]->soyad,
					
					);
					
					$this->session->set_userdata("uye_session",$sess_array);
					
					redirect(base_url()."home");
				
				
		
			}
			
			else
			{

				$this->session->set_flashdata("sonuc","Geçersiz Email ya da Şifre");
				redirect(base_url()."home/uyelik");
			}
			
			
			
			
		}	
		
	}



		public function sepet()
	{
  		if(!$this->session->userdata("uye_session"))
		  {
			  
			  $this->session->set_flashdata("login_hata","Önce Giriş Yapınız");
			  redirect(base_url().'home/uyelik');  
		  }	

		$musteri_id=$this->session->uye_session["id"];

		$query=$this->db->query("select * from ayarlar limit 1");
		$data["veri"]=$query->result();

			
		$query=$this->db->query("SELECT sepet.*, urunler.adi as urunadi, urunler.BirimFiyat as urunfiyat, urunler.resim as urunresim
		FROM sepet
		INNER JOIN urunler ON urunler.id=sepet.urun_id
		WHERE sepet.musteri_id= $musteri_id
		order by urunadi");
		$data["veriler"]=$query->result();
		


		$this->load->view('_topmenu',$data);
		$this->load->view('_header');
		$this->load->view('_menubar');
		//$this->load->view('_uye_sidebar');
		$this->load->view('_uye_sepet',$data);
		$this->load->view('_footer');
	}



	public function odeme()
	{
  		if(!$this->session->userdata("uye_session"))
		  {
			  
			  $this->session->set_flashdata("login_hata","Önce Giriş Yapınız");
			  redirect(base_url().'home/uyelik');  
		  }	





		$musteri_id=$this->session->uye_session["id"];

		$query=$this->db->query("select * from musteriler WHERE Id=$musteri_id");
		$data["musteri"]=$query->result();

		$query=$this->db->query("select * from ayarlar limit 1");
		$data["veri"]=$query->result();


		$query=$this->db->query("SELECT sepet.*, urunler.adi as urunadi, urunler.BirimFiyat as urunfiyat, urunler.resim as urunresim
		FROM sepet
		INNER JOIN urunler ON urunler.id=sepet.urun_id
		WHERE sepet.musteri_id= $musteri_id
		order by urunadi");

		$data["veriler"]=$query->result();
		


		$this->load->view('_topmenu',$data);
		$this->load->view('_header');
		$this->load->view('_menubar');
		//$this->load->view('_uye_sidebar');
		$this->load->view('_odeme',$data);
		$this->load->view('_footer');
	}



		public function sepete_ekle($id)
	{
		
			
			$data=array (
			'musteri_id' => $this->session->uye_session["id"],
			'urun_id' => $id,
			'miktar' => $this->input->post('miktar'),
		
				);
				$this->load->model("Database_Model");
				$this->Database_Model->insert_data("sepet",$data);
		
				$this->session->set_flashdata("sonuc","Ürün Sepete Başarı İle Eklendi");
				redirect (base_url()."home/urundetay/$id");
						
			
                
	}

	public function sepete_ekle_anasayfa($id)
	{
		
			
			$data=array (
			'musteri_id' => $this->session->uye_session["id"],
			'urun_id' => $id,
			'miktar' => $this->input->post('miktar'),
		
				);
				$this->load->model("Database_Model");
				$this->Database_Model->insert_data("sepet",$data);
		
				$this->session->set_flashdata("urunsonuc","Ürün Sepete Başarı İle Eklendi");
				redirect (base_url()."home");
						
			
                
	}
	public function sepet_sil($id)
	{
		
			
		 $this->db->query("DELETE FROM sepet WHERE Id=$id");
		 $this->session->set_flashdata("sonuc","Kayıt Silme İşlemi Başarı ile Gerçekleştirildi");
		 redirect (base_url()."home/sepet");
						
			
                
	}

	public function siparislerim()
	{
		  if(!$this->session->userdata("uye_session"))
		  {
			  
			  $this->session->set_flashdata("login_hata","Önce Giriş Yapınız");
			  redirect(base_url().'home/uyelik');  
		  }	
		

		 	$query=$this->db->query("select * from ayarlar limit 1");
			$data["veri"]=$query->result();



			$musteri_id=$this->session->uye_session["id"];

			$query=$this->db->query("select * from siparisler WHERE musteri_id=$musteri_id"); 
		    $data["veriler"]=$query->result();
		
		
			
			
				$this->load->view('_topmenu',$data);
				$this->load->view('_header');
				//$this->load->view('_sidebar');
				$this->load->view('_menubar');
				$this->load->view('_uye_sidebar');
				$this->load->view('_uye_siparisler',$data);
				$this->load->view('_footer');


				
		
	}

	public function siparis_tamamla()
	{


			$musteri_id=$this->session->uye_session["id"];

			$data=array (
			'adsoy' => $this->input->post('adsoy'),
			'email' => $this->input->post('email'),
			'tel' => $this->input->post('tel'),
			'postakodu' => $this->input->post('postakodu'),
			'adres' => $this->input->post('adres'),
			'sehir' => $this->input->post('sehir'),
			'tutar' => $this->input->post('tutar'),
			'musteri_id' => $musteri_id,
			'Ip' => $_SERVER['REMOTE_ADDR']
		
			);
			
			 $siparis_id=$this->Database_Model->insert_data("siparisler",$data);

			 if ($siparis_id) {
			 	
			 	$query=$this->db->query("SELECT sepet.*, urunler.adi as urunadi, urunler.Birim as urunbirim, urunler.BirimFiyat as urunfiyat, urunler.resim as urunresim
				FROM sepet
				INNER JOIN urunler ON urunler.id=sepet.urun_id
				WHERE sepet.musteri_id= $musteri_id
				order by urunadi");
				$veriler=$query->result();

				foreach ($veriler as $rs) 

				{

					$data=array (
					'musteri_id' => $this->session->uye_session["id"],
					'siparis_id' => $siparis_id,
					'urun_id' => $rs->Id,
					'adi' => $rs->urunadi,
					'miktar' => $rs->miktar,
					'fiyat' => $rs->urunfiyat,
					'birim' => $rs->urunbirim,
					'tutar' => $rs->miktar * $rs->urunfiyat
					);
					$this->Database_Model->insert_data("siparis_urunler",$data);


				}



			 }


			$this->db->query("DELETE FROM sepet WHERE musteri_id=$musteri_id");

			
			$this->session->set_flashdata("sonuc","Siparişiniz Alınmıştır <br> Teşekkür ederiz.");
			redirect (base_url()."home/siparislerim");



	}





	public function siparis_detay($id)
	{
		if(!$this->session->userdata("uye_session"))
		  {
			  
			  $this->session->set_flashdata("login_hata","Önce Giriş Yapınız");
			  redirect(base_url().'home/uyelik');  
		  }	
		
		    $musteri_id=$this->session->uye_session["id"];
			
			$query=$this->db->query("select * from ayarlar limit 1");
			$data["veri"]=$query->result();

		
		    $musteri_id=$this->session->uye_session["id"];
			$query=$this->db->query("SELECT * FROM siparisler WHERE Id=$id");
			 $data["siparis"]=$query->result();
			
			$query=$this->db->query("SELECT * FROM siparis_urunler WHERE siparis_id=$id");
			 $data["urunler"]=$query->result();
			 
			 
			 	$this->load->view('_topmenu',$data);
				$this->load->view('_header');
				//$this->load->view('_sidebar');
				$this->load->view('_menubar');
				$this->load->view('_uye_sidebar');
				$this->load->view('_uye_siparis_detay',$data);
				$this->load->view('_footer');
			
	}

	public function iletisim()
	{
		$query=$this->db->query("select * from ayarlar limit 1");
		$data["veri"]=$query->result();


		$this->load->view('_topmenu',$data);
		$this->load->view('_header');
		//$this->load->view('_sidebar');
		$this->load->view('_menubar');
		$this->load->view('_iletisim',$data);
		$this->load->view('_footer');
	}

	public function mesajkaydet()
		{
			$data=array(
			'adsoy' => $this->input->post('adsoy'),
			'email' => $this->input->post('email'),
			'konu' => $this->input->post('konu'),
			'mesaj' => $this->input->post('mesaj'),
			'Ip' => $_SERVER['REMOTE_ADDR']
			);
			$this->Database_Model->insert_data("mesajlar",$data);
			$this->session->set_flashdata("sonuc","Mesajınız Başarı İle Alındı");
			
			redirect(base_url()."home/iletisim");
			
			
		}
		public function urundetay($id)
		{

				$data["tek_urun"]=$this->Database_Model->urun_get($id);

				


				$query=$this->db->query("select * from urunler WHERE Id=$id"); 
				$data["urun"]=$query->result();


				$query=$this->db->query("select * from ayarlar limit 1");
				$data["veri"]=$query->result();

				$query=$this->db->query("select * from urun_resimler WHERE urun_id=$id"); 
				$data["resimler"]=$query->result();

					$this->load->view('_topmenu',$data);
					$this->load->view('_header');
					//$this->load->view('_sidebar');
					$this->load->view('_menubar');
					$this->load->view('_urundetay',$data);
					$this->load->view('_footer');
			
		}

		public function kategori($kategori)
		{

				$data["urun_kategori"]=$this->Database_Model->urun_get_kategori($kategori);

				$query=$this->db->query("select * from kategoriler WHERE Id=$kategori"); 
				$data["kategoriler"]=$query->result();


				$query=$this->db->query("select * from ayarlar limit 1");
				$data["veri"]=$query->result();

				$query=$this->db->query("SELECT DISTINCT Marka FROM urunler");
				$data["marka"]=$query->result();


				
				

					$this->load->view('_topmenu',$data);
					$this->load->view('_header');
					//$this->load->view('_sidebar');
					$this->load->view('_menubar');
					$this->load->view('_kategori',$data);
					$this->load->view('_footer');
			
		}

}
