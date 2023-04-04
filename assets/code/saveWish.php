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
if (isset($_POST['w_id'])) {
	$w_desc = dbPrep('w_desc','s');
	$w_ans = dbPrep('w_ans','s');
	$w_status = dbPrep('w_status','s');
	$w_sev = dbPrep('w_sev','i');
	$w_id = dbPrep('w_id','i');
	$prep_stmt = "UPDATE WishList SET w_desc=$w_desc,w_ans=$w_ans,w_status=$w_status,w_sev=$w_sev WHERE w_id=$w_id";
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