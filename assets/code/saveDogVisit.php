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
if (isset($_POST['fkV_DogT_id'])) {
	$fk_VetClinicT = dbPrep('fk_VetClinicT','i');
	$VetCost = dbPrep('VetCost','i');
	$VetDate = dbPrep('VetDate','d');
	$VetInvoice = dbPrep('VetInvoice','s');
	$VetFollowup = dbPrep('VetFollowup','d');
	$VetReason = dbPrep('VetReason','s');
	$VetResult = dbPrep('VetResult','s');
	if (isset($_POST['VetFollowupComp'])) {
		$VetFollowupComp = dbPrep('VetFollowupComp','s');
	} else {
		$VetFollowupComp = "'N'";
	}
	$fkV_DogT_id = $_POST['fkV_DogT_id'];
	$VetVisit_id = $_POST['VetVisit_id'];
	if ($VetVisit_id<>'0') {
		$prep_stmt = "UPDATE VetVisit SET fkV_DogT_id=$fkV_DogT_id,fk_VetClinicT=$fk_VetClinicT,VetDate=$VetDate,VetCost=$VetCost,VetInvoice=$VetInvoice,VetFollowup=$VetFollowup,VetFollowupComp=$VetFollowupComp,VetReason=$VetReason,VetResult=$VetResult,Deleted='N' WHERE VetVisit_id=$VetVisit_id";
	} else {
		$prep_stmt = "INSERT INTO VetVisit (fkV_DogT_id,fk_VetClinicT,VetDate,VetCost,VetInvoice,VetFollowup,VetFollowupComp,VetReason,VetResult,Deleted) VALUES ($fkV_DogT_id,$fk_VetClinicT,$VetDate,$VetCost,$VetInvoice,$VetFollowup,$VetFollowupComp,$VetReason,$VetResult,'N')";		
	}
	if ($result = $mysqli->query($prep_stmt)) {
		$id = $VetVisit_id<>'0' ? $VetVisit_id : $mysqli->insert_id;
	} else {
		$error_sql = $prep_stmt;
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