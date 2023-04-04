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
	$Company = dbPrep('Company','s');
	$Title = dbPrep('Title','s');
	$Contact = dbPrep('Contact','s');
	$WorkEmail = dbPrep('WorkEmail','s');
	$WorkPhone = dbPrep('WorkPhone','s');
	$PeopleT_id = dbPrep('PeopleT_id','i');
	if (isset($_POST['Vet'])) {
		$Vet = 'Y';
	} else {
		$Vet = 'N';
	}
	$update_stmt = "UPDATE PeopleT SET Company=$Company,Title=$Title,Contact=$Contact,WorkEmail=$WorkEmail,WorkPhone=$WorkPhone,Vet='$Vet' ".
					"WHERE PeopleT_id=$PeopleT_id";
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