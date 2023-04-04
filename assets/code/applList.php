<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
//include_once '../includes/psl-config.php';

$error_msg = $error_str = $error_sql = $applList = '';

if (!empty($_POST['pending'])) {
	try {
		if ($_POST['pending']=='y') {
			$prep_stmt = "SELECT applDocId FROM Applications WHERE applComplete='N' AND Deleted <> 'Y'";
		} else {
			$prep_stmt = "SELECT applDocId FROM Applications WHERE Deleted <> 'Y'";
		}
		$result = $mysqli->query($prep_stmt);
		$applList = $result->fetch_assoc();
		$result->data_seek(0);
		$applList1 = $result->fetch_row();
		$result->data_seek(0);
		$applList2 = $result->fetch_array();
		$result->data_seek(0);
		$applList3 = $result->fetch_object();
		$applList = readDB($mysqli,$prep_stmt,true);
		$prep_stmt = "SELECT Member_id FROM PeopleT WHERE Deleted <> 'Y'";
		$memList = readDB($mysqli,$prep_stmt,true);
	}
	catch(Exception $e) {
		$error_str = $e->getMessage();
		$error_sql = $prep_stmt;
		$error_msg = 'Could not obtain summary counts, try again later!';
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
'memList' => $memList,
'applList' => $applList1,
'applList2' => $applList2,
'applList3' => $applList3,
'applList1' =>  $applList);
echo json_encode($results);
$mysqli->close();

function readDB ($conn,$query,$multi) {
	if ($result = $conn->query($query) ) {
		if ($multi) {
			return (myFetch($result));
		} else {
			return ($result->fetch_assoc());
		}
	} else {
		throw new Exception($conn->error);
	}
}	
?>