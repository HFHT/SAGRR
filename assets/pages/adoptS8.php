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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec8,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec8']);
		$applProgress = json_decode($row[0]['applProgress']);
	} else {
        $error = true;
    }
} else {
	$error = true;
}
function checkBox($decision) {
	if ($decision==1) {
		echo ('checked');
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

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="adoptS8" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="adoptS8SaveBtn" class="ApplE" style="display:none"><a href="#adoptS8" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="adoptS8Form"class="forms">
	
			<ul data-role="listview" class="AgreeChecks" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[8]-> {'Sec'} ?><div onclick='alert("Some info...");' class="ui-icon ui-icon-check" style="color:white;float:right"><div></h3></li>
			<li>
			<div data-role="fieldcontain">
				<fieldset data-role="controlgroup">
				   <input type="checkbox" name="Ans53" id="Ans53" class="custom" value="Y" <?php checkBox($applSec->{'Ans53'}=='Y');?> data-mini="false"  data-iconpos="right"/>
				   <label for="Ans53">All the information I have provided in this application is true and complete.</label>
				   <input type="checkbox" name="Ans124" id="Ans124" class="custom" value="Y" <?php checkBox($applSec->{'Ans124'}=='Y');?> data-mini="false"  data-iconpos="right"/>
				   <label for="Ans124">I am at least 21 years of Age.</label>
				   <input type="checkbox" name="Ans121" id="Ans121" class="custom" value="Y" <?php checkBox($applSec->{'Ans121'}=='Y');?> data-mini="false"  data-iconpos="right"/>
				   <label for="Ans121">I understand SAGRR places rescues with their best match, not by order of applicants.</label>
				   <input type="checkbox" name="Ans54" id="Ans54" class="custom" value="Y" <?php checkBox($applSec->{'Ans54'}=='Y');?> data-mini="false"  data-iconpos="right"/>
				   <label for="Ans54">I consent to a home visit by the Southern Arizona Golden Retriever Rescue home visit team.</label>
				   <input type="checkbox" name="Ans55" id="Ans55" class="custom" value="Y" <?php checkBox($applSec->{'Ans55'}=='Y');?> data-mini="false"  data-iconpos="right"/>
				   <label for="Ans55">I understand that Southern Arizona Golden Retriever Rescue has the right to deny my application.</label>
				   <input type="checkbox" name="Ans122" id="Ans122" class="custom" value="Y" <?php checkBox($applSec->{'Ans122'}=='Y');?> data-mini="false"  data-iconpos="right"/>
				   <label for="Ans122">I understand Southern Arizona Golden Retriever Rescue requires home visits before and after adoption.</label>
				   <input type="checkbox" name="Ans123" id="Ans123" class="custom" value="Y" <?php checkBox($applSec->{'Ans123'}=='Y');?> data-mini="false"  data-iconpos="right"/>
				   <label for="Ans123">I understand that should the adoption not prove successful, or I can no longer care for the golden, the golden must be returned to Southern Arizona Golden Retriever Rescue.
			   </fieldset>
			</div>
			<div class="S8Error"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Thank you for your interest in Southern Arizona Golden Retriever Rescue. We appreciate the time you took to fill out this application.</p>
			</li>
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptS8Success" data-dismissible="true" data-theme="a" data-position="#adoptS8Form">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptS8Error" data-dismissible="false" data-theme="a"  data-position="#adoptS8Form">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptS8ErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>