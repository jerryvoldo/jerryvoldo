<?php  
	ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

	require_once __DIR__ . '/vendor/autoload.php';
	$mpdf = new \Mpdf\Mpdf(['format'=>'Legal','orientation'=>'L']);

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

	// load table penandatangan
	try {
			$con_pdf = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
			$sql = 'select * from oop_penandatangan';
			$query = $con_pdf->prepare($sql);
			$query->execute();
			$all_penandatangan = $query->fetch(PDO::FETCH_ASSOC);
			$con_pdf=null;

			if(!empty($all_penandatangan))
			{
				$penandatangan = array();
				foreach($all_penandatangan as $key=>$value)
				{
					try {
							$conn30 = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
							$sql_load_personel = 'select nama_lengkap, jabatan from oop_personel where id = :id_penandatangan';
							$query_load_personel = $conn30->prepare($sql_load_personel);
							$query_load_personel->execute(array(':id_penandatangan'=>$value));
							$personel = $query_load_personel->fetch(PDO::FETCH_ASSOC);
							$conn30=null;
							$penandatangan[$key] = array('nama_lengkap'=>$personel['nama_lengkap'], 'jabatan'=>$personel['jabatan']);
					} catch (PDOException $e) {
							print "Error!: " . $e->getMessage() . "<br/>";
					    	die();
					}
				}
			}

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
			font-size: 13px;
			margin-bottom: 50px;
		}
		th {
			background-color: gray;
		}

		.risk-detail-th {
			background-color: lightgray;
		}

		th, td {
			padding: 8px;
		}

		.new-page {
			page-break-before: always;
		}

	</style>
