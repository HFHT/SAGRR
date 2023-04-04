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
if (isset($_POST['FileAsset_id'])) {
	$attachType = dbPrep('attachType','s');
	$FileDesc = dbPrep('FileDesc','s');
	$Caption = dbPrep('Caption','s');
	$FileAsset_id = $_POST['FileAsset_id'];
	$prep_stmt = "UPDATE FileAsset SET Caption=$Caption,FileDesc=$FileDesc,attachType=$attachType WHERE FileAsset_id=$FileAsset_id";
	if ($result = $mysqli->query($prep_stmt)) {
			$id = $FileAsset_id;
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