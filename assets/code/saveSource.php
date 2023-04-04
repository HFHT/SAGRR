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
if (isset($_POST['DogS_id'])) {
	$DogS_Name = dbPrep('DogS_Name','s');
	$DogS_Contact = dbPrep('DogS_Contact','s');
	$DogS_Addr = dbPrep('DogS_Addr','s');
	$DogS_Phone = dbPrep('DogS_Phone','s');
	$DogS_Email = dbPrep('vetEmail','s');
	$DogS_id = $_POST['DogS_id'];
	if ($DogS_id<>'0') {
		$prep_stmt = "UPDATE DogSources SET DogS_Name=$DogS_Name,DogS_Contact=$DogS_Contact,DogS_Addr=$DogS_Addr,DogS_Phone=$DogS_Phone,DogS_Email=$DogS_Email WHERE DogS_id=$DogS_id";
	} else {
		$prep_stmt = "INSERT INTO DogSources (DogS_Name,DogS_Contact,DogS_Addr,DogS_Phone,DogS_Email) VALUES ($DogS_Name,$DogS_Contact,$DogS_Addr,$DogS_Phone,$DogS_Email)";		
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