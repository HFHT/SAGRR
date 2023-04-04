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
if (isset($_POST['SelId'])) {
	$SelText = dbPrep('SelText','s');
	$SelDesc = dbPrep('SelDesc','s');
	$vetClinic = dbPrep('vetClinic','s');
	$vetAddress = dbPrep('vetAddress','s');
	$vetPhone = dbPrep('vetPhone','s');
	$vetEmergencyPhone = dbPrep('vetEmergencyPhone','s');
	$vetContact = dbPrep('vetContact','s');
	$vetEmail = dbPrep('vetEmail','s');
	$SelId = $_POST['SelId'];
	if ($SelId<>'0') {
		$prep_stmt = "UPDATE VetClinicT SET SelText=$SelText,SelDesc=$SelDesc,vetClinic=$vetClinic,vetAddress=$vetAddress,vetPhone=$vetPhone,vetEmergencyPhone=$vetEmergencyPhone,vetContact=$vetContact,vetEmail=$vetEmail WHERE SelId=$SelId";
	} else {
		$prep_stmt = "INSERT INTO VetClinicT (SelText,SelDesc,vetClinic,vetAddress,vetPhone,vetEmergencyPhone,vetContact,vetEmail) VALUES ($SelText,$SelDesc,$vetClinic,$vetAddress,$vetPhone,$vetEmergencyPhone,$vetContact,$vetEmail)";		
	}
	if ($result = $mysqli->query($prep_stmt)) {
			$id = $SelId;
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