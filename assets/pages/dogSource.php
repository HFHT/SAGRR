<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

$error = false;
$row = array();
$today = date("Y-m-d");
if (isset($_GET['x'])) {
	$id = $_GET['x'];
    $prep_stmt = "SELECT * FROM DogSources WHERE DogS_id = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		var_dump($row[0]['DogS_Name']);
		var_dump(strpos($row[0]['DogS_Name'],'New Source')!==false);
		if (strpos($row[0]['DogS_Name'],'New Source')!==false) {
			$row[0]['DogS_Name']='';
		}
	} else {
        $error = true;
    }
} else {
	$error = true;
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
var_dump($row);
?>
</head>
<body>
<div data-role="page" id="sourceInfo" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" >Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="sourceSaveBtn"><a href="#" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">

		<form id="sourceEditForm" class="forms">
			<ul data-role="listview" id="source_update" data-autodividers="false" data-filter="false">
			<li>
			<table style="border-spacing:0;width:100%" id="sourceInfoTab">
				<tbody>
					<tr><td width="30%">Source Name*:</td><td><input type="text" name="DogS_Name" id="DogS_Name" size = "20" class="my-input-text" placeholder="Source Name..." required value="<?php echo $row[0]['DogS_Name'] ?>" data-mini="true"></td></tr>
					<tr><td>Contact(s):</td><td><input type="text" name="DogS_Contact" id="DogS_Contact" size="20" placeholder="Contact(s)..." value="<?php echo $row[0]['DogS_Contact'] ?>" data-mini="true"></td></tr>
					<tr><td>Email:<?php email($row[0]['DogS_Email'])?></td><td><input type="email" name="DogS_Email" id="DogS_Email" size = "20" class="my-input-text" placeholder="Email Address..." value="<?php echo $row[0]['DogS_Email'] ?>" data-mini="true"></td></tr>
					<tr><td>Phone:<?php phone($row[0]['DogS_Phone'])?></td><td><input type="tel" name="DogS_Phone" id="DogS_Phone" size = "20" class="my-input-text" placeholder="(   )___-___" value="<?php echo $row[0]['DogS_Phone'] ?>" data-mini="true"></td></tr>
					<tr><td colspan="2"><textarea name="DogS_Addr" id="DogS_Addr" data-mini="true" placeholder="Address..."><?php echo $row[0]['DogS_Addr']; ?></textarea></td></tr>
				</tbody>
			</table>
			</li>
			</ul>
			<input type="hidden" name="DogS_id" id="DogS_id" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="sourceSuccess" data-dismissible="true" data-theme="a" data-position="#sourceInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>Dog source record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="sourceError" data-dismissible="false" data-theme="a"  data-position="#sourceInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for the dog source failed, please try again later!</p>
			<p id="sourceErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>