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
$prevAppMsg = "";
if (isset($_GET['x'])) {
	$applid = $_GET['x'];
    $prep_stmt = "SELECT applApproveDate,applApproved,applApprovedBy FROM Applications WHERE applid = $applid";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		if (isset($row[0]['applApproveDate'])) {
			$prevAppMsg = '<p><b>Previously '.($row[0]['applApproved']=='Y'?'Approved' : 'Not approved').'</b> by '.$row[0]['applApprovedBy'].' on '.$row[0]['applApproveDate'].'</p>';
			$applApproveDate = $row[0]['applApproveDate'];
		} else {
			$applApproveDate = $today;
		}
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
<div data-role="page" id="applApprove" data-dom-cache="false" data-myappr="<?php echo $row[0]['applApproved']?>" >
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" >Back</a></li>	
				<li class="cancelBtn myapappr"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li class="myapappr" id="apapprSaveBtn" style="display:none;"><a href="#" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="apapprEdit" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li>
				<h3>Applicant Approval</h3>
				<div class="my-left-8"><?php echo $prevAppMsg;?><p><b><i><span id="apprBy"><?php echo $row[0]['applApprovedBy'];?></span></i></b> has reviewed the: <ul style="padding-left:30px;"><li>application and</li><li>results of the home visit</li></ul><br>I approve this request for adoption of a Golden.</p></div>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" class="xxxyyy">
					<input type="radio" name="applApproved" id="applApprovedY" class="applAppDisable" value="Y" <?php checkBox($row[0]['applApproved']=='Y');?> ><label for="applApprovedY">Approved</label>
					<input type="radio" name="applApproved" id="applApprovedN" class="applAppDisable" value="N" <?php checkBox($row[0]['applApproved']=='N');?>><label for="applApprovedN">Not Approved</label>
				</fieldset>
				<table>
				<tr><td>Date:</td><td>
				<input type="date" name="applApproveDate" id="applApproveDate" max="<?php echo $today ?>" value="<?php echo $applApproveDate ?>" required>
				</td></tr></table>
			</li>			
			<input type="hidden" name="applid" id="applid" value="<?php echo $applid;?>">			
			<input type="hidden" name="applApprovedBy" id="applApprovedBy" value="">			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="apapprSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>The approval was successfully noted.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="apapprError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for the approval, please try again later!</p>
			<p id="apapprErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>