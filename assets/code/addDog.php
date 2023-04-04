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
if (isset($_POST['AddNew'])) { 
	$yr = date('y');
	$result = $mysqli->query("SELECT MAX(SAGRR_id) as id FROM DogT where substring(SAGRR_id,1,2)='$yr'");
	$row = $result->fetch_assoc();
	if ($row['id']=='NULL') {													// No dogs entered into DB so far this year
		$id=$yr."-001";
	} else {
		$id = $yr."-".str_pad(substr($row['id'],3,3)+1,3,0,STR_PAD_LEFT);
	}	
	$insert_stmt = "INSERT INTO DogT (DogT_id,SAGRR_id) VALUES (NULL,'$id')";
	if ($result = $mysqli->query($insert_stmt)) {
		$rcd = $mysqli->insert_id;
	} else {
		$error_sql = $insert_stmt;
		$error_str = $mysqli->error;
		$error_msg = 'Database Error, Insert Failed.';
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
'id' => $id,
'rcd' =>  $rcd);
echo json_encode($results);
$mysqli->close();
?>