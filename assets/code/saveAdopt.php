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
$f0D = '';
if (isset($_POST['step'])) {
	$id = filter_input(INPUT_POST, 'v', FILTER_UNSAFE_RAW);
	$formSave = str_replace("'","''",$_POST['formData']);
	$f0D =  str_replace("'","''",$_POST['formDataS0']);
	$f0D = "applSec0='".$f0D."', ";
	$step = filter_input(INPUT_POST, 'step', FILTER_UNSAFE_RAW);
	$prep_stmt = "SELECT applProgress,applComplete,applStatus FROM Applications WHERE applid=$id";
	$result = $mysqli->query($prep_stmt);
	$row = $result->fetch_assoc();
	$applProgress = json_decode($row['applProgress']);
	$applProgress[$step]->{'Comp'}='Y';
	// Check if the overall adoption/foster flags need to get set based on the answer to a specific application question.
	$faA = $faF = '';											
	if (isset($_POST['fa'])) {
		$fa = json_decode($_POST['fa']);
		$faA = in_array('A',$fa)==true ? "applAdopt='Y', ":"applAdopt='N', ";
		$faF = in_array('F',$fa)==true ? "applFost='Y', ":"applFost='N', ";
	}
	// scan all progress steps to see if they are all marked Y, if so then update the overall status and move to next step
	// when jan gets back need to change row into variable
	if ($row['applComplete'] == 'N') {
		$fini = true;
		foreach ($applProgress as $pstep) {
			// hack to make the last three application pages optional 
			if (!($pstep->{'Sec'} == 'Fee Structure For Your Perfect Golden' || $pstep->{'Sec'} == 'Miscellaneous' || $pstep->{'Sec'} == 'Agreement')) {
				if ($pstep->{'Comp'}=='N') {
					$fini = false;
					break;
				}
			}
		}
		if ($fini) {
			$row['applComplete']='Y';
			if ($row['applStatus']=='Draft') {
				$row['applStatus'] = 'New';
			}
		}			
	}
	$applProgress = json_encode($applProgress);
	$update_stmt = "UPDATE Applications SET $faA $faF $f0D applSec$step='$formSave',applProgress='$applProgress',applStatus='".$row['applStatus']."',applComplete='".$row['applComplete']."' WHERE applid=$id";
	if ($result = $mysqli->query($update_stmt)) {
		$id = $id;
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
	'debug' => $fini,
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_msg' => $error_msg),
'data' =>  $id);
echo json_encode($results);
//var_dump($update_stmt);
$mysqli->close();
?>