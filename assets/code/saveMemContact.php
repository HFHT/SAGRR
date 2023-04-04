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
$today = date("Y-m-d");
if (isset($_POST['fk_PeopleT_id'])) {
	$logDate = dbPrep('logDate','d');
	$logBy = dbPrep('logBy','s');
	$logText = dbPrep('logText','s');
	$fk_PeopleT_id = $_POST['fk_PeopleT_id'];
	$fk_applid = $_POST['fk_applid'];	
	$fk_DogT_id = $_POST['fk_DogT_id'];	
	$logid = $_POST['logid'];
	if ($logid<>'0') {
		$prep_stmt = "UPDATE ContactLog SET logDate=$logDate,logText=$logText,logUpdateDate='$today',logUpdateBy=$logBy WHERE logid=$logid";
	} else {
		$prep_stmt = "INSERT INTO ContactLog (fk_PeopleT_id,fk_applid,fk_DogT_id,logDate,logBy,logText) VALUES ($fk_PeopleT_id,$fk_applid,$fk_DogT_id,$logDate,$logBy,$logText)";		
	}
	if ($result = $mysqli->query($prep_stmt)) {
		$id = $logid<>'0' ? $logid : $mysqli->insert_id;
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