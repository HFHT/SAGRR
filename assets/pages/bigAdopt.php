<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
$today = date("Y-m-d");
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$s = $_GET['s'];
	$where = ($s=='a') ? "AND a.applState='Active'" : "";
    $prep_stmt = "SELECT a.applid,a.applDocId,DATE_FORMAT(a.applDateTime,'%m/%d/%Y') AS applDateTime,a.applComplete,
				DATE_FORMAT(a.applApproveDate,'%m/%d/%Y') AS applApproveDate,a.applApproved,a.applApprovedBy,a.applFostAdopt,a.applFost,a.applAdopt,
				a.applContact,a.applSource,a.applProgress,a.applStatus,a.fk_PeopleT_id,a.applHV_fk_PeopleT,a.applAppr_fk_PeopleT,
				a.ApplSec0,a.ApplSec3,a.ApplSec4,
				b.PeopleT_id as rkey,b.Member_id as rid,b.FirstName as rfn,b.LastName as rln,
				c.PeopleT_id as hvkey,c.Member_id as hvid,c.FirstName as hvfn,c.LastName as hvln,
				d.PeopleT_id as apprkey,d.Member_id as apprid,d.FirstName as apprfn,d.LastName as apprln				
				FROM Applications AS a 
				LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) 
				LEFT JOIN PeopleT AS c ON (c.PeopleT_id=a.applHV_fk_PeopleT)
				LEFT JOIN PeopleT AS d ON (d.PeopleT_id=a.applAppr_fk_PeopleT) 				 				
				WHERE a.Deleted <> 'Y' $where";
	if (!$stmt = $mysqli->query($prep_stmt)) {
		$error = true;
	}
} else {
	$error = true;
}

