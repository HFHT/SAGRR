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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec3,a.ApplSec5,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec3']);
		$applSec5 = json_decode($row[0]['ApplSec5']);
		// New form moved a question from section 5 to section 3, this will get the data from old form
		$applSec->{'Ans116'} = $applSec->{'Ans116'}=='' ? $applSec5->{'Ans42'} : $applSec->{'Ans116'};
		
		$applProgress = json_decode($row[0]['applProgress']);
		if (!is_array($applSec->{'Ans26'})) {
			$applSec->{'Ans26'} = array('','');
		}
		if (!is_array($applSec->{'Ans25'})) {
			$applSec->{'Ans25'} = array('','');
		}
		if (!is_array($applSec->{'Ans24'})) {
			$applSec->{'Ans24'} = array('0','18');
		}
		if (!is_array($applSec->{'Ans23a'})) {
			$applSec->{'Ans23a'} = array('','');
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
<div data-role="page" id="adoptS3" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="adoptS3SaveBtn" class="ApplE" style="display:none"><a href="#adoptS3" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="adoptS3Form" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[3]-> {'Sec'} ?></h3></li>	
			<li style="display:">					
				<p style="font-size:1em;white-space:pre-line;">Specify Adopt and/or Foster: </p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="checkbox" name="Ans23a" id="Ans23aA" value="A" <?php checkBox(in_array('A',$applSec->{'Ans23a'}));?>><label for="Ans23aA">Adopt</label>
						<input type="checkbox" name="Ans23a" id="Ans23aF" value="F" <?php checkBox(in_array('F',$applSec->{'Ans23a'}));?>><label for="Ans23aF">Foster</label>
					</fieldset>
					<div class="AnsErr23a"></div>
			</li>
			<li>
						<div style="display:inline-flex;">
						<label for="Ans23">Gender:&nbsp;&nbsp;</label>
						<select name="Ans23" id="Ans23" data-mini="true" data-iconpos="noicon">
							<?php
							$selOpt = ["M" => "Male", "F" => "Female", "PM" => "Prefer Male", "PF" => "Prefer Female", "X" => "No Preference"];
							dropDownAry($selOpt,$applSec->{'Ans23'},'');
							?>
						</select>
						</div>
				<div class="AnsErr23"></div>
			</li>
			<li>
						<div class="ui-field-contain">					
							<div data-role="rangeslider">
								<label for="Ans24L">Age Range:</label>								
								<input type="range" name="Ans24" id="Ans24L" min="0" max="18" value="<?php echo $applSec->{'Ans24'}[0];?>">
								<label for="Ans24H">Age Range (Years):</label>								
								<input type="range" name="Ans24" id="Ans24H" min="0" max="18" value="<?php echo $applSec->{'Ans24'}[1];?>">
							</div>
						</div>
				<div class="AnsErr24"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">What are your expectations of your golden?</p>			
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
					<input type="checkbox" name="Ans25" id="Ans25CO" value="CO" <?php checkBox(in_array('CO',$applSec->{'Ans25'}));?>><label for="Ans25CO">Companion</label>
					<input type="checkbox" name="Ans25" id="Ans25CC" value="CC" <?php checkBox(in_array('CC',$applSec->{'Ans25'}));?>><label for="Ans25CC">Child Companion</label>						
					<input type="checkbox" name="Ans25" id="Ans25HU" value="HU" <?php checkBox(in_array('HU',$applSec->{'Ans25'}));?>><label for="Ans25HU">Hunting</label>	
				</fieldset>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
					<input type="checkbox" name="Ans25" id="Ans25OB" value="OB" <?php checkBox(in_array('OB',$applSec->{'Ans25'}));?>><label for="Ans25OB">Obedience</label>				
					<input type="checkbox" name="Ans25" id="Ans25TD" value="TD" <?php checkBox(in_array('TD',$applSec->{'Ans25'}));?>><label for="Ans25TD">Therapy Dog</label>	
					<input type="checkbox" name="Ans25" id="Ans25SD" value="SD" <?php checkBox(in_array('SD',$applSec->{'Ans25'}));?>><label for="Ans25SD">Service Dog</label>								
				</fieldset>	
				<div class="AnsErr25"></div>
				<textarea name="Ans117" id="Ans117" data-mini="true"><?php echo $applSec->{'Ans117'}; ?></textarea>			
			</li>
			<li>						
				<p style="font-size:1em;white-space:pre-line;">What best describes what you are looking for in your golden?</p>			
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
					<input type="checkbox" name="Ans26" id="Ans26QU" value="QU" <?php checkBox(in_array('QU',$applSec->{'Ans26'}));?>><label for="Ans26QU">Quiet</label>
					<input type="checkbox" name="Ans26" id="Ans26GE" value="GE" <?php checkBox(in_array('GE',$applSec->{'Ans26'}));?>><label for="Ans26GE">Gentle</label>
					<input type="checkbox" name="Ans26" id="Ans26CO" value="CO" <?php checkBox(in_array('CO',$applSec->{'Ans26'}));?>><label for="Ans26CO">Companion</label>				
				</fieldset>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
					<input type="checkbox" name="Ans26" id="Ans26PL" value="PL" <?php checkBox(in_array('PL',$applSec->{'Ans26'}));?>><label for="Ans26PL">Playful</label>					
					<input type="checkbox" name="Ans26" id="Ans26EN" value="EN" <?php checkBox(in_array('EN',$applSec->{'Ans26'}));?>><label for="Ans26EN">Energetic</label>	
					<input type="checkbox" name="Ans26" id="Ans26BA" value="BA" <?php checkBox(in_array('BA',$applSec->{'Ans26'}));?>><label for="Ans26BA">Ball Playing</label>	
				</fieldset>	
				<div class="AnsErr26"></div>
				<textarea name="Ans118" id="Ans118" data-mini="true"><?php echo $applSec->{'Ans118'}; ?></textarea>							
			</li>
			<li><p style="font-size:1em;white-space:pre-line;">Would you be willing to adopt a Golden with:</p>
			<ul  data-role="listview" data-inset="true" style="margin-top:0">
			<li>
				<p style="font-size:1em;white-space:pre-line;">Minor health problems? (Valley fever, thyroid, allergies, arthritis, etc)</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans27" id="Ans27Y" value="Y" <?php checkBox($applSec->{'Ans27'}=='Y');?>><label for="Ans27Y">Yes</label>
						<input type="radio" name="Ans27" id="Ans27N" value="N" <?php checkBox($applSec->{'Ans27'}=='N');?>><label for="Ans27N">No</label>
						<input type="radio" name="Ans27" id="Ans27M" value="M" <?php checkBox($applSec->{'Ans27'}=='M');?>><label for="Ans27M">Maybe</label>
					</fieldset>
					<div class="AnsErr27"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Major health issues?< (Seizures, uveitis, orthopedic issues, tick fever, etc)/p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans28" id="Ans28Y" value="Y" <?php checkBox($applSec->{'Ans28'}=='Y');?>><label for="Ans28Y">Yes</label>
						<input type="radio" name="Ans28" id="Ans28N" value="N" <?php checkBox($applSec->{'Ans28'}=='N');?>><label for="Ans28N">No</label>
						<input type="radio" name="Ans28" id="Ans28M" value="M" <?php checkBox($applSec->{'Ans28'}=='M');?>><label for="Ans28M">Maybe</label>
					</fieldset>
					<div class="AnsErr28"></div>
			</li>
			<li>					
				<p style="font-size:1em;white-space:pre-line;">Special needs that are treatable?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans29" id="Ans29Y" value="Y" <?php checkBox($applSec->{'Ans29'}=='Y');?>><label for="Ans29Y">Yes</label>
						<input type="radio" name="Ans29" id="Ans29N" value="N" <?php checkBox($applSec->{'Ans29'}=='N');?>><label for="Ans29N">No</label>
						<input type="radio" name="Ans29" id="Ans29M" value="M" <?php checkBox($applSec->{'Ans29'}=='M');?>><label for="Ans29M">Maybe</label>
					</fieldset>
					<div class="AnsErr29"></div>
			</li>
			<li>					
				<p style="font-size:1em;white-space:pre-line;">Cancer?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans30" id="Ans30Y" value="Y" <?php checkBox($applSec->{'Ans30'}=='Y');?>><label for="Ans30Y">Yes</label>
						<input type="radio" name="Ans30" id="Ans30N" value="N" <?php checkBox($applSec->{'Ans30'}=='N');?>><label for="Ans30N">No</label>
						<input type="radio" name="Ans30" id="Ans30M" value="M" <?php checkBox($applSec->{'Ans30'}=='M');?>><label for="Ans30M">Maybe</label>
					</fieldset>
					<div class="AnsErr30"></div>
			</li>
			</ul>
			</li>
			<li>
			<p style="white-space:pre-line;float:left;">Rescue dogs come from a variety of backgrounds. As such, many come with behavior issues, some that are simple to eliminate and others that can be addressed with professional advice and training. We have selected and work with several trainers who we know will be able to help the dog using only positive methods.</p>
			<p style="white-space:pre-line;float:left;">You will always be advised of a dog's issues, of which we are aware, before you commit to adoption/foster.</p>
			</li>
			<li><p style="font-size:1em;white-space:pre-line;">Would you adopt a Golden:</p>
			<ul  data-role="listview" data-inset="true" style="margin-top:0">
			<li>					
				<p style="font-size:1em;white-space:pre-line;">That isn't house trained?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans31" id="Ans31Y" value="Y" <?php checkBox($applSec->{'Ans31'}=='Y');?>><label for="Ans31Y">Yes</label>
						<input type="radio" name="Ans31" id="Ans31N" value="N" <?php checkBox($applSec->{'Ans31'}=='N');?>><label for="Ans31N">No</label>
						<input type="radio" name="Ans31" id="Ans31M" value="M" <?php checkBox($applSec->{'Ans31'}=='M');?>><label for="Ans31M">Maybe</label>
					</fieldset>
					<div class="AnsErr31"></div>
			</li>
			<li>					
				<p style="font-size:1em;white-space:pre-line;">With simple behavior issues?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans114" id="Ans114Y" value="Y" <?php checkBox($applSec->{'Ans114'}=='Y');?>><label for="Ans114Y">Yes</label>
						<input type="radio" name="Ans114" id="Ans114N" value="N" <?php checkBox($applSec->{'Ans114'}=='N');?>><label for="Ans114N">No</label>
						<input type="radio" name="Ans114" id="Ans114M" value="M" <?php checkBox($applSec->{'Ans114'}=='M');?>><label for="Ans114M">Maybe</label>
					</fieldset>
					<div class="AnsErr114"></div>
			</li>
			<li>					
				<p style="font-size:1em;white-space:pre-line;">With behavior issues that can be controlled with proper training?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans115" id="Ans115Y" value="Y" <?php checkBox($applSec->{'Ans115'}=='Y');?>><label for="Ans115Y">Yes</label>
						<input type="radio" name="Ans115" id="Ans115N" value="N" <?php checkBox($applSec->{'Ans115'}=='N');?>><label for="Ans115N">No</label>
						<input type="radio" name="Ans115" id="Ans115M" value="M" <?php checkBox($applSec->{'Ans115'}=='M');?>><label for="Ans115M">Maybe</label>
					</fieldset>
					<div class="AnsErr115"></div>
			</li>
			</ul></li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Would you be willing to work with a SAGRR approved trainer?</p>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
						<input type="radio" name="Ans116" id="Ans116Y" value="Y" <?php checkBox($applSec->{'Ans116'}=='Y');?>><label for="Ans116Y">Yes</label>
						<input type="radio" name="Ans116" id="Ans116N" value="N" <?php checkBox($applSec->{'Ans116'}=='N');?>><label for="Ans116N">No</label>
						<input type="radio" name="Ans116" id="Ans116M" value="M" <?php checkBox($applSec->{'Ans116'}=='M');?>><label for="Ans116M">Maybe</label>
					</fieldset>
					<div class="AnsErrAns116"></div>
			</li>
			<li style="display:none;">					
				<p style="font-size:1em;white-space:pre-line;">How many Goldens are you looking to adopt/foster</p>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
					<input type="radio" name="Ans31a" id="Ans31a1" value="1" <?php checkBox($applSec->{'Ans31a'}=='1');?>><label for="Ans31a1">One</label>
					<input type="radio" name="Ans31a" id="Ans31a2" value="2" <?php checkBox($applSec->{'Ans31a'}=='2');?>><label for="Ans31a2">Pair</label>
					<input type="radio" name="Ans31a" id="Ans31a3" value="3" <?php checkBox($applSec->{'Ans31a'}=='3');?>><label for="Ans31a3">Three</label>				
					<input type="radio" name="Ans31a" id="Ans31a4" value="4" <?php checkBox($applSec->{'Ans31a'}=='4');?>><label for="Ans31a4">Four+</label>				
				</fieldset>
					<div class="AnsErr31a"></div>
			</li>
			
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptS3Success" data-dismissible="true" data-theme="a" data-position="#adoptS3Form">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptS3Error" data-dismissible="false" data-theme="a"  data-position="#adoptS3Form">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptS3ErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>