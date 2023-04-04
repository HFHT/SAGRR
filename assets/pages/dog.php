<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$hardcode_span = 60;
$error = false;
$row = array();
$visitCnt = $statusCnt = $medicalCnt = 'x';
if ($_GET['x'] != '') {
	$id = $_GET['x'];
	$prep_stmt = "SELECT DogT_id,SAGRR_id,fk_DStatusTrackT_id,DogName,FormerName,Age_YY,Age_MM,Weight,AKC_id,DMChipData,DMChipMfg_id," .
		"fk_BreedT_id,a.fk_PeopleT_id,fk_ColorT_id,DogSex,AlteredBy,DateLastChanged,fk_SourceT_id,DogBlob,DogProcStatus," .
		"DogCurStatus,DogPhotoLink,DogCurMember,Behaviors,fk_applid,Bridge,NCI,InTake_fk_PeopleT," .
		"DATE_FORMAT(InTakeDate,'%m/%d/%Y') AS InTakeDate," .
		"DATE_FORMAT(Birthdate,'%m/%d/%Y') AS BirthDate," .
		"DATE_FORMAT(DeathDate,'%m/%d/%Y') AS DeathDate," .
		"DATE_FORMAT(AlteredDate,'%m/%d/%Y') AS AlteredDate," .
		"DATEDIFF(Rabies, NOW()) AS Rabies," .
		"DATEDIFF(DA2PP_DHLPP, NOW()) AS DA2PP_DHLPP," .
		"DATEDIFF(Bordetella, NOW()) AS Bordetella," .
		"DATEDIFF(Leptospirosis, NOW()) AS Leptospirosis," .
		"(PERIOD_DIFF( DATE_FORMAT(NOW(), '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) DIV 12 AS ageY," .
		"(PERIOD_DIFF( DATE_FORMAT(NOW(), '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) MOD 12 AS ageM," .
		"(PERIOD_DIFF( DATE_FORMAT(DeathDate, '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) DIV 12 AS lifeY," .
		"(PERIOD_DIFF( DATE_FORMAT(DeathDate, '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) MOD 12 AS lifeM," .
		"b.DogS_Name, c.applContact, d.SelText AS color " .
		"FROM DogT AS a " .
		"LEFT JOIN DogSources AS b ON b.DogS_id=a.fk_SourceT_id " .
		"LEFT JOIN Applications AS c ON c.applid=a.fk_applid " .
		"LEFT JOIN ColorT AS d ON d.SelId=a.fk_ColorT_id " .
		"WHERE a.DogT_id = $id";
	var_dump($prep_stmt);
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		var_dump($row);
		$beh = json_decode($row[0]['Behaviors']);
		$applContact = json_decode($row[0]['applContact']);
		$family = $applContact->{'LName'} == '' ? '' : $applContact->{'LName'} . ', ' . $applContact->{'FName'};
		$dogSource = $row[0]['fk_SourceT_id'] == '0' ? '<span class="myErrMsg">Please Select Intake Source</span>' : $row[0]['DogS_Name'];
		$prep_stmt = "SELECT Count(*) AS Qty FROM VetVisit WHERE fkV_DogT_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$visitCnt = $stmt->fetch_row();
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM DStatusTrackT WHERE fk_DogT_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$statusCnt = $stmt->fetch_row();
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM ContactLog WHERE fk_DogT_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$contactCnt = $stmt->fetch_row();
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM FileAsset WHERE Fk_Dog_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$mediaCnt = $stmt->fetch_row();
		}
		if ($row[0]['InTake_fk_PeopleT'] != 0) {
			$iid = $row[0]['InTake_fk_PeopleT'];
			$prep_stmt = "SELECT FirstName,LastName FROM PeopleT WHERE PeopleT_id = $iid";
			if ($stmt = $mysqli->query($prep_stmt)) {
				$intakeV = myFetch($stmt);
			}
		} else {
			$intakeV[0]['FirstName'] = '';
			$intakeV[0]['LastName'] = '';
		}
	} else {
		$error = true;
	}
} else {
	$error = true;
}
function ckVaccine($d1, $d2, $d3, $d4, $span)
{
	$d1 = $d1 == '' ? date("Y-m-d") : $d1;
	$d2 = $d2 == '' ? date("Y-m-d") : $d2;
	$d3 = $d3 == '' ? date("Y-m-d") : $d3;
	$d4 = $d4 == '' ? date("Y-m-d") : $d4;
	return (($d1 - $span < 0) or ($d2 - $span < 0) or ($d3 - $span < 0) or ($d4 - $span < 0));
}
function whichClass($b, $n)
{
	if ($b == 'Y') {
		echo 'rainbowBridge';
	} else {
		if ($n == 'Y') {
			echo 'NCI';
		} else {
			echo '';
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<?php
	var_dump($intakeV);
	?>
</head>

<body>
	<div data-role="page" id="dogPage">
		<script>
		</script>
		<div data-role="header" data-position="fixed">
			<img src="../img/SAGGR-DBLogo1hdr-320.gif">
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<?php if ($_GET['s'] == 'i') { ?>
						<li><a href="#dogActiveMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
					<?php } else { ?>
						<li class="backBtn"><a href="#" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
					<?php } ?>
					<li><a href="#home" data-icon="home">Home</a></li>
					<li>&nbsp;</li>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /header -->

		<div data-role="main" class="ui-content">
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
				<ul data-role="listview" id="dogview" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
					<li data-icon="edit"><a href="/assets/pages/dogInfo.php?x=<?php echo $id ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>&n=<?php echo $row[0]['DogName'] ?>" data-transition="slide" style="padding:0.5em 1em;">
							<table class="<?php whichClass($row[0]['Bridge'], $row[0]['NCI']); ?>" width="100%">
								<tbody>
									<tr>
										<td>
											<table style="border-spacing:0;width:100%;font-weight:400;border-bottom:1pt dotted rgb(196,196,196)">
												<tbody>
													<tr>
														<td style="font-weight:600"><?php echo $row[0]['DogName'] ?>:&nbsp;&nbsp; # <?php echo $row[0]['SAGRR_id'] ?></td>
														<td rowspan="4">
															<?php if (isset($row[0]['DogPhotoLink'])) { ?>
																<img src="<?php echo $row[0]['DogPhotoLink'] ?>" />
															<?php } else { ?>
																<img src="/assets/img/grstock.png" />
															<?php } ?>
														</td>
													</tr>
													<tr>
														<td style="font-weight:600"><?php echo $row[0]['DogProcStatus'] ?>:&nbsp; <?php echo $row[0]['DogCurStatus'] ?></td>
													</tr>
													<tr>
														<td>
															<?php if ($row[0]['DogProcStatus'] ==  'Deceased' and isset($row[0]['lifeY'])) { ?>
																Life Span: <?php echo $row[0]['lifeY'] . 'Y ' . $row[0]['lifeM'] . 'M' ?>
															<?php } ?>
															<?php if ($row[0]['DogProcStatus'] !=  'Deceased') { ?>
																Age: <?php if (isset($row[0]['ageY'])) {
																			echo $row[0]['ageY'] . 'Y ' . $row[0]['ageM'] . 'M';
																		} ?>
															<?php } ?>
														</td>
													</tr>
													<tr>
														<td>Sex: <?php echo $row[0]['DogSex'] ?></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table style="border-spacing:0;width:100%;font-weight:400;">
												<tbody>
													<?php if ($row[0]['FormerName'] != '') { ?>
														<tr>
															<td>Former Name:</td>
															<td><?php echo $row[0]['FormerName'] ?></td>
														</tr>
													<?php } ?>
													<tr>
														<td>Intake Date:</td>
														<td><?php echo $row[0]['InTakeDate'] ?></td>
													</tr>
													<tr>
														<td>Weight:</td>
														<td><?php echo $row[0]['Weight'] == '0.00' ? '' : $row[0]['Weight'] ?></td>
													</tr>
													<tr>
														<td>Color:</td>
														<td><?php echo $row[0]['fk_ColorT_id'] == '0' ? '' : $row[0]['color'];  ?></td>
													</tr>
													<?php if (isset($row[0]['DeathDate'])) { ?>
														<tr>
															<td>Death Date:</td>
															<td><?php echo $row[0]['DeathDate'] ?></td>
														</tr>
													<?php } ?>
												</tbody>
											</table>
											<?php if (!$row[0]['DogBlob'] == '') { ?>
												<table style="border-spacing:0;width:100%;font-weight:400;border-top:1pt dotted rgb(196,196,196)">
													<tbody>
														<tr>
															<td><?php echo $row[0]['DogBlob']; ?></td>
														</tr>
													</tbody>
												</table>
											<?php } ?>
										</td>
									</tr>
								</tbody>
							</table>
						</a></li>
					<?php
					if ($_GET['x'] != '') {
					?>
						<li style="display:none"><a href="/assets/pages/dogIntake.php?x=<?php echo $row[0]['InTake_fk_PeopleT']; ?>&n=<?php echo $row[0]['DogName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								<span class="my-intake"><?php echo $dogSource; ?></span></a>
						</li>
						<li><a href="/assets/pages/dogBeh.php?x=<?php echo $id ?>&n=<?php echo $row[0]['DogName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Behaviors & Attributes<span class="ui-li-count"><?php echo $beh->Qty; ?></span></a>
						</li>
						<li><a href="/assets/pages/dogMedical.php?x=<?php echo $id ?>&n=<?php echo $row[0]['DogName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Medical Information
								<span>
									<?php
									if (ckVaccine($row[0]['Rabies'], $row[0]['Bordetella'], $row[0]['DA2PP_DHLPP'], $row[0]['Leptospirosis'], 0)) {
										echo '<span class="myErrMsg">&nbsp;&nbsp;Expired!</span>';
									} else {
										if (ckVaccine($row[0]['Rabies'], $row[0]['Bordetella'], $row[0]['DA2PP_DHLPP'], $row[0]['Leptospirosis'], $hardcode_span)) {
											echo '<span class="myErrMsg">&nbsp;&nbsp;Expiring!</span>';
										}
									}
									?>
								</span></a>
						</li>
						<?php if ($row[0]['fk_PeopleT_id'] == 0) { ?>
							<li><a href="/assets/pages/adopt.php?x=<?php echo $row[0]['fk_applid'] ?>&l=&q=" data-transition="slide" style="padding:0.5em 1em;">
									Family: <?php echo $family ?></a>
							</li>
						<?php } else { ?>
							<li><a href="/assets/pages/member.php?x=<?php echo $row[0]['fk_PeopleT_id'] ?>&l=&q=" data-transition="slide" style="padding:0.5em 1em;">
									Member: <?php echo $row[0]['fk_PeopleT_id'] ?></a>
							</li>
						<?php } ?>
						<li><a href="/assets/pages/member.php?x=<?php echo $row[0]['InTake_fk_PeopleT'] ?>&n=<?php echo $intakeV[0]['LastName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Intake: <?php echo $intakeV[0]['FirstName']; ?>&nbsp;<?php echo $intakeV[0]['LastName']; ?></a>
						</li>
						<li><a href="/assets/pages/dogVisit.php?x=<?php echo $id ?>&n=<?php echo $row[0]['DogName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Veterinarian Vists<span class="ui-li-count"><?php echo $visitCnt[0] ?></span></a>
						</li>
						<li><a href="/assets/pages/dogStatus.php?x=<?php echo $id ?>&n=<?php echo $row[0]['DogName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Status Update and History<span class="ui-li-count"><?php echo $statusCnt[0] ?></span></a>
						</li>
						<li><a href="/assets/pages/memContact.php?x=<?php echo $id ?>&n=<?php echo $row[0]['DogName'] ?>&m=dog&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Contact Log<span class="ui-li-count"><?php echo $contactCnt[0] ?></span></a>
						</li>
						<li><a href="/assets/pages/mediaList.php?x=0&m=dog&v=0&d=<?php echo $id ?>&r=0&s=s" data-transition="slide" style="padding:0.5em 1em;">
								Media Attachments<span class="ui-li-count"><?php echo $mediaCnt[0] ?></span></a>
						</li>
					<?php
					}
					?>
				</ul>
			<?php
			}
			?>
		</div><!-- /content -->
		<div data-role="footer" data-position="fixed">
			<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
		</div><!-- /footer -->
	</div><!-- /page -->

</body>

</html>