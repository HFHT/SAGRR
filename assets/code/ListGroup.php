<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$objErr = new stdClass();
$row = array();
$s= $_POST['SelId'];
if (isset($s)) {
	$where = "'\"".$s."\"'";
	if ($_POST['SelMode']=='team') {
		$prep_stmt = "SELECT * FROM PeopleT WHERE INSTR(Teams,$where)>0 ORDER BY LastName,FirstName";
	} else {
		$prep_stmt = "SELECT * FROM PeopleT WHERE INSTR(Interests,$where)>0 ORDER BY LastName,FirstName";
	}
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$aryMem = array();
		foreach ($row as $rcd) {
			$objMem = new stdClass();
			$objMem->key = $rcd['PeopleT_id'];
			$objMem->name = $rcd['LastName'].', '.$rcd['FirstName'];
			$objMem->id = $rcd['Member_id'];
			$objMem->phone = $rcd['Phone'];
			$objMem->email = $rcd['Email'];
			array_push($aryMem,$objMem);
		}
	} else {
		$objErr->error_sql = $prep_stmt;
		$objErr->error_str = $mysqli->error;
		$objErr->error_msg = 'Database Error reading Member list.';
    }
} else {
	$objErr->error_str = '';
	$objErr->error_msg = 'Key was not provided';
}
$results = array(
'error' => (! empty($objErr->error_msg)),
'errorDetail' => $objErr,
'data' =>  $aryMem);
echo json_encode($results);
?>