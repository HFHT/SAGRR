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
if (isset($_POST['PeopleT_id'])) {
	$PeopleT_id = $_POST['PeopleT_id'];
	if (isset($_POST['int'])) {
		$interests = $_POST['int'];
	} else {
		$interests = [];
	}
	$int = new StdClass();
	$int= array('Qty' => count($interests), 'Interests' => $interests);
	$int = str_replace("'", "", json_encode($int));
	$int = str_replace("NULL","",$int);
	if ($_POST['mode']=='team') {
		$update_stmt = "UPDATE PeopleT SET Teams='$int' WHERE PeopleT_id=$PeopleT_id";
	} else {
		$update_stmt = "UPDATE PeopleT SET Interests='$int' WHERE PeopleT_id=$PeopleT_id";
	}
	if ($result = $mysqli->query($update_stmt)) {
			$id = $PeopleT_id;
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
$mysqli->close();
?>