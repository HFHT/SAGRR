<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once '../inc/functions.php';
sec_session_start();
session_start();
include_once '../inc/db_connect.php';
include_once '../inc/psl-config.php';

$error_msg = '';
$error_str = '';
$error_sql = '';
$row = array();
if (isset($_POST['userid'])) {
	$_SESSION["xxxMode"] = $_POST['userid'];
}
else {
	$error_msg = 'Key was not provided';
	$error_no = E_USER_ERROR;
}


$results = array(
'error' => (! empty($error_msg)),
'errorDetail' => array(
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_no' => $error_no,
	'error_msg' => $error_msg),
'data' =>  $_SESSION["xxxMode"]);
echo json_encode($results);

?>