function dropDown($DBConn,$DBtable,$Selected) {	
	$dropSQL = "SELECT * FROM $DBtable";
	if ($dropResp = $DBConn->query($dropSQL)) {
		echo "<option value='0'>--Select--</option>";
		while ($dropRow = $dropResp->fetch_object()) {
			if ($Selected == $dropRow->SelText) {
				$strSel = "Selected";
			} else {
				$strSel = "";
			}
			echo "<option value='".$dropRow->SelText."' ".$strSel.">".$dropRow->SelText."</option>";
		}
	} else {
		echo "<option value='0'>!!DB Error!!</option>";
	}
}
function dropDownAry($SelAry,$Selected,$SelText) {
	echo "<option value='0'>--$SelText--</option>";
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $key) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$key."' ".$strSel.">".$value."</option>";
	}
}
function phone($phoneNo) {
	if ($phoneNo != '') {
		echo '<span><a href="tel:'.$phoneNo.'"><img src="/assets/img/phoneicon.png" height="20" style="float:right;"/></a></span>';
	}
}
function email($email) {
	if ($email != '') {
		echo '<span><a href="mailto:'.$email.'"><img src="/assets/img/emailicon.png" height="20" style="float:right;"/></a></span>';
	}
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($prep_stmt);
?>
</head>
<body>
<div data-role="page" id="adoptBig" class="my-ui-page" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" class="my-ui-page" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php switch($s) { 
					case 'a':
				?>
					<li><a href="#adoptActivePage" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<?php break;
					default:
				?>
				<li><a href="#adoptPage" data-icon="back" class="myNav" >Back</a></li>	
				<?php } ?>
			
				<li><a href="#home" data-icon="home">Home</a></li>	
				<?php if ($error) { ?>
				<li>&nbsp;</li>
				<?php } else { ?>
				<li id="adoptDownx"><a href="/assets/code/csvAdopt.php?x=1&s=<?php echo $s;?>" data-icon="arrow-d" data-ajax="false" class="myNav">Download</a></li>
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
				<p>Could not access the Adoption Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
	
		<table style="font-size:small;" id="bigAdoptTbl" class="tablesorter">
			<thead class="my-tblhdr-fixed">
				<tr>
					<th></th>
					<th>Status</th>
					<th>Desire</th>
					<th>Qty</th>
					<th>Applicant</th>
					<th>City</th>
					<th>St</th>
					<th>Zip</th>
					<th>Phone</th>
					<th>Cell</th>
					<th>Submitted</th>
					<th>Approved</th>
					<th>HomeVisit</th>
					<th>Alone</th>
					<th>Mix</th>
					<th>Sex</th>
					<th>Low</th>
					<th>High</th>				
					<th>Pets</th>
					<th>Kids</th>
					<th>Special</th>
				</tr>
			</thead>
			<tbody>
				<?php
					while ($rcd = $stmt->fetch_assoc()) {
						$contact = json_decode($rcd['applContact']);
						$source = json_decode($rcd['applSource']);
						$sec0 = json_decode($rcd['ApplSec0']);
						$sec3 = json_decode($rcd['ApplSec3']);
						$sec4 = json_decode($rcd['ApplSec4']);
						if (strpos($contact->{'FName'},'FirstName')!==false) {
							continue;
						}						
						$addr = $contact->{'Addr'}==''?'{Address}':$contact->{'Addr'}.', '.$contact->{'City'}.', '.$contact->{'St'}.' '.$contact->{'Zip'};
						$fa = '';
						if ($rcd['applFost']=='Y' & $rcd['applAdopt']=='Y') {
							$fa = 'Both';
						} else {
							if ($rcd['applFost']=='Y') {
								$fa = 'Foster';
							}
							if ($rcd['applAdopt']=='Y') {
								$fa = 'Adopt';
							}
						}
						$spec = $sec3->{'Ans27'}=='Y' || $sec3->{'Ans27'}=='M' ?'Minor ':'';
						$spec = $spec.($sec3->{'Ans28'}=='Y' || $sec3->{'Ans28'}=='M' ?'Major ':'');
						$spec = $spec.($sec3->{'Ans29'}=='Y' || $sec3->{'Ans29'}=='M' ?'Treatable ':'');
						$spec = $spec.($sec3->{'Ans30'}=='Y' || $sec3->{'Ans30'}=='M' ?'Cancer ':'');
						$spec = $spec.($sec3->{'Ans31'}=='Y' || $sec3->{'Ans31'}=='M' ?'xTrained ':'');
						$kids = '';
						?>
						<?php
//						var_dump($sec0->{PetType});
						?>
						<?php
						foreach ($sec0->{'MemAge'} as $nm) {
							if ($nm<18) {
								$kids = $kids.$nm.' ';
							}
						}
						$pets = '';
						foreach ($sec0->{'PetType'} as $type) {							
						if ($type<>'' AND $type<>'0') {
								$pets = $pets.$type.' ';
							}
						}
				?>
						<tr>
							<td><a href="/assets/pages/adopt.php?x=<?php echo $rcd['applid']; ?>&l=&q=" style="" data-transition="slide"><?php echo $rcd['applid'];?></a></td>
							<td><?php echo $rcd['applStatus'];?></td>
							<td><?php echo $fa;?></td>
							<td><?php echo $sec3->{'Ans31a'};?></td>
							<td><?php echo $contact->{'LName'}==''?'':$contact->{'LName'}.', '.$contact->{'FName'};?></td>
							<td><?php echo $contact->{'City'};?></td>
							<td><?php echo $contact->{'St'};?></td>
							<td><?php echo $contact->{'Zip'};?></td>
							<td><?php echo trim($contact->{'Phone'});?></td>
							<td><?php echo trim($contact->{'Cell'});?></td>
							<td><?php echo $rcd['applDateTime'];?></td>
							<td><?php echo $rcd['applApproveDate'];?></td>
							<td><?php echo $rcd['hvln']==''?'':$rcd['hvln'].','.$rcd['hvfn'];?></td>
							<td style="text-align:right;"><?php echo $sec4->{'Ans33'};?></td>
							<td><?php echo 'mix?';?></td>
							<td><?php echo $sec3->{'Ans23'};?></td>
							<td style="text-align:right;"><?php echo $sec3->{'Ans24'}[1]==''?'':$sec3->{'Ans24'}[0];?></td>
							<td style="text-align:right;"><?php echo $sec3->{'Ans24'}[1];?></td>
							<td><?php echo $pets;?></td>
							<td><?php echo $kids;?></td>
							<td><?php echo $spec;?></td>
						</tr>
					<?php } ?>
			</tbody>
		</table>
		<form id="adoptDownForm" action="/assets/code/csvAdopt.php"><input type="hidden" name="x" value="1"></form>
	<?php } ?>
	</div><!-- /content -->

	<div data-role="footer" class="my-ui-page" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="downSuccess" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Download Successful</h1>
		</div>
		<div data-theme="a">
			<p>The family list download was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="downError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Download Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The family list download failed, please try again later!</p>
			<p id="downErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>