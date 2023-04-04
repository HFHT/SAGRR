<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$hardcode_span = 60;
$error = false;
$row = array();
$today = date("Y-m-d");
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$prep_stmt = "SELECT DogT_id,SAGRR_id,fk_DStatusTrackT_id,DogName,FormerName,Age_YY,Age_MM,Weight,AKC_id,DMChipData,DMChipMfg_id," .
		"fk_BreedT_id,a.fk_PeopleT_id,fk_ColorT_id,DogSex,AlteredBy,DateLastChanged,fk_SourceT_id,DogBlob,DogProcStatus," .
		"DogCurStatus,DogPhotoLink,DogCurMember,Behaviors,fk_applid,Medical," .
		"DATE_FORMAT(InTakeDate,'%m/%d/%Y') AS InTakeDate," .
		"DATE_FORMAT(Birthdate,'%m/%d/%Y') AS BirthDate," .
		"DATE_FORMAT(DeathDate,'%m/%d/%Y') AS DeathDate," .
		"DATE_FORMAT(AlteredDate,'%m/%d/%Y') AS AlteredDate," .
		"DATEDIFF(Rabies, NOW()) AS Rabies," .
		"DATEDIFF(DA2PP_DHLPP, NOW()) AS DA2PP_DHLPP," .
		"DATEDIFF(Bordetella, NOW()) AS Bordetella," .
		"(PERIOD_DIFF( DATE_FORMAT(NOW(), '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) DIV 12 AS ageY," .
		"(PERIOD_DIFF( DATE_FORMAT(NOW(), '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) MOD 12 AS ageM," .
		"(PERIOD_DIFF( DATE_FORMAT(DeathDate, '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) DIV 12 AS lifeY," .
		"(PERIOD_DIFF( DATE_FORMAT(DeathDate, '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) MOD 12 AS lifeM," .
		"b.DogS_Name, c.applContact, d.SelText as color, e.SelText as ChipMfg " .
		"FROM DogT AS a " .
		"LEFT JOIN DogSources AS b ON b.DogS_id=a.fk_SourceT_id " .
		"LEFT JOIN Applications AS c ON c.applid=a.fk_applid " .
		"LEFT JOIN ColorT AS d ON d.SelId=a.fk_ColorT_id " .
		"LEFT JOIN MChipMfg AS e ON e.SelId=a.DMChipMfg_id " .
		"WHERE a.Deleted <> 'Y'";
	if (!$stmt = $mysqli->query($prep_stmt)) {
		$error = true;
	}
} else {
	$error = true;
}
function ckVaccine($d1, $d2, $d3, $span)
{
	return (($d1 - $span < 0) or ($d2 - $span < 0) or ($d3 - $span < 0));
}
?>
<!DOCTYPE html>
<html>

<head>
	<?php
	var_dump($prep_stmt);
	var_dump($row);
	?>
</head>

<body>
	<div data-role="page" id="dogBig" class="my-ui-page" data-dom-cache="false">
		<script>
		</script>
		<div data-role="header" class="my-ui-page" data-position="fixed">
			<img src="../img/SAGGR-DBLogo1hdr-320.gif">
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li><a href="#dogMenu" data-icon="back" class="myNav">Back</a></li>
					<li><a href="#home" data-icon="home">Home</a></li>
					<?php if ($error) { ?>
						<li>&nbsp;</li>
					<?php } else { ?>
						<li id="dogDownx"><a href="/assets/code/csvIntake.php?x=1" data-icon="arrow-d" data-ajax="false" class="myNav">Download</a></li>
					<?php } ?>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /header -->

		<div data-role="main" class="ui-content my-narrow" style="overflow-x:scroll;">
			<?php
			if ($error) {
			?>
				<div class="ui-corner-all">
					<div class="ui-bar ui-bar-a">
						<h3>Error Occurred</h3>
					</div>
					<div class="ui-body ui-body-a">
						<p>Could not access the Dog Database, try again later </p>
					</div>
				</div>
			<?php
			} else {
			?>

				<table style="font-size:small;" id="bigDogTbl" class="tablesorter">
					<thead class="my-tblhdr-fixed">
						<tr>
							<th>No.</th>
							<th>Id</th>
							<th>Intake</th>
							<th>Status</th>
							<th>Name</th>
							<th>Sex</th>
							<th>Age</th>
							<th>Source</th>
							<th>AKC</th>
							<th>Color</th>
							<th>Weight</th>
							<th>Microchip</th>
							<th>Chip Mfg</th>
							<th>Altered</th>
							<th>Shots</th>
							<th>Medical</th>
							<th>Good w</th>
							<th>Family</th>
						</tr>
					</thead>
					<tbody>
						<?php
						while ($rcd = $stmt->fetch_assoc()) {
							$vac = '';
							if (ckVaccine($rcd['Rabies'], $rcd['Bordetella'], $rcd['DA2PP_DHLPP'], 0)) {
								$vac = 'Expired';
							} else {
								if (ckVaccine($rcd['Rabies'], $rcd['Bordetella'], $rcd['DA2PP_DHLPP'], $hardcode_span)) {
									$vac = 'Expiring';
								}
							}
							$med = json_decode($rcd['Medical']);
							$beh = json_decode($rcd['Behaviors']);
							$b = in_array('19', $beh->{'Beh'}) == true ? 'dog ' : '';
							$b = $b . (in_array('20', $beh->{'Beh'}) == true ? 'cat ' : '');
							$b = $b . (in_array('21', $beh->{'Beh'}) == true ? 'kid ' : '');
							$applContact = !empty($rcd['applContact']) ? json_decode($rcd['applContact']) : '';
							$family = $applContact->{'LName'} == '' ? '' : $applContact->{'LName'} . ', ' . $applContact->{'FName'};
						?>
							<tr>
								<td><a href="/assets/pages/dog.php?x=<?php echo $rcd['DogT_id']; ?>&l=&q=" style="" data-transition="slide"><?php echo $rcd['DogT_id']; ?></a></td>
								<td><?php echo $rcd['SAGRR_id']; ?></td>
								<td><?php echo $rcd['InTakeDate']; ?></td>
								<td><?php echo $rcd['DogProcStatus'] . ':' . $rcd['DogCurStatus']; ?></td>
								<td><?php echo $rcd['DogName']; ?></td>
								<td><?php echo $rcd['DogSex']; ?></td>
								<td><?php echo $rcd['AgeY'] . ':' . $rcd['AgeM']; ?></td>
								<td><?php echo $rcd['DogS_Name']; ?></td>
								<td><?php echo $rcd['AKC_id']; ?></td>
								<td><?php echo $rcd['color']; ?></td>
								<td><?php echo $rcd['Weight']; ?></td>
								<td><?php echo $rcd['DMChipData']; ?></td>
								<td><?php echo $rcd['ChipMfg']; ?></td>
								<td><?php echo $rcd['AlteredBy']; ?></td>
								<td><?php echo $vac; ?></td>
								<td><?php echo $med->{'MedicalClass'}; ?></td>
								<td><?php echo $b; ?></td>
								<td><?php echo $family; ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div><!-- /content -->

		<div data-role="footer" class="my-ui-page" data-position="fixed">
			<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
		</div><!-- /footer -->

	</div><!-- /page -->

</body>

</html>