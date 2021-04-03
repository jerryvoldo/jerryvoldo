<?php  
	ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

	require_once __DIR__ . '/vendor/autoload.php';
	$mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);

	// load table risk register
	try {
			$con_pdf = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
			$sql = 'select * from oop_sasaran_strategis order by id asc';
			$query = $con_pdf->prepare($sql);
			$query->execute();
			$sasaran = $query->fetchAll(PDO::FETCH_ASSOC);
			$con_pdf=null;
	} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
	    	die();
	}
?>




<?php ob_start();?>
<!DOCTYPE html>
<html>
<head>
	<title>export pdf</title>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		th {
			background-color: gray;
		}
		th, td {
			padding: 10px;
		}
	</style>
</head>
<body>
	<center><h3>Risk Register</h3></center>
	<br>
	<p>Unit Kerja: Direktorat Pengawasan Peredaran Pangan Olahan</p>
	<p>Periode Identifikasi: Triwulan 1 2021</p>
	<ol>
	<?php foreach($sasaran as $s):?>
		<?php $counter_risk=1;?>
		<li>Sasaran Strategis: <?=$s['deskripsi']?></li>
		<!-- load tabel risiko -->
		<?php 
			try {
					$con_pdf = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
					$sql = 'select risiko.*, kategori.deskripsi as kategori_deskripsi from oop_risk_register risiko 
							left join oop_kategori_risiko kategori on risiko.kategori_risiko_id = kategori.id
					 		where risiko.sasaran_id = :sasaran_id order by id asc';
					$query = $con_pdf->prepare($sql);
					$query->execute(array(':sasaran_id'=>$s['id']));
					$risks = $query->fetchAll(PDO::FETCH_ASSOC);
					$con_pdf=null;
			} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
			    	die();
			}
		?>
		<!-- end load tabel risiko -->
		<table border="1">
			<thead>
				<tr>
					<th>No</th>
					<th>Proses Bisnis</th>
					<th>Kode Risiko</th>
					<th>Kategori Risiko</th>
					<th>Risk Event</th>
					<th>Penyebab Risiko</th>
					<th>Sumber Risiko</th>
					<th>Potensi Kerugian</th>
					<th>Pemilik Risiko</th>
					<th>Unit Terkait</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($risks)):?>
					<tr>
						<td colspan="10"><center>Belum ada data</center></td>
					</tr>
				<?php else:?>
				<?php foreach($risks as $risk):?>
					<tr>
						<td><?=$counter_risk?></td>
						<td><?=$risk['proses_bisnis']?></td>
						<td><?=$risk['kode_risiko_id']?></td>
						<td><?=$risk['kategori_deskripsi']?></td>
						<td><?=$risk['risk_event']?></td>
						<td><?=$risk['penyebab_risiko']?></td>
						<td><?=$risk['sumber_risiko']?></td>
						<td><?=$risk['potensi_kerugian']?></td>
						<td><?=$risk['pemilik_risiko']?></td>
						<td><?=$risk['unit_terkait']?></td>
					</tr>
					<tr>
						<td colspan="10">
							<table width="100%" border="1">
								<thead>
									<tr>
										<th colspan="3">Risiko Inheren</th>
									</tr>
									<tr>
										<th>Kemungkinan</th>
										<th>Dampak</th>
										<th>Level</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?=$risk['risiko_inheren_kemungkinan']?></td>
										<td><?=$risk['risiko_inheren_dampak']?></td>
										<td><?=$risk['risiko_inheren_level']?></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<?php $counter_risk++;?>
				<?php endforeach;?>
				<?php endif;?>
			</tbody>
		</table><br>
	<?php endforeach;?>
	</ol>
</body>
</html>

<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf->shrink_tables_to_fit = 1;
$mpdf->WriteHTML($html);
$mpdf->Output();
?>