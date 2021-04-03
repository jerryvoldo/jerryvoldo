<?php  
	ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

	header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8");
	header("Content-Disposition: attachment; filename=risk_register.xls");

	// load table risk register
	try {
			$con_pdf = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
			$sql = 'select risiko.*, sasaran.deskripsi as sasaran_deskripsi, kategori.deskripsi as kategori_risiko from oop_risk_register risiko
					left join oop_sasaran_strategis sasaran on risiko.sasaran_id = sasaran.id 
					left join oop_kategori_risiko kategori on risiko.kategori_risiko_id = kategori.id
					order by risiko.id asc';
			$query = $con_pdf->prepare($sql);
			$query->execute();
			$risks = $query->fetchAll(PDO::FETCH_ASSOC);
			$con_pdf=null;
	} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
	    	die();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>export pdf</title>
</head>
<body>
	<center><h3>Risk Register</h3></center>
	<br>
	<p>Unit Kerja: Direktorat Pengawasan Peredaran Pangan Olahan</p>
	<p>Periode Identifikasi: Triwulan 1 2021</p>
	<table border="1">
		<thead>
			<tr>
				<th>No</th>
				<th>Sasaran</th>
				<th>Kegiatan/Proses Bisnis</th>
				<th>Kode Risiko</th>
				<th>Kategori Risiko</th>
				<th>Risk Event</th>
				<th>Penyebab Risiko</th>
				<th>Sumber Risiko</th>
				<th>Akibat/Potensi Kerugian</th>
				<th>Pemilik Risiko</th>
				<th>Nama Unit Kerja Terkait</th>
			</tr>
			<tr>
				<th>1</th>
				<th>2</th>
				<th>3</th>
				<th>4</th>
				<th>5</th>
				<th>6</th>
				<th>7</th>
				<th>8</th>
				<th>9</th>
				<th>10</th>
				<th>11</th>
			</tr>
		</thead>
		<tbody>
			<?php $counter_risiko=1;?>
			<?php foreach($risks as $risk):?>
				<tr>
					<td><?=$counter_risiko?></td>
					<td><?=$risk['sasaran_deskripsi']?></td>
					<td><?=$risk['proses_bisnis']?></td>
					<td><?=$risk['kode_risiko_id']?></td>
					<td><?=$risk['kategori_risiko']?></td>
					<td><?=$risk['risk_event']?></td>
					<td><?=$risk['penyebab_risiko']?></td>
					<td><?=$risk['sumber_risiko']?></td>
					<td><?=$risk['potensi_kerugian']?></td>
					<td><?=$risk['pemilik_risiko']?></td>
					<td><?=$risk['unit_terkait']?></td>
				</tr>
				<?php $counter_risiko++;?>
			<?php endforeach;?>
		</tbody>
	</table>
</body>
</html>