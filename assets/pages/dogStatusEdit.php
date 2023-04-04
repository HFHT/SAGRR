<?php
include_once '../inc/functions.php';
sec_session_start();
session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$vetVisit = '';
$row = array();
$today = date("Y-m-d");
$id = $_GET['x'];
$vid = $_GET['v'];
if (isset($_GET['x'])) {
	if ($vid<>'0') {
		$prep_stmt = "SELECT a.DStatusTrackT_id, a.StatusComment, b.SelText AS DStatus, c.SelText AS Location, d.FirstName, d.LastName,	d.Member_id,
					a.DogProcStatus, a.DogCurStatus, a.StatusDate, a.fk_PeopleT_id, a.fk_applid, a.applProcStatus, a.applProcState, a.NCI, a.Bridge
					FROM DStatusTrackT as a 
					LEFT JOIN DStatusT as b ON b.SelId=a.fk_DStatusT_id
					LEFT JOIN LocationT as c ON c.SelId=a.fk_Location_id
					LEFT JOIN PeopleT as d ON d.PeopleT_id=a.fk_PeopleT_id
					WHERE a.DStatusTrackT_id=$vid";
		
		if ($stmt = $mysqli->query($prep_stmt)) {
			$row = myFetch($stmt);
		} else {
			$error = true;
		}
	} else {
		$prep_stmt = "SELECT DogProcStatus,DogCurStatus, NCI, Bridge FROM DogT WHERE DogT_id=$id";
		if (!$stmt = $mysqli->query($prep_stmt)) {
			$error = true;
		}
		$dog = myFetch($stmt);
		$row[0] = ["DStatusTrackT_id" => 0, "fk_DogT_id" => $id, "fk_DStatusT_id" => 0, "fk_PeopleT_id" => '', "fk_applid" => 0, "fk_Location_id" => 0,
				"StatusDate" => "$today", "StatusComment" => "", "DogProcStatus" => $dog[0]['DogProcStatus'], "DogCurStatus" => $dog[0]['DogCurStatus'], 
				"NCI" => $dog[0]['NCI'], "Bridge" => $dog[0]['Bridge'], "applProcStatus" => 0, "applProcState" => 0, "Member_id" => 0, "LastName" => "", "FirstName" => ""  ];
	}
} else {
	$error = true;;
}
function dropDown($DBConn,$Selected,$dropSQL) {	
	if ($dropResp = $DBConn->query($dropSQL)) {
		echo "<option value='0'>--Select--</option>";
		while ($dropRow = $dropResp->fetch_object()) {
			if ($Selected == $dropRow->SelId) {
				$strSel = "Selected";
			} else {
				$strSel = "";
			}
			echo "<option value='".$dropRow->SelId."' ".$strSel.">".$dropRow->SelText."</option>";
		}
	} else {
		echo "<option value='0'>!!DB Error!!</option>";
	}
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
<div data-role="page" id="dogStatusEdit">
	<script>
		var DogProcStatus = '<?php echo $row[0]['DogProcStatus'] ?>';
		var DogCurStatus = '<?php echo $row[0]['DogCurStatus'] ?>';
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back"  class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>	
				<?php if ($vid<>'0' && $_SESSION["xxxMode"]=='off') { ?>
					<li>&nbsp;</li>
				<?php } else { ?>
					<li id="dogStatusSaveBtn"><a href="#dogStatusVisit" data-icon="action" class="myNav">Save</a></li>				
				<?php } ?>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">	<?php
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
		<form id="DogStatusEditForm" class="forms">
			<ul data-role="listview" id="dog_visitupdate" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-icon="info"><a href="#" id="DogStatusHelpBtn">Status change for <?php echo $_GET['n'] ?></a></li>
			<li>
			<table style="border-spacing:0;width:100%" class="<?php echo $row[0]['Bridge']=='Y' ? 'rainbowBridge' : '';?>">
				<tbody>
					<tr><td colspan="2">
						<div id="DogProcDiv">
						<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" class="DogProcStatus">
							<!--legend>Dog Status</legend-->
							<input type="radio" name="DogProcStatus" id="DogProcStatus0" value="InProcess">
							<label for="DogProcStatus0" class="my-radio">InProcess</label>
							<input type="radio" name="DogProcStatus" id="DogProcStatus1" value="Fostered">
							<label for="DogProcStatus1" class="my-radio">Fostered</label>
							<input type="radio" name="DogProcStatus" id="DogProcStatus2" value="Adopted">
							<label for="DogProcStatus2" class="my-radio">Adopted</label>
							<input type="radio" name="DogProcStatus" id="DogProcStatus3" value="Deceased">
							<label for="DogProcStatus3" class="my-radio">Deceased</label>							
						</fieldset>
						</div>
						<div id="DogCurDiv">
						<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" class="DogCurStatus">
							<!--legend>Dog Status</legend-->
							<input type="radio" name="DogCurStatus" id="DogCurStatus0" value="Available">
							<label for="DogCurStatus0" class="my-radio">Available</label>
							<input type="radio" name="DogCurStatus" id="DogCurStatus1" value="Hold-Behavior">
							<label for="DogCurStatus1" class="my-radio">Hold-Behavior</label>
							<input type="radio" name="DogCurStatus" id="DogCurStatus2" value="Hold-Medical">
							<label for="DogCurStatus2" class="my-radio">Hold-Medical</label>
							<input type="radio" name="DogCurStatus" id="DogCurStatus3" value="InTransit">
							<label for="DogCurStatus3" class="my-radio">InTransit</label>							
						</fieldset>	
						</div>
					</td>
					</tr>
					<tr><td colspan="2"><div id="DogErrContainer"></div><div id="DogErrContainer1"></div></tr>
					<tr><td width="40%">Status Date:</td><td width="60%"><input type="date" data-clear-btn="false" name="StatusDate" id="StatusDate" max="<?php echo $today ?>" value="<?php echo $row[0]['StatusDate'] ?>" required></td></tr>					
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="StatusComment" id="StatusComment" placeholder="Notes..."><?php echo $row[0]['StatusComment'] ?></textarea></td></tr>
				</tbody>
			</table>
			</li>
			<!--li>
				<a href="/assets/pages/memSelect.php?m=<?php echo $row[0]['fk_PeopleT_id'] ?>&n=<?php echo $_GET['n'] ?>" data-icon="edit" data-transition="slide">Responsible:&nbsp;<span id="memSelected"><?php echo ($row[0]['LastName'].", ".$row[0]['FirstName']) ?></span></a>
			</li-->								
			</ul>
			<input type="hidden" name="fk_DogT_id" id="fk_DogT_id" value="<?php echo $id ?>">			
			<input type="hidden" name="DStatusTrackT_id" id="DStatusTrackT_id" value="<?php echo $vid ?>">			
			<input type="hidden" name="fk_PeopleT_id" id="fk_PeopleT_id" value="<?php echo $row[0]['fk_PeopleT_id'] ?>">			
			<input type="hidden" name="fk_applid" id="fk_applid" value="<?php echo $row[0]['fk_applid'] ?>">	
			<input type="hidden" name="applProcStatus" id="applProcStatus" value="<?php echo $row[0]['applProcStatus'] ?>">			
			<input type="hidden" name="applProcState" id="applProcState" value="<?php echo $row[0]['applProcState'] ?>">			
			<input type="hidden" name="fk_DStatusT_id" id="fk_DStatusT_id" value="0">			
			<input type="hidden" name="fk_Location_id" id="fk_Location_id" value="0">			
			<input type="hidden" name="createBy" id="createBy" value="">			
			<input type="hidden" name="DogCurMember" id="DogCurMember" value="<?php echo ($row[0]['LastName'].", ".$row[0]['FirstName']) ?>">			
			<input type="hidden" name="DogCurMemNo" id="DogCurMemNo" value="<?php echo ($row[0]['Member_id']) ?>">
			<input type="hidden" name="a" id="a" value="<?php echo $_GET['a'] ?>">	
			<input type="hidden" name="DogNCIBridgeSel" id="DogNCIBridgeSel" value="">				
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
		<form id="dogNCIBridge" class="forms">	
			<?php if ($row[0]['Bridge']=='Y') { ?>

			<?php } else { ?>
			<ul data-role="listview"  data-autodividers="false" data-filter="false" style="margin-top: 1em;">		
			<?php if (($vid=='0') && ($row[0]['NCI']=='N' || $row[0]['Bridge']=='N')) { ?>					
				<li>
				<div class="ui-input-btn ui-btn ui-btn-b data-mini="true" id="">
				<?php if ($row[0]['NCI']=='N' && $row[0]['Bridge']=='N') { ?>	
					<span id="hideDogNCI">
					<input type="button" name="dogNCI" class="dogNCIBridge" id="dogNCI" value="Dog Not Coming In (NCI)" data-mini="true">
					</span>
				<?php } ?>
				<?php if ($row[0]['Bridge']=='N') {	?>	
					<span id="hideDogBridge">				
					<input type="button" name="dogBridge" class="dogNCIBridge" id="dogBridge" value="Went to Bridge" data-mini="true">
					</span>				
				<?php } ?>
				</div>
				</li>			
			<?php } ?>
			</ul>
			<?php } ?>
		</form>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->

	<div data-role="popup" id="dogStatusHelp" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Dog Status Update</h1>
		</div>
		<div data-theme="a" style="margin:1em;">
			<p>The dog's primary status is updated by assigning a dog to an applicant or member as part of the foster or adoption process.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="dogStatusSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="dogStatusBridge" data-dismissible="true" data-theme="a" class="rainbowBridge">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>The passing of <?php echo $_GET['n'] ?> has been saved.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->

	<div data-role="popup" id="dogStatusNCI" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>Record updated to indicate that <?php echo $_GET['n'] ?> is not coming in (NCI).</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->

	
	<div data-role="popup" id="dogStatusError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="dogStatusErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
</div><!-- /page -->

</body>
</html>