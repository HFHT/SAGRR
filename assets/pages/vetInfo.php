<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

$error = false;
$row = array();
$today = date("Y-m-d");
if (isset($_GET['x'])) {
	$id = $_GET['x'];
    $prep_stmt = "SELECT * FROM VetClinicT WHERE SelId = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		if (strpos($row[0]['SelText'],'New Clinic')) {
			$row[0]['SelText']='';
		}
		if (strpos($row[0]['vetClinic'],'New Clinic')) {
			$row[0]['vetClinic']='';
		}
	} else {
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
?>
</head>
<body>
<div data-role="page" id="vetInfo" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" >Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="vetSaveBtn" class="DogE" style="display:none"><a href="#" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">

		<form id="VetEdit" class="forms">
			<ul data-role="listview" id="vet_update" data-autodividers="false" data-filter="false">
			<li>
			<table style="border-spacing:0;width:100%" id="vetInfoTab">
				<tbody>
					<tr><td colspan="2"><textarea name="vetClinic" id="vetClinic" data-mini="true" placeholder="Clinic Name..."><?php echo $row[0]['vetClinic']; ?></textarea></td></tr>
					<tr><td colspan="2"><textarea name="vetAddress" id="vetAddress" data-mini="true" placeholder="Address..."><?php echo $row[0]['vetAddress']; ?></textarea></td></tr>
					<!--tr><td>Clinic*:</td><td><input type="text" name="vetClinic" id="vetClinic" size = "20" class="my-input-text" placeholder="Clinic Name..." required value="<?php echo $row[0]['vetClinic'] ?>" data-mini="true"></td></tr-->
					<tr><td width="30%">Short Name*:</td><td><input type="text" name="SelText" id="SelText" size = "20" class="my-input-text" placeholder="Drop down..." required value="<?php echo $row[0]['SelText'] ?>" data-mini="true"></td></tr>
					<tr><td>Email:<?php email($row[0]['vetEmail'])?></td><td><input type="email" name="vetEmail" id="vetEmail" size = "20" class="my-input-text" placeholder="Email Address..." value="<?php echo $row[0]['vetEmail'] ?>" data-mini="true"></td></tr>
					<tr><td>Phone*:<?php phone($row[0]['vetPhone'])?></td><td><input type="tel" name="vetPhone" id="vetPhone" size = "20" class="my-input-text" placeholder="(   )___-___" required value="<?php echo $row[0]['vetPhone'] ?>" data-mini="true"></td></tr>
					<tr><td style="white-space:pre-wrap;">Emergency Phone:<?php phone($row[0]['vetEmergencyPhone'])?></td><td><input type="tel" name="vetEmergencyPhone" id="vetEmergencyPhone" size = "20" class="my-input-text" placeholder="(   )___-___" value="<?php echo $row[0]['vetEmergencyPhone'] ?>" data-mini="true"></td></tr>
					<!--tr><td>Address:</td><td><input type="text" name="vetAddress" id="vetAddress" size = "20" class="my-input-text" placeholder="Address..."  value="<?php echo $row[0]['vetAddress'] ?>" data-mini="true"></td></tr-->
					<tr><td>Contact(s):</td><td><input type="text" name="vetContact" id="vetContact" size="20" value="<?php echo $row[0]['vetContact'] ?>" data-mini="true"></td></tr>
				</tbody>
			</table>
			</li>
			</ul>
			<input type="hidden" name="SelId" id="SelId" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="vetSuccess" data-dismissible="true" data-theme="a" data-position="#memInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>Clinic's record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="vetError" data-dismissible="false" data-theme="a"  data-position="#memInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for the clinic failed, please try again later!</p>
			<p id="vetErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>