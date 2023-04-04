<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once '../inc/functions.php';
sec_session_start();
$_SESSION["xxxMode"] = "off";
session_start();
include_once '../inc/db_connect.php';
include_once '../inc/psl-config.php';

$error_msg = '';
$error_str = '';
$error_sql = '';
$error_no = '';
$row = array();
if (isset($_POST['userid'])) {
	$userid = dbPrep('userid','s');
	$email = dbPrep('email','s');
	$name = dbPrep('name','s');
	$update_stmt =	"INSERT INTO members (username,email,memname,LastLogin) VALUES ($userid,$email,$name,NOW()) ". 
					"ON DUPLICATE KEY UPDATE username=$userid,email=$email,memname=$name,LastLogin=NOW(),LoginCnt=LoginCnt+1";
	if ($result = $mysqli->query($update_stmt)) {
		$update_stmt = "SELECT Role, Options, Locked, LockedDate, LockedReason, fk_PeopleT_id, Deleted FROM members WHERE username=$userid";
		if ($result = $mysqli->query($update_stmt)) {
			$row = myFetch($result);			
		}
		else {
			$error_sql = $update_stmt;
			$error_str = $mysqli->error;
			$error_no = $mysqli->errno;
			$error_msg = 'Database Error on query';			
		}
		
	} else {
		$error_sql = $update_stmt;
		$error_str = $mysqli->error;
		$error_no = $mysqli->errno;
		$error_msg = 'Database Error on update';
	}				
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
'data' =>  $row);
echo json_encode($results);

?>