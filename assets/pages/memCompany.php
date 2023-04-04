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
    $prep_stmt = "SELECT FirstName,Company,WorkPhone,WorkEmail,Contact,Vet,Title FROM PeopleT WHERE PeopleT_id = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		if ($row[0]['Vet']=='Y') {
			$strSel = 'checked';
		} else {
			$strSel = '';
		}
	} else {
        $error = true;
    }
} else {
	$error = true;
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="memCompany" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="memCompanySaveBtn" class="MemE" style="display:none"><a href="#memSkills" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="MemCompanyForm" class="forms">
			<ul data-role="listview" id="mem_update" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Company Information for <?php echo $_GET['n'] ?></li>						
			<li>
			<table style="border-spacing:0;width:100%" id="memInfoTab">
				<tbody>
					<tr><td>Company*:</td><td><input type="text" name="Company" id="Company" size = "20" class="my-input-text" placeholder="Company..." required value="<?php echo $row[0]['Company'] ?>" data-mini="true"></td></tr>
					<tr><td>Title:</td><td><input type="text" name="Title" id="Title" size = "20" class="my-input-text" placeholder="Title..."  value="<?php echo $row[0]['Title'] ?>" data-mini="true"></td></tr>
					<tr><td>Contact:</td><td><input type="text" name="Contact" id="Contact" size = "20" class="my-input-text" placeholder="Contact Name..."  value="<?php echo $row[0]['Contact'] ?>" data-mini="true"></td></tr>
					<tr><td>Work Email:</td><td><input type="email" name="WorkEmail" id="WorkEmail" size = "20" class="my-input-text" placeholder="Email Address..." value="<?php echo $row[0]['WorkEmail'] ?>" data-mini="true"></td></tr>
					<tr><td>Work Phone:</td><td><input type="tel" name="WorkPhone" id="WorkPhone" size = "20" class="my-input-text" placeholder="(   )___-___"  value="<?php echo $row[0]['WorkPhone'] ?>" pattern="^(?:\([2-9]\d{2}\)\ ?|[2-9]\d{2}(?:\-?|\ ?))[2-9]\d{2}[- ]?\d{4}$" data-mini="true"></td></tr>
					<tr><td colspan="2"><input type="checkbox" name="Vet" id="Vet" value="Y" <?php echo $strSel; ?> ><label for="Vet">Veterinarian? </label></td></tr>				
					</tbody>
			</table>
			</li>
			</ul>
			<input type="hidden" name="PeopleT_id" id="PeopleT_id" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="memCompanySuccess" data-dismissible="true" data-theme="a" data-position="#memInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $row[0]['FirstName'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="memCompanyError" data-dismissible="false" data-theme="a"  data-position="#memInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $row[0]['FirstName'] ?> failed, please try again later!</p>
			<p id="memCompanyErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>