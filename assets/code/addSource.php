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
	$insert_stmt = "INSERT INTO DogSources (DogS_id) VALUES (NULL)";
	if ($result = $mysqli->query($insert_stmt)) {
		$id = $mysqli->insert_id;
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
'data' =>  $id);
echo json_encode($results);
$mysqli->close();
?>