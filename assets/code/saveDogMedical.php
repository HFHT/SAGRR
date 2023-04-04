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
	$AlteredBy = dbPrep('AlteredBy','s');
	$AlteredDate = dbPrep('AlteredDate','s');
	$RabiesTag = dbPrep('RabiesTag','s');
	$RabiesCounty = dbPrep('RabiesCounty','s');
	$RabiesExpiration = dbPrep('RabiesExpiration','d');
	$DA2PP_DHLPPExpiration = dbPrep('DA2PP_DHLPPExpiration','d');
	$BordetellaExpiration = dbPrep('BordetellaExpiration','d');
	$LeptospirosisExpiration = dbPrep('LeptospirosisExpiration','d');
	$MedicalReleaseDate = dbPrep('MedicalReleaseDate','d');
	$SpecialDiet = dbPrep('SpecialDiet','s');
	$MedicalConditions = dbPrep('MedicalConditions','s');
	$MedicalClass = dbPrep('MedicalClass','s');	
	$DogCurStatus = dbPrep('DogCurStatus','s');	
	$DogT_id = $_POST['DogT_id'];
	$fk_DogT_id = dbPrep('fk_DogT_id','s');	
	$fk_PeopleT_id = dbPrep('fk_PeopleT_id','s');	
	$createBy = dbPrep('createBy','s');	
	$DogProcStatus = dbPrep('DogProcStatus','s');	
	$StatusComment = dbPrep('MedChng','s');
	
	$medical = new StdClass();
	$medical= array('RabiesTag' => $RabiesTag, 'RabiesCounty' => $RabiesCounty,
					'SpecialDiet' => $SpecialDiet, 'MedicalConditions' => $MedicalConditions, 'MedicalClass' => $MedicalClass);
	$medical = str_replace("'", "", json_encode($medical));
	$medical = str_replace("NULL","",$medical);
	$update_stmt = "UPDATE DogT SET medical='$medical',Rabies=$RabiesExpiration,DA2PP_DHLPP=$DA2PP_DHLPPExpiration,Bordetella=$BordetellaExpiration,Leptospirosis=$LeptospirosisExpiration,MedicalReleaseDate=$MedicalReleaseDate,DogCurStatus=$DogCurStatus,AlteredDate=$AlteredDate,AlteredBy=$AlteredBy WHERE DogT_id=$DogT_id";
	if ($result = $mysqli->query($update_stmt)) {
			$id = $DogT_id;
			if ($_POST['MedChng']<>"N") {
				$prep_stmt = "INSERT INTO DStatusTrackT (fk_DogT_id,fk_PeopleT_id,StatusDate,DogProcStatus,DogCurStatus,applProcStatus,StatusComment,createBy,Deleted) VALUES($fk_DogT_id,$fk_PeopleT_id,$MedicalReleaseDate,$DogProcStatus,$DogCurStatus,'X',$StatusComment,$createBy,'N')";
				if ($result = $mysqli->query($prep_stmt)) {
				} else {
					$error_sql = $prep_stmt;
					$error_str = $mysqli->error;
					$error_msg = 'Database Error on status update';
				}
			}
	} else {
		$error_sql = $update_stmt;
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