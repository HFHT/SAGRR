<?php
include_once '../inc/functions.php';
sec_session_start();
session_start();
include_once '../inc/db_connect.php';
$error = false;
$vetVisit = '';
$row = array();
$today = date("Y-m-d");
$id = $_GET['x'];
$vid = $_GET['v'];
if (isset($_GET['x'])) {
	if ($vid<>'0') {
		$prep_stmt = "SELECT a.DStatusTrackT_id, a.StatusComment, b.SelText AS DStatus, c.SelText AS Location, d.FirstName, d.LastName,	d.Member_id,
					a.DogProcStatus, a.DogCurStatus, a.StatusDate, a.fk_PeopleT_id, a.applProcStatus, a.applProcState, a.fk_applid
					FROM DStatusTrackT as a 
					LEFT JOIN DStatusT as b ON b.SelId=a.fk_DStatusT_id
					LEFT JOIN LocationT as c ON c.SelId=a.fk_Location_id
					LEFT JOIN PeopleT as d ON d.PeopleT_id=a.fk_PeopleT_id
					WHERE a.DStatusTrackT_id=$vid";
		
		if ($stmt = $mysqli->query($prep_stmt)) {
			$row = myFetch($stmt);
		} else {
			$error = true;;
		}
		$statType = 'record';
	} else {
		$row[0] = ["DStatusTrackT_id" => 0, "fk_DogT_id" => $id, "fk_DStatusT_id" => 0, "fk_PeopleT_id" => '', "fk_Location_id" => 0,
				"StatusDate" => "$today", "StatusComment" => "", "applProcStatus" => $_GET['q'], "applProcState" => $_GET['s'], "fk_applid" => 0, "DogCurStatus" => 'X',
				"DogProcStatus" => 'X'];
		$statType = 'change';
	}
} else {
	$error = true;
}
function checkBox($decision) {
//	if ($decision==1) {
//		echo ('checked');
//	}
}
function dropDownAry($SelAry,$Selected,$SelText) {
//	echo "<option value='0'>-$SelText-</option>";
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $value) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$key."' ".$strSel.">".$value."</option>";
	}
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
var_dump($row);
$memname = $row[0][fk_PeopleT_id]==0?'':$row[0]['LastName'].", ".$row[0]['FirstName'];
?>
</head>
<body>
<div data-role="page" id="adoptStatusEdit">
	<script>
	var applProcStatus = '<?php echo $row[0]['applProcStatus'] ?>';
	var applProcState = '<?php echo $row[0]['applProcState'] ?>';
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
					<li id="adoptStatusSaveBtn" class="ApplE" style="display:none"><a href="#" data-icon="action" class="myNav">Save</a></li>	
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
		<form id="adoptStatusEditForm" class="forms">
			<ul data-role="listview" id="dog_visitupdate" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-icon="info"><a href="#" id="applStatusHelpBtn">Status <?php echo $vid<>'0' ?  'record' : 'change'; ?> for <?php echo $_GET['n'] ?></a></li>
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td>Application State:</td>
					<td>
						<select name="applProcState" id="applProcState" data-mini="true">
							<?php
							$selOpt = ["Active" => "Active", "Inactive" => "Inactive", "OnHold" => "OnHold", "Withdrawn" => "Withdrawn", "DFOS" => "DFOS", "NotApproved" => "NotApproved"];
							if ($row[0]['applProcState']=='Complete' || $_SESSION["xxxMode"]=='on') {$selOpt["Complete"] =  "Complete";}
							dropDownAry($selOpt,$row[0]['applProcState'],'');
							?>
						</select>
					</td></tr>									
					<tr><td>Process Status:</td>
					<td>
						<select name="applProcStatus" id="applProcStatus" data-mini="true">
							<?php
							$selOpt = ["New" => "New", "HomeVisit" => "HomeVisit", "VisitComplete" => "VisitComplete", "Decision" => "Decision", "WaitList" => "WaitList", "ShortList" => "ShortList"];
							if ($_SESSION["xxxMode"]=='on' || $vid<>'0') {
								$addOpt = ["Match-Trial" => "Match-Trial", "Matched-Foster" => "Matched-Foster", "Matched-Adopted" => "Matched-Adopted"];
								$selOpt = array_merge($selOpt, $addOpt);
								$selOpt = array_merge(["Draft"=>"Draft"],$selOpt);
							}
							dropDownAry($selOpt,$row[0]['applProcStatus'],'');
							?>
						</select>
					</td></tr>
					<tr><td colspan="2"><div id="adoptErrContainer"></div><div id="adoptErrContainer1"></div></tr>
					<tr><td width="40%">Status Date:</td><td width="60%"><input type="date" data-clear-btn="false" name="StatusDate" id="StatusDate" max="<?php echo $today ?>" value="<?php echo $row[0]['StatusDate'] ?>" required></td></tr>					
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="StatusComment" id="StatusComment" placeholder="Notes..."><?php echo $row[0]['StatusComment'] ?></textarea></td></tr>
					<tr><td width="40%">Responsible:</td><td width="60%"><span id="memSelected"><?php echo $memname==''?'No one assigned':$memname; ?></span></td></tr>					
				</tbody>
			</table>
			</li>
			<?php if ($vid=='0') { ?>
			<li>
				<a href="/assets/pages/memSelect.php?m=<?php echo $row[0][fk_PeopleT_id] ?>&n=<?php echo $_GET['n'] ?>&l=visit" data-icon="edit" data-transition="slide">Select Home Visit Volunteer</a></li>					
			</li>
			<?php } ?>
			</ul>
			<input type="hidden" name="fk_DogT_id" id="fk_DogT_id" value="0">			
			<input type="hidden" name="fk_applid" id="fk_applid" value="<?php echo $id;?>">			
			<input type="hidden" name="DStatusTrackT_id" id="DStatusTrackT_id" value="<?php echo $vid ?>">			
			<input type="hidden" name="applHV_fk_PeopleT" id="applHV_fk_PeopleT" value="<?php echo $row[0]['applHV_fk_PeopleT'] ?>" >			
			<input type="hidden" name="applInTake_fk_PeopleT" id="applInTake_fk_PeopleT" value="<?php echo $row[0]['applInTake_fk_PeopleT'] ?>" >			
			<input type="hidden" name="fk_PeopleT_id" id="fk_PeopleT_id" value="<?php echo $row[0]['fk_PeopleT_id'] ?>" >			
			<input type="hidden" name="fk_DStatusT_id" id="fk_DStatusT_id" value="0">			
			<input type="hidden" name="fk_Location_id" id="fk_Location_id" value="0">			
			<input type="hidden" name="createBy" id="createBy" value="">			
			<input type="hidden" name="DogProcStatus" id="DogProcStatus" value="<?php echo ($row[0]['DogProcStatus']) ?>">			
			<input type="hidden" name="DogCurStatus" id="DogCurStatus" value="<?php echo ($row[0]['DogCurStatus']) ?>">			
			<input type="hidden" name="DogCurMember" id="DogCurMember" value="<?php echo ($row[0]['LastName'].", ".$row[0]['FirstName']) ?>">			
			<input type="hidden" name="DogCurMemNo" id="DogCurMemNo" value="<?php echo ($row[0]['Member_id']) ?>">
			<input type="hidden" name="a" id="a" value="<?php echo $_GET['a'] ?>">						
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	<div data-role="popup" id="applStatusHelp" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Application Status</h1>
		</div>
		<div data-theme="a" style="margin:1em;">
			<p>Some of the Application States and Process Statuses are controlled by the foster or adoption process and will not be shown in the dropdown boxes.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptStatusSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptStatusError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptStatusErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
</div><!-- /page -->

</body>
</html>