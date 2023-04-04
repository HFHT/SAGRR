<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/psl-config.php';
 
$objErr = new StdClass();				

if (isset($_POST['username'])) {
	$objRole = new StdClass();
	$_POST['rS']=='Y' ? $objRole->Self = 'Y' : $objRole->Self = 'N';
	$_POST['rD']=='Y' ? $objRole->Dog = 'Y' : $objRole->Dog = 'N';
	$_POST['rM']=='Y' ? $objRole->Mem = 'Y' : $objRole->Mem = 'N';
	$_POST['rP']=='Y' ? $objRole->App = 'Y' : $objRole->App = 'N';	
	$_POST['rL']=='Y' ? $objRole->Appl = 'Y' : $objRole->Appl = 'N';	
	$_POST['rX']=='Y' ? $objRole->Assign = 'Y' : $objRole->Assign = 'N';	
	$_POST['rA']=='Y' ? $objRole->Admin = 'Y' : $objRole->Admin = 'N';
	$role = json_encode($objRole);
	$_POST['Locked']=='Y' ? $Locked = 'Y' : $Locked = 'N';
	$username = dbPrep('username','s');
	$LockedDate = dbPrep('LockedDate','d');
	$LockedReason = dbPrep('LockedReason','s');
	$fk_PeopleT_id = dbPrep('fk_PeopleT_id','i');
	$update_stmt = "UPDATE members SET Role='$role', Validated='Y', Locked='$Locked', LockedDate=$LockedDate, LockedReason=$LockedReason, fk_PeopleT_id=$fk_PeopleT_id  WHERE username=$username";
	if ($result = $mysqli->query($update_stmt)) {
		$id = $username;
		$objErr->error_sql = $update_stmt;
	} else {
		$objErr->error_str = $mysqli->error;
		$objErr->error_sql = $update_stmt;
		$objErr->error_msg = 'Could not obtain summary counts, try again later!';		
	}				
}
else {
	$objErr->error_msg = 'Invalid key provided.';
}

$results = array(
'error' => (! empty($objErr->error_msg)),
'errorDetail' => $objErr,
'data' =>  $objRole);
echo json_encode($results);
//var_dump($update_stmt);
$mysqli->close();
?>