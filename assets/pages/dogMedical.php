<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$medical = '';
$row = array();
$today = date("Y-m-d");
$oneMonthOn = new DateTime();
$oneMonthOn->add(new DateInterval('P30D'));
// $oneMonthOn = date($today, strtotime("+30 days"));

if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$prep_stmt = "SELECT Medical,Rabies,DA2PP_DHLPP,Bordetella,Leptospirosis,MedicalReleaseDate,DogProcStatus,DogCurStatus,AlteredBy,AlteredDate FROM DogT WHERE DogT_id=$id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$medical = json_decode($row[0]['Medical']);
		$OnHoldSel = $row[0]['DogCurStatus']=='Hold-Medical' ? 'selected' : '';
		$OnHoldCur = $row[0]['DogCurStatus']=='Hold-Medical' ? 'Available' : $row[0]['DogCurStatus'];
		$OnHoldDisable = ($row[0]['DogProcStatus']=='InProcess' OR $row[0]['DogProcStatus']=='Fostered') ? '' : 'disabled';
	} else {
        $error = true;
    }
} else {
	$error = true;;
}
function checkBox($decision) {
	if ($decision==1) {
		echo ('checked');
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
<div data-role="page" id="dogMedical">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>	
				<li id="dogMedSaveBtn" class="DogE" style="display:none"><a href="#dogMedical" data-icon="action" class="myNav">Save</a></li>				
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">	<?php
	if ($error) {
	?>
		<div class="ui-corner-all">
			<div class="ui-bar ui-bar-a">
				<h3>Error Occurred</h3>
			</div>
			<div class="ui-body ui-body-a">
				<p>Could not access the Dog Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<form id="DogMedicalForm"  class="forms">
			<ul data-role="listview" id="dog_medicalupdate" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Medical Info for <?php echo $_GET['n'] ?></li>	
			<li>
				<p style="font-size:1em;white-space:pre-line;">Overall medical condition:</p>			
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
					<input type="radio" name="MedicalClass" id="MC0" value="Good" <?php checkBox($medical->{'MedicalClass'}=='Good');?>><label  class="my-radio" for="MC0">Good</label>
					<input type="radio" name="MedicalClass" id="MC1" value="Minor" <?php checkBox($medical->{'MedicalClass'}=='Minor');?>><label class="my-radio" for="MC1">Minor</label>
					<input type="radio" name="MedicalClass" id="MC2" value="Major" <?php checkBox($medical->{'MedicalClass'}=='Major');?>><label class="my-radio" for="MC2">Major</label>
					<input type="radio" name="MedicalClass" id="MC3" value="Treatable" <?php checkBox($medical->{'MedicalClass'}=='Treatable');?>><label class="my-radio" for="MC3">Treatable</label>							
					<input type="radio" name="MedicalClass" id="MC4" value="Cancer" <?php checkBox($medical->{'MedicalClass'}=='Cancer');?>><label class="my-radio" for="MC4">Cancer</label>							
				</fieldset>
				<div class="MedConErr"></div>			
			</li>
			<li>
				<table width="100%"><tbody>
				<?php if ($OnHoldDisable == "") { ?>
				<tr><td style="padding-bottom:0;" width="50%">
					<label for="DogCurStatus">On Medical Hold</label>
				</td>
				<td>
				<select name="DogCurStatus" id="DogCurStatus" data-role="slider" data-mini="true" <?php echo $OnHoldDisable;?>>
					<option value = "<?php echo $OnHoldCur;?>">No</option>
					<option value = "Hold-Medical" <?php echo $OnHoldSel;?>>Yes</option>
				</select>
				</td>
				<?php } ?>
				<tr>
				<td style="padding-bottom:0;" width="50%">Medical Release Date</td>
				<td><input type="date" data-clear-btn="false" name="MedicalReleaseDate" id="MedicalReleaseDate" class="my-date" max="<?php echo $today ?>" value="<?php echo $row[0]['MedicalReleaseDate'] ?>" ></td>
				</tr>
				</tbody></table>
			</li>
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td width="50%">Altered By:</td><td><input type="text" name="AlteredBy" id="AlteredBy" size = "20" class="my-input-text" placeholder="Altered By..." value="<?php echo $row[0]['AlteredBy'] ?>" data-mini="true"></td></tr>								
					<tr><td>Altered Date:</td><td><input type="date" data-clear-btn="false" name="AlteredDate" id="AlteredDate" min="2000-01-01" max="<?php echo $oneMonthOn->format('Y-m-d'); ?>" value="<?php echo $row[0]['AlteredDate'] ?>" data-mini="true"></td></tr>								

				</tbody>
			</table>
			
			</li>
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td width="50%">Rabies License:</td><td width="50%"><input type="text" name="RabiesTag" id="RabiesTag" size = "20" class="my-input-text" placeholder="Rabies License..." value="<?php echo $medical -> {'RabiesTag'}; ?>"></td></tr>
					<tr><td>Rabies County:</td><td><input type="text" name="RabiesCounty" id="RabiesCounty" size = "20" class="my-input-text" placeholder="Rabies County..." value="<?php echo $medical -> {'RabiesCounty'} ?>"></td></tr>
					<tr><td colspan="2"><table style="border-spacing:0;width:100%;font-weight:400;border-top:1pt dotted rgb(196,196,196)"><tbody>
					<tr><td colspan="2" style="font-weight:600"><i>Vaccination Expiration Dates</i></td></tr>
					<tr><td>Rabies:</td><td><input type="date" data-clear-btn="false" name="RabiesExpiration" id="RabiesExpiration" value="<?php echo $row[0]['Rabies'] ?>"></td></tr>
					<tr><td>DA2PP/DHLPP:</td><td><input type="date" data-clear-btn="false" name="DA2PP_DHLPPExpiration" id="DA2PP_DHLPPExpiration" class="my-date" value="<?php echo $row[0]['DA2PP_DHLPP'] ?>" ></td></tr>
					<tr><td>Bordetella:</td><td><input type="date" data-clear-btn="false" name="BordetellaExpiration" id="BordetellaExpiration" class="my-date" value="<?php echo $row[0]['Bordetella'] ?>" ></td></tr>
					<tr><td>Leptospirosis:</td><td><input type="date" data-clear-btn="false" name="LeptospirosisExpiration" id="LeptospirosisExpiration" class="my-date" value="<?php echo $row[0]['Leptospirosis'] ?>" ></td></tr>
					</td></tr></tbody></table>
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="SpecialDiet" id="SpecialDiet" placeholder="Special Diet..."><?php echo $medical -> {'SpecialDiet'} ?></textarea></td></tr>
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="MedicalConditions" id="MedicalConditions" placeholder="Medical Conditions..."><?php echo $medical -> {'MedicalConditions'} ?></textarea></td></tr>
				</tbody>
			</table>
			</li>
			</ul>
			<img src="http://southern-az-golden-retriever-rescue.org/database/assets/veterinarian_logo.png" width="50" style="margin-top:2em"/>
			<input type="hidden" name="DogT_id" id="DogT_id" value="<?php echo $id ?>">
			<input type="hidden" name="fk_DogT_id" id="fk_DogT_id" value="<?php echo $id ?>">
			<input type="hidden" name="fk_PeopleT_id" id="fk_PeopleT_id" value="">
			<input type="hidden" name="createBy" id="createBy" value="">
			<input type="hidden" name="DogProcStatus" id="DogProcStatus" value="<?php echo $row[0]['DogProcStatus'] ?>">
			<input type="hidden" name="MedChng" id="MedChng" value="N">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->

	<div data-role="popup" id="MedicalReleaseConfirm" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Change of Medical Hold Status</h1>
		</div>
		<div data-theme="a" style="margin:5px;">
			<p>When you <i><b>Save</b></i> this update, a status change record will be created for you to reflect that <i><b><?php echo $_GET['n'] ?></b></i> is being <i><b><span id="MedicalChange"></span></b></i></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>		
	</div> <!-- /popup -->
	
	<div data-role="popup" id="dogMedicalSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="dogMedicalError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="dogMedicalErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
</div><!-- /page -->

</body>
</html>