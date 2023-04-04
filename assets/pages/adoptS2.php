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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec2,a.ApplSec0,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec2']);
		$applSec0 = json_decode($row[0]['ApplSec0']);		
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
<div data-role="page" id="adoptS2" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="adoptS2SaveBtn" class="ApplE" style="display:none"><a href="#adoptS2" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="adoptS2Form" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[2]-> {'Sec'} ?></h3></li>
			<li>
				<table style="table-layout:fixed;width:100%;"><tbody>
					<tr style="display:none;"><td style="padding-bottom:0;white-space:pre-line;" width="70%">Have you owned dogs before?
						<div class="AnsErr16"></div>					
					</td><td style="padding-bottom:0;" width="30%">
						<fieldset id="Ans16" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
							<input type="radio" name="Ans16" id="Ans16Y" value="Y" <?php checkBox($applSec->{'Ans16'}=='Y');?>><label for="Ans16Y" class="my-pad-8">Yes</label>
							<input type="radio" name="Ans16" id="Ans16N" value="N" <?php checkBox($applSec->{'Ans16'}=='N');?>><label for="Ans16N" class="my-pad-8">No</label>
						</fieldset>
					</td></tr>
					<tr class="Ans16Hide" <?php hide($applSec->{'Ans16'}=='N');?>><td style="padding-top:0;padding-bottom:0;white-space:pre-line;">Have you previously owned a Golden?			
						<div class="AnsErr17"></div>
					</td><td style="padding-top:0;padding-bottom:0;">
						<fieldset class="Ans16Clear" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
							<input type="radio" name="Ans17" id="Ans17Y" value="Y" <?php checkBox($applSec->{'Ans17'}=='Y');?>><label for="Ans17Y" class="my-pad-8">Yes</label>
							<input type="radio" name="Ans17" id="Ans17N" value="N" <?php checkBox($applSec->{'Ans17'}=='N');?>><label for="Ans17N" class="my-pad-8">No</label>
						</fieldset>
					</td></tr>
				</tbody></table>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Why do you want to adopt a Golden?</p>			
				<textarea name="Ans18" id="Ans18" data-mini="true"><?php echo $applSec->{'Ans18'}; ?></textarea>
				<div class="AnsErr18"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Why do you want to adopt through a rescue organization?</p>			
				<textarea name="Ans19" id="Ans19" data-mini="true"><?php echo $applSec->{'Ans19'}; ?></textarea>
				<div class="AnsErr19"></div>
			</li>
			<li>
				<p style="font-size:1em;white-space:pre-line;">Have you adopted through a rescue organization before?</p>			
				<fieldset id="Ans112" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
					<input type="radio" name="Ans112" id="Ans112Y" value="Y" <?php checkBox($applSec->{'Ans112'}=='Y');?>><label for="Ans112Y" class="my-pad-8">Yes</label>
					<input type="radio" name="Ans112" id="Ans112N" value="N" <?php checkBox($applSec->{'Ans112'}=='N');?>><label for="Ans112N" class="my-pad-8">No</label>
				</fieldset>
				<div class="Ans112Hide" <?php hide($applSec->{'Ans112'}=='N' or $applSec->{'Ans112'}=='');?>>
					<p style="font-size:1em;white-space:pre-line;">Please provide the name of the rescue and date:</p>							
					<textarea class="Ans112Clear" name="Ans113" id="Ans113" data-mini="true"><?php echo $applSec->{'Ans113'}; ?></textarea>				
					<div class="AnsErr113"></div>																										
				</div>
			</li>
			<li>
				<table><tbody>
					<tr><td colspan="2" style="padding-bottom:0;"><p style="font-size:1em;white-space:pre-line;">If you currently have pets, or have owned a pet before, may we contact your veterinarian?</p></td></tr>			
					<tr><td colspan="2" style="padding-bottom:0;">
						<fieldset id="Ans20" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
							<input type="radio" name="Ans20" id="Ans20Y" value="Y" <?php checkBox($applSec->{'Ans20'}=='Y');?>><label for="Ans20Y">Yes</label>
							<input type="radio" name="Ans20" id="Ans20N" value="N" <?php checkBox($applSec->{'Ans20'}=='N');?>><label for="Ans20N">No</label>
							<input type="radio" name="Ans20" id="Ans20U" value="U" <?php checkBox($applSec->{'Ans20'}=='U');?>><label for="Ans20U">None</label>							
						</fieldset>
						<div class="AnsErr20"></div>
					</td></tr>
					<tr class="Ans20Hide" <?php hide($applSec->{'Ans20'}=='N');?>><td style="padding-top:0;padding-bottom:0;">Name:			
					</td><td style="padding-top:0;padding-bottom:0;">
						<input type="text" name="Ans21" id="Ans21" size = "20" class="my-input-text Ans20Clear" placeholder="Veterinarian..." value="<?php echo $applSec->{'Ans21'}; ?>" data-mini="true">
						<div class="AnsErr21"></div>
					</td></tr>
					<tr class="Ans20Hide" <?php hide($applSec->{'Ans20'}=='N');?>><td style="padding-top:0;padding-bottom:0;">Phone:			
					</td><td style="padding-top:0;padding-bottom:0;">
						<input type="tel" name="Ans22" id="Ans22" size = "20" class="my-input-text Ans20Clear" placeholder="(   )___-___"  value="<?php echo $applSec->{'Ans22'}; ?>" pattern="^(?:\([2-9]\d{2}\)\ ?|[2-9]\d{2}(?:\-?|\ ?))[2-9]\d{2}[- ]?\d{4}$" data-mini="true">
						<div class="AnsErr22"></div>
					</td></tr>
				</tbody></table>
			</li>
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
		</form>
		<form id="adoptS02Form" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">				
					<?php for ($x=0; $x<9; $x++) { ?>
					<input type="hidden" name="MemName" id="MemName" value="<?php echo $applSec0->{'MemName'}[$x] ?>">
					<input type="hidden" name="MemAge" id="MemAge" value="<?php echo $applSec0->{'MemAge'}[$x] ?>">
					<?php if ($x>0 and $applSec0->{'MemName'}[$x]=='') {break;}					
					} ?>
			<li>
				<p style="font-size:1em;white-space:pre-line;">List other pets you have previously owned. Include their gender, age, and breed/species.</p>
				<table id="tblPet" style="background-color:rgb(223,223,223);"><thead>
				<tr><th width="25%">Pet</th><th width="25%">Sex</th><th width="25%">Age</th><th width="25%">Breed</th></tr>
				</thead>
				<tbody>
				<?php
				$rd = 0;				
				for ($x=0; $x<12; $x++) {
					$display='';					
					if ($rd>2) {
						if ($applSec0->{'PetType'}[$x]=='') {
							$display='style="display:none"';
						} else {
							$display='';
						}
//						$display=$applSec0->{'PetType'}[$x]==''?'style="display:none"':$display='';
					}
					if ($applSec0->{'PetHome'}[$x]=='Yes') {
//						$display='style="display:none;" data-x="'.$rd.'"';
						$display='style="display:none;"';
					} else {
						$rd++;
					}							
					if ($applSec0->{'PetHome'}[$x]=='' or $applSec0->{'PetHome'}[$x]=='0') {
						$applSec0->{'PetHome'}[$x]='No';
					}
				?>
				<tr <?php echo $display;?>><td>
					<select name="PetType" id="PetType" data-mini="true" data-iconpos="noicon">
						<?php
						$selOpt = ["Dog" => "Dog", "SmallDog" => "SmallDog", "Cat" => "Cat", "Bird" => "Bird", "Horse" => "Horse", "Other" => "Other"];
						dropDownAry($selOpt,$applSec0->{'PetType'}[$x],'');
						?>
					</select>				
				</td><td>
					<select name="PetSex" id="PetSex" data-mini="true" data-iconpos="noicon">
						<?php
						$selOpt = ["M" => "Male", "F" => "Female"];
						dropDownAry($selOpt,$applSec0->{'PetSex'}[$x],'');
						?>
					</select>				
				</td><td>
					<select name="PetAge" id="PetAge" data-mini="true" data-iconpos="noicon">
						<?php
						$selOpt = ["P"=>"Puppy","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10+"];
						dropDownAry($selOpt,$applSec0->{'PetAge'}[$x],'');
						?>
					</select>				
				</td><td>
					<input type="text" name="PetBreed" id="PetBreed" size = "25" class="my-input-text" placeholder="Breed..." value="<?php echo $applSec0->{'PetBreed'}[$x]; ?>" data-mini="true">			
					<input type="hidden" name="PetHome" id="PetHome" value="<?php echo $applSec0->{'PetHome'}[$x]; ?>">
<!--				<select name="PetHome" id="PetHome" data-mini="true" data-iconpos="noicon">
						<?php
//						$selOpt = ["Y" => "Yes", "N" => "No"];
//						dropDownAry($selOpt,$applSec0->{'PetHome'}[$x],'');
						?>
					</select>	-->			
				</td></tr>
				<?php } ?>
				</tbody></table>
				<div class="ui-grid-solo" id="addPetBtn">
					<div class="ui-block-a" style="width:100%"><div class="ui-bar ui-bar-a"><input type="button" value="Click to Add Pet" class="addPet" data-mini="true"></div></div>
				</div>
			</li>	
					<input type="hidden" name="Ans2" id="Ans2" value="<?php echo $applSec0->{'Ans2'} ?>">
					<input type="hidden" name="Ans3" id="Ans3" value="<?php echo $applSec0->{'Ans3'} ?>">
					<input type="hidden" name="Ans4" id="Ans4" value="<?php echo $applSec0->{'Ans4'} ?>">
					<input type="hidden" name="Ans5" id="Ans5" value="<?php echo $applSec0->{'Ans5'} ?>">
			</ul>
		</form>

	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptS2Success" data-dismissible="true" data-theme="a" data-position="#adoptS2Form">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptS2Error" data-dismissible="false" data-theme="a"  data-position="#adoptS2Form">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptS2ErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>