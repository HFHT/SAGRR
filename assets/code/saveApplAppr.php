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
$applid = "0";
if (isset($_POST['applid'])) {
	$applid = dbPrep('applid','i');
	$applApprovedBy = dbPrep('applApprovedBy','s');
	$applApproved = dbPrep('applApproved','s');
	$applApproveDate = dbPrep('applApproveDate', 'd');
	if ($result = $mysqli->query("UPDATE Applications SET applApprovedBy=$applApprovedBy,applApproved=$applApproved,applApproveDate=$applApproveDate WHERE applid=$applid")) {
		$applid = $applid;
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
'data' =>  $applid);
echo json_encode($results);
$mysqli->close();
?>