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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec4,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec4']);
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
<div data-role="page" id="adoptS4" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="adoptS4SaveBtn" class="ApplE" style="display:none"><a href="#adoptS4" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="adoptS4Form" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[4]-> {'Sec'} ?></h3></li>	
			<li>
				<p style="font-size:1em;white-space:pre-line;">Who will be the primary care giver for your Golden?</p>
				<textarea name="Ans32" id="Ans32" data-mini="true"><?php echo $applSec->{'Ans32'}; ?></textarea>
				<div class="AnsErr32"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">How long will the Golden be left alone on a daily basis?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans33" id="Ans332" value="0-4" <?php checkBox($applSec->{'Ans33'}=='0-4');?>><label for="Ans332">0-4</label>
						<input type="radio" name="Ans33" id="Ans334" value="4-6" <?php checkBox($applSec->{'Ans33'}=='4-6');?>><label for="Ans334">4-6</label>
						<input type="radio" name="Ans33" id="Ans336" value="6-8" <?php checkBox($applSec->{'Ans33'}=='6-8');?>><label for="Ans336">6-8</label>
						<input type="radio" name="Ans33" id="Ans338" value="8-10" <?php checkBox($applSec->{'Ans33'}=='8-10');?>><label for="Ans338">8-10</label>
						<input type="radio" name="Ans33" id="Ans3310" value="10+" <?php checkBox($applSec->{'Ans33'}=='10+');?>><label for="Ans3310">10+</label>						
					</fieldset>	
					<div class="AnsErr33"></div>					
				<p style="font-size:1em;white-space:pre-line;clear:both;">Where will the Golden stay during this time?</p>
				<textarea name="Ans34" id="Ans34" data-mini="true"><?php echo $applSec->{'Ans34'}; ?></textarea>
				<div class="AnsErr34"></div>
			</li><li><p  style="font-size:1em;white-space:pre-line;clear:both;">Will the golden:</p>
				<ul  data-role="listview" data-inset="true" style="margin-top:0">
				<li>
				<p style="font-size:1em;white-space:pre-line;clear:both;">Be left outside or in the garage while you are away?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans35" id="Ans35Y" value="Y" <?php checkBox($applSec->{'Ans35'}=='Y');?>><label for="Ans35Y">Yes</label>
						<input type="radio" name="Ans35" id="Ans35N" value="N" <?php checkBox($applSec->{'Ans35'}=='N');?>><label for="Ans35N">No</label>
					</fieldset>
					<div class="AnsErr35"></div>
				</li>
				<li>
				<p style="font-size:1em;white-space:pre-line;clear:both;">Be an outside dog?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans36" id="Ans36Y" value="Y" <?php checkBox($applSec->{'Ans36'}=='Y');?>><label for="Ans36Y">Yes</label>
						<input type="radio" name="Ans36" id="Ans36N" value="N" <?php checkBox($applSec->{'Ans36'}=='N');?>><label for="Ans36N">No</label>
					</fieldset>	
					<div class="AnsErr36"></div>	
				</li>
				<li>
				<p style="font-size:1em;white-space:pre-line;clear:both;">Be chained outside?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans37" id="Ans37Y" value="Y" <?php checkBox($applSec->{'Ans37'}=='Y');?>><label for="Ans37Y">Yes</label>
						<input type="radio" name="Ans37" id="Ans37N" value="N" <?php checkBox($applSec->{'Ans37'}=='N');?>><label for="Ans37N">No</label>
					</fieldset>
					<div class="AnsErr37"></div>
				</li>
				<li>
				<p style="font-size:1em;white-space:pre-line;">Sleep inside, outside, or both?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans38" id="Ans38Y" value="I" <?php checkBox($applSec->{'Ans38'}=='I');?>><label for="Ans38Y">Inside</label>
						<input type="radio" name="Ans38" id="Ans38N" value="O" <?php checkBox($applSec->{'Ans38'}=='O');?>><label for="Ans38N">Outside</label>
						<input type="radio" name="Ans38" id="Ans38B" value="B" <?php checkBox($applSec->{'Ans38'}=='B');?>><label for="Ans38B">Both</label>
					</fieldset>
					<div class="AnsErr38"></div>
				</li>
				</ul>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">When you travel, how will the Golden be cared for?</p>
				<textarea name="Ans41" id="Ans41" data-mini="true"><?php echo $applSec->{'Ans41'}; ?></textarea>
				<div class="AnsErr41"></div>
			</li>
			
			<li>					
				<p style="font-size:1em;white-space:pre-line;">Wil you exercise the Golden regularly?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans39" id="Ans39Y" value="Y" <?php checkBox($applSec->{'Ans39'}=='Y');?>><label for="Ans39Y">Yes</label>
						<input type="radio" name="Ans39" id="Ans39N" value="N" <?php checkBox($applSec->{'Ans39'}=='N');?>><label for="Ans39N">No</label>
					</fieldset>				
					<div class="AnsErr39"></div>
				<textarea name="Ans119" id="Ans119" data-mini="true" placeholder="If yes, how?"><?php echo $applSec->{'Ans119'}; ?></textarea>
			</li>
			<li>					
				<p style="font-size:1em;white-space:pre-line;">Are you aware Goldens are considered a large breed and will require maintenance costs higher than small-to-medium sized dogs, including: food, vet, grooming, etc</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans40" id="Ans40Y" value="Y" <?php checkBox($applSec->{'Ans40'}=='Y');?>><label for="Ans40Y">Yes</label>
						<input type="radio" name="Ans40" id="Ans40N" value="N" <?php checkBox($applSec->{'Ans40'}=='N');?>><label for="Ans40N">No</label>
						<input type="radio" name="Ans40" id="Ans40X" value="X" <?php checkBox($applSec->{'Ans40'}=='X');?>><label for="Ans40X">Wasn't aware</label>
					</fieldset>
					<div class="AnsErr40"></div>
			</li>			
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptS4Success" data-dismissible="true" data-theme="a" data-position="#adoptS4Form">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptS4Error" data-dismissible="false" data-theme="a"  data-position="#adoptS4Form">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptS4ErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>