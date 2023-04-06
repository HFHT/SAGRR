<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
$today = date("Y-m-d");
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec1,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec1']);
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
function hide($decision)
{
	if ($decision == 1) {
		echo ('style="display:none"');
	}
}
function dropDownAry($SelAry, $Selected, $SelText)
{
	echo "<option value='0'>-$SelText-</option>";
	foreach ($SelAry as $key => $value) {
		if ($Selected == $key) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='" . $key . "' " . $strSel . ">" . $value . "</option>";
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<?php
	var_dump($row, $applSec);
	?>
</head>

<body>
	<div data-role="page" id="adoptS1" data-dom-cache="false">
		<script>
		</script>
		<div data-role="header" data-position="fixed">
			<img src="../img/SAGGR-DBLogo1hdr-320.gif">
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>
					<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>
					<li id="adoptS1SaveBtn" class="ApplE" style="display:none"><a href="#adoptS1" data-icon="action" class="myNav">Save</a></li>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /header -->

		<div data-role="main" class="ui-content my-narrow">
			<form id="adoptS1Form" class="forms">
				<ul data-role="listview" data-autodividers="false" data-filter="false">
					<li data-role="list-divider" role="heading" style="font-size:initial;">
						<h3><?php echo $applProgress[1]->{'Sec'} ?></h3>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">What type of home do you live in?</p>
						<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
							<input type="radio" name="Ans6" id="Ans6S" value="Single" <?php checkBox($applSec->{'Ans6'} == 'Single'); ?>><label for="Ans6S" class="my-pad-7">Single Family</label>
							<input type="radio" name="Ans6" id="Ans6C" value="Condo" <?php checkBox($applSec->{'Ans6'} == 'Condo'); ?>><label for="Ans6C">Condo</label>
							<input type="radio" name="Ans6" id="Ans6M" value="Mobile" <?php checkBox($applSec->{'Ans6'} == 'Mobile'); ?>><label for="Ans6M">Mobile</label>
							<input type="radio" name="Ans6" id="Ans6A" value="Apartment" <?php checkBox($applSec->{'Ans6'} == 'Apartment'); ?>><label for="Ans6A" class="my-pad-7">Apartment</label>
						</fieldset>
						<div class="AnsErr6"></div>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">Do you own your home or rent?</p>
						<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
							<input type="radio" name="Ans7" id="Ans7O" value="Own" <?php checkBox($applSec->{'Ans7'} == 'Own'); ?>><label for="Ans7O">Own</label>
							<input type="radio" name="Ans7" id="Ans7R" value="Rent" <?php checkBox($applSec->{'Ans7'} == 'Rent'); ?>><label for="Ans7R">Rent</label>
						</fieldset>
						<p class="AnsErr7"></p>
						<p style="white-space:pre-line;float:left;">If you rent, an approval letter from the landlord stating they will accept a dog over 25lbs will be needed in order for your application to be processed.</p>
					</li>
					<li style="display:none;">
						<p style="font-size:1em;white-space:pre-line;">If the home is not in your name please provide name of owner/lessee?</p>
						<textarea name="Ans8" id="Ans8" data-mini="true"><?php echo $applSec->{'Ans8'}; ?></textarea>
						<div class="AnsErr8"></div>
					</li>
					<li>
						<table width="100%">
							<tbody>
								<tr>
									<td style="padding-bottom:0;" width="50%" wrap>Do you have a dog door?
										<div class="AnsErr9"></div>
									</td>
									<td style="padding-bottom:0;" width="50%">
										<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
											<input type="radio" name="Ans9" id="Ans9Y" value="Y" <?php checkBox($applSec->{'Ans9'} == 'Y'); ?>><label for="Ans9Y" class="my-pad-8">Yes</label>
											<input type="radio" name="Ans9" id="Ans9N" value="N" <?php checkBox($applSec->{'Ans9'} == 'N'); ?>><label for="Ans9N" class="my-pad-8">No</label>
										</fieldset>
									</td>
								</tr>
								<tr>
									<td style="padding-top:0;padding-bottom:0;" wrap>Is your yard fenced?
										<div class="AnsErr12"></div>
									</td>
									<td style="padding-top:0;padding-bottom:0;">
										<fieldset id="Ans12" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
											<input type="radio" name="Ans12" id="Ans12Y" value="Y" <?php checkBox($applSec->{'Ans12'} == 'Y'); ?>><label for="Ans12Y" class="my-pad-8">Yes</label>
											<input type="radio" name="Ans12" id="Ans12N" value="N" <?php checkBox($applSec->{'Ans12'} == 'N'); ?>><label for="Ans12N" class="my-pad-8">No</label>
										</fieldset>
									</td>
								</tr>
								<tr class="Ans12Hide" <?php hide($applSec->{'Ans12'} == 'N'); ?>>
									<td colspan="2" style="padding-left:1.5em; padding-top:0;padding-bottom:0;">
										<p style="font-size:1em;white-space:pre-line;">Type of fence?</p>
										<fieldset class="Ans12Clear" data-role="controlgroup" data-type="horizontal" data-mini="true">
											<input type="radio" name="Ans13" id="Ans13B" value="Block" <?php checkBox($applSec->{'Ans13'} == 'Block'); ?>><label for="Ans13B">Block</label>
											<input type="radio" name="Ans13" id="Ans13I" value="Iron" <?php checkBox($applSec->{'Ans13'} == 'Iron'); ?>><label for="Ans13I">Iron</label>
											<input type="radio" name="Ans13" id="Ans13W" value="Wood" <?php checkBox($applSec->{'Ans13'} == 'Wood'); ?>><label for="Ans13W">Wood</label>
											<input type="radio" name="Ans13" id="Ans13C" value="ChainLink" <?php checkBox($applSec->{'Ans13'} == 'ChainLink'); ?>><label for="Ans13C">ChainLink</label>
										</fieldset>
										<div class="AnsErr13"></div>
									</td>
								</tr>
								<tr class="Ans12Hide" <?php hide($applSec->{'Ans12'} == 'N'); ?>>
									<td style="padding-left:1.5em; padding-top:0;padding-bottom:0;" wrap>Height of fence:
										<div class="AnsErr14"></div>
									</td>
									<td style="padding-top:0;padding-bottom:0;" class="Ans12Zero">
										<select name="Ans14" id="Ans14" data-mini="true" data-iconpos="noicon">
											<?php
											$selOpt = ["3" => "3'", "3.5" => "3.5'", "4" => "4'", "4.5" => "4.5'", "5" => "5'", "5.5" => "5.5'", "6" => "6'+"];
											dropDownAry($selOpt, $applSec->{'Ans14'}, '');
											?>
										</select>
									</td>
								</tr>
						</table>
					</li>
					<li>
						<p style="font-size:1em;white-space:pre-line;">Describe your back yard (Grass, Stone, Flagstone, Cement, Irrigation System, Trees, Citrus, etc.):</p>
						<textarea name="Ans15" id="Ans15" data-mini="true"><?php echo $applSec->{'Ans15'}; ?></textarea>
						<div class="AnsErr15"></div>
					</li>
					<li>
						<table width="100%">
							<tbody>
								<tr>
									<td style="padding-top:0;padding-bottom:0;" wrap>Do you own a swimming pool?
										<div class="AnsErr10"></div>
									</td>
									<td style="padding-top:0;padding-bottom:0;">
										<fieldset id="Ans10" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
											<input type="radio" name="Ans10" id="Ans10Y" value="Y" <?php checkBox($applSec->{'Ans10'} == 'Y'); ?>><label for="Ans10Y" class="my-pad-8">Yes</label>
											<input type="radio" name="Ans10" id="Ans10N" value="N" <?php checkBox($applSec->{'Ans10'} == 'N'); ?>><label for="Ans10N" class="my-pad-8">No</label>
										</fieldset>
									</td>
								</tr>
								<tr class="Ans10Hide" <?php hide($applSec->{'Ans10'} == 'N'); ?>>
									<td style="padding-left:1.5em; padding-top:0;padding-bottom:0;" wrap>Is swimming pool fenced?
										<div class="AnsErr11"></div>
									</td>
									<td style="padding-top:0;padding-bottom:0;">
										<fieldset class="Ans10Clear" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
											<input type="radio" name="Ans11" id="Ans11Y" value="Y" <?php checkBox($applSec->{'Ans11'} == 'Y'); ?>><label for="Ans11Y" class="my-pad-8">Yes</label>
											<input type="radio" name="Ans11" id="Ans11N" value="N" <?php checkBox($applSec->{'Ans11'} == 'N'); ?>><label for="Ans11N" class="my-pad-8">No</label>
										</fieldset>
									</td>
								</tr>
								<tr>
									<td style="padding-top:0;padding-bottom:0;" wrap>Do you own a spa?
										<div class="AnsErr110"></div>
									</td>
									<td style="padding-top:0;padding-bottom:0;">
										<fieldset id="Ans110" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
											<input type="radio" name="Ans110" id="Ans110Y" value="Y" <?php checkBox($applSec->{'Ans110'} == 'Y'); ?>><label for="Ans110Y" class="my-pad-8">Yes</label>
											<input type="radio" name="Ans110" id="Ans110N" value="N" <?php checkBox($applSec->{'Ans110'} == 'N'); ?>><label for="Ans110N" class="my-pad-8">No</label>
										</fieldset>
									</td>
								</tr>
								<tr class="Ans110Hide" <?php hide($applSec->{'Ans110'} == 'N' or $applSec->{'Ans110'} == ''); ?>>
									<td style="padding-left:1.5em; padding-top:0;padding-bottom:0;" wrap>Is spa fenced or covered?
										<div class="AnsErr111"></div>
									</td>
									<td style="padding-top:0;padding-bottom:0;">
										<fieldset class="Ans110Clear" data-role="controlgroup" data-type="horizontal" data-mini="true" style="margin-top:0px;margin-bottom:0px;">
											<input type="radio" name="Ans111" id="Ans111Y" value="Y" <?php checkBox($applSec->{'Ans111'} == 'Y'); ?>><label for="Ans111Y" class="my-pad-8">Yes</label>
											<input type="radio" name="Ans111" id="Ans111N" value="N" <?php checkBox($applSec->{'Ans111'} == 'N'); ?>><label for="Ans111N" class="my-pad-8">No</label>
										</fieldset>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
					<li>
					</li>
				</ul>
				<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
				<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->
			</form>
		</div><!-- /content -->

		<div data-role="footer" data-position="fixed">
			<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
		</div><!-- /footer -->

		<div data-role="popup" id="adoptS1Success" data-dismissible="true" data-theme="a" data-position="#adoptS1Form">
			<div data-role="header" data-theme="a">
				<h1>Update Successful</h1>
			</div>
			<div data-theme="a">
				<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
				<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>
			</div>
		</div> <!-- /popup -->

		<div data-role="popup" id="adoptS1Error" data-dismissible="false" data-theme="a" data-position="#adoptS1Form">
			<div data-role="header" data-theme="b">
				<h1>Update Failed!</h1>
			</div>
			<div data-theme="a">
				<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
				<p id="adoptS1ErrorText" class="myErrMsg"></p>
				<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
			</div>
		</div> <!-- /popup -->
	</div><!-- /page -->

</body>

</html>