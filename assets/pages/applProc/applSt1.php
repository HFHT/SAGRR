<?php
// Step 1, check to see if an application is already in process
include_once '../../inc/functions.php';
sec_session_start();
include_once '../../inc/db_connect.php';

$error_msg = $error_str = $error_sql = $applList = '';

if (!empty($_POST['x'])) {
	try {
		$prep_stmt = "SELECT applDocId FROM Applications WHERE applComplete='N' AND Deleted <> 'Y'";
		$applList = readDB($mysqli,$prep_stmt,true);
	}
	catch(Exception $e) {
		$error_str = $e->getMessage();
		$error_sql = $prep_stmt;
		$error_msg = 'Could not obtain Application, try again later!';
	}
} else {
	$error_msg = 'Internal program error';
}
$mysqli->close();

function readDB ($conn,$query,$multi) {
	if ($result = $conn->query($query) ) {
		if ($multi) {
			return ($result->fetch_row());
		} else {
			return ($result->fetch_assoc());
		}
	} else {
		throw new Exception($conn->error);
	}
}	

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($applList);
?>
</head>
<body>
<div data-role="page" id="resumePage">
	<script>
	var nextPage='/assets/pages/applProc/applSt2.php';
	var aryApplList = <?php echo $applList;?>;
	</script>
	<div data-role="header" data-position="fixed">
		<img src="assets/img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-theme="b">
			<ul>
				<li><a href="#home" class="ui-icon-arrow-l ui-btn-icon-left">Previous</a></li>	
				<li><a href="#" class="my-fwd ui-icon-arrow-r ui-btn-icon-right">Next</a></li>	
			</ul>
		</div><!-- /navbar -->		
	</div><!-- /header -->

	<div data-role="main" class="ui-content">
	<?php
	if ($error) {
	?>
		<div class="ui-corner-all">
			<div class="ui-bar ui-bar-a">
				<h3>Error Occurred</h3>
			</div>
			<div class="ui-body ui-body-a">
				<p>Could not access the SAGRR Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<h4 style="margin-top:0;">Resume editing an application </h4>
		<p>Did you already start but not complete an application and have the Application Number you were provided?</p>
		<form id="resumeForm">
			<select name="resume" id="resume" data-role="slider">
				<option value="N">No</option>
				<option value="Y">Yes</option>
			</select>
			<div class="ui-grid-a" id="hideresume" style="display:none;">
				<div class="ui-block-a" style="width:30%;white-space:pre-line;">Application Number</div>
				<div class="ui-block-b"><input type="text" name="resumeId" id="resumeId" value=""></div>
			</div>
		</form>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->

	<div data-role="popup" id="resumeError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h4>Could not locate Application Number!</h4>
		</div>
		<div data-theme="a">
			<p>Could not locate Application Number: <span id="resumeErrorText">,
			<br>please check and try again</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
</div><!-- /page -->



</body>
</html>