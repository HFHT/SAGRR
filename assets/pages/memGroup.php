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
} else {
	$error = true;
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
function dropDownAry($SelAry,$Selected,$SelText) {
	echo "<option value='0'>-$SelText-</option>";
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $value) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$value."' ".$strSel.">".$value."</option>";
	}
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
?>
</head>
<body>
<div data-role="page" id="memGroup" data-dom-cache="false" data-myteam="<?php echo $_GET['m']?>">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li><a href="#teamMenu" data-icon="back" class="myNav">Back</a></li>	
				<li><a href="#home" data-icon="home">Home</a></li>
				<li>&nbsp;</li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="memGroupForm" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[3]-> {'Sec'} ?></h3></li>	
			<li>
				<table style="table-layout:fixed;width:100%;"><tbody>
					<tr><td style="padding-bottom:0;"><?php echo $_GET['m']=='team'?'Team':'Interest';?>:&nbsp;
						<div style="display:inline-flex;">
						<select name="SelId" id="SelId" data-mini="false">
							<?php dropDown($mysqli,"SIGroupT ORDER BY SelText",'0'); ?>							  							
						</select>
						</div>
					</td></tr>
				</tbody></table>
			</li>
			</ul>
			<ul id="memGroupMembers" data-role="listview" data-autodividers="false" data-filter="false">
				<!-- Javascript places the selected folks here -->
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="memGroupSuccess" data-dismissible="true" data-theme="a" data-position="#memGroupForm">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $row[0][FirstName] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="memGroupError" data-dismissible="false" data-theme="a"  data-position="#memGroupForm">
		<div data-role="header" data-theme="b">
		<h1>Search Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The search for the selected interest group failed, please try again later!</p>
			<p id="memGroupErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>