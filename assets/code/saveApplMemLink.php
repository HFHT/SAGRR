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
if (isset($_POST['mem'])) {
	$mem = $_POST['mem'];
	$id=$_POST['v'];
	$update_stmt = "UPDATE Applications SET fk_PeopleT_id=$mem WHERE applid=$id";
	if ($result = $mysqli->query($update_stmt)) {
		$id = $id;
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
//var_dump($update_stmt);
$mysqli->close();
?>