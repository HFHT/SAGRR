<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$vetVisit = '';
$row = array();
$today = date("Y-m-d");
$oneYearOn = new DateTime();
$oneYearOn->add(new DateInterval('P365D'));
$sixMonthOn = new DateTime();
$sixMonthOn->add(new DateInterval('P180D'));
// $oneYearOn = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day"));
// $sixMonthOn = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 6 month"));
$id = $_GET['x'];
$vid = $_GET['v'];
$qty = '-';
if (isset($_GET['x'])) {
	if ($vid<>'0') {
		$prep_stmt = "SELECT * FROM VetVisit WHERE VetVisit_id=$vid";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$row = myFetch($stmt);
			$prep_stmt = "SELECT COUNT(*) AS Qty FROM FileAsset WHERE Fk_VetVisit_id=$vid AND Deleted <> 'Y'";
			if ($stmt = $mysqli->query($prep_stmt)) {
				$qty = $stmt->fetch_assoc();
			}
		} else {
			$error = true;;
		}
	} else {
		$row[0] = ["VetVisit_id" => 0, "fkV_DogT_id" => $id, "fk_VetClinicT" => 0, "VetCost" => 0,
				"VetDate" => "", "VetInvoice" => "", "VetFollowup" => "", "VetFollowupComp" => "N",
				"VetReason" => "", "VetResult" => ""];
	}
} else {
	$error = true;;
}
function dropDown($DBConn,$DBtable,$Selected) {	
	$dropSQL = "SELECT * FROM $DBtable";
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
function checkBox($decision) {
	if ($decision==1) {
		echo ('checked');
	}
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="dogVisitEdit">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>	
				<li id="dogVisitSaveBtn"><a href="#dogVetVisit" data-icon="action" class="myNav">Save</a></li>				
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
		<form id="DogVetVisit" class="forms">
			<ul data-role="listview" id="dog_visitupdate" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Veterinarian visit for <?php echo $_GET['n'] ?></li>			
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td width="40%">Visit Date:</td><td width="60%"><input type="date" data-clear-btn="false" name="VetDate" id="VetDate" min="2000-01-01" max="<?php echo $sixMonthOn->format('Y-m-d'); ?>" value="<?php echo $row[0]['VetDate'] ?>"></td></tr>
					<tr><td>Clinic:</td><td>
						<select name="fk_VetClinicT" id="fk_VetClinicT" data-mini="true">
							<?php dropDown($mysqli,"VetClinicT WHERE SelText<>'_New Clinic'",$row[0]['fk_VetClinicT']); ?>							  
						</select>					
					</td></tr>
					<tr><td>Cost:</td><td><input type="text" name="VetCost" id="VetCost" size = "20" class="my-input-text" placeholder="nnnn.nn" pattern="(?:\d*\.)?\d\d" value="<?php echo $row[0]['VetCost'] ?>"></td></tr>
					<tr><td>Invoice:</td><td><input type="text" name="VetInvoice" id="VetInvoice" size = "20" class="my-input-text" placeholder="Invoice number..." value="<?php echo $row[0]['VetInvoice'] ?>"></td></tr>
					<tr><td>Followup:</td><td><input type="date" data-clear-btn="false" name="VetFollowup" id="VetFollowup" min="2000-01-01" max="<?php echo $oneYearOn->format('Y-m-d'); ?>" value="<?php echo $row[0]['VetFollowup'] ?>"></td></tr>
					<tr><td colspan="2">
					<table><tbody>
						<tr><td>Followup Complete:</td><td>
						<input type="checkbox" name="VetFollowupComp" id="VetFollowupComp" value="Y" <?php checkBox($row[0]['VetFollowupComp']=='Y');?> data-mini="false" data-iconpos="right">
						</td></tr>
					</tbody></table>
					</td></tr>
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="VetReason" id="VetReason" placeholder="Reason for visit..."><?php echo $row[0]['VetReason'] ?></textarea></td></tr>
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="VetResult" id="VetResult" placeholder="Result of visit..."><?php echo $row[0]['VetResult'] ?></textarea></td></tr>
				</tbody>
			</table>
			</li>
			<?php if ($vid<>'0') { ?>
			<li><a href="/assets/pages/mediaList.php?x=0&v=0&r=<?php echo $vid;?>&d=<?php echo $id;?>&s=s&m=visit" data-transition="slide"> Attachments <span class="ui-li-count"><?php echo $qty['Qty'];?></span></a></li>						
			<?php } ?>
			</ul>
			<img src="http://southern-az-golden-retriever-rescue.org/database/assets/veterinarian_logo.png" width="50" style="margin-top:2em"/>
			<input type="hidden" name="fkV_DogT_id" id="fkV_DogT_id" value="<?php echo $row[0]['fkV_DogT_id'] ?>">			
			<input type="hidden" name="VetVisit_id" id="VetVisit_id" value="<?php echo $vid ?>">			
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="dogVisitSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="dogVisitError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="dogVisitErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
</div><!-- /page -->

</body>
</html>