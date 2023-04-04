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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec5,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec5']);
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
<div data-role="page" id="adoptS5" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="adoptS5SaveBtn" class="ApplE" style="display:none"><a href="#adoptS5" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="adoptS5Form" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[5]-> {'Sec'} ?></h3></li>
			<li style="display:none;>
				<p style="font-size:1em;white-space:pre-line;">Upon recommendation from SAGRR, are you willing to use a recommended dog trainer for your Golden?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans42" id="Ans42Y" value="Y" <?php checkBox($applSec->{'Ans42'}=='Y');?>><label for="Ans42Y">Yes</label>
						<input type="radio" name="Ans42" id="Ans42N" value="N" <?php checkBox($applSec->{'Ans42'}=='N');?>><label for="Ans42N">No</label>
					</fieldset>
					<div class="AnsErr42"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Are you willing, if recommended, to crate train you golden?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans43" id="Ans43Y" value="Y" <?php checkBox($applSec->{'Ans43'}=='Y');?>><label for="Ans43Y">Yes</label>
						<input type="radio" name="Ans43" id="Ans43N" value="N" <?php checkBox($applSec->{'Ans43'}=='N');?>><label for="Ans43N">No</label>
					</fieldset>
					<div class="AnsErr43"></div>
			<ul  data-role="listview" data-inset="true" style="margin-top:0">				
			<li>
				<p style="font-size:1em;white-space:pre-line;">If no, are you willing to use other methods to contain the dog, e.g. a baby gate?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans120" id="Ans120Y" value="Y" <?php checkBox($applSec->{'Ans120'}=='Y');?>><label for="Ans120Y">Yes</label>
						<input type="radio" name="Ans120" id="Ans120N" value="N" <?php checkBox($applSec->{'Ans120'}=='N');?>><label for="Ans120N">No</label>
					</fieldset>
					<div class="AnsErr43"></div>
			</li>
			</ul>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Do you own a crate or baby gate?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans44" id="Ans44Y" value="Y" <?php checkBox($applSec->{'Ans44'}=='Y');?>><label for="Ans44Y">Yes</label>
						<input type="radio" name="Ans44" id="Ans44N" value="N" <?php checkBox($applSec->{'Ans44'}=='N');?>><label for="Ans44N">No</label>
					</fieldset>
					<div class="AnsErr44"></div>
			<ul  data-role="listview" data-inset="true" style="margin-top:0">		
			<li>
				<p style="font-size:1em;white-space:pre-line;">If no, are you willing to purchase a crate or baby gate?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans45" id="Ans45Y" value="Y" <?php checkBox($applSec->{'Ans45'}=='Y');?>><label for="Ans45Y">Yes</label>
						<input type="radio" name="Ans45" id="Ans45N" value="N" <?php checkBox($applSec->{'Ans45'}=='N');?>><label for="Ans45N">No</label>
					</fieldset>
					<div class="AnsErr45"></div>
			</li>
			</ul>
			</li>
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptS5Success" data-dismissible="true" data-theme="a" data-position="#adoptS5Form">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptS5Error" data-dismissible="false" data-theme="a"  data-position="#adoptS5Form">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptS5ErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>