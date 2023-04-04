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
if (isset($_POST['DogT_id'])) {
	$DogT_id = dbPrep('DogT_id','i');
	$fk_SourceT_id = dbPrep('fk_SourceT_id','i');
	$dogSource = dbPrep('dogSource','s');
	$insert_stmt = "INSERT INTO DogT SET ".
					"DogCurStatus = '$_POST[DogCurStatusHid]',".
					"SAGRR_id = '$_POST[SAGRR_id]',".
					"DogName = '$_POST[DogName]',".
					"FormerName = '$_POST[FormerName]',".
					"Birthdate = '$_POST[Birthdate]',".
					"InTakeDate = '$_POST[InTakeDate]',".
					"Weight = $_POST[Weight],".
					"DogSex = '$_POST[DogSex]',".
					"fk_ColorT_id = $_POST[fk_ColorT_id],".
					"fk_SourceT_id = $_POST[fk_SourceT_id],".
					"DogBlob = '$_POST[DogBlob]',".
					"DMChipMfg_id = $_POST[DMChipMfg_id],".
					"DMChipData = '$_POST[DMChipData]',".
					"AKC_id = '$_POST[AKC_id]',".
					"DogProcStatus = '$_POST[DogProcStatus]',".
					"InTake_fk_PeopleT = $_POST[InTake_fk_PeopleT],".
					"Deleted='N'";			
	if ($result = $mysqli->query($insert_stmt)) {
		$id = $DogT_id;
		$insert_stmt = "INSERT INTO DStatusTrackT SET ".
					"fk_DogT_id=$id,".
					"fk_PeopleT_id=$_POST[InTake_fk_PeopleT],".
					"StatusDate='$_POST[InTakeDate]',".
					"DogProcStatus='$_POST[DogProcStatus]',".
					"DogCurStatus='$_POST[DogCurStatusHid]',".
					"StatusComment='Intake record created.',".
					"createBy='$_POST[createBy]',".
					"Deleted='N', NCI='N', Bridge='N'";
		$error_sql = $insert_stmt;					
		if ($result = $mysqli->query($insert_stmt)) {
		} else {
			$error_sql = $insert_stmt;
			$error_str = $mysqli->error;
			$error_msg = 'Database Error on update';			
		}
	} else {
		$error_sql = $insert_stmt;
		$error_str = $mysqli->error;
		$error_msg = 'Database Error on update';
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