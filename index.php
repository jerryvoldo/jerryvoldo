<!-- to do -->
<!-- link tim manajemen -->
<!-- link materi/SK manajemen risiko -->
<!-- link login sistem -->
<!-- link upload data dukung -->


<?php 
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

session_start();

// get url
function base_url()
{
	 if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
         $url = "https://";   
    else  
         $url = "http://";   
    // Append the host(domain name, ip) to the URL.   
    $url.= $_SERVER['HTTP_HOST']; 
    return $url;  
}



function warnaRisiko($level_risiko)
{
	$matriksLevelRisiko = array(
								'sangat tinggi'=>array('min'=>20, 'max'=>25, 'warna'=>"#FF0000"),
								'tinggi'=>array('min'=>16, 'max'=>19, 'warna'=>"#FF9B00"),
								'sedang'=>array('min'=>12, 'max'=>15, 'warna'=>"#FFF700"),
								'rendah'=>array('min'=>6, 'max'=>12, 'warna'=>"#13FF00"),
								'sangat rendah'=>array('min'=>1, 'max'=>5, 'warna'=>"#002EFF"),
						);
	foreach($matriksLevelRisiko as $key=>$value)
	{
		if(($level_risiko >= $value['min']) && ($level_risiko <= $value['max']))
		{
			return $value['warna'];
		}
	}
}
?>

