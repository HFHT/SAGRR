<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
$today = date("Y-m-d");
$oneMonthOn = new DateTime();
$oneMonthOn->add(new DateInterval('P30D'));

// $oneMonthOn = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 1 month"));
if ($_GET['x']!='') {
	$id = $_GET['x'];
	if ($id==0) {
		$prep_stmt = "SELECT MAX(DogT_id) as MaxId FROM DogT";
		$result = $mysqli->query($prep_stmt);
		$DogId = $result->fetch_assoc();
		var_dump($DogId);
		$id = $DogId['MaxId']+1;
		$prep_stmt = "SELECT MAX(SAGRR_id) as MaxDog FROM DogT where substring(SAGRR_id,1,2)='".date('y')."'";
		if ($result = $mysqli->query($prep_stmt)) {
			$DogId = $result->fetch_assoc();
			var_dump($DogId);
			if (is_null($DogId['MaxDog'])) {
				$row[0]['SAGRR_id'] = date('y').'-001';				/* Handle first new record for the year */
				echo '1';
			} else {
				if (strlen($DogId['MaxDog']) == 6) {
					echo '2';
					$lastThree = substr($DogId['MaxDog'],3);
					$lastThree = $lastThree+1;
					$lastThree = '000000'.$lastThree;
					$lastThree = substr($lastThree,(strlen($lastThree)-3));
					$row[0]['SAGRR_id']=substr($DogId[MaxDog],0,3).$lastThree;
				} else {
					echo '3';
					$row[0]['SAGRR_id']='';
				}
			}
		} else {
			echo '4';
			$error = true;
		}
		$row[0]['InTakeDate']=$today;
		$row[0]['Weight']=0;
		$row[0]['DogPhotoLink']='/assets/img/grstock.png';
		$row[0]['DogT_id']=$id;
		$row[0]['DogProcStatus']='InProcess';
		$memname = '';
	} else {
		echo '5';
		$prep_stmt = "SELECT DogT_id,SAGRR_id,fk_DStatusTrackT_id,DogName,FormerName,Age_YY,Age_MM,Weight,AKC_id,DMChipData,DMChipMfg_id,fk_BreedT_id,
						fk_ColorT_id,DogSex,AlteredBy,DateLastChanged,fk_SourceT_id,DogBlob,DogProcStatus,DogCurStatus,DogPhotoLink,InTakeDate,Birthdate,
						DeathDate, AlteredDate, DogCurMember, DogCurMemNo, Deleted
						FROM DogT WHERE DogT_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$row = myFetch($stmt);	
			if ($row[0]['Deleted']=='X') {
				$row[0]['SAGRR_id']	= $row[0]['DogName'] = '';
			}
		} else {
			$error = true;
		}
	}
} else {
	$error = true;;	
}

function dropDown($DBConn,$DBtable,$Selected) {	
	$dropSQL = "SELECT * FROM $DBtable";
	if ($dropResp = $DBConn->query($dropSQL)) {
		echo "<option value='0'>--Select--</option>";
		while ($dropRow = $dropResp->fetch_object()) {
			if ($Selected == $dropRow->SelId) {
				$strSel = "Selected";
			} else {
				$strSel = "";
			}
			echo "<option value='".$dropRow->SelId."' ".$strSel.">".$dropRow->SelText."</option>";
		}
	} else {
		echo "<option value='0'>!!DB Error!!</option>";
	}
}
function dropDownSource($DBConn,$DBtable,$Selected) {	
	$dropSQL = "SELECT * FROM $DBtable";
	if ($dropResp = $DBConn->query($dropSQL)) {
		echo "<option value='0'>--Select--</option>";
		while ($dropRow = $dropResp->fetch_object()) {
			if ($Selected == $dropRow->DogS_id) {
				$strSel = "Selected";
			} else {
				$strSel = "";
			}
			echo "<option value='".$dropRow->DogS_id."' ".$strSel.">".$dropRow->DogS_Name."</option>";
		}
	} else {
		echo "<option value='0'>!!DB Error!!</option>";
	}
}

