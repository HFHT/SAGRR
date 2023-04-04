<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/psl-config.php';
 
$error_msg = '';
$error_str = '';
$error_sql = '';
$id = "0";
if (isset($_POST['DogName'])) {
	$DogProcStatus = dbPrep('DogProcStatus','s');
	$DogCurStatus = dbPrep('DogCurStatus','s');
	$DogName = dbPrep('DogName','s');
	$SAGRR_id = dbPrep('SAGRR_id','s');
	$AKC_id = dbPrep('AKC_id','s');
	$DogSex = dbPrep('DogSex','s');
//	$fk_BreedT_id = dbPrep('fk_BreedT_id','i');
	$fk_ColorT_id = dbPrep('fk_ColorT_id','s');
	$InTakeDate = dbPrep('InTakeDate','d');
	$FormerName = dbPrep('FormerName','s');
	$Birthdate = dbPrep('Birthdate','d');
	$Age_YY = dbPrep('Age_YY','s');
	$Age_MM = dbPrep('Age_MM','s');
	$AlteredDate = dbPrep('AlteredDate','d');
	$AlteredBy = dbPrep('AlteredBy','s');
	$Weight = dbPrep('Weight','i');
	$DeathDate = dbPrep('DeathDate','d');
	$DogBlob = dbPrep('DogBlob','s');
	$DMChipData = dbPrep('DMChipData','s');
	$DMChipMfg_id = dbPrep('DMChipMfg_id','s');	
	$DogT_id = $_POST['DogT_id'];
	$DogCurMemNo = dbPrep('DogCurMemNo','s');
	$DogCurMember = dbPrep('DogCurMember','s');	

	if ($DogT_id == 0) {
		// if we are inserting a new record check that the dog doesn't already exist
		if ($result = $mysqli->query("SELECT DogT_id FROM DogT WHERE SAGRR_id = $SAGRR_id")) {
			$row_cnt = $result->num_rows;
			if ($row_cnt > 0) {		
				// A dog with this id already exists
				$error_msg .= 'A dog with this SAGRR id already exists!';
			} else {
				// Insert the new dog into the database 																
				$insert_stmt = "INSERT INTO DogT (SAGRR_id, InTakeDate, DogName, FormerName, Birthdate, Age_YY, Age_MM, AKC_id, DeathDate,".
								"DMChipData, DMChipMfg_id, DogSex, AlteredBy, AlteredDate, fk_DStatusTrackT_id, Weight, fk_ColorT_id,".
								"fk_SourceT_id, DogBlob, DogCurMemNo, DogCurMember, Deleted) ". 
								"VALUES($SAGRR_id,$InTakeDate,$DogName,$FormerName,$Birthdate,$Age_YY,$Age_MM,$AKC_id,$DeathDate,".
								"$DMChipData,$DMChipMfg_id,$DogSex,$AlteredBy,$AlteredDate,NULL,$Weight,$fk_ColorT_id,NULL,$DogBlob,".
								"$DogCurMemNo,$DogCurMember,'N')";
//				echo $insert_stmt;
				if ($result = $mysqli->query($insert_stmt)) {
					$id = $mysqli->insert_id;
				} else {
					$error_sql = $insert_stmt;
					$error_str = $mysqli->error;
					$error_msg = 'Database Error, Insert Failed.';
				}	
			}
		} else {
			$error_msg = 'Database Error, check of dog record failed.';
		}
	} else {
		$update_stmt = "UPDATE DogT SET SAGRR_id=$SAGRR_id,InTakeDate=$InTakeDate,DogName=$DogName,DogProcStatus=$DogProcStatus,".
						"DogCurStatus=$DogCurStatus,FormerName=$FormerName,Birthdate=$Birthdate,Age_YY=$Age_YY,Age_MM=$Age_MM,AKC_id=$AKC_id,".
						"DeathDate=$DeathDate,DMChipData=$DMChipData,DMChipMfg_id=$DMChipMfg_id,DogSex=$DogSex,AlteredBy=$AlteredBy,".
						"AlteredDate=$AlteredDate,Weight=$Weight,fk_ColorT_id=$fk_ColorT_id,DogBlob=$DogBlob,DogCurMemNo=$DogCurMemNo,".
						"DogCurMember=$DogCurMember,Deleted='N' WHERE DogT_id=$DogT_id";
		if ($result = $mysqli->query($update_stmt)) {
				$id = $DogT_id;
		} else {
			$error_sql = $update_stmt;
			$error_str = $mysqli->error;
			$error_msg = 'Database Error on update';
		}				
	}
}
else {
	$error_msg = 'Key was not provided';
}

$results = array(
'error' => (! empty($error_msg)),
'errorDetail' => array(
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_msg' => $error_msg),
'data' =>  $id);
echo json_encode($results);
$mysqli->close();
?>