<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../includes/functions.php';
sec_session_start();
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';

$error_msg = "";

unset($id);
if (isset($_GET['pk'])) {
	$id = $_SESSION['user_id'];
}
else {
	if (isset($_POST['id'])) {
		$id = $_POST['id'];
	}
}
if (isset($id)) {
    $prep_stmt = "SELECT DogT_id,SAGRR_id,fk_DStatusTrackT_id,DogName,FormerName,Age_YY,Age_MM,Weight,AKC_id,DMChipData,DMChipMfg_id,fk_BreedT_id,
					fk_ColorT_id,DogSex,AlteredBy,DateLastChanged,fk_SourceT_id,DogBlob,
					DATE_FORMAT(InTakeDate,'%m/%d/%Y') AS InTakeDate, 
					DATE_FORMAT(Birthdate,'%m/%d/%Y') AS Birthdate, 
					DATE_FORMAT(DeathDate,'%m/%d/%Y') AS DeathDate, 
					DATE_FORMAT(AlteredDate,'%m/%d/%Y') AS AlteredDate					
					FROM DogT WHERE DogT_id = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		echo json_encode(myFetch($stmt));			
	} else {
        echo "Database Prepare Error 1";
    }
}
else {		
	$prep_stmt = "SELECT * FROM DogT WHERE Deleted <> 'Y' ORDER BY DogName, SAGRR_id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		echo json_encode(myFetch($stmt));			
	} else {
        echo "Database Prepare Error 2";
    }
}
$mysqli->close();
?>