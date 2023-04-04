<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$hardcode_span = -305;
$error = false;
$row = array();
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$prep_stmt1 = "SELECT a.applid,a.applDocId,a.applContact,a.applComplete,a.applApproveDate,a.applProgress,a.applFostAdopt,a.applSource,a.applApproved,
				a.applStatus,a.applState,a.fk_PeopleT_id,a.applInTake_fk_PeopleT,a.applHV_fk_PeopleT,a.applAppr_fk_PeopleT,DATE_FORMAT(a.applDateTime,'%m/%d/%Y') AS applDateTime,
				DATE_FORMAT(a.applApproveDate,'%m/%d/%Y') AS applApproveDate,a.applNote,
				b.PeopleT_id as rkey,b.Member_id as rid,b.FirstName as rfn,b.LastName as rln,
				c.PeopleT_id as hvkey,c.Member_id as hvid,c.FirstName as hvfn,c.LastName as hvln,
				d.PeopleT_id as apprkey,d.Member_id as apprid,d.FirstName as apprfn,d.LastName as apprln,
				e.PeopleT_id as intakekey,e.Member_id as intakeid,e.FirstName as intakefn,e.LastName as intakeln
				FROM Applications AS a 
				LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) 
				LEFT JOIN PeopleT AS c ON (c.PeopleT_id=a.applHV_fk_PeopleT)
				LEFT JOIN PeopleT AS d ON (d.PeopleT_id=a.applAppr_fk_PeopleT) 	
				LEFT JOIN PeopleT AS e ON (e.PeopleT_id=a.applInTake_fk_PeopleT)
				WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt1)) {
		$row = myFetch($stmt);
		$applProgress = json_decode($row[0]['applProgress']);
		$contact = json_decode($row[0]['applContact']);
		$addr = $contact->{'Addr'} == '' ? ' ' : $contact->{'Addr'} . ', ' . $contact->{'City'} . ', ' . $contact->{'St'} . ' ' . $contact->{'Zip'};
		$source = json_decode($row[0]['applSource']);
		$rname = $row[0]['fk_PeopleT_id'] == '0' ? '' : $row[0]['rln'] . ', ' . $row[0]['rfn'];
		$hvname = $row[0]['applHV_fk_PeopleT'] == '0' ? '' : $row[0]['hvln'] . ', ' . $row[0]['hvfn'];
		$itname = $row[0]['applInTake_fk_PeopleT'] == '0' ? '' : $row[0]['intakeln'] . ', ' . $row[0]['intakefn'];
		$apprname = $row[0]['applAppr_fk_PeopleT'] == '0' ? '' : $row[0]['apprln'] . ', ' . $row[0]['apprfn'];
		$prep_stmt = "SELECT DogProcStatus, Count(*) AS Qty FROM DogT WHERE fk_applid = $id GROUP BY DogProcStatus";
		$dogQty = 0;
		if ($stmt = $mysqli->query($prep_stmt)) {
			$dogCnt = new StdClass();
			while ($rowx = $stmt->fetch_assoc()) {
				$dogCnt->$rowx['DogProcStatus'] = $rowx['Qty'];
				$dogQty = $dogQty + $rowx['Qty'];
			}
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM FileAsset WHERE fk_applid = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$mediaCnt = $stmt->fetch_row();
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM ContactLog WHERE fk_applid = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$contactCnt = $stmt->fetch_row();
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM DStatusTrackT WHERE fk_applid = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$statusCnt = $stmt->fetch_row();
		}
	} else {
		$error = true;;
	}
} else {
	$error = true;;
}
function icon($choice)
{
	echo $choice == 'N' ? 'action' : 'check';
}
function iconAppr($choice)
{
	echo ($choice == 'N') ? 'delete' : (($choice == 'Y') ? 'check' : 'action');
}
function msgAppr($choice)
{
	echo ($choice == 'N') ? 'Not approved-' : (($choice == 'Y') ? 'Approved-' : '');
}
function colorAppr($choice)
{
	echo ($choice == 'N') ? '#FF7171' : (($choice == 'Y') ? 'darkgreen' : '');
}
function hideShow($choice)
{
	if ($choice) {
		echo 'style="display:none"';
	} else {
		echo 'style="display:inherit"';
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<?php
	var_dump($prep_stmt1);
	?>
</head>

<body>
	<div data-role="page" id="adoptPg" data-applid='<?php echo $id; ?>'>
		<script>
			var applStatus = '<?php echo $row[0]['applStatus']; ?>';
		</script>
		<div data-role="header" data-position="fixed">
			<img src="../img/SAGGR-DBLogo1hdr-320.gif">
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="backBtn"><a href="#" data-icon="back">Back</a></li>
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
						<p>Could not access the SAGRR Database, try again later </p>
					</div>
				</div>
				<?php
			} else {
				if ($id == 0) {
				?>
					<div class="ui-corner-all">
						<div class="ui-bar ui-bar-a">
							<h3>No Family has been assigned.</h3>
						</div>
						<div class="ui-body ui-body-a">
							<p>A family has not been assigned for this foster or adoption. </p>
						</div>
					</div>
				<?php
				} else {
				?>
					<ul data-role="listview" id="adopt_info" data-autodividers="false" data-filter="false">
						<li data-role="list-divider" role="heading" style="font-size:initial;">
							<h3>Family: <?php echo $row[0]['applid']; ?>&nbsp;&nbsp;&nbsp;Dated:&nbsp;<?php echo $row[0]['applDateTime']; ?><br>Status: <?php echo $row[0]['applState'] . ' &#187 ' . $row[0]['applStatus']; ?></h3>
						</li>
						<li data-icon="<?php icon($applProgress[0]->{'Comp'}); ?>"><a href="/assets/pages/adoptInfo.php?x=<?php echo $id; ?>&n=<?php echo $contact->{'FName'}; ?>" data-transition="slide" style="padding:0.5em 1em;">
								<table style="border-spacing:0;width:100%;font-weight:400">
									<tbody>
										<tr>
											<td style="font-weight:600"><?php echo $contact->{'LName'} . ', ' . $contact->{'FName'}; ?></td>
										</tr>
										<tr>
											<td><?php echo $addr; ?></td>
										</tr>
										<tr>
											<td>Email: <?php echo $contact->{'Email'} ?></td>
										</tr>
										<tr>
											<td>Phone: <?php echo $contact->{'Phone'} ?></td>
									</tbody>
								</table>
								<p><?php echo $row[0]['applNote']; ?></p>
							</a></li>
						<?php if ($row[0]['fk_PeopleT_id'] == 0) { ?>
							<li id="showMemSpawn" class="ApplE" style="display:none">
								<form id="memSpawn" class="forms">
									<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
										    <legend>Create or Link Membership</legend>
										    <input type="radio" name="NoMembership" id="spawnMembership" value="spawn">
										    <label for="spawnMembership">Create New</label>
										    <input type="radio" name="NoMembership" id="linkMembership" value="link">
										    <label for="linkMembership">Link Existing</label>
									</fieldset>
								</form>
							</li>
						<?php } ?>
						<li <?php hideShow($row[0]['fk_PeopleT_id'] == 0); ?> id="showMemberRcd">
							<a href="/assets/pages/member.php?x=<?php echo $row[0]['fk_PeopleT_id'] ?>&n=&l=&q=" data-transition="slide" style="padding:0.5em 1em;" id="memberRcd">
								Member Record: <?php echo $rname ?></a>
						</li>
						<li data-icon="<?php icon($row[0]['applComplete']); ?>"><a href="/assets/pages/adoptAppl.php?x=<?php echo $id ?>&n=<?php echo $contact->{'LName'} ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Application
								<span>
									<?php
									if ($row[0]['applComplete'] == 'N') {
										echo '<span class="myErrMsg">&nbsp;&nbsp;Incomplete!</span>';
									}
									?>
								</span></a>
						</li>
						<li data-icon="<?php iconAppr($row[0]['applApproved']); ?>"><a href="/assets/pages/adoptApprove.php?x=<?php echo $id; ?>&n=&l=&q=" data-transition="slide" style="padding:0.5em 1em;">
								Approval: <span style="font-size:smaller;color:<?php colorAppr($row[0]['applApproved']) ?> "><?php msgAppr($row[0]['applApproved']);
																															echo $row[0]['applApproveDate'] ?></span></a>
						</li>
						<li><a href="/assets/pages/member.php?x=<?php echo $row[0]['applHV_fk_PeopleT'] ?>&n=&l=&q=" data-transition="slide" style="padding:0.5em 1em;">
								Home Visit: <?php echo $hvname ?></a>
						</li>
						<li><a href="/assets/pages/adoptStatus.php?x=<?php echo $id ?>&n=<?php echo $contact->{'FName'} ?>&l=&q=<?php echo $row[0]['applStatus']; ?>&s=<?php echo $row[0]['applState']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Status Update and History<span class="ui-li-count"><?php echo $statusCnt[0] ?></span></a>
						</li>
						<li><a href="/assets/pages/mediaList.php?x=0&m=appl&v=0&d=&r=&f=<?php echo $id ?>&s=s" data-transition="slide" style="padding:0.5em 1em;">
								Media Attachments<span class="ui-li-count"><?php echo $mediaCnt[0] ?></span></a>
						</li>
						<li><a href="/assets/pages/memContact.php?x=<?php echo $id ?>&n=<?php echo $contact->{'FName'} ?>&m=appl&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Contact Log<span class="ui-li-count"><?php echo $contactCnt[0] ?></span></a>
						</li>
						<li><a href="/assets/pages/adoptDog.php?x=1&n=<?php echo $contact->{'FName'} ?>&l=okt&q=<?php echo $id ?>&s=<?php echo $row[0]['applState']; ?>&a=<?php echo $dogQty; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Trial Match<span class="ui-li-count"><?php echo $dogCnt->{'Match-Trial'}; ?></span></a>
						</li>
						<li><a href="/assets/pages/adoptDog.php?x=1&n=<?php echo $contact->{'FName'} ?>&l=okf&q=<?php echo $id ?>&s=<?php echo $row[0]['applState']; ?>&a=<?php echo $dogQty; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Fostered Dogs<span class="ui-li-count"><?php echo $dogCnt->{'Fostered'}; ?></span></a>
						</li>
						<li><a href="/assets/pages/adoptDog.php?x=1&n=<?php echo $contact->{'FName'} ?>&l=oka&q=<?php echo $id ?>&s=<?php echo $row[0]['applState']; ?>&a=<?php echo $dogQty; ?>" data-transition="slide" style="padding:0.5em 1em;">
								Adopted Dogs<span class="ui-li-count"><?php echo $dogCnt->{'Adopted'}; ?></span></a>
						</li>
					</ul>
			<?php
				}
			}
			?>
		</div><!-- /content -->
		<div data-role="footer" data-position="fixed">
			<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
		</div><!-- /footer -->

		<div data-role="popup" id="memSpawnConfirm" data-dismissible="true" data-theme="a">
			<div data-role="header" data-theme="a">
				<h1>Please confirm Membership</h1>
			</div>
			<div data-theme="a" style="margin:5px;">
				<p>Please confirm the creation of a Membership record for this applicant.</p>
				<p>You have instructed that a Membership record be created for this applicant and set to Pending status.</p>
				<p>This action cannot be undone.</p>
				<div data-role="navbar" data-iconpos="left" data-theme="b">
					<ul>
						<li class="myNav"><a href="#" data-icon="delete" data-rel="back" class="myNav">Cancel</a></li>
						<li class="myNav confMemSpawn"><a href="#" data-icon="check" id="memSpawnBtn" class="myNav confMemSpawn">Confirm</a></li>
					</ul>
				</div><!-- /navbar -->
			</div>
		</div> <!-- /popup -->


	</div><!-- /page -->

</body>

</html>