<!-- begin process form POST -->
<?php 
	
	if(isset($_POST['login']))
	{
		$user_logon = array('email'=>$_POST['email']);
		// auth user using query to database
		try {
				$conn15 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn15->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_user_logon = 'select * from oop_user where email = :email and isapproved = true';
				$query_user_logon = $conn15->prepare($sql_user_logon);
				$query_user_logon->execute($user_logon);
				$user = $query_user_logon->fetch(PDO::FETCH_ASSOC);
				$conn15=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		if(!empty($user) && password_verify($_POST['password'], $user['password']))
		{
			
			$_SESSION['email'] = $user['email'];
			$_SESSION['user_level'] = $user['user_level'];
			$_SESSION['login'] = true;
		}
		else
		{
			header('Location:'.base_url());
		}
	}

	if(isset($_GET['logout']))
	{
		session_destroy();
		header('Location:'.base_url());
	}
	
	if(isset($_POST['register_new_user']))
	{
		if($_POST['register_password'] !== $_POST['register_password_confirmation'])
		{
			header('Location:'.base_url());
		}
		$data_new_user = array(
								'id'=>$_POST['nip'],
								'nama'=>$_POST['name'],
								'email'=>$_POST['register_email'],
								'password'=>password_hash($_POST['register_password_confirmation'], PASSWORD_DEFAULT),
								'created_at'=>time(),
								'modified_at'=>time(),
								'user_level'=>1
							);
		// simpan data ke database
		try {
				$conn16 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn16->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_register_user = 'insert into oop_user (id, nama, email, password, created_at, modified_at, user_level) 
												values (:id, :nama, :email, :password, :created_at, :modified_at, :user_level)';
				$query_register_user = $conn16->prepare($sql_register_user);
				$query_register_user->execute($data_new_user);
				$conn16=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url());
	}

	if(isset($_POST['submit_add_sasaran_strategis']))
	{
		$sasaran_strategis_add = array('deskripsi'=>$_POST['new_sasaran_strategis'], 
										'created_at'=>time(),
										'modified_at'=>time());
		// simpan data ke database
		try {
				$conn22 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn22->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_insert_sasaran_strategis = 'insert into oop_sasaran_strategis (deskripsi, created_at, modified_at) values (:deskripsi, :created_at, :modified_at)';
				$query = $conn22->prepare($sql_insert_sasaran_strategis);
				$query->execute($sasaran_strategis_add);
				$conn22=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url().'/?administrasi&mastersasaran');
	}

	if(isset($_POST['submit_edit_sasaran_strategis']))
	{
		$sasaran_strategis_edit = array('sasaran_deskripsi'=>$_POST['sasaran_deskripsi'], 
										'id'=>$_POST['sasaran_id'],
										'modified_at'=>time());
		// simpan data ke database
		try {
				$conn20 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn20->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_insert_edited_sasaran_strategis = 'update oop_sasaran_strategis set deskripsi = :sasaran_deskripsi, modified_at = :modified_at where id = :id';
				$query = $conn20->prepare($sql_insert_edited_sasaran_strategis);
				$query->execute($sasaran_strategis_edit);
				$conn20=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url().'/?administrasi&mastersasaran');
	}

	if(isset($_POST['submit_delete_sasaran_strategis']))
	{
		$sasaran_strategis_delete = array('id'=>$_POST['sasaran_id']);
		// simpan data ke database
		try {
				$conn20 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn20->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_delete_sasaran_strategis = 'delete from oop_sasaran_strategis where id = :id';
				$query = $conn20->prepare($sql_delete_sasaran_strategis);
				$query->execute($sasaran_strategis_delete);
				$conn20=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url().'/?administrasi&mastersasaran');
	}

	if(isset($_POST['submit']))
	{
		// ekstrak level dampak, level kemungkinan dan level risiko dari input radio level_risiko
		$all_level_risiko = $_POST['level_risiko'];
		$explode_level_risiko = explode('-', $all_level_risiko);
		$level_risiko = (int) $explode_level_risiko[0];
		$gabungan_level_kemungkinan_dampak = str_split($explode_level_risiko[1]);
		$level_kemungkinan = (int) $gabungan_level_kemungkinan_dampak[0];
		$level_dampak = (int) $gabungan_level_kemungkinan_dampak[1];

		$toSubmit = array(
					'sasaran_id'=>$_POST['sasaran_id'],
					'proses_bisnis'=>$_POST['proses_bisnis'],
					'kode_risiko_id'=>$_POST['kode_risiko'],
					'kategori_risiko_id'=>$_POST['kategori_risiko'],
					'risk_event'=>$_POST['risk_event'],
					'penyebab_risiko'=>$_POST['sebab_risiko'],
					'sumber_risiko'=>$_POST['sumber_risiko'],
					'potensi_kerugian'=>$_POST['potensi_kerugian'],	
					'pemilik_risiko'=>$_POST['pemilik_risiko'],
					'unit_terkait'=>$_POST['unit_terkait'],
					'created_at'=>time(),
					'modified_at'=>time(),
					'risiko_inheren_kemungkinan'=>$level_kemungkinan,
					'risiko_inheren_dampak'=>$level_dampak,
					'risiko_inheren_level'=>$level_risiko	
			);
		// simpan data ke database
		try {
				$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql = 'insert into oop_risk_register (sasaran_id, proses_bisnis, kode_risiko_id, kategori_risiko_id, risk_event, penyebab_risiko, sumber_risiko, potensi_kerugian, pemilik_risiko, unit_terkait, created_at, modified_at, risiko_inheren_kemungkinan, risiko_inheren_dampak, risiko_inheren_level)
					values (:sasaran_id, :proses_bisnis, :kode_risiko_id, :kategori_risiko_id, :risk_event, :penyebab_risiko, :sumber_risiko, :potensi_kerugian, :pemilik_risiko, :unit_terkait, :created_at, :modified_at, :risiko_inheren_kemungkinan, :risiko_inheren_dampak, :risiko_inheren_level )';
				$query = $pdo->prepare($sql);
				$query->execute($toSubmit);
				$pdo=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url().'/?sasaran='.$_POST['sasaran_id'].'&riskregister=true');
	}

	if (isset($_POST['hapus']))
	{
		try {
				$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql = 'delete from oop_risk_register where id=:id';
				$query = $pdo->prepare($sql);
				$query->execute(array(':id'=>$_POST['hapus_id_risiko']));
				$pdo=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url().'/?sasaran='.$_POST['sasaran_id'].'&riskregister=true');
	}

	if(isset($_POST['pengendalian_reviu_dokumen']) || isset($_POST['pengendalian_reviu_dokumen_mitigasi']))
	{
		$all_level_risiko_residual_reviu_dokumen = $_POST['risiko_residual_reviu_dokumen'];
		$explode_level_risiko = explode('-', $all_level_risiko_residual_reviu_dokumen);
		$level_risiko = (int) $explode_level_risiko[0];
		$gabungan_level_kemungkinan_dampak = str_split($explode_level_risiko[1]);
		$level_kemungkinan = (int) $gabungan_level_kemungkinan_dampak[0];
		$level_dampak = (int) $gabungan_level_kemungkinan_dampak[1];

		$dataPengendalianReviuDokumen = array(
												'aktivitas_pengendalian'=>$_POST['aktivitas_pengendalian'],
												'atribut_pengendalian'=>$_POST['atribut_pengendalian'],
												'jumlah_sampel'=>$_POST['jumlah_sampel'],
												'jumlah_sampel_sesuai_rancangan_pengendalian'=>$_POST['jumlah_sampel_sesuai_rancangan_pengendalian'],
												'jumlah_sampel_tidak_sesuai_rancangan_pengendalian'=>$_POST['jumlah_sampel_tidak_sesuai_rancangan_pengendalian'],
												'uraian_ketidaksesuaian'=>$_POST['uraian_ketidaksesuaian'],
												'persentase_ketidaksesuaian'=>$_POST['persentase_tidak_sesuai_rancangan_pengendalian'],
										);
		if(isset($_POST['pengendalian_reviu_dokumen_mitigasi']))
		{
			$dataPengendalianReviuDokumen['isaftermitigasi'] = "true";
			$dataPengendalianReviuDokumen['risiko_id'] = $_POST['risiko_id_pengendalian'];
			$dataPengendalianReviuDokumen['id_monitoring'] = $_POST['id_monitoring'];
		}
		else
		{
			$dataPengendalianReviuDokumen['isaftermitigasi'] = "false";
			$dataPengendalianReviuDokumen['risiko_id'] = $_POST['risiko_id'];
			$dataPengendalianReviuDokumen['id_monitoring'] = 9999;
		}

		if($_POST['metode_sampling'] == "uji_petik" || $_POST['metode_sampling_mitigasi'] == "uji_petik")
		{
			switch ($_POST['persentase_tidak_sesuai_rancangan_pengendalian']) {
				case ($_POST['persentase_tidak_sesuai_rancangan_pengendalian'] > 0 && $_POST['persentase_tidak_sesuai_rancangan_pengendalian'] < 1):
					$dataPengendalianReviuDokumen['penilaian_kelemahan_pengendalian'] = 'Kelemahan Tidak Signifikan';
					$dataPengendalianReviuDokumen['simpulan_efektivitas_pengendalian'] = 'Efektif';
					$dataPengendalianReviuDokumen['rekomendasi'] = 'Rancangan Pengendalian Telah Efektif';
					break;
				
				case ($_POST['persentase_tidak_sesuai_rancangan_pengendalian'] >= 1 && $_POST['persentase_tidak_sesuai_rancangan_pengendalian'] < 5):
					$dataPengendalianReviuDokumen['penilaian_kelemahan_pengendalian'] = 'Kelemahan Signifikan';
					$dataPengendalianReviuDokumen['simpulan_efektivitas_pengendalian'] = 'Tidak Efektif';
					$dataPengendalianReviuDokumen['rekomendasi'] = 'Meningkatkan Kepatuhan atas Rancangan Pengendalian';
					break;

				case ($_POST['persentase_tidak_sesuai_rancangan_pengendalian'] >= 5):
					$dataPengendalianReviuDokumen['penilaian_kelemahan_pengendalian'] = 'Kelemahan Material';
					$dataPengendalianReviuDokumen['simpulan_efektivitas_pengendalian'] = 'Tidak Efektif';
					$dataPengendalianReviuDokumen['rekomendasi'] = 'Perbaikan Rancangan Pengendalian';
					break;
			}
		}

		if($_POST['metode_sampling'] == "sensus" || $_POST['metode_sampling_mitigasi'] == "sensus")
		{
			switch ($_POST['persentase_tidak_sesuai_rancangan_pengendalian']) {
				case ($_POST['persentase_tidak_sesuai_rancangan_pengendalian'] > 0 && $_POST['persentase_tidak_sesuai_rancangan_pengendalian'] < 5):
					$dataPengendalianReviuDokumen['penilaian_kelemahan_pengendalian'] = 'Kelemahan Tidak Signifikan';
					$dataPengendalianReviuDokumen['simpulan_efektivitas_pengendalian'] = 'Efektif';
					$dataPengendalianReviuDokumen['rekomendasi'] = 'Rancangan Pengendalian Telah Efektif';
					break;
				
				case ($_POST['persentase_tidak_sesuai_rancangan_pengendalian'] >= 5 && $_POST['persentase_tidak_sesuai_rancangan_pengendalian'] < 10):
					$dataPengendalianReviuDokumen['penilaian_kelemahan_pengendalian'] = 'Kelemahan Signifikan';
					$dataPengendalianReviuDokumen['simpulan_efektivitas_pengendalian'] = 'Tidak Efektif';
					$dataPengendalianReviuDokumen['rekomendasi'] = 'Meningkatkan Kepatuhan atas Rancangan Pengendalian';
					break;

				case ($_POST['persentase_tidak_sesuai_rancangan_pengendalian'] >= 10):
					$dataPengendalianReviuDokumen['penilaian_kelemahan_pengendalian'] = 'Kelemahan Material';
					$dataPengendalianReviuDokumen['simpulan_efektivitas_pengendalian'] = 'Tidak Efektif';
					$dataPengendalianReviuDokumen['rekomendasi'] = 'Perbaikan Rancangan Pengendalian';
					break;
			}
		}

		$dataPengendalianReviuDokumen['risiko_residual_kemungkinan'] = $level_kemungkinan;
		$dataPengendalianReviuDokumen['risiko_residual_dampak'] = $level_dampak;
		$dataPengendalianReviuDokumen['risiko_residual_level'] = $level_risiko;

		

		try {
				$conn3 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sqlPengendalianReviuDokumen = 'with pengendalian_risiko as (insert into oop_pengendalian_reviu_dokumen (risiko_id, aktivitas_pengendalian, atribut_pengendalian, jumlah_sampel, 
												jumlah_sampel_sesuai_rancangan_pengendalian, jumlah_sampel_tidak_sesuai_rancangan_pengendalian, uraian_ketidaksesuaian, persentase_ketidaksesuaian,
												penilaian_kelemahan_pengendalian, simpulan_efektivitas_pengendalian, rekomendasi, risiko_residual_kemungkinan, risiko_residual_dampak, risiko_residual_level,
												isaftermitigasi)
												values 
												(:risiko_id, :aktivitas_pengendalian, :atribut_pengendalian, :jumlah_sampel, :jumlah_sampel_sesuai_rancangan_pengendalian,
												 :jumlah_sampel_tidak_sesuai_rancangan_pengendalian, :uraian_ketidaksesuaian, :persentase_ketidaksesuaian, :penilaian_kelemahan_pengendalian, 
												 :simpulan_efektivitas_pengendalian, :rekomendasi, :risiko_residual_kemungkinan, :risiko_residual_dampak, :risiko_residual_level, :isaftermitigasi) returning id, risiko_id),
												 update_risk_register as (update oop_risk_register set pengendalian_reviu_dokumen_id = (select id from pengendalian_risiko) where id = (select risiko_id from pengendalian_risiko))
												 update oop_monitoring_risiko set pengendalian_id = (select id from pengendalian_risiko) where id = :id_monitoring';
				$queryPengendalianReviuDokumen = $conn3->prepare($sqlPengendalianReviuDokumen);
				$queryPengendalianReviuDokumen->execute($dataPengendalianReviuDokumen);
				$conn3=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		header('Location:'.base_url().'/?sasaran='.$_POST['sasaran_id'].'&riskregister=true');
	}

	if(isset($_POST['submit_risiko_mitigasi']))
	{
		// ekstrak level dampak, level kemungkinan dan level risiko dari input radio level_risiko
		$all_level_risiko = $_POST['risiko_mitigasi'];
		$explode_level_risiko = explode('-', $all_level_risiko);
		$level_risiko = (int) $explode_level_risiko[0];
		$gabungan_level_kemungkinan_dampak = str_split($explode_level_risiko[1]);
		$level_kemungkinan = (int) $gabungan_level_kemungkinan_dampak[0];
		$level_dampak = (int) $gabungan_level_kemungkinan_dampak[1];

		$toSubmit = array(
					'risiko_id'=>$_POST['risiko_id'],
					'respon_risiko'=>$_POST['respon_risiko'],
					'deskripsi_tindakan_mitigasi'=>$_POST['deskripsi_tindakan_mitigasi'],
					'pic'=>$_POST['pic'],
					'kebutuhan_sumber_daya'=>$_POST['kebutuhan_sumber_daya'],
					'target_waktu_selesai'=>$_POST['sebab_risiko'],
					'mitigasi_kemungkinan'=>$level_kemungkinan,
					'mitigasi_dampak'=>$level_dampak,	
					'mitigasi_level'=>$level_risiko,
					'uraian_target'=>$_POST['uraian_target'],
					'target_waktu_selesai'=>strtotime($_POST['target_waktu_selesai'])
			);
		// simpan data ke database
		try {
				$conn9 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn9->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_insert_risk_mitigation = 'insert into oop_mitigasi_risiko (risiko_id, respon_risiko, deskripsi_tindakan_mitigasi, pic, kebutuhan_sumber_daya, 
												mitigasi_kemungkinan, mitigasi_dampak, mitigasi_level, uraian_target, target_waktu_selesai) 
												values (:risiko_id, :respon_risiko, :deskripsi_tindakan_mitigasi, :pic, :kebutuhan_sumber_daya, 
												:mitigasi_kemungkinan, :mitigasi_dampak, :mitigasi_level, :uraian_target, :target_waktu_selesai)';
				$query_insert_risk_mitigation = $conn9->prepare($sql_insert_risk_mitigation);
				$query_insert_risk_mitigation->execute($toSubmit);
				$conn9=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url().'/?sasaran='.$_POST['sasaran_id'].'&riskmitigation=true');
	}

	if(isset($_POST['submit_monitoring_risiko']))
	{
		$toSubmit = array(
					'risiko_id'=>$_POST['risiko_id'],
					'uraian_progress'=>$_POST['uraian_progress_monitoring'],
					'epoch_pemantauan'=>strtotime($_POST['date_monitoring'])
			);
		// simpan data ke database
		try {
				$conn12 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$conn12->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_insert_monitoring_risiko = 'insert into oop_monitoring_risiko (risiko_id, uraian_progress, epoch_pemantauan) values (:risiko_id, :uraian_progress, :epoch_pemantauan)';
				$query_insert_monitoring_risiko = $conn12->prepare($sql_insert_monitoring_risiko);
				$query_insert_monitoring_risiko->execute($toSubmit);
				$conn9=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		 header('Location:'.base_url().'/?sasaran='.$_POST['sasaran_id'].'&riskmonitoring=true');
	}

?>
<!-- end process form POST -->


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    <style type="text/css">
    	body {
    			background-color: #3AAFA9;
    	}
    </style>
	<title>ManRisk</title>
</head>
<body> 
	<?php if(empty($_GET)):?>
		<?php if( isset($_SESSION['login']) && $_SESSION['login'] == true ):?>
			<!-- begin navbar -->
			<nav class="navbar navbar-expand-lg navbar-light sticky-top" style="background-color: #17252A;">
			  <div class="container-fluid">
			    <a class="navbar-brand" href="<?=base_url()?>" style="color: #FEFFFF;">Direktorat Pengawasan Peredaran Pangan Olahan</a>
			    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			      <span class="navbar-toggler-icon"></span>
			    </button>
			    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
			      <ul class="navbar-nav">
			      	<li class="nav-item">
			      	  <a class="nav-link me-3" aria-current="page" href="<?=base_url()?>/?administrasi&mastersasaran"  style="color: #FEFFFF;">Administrasi</a>
			      	</li>
			        <li class="nav-item">
			          <a class="nav-link btn btn-outline-warning" aria-current="page" href="<?=base_url()?>/?logout"  style="color: #FEFFFF;">Logout</a>
			        </li>
			      </ul>
			    </div>
			  </div>
			</nav>
			<!-- end navbar -->
	
			<?php  
				// load sasaran strategis
				try {
						$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
						$sql = 'select * from oop_sasaran_strategis';
						$query = $pdo->prepare($sql);
						$query->execute();
						$all_sasaran = $query->fetchAll(PDO::FETCH_ASSOC);
						$pdo=null;
				} catch (PDOException $e) {
						print "Error!: " . $e->getMessage() . "<br/>";
				    	die();
				}
			?>
			<div class="container">
				<div class="row mt-4">
					<div class="col-md">
						<h2 class="display-6">Manajemen Risiko</h2>
						<h4 class="mb-4 fw-light">Tahun Anggaran 2021</h4>
						<div class="row row-cols-1 row-cols-md-2 g-4">
							<?php foreach($all_sasaran as $sasaran):?>
							<div class="col">
								<div class="card shadow-sm" style="border: none">
								  <div class="card-body text-white" style="background-color: #1E9994">
								    <h5 class="card-title text-white small text-uppercase">Sasaran Strategis <?=$sasaran['id']?></h5>
								    <p class="card-text lead"><?=$sasaran['deskripsi']?></p>
								    <a href="<?=base_url()?>/?sasaran=<?=$sasaran['id']?>&riskregister=true" class="stretched-link" style=""></a>
								  </div>
								</div>
							</div>
							<?php endforeach;?>
						</div>
					</div>
				</div>
			</div>
		<?php else:?>
			<div class="container-fluid">
				<div class="row">
					<div class="col"></div>
					<div class="col-md-6 vh-100 d-flex flex-column align-items-center justify-content-center">
							<p class="text-white h1 text-center">Sistem Informasi Internal Manajemen Risiko</p>
							<p class="text-white h4 text-center mb-4">Direktorat Pengawasan Peredaran Pangan Olahan</p>
							<div class="card w-50">
								<div class="card-body">
									<center class="h4 mt-2 mb-4">Please Login</center>
									<form method="POST" action="index.php">
									  <div class="mb-3">
									    <label for="exampleInputEmail1" class="form-label">Email</label>
									    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
									  </div>
									  <div class="mb-3">
									    <label for="exampleInputPassword1" class="form-label">Password</label>
									    <input type="password" class="form-control" id="password" name="password">
									  </div>
									  <button type="submit" class="btn btn-primary" name="login">Submit</button>
									</form>
								</div>
							</div>
							<p class="mt-2">Silahkan <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#registerModal">register</button> untuk mendapatkan akun akses.</p>
					</div>
					<div class="col"></div>
				</div>
			</div>
		<?php endif;?>
	<?php endif;?>

	<?php if(isset($_GET['sasaran']) && isset($_SESSION['login']) && $_SESSION['login'] == true):?>
		<?php 
    	try {
				$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$sql = 'select * from oop_sasaran_strategis where id= :id';
				$query = $pdo->prepare($sql);
				$query->execute(array(':id'=>$_GET['sasaran']));
				$sasaran = $query->fetch(PDO::FETCH_ASSOC);
				$pdo=null;
			} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
		    	die();
			}
		?>
		<div class="container-fluid">
			<div class="row mt-4">
				<div class="col-md-2">
					<div class="card"  style="background-color: #1E9994;  color: #FEFFFF">
					  <div class="card-header">
					    <p class="lead mt-3">Sasaran Strategis <?=$_GET['sasaran']?></p>
					    <p class="fs-5 mb-5"><?=$sasaran['deskripsi']?></p>
					    <button class="btn btn-sm btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#AddriskModal">Add Risk</button>
					  </div>
					  <ul class="list-group list-group-flush" >
					    <a href="<?=base_url()?>?sasaran=<?=$_GET['sasaran']?>&riskregister=true" class="list-group-item"  style="background-color: #1E9994; color: #FEFFFF">
					    	Risk Register
					    </a>

					    <a href="<?=base_url()?>?sasaran=<?=$_GET['sasaran']?>&riskmitigation=true"  class="list-group-item"  style="background-color: #1E9994; color: #FEFFFF">Risk Mitigation</a>
					    <a href="<?=base_url()?>?sasaran=<?=$_GET['sasaran']?>&riskmonitoring=true"  class="list-group-item"  style="background-color: #1E9994; color: #FEFFFF">Risk Monitoring</a>
					  </ul>
					</div>
					<a class="btn btn-lg btn-dark mt-4" href="<?=base_url()?>">Back</a>
				</div>
				<div class="col-md">
					<?php  
					// get all risks
						$sasaran_risk_register = array();
						try {
								$conn1 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
								$sql_risk_register = 'select rr.*, kategori.deskripsi as kategori_risiko from oop_risk_register rr
														left join oop_kategori_risiko kategori on rr.kategori_risiko_id = kategori.id
								 						where rr.sasaran_id= :sasaran_id ';
								$query_risk_register = $conn1->prepare($sql_risk_register);
								$query_risk_register->execute(array(':sasaran_id'=>$_GET['sasaran']));
								$sasaran_risk_register = $query_risk_register->fetchAll(PDO::FETCH_ASSOC);
								$conn1=null;
							} catch (PDOException $e) {
								print "Error!: " . $e->getMessage() . "<br/>";
						    	die();
							}
					?>
					<div class="card"  style="background-color: #1E9994;">
					  <div class="card-body">
					    <?php if(isset($_GET['sasaran']) && isset($_GET['riskregister'])):?>
					    	<p class="h4 mb-4 text-white">Risk Register</p>
					    	<div class="table-responsive">
						    	<table class="table table-sm mb-4">
								  <thead style="background-color: #1E6199" class="text-white">
								    <tr>
								      <th scope="col">#</th>
								      <th scope="col" class="col-md-2">Proses Bisnis</th>
								      <th scope="col">Kode Risiko</th>
								      <th scope="col">Kategori Risiko</th>
								      <th scope="col">Risk Event</th>
								      <th scope="col">Penyebab Risiko</th>
								      <th scope="col" class="col-md-1">Sumber Risiko</th>
								      <th scope="col">Potensi Kerugian</th>
								      <th scope="col">Pemilik Risiko</th>
								      <th scope="col">Unit terkait</th>
								    </tr>
								  </thead>
								  <tbody>
								  	<?php if($sasaran_risk_register):?>
									  	<?php foreach($sasaran_risk_register as $key=>$risk):?>
										    <tr class="bg-light">
										    	<td><?=$key+1?></td>
										    	<td>
										    		<?=$risk['proses_bisnis']?>
										    		<p class="small mt-2">
										    			<a class="text-white badge rounded-pill bg-secondary" href="<?=base_url()?>?sasaran=<?=$_GET['sasaran']?>&riskregister=true&details=<?=$risk['id']?>">Lihat detail</a>
										    			<a class="text-white badge rounded-pill bg-secondary" href="<?=base_url()?>?sasaran=<?=$_GET['sasaran']?>&riskregister=true">Tutup detail</a>
										    		</p>
										    		<p>
										    			<form method="POST" action="#">
										    				<input type="hidden" name="hapus_id_risiko" value="<?=$risk['id']?>">
										    				<input type="hidden" name="sasaran_id" value="<?=$risk['sasaran_id']?>">
										    				<button class="badge bg-danger" name="hapus">Hapus risiko</button>
										    				<button class="badge bg-warning" name="edit">Edit risiko</button>
										    			</form>
										    		</p>
										    	</td>
										    	<td><?=$risk['kode_risiko_id']?></td>
										    	<td><?=$risk['kategori_risiko']?></td>
										    	<td><?=$risk['risk_event']?></td>
										    	<td><?=$risk['penyebab_risiko']?></td>
										    	<td><?=$risk['sumber_risiko']?></td>
										    	<td><?=$risk['potensi_kerugian']?></td>
										    	<td><?=$risk['pemilik_risiko']?></td>
										    	<td><?=$risk['unit_terkait']?></td>
										    </tr>
										    <?php if(isset($_GET['details']) && $_GET['details'] == $risk['id']):?>
											    <!-- begin show detail per risk -->
												    <tr style="background-color: #1A807C;">
												    	<td colspan="10">
															<div class="row">
																<div class="col-md">

																	<!-- begin risiko inheren -->
																	<div class="fw-bold px-4 mt-3">Risiko Inheren</div>
																	<div class="row  text-dark px-4">
																		<div class="col-md">
																			<div class="card">
																				<div class="card-body">
																					<div class="h1 text-dark"><?=$risk['risiko_inheren_kemungkinan']?></div>
																					<div class="text-uppercase">Kemungkinan</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md">
																			<div class="card">
																				<div class="card-body">
																					<div class="h1 text-dark"><?=$risk['risiko_inheren_dampak']?></div>
																					<div class="text-uppercase">Dampak</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md">
																			<div class="card" style="background-color: <?=warnaRisiko($risk['risiko_inheren_level'])?>">
																				<div class="card-body">
																					<div class="h1 text-dark"><?=$risk['risiko_inheren_level']?></div>
																					<div class="text-uppercase">Level Risiko</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<!-- end risiko inheren -->

																	<!-- begin aktivitas pengendalian saat ini -->
																	<div class="fw-bold px-4 mt-3">Aktivitas Pengendalian Saat Ini</div>
																	<?php if(!empty($risk['pengendalian_reviu_dokumen_id']) || !empty($risk['pengendalian_wawancara_id'])):?>
																		<?php  
																			try {
																					$conn5 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
																					if(!empty($risk['pengendalian_reviu_dokumen_id']))
																					{
																						$sql_pengendalian = 'select * from oop_pengendalian_reviu_dokumen where risiko_id = :id';
																					}
																					elseif (!empty($risk['pengendalian_wawancara_id']))
																					{
																						$sql_pengendalian = 'select * from oop_pengendalian_wawancara where risiko_id = :id';
																					}
																					
																					$query_pengendalian = $conn5->prepare($sql_pengendalian);
																					$query_pengendalian->execute(array(':id'=>$_GET['details']));
																					$pengendalian_risiko = $query_pengendalian->fetch(PDO::FETCH_ASSOC);
																					$conn5=null;
																				} catch (PDOException $e) {
																					print "Error!: " . $e->getMessage() . "<br/>";
																			    	die();
																				}
																		?>
																		<div class="row  text-dark px-4">
																			<div class="col-md">
																				<div class="card" style="height: 10rem;">
																					<div class="card-body">
																						<div class="text-uppercase h5">Aktivitas</div>
																						<div class=""><?=$pengendalian_risiko['aktivitas_pengendalian']?></div>
																					</div>
																				</div>
																			</div>
																			<div class="col-md">
																				<div class="card" style="height: 10rem;">
																					<div class="card-body">
																						<div class="text-uppercase h5">Atribute</div>
																						<div class=""><?=$pengendalian_risiko['atribut_pengendalian']?></div>
																					</div>
																				</div>
																			</div>
																			<div class="col-md">
																				<div class="card" style="height: 10rem;">
																					<div class="card-body">
																						<div class="text-uppercase h5">Penilaian Kelemahan</div>
																						<div class=""><?=$pengendalian_risiko['penilaian_kelemahan_pengendalian']?></div>
																					</div>
																				</div>
																			</div>
																			<div class="col-md">
																				<div class="card" style="height: 10rem;">
																					<div class="card-body">
																						<div class="text-uppercase h5">Simpulan Efektifitas</div>
																						<div class=""><?=$pengendalian_risiko['simpulan_efektivitas_pengendalian']?></div>
																					</div>
																				</div>
																			</div>
																			<div class="col-md">
																				<div class="card" style="height: 10rem;">
																					<div class="card-body">
																						<div class="text-uppercase h5">Rekomendasi</div>
																						<div class=""><?=$pengendalian_risiko['rekomendasi']?></div>
																					</div>
																				</div>
																			</div>
																		</div>
																	<?php elseif($risk['risiko_inheren_level'] < 16):?>
																		<div class="card">
																			<div class="card-body">
																				<center>
																					<p class="text-dark h5">Tidak perlu dilakukan pengendalian risiko</p>
																				</center>
																			</div>
																		</div>
																	<?php else:?>
																		<div class="card">
																			<div class="card-body">
																				<center>
																					<p class="text-dark h5">
																						Belum ada Data Pengujian <br>
																						Pilih salah satu metode pengujian di bawah ini dan isi pada form yang tersedia.	
																					</p>
																					<div class="mb-4">
																						<button class="btn btn-sm btn-outline-dark mb-3" data-bs-toggle="modal" data-bs-target="#AddPengendalianModalReviuDokumen">Reviu Dokumen</button>
																						<button class="btn btn-sm btn-outline-dark mb-3" data-bs-toggle="modal" data-bs-target="#AddPengendalianModalWawancara">Wawancara/Survei/Observasi</button>
																					</div>
																				</center>
																			</div>
																		</div>
																	<?php endif;?>
																	<!-- end aktivitas pengendalian saat ini -->

																	<div class="col-md">
																	<!-- begin risiko residual -->
																	<div class="fw-bold px-4 mt-4">Risiko Residual</div>

																	<?php if(!empty($pengendalian_risiko['risiko_residual_kemungkinan'])):?>
																	<div class="row text-dark px-4 mb-4">
																		<div class="col-md">
																			<div class="card">
																				<div class="card-body">
																					<div class="h1 text-dark"><?=$pengendalian_risiko['risiko_residual_kemungkinan']?></div>
																					<div class="text-uppercase">Kemungkinan</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md">
																			<div class="card">
																				<div class="card-body">
																					<div class="h1 text-dark"><?=$pengendalian_risiko['risiko_residual_dampak']?></div>
																					<div class="text-uppercase">Dampak</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md">
																			<div class="card" style="background-color: <?=warnaRisiko($risk['risiko_inheren_level'])?>">
																				<div class="card-body">
																					<div class="h1 text-dark"><?=$pengendalian_risiko['risiko_residual_level']?></div>
																					<div class="text-uppercase">Level Risiko</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<?php elseif($risk['risiko_inheren_level'] < 16):?>
																		<div class="card">
																			<div class="card-body">
																				<center>
																					<p class="text-dark h5">Tidak perlu dilakukan pengendalian risiko</p>
																				</center>
																			</div>
																		</div>
																	<?php else:?>
																		<div class="card">
																			<div class="card-body">
																				<center>
																					<p class="text-dark h5">
																						Belum ada Data Pengujian <br>
																						Pilih salah satu metode pengujian di bawah ini dan isi pada form yang tersedia.	
																					</p>
																					<div class="mb-4">
																						<button class="btn btn-sm btn-outline-dark mb-3" data-bs-toggle="modal" data-bs-target="#AddPengendalianModalReviuDokumen">Reviu Dokumen</button>
																						<button class="btn btn-sm btn-outline-dark mb-3" data-bs-toggle="modal" data-bs-target="#AddPengendalianModalWawancara">Wawancara/Survei/Observasi</button>
																					</div>
																				</center>
																			</div>
																		</div>
																	<?php endif;?>
																	<!-- end risiko residual -->

																</div>
																</div>
															</div>
												    	</td>
												    </tr>
											    <!-- end show detil per risk -->
											<?php endif;?>
										<?php endforeach;?>
									<?php else:?>
										<tr>
											<td scope="col" colspan="10"><center>Belum ada data</center></td>
										</tr>
									<?php endif;?>
								  </tbody>
								</table>
							</div>
					    <?php elseif(isset($_GET['sasaran']) && isset($_GET['riskmitigation'])):?>
					    	<!-- get need-to-mitigated risks from particular sasaran id -->
					    	<?php  
								try {
										$conn7 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
										$sql_all_mitigated_risks = 'select oop_risk_register.kode_risiko_id, oop_risk_register.id as risiko_id, 
																	oop_risk_register.sasaran_id as sasaran_id,
										 							oop_risk_register.risk_event, oop_risk_register.penyebab_risiko, 
																	oop_pengendalian_reviu_dokumen.id, oop_pengendalian_reviu_dokumen.risiko_id, 
																	risiko_residual_kemungkinan, risiko_residual_dampak,
																	risiko_residual_level from oop_pengendalian_reviu_dokumen
																	left join oop_risk_register on oop_pengendalian_reviu_dokumen.risiko_id = oop_risk_register.id
																	where oop_risk_register.sasaran_id = :sasaran_id and  
																	oop_pengendalian_reviu_dokumen.risiko_residual_level >= 16
																	union all
																	select oop_risk_register.kode_risiko_id, oop_risk_register.id as risiko_id,
																	oop_risk_register.sasaran_id as sasaran_id, 
																	oop_risk_register.risk_event, oop_risk_register.penyebab_risiko, 
																	oop_pengendalian_wawancara.id, oop_pengendalian_wawancara.risiko_id, 
																	risiko_residual_kemungkinan, risiko_residual_dampak,
																	risiko_residual_level from oop_pengendalian_wawancara
																	left join oop_risk_register on oop_pengendalian_wawancara.risiko_id = oop_risk_register.id
																	where oop_risk_register.sasaran_id = :sasaran_id and 
																	oop_pengendalian_wawancara.risiko_residual_level >= 16
																	order by id asc';
										$query_all_mitigated_risks = $conn7->prepare($sql_all_mitigated_risks);
										$query_all_mitigated_risks->execute(array(':sasaran_id'=>$_GET['sasaran']));
										$all_mitigated_risks = $query_all_mitigated_risks->fetchAll(PDO::FETCH_ASSOC);
										$conn7=null;
									} catch (PDOException $e) {
										print "Error!: " . $e->getMessage() . "<br/>";
								    	die();
									}
					    	?>
					    	<p class="h4 mb-4 text-white">Risk Mitigation</p>
					    	<?php if(!empty($all_mitigated_risks)):?>
						    	<?php foreach($all_mitigated_risks as $mitigated_risk):?>
						    		<!-- load mitigasi risiko -->
							  		<?php  
							  			try {
												$conn8 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
												$sql_mitigasi_risiko = 'select * from oop_mitigasi_risiko where risiko_id = :risiko_id';
												$query_mitigasi_risiko = $conn8->prepare($sql_mitigasi_risiko);
												$query_mitigasi_risiko->execute(array(':risiko_id'=>$mitigated_risk['risiko_id']));
												$mitigasi_risiko = $query_mitigasi_risiko->fetchAll(PDO::FETCH_ASSOC);
												$conn8=null;
											} catch (PDOException $e) {
												print "Error!: " . $e->getMessage() . "<br/>";
										    	die();
											}
							  		?>
							    	<div class="card mb-4 border-0">
									  <div class="card-header fs-5 fw-bold" style="background-color: #1E6199; color: #FEFFFF">
									   <span class="badge bg-dark"><?=$mitigated_risk['kode_risiko_id']?></span> <span><?=$mitigated_risk['risk_event']?>
									  </span></div>
									  <div class="card-body">
									  	<!-- begin table mitigasi -->
									  	<?php if(empty($mitigasi_risiko)):?>
									  		<center>
									  			<p class="h5">Belum ada data</p>
									  			<button class="btn btn-warning btn-sm mb-2 mt-2" data-bs-toggle="modal" data-bs-target="#AddMitigasi" data-bs-risikoId="<?=$mitigated_risk['risiko_id']?>">Add Mitigation</button>
									  		</center>
									  	<?php else:?>
										    <table class="table table-sm table-hovered">
										    	<thead>
										    		<tr>
											    		<th scope="col">Respon Risiko</th>
											    		<th scope="col">Tindakan Mitigasi</th>
											    		<th scope="col">PIC</th>
											    		<th scope="col">Kebutuhan Sumber Daya</th>
											    		<th scope="col">Uraian Target</th>
											    		<th scope="col">Target Waktu Selesai</th>
											    		<th scope="col">Kemungkinan Risiko</th>
											    		<th scope="col">Dampak Risiko</th>
											    		<th scope="col">Level Risiko</th>
										    		</tr>
										    	</thead>
										    	<tbody>
										    		<?php foreach($mitigasi_risiko as $mitigasi):?>
										    		<tr>
										    			<td class="text-capitalize"><?=$mitigasi['respon_risiko']?></td>
										    			<td><?=$mitigasi['deskripsi_tindakan_mitigasi']?></td>
										    			<td><?=$mitigasi['pic']?></td>
										    			<td><?=$mitigasi['kebutuhan_sumber_daya']?></td>
										    			<td><?=$mitigasi['uraian_target']?></td>
										    			<td><?=date('d F Y', $mitigasi['target_waktu_selesai'])?></td>
										    			<td><?=$mitigasi['mitigasi_kemungkinan']?></td>
										    			<td><?=$mitigasi['mitigasi_dampak']?></td>
										    			<td><?=$mitigasi['mitigasi_level']?></td>
										    		</tr>
										    		<?php endforeach;?>
										    	</tbody>
										    </table>
									    <?php endif;?>
									    <!-- end tabel mitigasi -->
									  </div>
									</div>
								<?php endforeach;?>
							<?php else:?>
								<p class="mt-4">Tidak Ada Data</p>
							<?php endif;?>
					    <?php elseif(isset($_GET['sasaran']) && isset($_GET['riskmonitoring'])):?>
					    	<!-- load mitigasi risiko -->
					  		<?php  
					  			try {
										$conn10 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
										$sql_mitigasi_risiko = 'select mitigasi.*, risiko.risk_event, risiko.kode_risiko_id from oop_mitigasi_risiko mitigasi
																left join oop_risk_register risiko on mitigasi.risiko_id = risiko.id 
																where risiko.sasaran_id = :sasaran_id';
										$query_mitigasi_risiko = $conn10->prepare($sql_mitigasi_risiko);
										$query_mitigasi_risiko->execute(array(':sasaran_id'=>$_GET['sasaran']));
										$mitigasi_risiko = $query_mitigasi_risiko->fetchAll(PDO::FETCH_ASSOC);
										$conn10=null;
									} catch (PDOException $e) {
										print "Error!: " . $e->getMessage() . "<br/>";
								    	die();
									}
					  		?>
					    	<p class="h4 mb-4 text-white">Risk Monitoring</p>
					    	<?php foreach($mitigasi_risiko as $mitigasi):?>
					    	<div class="card mb-4 border-0">
					    		<div class="card-header" style="background-color: #1E6199; color: #FEFFFF">
					    			<div class="row">
					    				<div class="col-md-7">
											<div class="fw-bold">Rencana Mitigasi</div> 
											<div class="lead mb-3"><?=$mitigasi['deskripsi_tindakan_mitigasi']?></div>
											<div class=""><span class="fw-bold">Uraian Target: </span><?=$mitigasi['uraian_target']?></div>
					    					<div class=""><span class="fw-bold">Due Date</span> : <?=date('d F Y', $mitigasi['target_waktu_selesai'])?></div>
					    					<div class=""><span class="fw-bold">PIC :</span>  <?=$mitigasi['pic']?></div>
					    				</div>
					    				<div class="col-md">
					    					<div class="mb-1"><span class="fw-bold">Kode Risiko: <?=$mitigasi['kode_risiko_id']?></div>
					    					<div class="mb-2"><?=$mitigasi['risk_event']?></div>
					    					<button class="btn btn-sm btn-success mt-5"  data-bs-toggle="modal" data-bs-target="#addMonitoringRisiko" data-bs-risikoId="<?=$mitigasi['risiko_id']?>">Add Monitoring Progress</button>
					    				</div>	
					    			</div>
					    		</div>
					    		<div class="card-body">
					    			<!-- load monitoring risiko -->
							  		<?php  
							  			try {
												$conn11 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');

												$sql_monitoring_risiko = "select monitoring.id, monitoring.risiko_id, monitoring.uraian_progress, monitoring.epoch_pemantauan,
																			pengendalian.penilaian_kelemahan_pengendalian, 
																			pengendalian.simpulan_efektivitas_pengendalian,
																			pengendalian.risiko_residual_kemungkinan,
																			pengendalian.risiko_residual_dampak,
																			pengendalian.risiko_residual_level,
																			pengendalian.rekomendasi,
																			pengendalian.id as pengendalian_id
																			from oop_monitoring_risiko monitoring
																			left join 
																			(
																			   select id, risiko_id, aktivitas_pengendalian, penilaian_kelemahan_pengendalian, simpulan_efektivitas_pengendalian, 
																			   rekomendasi, risiko_residual_kemungkinan, risiko_residual_dampak, risiko_residual_level, isaftermitigasi
																			   from oop_pengendalian_reviu_dokumen pengendalian_dokumen where risiko_id = :risiko_id
																			   union
																			   select id, risiko_id, aktivitas_pengendalian, penilaian_kelemahan_pengendalian, simpulan_efektivitas_pengendalian, 
																			   rekomendasi, risiko_residual_kemungkinan, risiko_residual_dampak, risiko_residual_level, isaftermitigasi
																			   from oop_pengendalian_wawancara pengendalian_wawancara where risiko_id = :risiko_id
																			) as pengendalian
																			on monitoring.pengendalian_id = pengendalian.id
																			where monitoring.risiko_id = :risiko_id";

												$query_monitoring_risiko = $conn11->prepare($sql_monitoring_risiko);
												$query_monitoring_risiko->execute(array(':risiko_id'=>$mitigasi['risiko_id']));
												$monitoring_risiko = $query_monitoring_risiko->fetchAll(PDO::FETCH_ASSOC);
												$conn11=null;
											} catch (PDOException $e) {
												print "Error!: " . $e->getMessage() . "<br/>";
										    	die();
											}
							  		?>
					    			<p class="mb-3 h4">Hasil Pemantauan</p>
					    			<div>
					    				<table class="table table-sm table-hovered">
					    					<thead>
					    						<tr>
					    							<th scope="col" class="col">#</th>
					    							<th scope="col" class="col-md-2">Progress</th>
					    							<th scope="col" class="col-md-1">Date</th>
					    							<th scope="col" class="col-md-1">Prob. Risiko Residual</th>
					    							<th scope="col" class="col-md-1">Imp. Risiko Residual</th>
					    							<th scope="col" class="col-md-1">Lev. Risiko Residual</th>
					    							<th scope="col" class="cold-md-2">Penilaian Kelemahan Pengendalian</th>
					    							<th scope="col" class="cold-md-2">Simpulan Aktitivas Pengendalian</th>
					    							<th scope="col" class="cold-md-1">Rekomendasi</th>
					    						</tr>
					    					</thead>
					    					<tbody>
					    						<?php if(empty($monitoring_risiko)):?>
					    							<tr>
					    								<td colspan="5">
					    									<center>
					    										<div class="m-2 fw-bold">Belum ada data</div>
					    									</center>
					    								</td>
					    							</tr>
					    						<?php else:?>
					    							<?php $i = 1?>
					    							<?php foreach($monitoring_risiko as $monitoring):?>
					    							<tr>
					    								<td><?=$i?></td>
					    								<td><?=$monitoring['uraian_progress']?></td>
					    								<td><?=date('d F Y', $monitoring['epoch_pemantauan'])?></td>
					    								<?php if(!isset($monitoring['penilaian_kelemahan_pengendalian'])):?>
					    									<td colspan="9"><center><button class="btn btn-sm btn-outline-danger m-2" data-bs-toggle="modal" data-bs-target="#addPengendalianModalReviuDokumenMitigasi" data-bs-risikoIdPengendalian="<?=$mitigasi['risiko_id']?>" data-bs-monitoringId="<?=$monitoring['id']?>">Add Pengendalian</button></center></td>
					    								<?php else:?>
					    									<td><?=$monitoring['risiko_residual_kemungkinan']?></td>
					    									<td><?=$monitoring['risiko_residual_dampak']?></td>
					    									<td><?=$monitoring['risiko_residual_level']?></td>
					    									<td>
					    										<?=$monitoring['penilaian_kelemahan_pengendalian']?>
					    										<p class="small mt-2"><a href="<?=base_url()?>?sasaran=<?=$_GET['sasaran']?>&riskmonitoring=true&penilaian=<?=$monitoring['pengendalian_id']?>">Lihat detail penilaian</a></p>
					    									</td>
					    									<td><?=$monitoring['simpulan_efektivitas_pengendalian']?></td>
					    									<td><?=$monitoring['rekomendasi']?></td>
					    								<?php endif;?>
					    							</tr>
						    							<?php if(isset($_GET['sasaran']) && isset($_GET['riskmonitoring']) && isset($_GET['penilaian']) && $_GET['penilaian'] == $monitoring['pengendalian_id']):?>
						    							<!-- load tabel pengendalian -->
						    							<?php  
												  			try {
																	$conn13 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
																	$sql_pengendalian_dokumen = 'select * from oop_pengendalian_reviu_dokumen pengendalian_dokumen where id = :pengendalian_id';
																	$sql_pengendalian_wawancara = 'select * from oop_pengendalian_wawancara pengendalian_wawancara where id = :pengendalian_id';

																	$query_pengendalian_dokumen = $conn13->prepare($sql_pengendalian_dokumen);
																	$query_pengendalian_dokumen->execute(array(':pengendalian_id'=>$monitoring['pengendalian_id']));
																	$data_pengendalian_dokumen = $query_pengendalian_dokumen->fetch(PDO::FETCH_ASSOC);

																	$query_pengendalian_wawancara = $conn13->prepare($sql_pengendalian_wawancara);
																	$query_pengendalian_wawancara->execute(array(':pengendalian_id'=>$monitoring['pengendalian_id']));
																	$data_pengendalian_wawancara = $query_pengendalian_wawancara->fetch(PDO::FETCH_ASSOC);

																	$conn13=null;
																} catch (PDOException $e) {
																	print "Error!: " . $e->getMessage() . "<br/>";
															    	die();
																}
												  		?>
						    								<tr class="bg-light">
						    									<td colspan="9" class="px-4">
						    										<div class="fw-bold mb-3 mt-2"><center>Detail Penilaian Kelemahan Pengendalian</center></div>
					    											Metode Penilaian: 
					    											<?php if(!empty($data_pengendalian_dokumen)):?>
					    												Pengujian Pengendalian Metode Reviu Dokumen
					    												<table class="table table-sm table-bordered">
					    													<thead>
					    														<tr>
					    															<th scope="col" class="col-md-4">Atribut Pengendalian</th>
					    															<th scope="col">Jumlah Sampel</th>
					    															<th scope="col">Sampel sesuai Rancangan Pengendalian</th>
					    															<th scope="col">Sampel tidak Sesuai Rancangan Pengendalian</th>
					    															<th scope="col" class="col-md-4">Uraian Ketidaksesuaian</th>
					    															<th scope="col" class="col-md-1">Persentase Ketidaksesuaian</th>
					    														</tr>
					    													</thead>
					    													<tbody>
					    														<tr>
					    															<td><?=$data_pengendalian_dokumen['atribut_pengendalian']?></td>
					    															<td><?=$data_pengendalian_dokumen['jumlah_sampel']?></td>
					    															<td><?=$data_pengendalian_dokumen['jumlah_sampel_sesuai_rancangan_pengendalian']?></td>
					    															<td><?=$data_pengendalian_dokumen['jumlah_sampel_tidak_sesuai_rancangan_pengendalian']?></td>
					    															<td><?=$data_pengendalian_dokumen['uraian_ketidaksesuaian']?></td>
					    															<td><?=$data_pengendalian_dokumen['persentase_ketidaksesuaian']?></td>
					    														</tr>
					    													</tbody>
					    												</table>
					    											<?php else:?>
					    												Pengujian Pengendalian Metode Wawancara/Survei/Observasi
					    												<table class="table table-sm table-hovered">
					    													<thead>
					    														<tr>
					    															<th scope="col" class="col-md-4">Aktivitas Pengendalian</th>
					    															<th scope="col">Tipe Pemantauan</th>
					    															<th scope="col">Hasil Uji Aktivitas Pengendalian</th>
					    															<th scope="col" class="col-md-4">Uraian Ketidaksesuaian</th>
					    														</tr>
					    													</thead>
					    													<tbody>
					    														<tr>
					    															<td><?=$data_pengendalian_dokumen['aktivitas_pengendalian']?></td>
					    															<td><?=$data_pengendalian_dokumen['tipe_pemantauan']?></td>
					    															<td><?=$data_pengendalian_dokumen['hasil_uji_aktivitas_pengendalian']?></td>
					    															<td><?=$data_pengendalian_dokumen['uraian_ketidaksesuaian']?></td>
					    														</tr>
					    													</tbody>
					    												</table>
					    											<?php endif;?>
						    									</td>
						    								</tr>
						    							<?php endif;?>
					    							<?php $i++?>
					    							<?php endforeach;?>
					    						<?php endif;?>
					    					</tbody>
					    				</table>
					    			</div>
					    		</div>
					    	</div>
					    	<?php endforeach;?>
					    <?php endif;?>
					  </div>
					</div>
				</div>
			</div>
		</div>
	<?php endif;?>

	<?php if(isset($_GET['administrasi']) && isset($_SESSION['login']) && $_SESSION['login'] == true && $_SESSION['user_level'] == 1):?>
		<!-- begin navbar -->
		<nav class="navbar navbar-expand-lg navbar-light sticky-top" style="background-color: #17252A;">
		  <div class="container-fluid">
		    <a class="navbar-brand" href="<?=base_url()?>" style="color: #FEFFFF;">Direktorat Pengawasan Peredaran Pangan Olahan</a>
		    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		      <span class="navbar-toggler-icon"></span>
		    </button>
		    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
		      <ul class="navbar-nav">
		      	<li class="nav-item">
		      	  <a class="nav-link me-3" aria-current="page" href="<?=base_url()?>/?administrasi&mastersasaran"  style="color: #FEFFFF;">Master Sasaran Strategis</a>
		      	</li>
		      	<li class="nav-item">
		      	  <a class="nav-link me-3" aria-current="page" href="<?=base_url()?>/?administrasi&masterprosesbisnis"  style="color: #FEFFFF;">Master Proses Bisnis</a>
		      	</li>
		      	<li class="nav-item">
		      	  <a class="nav-link me-3" aria-current="page" href="<?=base_url()?>/?administrasi&manajemenuser"  style="color: #FEFFFF;">Manajemen User</a>
		      	</li>
		        <li class="nav-item">
		          <a class="nav-link btn btn-outline-warning" aria-current="page" href="<?=base_url()?>/?logout"  style="color: #FEFFFF;">Logout</a>
		        </li>
		      </ul>
		    </div>
		  </div>
		</nav>
		<!-- end navbar -->

		<!-- begin admin main content -->
		<div class="container-fluid">
		<?php if(isset($_GET['mastersasaran'])):?>
			<?php  
				// load sasaran strategis from database
				try {
					$conn17 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
					$conn17->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$sql_load_sasaran_strategis = 'select * from oop_sasaran_strategis order by id asc';
					$query_load_sasaran_strategis = $conn17->prepare($sql_load_sasaran_strategis);
					$query_load_sasaran_strategis->execute();
					$sasaran_strategis = $query_load_sasaran_strategis->fetchAll(PDO::FETCH_ASSOC);
					$conn17=null;
				} catch (PDOException $e) {
						print "Error!: " . $e->getMessage() . "<br/>";
				    	die();
				}
			?>
			<div class="row mt-4">
				<div class="col-md"></div>
				<div class="col-md-9">
					<div class="card">
						<div class="card-header">
							<div class="fw-bold fs-4">Sasaran Strategis</div>
						</div>
						<div class="card-body">
							<button class="btn btn-success my-4 float-end" data-bs-toggle="modal" data-bs-target="#addSasaranStrategisModal">Add New Sasaran Strategis</button>
							<table class="table table-sm table-hover">
								<thead>
									<tr>
										<th scope="col" class="col-md-1">Nomor</th>
										<th scope="col" class="col-md-7">Sasaran</th>
										<th scope="col" class="col-md-1">Edit</th>
										<th scope="col" class="col-md-1">Hapus</th>
										<th scope="col" class="col-md-1">Created</th>
										<th scope="col" class="col-md-1">Modified</th>
									</tr>
								</thead>
								<tbody>
									<?php if(empty($sasaran_strategis)):?>
										<tr>
											<td colspan="6"><center><span class="h4 my-2">Belum ada data</span></center></td>
										</tr>
									<?php else:?>
										<?php $counter_sasaran=1;?> 
										<?php foreach($sasaran_strategis as $sasaran):?>
												<tr>
													<td><?=$counter_sasaran?></td>
													<td><?=$sasaran['deskripsi']?></td>
													<td><a class="btn btn-warning btn-sm" href="<?=base_url()?>/?administrasi&mastersasaran&edit=<?=$sasaran['id']?>">Edit</a></td>
													<td><a class="btn btn-danger btn-sm" href="<?=base_url()?>/?administrasi&mastersasaran&delete=<?=$sasaran['id']?>">Hapus</a></td>
													<td><?=date('d F Y H:i:s', $sasaran['created_at'])?></td>
													<td><?=date('d F Y H:i:s', $sasaran['modified_at'])?></td>
												</tr>
											<?php if(isset($_GET['edit']) && $_GET['edit'] == $sasaran['id']):?>
												<tr>
													<td colspan="6">
														<div class="card">
															<div class="card-body">
																<p class="lead">Edit Sasaran Strategis ke-<?=$sasaran['id']?></p>
																<form method="POST" action="index.php">
																	<input type="hidden" name="sasaran_id" value="<?=$sasaran['id']?>">
																	<div class="mb-3">
																		<label class="form-label">Sasaran</label>
																		<textarea class="form-control" name="sasaran_deskripsi"><?=$sasaran['deskripsi']?></textarea>
																	</div>
																	<button type="submit" class="btn btn-success" name="submit_edit_sasaran_strategis">Simpan</button>	
																</form>
															</div>
														</div>
													</td>
												</tr>
											<?php endif;?>
											<?php if(isset($_GET['delete']) && $_GET['delete'] == $sasaran['id']):?>
												<tr>
													<td colspan="6">
														<div class="card">
															<div class="card-body">
																<p class="">Apakah Anda yakin untuk menghapus sasaran ini:</p>
																<center><span class="fw-bold fs-4"><?=$sasaran['deskripsi']?> ?</span></center>
																<form method="POST" action="index.php">
																	<input type="hidden" name="sasaran_id" value="<?=$sasaran['id']?>">
																	<center><button type="submit" class="mt-4 btn btn-danger btn-lg" name="submit_delete_sasaran_strategis">Delete</button></center>
																</form>
															</div>
														</div>
													</td>
												</tr>
											<?php endif;?>
											<?php $counter_sasaran++;?>
										<?php endforeach;?>
									<?php endif;?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-md"></div>
			</div>
		<?php elseif(isset($_GET['masterprosesbisnis'])):?>
		<?php elseif(isset($_GET['manajemenuser'])):?>
		<?php endif;?>
		</div>
		<!-- end admin main content -->
	<?php endif;?>

