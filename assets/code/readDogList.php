<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
//include_once '../includes/psl-config.php';

$error_msg = '';
$error_str = '';
$error_sql = '';
$row = array();

if (!empty($_POST['userId'])) {
	$prep_stmt = "SELECT * FROM DogT WHERE Deleted <> 'Y' ORDER BY DogName, SAGRR_id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);			
	} else {
		$error_sql = $prep_stmt;
		$error_str = $mysqli->error;
		$error_msg = "Could not get dog list, try again later!";
    }
} else {
	$error_msg = 'Internal program error';
}
$results = array(
'error' => (! empty($error_msg)),
'errorDetail' => array(
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_msg' => $error_msg),
'data' =>  $row);
echo json_encode($results);
$mysqli->close();
?>