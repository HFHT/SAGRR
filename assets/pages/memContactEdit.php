<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$error = false;

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$vetVisit = '';
$row = array();
$today = date("Y-m-d");
$id = $_GET['x'];
$vid = $_GET['v'];
if (isset($_GET['x'])) {
	if ($vid<>'0') {
		$prep_stmt = "SELECT * FROM ContactLog WHERE logid=$vid";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$row = myFetch($stmt);
		} else {
			$error = true;
		}
	} else {
		if ($_GET['m']=='appl') {
			$row[0]['fk_applid'] = $id;
		} else {
			if ($_GET['m']=='dog') {
				$row[0]['fk_DogT_id'] = $id;
			} else {
				$row[0]['fk_PeopleT_id'] = $id;
			}
		}		
		$row[0]['logDate'] = $today;
	}
} else {
	$error = true;
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

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($row,$prep_stmt);
?>
</head>
<body>
<div data-role="page" id="memContactEdit">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>	
				<li id="memContactSaveBtn" class="MemE" style="display:none"><a href="#memContact" data-icon="action" class="myNav">Save</a></li>				
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
				<p>Could not access the Member Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<form id="memContactForm" class="forms">
			<ul data-role="listview" id="mem_contact" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Contact Log Entry for <?php echo $_GET['n'] ?></li>			
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td width="40%">Contact Date:</td><td width="60%"><input type="date" data-clear-btn="false" name="logDate" id="logDate" max="<?php echo $today ?>" value="<?php echo $row[0]['logDate'] ?>"></td></tr>
					<tr><td colspan="2"><textarea data-mini="true" rows="7" name="logText" id="logText" placeholder="Log Entry..."><?php echo $row[0]['logText'] ?></textarea></td></tr>
				</tbody>
			</table>
			</li>
			</ul>
			<input type="hidden" name="fk_PeopleT_id" id="fk_PeopleT_id" value="<?php echo $row[0]['fk_PeopleT_id']==''?'0':$row[0]['fk_PeopleT_id'];?>">			
			<input type="hidden" name="fk_applid" id="fk_applid" value="<?php echo $row[0]['fk_applid']==''?'0':$row[0]['fk_applid'];?>">			
			<input type="hidden" name="fk_DogT_id" id="fk_DogT_id" value="<?php echo $row[0]['fk_DogT_id']==''?'0':$row[0]['fk_DogT_id'];?>">			
			<input type="hidden" name="logid" id="logid" value="<?php echo $vid ?>">
			<input type="hidden" name="logBy" id="logBy" value="">						
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="memContactSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="memContactError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="memContactErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
</div><!-- /page -->

</body>
</html>