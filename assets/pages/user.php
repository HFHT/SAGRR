<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

$error = false;
$row = array();
$today = date("Y-m-d");
$objRole = new StdClass();
$objOpts = new StdClass();
if (isset($_GET['x'])) {
	$username = $_GET['x'];
    $prep_stmt = "SELECT * FROM members as a LEFT JOIN PeopleT as d ON d.PeopleT_id=a.fk_PeopleT_id WHERE username = '$username'";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$objRole = json_decode($row[0]['Role']);
		$objOpts = json_decode($row[0]['Options']);
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
	echo "<option value='0'>$SelText</option>";
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
function checkBox($decision) {
	if ($decision==1) {
		echo ('checked');
	}
}
function hide($decision) {
	if ($decision==1) {
		echo ('style="display:none"');
	}
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($prep_stmt);
var_dump($row[0]);
?>
</head>
<body>
<div data-role="page" id="user" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" >Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="userSaveBtn"><a href="#" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">

		<form id="userEdit" class="forms">
			<ul data-role="listview" id="vet_update" data-autodividers="false" data-filter="false">
			<li>
				<h3><?php echo $row[0]['memname']; ?></h3>
				<h3><?php echo $row[0]['email']; ?></h3>
				<table style="border-spacing:0;width:100%" id="userInfoTab"><tbody>				
					<tr><td style="width:50%;">SAGRR User:</td><td><?php echo $row[0]['username']; ?></td></tr>
					<tr><td>Last Login:</td><td><?php echo $row[0]['LastLogin']; ?></td></tr>
					<tr><td>First Login:</td><td><?php echo $row[0]['BeginDate']; ?></td></tr>
					<tr><td>Login Count:</td><td><?php echo $row[0]['LoginCnt']; ?></td></tr>
				</tbody></table>
			</li>
			<li>
				<a href="/assets/pages/memSelect.php?m=<?php echo $row[0]['fk_PeopleT_id'] ?>&n=<?php echo $row[0]['email'] ?>" data-icon="edit" data-transition="slide">Linked to:&nbsp;<span id="memSelected"><?php echo ($row[0]['LastName'].", ".$row[0]['FirstName']) ?></span></a></li>					
			</li>			
			<li>
				<h3>Permissions</h3>
				<table style="border-spacing:0;width:100%" id="userInfoTab"><tbody>				
					<tr><td style="width:50%;">Dog:</td><td><input type="checkbox" data-role="flipswitch" name="rD" id="rD" data-on-text="Edit" data-off-text="View" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($objRole->{'Dog'}=='Y');?>/></td></tr>
					<tr><td>Members:</td><td><input type="checkbox" data-role="flipswitch" name="rM" id="rM" data-on-text="Edit" data-off-text="View" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($objRole->{'Mem'}=='Y');?>/></td></tr>
					<tr><td>Applicant:</td><td><input type="checkbox" data-role="flipswitch" name="rL" id="rL" data-on-text="Edit" data-off-text="View" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($objRole->{'Appl'}=='Y');?>/></td></tr>
					<tr><td>Approval:</td><td><input type="checkbox" data-role="flipswitch" name="rP" id="rP" data-on-text="Yes" data-off-text="No" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($objRole->{'App'}=='Y');?>/></td></tr>
					<tr><td>Dog Assignment:</td><td><input type="checkbox" data-role="flipswitch" name="rX" id="rX" data-on-text="Yes" data-off-text="No" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($objRole->{'Assign'}=='Y');?>/></td></tr>
					<tr><td>Administrator?</td><td><input type="checkbox" data-role="flipswitch" name="rA" id="rA" data-on-text="Yes" data-off-text="No" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($objRole->{'Admin'}=='Y');?>/></td></tr>
				</tbody></table>
			</li>
			<li>
				<h3>Lock Out</h3>
				<table style="border-spacing:0;width:100%" id="userInfoTab"><tbody>						
					<tr><td style="width:50%;">Locked out?:</td><td><input type="checkbox" data-role="flipswitch" name="Locked" id="Locked" data-on-text="Yes" data-off-text="No" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($row[0]['Locked']=='Y');?>/></td></tr>
					<tr class="LockHide" <?php hide($row[0]['Locked']=='N');?>><td>Date locked out:</td><td><span class="lockdateText LockClear"><?php echo $row[0]['LockedDate'];?></span></td></tr>
					<tr class="LockHide" <?php hide($row[0]['Locked']=='N');?>><td>Reason:</td><td><textarea name="LockedReason" id="LockedReason" data-mini="true" class="LockClear"><?php echo $row[0]['LockedReason']; ?></textarea></td></tr>
				</tbody></table>
			</li>
			</ul>
			<input type="hidden" name="username" id="username" value="<?php echo $username ?>">
			<input type="hidden" name="Validated" id="Validated" value="Y">
			<input type="hidden" name="LockedDate" id="LockedDate" value="<?php echo $row[0]['LockedDate'];?>">
			<input type="hidden" name="fk_PeopleT_id" id="fk_PeopleT_id" value="<?php echo $row[0]['fk_PeopleT_id'];?>">
			<input type="hidden" name="rS" id="rS" value="Y">
			
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="userSuccess" data-dismissible="true" data-theme="a" data-position="#userInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>User's record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="userError" data-dismissible="false" data-theme="a"  data-position="#userInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for the user failed, please try again later!</p>
			<p id="userErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>