function dropDownAry($SelAry,$Selected) {
	echo "<option value='0'>--Select--</option>";
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $key) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$key."' ".$strSel.">".$value."</option>";
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
<div data-role="page" id="dogIntake" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="intakebackBtn"><a href="#" class="myNav" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<li class="cancelBtn"><a href="#dogActiveMenu" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>	
				<li id="intakeSaveBtn" class="DogE" style="display:none"><a href="#" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">

		<form id="DogIntakeForm" class="forms">
			<ul data-role="listview" id="dog_update" data-autodividers="false" data-filter="false">
			<li>
			<table style="border-spacing:0;width:100%" id="dogInfoTab">
				<tbody>
					<?php if ($_GET['x']=='0') { ?>
					<tr><td colspan="2">
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true" class="ICSField">
						<input type="radio" name="DogCurStatus" id="DogCurStatus0" value="Available" class="ICS">
						<label for="DogCurStatus0" class="my-radio">Available</label>
						<input type="radio" name="DogCurStatus" id="DogCurStatus1" value="Hold-Behavior" class="ICS">
						<label for="DogCurStatus1" class="my-radio">Hold-Behavior</label>
						<input type="radio" name="DogCurStatus" id="DogCurStatus2" value="Hold-Medical" class="ICS">
						<label for="DogCurStatus2" class="my-radio">Hold-Medical</label>
						<input type="radio" name="DogCurStatus" id="DogCurStatus3" value="InTransit" checked="checked" class="ICS">
						<label for="DogCurStatus3" class="my-radio">InTransit</label>
					</fieldset>
					</td></tr>
					<?php } ?>
					<tr><td colspan="2"><div id="DogErrContainer"></div><div id="DogErrContainer1"></div></tr>				
					<tr><td rowspan="3">
					<a href="#" data-transition="slide" style="padding:0.5em 1em;" >
					<img src="http://southern-az-golden-retriever-rescue.org/wp-content/uploads/2011/06/tiny-wo-borderTM.png" width="80"/>
					</a>
					</td><td><input type="text" name="SAGRR_id" id="SAGRR_id" size="20" class="my-input-text" placeholder="SAGRR ID..." required value="<?php echo $row[0]['SAGRR_id'] ?>" data-mini="true"></td></tr>
					<tr><td><input type="text" name="DogName" id="DogName" size="20" class="my-input-text" placeholder="Name of Dog..." required value="<?php echo $row[0]['DogName'] ?>" data-mini="true"></td></tr>
					<tr><td><input type="text" name="FormerName" id="FormerName" size = "20" class="my-input-text" placeholder="Also Known As..." value="<?php echo $row[0]['FormerName'] ?>" data-mini="true"></td></tr>
					<tr><td>Birthdate:<span style="font-size: x-small;">approx</span></td><td><input type="date" data-clear-btn="false" name="Birthdate" id="Birthdate" min="1990-01-01" max="<?php echo $today ?>" value="<?php echo $row[0]['Birthdate'] ?>" data-mini="true"></td></tr>
					<tr><td>Intake date:</td><td><input type="date" data-clear-btn="false" name="InTakeDate" id="InTakeDate" class="my-date" min="2000-01-01" max="<?php echo $oneMonthOn->format('Y-m-d'); ?>" value="<?php echo $row[0]['InTakeDate'] ?>" required data-mini="true"></td></tr>
					<tr><td>Weight:</td><td><input type="range" name="Weight" id="Weight" data-highlight="true" min="0" max="120" value="<?php echo $row[0]['Weight'] ?>"></td></tr>					
					<tr><td>Sex:</td><td>
						<select name="DogSex" id="DogSex" data-mini="true">
							<?php
							$dogOpt = ["Female" => "Female", "Male" => "Male", "Unknown" => "Unknown"];
							dropDownAry($dogOpt,$row[0]['DogSex']);
							?>
						</select>					
					</td></tr>
					<tr><td>Color:</td><td>
						<select name="fk_ColorT_id" id="fk_ColorT_id" data-mini="true">
							<?php dropDown($mysqli,"ColorT",$row[0]['fk_ColorT_id']); ?>							  
						</select>					
					</td></tr>
					<tr><td>Source:</td><td>
						<select name="fk_SourceT_id" id="fk_SourceT_id" data-mini="true">
							<?php dropDownSource($mysqli,"DogSources ORDER BY DogS_Name",$row[0]['fk_SourceT_id']); ?>							  							
						</select>					</td></tr>				
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="DogBlob" id="DogBlob" placeholder="Notes..."><?php echo $row[0]['DogBlob'] ?></textarea></td></tr>
					<tr><td>Chip Mfg:</td><td>
						<select name="DMChipMfg_id" id="DMChipMfg_id" data-mini="true">
							<?php dropDown($mysqli,"MChipMfg",$row[0]['DMChipMfg_id']) ?>					  
						</select>					
					</td></tr>
					<tr><td>Chip #:</td><td><input type="text" name="DMChipData" id="DMChipData" size = "20" class="my-input-text" placeholder="Microchip Number..." value="<?php echo $row[0]['DMChipData'] ?>" data-mini="true"></td></tr>								
					<tr><td>AKC Id:</td><td><input type="text" name="AKC_id" id="AKC_id" size = "20" class="my-input-text" placeholder="AKC Id Number..." value="<?php echo $row[0]['AKC_id'] ?>" data-mini="true"></td></tr>								
					<tr><td width="40%">Responsible:</td><td width="60%"><span id="memSelected"><?php echo $memname==''?'No one assigned':$memname; ?></span></td></tr>					
					</tbody>
			</table>
			</li>
			<li>
				<a href="/assets/pages/memSelect.php?m=0&n=0&l=intake" data-icon="edit" data-transition="slide">Select Intake Volunteer</a></li>					
			</li>		
			</ul>
			<input type="hidden" name="DogT_id" id="DogT_id" value="<?php echo $row[0]['DogT_id'] ?>">
			<input type="hidden" name="DogCurMember" id="DogCurMember" value="<?php echo $row[0]['DogCurMember'] ?>">
			<input type="hidden" name="DogCurMemNo" id="DogCurMemNo" value="<?php echo $row[0]['DogCurMemNo'] ?>">
			<input type="hidden" name="DogProcStatus" id="DogProcStatus" value="<?php echo $row[0]['DogProcStatus'] ?>">
			<input type="hidden" name="DogCurStatusHid" id="DogCurStatusHid" value="">
			<input type="hidden" name="InTake_fk_PeopleT" id="InTake_fk_PeopleT" value="<?php echo $row[0]['InTake_fk_PeopleT'] ?>">	
			<input type="hidden" name="createBy" id="createBy" value="0">	
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="dogSuccess" data-dismissible="true" data-theme="a" data-position="#dogInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="dogError" data-dismissible="false" data-theme="a"  data-position="#dogInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="dogErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>