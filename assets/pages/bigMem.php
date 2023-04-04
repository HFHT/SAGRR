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
    $prep_stmt = "SELECT PeopleT_id,Member_id,FirstName,LastName,Title,Salutation,Email,Phone,Cell,Fax,Address,City,StateProvince,MemberLevel,".
				"Country,MailCode,Company,WorkPhone,WorkEmail,Contact,Vet,Membership,MemCount,MemberBlob,Deleted,Interests,Teams,MemberType,PhotoLink,OtherName,".
				"DATE_FORMAT(MemberSince,'%m/%d/%Y') AS MemberSince,DATE_FORMAT(MemberRenewed,'%m/%d/%Y') AS MemberRenewed, ".
				"DATEDIFF(MemberRenewed, NOW()) AS MemberExpire ".
				"FROM PeopleT WHERE Deleted <> 'Y'";
	if (!$stmt = $mysqli->query($prep_stmt)) {
		$error = true;
	}
} else {
	$error = true;
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="memberBig" class="my-ui-page" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" class="my-ui-page" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li><a href="#memberMenu" data-icon="back" class="myNav" >Back</a></li>	
				<li><a href="#home" data-icon="home">Home</a></li>	
				<?php if ($error) { ?>
				<li>&nbsp;</li>
				<?php } else { ?>
				<li id="dogDownx"><a href="/assets/code/csvMem.php?x=1" data-icon="arrow-d" data-ajax="false" class="myNav">Download</a></li>
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
	
		<table style="font-size:small;" id="bigMemTbl" class="tablesorter">
			<thead class="my-tblhdr-fixed">
				<tr>
					<th>No.</th>
					<th>Id</th>
					<th>Since</th>
					<th>Renewed</th>
					<th>Qty</th>
					<th>Name</th>
					<th>Address</th>
					<th>City,St,Zip</th>
					<th>Phone</th>
					<th>Cell</th>
					<th>Email</th>
					<th>Membership</th>
					<th>Interests</th>
					<th>Teams</th>
					<th>Skills</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$result = $mysqli->query("SELECT * FROM SIGroupT");
					$aryInt = array();
					while ($rcd = $result->fetch_assoc()) {
						$aryInt[$rcd['SelId']] =  preg_replace('/\s+/', '',$rcd['SelText']);
					}
					while ($rcd = $stmt->fetch_assoc()) {
						$int = json_decode($rcd['Interests']);
						$tms = json_decode($rcd['Teams']);
						$interest = '';
						foreach ($int->{'Interests'} as $in) {
							$interest = $interest.$aryInt[$in].' ';
						}
						$team = '';
						foreach ($tms->{'Interests'} as $in) {
							$team = $team.$aryInt[$in].' ';
						}
						if ($rcd['OtherName']=='') {
							$nm = $rcd['LastName'].', '.$rcd['FirstName'];
						} else {
							$nm = $rcd['LastName'].', '.$rcd['FirstName'].' ('.$rcd['OtherName'].')';
						}
						
				?>
						<tr>
							<td><a href="/assets/pages/member.php?x=<?php echo $rcd['PeopleT_id']; ?>&l=&q=" style="" data-transition="slide"><?php echo $rcd['PeopleT_id'];?></a></td>
							<td><?php echo $rcd['Member_id'];?></td>
							<td><?php echo $rcd['MemberSince'];?></td>
							<td><?php echo $rcd['MemberRenewed'];?></td>
							<td><?php echo $rcd['MemCount'];?></td>
							<td><?php echo $nm;?></td>
							<td><?php echo $rcd['Address'];?></td>
							<td><?php echo $rcd['City']==''?'':$rcd['City'].', '.$rcd['StateProvince'].' '.$rcd['MailCode'];?></td>
							<td nowrap><?php echo $rcd['Phone'];?></td>
							<td nowrap><?php echo $rcd['Cell'];?></td>
							<td><?php echo $rcd['Email'];?></td>
							<td><?php echo $rcd['Membership'].':'.$rcd['MemberLevel'];?></td>
							<td><?php echo $interest;?></td>
							<td><?php echo $team;?></td>
							<td><?php echo $rcd['MemberBlob'];?></td>
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