<!-- begin modals area -->

	<!-- begin register modal -->
	<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="registerModalLabel">Register New User</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        <form method="POST" action="index.php">
	          <div class="mb-3">
			    <label  class="form-label">Input your full name</label>
			    <input type="text" class="form-control" name="name" aria-describedby="emailHelp">
			  </div>
			  <div class="mb-3">
			    <label  class="form-label">Input your NIP</label>
			    <input type="number" class="form-control" name="nip" aria-describedby="emailHelp">
			    <small><i>Kosongkan jika tidak punya NIP</i></small>
			  </div>
			  <div class="mb-3">
			    <label  class="form-label">Input your email</label>
			    <input type="email" class="form-control" name="register_email" aria-describedby="emailHelp">
			  </div>
			  <div class="mb-3">
			    <label class="form-label">Input your desired password</label>
			    <input type="password" class="form-control" name="register_password">
			  </div>
			  <div class="mb-3">
			    <label class="form-label">Repeat your desired password</label>
			    <input type="password" class="form-control" name="register_password_confirmation">
			  </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary" name="register_new_user">Save changes</button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- end register modal -->

	<!-- begin get kategori risiko -->
	<?php  
		try {
				$conn2 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$sql_kategori_risiko = 'select * from oop_kategori_risiko';
				$query_kategori_risiko = $conn2->prepare($sql_kategori_risiko);
				$query_kategori_risiko->execute();
				$kategori_risiko = $query_kategori_risiko->fetchAll(PDO::FETCH_ASSOC);
				$conn2=null;
			} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
		    	die();
			}
	?>
	<!-- begin insert risk Modal -->
	<div class="modal fade" id="AddriskModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="AddriskModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="AddriskModalLabel">New Risk</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="index.php">
	      		<input type="hidden" name="sasaran_id" value="<?=$_GET['sasaran']?>">
				<div class="mb-3">
				 	<label class="form-label">Proses Bisnis</label>
				 	<textarea class="form-control" rows="3" name="proses_bisnis"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Kode Risiko</label>
				 	<input type="text" class="form-control" placeholder="Kode Risiko" name="kode_risiko" value="<?='S'.$_GET['sasaran'].date('my', time()).rand(1000,9999)?>" readonly >
				</div>
				<div class="mb-3">
					<label class="form-label">Kategori Risiko</label>
					<select class="form-select form-select-sm" name="kategori_risiko">
						<?php foreach($kategori_risiko as $kategori):?>
					  	<option value="<?=$kategori['id']?>"><?=$kategori['deskripsi']?></option>
					  	<?php endforeach;?>
					</select>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Risk Event/Uraian peristiwa risiko</label>
				 	<textarea class="form-control" rows="3" name="risk_event"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Penyebab Risiko</label>
				 	<textarea class="form-control" rows="3" name="sebab_risiko"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Sumber Risiko</label>
				 	<select class="form-select form-select-sm" name="sumber_risiko">
					  <option value="internal">Internal</option>
					  <option value="eksternal">Eksternal</option>
					</select>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Potensi Kerugian</label>
				 	<textarea class="form-control" rows="3" name="potensi_kerugian"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Pemilik Risiko</label>
				 	<input type="text" class="form-control" placeholder="mis: Direktur RPO" name="pemilik_risiko">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Nama Unit Kerja Terkait</label>
				 	<input type="text" class="form-control" placeholder="mis: Direktorat Registrasi Pangan Olahan" name="unit_terkait">
				</div>
				<div class="mb-3">
					<p>Risiko Inheren</p>
				 	<table class="table table-sm table-bordered small">
				 		<thead class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="3" colspan="3">Matriks Analisis Risiko</th>
				 				<th scope="col" colspan="5">Level Dampak</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">2</th>
				 				<th scope="col">3</th>
				 				<th scope="col">4</th>
				 				<th scope="col">5</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">Tidak Signifikan</th>
				 				<th scope="col">Kecil</th>
				 				<th scope="col">Sedang</th>
				 				<th scope="col">Besar</th>
				 				<th scope="col">Katastrope</th>
				 			</tr>
				 		</thead>
				 		<tbody class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="5">Level Kemungkinan</th>
				 				<th scope="col">5</th>
				 				<th scope="col">Hampir Pasti</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="9-51" data-kemungkinan="5" data-dampak="1">
				 						<label class="form-check-label">9</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="15-52" data-kemungkinan="5" data-dampak="2">
				 						<label class="form-check-label">15</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="18-53" data-kemungkinan="5" data-dampak="3">
				 						<label class="form-check-label">18</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="23-54" data-kemungkinan="5" data-dampak="4">
				 						<label class="form-check-label">23</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="25-55" data-kemungkinan="5" data-dampak="5">
				 						<label class="form-check-label">25</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">4</th>
				 				<th scope="col">Kemungkinan Besar</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline  form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="6-41" data-kemungkinan="4" data-dampak="1">
				 						<label class="form-check-label">6</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="12-42" data-kemungkinan="4" data-dampak="2">
				 						<label class="form-check-label">12</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="16-43" data-kemungkinan="4" data-dampak="3">
				 						<label class="form-check-label">16</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="19-44" data-kemungkinan="4" data-dampak="4">
				 						<label class="form-check-label">19</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="24-45" data-kemungkinan="4" data-dampak="5">
				 						<label class="form-check-label">24</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">3</th>
				 				<th scope="col">Mungkin</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="4-31" data-kemungkinan="3" data-dampak="1">
				 						<label class="form-check-label">4</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="10-32" data-kemungkinan="3" data-dampak="2">
				 						<label class="form-check-label">10</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="14-33" data-kemungkinan="3" data-dampak="3">
				 						<label class="form-check-label">14</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="17-34" data-kemungkinan="3" data-dampak="4">
				 						<label class="form-check-label">17</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="22-35" data-kemungkinan="3" data-dampak="5">
				 						<label class="form-check-label">22</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">2</th>
				 				<th scope="col">Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="2-21" data-kemungkinan="2" data-dampak="1">
				 						<label class="form-check-label">2</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="7-22" data-kemungkinan="2" data-dampak="2">
				 						<label class="form-check-label">7</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="11-23" data-kemungkinan="2" data-dampak="3">
				 						<label class="form-check-label">11</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="13-24" data-kemungkinan="2" data-dampak="4">
				 						<label class="form-check-label">13</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="21-25" data-kemungkinan="2" data-dampak="5">
				 						<label class="form-check-label">21</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">Sangat Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="1-11" data-kemungkinan="1" data-dampak="1">
				 						<label class="form-check-label">1</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="3-12" data-kemungkinan="1" data-dampak="2">
				 						<label class="form-check-label">3</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="5-13" data-kemungkinan="1" data-dampak="3">
				 						<label class="form-check-label">5</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="8-14" data-kemungkinan="1" data-dampak="4">
				 						<label class="form-check-label">8</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="level_risiko" value="20-15" data-kemungkinan="1" data-dampak="5">
				 						<label class="form-check-label">20</label>
				 					</div>
				 				</td>
				 			</tr>
				 		</tbody>
				 	</table>
				</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary" name="submit">Save changes</button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- end insert risk Modal -->
	

	<!-- begin insert aktivitas pengendalian Modal -->
	<div class="modal fade" id="AddPengendalianModalReviuDokumen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="AddpengendalianModalReviuDokumenLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="AddPengendalianModalReviuDokumenLabel">Pengujian Aktivitas Pengendalian dengan Reviu Dokumen</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="index.php">
	      		<input type="hidden" name="sasaran_id" value="<?=$_GET['sasaran']?>">
	      		<input type="hidden" name="risiko_id" value="<?=$_GET['details']?>">
	      		<div class="mb-3">
				 	<label class="form-label">Aktivitas Pengendalian</label>
				 	<textarea class="form-control" rows="3" name="aktivitas_pengendalian"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Atribut Pengendalian</label>
				 	<textarea class="form-control" rows="3" name="atribut_pengendalian"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Metode Sampling</label>
				 	<select class="form-select" name="metode_sampling">
						<option selected>Pilih Metode sampling</option>
						<option value="sensus">Sensus</option>
						<option value="uji_petik">Uji Petik</option>
					</select>
				</div>
	      		<div class="mb-3">
				 	<label class="form-label">Jumlah Sampel</label>
				 	<input type="number" class="form-control" name="jumlah_sampel" id="jumlah_sampel" onchange="updatePersentaseTidakSesuaiPengendalian()">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Jumlah Sampel yang Sesuai Rancangan Pengendalian</label>
				 	<input type="number" class="form-control" name="jumlah_sampel_sesuai_rancangan_pengendalian" id="jumlah_sampel_sesuai_rancangan_pengendalian">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Jumlah Sampel yang TIDAK Sesuai Rancangan Pengendalian</label>
				 	<input type="number" class="form-control" name="jumlah_sampel_tidak_sesuai_rancangan_pengendalian" id="jumlah_sampel_tidak_sesuai_rancangan_pengendalian" onchange="updatePersentaseTidakSesuaiPengendalian()">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Uraian Ketidaksesuaian</label>
				 	<textarea class="form-control" rows="3" name="uraian_ketidaksesuaian"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">% Ketidaksesuaian </label>
				 	<input type="number" step="any" class="form-control" name="persentase_tidak_sesuai_rancangan_pengendalian" id="persentase_tidak_sesuai_rancangan_pengendalian">
				</div>
				<div class="mb-3">
					<p>Risiko Residual</p>
				 	<table class="table table-sm table-bordered small">
				 		<thead class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="3" colspan="3">Matriks Analisis Risiko</th>
				 				<th scope="col" colspan="5">Level Dampak</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">2</th>
				 				<th scope="col">3</th>
				 				<th scope="col">4</th>
				 				<th scope="col">5</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">Tidak Signifikan</th>
				 				<th scope="col">Kecil</th>
				 				<th scope="col">Sedang</th>
				 				<th scope="col">Besar</th>
				 				<th scope="col">Katastrope</th>
				 			</tr>
				 		</thead>
				 		<tbody class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="5">Level Kemungkinan</th>
				 				<th scope="col">5</th>
				 				<th scope="col">Hampir Pasti</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="9-51" data-kemungkinan="5" data-dampak="1">
				 						<label class="form-check-label">9</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="15-52" data-kemungkinan="5" data-dampak="2">
				 						<label class="form-check-label">15</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="18-53" data-kemungkinan="5" data-dampak="3">
				 						<label class="form-check-label">18</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="23-54" data-kemungkinan="5" data-dampak="4">
				 						<label class="form-check-label">23</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="25-55" data-kemungkinan="5" data-dampak="5">
				 						<label class="form-check-label">25</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">4</th>
				 				<th scope="col">Kemungkinan Besar</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline  form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="6-41" data-kemungkinan="4" data-dampak="1">
				 						<label class="form-check-label">6</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="12-42" data-kemungkinan="4" data-dampak="2">
				 						<label class="form-check-label">12</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="16-43" data-kemungkinan="4" data-dampak="3">
				 						<label class="form-check-label">16</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="19-44" data-kemungkinan="4" data-dampak="4">
				 						<label class="form-check-label">19</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="24-45" data-kemungkinan="4" data-dampak="5">
				 						<label class="form-check-label">24</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">3</th>
				 				<th scope="col">Mungkin</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="4-31" data-kemungkinan="3" data-dampak="1">
				 						<label class="form-check-label">4</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="10-32" data-kemungkinan="3" data-dampak="2">
				 						<label class="form-check-label">10</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="14-33" data-kemungkinan="3" data-dampak="3">
				 						<label class="form-check-label">14</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="17-34" data-kemungkinan="3" data-dampak="4">
				 						<label class="form-check-label">17</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="22-35" data-kemungkinan="3" data-dampak="5">
				 						<label class="form-check-label">22</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">2</th>
				 				<th scope="col">Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="2-21" data-kemungkinan="2" data-dampak="1">
				 						<label class="form-check-label">2</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="7-22" data-kemungkinan="2" data-dampak="2">
				 						<label class="form-check-label">7</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="11-23" data-kemungkinan="2" data-dampak="3">
				 						<label class="form-check-label">11</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="13-24" data-kemungkinan="2" data-dampak="4">
				 						<label class="form-check-label">13</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="21-25" data-kemungkinan="2" data-dampak="5">
				 						<label class="form-check-label">21</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">Sangat Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="1-11" data-kemungkinan="1" data-dampak="1">
				 						<label class="form-check-label">1</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="3-12" data-kemungkinan="1" data-dampak="2">
				 						<label class="form-check-label">3</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="5-13" data-kemungkinan="1" data-dampak="3">
				 						<label class="form-check-label">5</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="8-14" data-kemungkinan="1" data-dampak="4">
				 						<label class="form-check-label">8</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="20-15" data-kemungkinan="1" data-dampak="5">
				 						<label class="form-check-label">20</label>
				 					</div>
				 				</td>
				 			</tr>
				 		</tbody>
				 	</table>
				</div>			
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary" name="pengendalian_reviu_dokumen">Save changes</button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- end insert aktivitas pengendalian Modal -->


	<!-- begin insert mitigasi Modal -->
	<div class="modal fade" id="AddMitigasi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="AddMitigasiLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="AddMitigasiLabel">Pengujian Aktivitas Pengendalian dengan Reviu Dokumen</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="index.php">
	      		<input type="hidden" name="sasaran_id" value="<?=$_GET['sasaran']?>">
	      		<input type="hidden" name="risiko_id" id="risikoId">
	      		<div class="mb-3">
				 	<label class="form-label">Respon Risiko</label>
				 	<select class="form-select" name="respon_risiko" id="respon_risiko">
				 		<option value="hindari">Hindari</option>
				 		<option value="reduksi">Reduksi</option>
				 		<option value="alihkan">Alihkan/Bagi</option>
				 		<option value="terima">Terima</option>
				 	</select>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Deskripsi Tindakan Mitigasi</label>
				 	<textarea class="form-control" rows="3" name="deskripsi_tindakan_mitigasi"></textarea>
				</div>
	      		<div class="mb-3">
				 	<label class="form-label">PIC</label>
				 	<input type="text" class="form-control" name="pic" id="pic">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Sumber Daya Yang Dibutuhkan</label>
				 	<input type="text" class="form-control" name="kebutuhan_sumber_daya" id="kebutuhan_sumber_daya">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Uraian Target</label>
				 	<textarea class="form-control" rows="3" name="uraian_target"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Target Waktu Penyelesaian</label>
				 	<input type="date" class="form-control" name="target_waktu_selesai" id="target_waktu_selesai">
				</div>
				
				<div class="mb-3">
					<p>Risiko Mitigasi</p>
				 	<table class="table table-sm table-bordered small">
				 		<thead class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="3" colspan="3">Matriks Analisis Risiko</th>
				 				<th scope="col" colspan="5">Level Dampak</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">2</th>
				 				<th scope="col">3</th>
				 				<th scope="col">4</th>
				 				<th scope="col">5</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">Tidak Signifikan</th>
				 				<th scope="col">Kecil</th>
				 				<th scope="col">Sedang</th>
				 				<th scope="col">Besar</th>
				 				<th scope="col">Katastrope</th>
				 			</tr>
				 		</thead>
				 		<tbody class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="5">Level Kemungkinan</th>
				 				<th scope="col">5</th>
				 				<th scope="col">Hampir Pasti</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="9-51" data-kemungkinan="5" data-dampak="1">
				 						<label class="form-check-label">9</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="15-52" data-kemungkinan="5" data-dampak="2">
				 						<label class="form-check-label">15</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="18-53" data-kemungkinan="5" data-dampak="3">
				 						<label class="form-check-label">18</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="23-54" data-kemungkinan="5" data-dampak="4">
				 						<label class="form-check-label">23</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="25-55" data-kemungkinan="5" data-dampak="5">
				 						<label class="form-check-label">25</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">4</th>
				 				<th scope="col">Kemungkinan Besar</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline  form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="6-41" data-kemungkinan="4" data-dampak="1">
				 						<label class="form-check-label">6</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="12-42" data-kemungkinan="4" data-dampak="2">
				 						<label class="form-check-label">12</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="16-43" data-kemungkinan="4" data-dampak="3">
				 						<label class="form-check-label">16</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="19-44" data-kemungkinan="4" data-dampak="4">
				 						<label class="form-check-label">19</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="24-45" data-kemungkinan="4" data-dampak="5">
				 						<label class="form-check-label">24</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">3</th>
				 				<th scope="col">Mungkin</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="4-31" data-kemungkinan="3" data-dampak="1">
				 						<label class="form-check-label">4</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="10-32" data-kemungkinan="3" data-dampak="2">
				 						<label class="form-check-label">10</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="14-33" data-kemungkinan="3" data-dampak="3">
				 						<label class="form-check-label">14</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="17-34" data-kemungkinan="3" data-dampak="4">
				 						<label class="form-check-label">17</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="22-35" data-kemungkinan="3" data-dampak="5">
				 						<label class="form-check-label">22</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">2</th>
				 				<th scope="col">Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="2-21" data-kemungkinan="2" data-dampak="1">
				 						<label class="form-check-label">2</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="7-22" data-kemungkinan="2" data-dampak="2">
				 						<label class="form-check-label">7</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="11-23" data-kemungkinan="2" data-dampak="3">
				 						<label class="form-check-label">11</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="13-24" data-kemungkinan="2" data-dampak="4">
				 						<label class="form-check-label">13</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="21-25" data-kemungkinan="2" data-dampak="5">
				 						<label class="form-check-label">21</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">Sangat Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="1-11" data-kemungkinan="1" data-dampak="1">
				 						<label class="form-check-label">1</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="3-12" data-kemungkinan="1" data-dampak="2">
				 						<label class="form-check-label">3</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="5-13" data-kemungkinan="1" data-dampak="3">
				 						<label class="form-check-label">5</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="8-14" data-kemungkinan="1" data-dampak="4">
				 						<label class="form-check-label">8</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_mitigasi" value="20-15" data-kemungkinan="1" data-dampak="5">
				 						<label class="form-check-label">20</label>
				 					</div>
				 				</td>
				 			</tr>
				 		</tbody>
				 	</table>
				</div>			
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary" name="submit_risiko_mitigasi">Save changes</button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- end insert aktivitas pengendalian Modal -->


	<!-- begin insert tindakan monitoring modal -->
	<div class="modal fade" id="addMonitoringRisiko" tabindex="-1" aria-labelledby="addMonitoringRisikoLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="addMonitoringRisikoLabel">Add Monitoring Risiko</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="index.php">
	      	<input type="hidden" name="sasaran_id" value="<?=$_GET['sasaran']?>">
	      	<input type="hidden" name="risiko_id" id="risiko_id_monitoring">
	      	<div class="mb-3">
			 	<label class="form-label">Uraian Progress</label>
			 	<textarea class="form-control" rows="3" name="uraian_progress_monitoring"></textarea>
			</div>
	        <div class="mb-3">
			 	<label class="form-label">Date</label>
			 	<input type="date" class="form-control" name="date_monitoring" id="date_monitoring">
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary" name="submit_monitoring_risiko">Save changes</button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- end insert tindakan monitoring modal -->

	<!-- begin insert aktivitas pengendalian after mitigasi Modal -->
	<div class="modal fade" id="addPengendalianModalReviuDokumenMitigasi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPengendalianModalReviuDokumenMitigasiLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-scrollable modal-xl">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="AddPengendalianModalReviuDokumenMitigasiLabel">Pengujian Aktivitas Pengendalian dengan Reviu Dokumen</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<form method="POST" action="index.php">
	      		<input type="hidden" name="sasaran_id" value="<?=$_GET['sasaran']?>">
	      		<input type="hidden" name="risiko_id_pengendalian" id="risiko_id_pengendalian">
	      		<input type="hidden" name="id_monitoring" id="id_monitoring">
	      		<div class="mb-3">
				 	<label class="form-label">Aktivitas Pengendalian</label>
				 	<textarea class="form-control" rows="3" name="aktivitas_pengendalian"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Atribut Pengendalian</label>
				 	<textarea class="form-control" rows="3" name="atribut_pengendalian"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">Metode Sampling</label>
				 	<select class="form-select" name="metode_sampling_mitigasi">
						<option selected>Pilih Metode sampling</option>
						<option value="sensus">Sensus</option>
						<option value="uji_petik">Uji Petik</option>
					</select>
				</div>
	      		<div class="mb-3">
				 	<label class="form-label">Jumlah Sampel</label>
				 	<input type="number" class="form-control" name="jumlah_sampel" id="jumlah_sampel_monitoring_mitigasi" onchange="updatePersentaseTidakSesuaiPengendalianMonitoringMitigasi()">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Jumlah Sampel yang Sesuai Rancangan Pengendalian</label>
				 	<input type="number" class="form-control" name="jumlah_sampel_sesuai_rancangan_pengendalian" id="jumlah_sampel_sesuai_rancangan_pengendalian_monitoring_mitigasi">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Jumlah Sampel yang TIDAK Sesuai Rancangan Pengendalian</label>
				 	<input type="number" class="form-control" name="jumlah_sampel_tidak_sesuai_rancangan_pengendalian" id="jumlah_sampel_tidak_sesuai_rancangan_pengendalian_monitoring_mitigasi" onchange="updatePersentaseTidakSesuaiPengendalianMonitoringMitigasi()">
				</div>
				<div class="mb-3">
				 	<label class="form-label">Uraian Ketidaksesuaian</label>
				 	<textarea class="form-control" rows="3" name="uraian_ketidaksesuaian"></textarea>
				</div>
				<div class="mb-3">
				 	<label class="form-label">% Ketidaksesuaian </label>
				 	<input type="number" step="any" class="form-control" name="persentase_tidak_sesuai_rancangan_pengendalian" id="persentase_tidak_sesuai_rancangan_pengendalian_monitoring_mitigasi"  readonly>
				</div>
				<div class="mb-3">
					<p>Risiko Residual</p>
				 	<table class="table table-sm table-bordered small">
				 		<thead class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="3" colspan="3">Matriks Analisis Risiko</th>
				 				<th scope="col" colspan="5">Level Dampak</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">2</th>
				 				<th scope="col">3</th>
				 				<th scope="col">4</th>
				 				<th scope="col">5</th>
				 			</tr>
				 			<tr>
				 				<th scope="col">Tidak Signifikan</th>
				 				<th scope="col">Kecil</th>
				 				<th scope="col">Sedang</th>
				 				<th scope="col">Besar</th>
				 				<th scope="col">Katastrope</th>
				 			</tr>
				 		</thead>
				 		<tbody class="align-middle text-center">
				 			<tr>
				 				<th scope="col" rowspan="5">Level Kemungkinan</th>
				 				<th scope="col">5</th>
				 				<th scope="col">Hampir Pasti</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="9-51" data-kemungkinan="5" data-dampak="1">
				 						<label class="form-check-label">9</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="15-52" data-kemungkinan="5" data-dampak="2">
				 						<label class="form-check-label">15</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="18-53" data-kemungkinan="5" data-dampak="3">
				 						<label class="form-check-label">18</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="23-54" data-kemungkinan="5" data-dampak="4">
				 						<label class="form-check-label">23</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="25-55" data-kemungkinan="5" data-dampak="5">
				 						<label class="form-check-label">25</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">4</th>
				 				<th scope="col">Kemungkinan Besar</th>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline  form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="6-41" data-kemungkinan="4" data-dampak="1">
				 						<label class="form-check-label">6</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="12-42" data-kemungkinan="4" data-dampak="2">
				 						<label class="form-check-label">12</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="16-43" data-kemungkinan="4" data-dampak="3">
				 						<label class="form-check-label">16</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="19-44" data-kemungkinan="4" data-dampak="4">
				 						<label class="form-check-label">19</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="24-45" data-kemungkinan="4" data-dampak="5">
				 						<label class="form-check-label">24</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">3</th>
				 				<th scope="col">Mungkin</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="4-31" data-kemungkinan="3" data-dampak="1">
				 						<label class="form-check-label">4</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="10-32" data-kemungkinan="3" data-dampak="2">
				 						<label class="form-check-label">10</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="14-33" data-kemungkinan="3" data-dampak="3">
				 						<label class="form-check-label">14</label>
				 					</div>
				 				</td>
				 				<td style="background-color: #CF9E00;">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="17-34" data-kemungkinan="3" data-dampak="4">
				 						<label class="form-check-label">17</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="22-35" data-kemungkinan="3" data-dampak="5">
				 						<label class="form-check-label">22</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">2</th>
				 				<th scope="col">Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="2-21" data-kemungkinan="2" data-dampak="1">
				 						<label class="form-check-label">2</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="7-22" data-kemungkinan="2" data-dampak="2">
				 						<label class="form-check-label">7</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="11-23" data-kemungkinan="2" data-dampak="3">
				 						<label class="form-check-label">11</label>
				 					</div>
				 				</td>
				 				<td class="bg-warning">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="13-24" data-kemungkinan="2" data-dampak="4">
				 						<label class="form-check-label">13</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="21-25" data-kemungkinan="2" data-dampak="5">
				 						<label class="form-check-label">21</label>
				 					</div>
				 				</td>
				 			</tr>
				 			<tr>
				 				<th scope="col">1</th>
				 				<th scope="col">Sangat Jarang</th>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="1-11" data-kemungkinan="1" data-dampak="1">
				 						<label class="form-check-label">1</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="3-12" data-kemungkinan="1" data-dampak="2">
				 						<label class="form-check-label">3</label>
				 					</div>
				 				</td>
				 				<td class="bg-info">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="5-13" data-kemungkinan="1" data-dampak="3">
				 						<label class="form-check-label">5</label>
				 					</div>
				 				</td>
				 				<td class="bg-success">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="8-14" data-kemungkinan="1" data-dampak="4">
				 						<label class="form-check-label">8</label>
				 					</div>
				 				</td>
				 				<td class="bg-danger">
				 					<div class="form-check form-check-inline form-switch">
				 						<input class="form-check-input" type="radio" name="risiko_residual_reviu_dokumen" value="20-15" data-kemungkinan="1" data-dampak="5">
				 						<label class="form-check-label">20</label>
				 					</div>
				 				</td>
				 			</tr>
				 		</tbody>
				 	</table>
				</div>			
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary" name="pengendalian_reviu_dokumen_mitigasi">Save changes</button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- end insert aktivitas pengendalian after mitigasi Modal -->

	<!-- begin add sasaran strategis modal -->
	<div class="modal" id="addSasaranStrategisModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
		<div class="modal-dialog modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addSasaranStrategisModalLabel">New Sasaran Strategis</h5>
		        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="POST" action="index.php">
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Input Sasaran Strategis baru di bawah ini</label>
							<textarea class="form-control" rows="6" name="new_sasaran_strategis"></textarea>
						</div>	
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			        	<button type="submit" class="btn btn-primary" name="submit_add_sasaran_strategis">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- end add sasaran strategis modal -->