</head>
<body>
	<h3><center>Daftar Risiko</center></h3>
	<p>
		Tahun : <?=date('Y', time())?><br>
		Satuan/Unit Kerja: Direktorat Pengawasan Peredaran Pangan Olahan<br>
		Tanggal : <?=date('d F Y', time())?>
	</p>
	<?php foreach($sasaran as $s):?>
		<?php $counter_risk=1;?>
		<div style="margin-bottom: 4px;">Sasaran Strategis: <?=$s['deskripsi']?></div>
		<!-- load tabel risiko -->
		<?php 
			try {
					$con_pdf = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
					$sql = 'select risiko.proses_bisnis, risiko.risk_event, risiko.penyebab_risiko, risiko.sumber_risiko, risiko.potensi_kerugian, risiko.pemilik_risiko, risiko.unit_terkait,
						 	risiko.kode_risiko_id, kategori.deskripsi as kategori_deskripsi, risiko.risiko_inheren_kemungkinan,
							risiko.risiko_inheren_dampak, risiko.risiko_inheren_level,
							pengendalian.aktivitas_pengendalian, pengendalian.atribut_pengendalian, pengendalian.penilaian_kelemahan_pengendalian,
							pengendalian.simpulan_efektivitas_pengendalian, pengendalian.risiko_residual_kemungkinan,
							pengendalian.risiko_residual_dampak, pengendalian.risiko_residual_level
							from oop_risk_register risiko  
							left join oop_kategori_risiko kategori on risiko.kategori_risiko_id = kategori.id
							left join 
							(select risiko_id, aktivitas_pengendalian, atribut_pengendalian,
							uraian_ketidaksesuaian, penilaian_kelemahan_pengendalian, 
							simpulan_efektivitas_pengendalian, risiko_residual_kemungkinan,
							risiko_residual_dampak, risiko_residual_level
							from oop_pengendalian_reviu_dokumen where isaftermitigasi = false

							union

							select risiko_id, aktivitas_pengendalian, atribut_pengendalian,
							uraian_ketidaksesuaian, penilaian_kelemahan_pengendalian, 
							simpulan_efektivitas_pengendalian, risiko_residual_kemungkinan,
							risiko_residual_dampak, risiko_residual_level
							from oop_pengendalian_wawancara where isaftermitigasi = false) as pengendalian on risiko.id = pengendalian.risiko_id

							where risiko.sasaran_id = :sasaran_id';
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
		<table border="1" width="100%">
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
						<td align="right"><?=$risk['kode_risiko_id']?></td>
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
										<th colspan="3" class="risk-detail-th">Risiko Inheren</th>
										<th colspan="4" class="risk-detail-th">Aktivitas Pengendalian Saat Ini</th>
										<th colspan="3" class="risk-detail-th">Risiko Residual</th>
									</tr>
									<tr>
										<th class="risk-detail-th">Kemungkinan</th>
										<th class="risk-detail-th">Dampak</th>
										<th class="risk-detail-th">Level</th>
										<th class="risk-detail-th">Aktivitas</th>
										<th class="risk-detail-th">Atribut</th>
										<th class="risk-detail-th">Penilaian Kelemahan</th>
										<th class="risk-detail-th">Simpulan Efektivitas</th>
										<th class="risk-detail-th">Kemungkinan</th>
										<th class="risk-detail-th">Dampak</th>
										<th class="risk-detail-th">Level</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td align="right"><?=$risk['risiko_inheren_kemungkinan']?></td>
										<td align="right"><?=$risk['risiko_inheren_dampak']?></td>
										<td align="right"><?=$risk['risiko_inheren_level']?></td>
										<td><?=$risk['aktivitas_pengendalian']?></td>
										<td><?=$risk['atribut_pengendalian']?></td>
										<td><?=$risk['penilaian_kelemahan_pengendalian']?></td>
										<td><?=$risk['simpulan_efektivitas_pengendalian']?></td>
										<td align="right"><?=$risk['risiko_residual_kemungkinan']?></td>
										<td align="right"><?=$risk['risiko_residual_dampak']?></td>
										<td align="right"><?=$risk['risiko_residual_level']?></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<?php $counter_risk++;?>
				<?php endforeach;?>
				<?php endif;?>
			</tbody>
		</table>
	<?php endforeach;?>

	<table width="100%">
		<tbody>
			<tr>
				<td style="padding: 0">Disusun oleh :</td>
				<td style="padding: 0"><?=$penandatangan['penyusun']['nama_lengkap']?></td>
				<td style="padding: 0">Disetujui oleh :</td>
				<td style="padding: 0"><?=$penandatangan['penyetuju']['jabatan']?></td>
			</tr>
			<tr>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
			</tr>
			<tr>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
			</tr>
		</tbody>
	</table>


	<h3 class="new-page"><center>Rencana Tindak Pengendalian</center></h3>
	<p>
		Tahun : <?=date('Y', time())?><br>
		Satuan/Unit Kerja: Direktorat Pengawasan Peredaran Pangan Olahan<br>
		Tanggal : <?=date('d F Y', time())?>
	</p>
	<!-- load tabel mitigasi -->
	<?php  
		try {
				$con_pdf = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$sql = 'select risiko.kode_risiko_id, risiko.kategori_risiko_id, risiko.risk_event, risiko.penyebab_risiko, risiko.sumber_risiko,
						mitigasi.respon_risiko, mitigasi.deskripsi_tindakan_mitigasi, mitigasi.pic, mitigasi.kebutuhan_sumber_daya,
						mitigasi.target_waktu_selesai, mitigasi.mitigasi_kemungkinan, mitigasi.mitigasi_dampak, mitigasi.mitigasi_level,
						mitigasi.uraian_target
						from oop_mitigasi_risiko mitigasi
						left join oop_risk_register risiko on mitigasi.risiko_id = risiko.id
						order by mitigasi.id asc';
				$query = $con_pdf->prepare($sql);
				$query->execute();
				$all_mitigasi = $query->fetchAll(PDO::FETCH_ASSOC);
				$con_pdf=null;
		} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
		    	die();
		}
	?>
	<table border="1" width="100%">
		<thead>
			<tr>
				<th rowspan="2">No.</th>
				<th rowspan="2">Kode Risiko</th>
				<th rowspan="2">Risk Event</th>
				<th rowspan="2">Penyebab Risiko</th>
				<th rowspan="2">Sumber Risiko</th>
				<th colspan="5">Mitigasi</th>
				<th colspan="3">Risiko Mitigasi</th>
				<th rowspan="2">Uraian Target</th>
			</tr>
			<tr>
				<th>Respon Risiko</th>
				<th>Deskripsi Tindakan Mitigasi</th>
				<th>PIC</th>
				<th>Sumber daya</th>
				<th>Target Waktu Penyelesaian</th>
				<th>Kemungkinan</th>
				<th>Dampak</th>
				<th>Level Risiko</th>
			</tr>
		</thead>
		<tbody>
			<?php $count_mitigasi = 1;?>
			<?php foreach($all_mitigasi as $mitigasi):?>
				<tr>
					<td><?=$count_mitigasi?></td>
					<td><?=$mitigasi['kode_risiko_id']?></td>
					<td><?=$mitigasi['risk_event']?></td>
					<td><?=$mitigasi['penyebab_risiko']?></td>
					<td><?=$mitigasi['sumber_risiko']?></td>
					<td><?=$mitigasi['respon_risiko']?></td>
					<td><?=$mitigasi['deskripsi_tindakan_mitigasi']?></td>
					<td><?=$mitigasi['pic']?></td>
					<td><?=$mitigasi['kebutuhan_sumber_daya']?></td>
					<td><?=date('d F Y', $mitigasi['target_waktu_selesai'])?></td>
					<td><?=$mitigasi['mitigasi_kemungkinan']?></td>
					<td><?=$mitigasi['mitigasi_dampak']?></td>
					<td><?=$mitigasi['mitigasi_level']?></td>
					<td><?=$mitigasi['uraian_target']?></td>
				</tr>
				<?php $count_mitigasi++;?>
			<?php endforeach;?>
		</tbody>
	</table>
	<table width="100%">
		<tbody>
			<tr>
				<td style="padding: 0">Disusun oleh :</td>
				<td style="padding: 0"><?=$penandatangan['penyusun']['nama_lengkap']?></td>
				<td style="padding: 0">Diperiksa oleh :</td>
				<td style="padding: 0"><?=$penandatangan['pemeriksa']['nama_lengkap']?></td>
				<td style="padding: 0">Disetujui oleh :</td>
				<td style="padding: 0"><?=$penandatangan['penyetuju']['jabatan']?></td>
			</tr>
			<tr>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
			</tr>
			<tr>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
			</tr>
		</tbody>
	</table>

	<h3 class="new-page"><center>Pemantauan / Reviu Risiko</center></h3>
	<p>
		Tahun : <?=date('Y', time())?><br>
		Satuan/Unit Kerja: Direktorat Pengawasan Peredaran Pangan Olahan<br>
		Tanggal : <?=date('d F Y', time())?>
	</p>
	<!-- load tabel mitigasi -->
	<?php  
		try {
				$con_pdf = new PDO('pgsql:host=localhost;port=5432;dbname=oop;user=jerry;password=heliumvoldo');
				$sql = 'select risiko.kode_risiko_id, risiko.risk_event, mitigasi.respon_risiko, 
						mitigasi.deskripsi_tindakan_mitigasi, mitigasi.pic, mitigasi.kebutuhan_sumber_daya,
						mitigasi.target_waktu_selesai, mitigasi.uraian_target, monitoring.uraian_progress, monitoring.epoch_pemantauan, pengendalian.*
						from oop_mitigasi_risiko mitigasi
						left join oop_risk_register risiko on mitigasi.risiko_id = risiko.id
						left join oop_monitoring_risiko monitoring on mitigasi.risiko_id = monitoring.risiko_id
						left join 
						(
							select id, risiko_id, aktivitas_pengendalian, atribut_pengendalian,
							uraian_ketidaksesuaian, penilaian_kelemahan_pengendalian, 
							simpulan_efektivitas_pengendalian, risiko_residual_kemungkinan,
							risiko_residual_dampak, risiko_residual_level
							from oop_pengendalian_reviu_dokumen where isaftermitigasi = true

							union

							select id, risiko_id, aktivitas_pengendalian, atribut_pengendalian,
							uraian_ketidaksesuaian, penilaian_kelemahan_pengendalian, 
							simpulan_efektivitas_pengendalian, risiko_residual_kemungkinan,
							risiko_residual_dampak, risiko_residual_level
							from oop_pengendalian_wawancara where isaftermitigasi = true
						) as pengendalian on monitoring.pengendalian_id = pengendalian.id
						order by mitigasi.id asc';
				$query = $con_pdf->prepare($sql);
				$query->execute();
				$mon_mitigasi = $query->fetchAll(PDO::FETCH_ASSOC);
				$con_pdf=null;
		} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
		    	die();
		}
	?>
	<table border="1">
		<thead>
			<tr>
				<th rowspan="3">No.</th>
				<th rowspan="3">Kode Risiko</th>
				<th rowspan="3">Risk Event</th>
				<th colspan="2">Risk Treatment</th>
				<th colspan="7">Monitoring</th>
			</tr>
			<tr>
				<th rowspan="2">Respon Risiko</th>
				<th rowspan="2">Deskripsi Tindakan Mitigasi</th>
				<th colspan="3">Rencana Mitigasi</th>
				<th colspan="4">Hasil Pemantauan</th>
			</tr>
			<tr>
				<th>Uraian Target</th>
				<th>Due Date</th>
				<th>PIC</th>
				<th>Progress</th>
				<th>Date</th>
				<th>Penilaian Kelemahan Pengendalian</th>
				<th>Simpulan Efektivitas Pengendalian</th>
			</tr>
		</thead>
		<tbody>
			<?php $count_mon_mitigasi = 1;?>
			<?php foreach($mon_mitigasi as $monitoring):?>
				<tr>
					<td><?=$count_mon_mitigasi?></td>
					<td><?=$monitoring['kode_risiko_id']?></td>
					<td><?=$monitoring['risk_event']?></td>
					<td><?=$monitoring['respon_risiko']?></td>
					<td><?=$monitoring['deskripsi_tindakan_mitigasi']?></td>
					<td><?=$monitoring['uraian_target']?></td>
					<td><?=date('d F Y', $monitoring['target_waktu_selesai'])?></td>
					<td><?=$monitoring['pic']?></td>
					<td><?=(!empty($monitoring['uraian_progress']) ? $monitoring['uraian_progress'] : 'belum ada')?></td>
					<td><?=(!empty($monitoring['epoch_pemantauan']) ? date('d F Y', $monitoring['epoch_pemantauan']) : '')?></td>
					<td><?=$monitoring['penilaian_kelemahan_pengendalian']?></td>
					<td><?=$monitoring['simpulan_efektivitas_pengendalian']?></td>

				</tr>

			<?php $count_mon_mitigasi++;?>
			<?php endforeach;?>
		</tbody>
	</table>
	<table width="100%">
		<tbody>
			<tr>
				<td style="padding: 0">Disusun oleh :</td>
				<td style="padding: 0"><?=$penandatangan['penyusun']['nama_lengkap']?></td>
				<td style="padding: 0">Diperiksa oleh :</td>
				<td style="padding: 0"><?=$penandatangan['pemeriksa']['nama_lengkap']?></td>
				<td style="padding: 0">Disetujui oleh :</td>
				<td style="padding: 0"><?=$penandatangan['penyetuju']['jabatan']?></td>
			</tr>
			<tr>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
				<td style="padding: 0">Tanggal : </td>
				<td style="padding: 0"><?=date('d F Y', time())?></td>
			</tr>
			<tr>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
				<td style="padding: 0"></td>
			</tr>
		</tbody>
	</table>
</body>
</html>

<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf->shrink_tables_to_fit = 1;
$mpdf->WriteHTML($html);
$mpdf->Output();
?>