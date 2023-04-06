<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';
//	$formData = json_decode($_POST['formData']);

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
$today = date("Y-m-d");
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec0,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec0'] !== '' ? $row[0]['ApplSec0']  : '{"MemName":[""],"MemAge":[""],"PetType":["0"],"PetSex":["0"],"PetAge":["0"],"PetHome":["0"],"Ans2":"","Ans3":"","Ans4":"","Ans5":""}');
		$applProgress = json_decode($row[0]['applProgress']);
	} else {
		$error = true;
	}
} else {
	$error = true;
}
function checkBox($decision)
{
	if ($decision == 1) {
		echo ('checked');
	}
}
function dropDownAry($SelAry, $Selected, $SelText)
{
	echo "<option value='0'>-$SelText-</option>";
	foreach ($SelAry as $key => $value) {
		if ($Selected == $value) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='" . $value . "' " . $strSel . ">" . $value . "</option>";
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<?php
	var_dump($row, $applSec, $applProgress);
	?>
</head>

<body>
	<div data-role="page" id="adoptS0" data-dom-cache="false">
		<script>
		</script>
		<div data-role="header" data-position="fixed">
			<img src="../img/SAGGR-DBLogo1hdr-320.gif">
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>
					<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>
					<li id="adoptS0SaveBtn" class="ApplE" style="display:none"><a href="#adoptS0" data-icon="action" class="myNav">Save</a></li>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /header -->

		<div data-role="main" class="ui-content my-narrow">
			<form id="adoptS0Form" class="forms">
				<ul data-role="listview" data-autodividers="false" data-filter="false">
					<li data-role="list-divider" role="heading" style="font-size:initial;">
						<h3><?php echo $applProgress[0]->{'Sec'} ?></h3>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">List other people, including children, who will be living with the dog. Include their name and age.</p>
						<div class="ui-grid-a" id="tblPerson">
							<?php
							for ($x = 0; $x < 9; $x++) {
								if ($x > 2) {
									$display = $applSec->{'MemName'}[$x] == '' ? 'style="display:none"' : $display = '';
								}
							?>
								<div class="htmlPeople" <?php echo $display; ?>>
									<div class="ui-block-a" style="width:70%">
										<div class="ui-bar ui-bar-a">
											<input type="text" name="MemName" id="MemName" size="15" class="my-input-text" placeholder="Name..." value="<?php echo $applSec->{'MemName'}[$x]; ?>" data-mini="true">
										</div>
									</div>
									<div class="ui-block-b" style="width:30%">
										<div class="ui-bar ui-bar-a">
											<input type="number" name="MemAge" id="MemAge" size="3" class="my-input-text" placeholder="Age" value="<?php echo $applSec->{'MemAge'}[$x]; ?>" data-mini="true">
										</div>
									</div>
								</div>
							<?php
							}
							?>
						</div>
						<div class="ui-grid-solo" id="addPerson">
							<div class="ui-block-a" style="width:100%">
								<div class="ui-bar ui-bar-a"><input type="button" value="Click to Add Person" class="addPerson" data-mini="true"></div>
							</div>
						</div>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">List other pets <u>currently</u> living in your home. Include their gender, age, and breed/species.</p>
						<table id="tblPet" style="background-color:rgb(223,223,223);">
							<thead>
								<tr>
									<th width="25%">Pet</th>
									<th width="25%">Sex</th>
									<th width="25%">Age</th>
									<th width="25%">Breed</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$rd = 0;
								for ($x = 0; $x < 12; $x++) {
									$display = '';
									if ($rd > 2) {
										if ($applSec->{'PetType'}[$x] == '') {
											$display = 'style="display:none"';
										} else {
											$display = '';
										}
										//						$display=$applSec->{'PetType'}[$x]==''?'style="display:none"':$display='';
									}
									if ($applSec->{'PetHome'}[$x] == 'No') {
										//						$display='style="display:none;" data-x="'.$rd.'"';
										$display = 'style="display:none;"';
									} else {
										$rd++;
									}
									if ($applSec->{'PetHome'}[$x] == '' or $applSec->{'PetHome'}[$x] == '0') {
										$applSec->{'PetHome'}[$x] = 'Yes';
									}
								?>
									<tr <?php echo $display; ?>>
										<td>
											<select name="PetType" id="PetType" data-mini="true" data-iconpos="noicon">
												<?php
												$selOpt = ["Dog" => "Dog", "SmallDog" => "SmallDog", "Cat" => "Cat", "Bird" => "Bird", "Horse" => "Horse", "Other" => "Other"];
												dropDownAry($selOpt, $applSec->{'PetType'}[$x], '');
												?>
											</select>
										</td>
										<td>
											<select name="PetSex" id="PetSex" data-mini="true" data-iconpos="noicon">
												<?php
												$selOpt = ["M" => "Male", "F" => "Female"];
												dropDownAry($selOpt, $applSec->{'PetSex'}[$x], '');
												?>
											</select>
										</td>
										<td>
											<select name="PetAge" id="PetAge" data-mini="true" data-iconpos="noicon">
												<?php
												$selOpt = ["P" => "Puppy", "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10+"];
												dropDownAry($selOpt, $applSec->{'PetAge'}[$x], '');
												?>
											</select>
										</td>
										<td>
											<input type="text" name="PetBreed" id="PetBreed" size="25" class="my-input-text" placeholder="Breed..." value="<?php echo $applSec->{'PetBreed'}[$x]; ?>" data-mini="true">
											<input type="hidden" name="PetHome" id="PetHome" value="<?php echo $applSec->{'PetHome'}[$x]; ?>">
											<!--				<select name="PetHome" id="PetHome" data-mini="true" data-iconpos="noicon">
						<?php
									//						$selOpt = ["Y" => "Yes", "N" => "No"];
									//						dropDownAry($selOpt,$applSec->{'PetHome'}[$x],'');
						?>
					</select>	-->
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<div class="ui-grid-solo" id="addPetBtn">
							<div class="ui-block-a" style="width:100%">
								<div class="ui-bar ui-bar-a"><input type="button" value="Click to Add Pet" class="addPet" data-mini="true"></div>
							</div>
						</div>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">Were all of the animals listed above spayed/neutered? If not, please explain:</p>
						<textarea name="Ans2" id="Ans2" data-mini="true"><?php echo $applSec->{'Ans2'}; ?></textarea>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">Is anyone in your family allergic to dogs or cats?</p>
						<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
							<input type="radio" name="Ans4" id="Ans4Y" value="Y" <?php checkBox($applSec->{'Ans4'} == 'Y'); ?>><label for="Ans4Y">Yes</label>
							<input type="radio" name="Ans4" id="Ans4N" value="N" <?php checkBox($applSec->{'Ans4'} == 'N'); ?>><label for="Ans4N">No</label>
							<input type="radio" name="Ans4" id="Ans4U" value="U" <?php checkBox($applSec->{'Ans4'} == 'U'); ?>><label for="Ans4U">Unknown</label>
						</fieldset>
						<div class="Ans4Err"></div>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">Do you own livestock? If yes, what kind:</p>
						<textarea name="Ans3" id="Ans3" data-mini="true"><?php echo $applSec->{'Ans3'}; ?></textarea>
					</li>
					<li style="display:none;">
						<p style="font-size:1em;white-space:pre-line;">Are you aware Golden's shed?</p>
						<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
							<input type="radio" name="Ans5" id="Ans5Y" value="Y" <?php checkBox($applSec->{'Ans5'} == 'Y'); ?>><label for="Ans5Y">Yes</label>
							<input type="radio" name="Ans5" id="Ans5N" value="N" <?php checkBox($applSec->{'Ans5'} == 'N'); ?>><label for="Ans5N">No</label>
							<input type="radio" name="Ans5" id="Ans5U" value="U" <?php checkBox($applSec->{'Ans5'} == 'U'); ?>><label for="Ans5U">Wasn't aware</label>
						</fieldset>
						<div class="Ans5Err"></div>
					</li>
				</ul>
				<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
				<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->
			</form>
		</div><!-- /content -->

		<div data-role="footer" data-position="fixed">
			<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
		</div><!-- /footer -->

		<div data-role="popup" id="adoptS0Success" data-dismissible="true" data-theme="a" data-position="#memInfoTab">
			<div data-role="header" data-theme="a">
				<h1>Update Successful</h1>
			</div>
			<div data-theme="a">
				<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
				<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>
			</div>
		</div> <!-- /popup -->

		<div data-role="popup" id="adoptS0Error" data-dismissible="false" data-theme="a" data-position="#memInfoTab">
			<div data-role="header" data-theme="b">
				<h1>Update Failed!</h1>
			</div>
			<div data-theme="a">
				<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
				<p id="adoptS0ErrorText" class="myErrMsg"></p>
				<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
			</div>
		</div> <!-- /popup -->
	</div><!-- /page -->

</body>

</html>