<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$error = false;
$vetVisit = '';
$row = array();
$today = date("Y-m-d");
$id = $_GET['x'];
if (isset($_GET['x'])) {
	if ($_GET['m']=='team') {
		$prep_stmt = "SELECT Teams as Interests FROM PeopleT WHERE PeopleT_id=$id";
	} else {
		$prep_stmt = "SELECT Interests FROM PeopleT WHERE PeopleT_id=$id";
	}
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$interests = json_decode(str_replace(' ','',$row[0]['Interests']));
	} else {		
		$error = true;;
	}
} else {
	$error = true;;
}
function checkBox($DBConn,$DBtable,$Selected) {	
	$dropSQL = "SELECT * FROM $DBtable ORDER BY SelText";
	if ($dropResp = $DBConn->query($dropSQL)) {
		$strSelY = $strSelN = '';
		while ($dropRow = $dropResp->fetch_object()) {
			if ($dropRow->SelDesc == 'Y') {
				$strStyle = ' STYLE="color:green;line-height:1em;font-size:0.9em;" ';
			} else {
				$strStyle = ' STYLE="line-height:1em;font-size:0.9em;" ';
			}
			if (in_array($dropRow->SelId,$Selected)) {
				$strSel = "checked";
				$strSelY = $strSelY.'<input type="checkbox" name="int[]" id="b'.$dropRow->SelId.'" value="'.$dropRow->SelId.'" '.$strSel.'><label for="b'.$dropRow->SelId.'"'.$strStyle.'>'.$dropRow->SelText.'</label>';
			} else {
				$strSel = "";
				$strSelN = $strSelN.'<input type="checkbox" name="int[]" id="b'.$dropRow->SelId.'" value="'.$dropRow->SelId.'" '.$strSel.'><label for="b'.$dropRow->SelId.'"'.$strStyle.'>'.$dropRow->SelText.'</label>';				
			}			
		}
		echo $strSelY.$strSelN;
	} else {
		echo "<option value='0'>!!DB Error!!</option>";
	}
}

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($interests);
?>
</head>
<body>
<div data-role="page" id="memInterest">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="memInterestSaveBtn" class="MemE" style="display:none"><a href="#" data-icon="action" class="myNav">Save</a></li>				
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
		<form id="memInterestForm" class="forms">
			<ul data-role="listview" id="mem_interestupdate" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><?php echo $_GET['m']=='team'?'Team Assignments':'Interest Groups';?> for <?php echo $_GET['n'] ?></li>			
			<li>		
			<fieldset data-role="controlgroup">
				<?php
				checkBox($mysqli,'SIGroupT',$interests -> {'Interests'});
				?>
			</fieldset>
			<input type="hidden" name="PeopleT_id" id="PeopleT_id" value="<?php echo $id ?>">
			<input type="hidden" name="mode" id="mode" value="<?php echo $_GET['m'];?>">			
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->
			</li>
		</form>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="memInterestSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="memInterestError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="memInterestErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
</div><!-- /page -->

</body>
</html>