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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec7,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec7']);
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

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="adoptS7" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="adoptS7SaveBtn" class="ApplE" style="display:none"><a href="#adoptS7" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="adoptS7Form" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[7]-> {'Sec'} ?></h3></li>
			<li style="display:none;">
				<p style="font-size:1em;white-space:pre-line;">Do you understand that SAGRR place rescues with their best match and not by order of applicant?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans47" id="Ans47Y" value="Y" <?php checkBox($applSec->{'Ans47'}=='Y');?>><label for="Ans47Y">Yes</label>
						<input type="radio" name="Ans47" id="Ans47N" value="N" <?php checkBox($applSec->{'Ans47'}=='N');?>><label for="Ans47N">No</label>
						<input type="radio" name="Ans47" id="Ans47U" value="U" <?php checkBox($applSec->{'Ans47'}=='U');?>><label for="Ans47U">Wasn't aware</label>						
					</fieldset>
					<div class="AnsErr47"></div>
			</li>
			<li style="display:none;">
				<p style="font-size:1em;white-space:pre-line;">Would you like to become involved with SAGRR?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans48" id="Ans48Y" value="Y" <?php checkBox($applSec->{'Ans48'}=='Y');?>><label for="Ans48Y">Yes</label>
						<input type="radio" name="Ans48" id="Ans48N" value="N" <?php checkBox($applSec->{'Ans48'}=='N');?>><label for="Ans48N">No</label>
					</fieldset>
					<div class="AnsErr48"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Are you interested in volunteering with SAGRR?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans49" id="Ans49Y" value="Y" <?php checkBox($applSec->{'Ans49'}=='Y');?>><label for="Ans49Y">Yes</label>
						<input type="radio" name="Ans49" id="Ans49N" value="N" <?php checkBox($applSec->{'Ans49'}=='N');?>><label for="Ans49N">No</label>
					</fieldset>
					<div class="AnsErr49"></div>
			</li>
			<li>					
				<p style="font-size:1em;white-space:pre-line;">Are you interested in becoming a SAGRR foster parent?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans50" id="Ans50Y" value="Y" <?php checkBox($applSec->{'Ans50'}=='Y');?>><label for="Ans50Y">Yes</label>
						<input type="radio" name="Ans50" id="Ans50N" value="N" <?php checkBox($applSec->{'Ans50'}=='N');?>><label for="Ans50N">No</label>
					</fieldset>
					<div class="AnsErr50"></div>
			<ul  data-role="listview" data-inset="true" style="margin-top:0">
			<li>
				<p style="font-size:1em;white-space:pre-line;">Were you aware that foster parents may adopt their foster Golden?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans51" id="Ans51Y" value="Y" <?php checkBox($applSec->{'Ans51'}=='Y');?>><label for="Ans51Y">Yes</label>
						<input type="radio" name="Ans51" id="Ans51N" value="N" <?php checkBox($applSec->{'Ans51'}=='N');?>><label for="Ans51N">No</label>
						<input type="radio" name="Ans51" id="Ans51U" value="U" <?php checkBox($applSec->{'Ans51'}=='U');?>><label for="Ans51U">Wasn't aware</label>												
					</fieldset>
					<div class="AnsErr51"></div>
			</li>
			</ul>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">How did you learn about SAGRR:</p>
				<textarea name="Ans52" id="Ans52" data-mini="true"><?php echo $applSec->{'Ans52'}; ?></textarea>
				<div class="AnsErr52"></div>
			</li>
			
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptS7Success" data-dismissible="true" data-theme="a" data-position="#adoptS7Form">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptS7Error" data-dismissible="false" data-theme="a"  data-position="#adoptS7Form">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptS7ErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>