<!--end modals area -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

    <script type="text/javascript">
    	function updatePersentaseTidakSesuaiPengendalian() {
	    	let jumlah_sampel = document.getElementById("jumlah_sampel").value;
	    	let jumlah_sampel_tidak_sesuai_rancangan_pengendalian = document.getElementById("jumlah_sampel_tidak_sesuai_rancangan_pengendalian").value;
	    	let persentase_tidak_sesuai_rancangan_pengendalian = jumlah_sampel_tidak_sesuai_rancangan_pengendalian * 100 / jumlah_sampel;
	    	document.getElementById("persentase_tidak_sesuai_rancangan_pengendalian").value = persentase_tidak_sesuai_rancangan_pengendalian;
	    }

	    function updatePersentaseTidakSesuaiPengendalianMonitoringMitigasi() {
	    	let jumlah_sampel = document.getElementById("jumlah_sampel_monitoring_mitigasi").value;
	    	let jumlah_sampel_tidak_sesuai_rancangan_pengendalian = document.getElementById("jumlah_sampel_tidak_sesuai_rancangan_pengendalian_monitoring_mitigasi").value;
	    	let persentase_tidak_sesuai_rancangan_pengendalian = jumlah_sampel_tidak_sesuai_rancangan_pengendalian * 100 / jumlah_sampel;
	    	document.getElementById("persentase_tidak_sesuai_rancangan_pengendalian_monitoring_mitigasi").value = persentase_tidak_sesuai_rancangan_pengendalian;
	    }


	    let modalMitigasiRisiko = document.getElementById('AddMitigasi');
	    modalMitigasiRisiko.addEventListener('show.bs.modal', function(event) {
	    	let button = event.relatedTarget;
	    	let recipient = button.getAttribute('data-bs-risikoId');
	    	let inputRisikoId = modalMitigasiRisiko.querySelector('.modal-body #risikoId');
	    	inputRisikoId.value = recipient;
	    })	

	    let addMonitoringRisiko = document.getElementById('addMonitoringRisiko');
	   addMonitoringRisiko.addEventListener('show.bs.modal', function(event) {
	    	let button = event.relatedTarget;
	    	let recipient = button.getAttribute('data-bs-risikoId');
	    	let inputRisikoId = addMonitoringRisiko.querySelector('.modal-body #risiko_id_monitoring');
	    	inputRisikoId.value = recipient;
	    })	

	   let addPengendalianModalReviuDokumenMitigasi = document.getElementById('addPengendalianModalReviuDokumenMitigasi');
	   addPengendalianModalReviuDokumenMitigasi.addEventListener('show.bs.modal', function(event) {
	    	let button2 = event.relatedTarget;
	    	let recipient = button2.getAttribute('data-bs-risikoIdPengendalian');
	    	let idMonitoring = button2.getAttribute('data-bs-monitoringId');
	    	let inputRisikoIdPengendalian = addPengendalianModalReviuDokumenMitigasi.querySelector('.modal-body #risiko_id_pengendalian');
	    	let inputIdMonitoring = addPengendalianModalReviuDokumenMitigasi.querySelector('.modal-body #id_monitoring');
	    	inputRisikoIdPengendalian.value = recipient;
	    	inputIdMonitoring.value =idMonitoring;
	    })	
    </script>
</body>
</html>